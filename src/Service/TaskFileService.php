<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class TaskFileService
{
    public function __construct(
        private Filesystem $filesystem,
        #[Autowire('%kernel.project_dir%/var/tasks')]
        private string $taskDirectory
    ) {
        // Crée le dossier s'il n'existe pas
        if (!$this->filesystem->exists($this->taskDirectory)) {
            $this->filesystem->mkdir($this->taskDirectory);
        }
    }

    public function createTask(string $title, string $description): void
    {
        $id = uniqid();
        $filePath = $this->taskDirectory . '/' . $id . '.txt';
        $content = "Titre : " . $title . "\n\n" . "Description : " . $description;

        $this->filesystem->dumpFile($filePath, $content);
    }

    public function updateTask(string $id, string $title, string $description): void
    {
        $filePath = $this->taskDirectory . '/' . $id . '.txt';

        if ($this->filesystem->exists($filePath)) {
            $content = "Titre : " . $title . "\n\n" . "Description : " . $description;
            $this->filesystem->dumpFile($filePath, $content);
        } else {
            throw new \Exception("Le fichier de la tâche n'existe pas.");
        }
    }

    public function listTasks(): array
    {
        if (!$this->filesystem->exists($this->taskDirectory)) {
            return [];
        }

        $files = scandir($this->taskDirectory);
        $tasks = [];

        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'txt') {
                $content = file_get_contents($this->taskDirectory . '/' . $file);

                // Sécurisation de l'explode
                $parts = explode("\n\n", $content, 2);
                $title = isset($parts[0]) ? str_replace('Titre : ', '', $parts[0]) : 'Sans titre';
                $description = isset($parts[1]) ? str_replace('Description : ', '', $parts[1]) : '';

                $tasks[] = [
                    'id' => pathinfo($file, PATHINFO_FILENAME),
                    'title' => $title,
                    'description' => $description,
                ];
            }
        }
        return $tasks;
    }

    public function getTask(string $id): ?array
    {
        $filePath = $this->taskDirectory . '/' . $id . '.txt';

        if ($this->filesystem->exists($filePath)) {
            $content = file_get_contents($filePath);

            $parts = explode("\n\n", $content, 2);
            $title = isset($parts[0]) ? str_replace('Titre : ', '', $parts[0]) : 'Sans titre';
            $description = isset($parts[1]) ? str_replace('Description : ', '', $parts[1]) : '';

            return [
                'id' => $id,
                'title' => $title,
                'description' => $description,
            ];
        }
        return null;
    }

    public function deleteTask(string $id): void
    {
        $filePath = $this->taskDirectory . '/' . $id . '.txt';

        if ($this->filesystem->exists($filePath)) {
            $this->filesystem->remove($filePath);
        } else {
            throw new \Exception("Le fichier de la tâche n'existe pas.");
        }
    }
}