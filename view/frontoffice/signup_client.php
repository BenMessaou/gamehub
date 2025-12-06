<?php
// ... (top part same)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = $_POST['name'] ?? '';
    $lastname = $_POST['lastname'] ?? '';
    $email    = $_POST['email'] ?? '';
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);  // HASHED
    $cin      = $_POST['cin'] ?? '';
    $tel      = $_POST['tel'] ?? '';
    $gender   = $_POST['gender'] ?? '';
    $role     = 'client';

    $user = new User(null, $name, $lastname, $email, $password, $cin, $tel, $gender, $role);

    try {
        $userController->addUser($user);
        $insertedUser = $userController->getUserByEmail($email);
        $_SESSION['user_id'] = $insertedUser['id_user'];
        header('Location: profile.php');
        exit;
    } catch (Exception $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}
?>