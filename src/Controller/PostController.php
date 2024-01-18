<?php

namespace App\Controller;

use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostController extends AbstractController
{
    #[Route('/post/{slug}/edit', name: 'post_edit')]
    public function edit(
        // Inject the request object to get the category name
        Request                $request,
        // Inject the category repository to find the category
        PostRepository         $postRepository,
        // Inject the post repository to find all posts in the category
        EntityManagerInterface $em
    ): Response
    {
        // Find the category by its name
        $post = $postRepository->findOneBy([
            'slug' => $request->get('slug')
        ]);
        // TODO Add the form here
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request); // Handle the request
        if ($form->isSubmitted() && $form->isValid()) {
            // If the form is submitted and valid
            $slug = str_replace(' ', '-', strtolower($form->get('title')->getData()));
            $extract = substr($form->get('content')->getData(), 0, strpos($form->get('content')->getData(), ' ', 200)) . '...';
            // Upload the image
            if ($form->get('image')->getData()) {
                $imageFile = $form->get('image')->getData();
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                    $post->setImage($newFilename); // Set the new filename in the category
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Une erreur est survenue lors de l\'upload de votre Image.');
                }
            }

            $post->setTitle($form->get('title')->getData()); // Set the new title in the post
            $post->setSlug($slug); // Set the new slug in the post
            $post->setExtract($extract); // Set the new extract in the post
            $post->setContent($form->get('content')->getData()); // Set the new content in the post
            $post->setIspublished($form->get('ispublished')->getData()); // Set the new ispublished in the category
            $em->persist($post);
            $em->flush();
            return $this->redirectToRoute('post', ['slug' => $post->getSlug()]);
        }
        // Return the view
        return $this->render('post/edit.html.twig', [
            // Pass the category object to the view
            'title' => 'Modifier le Post ' . $post->getTitle(),
            'post' => $post,
            'editForm' => $form
        ]);
    }

    // Route to add a new category
    #[Route('/new-post', name: 'post_new')]
    public function new(
        // Inject the request object to get the data from the form
        Request                $request,
        // Add the EntityManagerInterface to save the category
        EntityManagerInterface $em,
    ): Response
    {
        // TODO Add the form here

        // TODO Add the form proccess here

        // Return the view
        return $this->render('post/new.html.twig', [
            // Pass the form to the view
        ]);
    }

    // Route to delete a category
}