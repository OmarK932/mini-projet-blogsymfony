<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        Request $request,
        PostRepository $postRepository
    ): Response {

        $search = $request->query->get('q');

        if ($search) {
            $posts = $postRepository->search($search);
        } else {
            $posts = $postRepository->findAllOrdered();
        }

        return $this->render('home/index.html.twig', [
            'posts' => $posts,
            'search' => $search,
            'user' => $this->getUser(),
        ]);
    }
}
