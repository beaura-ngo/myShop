<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/cart', name: 'app_cart_')]
class CartController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(SessionInterface $session, ProductRepository $productRepo): Response
    {   
        $total = 0;

        $panier = $session->get('panier', []);
        $panierData = [];

        foreach ($panier as $id => $quantity) {
            $panierData[] = [
                'product' => $productRepo->find($id),
                'quantity' => $quantity
            ];
        }

        foreach ($panierData as $couple) {
            $total += $couple['product']->getPrice() * $couple['quantity'];
        }
        
        return $this->render('cart/index.html.twig', [
            'controller_name' => 'CartController',
            'dataElement' => $panierData,
            'total' => $total
        ]);
    }

    #[Route('redirect', name: 'redirect')]
    public function redir(): RedirectResponse
    {
        return $this->render('security/login.html.twig');
    }
    
    #[Route('/add/{id}', name:'add')]
    public function addToCart($id,
                              SessionInterface $session,
                              ManagerRegistry $doctrine
                              )
    {
        $entityManager = $doctrine->getManager();
        $product = $entityManager->getRepository(Product::class)->find($id);
        $slug = $product->getSlug();

        $panier = $session->get('panier', []);

        if (empty($panier[$id])) {
            $panier[$id] = 0;
        }

        $panier[$id]++;

        $session->set('panier', $panier);

        $this->addFlash('success', $product->getName() . ' has been added');

        return $this->redirectToRoute('app_products_details', ['slug' => $slug] );
    }

    #[Route('/add/{id}/one', name:'add_one')]
    public function addOneToCart($id,
                              SessionInterface $session,
                              ManagerRegistry $doctrine
                              )
    {
        $entityManager = $doctrine->getManager();
        $product = $entityManager->getRepository(Product::class)->find($id);
        $slug = $product->getSlug();

        $panier = $session->get('panier', []);

        if (empty($panier[$id])) {
            $panier[$id] = 0;
        }

        $panier[$id]++;

        $session->set('panier', $panier);

        return $this->redirectToRoute('app_cart_index', ['slug' => $slug] );
    }

    #[Route('/delete/{id}', name:'remove')]
    public function deleteFromCart($id,
                              SessionInterface $session,
                              ManagerRegistry $doctrine
                              )
    {
        $entityManager = $doctrine->getManager();
        $product = $entityManager->getRepository(Product::class)->find($id);
        $slug = $product->getSlug();

        $panier = $session->get('panier', []);

        if (empty($panier[$id])) {
            $panier[$id] = 0;
        }

        if ($panier[$id] > 0) {
            $panier[$id]--;
        }

        $session->set('panier', $panier);

        return $this->redirectToRoute('app_cart_index', ['slug' => $slug] );
    }

    #[Route('/delete', name:'deleteAll')]
    public function deleteAll(SessionInterface $session)
    {
        $total = 0;
        $session->set('panier', []);
        $panierData = [];
        
        return $this->render('cart/index.html.twig', [
            'controller_name' => 'CartController',
            'dataElement' => $panierData,
            'total' => $total
        ]);
    }

    #[Route('/pay', name:'pay')]
    public function pay()
    {
        if (!$this->getUser()) {
            $this->addFlash('warning', 'You should first log in before payment');
            return $this->redirectToRoute('app_login');
        }

        if (random_int(100, 999) % 2) {
            $this->addFlash('warning', "Payment failed! You don't have enough credit");
            return $this->redirectToRoute('app_cart_index');
        }

        $this->addFlash('success', 'Thank you for your purchase!');
        return $this->redirectToRoute('app_home');
    }
}
