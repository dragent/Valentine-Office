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
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DiscordAuthenticator extends AbstractAuthenticator
{
    private const ROLE_MAPPING = [
    '123456789012345678' => 'ROLE_STAFF',
    '1352280665294372935' => 'ROLE_MAIRIE',
    '1349820788743737354' => 'ROLE_SHERIFF_COMTE',
    '123456789012345678' => 'ROLE_SHERIFF_ADJOINT',
    '123456789012345678' => 'ROLE_SHERIFF_CHEF',
    '303186987370938368' => 'ROLE_SHERIFF',
    '1349821172744982598' => 'ROLE_SHERIFF_DEPUTY',
    '1349821217934151792' => 'ROLE_DEPUTY'
    ];

    public function __construct(
        private ClientRegistry $clientRegistry,
        private EntityManagerInterface $em,
        private UrlGeneratorInterface $urlGenerator,
        private JWTTokenManagerInterface $jwtManager,
        private HttpClientInterface $client,
        private string $discord_id,
        private string $discord_bot
    ) {

    }
    private function mapDiscordRolesToSymfonyRoles(array $discordRoles): array
    {
        $symfonyRoles = [];
        foreach ($discordRoles as $discordRoleId) {
            if (isset(self::ROLE_MAPPING[$discordRoleId])) {
                $symfonyRoles[] = self::ROLE_MAPPING[$discordRoleId];
            }
        }
        return $symfonyRoles;
    }
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
        
        /** @var User $user*/
        $user = $this->em->getRepository(User::class)->findOneBy(['discordId' => $discordId]);
        try {
            $response = $this->client->request('GET', "https://discord.com/api/guilds/{$this->discord_id}/members/{$discordId}", [
                'headers' => [
                    'Authorization' => "Bot {$this->discord_bot}"
                ]
            ]);

            $memberData = $response->toArray();
            $discordRoles = $memberData['roles'] ?? [];

            $symfonyRoles = $this->mapDiscordRolesToSymfonyRoles($discordRoles);
        } catch (\Exception $e) {
            throw new AuthenticationException('Impossible de récupérer les rôles Discord.');
        }
        if (!$user) {
            $user = new User();
            $user->setDiscordId($discordId);
            $user->setUsername($username);
            $user->setEmail($email);
            $user->setAvatarUrl($avatarUrl);
            $user->setRoles($symfonyRoles);
            $this->em->persist($user);
            $this->em->flush();
        }
        else {
            $desiredRoles = array_map('strval', $symfonyRoles);
            
            $currentRoles = array_map('strval', $user->getRoles());

            $diff = array_diff($currentRoles, $desiredRoles);

            if (count($diff) !== 0) {
                $user->setRoles($desiredRoles); // tableau plat de rôles
                $this->em->flush();
            }
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
