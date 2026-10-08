<?php

namespace App\Tests;

use App\Entity\TaskItem;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

final class TaskItemTest extends TestCase
{
    private function person(int $id): User
    {
        $user = new User();
        (new \ReflectionProperty(User::class, 'id'))->setValue($user, $id);
        return $user;
    }

    public function testSharedCompletionKeepsOriginalOwners(): void
    {
        $one = $this->person(1);
        $two = $this->person(2);
        $task = new TaskItem('Shared task', $one, $one);
        $task->setAssignees([$one, $two, $one])->setStatus('done');
        self::assertSame([1, 2], $task->getCompletedForIds());
        $closed = $task->getCompletedAt();
        $task->setAssignees([$two])->setStatus('done');
        self::assertSame([1, 2], $task->getCompletedForIds());
        self::assertSame($closed, $task->getCompletedAt());
        $task->setStatus('open');
        self::assertNull($task->getCompletedAt());
        self::assertSame([], $task->getCompletedForIds());
        $task->setStatus('not_done');
        self::assertSame([2], $task->getCompletedForIds());
    }
}
