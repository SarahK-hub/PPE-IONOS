# PPE – Déploiement IONOS

## Installation rapide

1. Clone ou upload le projet sur IONOS.
2. Renomme `.env.example` en `.env` et renseigne tes identifiants de base de données IONOS.
3. Configure le domaine IONOS pour pointer sur le dossier `public/`.
4. L'application est accessible à la racine de ton domaine.

## Compte administrateur par défaut

- Login : `alice.m`
- Mot de passe : `hash_pwd_1`

## Déploiement automatique via GitHub Actions

Configure les secrets GitHub suivants dans ton repo :

| Secret | Description |
|---|---|
| `DB_HOST` | Hôte MySQL IONOS |
| `DB_PORT` | Port (3306 par défaut) |
| `DB_NAME` | Nom de la base de données |
| `DB_USER` | Utilisateur MySQL |
| `DB_PASSWORD` | Mot de passe MySQL |
| `SFTP_HOST` | Adresse SFTP IONOS |
| `SFTP_USER` | Utilisateur SFTP |
| `SFTP_PASSWORD` | Mot de passe SFTP |

## Différences avec la version locale

- `Database.php` : lit le fichier `.env` (et non les variables d'environnement système)
- `Router.php` : supporte les routes regex pour les URLs dynamiques
- `Controller.php` : redirections en chemin absolu (pas de sous-dossier à préfixer)
- `public/index.php` : normalisation du path compatible IONOS (`/index.php/route`)
- `Views/layout.php` : liens absolus sans `BASE_URL`
- Dossier `ppe_logs/` : logs de diagnostic pour déboguer sur IONOS
