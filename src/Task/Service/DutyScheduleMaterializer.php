<?php

namespace App\Task\Service;

use App\Entity\DutySchedule;
use App\Entity\DutyShift;
use App\Entity\TaskItem;
use App\Entity\TaskAudit;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

final class DutyScheduleMaterializer
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    public function materializeUntilToday(): void
    {
        $timezone = new \DateTimeZone('Europe/Moscow');
        $today = new \DateTimeImmutable('today', $timezone);
        $connection = $this->em->getConnection();
        $connection->beginTransaction();
        try {
            // The unique occurrence index is a second guard; the lock also makes concurrent page loads safe.
            $connection->executeQuery('SELECT pg_advisory_xact_lock(20260920)')->fetchOne();
            foreach ($this->em->getRepository(DutySchedule::class)->findBy(['active' => true]) as $schedule) {
                $first = \DateTimeImmutable::createFromFormat('!Y-m-d', $schedule->getEffectiveFrom()->format('Y-m-d'), $timezone);
                if (!$first) continue;
                if ($schedule->getLastGeneratedDate()) {
                    $next = \DateTimeImmutable::createFromFormat('!Y-m-d', $schedule->getLastGeneratedDate()->format('Y-m-d'), $timezone)?->modify('+1 day');
                    if ($next && $next > $first) $first = $next;
                }
                for ($day = $first; $day <= $today; $day = $day->modify('+1 day')) {
                    $rule = $schedule->getDays()[$day->format('N') - 1] ?? null;
                    if (!is_array($rule) || empty($rule['enabled']) || empty($rule['userIds'])) continue;
                    $start = $day->setTime(...array_map('intval', explode(':', $rule['start'])));
                    $end = $day->setTime(...array_map('intval', explode(':', $rule['end'])));
                    if ($end <= $start) $end = $end->modify('+1 day');
                    foreach ($rule['userIds'] as $id) {
                        $user = $this->em->find(User::class, (int) $id);
                        if (!$user || !$user->isActive()) continue;
                        $existing = $this->em->getRepository(DutyShift::class)->findOneBy([
                            'schedule' => $schedule, 'assignee' => $user, 'occurrenceDate' => $day,
                        ]);
                        if ($existing) continue;
                        $titles = array_column($schedule->getTaskTemplates(), 'title');
                        $shift = (new DutyShift($user, $schedule->getTitle(), $start, $end, $titles))
                            ->setSchedule($schedule)->setOccurrenceDate($day)
                            ->setColor($schedule->getColor())->setComment($schedule->getComment());
                        $this->em->persist($shift);
                        foreach ($schedule->getTaskTemplates() as $template) {
                            $task = (new TaskItem($template['title'], $user, null))
                                ->setDescription($template['description'] ?: null)
                                ->setPriority($template['priority'])
                                ->setColor($schedule->getColor())
                                ->setDutyShift($shift)->setDueAt($end);
                            $this->em->persist($task);
                            $this->em->persist(new TaskAudit($task, null, 'created'));
                        }
                    }
                }
                if ($first <= $today) $schedule->setLastGeneratedDate($today);
            }
            $this->em->flush();
            $connection->commit();
        } catch (\Throwable $error) {
            $connection->rollBack();
            throw $error;
        }
    }
}
