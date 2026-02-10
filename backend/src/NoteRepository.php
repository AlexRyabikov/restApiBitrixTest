<?php

declare(strict_types=1);

namespace App;

use PDO;

final class NoteRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function all(): array
    {
        $stmt = $this->pdo->query('SELECT id, title, content, created_at, updated_at FROM notes ORDER BY id DESC');

        return $stmt->fetchAll() ?: [];
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, title, content, created_at, updated_at FROM notes WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    public function create(string $title, string $content): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO notes (title, content) VALUES (:title, :content)');
        $stmt->execute([
            'title' => $title,
            'content' => $content,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, string $title, string $content): bool
    {
        $stmt = $this->pdo->prepare('UPDATE notes SET title = :title, content = :content WHERE id = :id');
        $stmt->execute([
            'id' => $id,
            'title' => $title,
            'content' => $content,
        ]);

        return $stmt->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM notes WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() > 0;
    }
}