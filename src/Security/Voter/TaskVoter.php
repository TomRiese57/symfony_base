<?php

namespace App\Security\Voter;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class TaskVoter extends Voter
{
    // On définit les actions possibles
    public const EDIT = 'TASK_EDIT';
    public const VIEW = 'TASK_VIEW';
    public const DELETE = 'TASK_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // Ce voter ne s'active que si l'attribut est dans la liste ET que le sujet est une Task
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE])
            && $subject instanceof Task;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // Si l'utilisateur n'est pas connecté, accès refusé
        if (!$user instanceof UserInterface) {
            return false;
        }

        // Si l'utilisateur est ADMIN, il a tous les droits
        // On retourne true immédiatement
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        /** @var Task $task */
        $task = $subject;

        // On vérifie les règles spécifiques
        return match ($attribute) {
            self::EDIT => $this->canEdit($task, $user),
            self::VIEW => $this->canView($task, $user),
            self::DELETE => $this->canDelete($task, $user),
            default => false,
        };
    }

    private function canEdit(Task $task, UserInterface $user): bool
    {
        // Règle : Seul l'auteur de la tâche peut la modifier
        return $user === $task->getAuthor();
    }

    private function canView(Task $task, UserInterface $user): bool
    {
        // Règle : Un utilisateur peut seulement voir les tâches dont il est l'auteur
        return $this->canEdit($task, $user); // La logique est la même que pour l'édition
    }

    private function canDelete(Task $task, UserInterface $user): bool
    {
        // Règle : Un utilisateur (non-admin) ne peut pas supprimer une tâche
        // Comme les admins sont déjà gérés plus haut, ici on retourne toujours false
        return false;
    }
}