<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'task_board_layout')]
class TaskBoardLayout
{
    #[ORM\Id, ORM\Column]
    private int $id = 1;

    #[ORM\Column(type: Types::JSON)]
    private array $columns = [];

    public function getId(): int { return $this->id; }
    public function getColumns(): array { return $this->columns; }
    public function setColumns(array $columns): self { $this->columns = $columns; return $this; }

    public static function defaults(): array
    {
        return [
            ['status' => 'open', 'name' => 'К работе', 'color' => '#718096'],
            ['status' => 'in_progress', 'name' => 'В работе', 'color' => '#3d70f5'],
            ['status' => 'review', 'name' => 'На проверке', 'color' => '#ed9a32'],
            ['status' => 'done', 'name' => 'Выполнено', 'color' => '#1faf79'],
            ['status' => 'not_done', 'name' => 'Не выполнено', 'color' => '#8993a4'],
        ];
    }
}
