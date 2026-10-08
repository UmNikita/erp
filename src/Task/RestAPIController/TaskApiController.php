<?php

namespace App\Task\RestAPIController;

use App\Entity\DutyShift;
use App\Entity\DutySchedule;
use App\Entity\TaskItem;
use App\Entity\TaskAudit;
use App\Entity\TaskFeedback;
use App\Entity\TaskMessage;
use App\Entity\TaskOverdueReport;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\Enum\Permission;
use App\Task\Service\DutyScheduleMaterializer;
use App\Task\Service\TaskDashboard;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/tasks')]
final class TaskApiController extends AbstractController
{
    private const COLORS = ['#3d70f5', '#1faf79', '#ef5361', '#ed9a32', '#8a63ea', '#4e8cd8', '#16a6b5', '#d65799', '#a98962', '#718096', '#72ae45', '#263248'];
    private const STATUSES = ['open', 'in_progress', 'review', 'done', 'not_done'];

    #[Route('/bootstrap', methods: ['GET'])]
    public function bootstrap(EntityManagerInterface $em, UserRepository $users, DutyScheduleMaterializer $materializer, TaskDashboard $dashboard): JsonResponse
    {
        $this->assertAccess();
        $materializer->materializeUntilToday();
        $user = $this->currentUser();
        $admin = $this->canAdmin();
        $allTasks = $em->getRepository(TaskItem::class)->findBy([], ['createdAt' => 'DESC']);
        $tasks = array_values(array_filter($allTasks, fn (TaskItem $task) => $admin || $this->canViewTask($task, $user)));
        $ids = array_map(static fn (TaskItem $task) => $task->getId(), $tasks);
        $messages = $em->getRepository(TaskMessage::class)->findBy([], ['createdAt' => 'ASC']);
        $messages = array_values(array_filter($messages, static fn (TaskMessage $message) => in_array($message->getTask()->getId(), $ids, true)));
        $allReports = $em->getRepository(TaskOverdueReport::class)->findBy([], ['createdAt' => 'DESC']);
        $reports = array_values(array_filter($allReports, fn (TaskOverdueReport $report) => $admin || $report->getAuthor()?->getId() === $user->getId() || in_array($report->getTask()->getId(), $ids, true)));
        $people = $users->findBy(['is_active' => true], ['name' => 'ASC']);
        $feedback = $em->getRepository(TaskFeedback::class)->findBy([], ['createdAt' => 'DESC']);
        $feedback = array_values(array_filter($feedback, fn (TaskFeedback $item) => $admin || $item->getRecipient()->getId() === $user->getId()));
        $shifts = $em->getRepository(DutyShift::class)->findBy([], ['startsAt' => 'ASC']);
        $shifts = array_values(array_filter($shifts, fn (DutyShift $shift) => $admin || $shift->getAssignee()->getId() === $user->getId()));

        return $this->json([
            'currentUser' => $this->userData($user),
            'canAdmin' => $admin,
            'users' => array_map($this->userData(...), $people),
            'tasks' => array_map($this->taskData(...), $tasks),
            'shifts' => array_map($this->shiftData(...), $shifts),
            'schedules' => array_map($this->scheduleData(...), $em->getRepository(DutySchedule::class)->findBy([], ['id' => 'DESC'])),
            'messages' => array_map($this->messageData(...), $messages),
            'overdueReports' => array_map($this->overdueReportData(...), $reports),
            'dashboard' => $dashboard->build($allTasks, $people, $allReports, $user),
            'feedback' => array_map($this->feedbackData(...), $feedback),
        ]);
    }

    #[Route('/audit', methods: ['GET'])]
    public function audit(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $this->assertAdmin();
        $offset = max(0, min(100000, $request->query->getInt('offset')));
        $repository = $em->getRepository(TaskAudit::class);
        $entries = $repository->findBy([], ['createdAt' => 'DESC', 'id' => 'DESC'], 100, $offset);
        return $this->json(['items' => array_map($this->auditData(...), $entries), 'total' => $repository->count([])]);
    }

    #[Route('/feedback', methods: ['POST'])]
    public function addFeedback(Request $request, EntityManagerInterface $em, UserRepository $users): JsonResponse
    {
        $this->assertAdmin();
        $data = $this->data($request);
        $item = new TaskFeedback($this->assignee($data['recipientId'] ?? null, $users), $this->currentUser(), $this->text($data['body'] ?? null, 2000));
        $em->persist($item);
        $em->flush();
        return $this->json($this->feedbackData($item), Response::HTTP_CREATED);
    }

    #[Route('/feedback/{id}', requirements: ['id' => '\\d+'], methods: ['DELETE'])]
    public function deleteFeedback(int $id, EntityManagerInterface $em): Response
    {
        $this->assertAdmin();
        $item = $em->find(TaskFeedback::class, $id);
        if (!$item) return $this->json(['error' => 'Комментарий не найден'], Response::HTTP_NOT_FOUND);
        $em->remove($item);
        $em->flush();
        return new Response(status: Response::HTTP_NO_CONTENT);
    }

    #[Route('/schedules', methods: ['POST'])]
    public function createSchedule(Request $request, EntityManagerInterface $em, UserRepository $users): JsonResponse
    {
        $this->assertAdmin();
        $schedule = new DutySchedule('Новое дежурство');
        $this->updateScheduleFields($schedule, $this->data($request), $users);
        $em->persist($schedule);
        $em->flush();
        return $this->json($this->scheduleData($schedule), Response::HTTP_CREATED);
    }

    #[Route('/schedules/{id}', requirements: ['id' => '\\d+'], methods: ['PATCH'])]
    public function updateSchedule(int $id, Request $request, EntityManagerInterface $em, UserRepository $users): JsonResponse
    {
        $this->assertAdmin();
        $schedule = $em->find(DutySchedule::class, $id);
        if (!$schedule) return $this->json(['error' => 'Дежурство не найдено'], 404);
        $this->updateScheduleFields($schedule, $this->data($request), $users);
        // Existing shift instances and their tasks are snapshots. Changes take effect tomorrow.
        $schedule->setEffectiveFrom(new \DateTimeImmutable('tomorrow', new \DateTimeZone('Europe/Moscow')));
        $em->flush();
        return $this->json($this->scheduleData($schedule));
    }

    private function updateScheduleFields(DutySchedule $schedule, array $data, UserRepository $users): void
    {
        $schedule->setTitle($this->text($data['title'] ?? null, 160));
        if (!in_array($data['color'] ?? null, self::COLORS, true)) throw new UnprocessableEntityHttpException('Выберите цвет из палитры');
        $schedule->setColor($data['color']);
        $schedule->setComment($this->optionalText($data['comment'] ?? null, 3000));
        if (!is_bool($data['active'] ?? null) || !is_bool($data['sameTime'] ?? null)) throw new UnprocessableEntityHttpException('Некорректные настройки дежурства');
        $schedule->setActive($data['active'])->setSameTime($data['sameTime']);
        $days = $data['days'] ?? null;
        if (!is_array($days) || count($days) !== 7 || array_keys($days) !== range(0, 6)) throw new UnprocessableEntityHttpException('Укажите все дни недели');
        $normalized = [];
        foreach ($days as $day) {
            if (!is_array($day) || !is_bool($day['enabled'] ?? null)) throw new UnprocessableEntityHttpException('Некорректный день недели');
            $start = $day['start'] ?? null; $end = $day['end'] ?? null;
            if (!is_string($start) || !preg_match('/^(?:[01]\\d|2[0-3]):[0-5]\\d$/', $start) || !is_string($end) || !preg_match('/^(?:[01]\\d|2[0-3]):[0-5]\\d$/', $end) || $start === $end) throw new UnprocessableEntityHttpException('Укажите корректное время начала и конца');
            $ids = $day['userIds'] ?? null;
            if (!is_array($ids) || count($ids) > 100) throw new UnprocessableEntityHttpException('Некорректный список сотрудников');
            $userIds = [];
            foreach ($ids as $id) $userIds[] = $this->assignee($id, $users)->getId();
            $normalized[] = ['enabled' => $day['enabled'], 'start' => $start, 'end' => $end, 'userIds' => array_values(array_unique($userIds))];
        }
        if ($schedule->isSameTime()) {
            $first = $normalized[0];
            foreach ($normalized as &$day) { $day['start'] = $first['start']; $day['end'] = $first['end']; }
            unset($day);
        }
        if ($schedule->isActive() && !array_filter($normalized, static fn (array $day) => $day['enabled'] && count($day['userIds']) > 0)) throw new UnprocessableEntityHttpException('Назначьте сотрудника хотя бы на один выбранный день');
        $schedule->setDays($normalized);
        $templates = $data['taskTemplates'] ?? null;
        if (!is_array($templates) || count($templates) > 30) throw new UnprocessableEntityHttpException('Можно добавить не более 30 задач');
        $items = [];
        foreach ($templates as $item) {
            if (!is_array($item)) throw new UnprocessableEntityHttpException('Некорректная задача дежурства');
            $priority = $item['priority'] ?? 'normal';
            if (!in_array($priority, ['low', 'normal', 'high'], true)) throw new UnprocessableEntityHttpException('Некорректный приоритет');
            $items[] = ['id' => is_string($item['id'] ?? null) && strlen($item['id']) <= 64 ? $item['id'] : bin2hex(random_bytes(8)), 'title' => $this->text($item['title'] ?? null, 180), 'description' => $this->optionalText($item['description'] ?? null, 3000) ?? '', 'priority' => $priority];
        }
        $schedule->setTaskTemplates($items);
    }

    #[Route('', methods: ['POST'])]
    public function createTask(Request $request, EntityManagerInterface $em, UserRepository $users): JsonResponse
    {
        $this->assertAccess();
        $data = $this->data($request);
        $user = $this->currentUser();
        if (array_key_exists('requestedById', $data) && (int) $data['requestedById'] !== $user->getId() && !$this->canAdmin()) throw $this->createAccessDeniedException();
        $task = new TaskItem($this->text($data['title'] ?? null, 180), $user, $user);
        $task->setRequestedBy($user);
        $this->updateTaskFields($task, $data, $users);
        $em->persist($task);
        $em->persist(new TaskAudit($task, $user, 'created'));
        $em->flush();
        return $this->json($this->taskData($task), Response::HTTP_CREATED);
    }

    #[Route('/{id}', requirements: ['id' => '\\d+'], methods: ['PATCH'])]
    public function updateTask(int $id, Request $request, EntityManagerInterface $em, UserRepository $users): JsonResponse
    {
        $this->assertAccess();
        $task = $em->find(TaskItem::class, $id);
        if (!$task) return $this->json(['error' => 'Задача не найдена'], 404);
        if (!$this->canViewTask($task, $this->currentUser()) && !$this->canAdmin()) throw $this->createAccessDeniedException();
        $data = $this->data($request);
        if (array_key_exists('requestedById', $data) && !$this->canAdmin() && $task->getCreatedBy()?->getId() !== $this->currentUser()->getId()) throw $this->createAccessDeniedException();
        if ($this->isOverdueRegularTask($task) && (array_key_exists('dueAt', $data) || array_key_exists('assigneeId', $data) || array_key_exists('assigneeIds', $data) || array_key_exists('status', $data))) {
            throw new UnprocessableEntityHttpException('Сначала укажите причину просрочки и новый срок в карточке задачи');
        }
        $before = $this->taskSnapshot($task);
        $this->updateTaskFields($task, $data, $users);
        $after = $this->taskSnapshot($task);
        $changes = [];
        foreach ($after as $field => $value) if ($value !== $before[$field]) $changes[$field] = ['before' => $before[$field], 'after' => $value];
        if ($changes) { $task->touch(); $em->persist(new TaskAudit($task, $this->currentUser(), 'updated', $changes)); }
        $em->flush();
        return $this->json($this->taskData($task));
    }

    #[Route('/{id}/report-overdue', requirements: ['id' => '\\d+'], methods: ['POST'])]
    public function reportOverdue(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $this->assertAccess();
        $task = $em->find(TaskItem::class, $id);
        if (!$task) return $this->json(['error' => 'Задача не найдена'], 404);
        if (!$task->hasAssignee($this->currentUser()) && !$this->canAdmin()) throw $this->createAccessDeniedException();
        if (!$this->isOverdueRegularTask($task)) throw new UnprocessableEntityHttpException('Задача не просрочена или относится к дежурству');
        $data = $this->data($request);
        $reason = $this->text($data['reason'] ?? null, 1000);
        $newDueAt = $this->date($data['newDueAt'] ?? null);
        if ($newDueAt <= new \DateTimeImmutable() || $newDueAt <= $task->getDueAt()) throw new UnprocessableEntityHttpException('Новый срок должен быть позже текущего времени');
        $report = new TaskOverdueReport($task, $this->currentUser(), $reason, $task->getDueAt(), $newDueAt);
        $task->setDueAt($newDueAt);
        $em->persist($report);
        $em->persist(new TaskAudit($task, $this->currentUser(), 'deadline_moved', [
            'reason' => $reason,
            'dueAt' => ['before' => $report->getPreviousDueAt()->format(DATE_ATOM), 'after' => $newDueAt->format(DATE_ATOM)],
        ]));
        $em->flush();
        return $this->json($this->overdueReportData($report), Response::HTTP_CREATED);
    }

    private function isOverdueRegularTask(TaskItem $task): bool
    {
        return $task->getDutyShift() === null && $task->getDueAt() !== null && $task->getDueAt() < new \DateTimeImmutable() && !in_array($task->getStatus(), ['done', 'not_done'], true);
    }

    #[Route('/{id}/messages', requirements: ['id' => '\\d+'], methods: ['POST'])]
    public function addMessage(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $this->assertAccess();
        $task = $em->find(TaskItem::class, $id);
        if (!$task) return $this->json(['error' => 'Задача не найдена'], 404);
        if (!$this->canViewTask($task, $this->currentUser()) && !$this->canAdmin()) throw $this->createAccessDeniedException();
        $message = new TaskMessage($task, $this->currentUser(), $this->text($this->data($request)['body'] ?? null, 3000));
        $em->persist($message);
        $em->persist(new TaskAudit($task, $this->currentUser(), 'message_added'));
        $em->flush();
        return $this->json($this->messageData($message), Response::HTTP_CREATED);
    }

    #[Route('/shifts', methods: ['POST'])]
    public function createShift(Request $request, EntityManagerInterface $em, UserRepository $users): JsonResponse
    {
        $this->assertAdmin();
        $data = $this->data($request);
        $assignee = $this->assignee($data['assigneeId'] ?? null, $users);
        $start = $this->date($data['startsAt'] ?? null);
        $end = $this->date($data['endsAt'] ?? null);
        $this->validateShift($em, $assignee, $start, $end);
        $items = $this->checklist($data['checklist'] ?? []);
        $shift = new DutyShift($assignee, $this->text($data['title'] ?? null, 160), $start, $end, $items);
        $em->persist($shift);
        foreach ($items as $title) $this->createDutyTask($em, $shift, $title);
        $em->flush();
        return $this->json($this->shiftData($shift), Response::HTTP_CREATED);
    }

    #[Route('/shifts/{id}', requirements: ['id' => '\\d+'], methods: ['PATCH'])]
    public function updateShift(int $id, Request $request, EntityManagerInterface $em, UserRepository $users): JsonResponse
    {
        $this->assertAdmin();
        $shift = $em->find(DutyShift::class, $id);
        if (!$shift) return $this->json(['error' => 'Дежурство не найдено'], 404);
        $data = $this->data($request);
        $assignee = array_key_exists('assigneeId', $data) ? $this->assignee($data['assigneeId'], $users) : $shift->getAssignee();
        $start = array_key_exists('startsAt', $data) ? $this->date($data['startsAt']) : $shift->getStartsAt();
        $end = array_key_exists('endsAt', $data) ? $this->date($data['endsAt']) : $shift->getEndsAt();
        $this->validateShift($em, $assignee, $start, $end, $id);
        $items = array_key_exists('checklist', $data) ? $this->checklist($data['checklist']) : $shift->getChecklist();
        $existing = $shift->getChecklist();
        foreach ($existing as $title) {
            if (!in_array($title, $items, true)) throw new UnprocessableEntityHttpException('Созданные задачи чек-листа нельзя убрать из дежурства');
        }
        $shift->setAssignee($assignee)->setStartsAt($start)->setEndsAt($end)->setChecklist($items);
        if (array_key_exists('title', $data)) $shift->setTitle($this->text($data['title'], 160));
        foreach ($em->getRepository(TaskItem::class)->findBy(['dutyShift' => $shift]) as $task) {
            if (!in_array($task->getStatus(), ['done', 'not_done'], true)) {
                $before = $this->taskSnapshot($task);
                $task->setAssignee($assignee)->setDueAt($end);
                $after = $this->taskSnapshot($task);
                $changes = [];
                foreach ($after as $field => $value) if ($value !== $before[$field]) $changes[$field] = ['before' => $before[$field], 'after' => $value];
                if ($changes) { $task->touch(); $em->persist(new TaskAudit($task, $this->currentUser(), 'updated', $changes)); }
            }
        }
        foreach (array_diff($items, $existing) as $title) $this->createDutyTask($em, $shift, $title);
        $em->flush();
        return $this->json($this->shiftData($shift));
    }

    private function createDutyTask(EntityManagerInterface $em, DutyShift $shift, string $title): void
    {
        $task = new TaskItem($title, $shift->getAssignee(), $this->currentUser());
        $task->setDutyShift($shift)->setColor('#8a63ea')->setDueAt($shift->getEndsAt());
        $em->persist($task);
        $em->persist(new TaskAudit($task, $this->currentUser(), 'created'));
    }

    private function updateTaskFields(TaskItem $task, array $data, UserRepository $users): void
    {
        if (array_key_exists('title', $data)) $task->setTitle($this->text($data['title'], 180));
        if (array_key_exists('description', $data)) $task->setDescription($this->optionalText($data['description'], 3000));
        if (array_key_exists('result', $data)) $task->setResult($this->optionalText($data['result'], 3000));
        if (array_key_exists('assigneeIds', $data)) $task->setAssignees($this->assignees($data['assigneeIds'], $users));
        elseif (array_key_exists('assigneeId', $data)) $task->setAssignee($this->assignee($data['assigneeId'], $users));
        if (array_key_exists('status', $data)) {
            if (!in_array($data['status'], self::STATUSES, true)) throw new UnprocessableEntityHttpException('Неизвестный статус');
            $task->setStatus($data['status']);
        }
        if (array_key_exists('priority', $data)) {
            if (!in_array($data['priority'], ['low', 'normal', 'high'], true)) throw new UnprocessableEntityHttpException('Неизвестный приоритет');
            $task->setPriority($data['priority']);
        }
        if (array_key_exists('color', $data)) {
            if (!in_array($data['color'], self::COLORS, true)) throw new UnprocessableEntityHttpException('Неизвестный цвет');
            $task->setColor($data['color']);
        }
        if (array_key_exists('dueAt', $data)) $task->setDueAt($data['dueAt'] ? $this->date($data['dueAt']) : null);
        if (array_key_exists('requestedById', $data)) $task->setRequestedBy($this->assignee($data['requestedById'], $users));
    }

    private function validateShift(EntityManagerInterface $em, User $assignee, \DateTimeImmutable $start, \DateTimeImmutable $end, ?int $ignore = null): void
    {
        if ($start >= $end) throw new UnprocessableEntityHttpException('Окончание должно быть позже начала');
        if ($end->getTimestamp() - $start->getTimestamp() > 86400) throw new UnprocessableEntityHttpException('Дежурство не может длиться больше суток');
        foreach ($em->getRepository(DutyShift::class)->findBy(['assignee' => $assignee]) as $shift) {
            if ($shift->getId() !== $ignore && $start < $shift->getEndsAt() && $end > $shift->getStartsAt()) {
                throw new UnprocessableEntityHttpException('У сотрудника уже есть дежурство в это время');
            }
        }
    }

    private function checklist(mixed $value): array
    {
        if (!is_array($value) || count($value) > 20) throw new UnprocessableEntityHttpException('Некорректный чек-лист');
        $items = array_map(fn ($item) => $this->text($item, 180), $value);
        if (count($items) !== count(array_unique($items))) throw new UnprocessableEntityHttpException('Пункты чек-листа не должны повторяться');
        return array_values($items);
    }

    private function assignee(mixed $id, UserRepository $users): User
    {
        $user = filter_var($id, FILTER_VALIDATE_INT) ? $users->find((int) $id) : null;
        if (!$user || !$user->isActive()) throw new UnprocessableEntityHttpException('Выберите действующего сотрудника');
        return $user;
    }

    /** @return User[] */
    private function assignees(mixed $ids, UserRepository $users): array
    {
        if (!is_array($ids) || !array_is_list($ids) || !$ids || count($ids) > 30) throw new UnprocessableEntityHttpException('Выберите от 1 до 30 ответственных');
        $people = [];
        foreach ($ids as $id) {
            $person = $this->assignee($id, $users);
            $people[$person->getId()] = $person;
        }
        return array_values($people);
    }

    private function text(mixed $value, int $max): string
    {
        $text = is_string($value) ? trim($value) : '';
        if ($text === '' || mb_strlen($text) > $max) throw new UnprocessableEntityHttpException('Заполните поле (до '.$max.' символов)');
        return $text;
    }

    private function optionalText(mixed $value, int $max): ?string
    {
        if ($value === null || $value === '') return null;
        return $this->text($value, $max);
    }

    private function date(mixed $value): \DateTimeImmutable
    {
        if (!is_string($value) || trim($value) === '') throw new UnprocessableEntityHttpException('Укажите дату и время');
        if (!preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(?:\.\d+)?(?:Z|[+-]\d{2}:\d{2})$/', $value)) throw new UnprocessableEntityHttpException('Некорректная дата');
        try {
            $date = new \DateTimeImmutable($value);
            $errors = \DateTimeImmutable::getLastErrors();
            if ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0)) {
                throw new UnprocessableEntityHttpException('Некорректная дата');
            }
            return $date->setTimezone(new \DateTimeZone('UTC'));
        }
        catch (\Exception) { throw new UnprocessableEntityHttpException('Некорректная дата'); }
    }

    private function data(Request $request): array
    {
        try { $data = json_decode($request->getContent(), true, flags: JSON_THROW_ON_ERROR); }
        catch (\JsonException) { throw new BadRequestHttpException('Некорректный JSON'); }
        return is_array($data) ? $data : [];
    }

    private function assertAccess(): void { $this->denyAccessUnlessGranted(Permission::TASK_ACCESS->value); }
    private function assertAdmin(): void
    {
        $this->assertAccess();
        if (!$this->canAdmin()) throw $this->createAccessDeniedException();
    }
    private function canAdmin(): bool { return $this->currentUser()->isRoot() || $this->isGranted(Permission::TASK_ADMIN_ACCESS->value); }
    private function currentUser(): User
    {
        $user = $this->getUser();
        if (!$user instanceof User) throw $this->createAccessDeniedException();
        return $user;
    }
    private function canViewTask(TaskItem $task, User $user): bool
    {
        return $task->hasAssignee($user) || in_array($user->getId(), $task->getCompletedForIds(), true) || $task->getCreatedBy()?->getId() === $user->getId() || $task->getRequestedBy()?->getId() === $user->getId();
    }
    private function taskSnapshot(TaskItem $task): array
    {
        return [
            'title' => $task->getTitle(), 'description' => $task->getDescription(), 'result' => $task->getResult(),
            'status' => $task->getStatus(), 'priority' => $task->getPriority(),
            'dueAt' => $task->getDueAt()?->format(DATE_ATOM),
            'assignees' => array_map(static fn (User $person) => $person->getName(), $task->getAssignees()),
            'requestedBy' => $task->getRequestedBy()?->getName(),
        ];
    }
    private function userData(?User $user): ?array { return $user ? ['id' => $user->getId(), 'name' => $user->getName()] : null; }
    private function taskData(TaskItem $task): array
    {
        return [
            'id' => $task->getId(), 'title' => $task->getTitle(), 'description' => $task->getDescription(),
            'result' => $task->getResult(), 'status' => $task->getStatus(), 'priority' => $task->getPriority(),
            'color' => $task->getColor(), 'dueAt' => $task->getDueAt()?->format(DATE_ATOM),
            'assignee' => $this->userData($task->getAssignee()), 'assignees' => array_map($this->userData(...), $task->getAssignees()), 'createdBy' => $this->userData($task->getCreatedBy()), 'requestedBy' => $this->userData($task->getRequestedBy() ?? $task->getCreatedBy()),
            'dutyShiftId' => $task->getDutyShift()?->getId(),
            'createdAt' => $task->getCreatedAt()->format(DATE_ATOM), 'updatedAt' => $task->getUpdatedAt()->format(DATE_ATOM),
            'completedAt' => $task->getCompletedAt()?->format(DATE_ATOM), 'completedFor' => $this->userData($task->getCompletedFor()), 'completedForIds' => $task->getCompletedForIds(),
        ];
    }
    private function auditData(TaskAudit $entry): array
    {
        return [
            'id' => $entry->getId(), 'taskId' => $entry->getTask()?->getId(), 'taskTitle' => $entry->getTaskTitle(),
            'actor' => $this->userData($entry->getActor()), 'action' => $entry->getAction(),
            'changes' => $entry->getChanges(), 'createdAt' => $entry->getCreatedAt()->format(DATE_ATOM),
        ];
    }
    private function shiftData(DutyShift $shift): array
    {
        return [
            'id' => $shift->getId(), 'title' => $shift->getTitle(), 'assignee' => $this->userData($shift->getAssignee()),
            'startsAt' => $shift->getStartsAt()->format(DATE_ATOM), 'endsAt' => $shift->getEndsAt()->format(DATE_ATOM),
            'checklist' => $shift->getChecklist(), 'scheduleId' => $shift->getSchedule()?->getId(),
            'color' => $shift->getColor(), 'comment' => $shift->getComment(),
        ];
    }
    private function scheduleData(DutySchedule $schedule): array
    {
        return [
            'id' => $schedule->getId(), 'title' => $schedule->getTitle(), 'color' => $schedule->getColor(),
            'comment' => $schedule->getComment(), 'active' => $schedule->isActive(),
            'sameTime' => $schedule->isSameTime(), 'days' => $schedule->getDays(),
            'taskTemplates' => $schedule->getTaskTemplates(),
            'effectiveFrom' => $schedule->getEffectiveFrom()->format('Y-m-d'),
        ];
    }
    private function messageData(TaskMessage $message): array
    {
        return [
            'id' => $message->getId(), 'taskId' => $message->getTask()->getId(),
            'author' => $this->userData($message->getAuthor()), 'body' => $message->getBody(),
            'createdAt' => $message->getCreatedAt()->format(DATE_ATOM),
        ];
    }
    private function overdueReportData(TaskOverdueReport $report): array
    {
        return [
            'id' => $report->getId(), 'taskId' => $report->getTask()->getId(), 'taskTitle' => $report->getTask()->getTitle(),
            'author' => $this->userData($report->getAuthor()), 'reason' => $report->getReason(),
            'assigneeAtReport' => $this->userData($report->getAssigneeAtReport()),
            'assigneeAtReportIds' => $report->getAssigneeAtReportIds(),
            'previousDueAt' => $report->getPreviousDueAt()->format(DATE_ATOM),
            'newDueAt' => $report->getNewDueAt()->format(DATE_ATOM),
            'createdAt' => $report->getCreatedAt()->format(DATE_ATOM),
        ];
    }
    private function feedbackData(TaskFeedback $item): array
    {
        return [
            'id' => $item->getId(), 'recipient' => $this->userData($item->getRecipient()),
            'author' => $this->userData($item->getAuthor()), 'body' => $item->getBody(),
            'createdAt' => $item->getCreatedAt()->format(DATE_ATOM),
        ];
    }
}
