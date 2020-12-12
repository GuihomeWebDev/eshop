<?php


namespace App\EventDispatcher;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class PrenomSubscriber implements EventSubscriberInterface
{
    public function addPrenomToAtttributes(RequestEvent $requestEvent)
    {
        $requestEvent->getRequest()->attributes->set('prenom', 'GuihomeEasterEgg');
    }
    public function test()
    {
        dump("test");
    }
    public function testing()
    {
        dump("testing");
    }
  public static function getSubscribedEvents()
    {
        return [
          'kernel.request' => 'addPrenomToAtttributes',
          'kernel.controller' => 'test',
          'kernel.response' => 'testing',
        ];
    }
}