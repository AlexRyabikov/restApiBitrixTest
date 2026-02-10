<?php

namespace Alex\Notes;

final class NoteController
{
    public function __construct(private readonly NoteRepository $repository)
    {
    }

    public function dispatch(string $method, array $segments, array $payload): void
    {
        if (count($segments) >= 2 && $segments[0] === 'api' && $segments[1] === 'notes') {
            $id = isset($segments[2]) ? (int) $segments[2] : null;

            if ($method === 'GET' && $id === null) {
                $this->json(200, $this->repository->all());
                return;
            }

            if ($method === 'GET' && $id !== null) {
                $this->view($id);
                return;
            }

            if ($method === 'POST' && $id === null) {
                $this->create($payload);
                return;
            }

            if ($method === 'PUT' && $id !== null) {
                $this->update($id, $payload);
                return;
            }

            if ($method === 'DELETE' && $id !== null) {
                $this->delete($id);
                return;
            }
        }

        $this->json(404, ['error' => 'NOT_FOUND', 'message' => 'Route not found']);
    }

    private function view(int $id): void
    {
        if ($id <= 0) {
            $this->json(422, ['error' => 'VALIDATION_ERROR', 'message' => 'id must be positive integer']);
            return;
        }

        $note = $this->repository->find($id);
        if ($note === null) {
            $this->json(404, ['error' => 'NOT_FOUND', 'message' => 'Note not found']);
            return;
        }

        $this->json(200, $note);
    }

    private function create(array $payload): void
    {
        $validated = $this->validatePayload($payload);
        if (isset($validated['error'])) {
            $this->json(422, $validated);
            return;
        }

        $id = $this->repository->create($validated['title'], $validated['content']);
        $note = $this->repository->find($id);
        $this->json(201, $note ?? []);
    }

    private function update(int $id, array $payload): void
    {
        if ($id <= 0) {
            $this->json(422, ['error' => 'VALIDATION_ERROR', 'message' => 'id must be positive integer']);
            return;
        }

        if ($this->repository->find($id) === null) {
            $this->json(404, ['error' => 'NOT_FOUND', 'message' => 'Note not found']);
            return;
        }

        $validated = $this->validatePayload($payload);
        if (isset($validated['error'])) {
            $this->json(422, $validated);
            return;
        }

        $this->repository->update($id, $validated['title'], $validated['content']);
        $note = $this->repository->find($id);
        $this->json(200, $note ?? []);
    }

    private function delete(int $id): void
    {
        if ($id <= 0) {
            $this->json(422, ['error' => 'VALIDATION_ERROR', 'message' => 'id must be positive integer']);
            return;
        }

        if ($this->repository->find($id) === null) {
            $this->json(404, ['error' => 'NOT_FOUND', 'message' => 'Note not found']);
            return;
        }

        $this->repository->delete($id);
        http_response_code(204);
    }

    private function validatePayload(array $payload): array
    {
        $title = $payload['title'] ?? null;
        $content = $payload['content'] ?? null;

        if (!is_string($title) || trim($title) === '') {
            return ['error' => 'VALIDATION_ERROR', 'message' => 'title is required'];
        }

        if (mb_strlen($title) > 255) {
            return ['error' => 'VALIDATION_ERROR', 'message' => 'title must be <= 255 chars'];
        }

        if (!is_string($content) || trim($content) === '') {
            return ['error' => 'VALIDATION_ERROR', 'message' => 'content is required'];
        }

        return [
            'title' => trim($title),
            'content' => trim($content),
        ];
    }

    private function json(int $status, array $payload): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    }
}
