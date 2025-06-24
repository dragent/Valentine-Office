<?php
namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class DiscordAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private ClientRegistry $clientRegistry,
        private EntityManagerInterface $em,
        private UrlGeneratorInterface $urlGenerator,
        private JWTTokenManagerInterface $jwtManager,
    ) {}

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'connect_discord_check';
       
    }

    public function authenticate(Request $request): Passport
    {
        $client = $this->clientRegistry->getClient('discord');

        try {
            $discordUser = $client->fetchUser();
        } catch (\Exception $e) {
            throw new AuthenticationException('Erreur OAuth Discord : ' . $e->getMessage());
        }

        $data = $discordUser->toArray();

        $discordId = $data['id'];
        $email = $data['email'] ?? null;
        $username = $data['username'];
        $discriminator = $data['discriminator'];
        $avatar = $data['avatar'] ?? null;

        $avatarUrl = $avatar
            ? "https://cdn.discordapp.com/avatars/{$discordId}/{$avatar}.png"
            : "https://cdn.discordapp.com/embed/avatars/" . (intval($discriminator) % 5) . ".png";

        $user = $this->em->getRepository(User::class)->findOneBy(['discordId' => $discordId]);

        if (!$user) {
            $user = new User();
            $user->setDiscordId($discordId);
            $user->setUsername($username);
            $user->setEmail($email);
            $user->setAvatarUrl($avatarUrl);
            $this->em->persist($user);
            $this->em->flush();
        }

        return new SelfValidatingPassport(
            new UserBadge($user->getUserIdentifier(), fn() => $user)
        );
    }

    public function onAuthenticationSuccess(Request $request, \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token, string $firewallName): ?Response
    {
        $user = $token->getUser();
        $jwt = $this->jwtManager->create($user);

        return new JsonResponse(['token' => $jwt]);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new Response('Authentification Discord échouée : ' . $exception->getMessage(), Response::HTTP_UNAUTHORIZED);
    }
}
