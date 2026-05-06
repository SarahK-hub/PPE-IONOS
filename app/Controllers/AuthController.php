<?php
declare(strict_types=1);

namespace Controllers;

use Core\Controller;
use Models\User;

final class AuthController extends Controller
{
    private function log(string $file, string $msg): void
    {
        @file_put_contents(__DIR__ . '/../../ppe_logs/' . $file, '[' . date('c') . '] ' . $msg . "\n", FILE_APPEND);
    }

    public function login(): void
    {
        $this->log('auth.log', 'login() uid=' . ($_SESSION['uid'] ?? 'NULL'));

        if (!empty($_SESSION['uid'])) {
            $this->redirect('/dashboard');
        }

        $message = $_SESSION['flash'] ?? '';
        unset($_SESSION['flash']);

        $this->render('login', [
            'title'   => 'Connexion',
            'csrf'    => $this->csrfToken(),
            'message' => $message,
        ]);
    }

    public function doLogin(): void
    {
        $this->log('auth.log', 'doLogin() START');

        try {
            if (!$this->checkCsrf($_POST['csrf'] ?? null)) {
                $this->log('auth.log', 'doLogin() CSRF FAIL');
                http_response_code(400);
                echo 'CSRF invalide';
                return;
            }

            $username = trim((string)($_POST['username'] ?? ''));
            $password = (string)($_POST['password'] ?? '');

            $this->log('auth.log', 'doLogin() username=' . $username);

            if ($username === '' || $password === '') {
                $_SESSION['flash'] = 'Identifiants requis';
                $this->redirect('/');
            }

            $user = User::findByUsername($username);
            $this->log('auth.log', 'doLogin() findByUsername returned=' . (is_array($user) ? 'array' : 'null'));

            if (!$user || empty($user['mdp']) || !password_verify($password, (string)$user['mdp'])) {
                $_SESSION['flash'] = 'Mauvais identifiant ou mot de passe';
                $this->log('auth.log', 'doLogin() BAD CREDS');
                $this->redirect('/');
            }

            session_regenerate_id(true);
            $_SESSION['uid']  = (int)$user['id'];
            $_SESSION['name'] = (string)($user['login'] ?? $username);
            // Compatibilité avec les controllers qui utilisent $_SESSION['user']
            $_SESSION['user'] = [
                'id'    => (int)$user['id'],
                'login' => (string)($user['login'] ?? $username),
                'roles' => (string)($user['roles'] ?? 'visiteur'),
            ];

            $this->log('auth.log', 'doLogin() OK uid=' . $_SESSION['uid']);
            $this->redirect('/dashboard');

        } catch (\Throwable $e) {
            $this->log(
                'php-auth-exception.log',
                get_class($e) . ': ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine() . "\n" . $e->getTraceAsString()
            );
            http_response_code(500);
            echo 'Erreur interne (auth).';
        }
    }

    public function dashboard(): void
    {
        $this->log('dashboard.log', 'dashboard() uid=' . ($_SESSION['uid'] ?? 'NULL'));

        if (empty($_SESSION['uid'])) {
            $this->redirect('/');
        }

        $role = $_SESSION['user']['roles'] ?? 'visiteur';

        if ($role === 'comptable') {
            $this->redirect('/dashboard/comptable');
        } else {
            $this->redirect('/dashboard/visiteur');
        }
    }

    public function comptable(): void
    {
        if (empty($_SESSION['uid'])) $this->redirect('/');

        if (($_SESSION['user']['roles'] ?? '') !== 'comptable') {
            $_SESSION['flash'] = 'Accès refusé';
            $this->redirect('/dashboard');
        }

        $this->render('dashboard/comptable', [
            'title'    => 'Dashboard Comptable',
            'username' => $_SESSION['name'] ?? 'Utilisateur',
        ]);
    }

    public function visiteur(): void
    {
        if (empty($_SESSION['uid'])) $this->redirect('/');

        if (($_SESSION['user']['roles'] ?? '') !== 'visiteur') {
            $_SESSION['flash'] = 'Accès refusé';
            $this->redirect('/dashboard');
        }

        $this->render('dashboard/visiteur', [
            'title'    => 'Dashboard Visiteur',
            'username' => $_SESSION['name'] ?? 'Utilisateur',
        ]);
    }

    public function logout(): void
    {
        $this->log('auth.log', 'logout() uid=' . ($_SESSION['uid'] ?? 'NULL'));

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $p['path'] ?? '/',
                $p['domain'] ?? '',
                (bool)($p['secure'] ?? false),
                (bool)($p['httponly'] ?? true)
            );
        }

        session_destroy();
        $this->redirect('/');
    }
}
