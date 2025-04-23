# Valentine Infrastructure Docker Setup

Ce répertoire contient les fichiers nécessaires pour configurer l'infrastructure de conteneurs Docker pour le projet Valentine. Il inclut la configuration de Docker Compose pour déployer le backend Symfony, le frontend Next.js, et la base de données MySQL.

## Structure des fichiers

Voici un aperçu des fichiers principaux dans ce dossier :

```
/valentine-infrastructure 
├── README.md # Documentation de l'infrastructure Docker 
├── docker-compose.yml # Fichier de configuration pour Docker Compose 
├── Dockerfile.backend # Dockerfile pour le backend Symfony 
├── Dockerfile.frontend # Dockerfile pour le frontend Next.js 
├── nginx.conf # Configuration Nginx pour le reverse proxy 
└── .env # Variables d'environnement pour Docker
```

## Prérequis

Avant de commencer, assurez-vous d'avoir les outils suivants installés sur votre machine :

- [Docker](https://www.docker.com/get-started) (avec Docker Compose)
- [Git](https://git-scm.com/)
- [Node.js](https://nodejs.org/) (pour le frontend Next.js)
- [PHP](https://www.php.net/) (pour le backend Symfony)

## Installation et Démarrage

### 1. Clonez le projet

Commencez par cloner le projet depuis le dépôt Git :

```bash
git clone https://votre-lien-de-repository.git
cd valentine-project/valentine-infrastructure
```

### 2. Créez un fichier .env
Vous devrez définir les variables d'environnement pour que l'application fonctionne correctement. Copiez le fichier .env.sample en .env et personnalisez-le selon vos besoins. Exemple :
```bash
cp .env.sample .env
```
Modifiez les paramètres de connexion à la base de données et toute autre variable d'environnement requise.

### 3. Lancer les services avec Docker Compose
Assurez-vous d'être dans le dossier valentine-infrastructure, puis exécutez la commande suivante pour construire et démarrer les conteneurs :
```bash
docker-compose up --build
```

Cette commande va :

Construire les images Docker pour le backend Symfony et le frontend Next.js.

Lancer les services (backend, frontend, base de données et Nginx).

### 4. Accéder aux services
Une fois les conteneurs démarrés, vous pouvez accéder à votre application à l'adresse suivante :

Frontend : http://localhost:3000

Backend : http://localhost:8000

### 5. Arrêter les services
Pour arrêter les services en cours d'exécution, vous pouvez utiliser la commande suivante :
```bash
docker-compose down
```
Cela arrêtera et supprimera les conteneurs, mais gardera les volumes de données (comme la base de données) intacts. Si vous souhaitez tout nettoyer, utilisez :
```bash
docker-compose down --volumes
``` 

## Contribution
Si vous souhaitez contribuer à ce projet, suivez les étapes ci-dessous :

1. Forkez le projet.

2. Créez une branche pour votre fonctionnalité :
```bash
git checkout -b feature/ma-fonctionnalite
```
3. Faites vos changements.

4. Commitez vos modifications :
```bash
git commit -m "Ajout de ma fonctionnalité"
``` 

5. Poussez vos changements vers votre fork :
```bash
git push origin feature/ma-fonctionnalite
``` 

6. Créez une Pull Request sur le dépôt principal.

## Aide et Support
Si vous avez des questions ou des problèmes, n'hésitez pas à ouvrir une issue sur GitHub ou à demander de l'aide dans les forums ou canaux dédiés.