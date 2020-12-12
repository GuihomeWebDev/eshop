<?php


namespace App\EventDispatcher;


use App\Event\ProductViewEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductViewEmailSubscriber implements EventSubscriberInterface
{
    private $logger;
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function sendEmail(ProductViewEvent $event)
    {
        $this->logger->info("Vous avez vue ce Produit n°" . $event->getProduct()->getId());
    }

    public static function getSubscribedEvents():array
    {
        return [
        'product.view' => 'sendEmail'
    ];
    }
}