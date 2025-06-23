# Valentine Office - Backend Symfony

Ce projet est le backend d'une application web pour la gestion de bureaux de shérifs, développé en Symfony 7, avec une authentification via Discord (OAuth2).

## 🔧 Prérequis

- Docker & Docker Compose
- Symfony CLI
- Un compte Discord
- Une application Discord configurée sur https://discord.com/developers

---

## 🚀 Lancer le projet

```bash
docker-compose up --build
```
L'API Symfony sera accessible sur :👉 http://localhost:8080

## ⚙️ Configuration de l'authentification Discord
L'application utilise KnpUOAuth2ClientBundle pour l'auth via Discord.

## 🧪 Variables d'environnement
Dans .env ou .env.local, configure :

```ini
DISCORD_CLIENT_ID=TON_CLIENT_ID
DISCORD_CLIENT_SECRET=TON_SECRET
DISCORD_REDIRECT_URI=http://localhost:8080/connect/discord/check
DATABASE_URL="mysql://root:root@database:3306/valentine_db"
```

## 🔑 Créer ton app Discord
1. Va sur : https://discord.com/developers/applications

2. Crée une application, puis un OAuth2

3. Dans "OAuth2 > Redirects", ajoute :
```bash
http://localhost:8080/connect/discord/check
```
4. Active la scope identify et email (optionnel)

5. Copie l’ID et le secret dans .env.local

--- 

## 🔐 Routes importantes
- /connect/discord : redirige l’utilisateur vers Discord

- /connect/discord/check : point de retour après l’authentification

- Accès aux routes restreintes : ROLE_USER requis

---

## 🧠 Ce que fait l'authentification
- Récupère l'utilisateur depuis Discord

- S'il n'existe pas : le crée avec :

  - Son discordId

  - Son pseudo et email

  - Son avatar

- Connecte automatiquement l'utilisateur

- Tu peux accéder à l’utilisateur via app.user dans les contrôleurs ou les templates

---

## 🧑 Exemple d'affichage utilisateur (Twig)
```twig
<img src="{{ app.user.avatarUrl }}" alt="Avatar" />
<p>{{ app.user.username }}</p>
```
---

## 🗃️ Commandes utiles
```bash
symfony console doctrine:migrations:diff
symfony console doctrine:migrations:migrate
symfony console make:entity
symfony console make:migration
```
---


## 📁 Structure technique
- src/Security/DiscordAuthenticator.php : classe d'authentification

- config/packages/security.yaml : configuration de sécurité

- User : entité liée à Discord

---

## 🧪 Exemple de test du login
1. Lancer le backend

2. Accéder à http://localhost:8080/connect/discord

3. Autoriser l’application

4. Vérifier qu’un utilisateur est bien créé en base

---

## 🙌 Crédits
- Symfony 7

- KnpUOAuth2ClientBundle

- Discord OAuth2 API

---

## 📜 Licence
Projet open-source, libre d’utilisation et de modification.

