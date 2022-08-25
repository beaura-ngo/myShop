<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/products', name: 'app_products_')]
class ProductController extends AbstractController
{
    public function __construct(private ProductRepository $productRepo) {}

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        $allProducts = $this->productRepo->findAll();
        return $this->render('product/index.html.twig', compact('allProducts'));
    }

    #[Route('/{slug}', name: 'details')]
    public function showDetails(Product $product): Response
    {
        $relatedProducts = $this->productRepo->findProductInSameCategory($product);
        return $this->render('product/details.html.twig', compact('product', 'relatedProducts'));
    }
}
