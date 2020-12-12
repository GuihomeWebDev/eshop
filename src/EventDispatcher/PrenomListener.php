<?php


namespace App\EventDispatcher;


use Symfony\Component\HttpKernel\Event\RequestEvent;

class PrenomListener
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
}