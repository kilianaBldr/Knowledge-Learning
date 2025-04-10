<?php

namespace App\Controller;

use App\Entity\Lessons;
use App\Entity\Cursus;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart_show')]
    public function show(CartService $cartService, EntityManagerInterface $em): Response
    {
        $cart = $cartService->getCart();
        $lessons = $em->getRepository(Lessons::class)->findBy(['id' => $cart['lessons']]);
        $cursuses = $em->getRepository(Cursus::class)->findBy(['id' => $cart['cursuses']]);
        $total = $cartService->getTotal($em);

        return $this->render('cart/panier.html.twig', [
            'lessons' => $lessons,
            'cursuses' => $cursuses,
            'total' => $total,
        ]);
    }
    #[Route('/cart/add/lesson/{id}', name: 'app_cart_add_lesson')]
    public function addLesson(int $id, CartService $cartService): Response
    {
        $cartService->addLesson($id);
        return $this->redirectToRoute('app_cart_show');
    }

    #[Route('/cart/add/cursus/{id}', name: 'app_cart_add_cursus')]
    public function addCursus(int $id, CartService $cartService): Response
    {
        $cartService->addCursus($id);
        return $this->redirectToRoute('app_cart_show');
    }

    #[Route('/cart/remove/cursus/{id}', name: 'app_cart_remove_cursus')]
    public function removeCursus(int $id, CartService $cartService): RedirectResponse
    {
        $cartService->removeCursus($id);
        return $this->redirectToRoute('app_cart_show');
    }

    #[Route('/cart/remove/lesson/{id}', name: 'app_cart_remove_lesson')]
    public function removeLesson(int $id, CartService $cartService): RedirectResponse
    {
        $cartService->removeLesson($id);
        return $this->redirectToRoute('app_cart_show');
    }

    #[Route('/cart/clear', name: 'app_cart_clear')]
    public function clear(CartService $cartService): Response
    {
        $cartService->clearCarte();
        return $this->redirectToRoute('app_cart_show');
    }
}
