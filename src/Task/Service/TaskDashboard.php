<?php

namespace App\Task\Service;

use App\Entity\TaskItem;
use App\Entity\TaskOverdueReport;
use App\Entity\User;

final class TaskDashboard
{
    /** @param TaskItem[] $tasks @param User[] $users @param TaskOverdueReport[] $reports */
    public function build(array $tasks, array $users, array $reports, User $current): array
    {
        $zone = new \DateTimeZone('Europe/Moscow');
        $today = new \DateTimeImmutable('today', $zone);
        $start7 = $today->modify('-6 days');
        $start30 = $today->modify('-29 days');
        $moved = [];
        foreach ($reports as $report) $moved[$report->getTask()->getId()] = true;

        $daily = [];
        for ($day = $start7; $day <= $today; $day = $day->modify('+1 day')) {
            $key = $day->format('Y-m-d');
            $daily[$key] = ['date' => $key, 'done' => 0, 'failed' => 0];
        }

        $summary = [];
        foreach ($users as $user) $summary[$user->getId()] = [
            'person' => ['id' => $user->getId(), 'name' => $user->getName()],
            'done' => 0, 'failed' => 0, 'onTime' => 0, 'dueDated' => 0, 'moved' => 0,
        ];

        foreach ($tasks as $task) {
            $closed = $task->getCompletedAt();
            if (!$closed || !in_array($task->getStatus(), ['done', 'not_done'], true)) continue;
            $day = $closed->setTimezone($zone)->format('Y-m-d');
            if ($day < $start30->format('Y-m-d') || $day > $today->format('Y-m-d')) continue;
            foreach ($task->getCompletedForIds() as $ownerId) {
                if (!isset($summary[$ownerId])) continue;
                $row = &$summary[$ownerId];
                $row[$task->getStatus() === 'done' ? 'done' : 'failed']++;
                if ($task->getDueAt()) {
                    $row['dueDated']++;
                    if (isset($moved[$task->getId()])) $row['moved']++;
                    if ($task->getStatus() === 'done' && !isset($moved[$task->getId()]) && $closed <= $task->getDueAt()) $row['onTime']++;
                }
                unset($row);
            }
            if (in_array($current->getId(), $task->getCompletedForIds(), true) && isset($daily[$day])) $daily[$day][$task->getStatus() === 'done' ? 'done' : 'failed']++;
        }

        $ranking = array_values(array_map(static function (array $row): array {
            $closed = $row['done'] + $row['failed'];
            $row['completionRate'] = $closed ? round($row['done'] * 100 / $closed) : null;
            $row['score'] = $row['done'] * 10 + $row['onTime'] * 2 - $row['failed'] * 4 - $row['moved'] * 2;
            return $row;
        }, $summary));
        usort($ranking, static fn (array $a, array $b) => ($b['score'] <=> $a['score']) ?: ($b['done'] <=> $a['done']) ?: strcmp($a['person']['name'], $b['person']['name']));
        foreach ($ranking as $index => &$row) $row['rank'] = $index + 1;
        unset($row);

        $personal7 = ['done' => 0, 'failed' => 0, 'onTime' => 0, 'dueDated' => 0, 'moved' => 0];
        foreach ($tasks as $task) {
            if (!in_array($current->getId(), $task->getCompletedForIds(), true) || !$task->getCompletedAt()) continue;
            $day = $task->getCompletedAt()->setTimezone($zone)->format('Y-m-d');
            if (!isset($daily[$day])) continue;
            $personal7[$task->getStatus() === 'done' ? 'done' : 'failed']++;
            if ($task->getDueAt()) {
                $personal7['dueDated']++;
                if (isset($moved[$task->getId()])) $personal7['moved']++;
                if ($task->getStatus() === 'done' && !isset($moved[$task->getId()]) && $task->getCompletedAt() <= $task->getDueAt()) $personal7['onTime']++;
            }
        }
        $personal7['onTimeRate'] = $personal7['dueDated'] ? round($personal7['onTime'] * 100 / $personal7['dueDated']) : null;
        $personal7['transferRate'] = $personal7['dueDated'] ? round($personal7['moved'] * 100 / $personal7['dueDated']) : null;
        $personal7['completionRate'] = ($personal7['done'] + $personal7['failed']) ? round($personal7['done'] * 100 / ($personal7['done'] + $personal7['failed'])) : null;
        return ['daily' => array_values($daily), 'personal' => $personal7, 'ranking' => $ranking];
    }
}
