<?php
// app/controllers/AuthController.php
require_once __DIR__ . "/../models/User.php";

class AuthController {

    public function showLogin() {
        // affiche la vue login
        require __DIR__ . "/../views/admin/login.php";
    }

    public function login() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $_SESSION['error'] = "Tous les champs sont requis.";
            header("Location: /feedback-games/admin/index.php?action=login");
            exit;
        }

        $um = new UserModel();
        $user = $um->findByUsername($username);

        if (!$user) {
            $_SESSION['error'] = "Identifiants incorrects.";
            header("Location: /feedback-games/admin/index.php?action=login");
            exit;
        }

        $hash = $user['password'];

        // Accept both PHP password_hash() hashes and old MD5 hashes:
        $ok = false;
        if (password_verify($password, $hash)) {
            $ok = true;
        } elseif (md5($password) === $hash) {
            // legacy MD5 check (you used MD5 in phpMyAdmin)
            $ok = true;
        }

        if ($ok) {
            // login success
            $_SESSION['user'] = ['id'=>$user['id'], 'username'=>$user['username']];
            header("Location: /feedback-games/admin/index.php");
            exit;
        } else {
            $_SESSION['error'] = "Identifiants incorrects.";
            header("Location: /feedback-games/admin/index.php?action=login");
            exit;
        }
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        unset($_SESSION['user']);
        session_destroy();
        header("Location: /feedback-games/admin/index.php?action=login");
        exit;
    }
}
