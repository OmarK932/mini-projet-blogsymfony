<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]


class AdminController extends AbstractController
{
#[Route('', name: 'admin_dashboard')]
public function dashboard(): Response
{
    return $this->render('admin/dashboard.html.twig');
}

    // =========================
    // Gestion des commentaires
    // =========================

    #[Route('/comments', name: 'admin_comments')]
    public function comments(CommentRepository $commentRepository): Response
    {
        return $this->render('admin/comments.html.twig', [
            'comments' => $commentRepository->findBy(
                ['status' => 'en attente'],
                ['createdAt' => 'DESC']
            ),
        ]);
    }

    #[Route('/comment/{id}/approve', name: 'admin_comment_approve')]
    public function approve(
        int $id,
        CommentRepository $commentRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $comment = $commentRepository->find($id);

        if ($comment) {
            $comment->setStatus('approuvé');
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_comments');
    }

    // =========================
    // Gestion des utilisateurs
    // =========================

    #[Route('/users', name: 'admin_users')]
    public function users(UserRepository $userRepository): Response
    {
        return $this->render('admin/users.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/user/{id}/toggle', name: 'admin_user_toggle')]
    public function toggleUser(
        User $user,
        EntityManagerInterface $entityManager
    ): Response {
        $user->setIsActive(!$user->isActive());
        $entityManager->flush();

        return $this->redirectToRoute('admin_users');
    }
}
