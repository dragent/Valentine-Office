# Valentine Project ❤️

## Bienvenue dans le projet Valentine !

Ce projet a pour but de créer une plateforme de gestion des amendes, contraventions et rappels automatiques dans le cadre d'un serveur de jeu de rôle dans l'univers de **Red Dead Redemption**. Le projet est structuré en plusieurs services : le **backend Symfony**, le **frontend Next.js** et l'**infrastructure Docker**. 

On va s'assurer que ce projet soit aussi fluide que possible, en mettant en place une architecture bien pensée et en travaillant ensemble pour créer une expérience utilisateur parfaite !

## Prérequis 🛠️

Avant de commencer, voici les outils qu'il vous faut pour faire fonctionner ce projet sur votre machine :

- **Docker** : pour la conteneurisation des services.
- **Node.js** et **npm** : pour le développement du frontend avec Next.js.
- **PHP** et **Composer** : pour travailler avec Symfony sur le backend.
- **MySQL** : pour gérer notre base de données.

Si vous n'avez pas encore installé ces outils, vous pouvez les télécharger ici :
- [Docker](https://www.docker.com/get-started)
- [Node.js](https://nodejs.org/)
- [PHP](https://www.php.net/)
- [Composer](https://getcomposer.org/)
- [MySQL](https://dev.mysql.com/downloads/)

## Structure du Projet 🏗️

Voici comment les différents services du projet sont organisés :

```
/valentine-project 
├── README.md # Documentation du projet 
├── valentine-backend/ # Backend Symfony (logique métier) 
├── valentine-frontend/ # Frontend Next.js (interface utilisateur) 
└── valentine-infrastructure/ # Infrastructure Docker (docker-compose.yml)
```

### Détails des Services 🌟

- **valentine-backend** : C'est le cœur du projet ! Ce dépôt contient tout le backend Symfony qui gère la logique des amendes, des contraventions, et les utilisateurs. Il interagit avec notre base de données MySQL.
- **valentine-frontend** : Le frontend Next.js permet de donner vie à l'interface utilisateur. Il est connecté à l'API du backend pour afficher toutes les informations pertinentes.
- **valentine-infrastructure** : Ce dépôt contient la configuration Docker pour tout faire tourner en local ou en production sans tracas. Il inclut les services nécessaires comme PHP, MySQL et Nginx.

## Installation du Projet 🚀

### 1. Cloner les Dépôts

Commencez par cloner les trois dépôts dans votre répertoire local. Vous pouvez le faire en une seule commande :

```bash
git clone https://github.com/your-username/valentine-project.git
cd valentine-project
```

### 2. Démarrer l'Infrastructure Docker 🚢

Une fois le projet cloné, il est temps de démarrer l'infrastructure Docker ! Docker s'assure que tous les services (backend, frontend, base de données) sont bien configurés et fonctionnent ensemble.

Allez dans le dossier `valentine-infrastructure` et utilisez Docker Compose pour démarrer les services :

```bash
cd valentine-infrastructure
docker-compose up --build
```
Cela va construire tous les conteneurs nécessaires, comme PHP, MySQL et Nginx, et démarrer les services. Une fois que vous avez vu le message indiquant que les services sont en ligne, vous pouvez passer à l'étape suivante. 🎉

### 3. Installer les Dépendances Backend 
Pour que tout fonctionne correctement, vous devrez installer les dépendances PHP pour le backend Symfony. Allez dans le dossier valentine-backend et exécutez la commande suivante pour installer toutes les dépendances nécessaires avec Composer :
```bash
cd ../valentine-backend
composer install
```
### 4. Installer les Dépendances Frontend 🚀
Le frontend est construit avec Next.js et React. Rendez-vous dans le dossier valentine-frontend et installez les dépendances Node.js avec la commande suivante :
```bash
cd ../valentine-frontend
npm install
```
Cela va installer tout ce dont vous avez besoin pour le côté frontend. Si vous rencontrez un problème, assurez-vous d'avoir bien Node.js et npm installés sur votre machine. Si tout est ok, vous pouvez lancer l'application en mode développement !

### 5. Lancer les Services 🔥
Une fois les dépendances installées, vous êtes prêts à voir le projet en action ! Voici comment lancer les différents services.

Backend (Symfony) 🖥️
Pour démarrer le serveur Symfony en mode développement, exécutez la commande suivante dans le dossier valentine-backend :

```bash
php bin/console server:run
```

Cette commande va démarrer le serveur Symfony, et vous pourrez tester toutes les fonctionnalités backend via l'API.

Frontend (Next.js) 🌐
Ensuite, vous pouvez démarrer le frontend Next.js en mode développement avec cette commande dans le dossier valentine-frontend :

```bash
npm run dev
```

Cela va lancer le serveur de développement, et vous pourrez ouvrir l'interface dans votre navigateur à l'adresse http://localhost:3000. Vous êtes maintenant prêt à interagir avec l'application, que ce soit pour gérer les amendes ou consulter les informations !

## Développement 🌱

### Backend (Symfony)

Le backend est l'élément clé de la gestion des amendes et des contraventions. Voici quelques informations pour vous aider à naviguer dans ce côté du projet :

- **Entités** : Ajoutez vos entités pour gérer les données comme les amendes et les utilisateurs dans le dossier `valentine-backend/src/Entity`.
- **Contrôleurs** : Les contrôleurs API se trouvent dans `valentine-backend/src/Controller`. Ils sont responsables de la logique pour récupérer et manipuler les données du frontend.
- **Migrations** : Si vous avez besoin de modifier la structure de la base de données, vous pouvez créer des migrations avec :

```bash
php bin/console doctrine:migrations:migrate
```
### Frontend (Next.js)

Le frontend est là pour offrir une interface fluide et intuitive. Voici quelques points pour vous aider à démarrer :

- **Pages** : Toutes les pages de votre application Next.js sont dans le dossier `valentine-frontend/pages`. Vous pouvez ajouter de nouvelles pages pour créer plus de vues ou d'interactions.
- **Composants** : Créez des composants réutilisables pour rendre l'interface plus modulaire et maintenable. Ces composants se trouvent dans `valentine-frontend/components`.

Par exemple, pour afficher les amendes depuis l'API du backend, vous pouvez utiliser `getServerSideProps` dans une page comme suit :

```javascript
// pages/amendes.js
import fetch from 'node-fetch';

export async function getServerSideProps() {
  const res = await fetch('http://localhost:8000/api/amendes');
  const data = await res.json();
  return { props: { amendes: data } };
}

export default function Amendes({ amendes }) {
  return (
    <div>
      <h1>Amendes</h1>
      <ul>
        {amendes.map((amende) => (
          <li key={amende.id}>{amende.description}</li>
        ))}
      </ul>
    </div>
  );
}
```

### Docker 🌍

Docker facilite la gestion de tous les services nécessaires à l'application. Si vous avez besoin d'ajuster la configuration des services, tout se trouve dans le fichier `docker-compose.yml` situé dans le dossier `valentine-infrastructure`.

Si vous avez des soucis pour démarrer ou pour voir les logs des conteneurs, vous pouvez utiliser cette commande pour afficher les logs :

```bash
docker-compose logs
```
Et voilà ! Docker se charge de vous simplifier la vie.

### Tests 🧪

#### Backend (PHPUnit)

Nous utilisons **PHPUnit** pour tester les fonctionnalités du backend. Si vous voulez vérifier que tout fonctionne bien, exécutez cette commande dans le dossier `valentine-backend` :

```bash
./vendor/bin/phpunit
```
Cela exécutera tous les tests unitaires et vous permettra de vérifier que le backend est bien opérationnel.

#### Frontend (Jest)

Le frontend utilise **Jest** pour les tests unitaires. Si vous avez installé les dépendances frontend, vous pouvez tester le frontend avec cette commande :

```bash
npm run test
```
Jest s'occupera de tester les différentes parties de l'interface et de s'assurer que tout fonctionne comme prévu.

# Déploiement 🚀
Pour déployer le projet en production, vous pouvez utiliser **Docker**. Assurez-vous d'avoir tous vos services configurés avec **Docker Compose**. Pour déployer, suivez ces étapes :

1. Assurez-vous que tout fonctionne bien en local.
2. Construisez et démarrez les conteneurs avec Docker Compose :

```bash
docker-compose up --build
```

Une fois tous les services en ligne, vous pouvez déployer vos conteneurs sur un serveur distant.

Si vous avez des questions sur le déploiement ou si vous avez besoin d'aide, nous serons heureux de vous guider !

# Contribuer 🤝
Nous adorons recevoir des contributions ! Si vous voulez apporter votre touche à ce projet, voici comment procéder :

1. Forkez le projet.
2. Créez une branche pour votre fonctionnalité (`git checkout -b feature/ma-fonctionnalite`).
3. Ajoutez vos changements.
4. Faites un commit de vos modifications (`git commit -am 'Ajoute une nouvelle fonctionnalité'`).
5. Poussez sur votre branche (`git push origin feature/ma-fonctionnalite`).
6. Créez une Pull Request et nous l'examinerons ensemble.

Nous serons ravis de discuter de vos idées et de les intégrer dans le projet !

# Licence 📜
Ce projet est sous licence **MIT**. Vous pouvez consulter le fichier [LICENSE](LICENSE) pour plus de détails.

