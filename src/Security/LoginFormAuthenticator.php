<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class LoginFormAuthenticator extends AbstractGuardAuthenticator
{
    private $encoder;
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function supports(Request $request): bool
    {
        return $request->attributes->get('_route') === 'security_login'
            && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        return $request->request->get('login');
    }

    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
    {
        try {
            return $userProvider->loadUserByUsername($credentials['email']);
        } catch (UsernameNotFoundException $e) {
            throw new AuthenticationException("adresse mail n'existe pas !!");
        }

    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        $isValid = $this->encoder->isPasswordValid($user, $credentials['password']);
        if (!$isValid){
            throw new AuthenticationException( "password invalide essaie encore :)");
        }
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?\Symfony\Component\HttpFoundation\Response
    {
        $request->attributes->set(Security::AUTHENTICATION_ERROR, $exception);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey): RedirectResponse
    {
        return new RedirectResponse('/');
    }

    public function start(Request $request, AuthenticationException $authException = null): \Symfony\Component\HttpFoundation\Response
    {
        return new RedirectResponse('/login');
    }

    public function supportsRememberMe(): bool
    {
        // todo
    }
}