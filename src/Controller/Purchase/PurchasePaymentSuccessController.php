<?php


namespace App\Controller\Purchase;


use App\Cart\CartService;
use App\Entity\Purchase;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PurchasePaymentSuccessController extends AbstractController
{
    /**
     * @Route("/purchase/finish/{id}", name="purchase_payment_success", requirements={"id":"\d+"})
     * @IsGranted ("ROLE_USER", message="Il faut être connecté pour acceder à cette page")
     * @param $id
     * @param PurchaseRepository $purchaseRepository
     * @param EntityManagerInterface $em
     * @param CartService $cartService
     * @return RedirectResponse|Response
     */
    public function success($id, PurchaseRepository $purchaseRepository, EntityManagerInterface $em, CartService $cartService)
    {
        $purchase = $purchaseRepository->find($id);
        if (!$purchase ||
            ($purchase->getUser() !== $this->getUser()) ||
            ($purchase->getStatus() === Purchase::STATUS_PAID))
        {
            $this->addFlash('warning', "une Erreur c\'est produite");
            return $this->redirectToRoute('purchase_index');
        }
            $purchase->setStatus(Purchase::STATUS_PAID);
            $em->flush();

            $cartService->empty();
            $this->addFlash('success', "Votre commande à bien été payée");
            return $this->redirectToRoute('purchase_index');

    }
}