<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\PostRepository;
use App\Repository\CategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PageController extends AbstractController
{
    #[Route('/', name: 'home', methods: ['GET'])]
    public function index(
        Request $request,
        PostRepository $postRepository,
        CategoryRepository $categoryRepository,
        PaginatorInterface $paginator
    ): Response {
        $posts = $paginator->paginate(
            $postRepository->findAll(), // Request
            $request->query->getInt('page', 1), // Page number
            9 // Limit per page
        );
        return $this->render('page/home.html.twig', [
            'posts' => $posts,
            'categories' => $categoryRepository->findAll()
        ]);
    }

    #[Route('/post/{slug}', name: 'post', methods: ['GET'])]
    public function post(
        // Inject the request object to get the category name (NE PAS REECRIRE TOUS LES COMMENTAIRES)
        Request $request,
        // Inject the category repository to find the category
        CategoryRepository $categoryRepository,
        // Inject the post repository to find all posts in the category
        PostRepository $postRepository,
    ): Response {
        // Find the category by its name
        $post = $postRepository->findOneBy([
            'slug' => $request->get('slug')
        ]);
        // Return the view
        return $this->render('page/post.html.twig', [
            // Pass the category object to the view
            'post' => $post,
        ]);
    }

    // Route for displaying a single category (NE PAS REECRIRE TOUS LES COMMENTAIRES)
    #[Route('/{category}', name: 'category', methods: ['GET'])]
    public function category(
        Request $request,
        CategoryRepository $categoryRepository,
        PostRepository $postRepository,
    ): Response {
        $category = $categoryRepository->findOneBy([
            'name' => $request->get('category')
        ]);
        return $this->render('page/category.html.twig', [
            'category' => $category,
            'posts' => $postRepository->findBy(['category' => $category]
            )
        ]);
    }
}
