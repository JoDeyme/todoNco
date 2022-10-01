<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/users', name: 'users_list')]
    //seul les admins peuvent accéder à la liste des utilisateurs
    public function listUsers(UserRepository $userRepository): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', 'Vous devez être administrateur pour accéder à cette page.');
            return $this->redirectToRoute('task_list');
        }

        $users = $userRepository->findAll();

        return $this->render('user/list.html.twig', ['users' => $users]);
    }

    #[Route('/user/create', name: 'user_create')]

    public function createUser(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(['ROLE_USER']);

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );


            $userRepository->add($user, true);
            $this->addFlash('success', 'L\'utilisateur a été bien été ajouté.');

            return $this->redirectToRoute('homepage');
        }

        return $this->render('user/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/user/{id}/edit', name: 'user_edit')]
    //seul les admins peuvent modifier les utilisateurs

    public function editUser(Request $request, User $user, UserRepository $userRepository): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', 'Vous devez être administrateur pour accéder à cette page.');
            return $this->redirectToRoute('task_list');
        }
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->add($user, true);
            $this->addFlash('success', 'L\'utilisateur a été bien modifié.');

            return $this->redirectToRoute('users_list');
        }
        return  $this->render('user/edit.html.twig', [
            'form' => $form->createView(), 'user' => $user
        ]);
    }
}
