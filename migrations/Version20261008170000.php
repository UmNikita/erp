<?php

declare(strict_types=1);
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261008170000 extends AbstractMigration
{
    public function getDescription(): string { return 'Shared administrator-managed task funnel layout'; }
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE task_board_layout (id INT NOT NULL, columns JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO task_board_layout (id, columns) VALUES (1, ?)', [json_encode([
            ['status' => 'open', 'name' => 'К работе', 'color' => '#718096'],
            ['status' => 'in_progress', 'name' => 'В работе', 'color' => '#3d70f5'],
            ['status' => 'review', 'name' => 'На проверке', 'color' => '#ed9a32'],
            ['status' => 'done', 'name' => 'Выполнено', 'color' => '#1faf79'],
            ['status' => 'not_done', 'name' => 'Не выполнено', 'color' => '#8993a4'],
        ], JSON_THROW_ON_ERROR)]);
    }
    public function down(Schema $schema): void { $this->addSql('DROP TABLE task_board_layout'); }
}
