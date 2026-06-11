<?php
namespace Controllers;

use Core\Controller;
use Models\User;

final class AuthController extends Controller {

    public function login(): void {
        if (!empty($_SESSION['user'])) {
            $this->redirect('/index.php/dashboard');
        }

        $this->render('login', [
            'title' => 'Connexion',
            'csrf'  => $this->csrfToken(),
            'message' => $_SESSION['flash'] ?? '',
        ]);

        unset($_SESSION['flash']);
    }

    public function doLogin(): void {

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $_SESSION['flash'] = 'Champs obligatoires';
            $this->redirect('/index.php');
        }

        $user = User::findByUsername($username);

        if (!$user || !password_verify($password, $user['mdp'])) {
            $_SESSION['flash'] = 'Login ou mot de passe incorrect';
            $this->redirect('//index.php');
        }

        // SESSION
        $_SESSION['user'] = [
            'id' => $user['id'],
            'login' => $user['login'],
            'roles' => $user['roles']
        ];

        $this->redirect('/index.php/dashboard');
    }

    public function dashboard(): void {
        if (empty($_SESSION['user'])) {
            $this->redirect('/index.php');
        }

        if ($_SESSION['user']['roles'] === 'comptable') {
            $this->redirect('/index.php/dashboard/comptable');
        } else {
            $this->redirect('/index.php/dashboard/visiteur');
        }
    }

    public function comptable(): void {
        $this->requireRole('comptable');

        $this->render('dashboard/comptable', [
            'title' => 'Dashboard Comptable'
        ]);
    }

    public function visiteur(): void {
        $this->requireRole('visiteur');

        $this->render('dashboard/visiteur', [
            'title' => 'Dashboard Visiteur'
        ]);
    }

    public function logout(): void {
        session_destroy();
        $this->redirect('/index.php');
    }

    private function requireRole(string $role): void {
        if (empty($_SESSION['user'])) {
            $this->redirect('/index.php');
        }

        if ($_SESSION['user']['roles'] !== $role) {
            $_SESSION['flash'] = 'Accès refusé';
            $this->redirect('/index.php/dashboard');
        }
    }
}