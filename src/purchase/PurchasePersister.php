<?php


namespace App\purchase;



use App\Cart\CartService;
use App\Entity\PurchaseItem;
use App\Entity\Purchase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class PurchasePersister
{
    protected $security;
    protected $cartService;
    protected $em;
    public function __construct(Security $security, CartService $cartService, EntityManagerInterface $em)
    {
        $this->security = $security;
        $this->cartService = $cartService;
        $this->em = $em;
    }
    public function storePurchase(Purchase $purchase)
    {
        $purchase->setUser($this->security->getUser())
                 ->setPurchasedAt(new \DateTime('now'))
                 ->setTotal($this->cartService->getTotal());

        $this->em->persist($purchase);

        foreach ($this->cartService->getDetailCartItem() as $cartItem)
        {
            $purchaseItem = new PurchaseItem();
            $purchaseItem->setPurchase($purchase)
                ->setProduct($cartItem->product)
                ->setProductName($cartItem->product->getPrice())
                ->setQuantity($cartItem->qty)
                ->setTotal($cartItem->getTotal())
                ->setProductPrice($cartItem->product->getPrice());
            $this->em->persist($purchase);
        }

        $this->em->flush();
    }
}