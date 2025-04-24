# Valentine Project ❤️

Bienvenue dans le projet **Valentine** !

Ce projet a pour but de créer un **cahier de gestion de bureau** pour un **roleplay shérif** dans l’univers de Red Dead Redemption. Il comprend la **gestion des contraventions, des amendes, des rappels automatiques, des saisies, des groupes et d’un coffre**, avec une interface web moderne et une infrastructure solide.

## 🧱 Stack Technique

Le projet utilise des technologies modernes pour garantir performance, scalabilité et maintenabilité :

- **Symfony** (Backend/API) : gestion des données et endpoints REST.
- **Nuxt.js** (Frontend SSR) : rendu côté serveur pour une interface rapide et fluide.
- **Docker** : environnement de développement reproductible.
- **Maquettes Adobe XD** : design de l’interface utilisateur.

---

## 🔧 Prérequis

Avant de commencer, assurez-vous d’avoir installé :

- [Docker](https://www.docker.com/get-started) 🐳
- [Node.js](https://nodejs.org/) + npm 🟢
- [PHP](https://www.php.net/) + [Composer](https://getcomposer.org/) 🐘
- [MySQL](https://dev.mysql.com/downloads/) 🐬

---

## 🗂️ Structure du projet

```
/valentine-project 
├── README.md                       # Documentation du projet
├── LICENSE                         # Licence MIT du projet
├── valentine-backend/             # Backend Symfony (API)
├── valentine-frontend/            # Frontend Nuxt.js
├── valentine-infrastructure/      # Infrastructure Docker
└── maquettes/                     # Dossier contenant les maquettes UI (Adobe XD)
```

📁 Les maquettes sont visibles ici : [https://lnkd.in/dG8rn9rd](https://lnkd.in/dG8rn9rd) 📌 Suivi projet via Trello : [https://lnkd.in/d2CSQq8z](https://lnkd.in/d2CSQq8z)

📌 **Gestion des branches Git :**
- `main` : version stable du projet
- `back` : développement du backend Symfony
- `front` : développement du frontend Nuxt.js

---

## 🚀 Installation & Lancement

### 1. Cloner le projet

```bash
git clone https://github.com/dragent/Valentine-Office.git
cd valentine-project
```

### 2. Lancer Docker

```bash
cd valentine-infrastructure
docker-compose up --build
```

### 3. Backend Symfony

```bash
cd ../valentine-backend
composer install
php bin/console doctrine:migrations:migrate
php bin/console server:run
```

### 4. Frontend Nuxt.js

```bash
cd ../valentine-frontend
npm install
npm run dev
```

---

## 🔍 Développement

### Backend Symfony

- Les entités se trouvent dans `src/Entity`
- Les contrôleurs dans `src/Controller`
- Utilisation de Doctrine et migration des schémas avec `php bin/console doctrine:migrations:migrate`

Entités prévues :

- `User` (shérifs et administrateurs)
- `Groupe` (groupes de travail ou d’intervention)
- `Amende` (contraventions infligées)
- `Saisie` (biens ou personnes saisies)
- `Coffre` (éléments saisis ou en dépôt)

### Frontend Nuxt.js

- Pages dans `pages/`
- Composants dans `components/`
- Exemple de récupération via API :

```js
export async function getServerSideProps() {
  const res = await fetch('http://localhost:8000/api/amendes');
  const data = await res.json();
  return { props: { amendes: data } };
}
```

---

## 🧪 Tests

### Backend : PHPUnit

```bash
./vendor/bin/phpunit
```

### Frontend : Jest

```bash
npm run test
```

---

## 🌍 Déploiement

Le projet peut être facilement déployé via Docker Compose.

```bash
docker-compose up --build
```

---

## 🤝 Contribution

1. Forkez le projet
2. Créez une branche : `git checkout -b feature/ma-fonctionnalite`
3. Commit : `git commit -am 'Ajoute une nouvelle fonctionnalité'`
4. Push : `git push origin feature/ma-fonctionnalite`
5. Ouvrez une Pull Request 🚀

Toutes les contributions doivent respecter les termes de la licence MIT. Le fichier `LICENSE` est disponible à la racine du projet.

---

## 📜 Licence

MIT. Voir le fichier `LICENSE`.

