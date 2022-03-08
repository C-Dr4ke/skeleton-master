<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Exception\InvalidPasswordException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Guard\PasswordAuthenticatedInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator implements PasswordHasherInterface
{
use TargetPathTrait;

public const LOGIN_ROUTE = 'login';

private $entityManager;
private $urlGenerator;
private $csrfTokenManager;
private $passwordEncoder;

public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator, CsrfTokenManagerInterface $csrfTokenManager, UserPasswordHasherInterface $passwordEncoder)
{
$this->entityManager = $entityManager;
$this->urlGenerator = $urlGenerator;
$this->csrfTokenManager = $csrfTokenManager;
$this->passwordEncoder = $passwordEncoder;
}

public function supports(Request $request): bool
{
return self::LOGIN_ROUTE === $request->attributes->get('_route')
&& $request->isMethod('POST');
}

public function getCredentials(Request $request)
{
$credentials = [
'email' => $request->request->get('email'),
'password' => $request->request->get('password'),

];
$request->getSession()->set(
Security::LAST_USERNAME,
$credentials['email']
);

return $credentials;
}

public function getUser($credentials, UserProviderInterface $userProvider): ?User
{
 //dd($credentials);


$user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $credentials['email']]);

//dd($user);
if (!$user) {
// fail authentication with a custom error
throw new CustomUserMessageAuthenticationException('Email could not be found.');
}

return $user;
}

public function checkCredentials($credentials, UserInterface $user): bool
{
return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
}

/**
* Used to upgrade (rehash) the user's password automatically over time.
*/
public function getPassword($credentials): ?string
{
return $credentials['password'];
}

public function onAuthenticationSucces($role): ?Response
{
   //dd($role);
    //dd(in_array('ROLE_ADMIN', $role ) );

    if (in_array('ROLE_ADMIN', $role ) ):
  return  new RedirectResponse('/');

    else:
        return  new RedirectResponse('/');

    endif;
}

protected function getLoginUrl(): string
{
return $this->urlGenerator->generate(self::LOGIN_ROUTE);
}

    public function hash(string $plainPassword): string
    {
        // TODO: Implement hash() method.
    }

    public function verify(string $hashedPassword, string $plainPassword): bool
    {
        // TODO: Implement verify() method.
    }

    public function needsRehash(string $hashedPassword): bool
    {
        // TODO: Implement needsRehash() method.
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        // TODO: Implement onAuthenticationSuccess() method.
    }
}