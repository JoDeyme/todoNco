<?php

namespace App\Controller;

use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\TaskType;
use App\Repository\TaskRepository;

class TaskController extends AbstractController
{
    #[Route('/tasks', name: 'task_list')]
    public function listTasks(TaskRepository $taskRepository): Response
    {
        $tasks = $taskRepository->findAll();


        return $this->render('task/list.html.twig', ['tasks' => $tasks]);
    }

    //retourne la liste des tasks avec isdone=true
    #[Route('/tasks/done', name: 'task_list_done')]
    public function listDoneAction(TaskRepository $taskRepository): Response
    {
        $tasks = $taskRepository->findBy(['isDone' => true]);

        return $this->render('task/list.html.twig', ['tasks' => $tasks]);
    }

    #[Route('/tasks/create', name: 'task_create')]
    public function createTask(Request $request, TaskRepository $taskRepository): Response
    {
        //user must be login 
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Vous devez être connecté pour créer une tâche.');
            return $this->redirectToRoute('app_login');
        }

        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setCreatedAt(new \DateTimeImmutable());
            $task->setIsDone(false);
            $task->setUser($this->getUser());
            $taskRepository->add($task, true);
            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/tasks/{id}/edit', name: 'task_edit')]
    public function editTask(Request $request, Task $task, TaskRepository $taskRepository): Response
    {
        //user must be login and be the owner of the task or be admin
        if (!$this->getUser() || ($this->getUser() != $task->getUser() && !$this->isGranted('ROLE_ADMIN'))) {
            $this->addFlash('danger', 'Seul l\'auteur de la tâche peut la modifier.');
            return $this->redirectToRoute('task_list');
        }

        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $taskRepository->add($task, true);
            $this->addFlash('success', 'La tâche a été bien modifiée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    #[Route('/tasks/{id}/toggle', name: 'task_toggle')]
    public function toggleTask(Task $task, TaskRepository $taskRepository): Response
    {
        if ($task->getUser() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN') || $task->getUser() === null) {

            $this->addFlash('danger', 'Seul l\'auteur de la tâche peut la marquer comme faite.');
            return $this->redirectToRoute('task_list');
        }

        $task->toggle(!$task->isDone());
        $taskRepository->add($task, true);
        $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));

        return $this->redirectToRoute('task_list');
    }

    #[Route('/tasks/{id}/delete', name: 'task_delete')]
    public function deleteTask(Task $task, TaskRepository $taskRepository): Response
    {
        if ($task->getUser() !== $this->getUser() && ($task->getUser() !== null || !$this->isGranted('ROLE_ADMIN'))) {
            if ($task->getUser() === null) {
                $this->addFlash('danger', 'Seul un utilisateur non identifié peut supprimer cette tâche.');
            } else {
                $this->addFlash('danger', 'Seul l\'auteur de la tâche peut la supprimer.');
            }
            return $this->redirectToRoute('task_list');
        }
        $taskRepository->remove($task, true);
        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('task_list');
    }
}
