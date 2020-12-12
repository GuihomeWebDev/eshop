<?php

namespace App\Cart;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Cart\CartItem;

class CartService
{
    private $session;
    private $productRepository;
    public function __construct(SessionInterface $session, ProductRepository $productRepository)
    {
        $this->session = $session;
        $this->productRepository = $productRepository;
    }

    protected function getCart() :array
    {
        return $this->session->get('cart', []);
    }

    protected function saveCart(array $cart)
    {
        $this->session->set('cart', $cart);
    }

    public function add($id)
    {
        $cart = $this->getCart();
        if (!array_key_exists($id, $cart))
        {
            $cart[$id] = 0;
        }
            $cart[$id]++;

        $this->saveCart($cart);
    }

    public function empty()
    {
        $this->saveCart([]);
    }

    public function remove($id)
    {
        $cart = $this->getCart();
        unset($cart[$id]);
        $this->saveCart($cart);
    }

    public function decrement(int $id):void
    {
        $cart = $this->getCart();
        if (!array_key_exists($id, $cart)) {
            return;
        }
        if ($cart[$id] === 1)
        {
            $this->remove($id);
            return;
        }
        $cart[$id] --;
        $this->saveCart($cart);
    }

    public function getTotal() :int
    {
        $total = 0;

        foreach ($this->getCart() as $id => $qty)
        {
            $product = $this->productRepository->find($id);
            if (!$product)
            {
                continue;
            }
            $total += ($product->getPrice() * $qty);
        }
        return $total;
    }

    /**
     * @return CartItem[]
     */
    public function getDetailCartItem() :array
    {
        $detailCart = [];
        foreach ($this->getCart() as $id => $qty)
        {
            $product = $this->productRepository->find($id);
            if (!$product)
            {
                continue;
            }
            $detailCart[] = new CartItem($product, $qty);
        }
        return $detailCart;
    }
}
