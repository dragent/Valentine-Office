<?php
// src/Controller/ConnectController.php
namespace App\Controller\login;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Wohali\OAuth2\Client\Provider\DiscordResourceOwner;

class ConnectController extends AbstractController
{
    #[Route('/connect', name: 'connect_discord_start')]
    public function connect(ClientRegistry $clientRegistry): Response
    {
        // Redirige vers Discord
        return $clientRegistry
            ->getClient('discord')
            ->redirect([], []);
    }

    #[Route('/connect/check', name: 'connect_discord_check')]
    public function check(ClientRegistry $clientRegistry): Response
    {
        /** @var DiscordResourceOwner $user */
        $user = $clientRegistry->getClient('discord')->fetchUser();

        $data = $user->toArray();

        $discordId = $data['id'];
        $username = $data['username'];
        $discriminator = $data['discriminator'];
        $email = $data['email'] ?? null;
        $avatar = $data['avatar'] ?? null;

        $avatarUrl = $avatar
            ? "https://cdn.discordapp.com/avatars/{$discordId}/{$avatar}.png"
            : "https://cdn.discordapp.com/embed/avatars/" . (intval($discriminator) % 5) . ".png";

        dd([
            'id' => $discordId,
            'username' => $username,
            'discriminator' => $discriminator,
            'email' => $email,
            'avatar_url' => $avatarUrl,
        ]);
    }
}
