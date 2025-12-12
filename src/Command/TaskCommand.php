<?php

namespace App\Command;

use App\Service\TaskFileService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:tasks', // J'ai mis le nom directement ici
    description: 'Gérer les fichiers de tâches (create, list, update, delete)',
)]
class TaskCommand extends Command
{
    public function __construct(
        private TaskFileService $taskFileService
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('action', InputArgument::REQUIRED, 'L\'action à effectuer : create, list, view, update, delete')
            ->addArgument('id', InputArgument::OPTIONAL, 'L\'ID de la tâche (pour view, update, delete)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Récupération des arguments
        $action = $input->getArgument('action');
        $id = $input->getArgument('id');

        // On utilise match (PHP 8) pour diriger vers la bonne logique
        try {
            switch ($action) {
                case 'create':
                    $io->title('Création d\'une tâche');
                    $title = $io->ask('Titre de la tâche ?');
                    $desc = $io->ask('Description ?');

                    $this->taskFileService->createTask($title, $desc);
                    $io->success('Tâche créée avec succès !');
                    break;

                case 'list':
                    $io->title('Liste des tâches');
                    $tasks = $this->taskFileService->listTasks();

                    if (empty($tasks)) {
                        $io->warning('Aucune tâche trouvée.');
                    } else {
                        // Affichage propre en tableau
                        $io->table(
                            ['ID', 'Titre', 'Description'],
                            $tasks
                        );
                    }
                    break;

                case 'view':
                    if (!$id) {
                        $io->error('Vous devez fournir un ID pour voir une tâche.');
                        return Command::FAILURE;
                    }

                    $task = $this->taskFileService->getTask($id);
                    if ($task) {
                        $io->section('Détails de la tâche : ' . $id);
                        $io->writeln('<info>Titre :</info> ' . $task['title']);
                        $io->writeln('<info>Description :</info> ' . $task['description']);
                    } else {
                        $io->error('Tâche introuvable.');
                    }
                    break;

                case 'update':
                    if (!$id) {
                        $io->error('Vous devez fournir un ID pour modifier une tâche.');
                        return Command::FAILURE;
                    }

                    // On vérifie d'abord si la tâche existe
                    $existingTask = $this->taskFileService->getTask($id);
                    if (!$existingTask) {
                        $io->error('Tâche introuvable.');
                        return Command::FAILURE;
                    }

                    $io->section('Modification de la tâche : ' . $existingTask['title']);

                    // On propose de garder l'ancienne valeur par défaut
                    $newTitle = $io->ask('Nouveau titre', $existingTask['title']);
                    $newDesc = $io->ask('Nouvelle description', $existingTask['description']);

                    $this->taskFileService->updateTask($id, $newTitle, $newDesc);
                    $io->success('Tâche mise à jour avec succès.');
                    break;

                case 'delete':
                    if (!$id) {
                        $io->error('Vous devez fournir un ID pour supprimer une tâche.');
                        return Command::FAILURE;
                    }

                    if ($io->confirm('Êtes-vous sûr de vouloir supprimer la tâche ' . $id . ' ?', false)) {
                        $this->taskFileService->deleteTask($id);
                        $io->success('Tâche supprimée.');
                    } else {
                        $io->note('Suppression annulée.');
                    }
                    break;

                default:
                    $io->error(sprintf('Action "%s" inconnue. Actions possibles: create, list, view, update, delete', $action));
                    return Command::INVALID;
            }
        } catch (\Exception $e) {
            $io->error('Erreur : ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}