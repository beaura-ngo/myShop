<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/category', name: 'app_category_')]
class CategoryController extends AbstractController
{
    public function __construct(private CategoryRepository $categoryRepo, private ProductRepository $productRepo) {}

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        $categories = $this->categoryRepo->findAll();
        return $this->render('category/index.html.twig', compact('categories'));
    }

    #[Route('/{name}', name: 'related')]
    public function showRelated(Category $category, String $name): Response
    {
        $categoryId = $category->getId();
        $categorizeProducts = $this->productRepo->findProductByCategory($categoryId);
        return $this->render('category/categorizeProducts.html.twig', compact('category', 'categorizeProducts'));
    }
}
