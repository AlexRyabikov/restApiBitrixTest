<?php

namespace Alex\Notes;

use Bitrix\Main\Application;

final class NoteRepository
{
    public function all(): array
    {
        $connection = Application::getConnection();
        $result = $connection->query('SELECT ID, TITLE, CONTENT, CREATED_AT, UPDATED_AT FROM alex_notes ORDER BY ID DESC');

        $notes = [];
        while ($row = $result->fetch()) {
            $notes[] = $this->mapRow($row);
        }

        return $notes;
    }

    public function find(int $id): ?array
    {
        $connection = Application::getConnection();
        $id = max(0, $id);

        $result = $connection->query('SELECT ID, TITLE, CONTENT, CREATED_AT, UPDATED_AT FROM alex_notes WHERE ID = ' . $id);
        $row = $result->fetch();

        return $row ? $this->mapRow($row) : null;
    }

    public function create(string $title, string $content): int
    {
        $connection = Application::getConnection();
        $sqlHelper = $connection->getSqlHelper();
        $titleSql = "'" . $sqlHelper->forSql($title, 255) . "'";
        $contentSql = "'" . $sqlHelper->forSql($content) . "'";

        $connection->queryExecute(
            'INSERT INTO alex_notes (TITLE, CONTENT) VALUES (' . $titleSql . ', ' . $contentSql . ')'
        );

        return (int) $connection->getInsertedId();
    }

    public function update(int $id, string $title, string $content): bool
    {
        $connection = Application::getConnection();
        $sqlHelper = $connection->getSqlHelper();
        $id = max(0, $id);
        $titleSql = "'" . $sqlHelper->forSql($title, 255) . "'";
        $contentSql = "'" . $sqlHelper->forSql($content) . "'";

        $connection->queryExecute(
            'UPDATE alex_notes SET TITLE = ' . $titleSql . ', CONTENT = ' . $contentSql . ' WHERE ID = ' . $id
        );

        return true;
    }

    public function delete(int $id): bool
    {
        $connection = Application::getConnection();
        $id = max(0, $id);
        $connection->queryExecute('DELETE FROM alex_notes WHERE ID = ' . $id);

        return true;
    }

    private function mapRow(array $row): array
    {
        return [
            'id' => (int) $row['ID'],
            'title' => (string) $row['TITLE'],
            'content' => (string) $row['CONTENT'],
            'created_at' => (string) $row['CREATED_AT'],
            'updated_at' => (string) $row['UPDATED_AT'],
        ];
    }
}
