<?php

namespace App\Controller\Purchase;

use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PurchasesListController extends AbstractController
{
    /**
     * @Route("/purchases", name="purchase_index")
     * @IsGranted("ROLE_USER", message="vous devez vous connecter pour voir vos commandes")
     */
    public function index()
    {
        /**
         * @var User
         */
        $user = $this->getUser();
       return $this->render('purchase/index.html.twig', [
           'purchase' => $user->getPurchases(),
       ]);

    }
}