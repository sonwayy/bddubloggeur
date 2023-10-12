<?php

namespace App\Controller;

use App\Entity\Post;
//use App\Entity\Comment;
//use App\Form\CommentType;
use App\Form\PostFormType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


class PostController extends AbstractController
{
    private $em;
    private $postRepository;
    public function __construct(PostRepository $postRepository, EntityManagerInterface $em)
    {
        $this->postRepository = $postRepository;
        $this->em = $em;
    }

    #[Route('/post', methods: ['GET'], name: 'app_post')]
    public function index(): Response
    {
        // Get all the posts
        $posts = $this->postRepository->findAll();
        dd($posts);

        // Render the view
        return $this->render('post/index.html.twig', [
            'controller_name' => 'PostController',
            'posts' => $posts
        ]);
    }


    #[Route('/post/create', name: 'create_post')]
    public function create(Request $request): Response
    {

        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }
        // Create a new post
        $post = new Post();
        $form = $this->createForm(PostFormType::class, $post);
        $form->handleRequest($request);

        // If the form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            $newPost = $form->getData();

            // Get the image
            $thumbnailPath = $form->get('thumbnailPath')->getData();

            // If there is an image we are giving it a unique name and we are storing it in the public folder
            if($thumbnailPath){
                $newFileName = uniqid() . '.' . $thumbnailPath->guessExtension();
                try {
                    $thumbnailPath->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads',
                        $newFileName
                    );
                } catch (FileException $e) {
                    return new Response($e->getMessage());
                }
                $newPost->setThumbnailPath('/uploads/' . $newFileName);
            }

            // Save the post
            $newPost->setPublishdate(new \DateTime());
            $user = $this->getUser();
            $newPost->setUserId($user->getId());
            $newPost->setUserName($user->getNickname());

            // Save the post in the database
            $this->em->persist($newPost);
            $this->em->flush();

            return $this->redirectToRoute('app_home');
        }


        return $this->render('post/create.html.twig', [
            'controller_name' => 'PostController',
            'form' => $form->createView()
        ]);
    }

    #[Route('/post/edit/{id}', name: 'edit_post')]
    public function edit(Request $request, $id): Response
    {
        // edit a post

        $post = $this->postRepository->find($id);
        $form = $this->createForm(PostFormType::class, $post);

        $form->handleRequest($request);

        // Getting the original thumbnail
        $thumbnailPath = $form->get('thumbnailPath')->getData();

        if($form->isSubmitted() && $form->isValid()){

            if($thumbnailPath){
                // If there is a new image we are giving it a unique name and we are storing it in the public folder
                if ($post->getThumbnailPath() !== null) {
                    if (file_exists($this->getParameter('kernel.project_dir') . $post->getThumbnailPath())) {
                        $this->getParameter('kernel.project_dir') . $post->getThumbnailPath();
                    }
                    $newFileName = uniqid() . '.' . $thumbnailPath->guessExtension();

                    try {
                        $thumbnailPath->move(
                            $this->getParameter('kernel.project_dir') . '/public/uploads',
                            $newFileName
                        );
                    } catch (FileException $e) {
                        return new Response($e->getMessage());
                    }

                    $post->setThumbnailPath('/uploads/' . $newFileName);
                    $this->em->flush();

                    return $this->redirectToRoute('app_home');
                }
            }else{
                // If there is no new image we are keeping the original one
                $post->setTitle($form->get('title')->getData());
                $post->setPublishDate(new \DateTime());
                $post->setBody($form->get('body')->getData());

                $this->em->flush();

                return $this->redirectToRoute('app_home');

            }
        }

        return $this->render('post/edit.html.twig', [
            'controller_name' => 'PostController',
            'post' => $post,
            'form' => $form->createView()
        ]);
    }

    #[Route('/post/delete/{id}', methods: ['GET', 'DELETE'], name: 'delete_post')]
    public function delete($id): Response
    {
        // Delete a post

        $post = $this->postRepository->find($id);
        $this->em->remove($post);
        $this->em->flush();

        return $this->redirectToRoute('app_home');
    }

    #[Route('/post/{id}', methods: ['GET', 'POST'], name: 'show_post')]
    public function show($id, Request $request, ManagerRegistry $doctrine): Response
    {

        // finding the post
        $post = $this->postRepository->find($id);

        /*// creating a new comment
        $comment = new Comment();
        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);

        // if the comment form is submitted and is valid
        if($commentForm->isSubmitted() && $commentForm->isValid()){

            // setting the nw comment
            $newComment = $commentForm->getData();
            $newComment->setPost($post);
            $newComment->setCreatedAt(new \DateTimeImmutable());
            $newComment->setNickname($this->getUser()->getNickname());

            // getting the parent of the comment
            $parentid = $commentForm->get('parentid')->getData();

            if($parentid != null){
                //
                $parent = $this->em->getRepository(Comment::class)->find($parentid);
            }

            // if the parent is null
            $comment->setParent($parent ?? null);

            // persisting the comment
            $this->em->persist($newComment);
            $this->em->flush();
            $this->addFlash('success', 'Comment added');

            return $this->redirectToRoute('show_post', ['id' => $post->getId()]);
        }*/

        return $this->render('post/show.html.twig', [
            'controller_name' => 'PostController',
            'post' => $post,
            /*'commentForm' => $commentForm->createView()*/
        ]);
    }
}
