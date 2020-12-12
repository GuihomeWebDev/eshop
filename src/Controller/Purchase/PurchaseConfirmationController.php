<?php


namespace App\Controller\Purchase;


use App\Cart\CartService;
use App\Entity\Purchase;
use App\Form\CartConfirmationType;
use App\purchase\PurchasePersister;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PurchaseConfirmationController extends abstractController
{
    /**
     * @Route("/purchase/confirm", name="purchase_confirm")
     * @IsGranted("ROLE_USER", message="Vous devez être connecté pour faire une commande")
     * @param Request $request
     * @param CartService $cartService
     * @param PurchasePersister $persister
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function confirm(Request $request, CartService $cartService, PurchasePersister $persister)
    {
        $form = $this->createForm(CartConfirmationType::class);
        $form->handleRequest($request);

        if (!$form->isSubmitted())
        {
            $this->addFlash('warning', 'remplissez le formulaire de confirmation');
            return $this->render('cart_show');
        }


        $cartItems = $cartService->getDetailCartItem();
        if (count($cartItems) === 0)
        {
            $this->addFlash('warning', "votre panier doit contenir des articles");
            return $this->redirectToRoute('cart_show');
        }
        /**
         * @var Purchase
         */
        $purchase = $form->getData();
        $persister->storePurchase($purchase);
        $cartService->empty();
        $this->addFlash('success', "Votre commande à bien été validée");
        return $this->redirectToRoute('purchase_index');
    }
}