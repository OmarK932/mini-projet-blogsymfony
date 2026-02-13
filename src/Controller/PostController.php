<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Comment;
use App\Form\PostType;
use App\Form\CommentType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/post')]
final class PostController extends AbstractController
{
    #[Route(name: 'app_post_index', methods: ['GET'])]
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('post/index.html.twig', [
            'posts' => $postRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_post_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile|null $pictureFile */
            $pictureFile = $form->get('pictureFile')->getData();

            if ($pictureFile) {
                $originalFilename = pathinfo(
                    $pictureFile->getClientOriginalName(),
                    PATHINFO_FILENAME
                );

                $safeFilename = $slugger->slug($originalFilename);
                $extension = $pictureFile->getClientOriginalExtension() ?: 'bin';

                $newFilename = $safeFilename . '-' . uniqid() . '.' . strtolower($extension);

                $pictureFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/uploads',
                    $newFilename
                );

                $post->setPicture('/uploads/' . $newFilename);
            }

            $post->setAuthor($this->getUser());
            $post->setPublishedAt(new \DateTime());

            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('app_post_index');
        }

        return $this->render('post/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_post_show', methods: ['GET', 'POST'])]
    public function show(
        Request $request,
        Post $post,
        EntityManagerInterface $entityManager
    ): Response {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($this->getUser()) {
            if ($form->isSubmitted() && $form->isValid()) {

                $comment->setUser($this->getUser());
                $comment->setPost($post);
                $comment->setStatus('en attente');

                $entityManager->persist($comment);
                $entityManager->flush();

                return $this->redirectToRoute('app_post_show', [
                    'id' => $post->getId(),
                ]);
            }
        }

        return $this->render('post/show.html.twig', [
            'post' => $post,
            'commentForm' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_post_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(
        Request $request,
        Post $post,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile|null $pictureFile */
            $pictureFile = $form->get('pictureFile')->getData();

            if ($pictureFile) {
                $originalFilename = pathinfo(
                    $pictureFile->getClientOriginalName(),
                    PATHINFO_FILENAME
                );

                $safeFilename = $slugger->slug($originalFilename);
                $extension = $pictureFile->getClientOriginalExtension() ?: 'bin';

                $newFilename = $safeFilename . '-' . uniqid() . '.' . strtolower($extension);

                $pictureFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/uploads',
                    $newFilename
                );

                $post->setPicture('/uploads/' . $newFilename);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_post_index');
        }

        return $this->render('post/edit.html.twig', [
            'form' => $form,
            'post' => $post,
        ]);
    }

    #[Route('/{id}', name: 'app_post_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(
        Request $request,
        Post $post,
        EntityManagerInterface $entityManager
    ): Response {
        if (
            $this->isCsrfTokenValid(
                'delete' . $post->getId(),
                $request->request->get('_token')
            )
        ) {
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_post_index');
    }
}
