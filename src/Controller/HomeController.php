<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $em;
    private $postRepository;
    public function __construct(PostRepository $postRepository, EntityManagerInterface $em)
    {
        $this->postRepository = $postRepository;
        $this->em = $em;
    }

    #[Route('/', name: 'app_home')]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        // Get all the posts
        $posts = $this->postRepository->findAll();

        $pagination = $paginator->paginate(
            $posts,
            $request->query->getInt('page', 1),
            6
        );

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'posts' => $pagination
        ]);
    }
}
