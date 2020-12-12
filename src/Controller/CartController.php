<?php

namespace App\Controller;

use App\Cart\CartService;
use App\Form\CartConfirmationType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    private $productRepository;
    private $cartService;
    public function __construct(ProductRepository $productRepository, CartService $cartService)
    {
        $this->productRepository = $productRepository;
        $this->cartService = $cartService;
    }

    /**
     * @Route("/cart/add/{id}", name="cart_add", requirements={"id":"\d+"})
     * @param $id
     * @param FlashBagInterface $flashBag
     * @param Request $request
     * @return Response
     */
    public function add($id, FlashBagInterface $flashBag, Request $request): Response
    {
        $product = $this->productRepository->find($id);
        if (!$product)
        {
            throw $this->createNotFoundException('l\'id que vous avez entrez n\'est pas dans la base');
        }
        $this->cartService->add($id);

        $flashBag->add('success', "produit ajouté au panier");
        if ($request->get('returnToCart'))
        {
            return $this->redirectToRoute('cart_show');
        }
        return $this->redirectToRoute('product_show', [
            'category_slug' => $product->getCategory()->getSlug(),
            'slug' => $product->getSlug()
        ]);
    }

    /**
     * @Route("/cart", name="cart_show")
     * @return Response
     */
    public function show()
    {
        $form = $this->createForm(CartConfirmationType::class);
        $detailCart = $this->cartService->getDetailCartItem();
        $total = $this->cartService->getTotal();

        return $this->render('cart/show.html.twig', [
            'items' => $detailCart,
            'total' => $total,
            'confirmationForm' => $form->createView()
        ]);
    }

    /**
     * @Route("cart/delete/{id}", name="cart_delete", requirements={"id": "\d+"})
     * @param $id
     * @return Response
     */
    public function delete($id)
    {
        $product = $this->productRepository->findAll($id);
        if (!$product)
        {
            throw $this->createNotFoundException("pas de produit à supprimer");
        }
        $this->cartService->remove($id);
        $this->addFlash('success',"le produit à bien été supprimer");

        return $this->redirectToRoute('cart_show');
    }

    /**
     * @Route("/cart/decremente/{id}", name="cart_decremente", requirements={"id": "\d+"})
     * @param $id
     * @return Response
     */
    public function decrement($id)
    {
        $product = $this->productRepository->findAll($id);
        if (!$product)
        {
            throw $this->createNotFoundException("pas de produit à supprimer");
        }
        $this->cartService->decrement($id);
        $this->addFlash('success', "produit enlevé");
        return $this->redirectToRoute("cart_show");
    }
}
