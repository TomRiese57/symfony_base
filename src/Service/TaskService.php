<?php

namespace App\Service;

use App\Entity\Task;

class TaskService
{
    /**
     * Vérifie si la tâche a été créée il y a moins de 7 jours.
     */
    public function canEdit(Task $task): bool
    {
        $createdAt = $task->getCreatedAt();

        // Sécurité : si pas de date, on interdit ou autorise par défaut (ici on interdit)
        if (!$createdAt) {
            return false;
        }

        // On calcule la date limite : "Maintenant moins 7 jours"
        $limitDate = new \DateTimeImmutable('-7 days');

        // Si la date de création est PLUS GRANDE (donc plus récente) que la date limite
        // Alors c'est autorisé.
        return $createdAt > $limitDate;
    }
}