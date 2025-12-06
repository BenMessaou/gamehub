<?php
require_once __DIR__ . '/../config.php';
include(__DIR__ . '/../model/User.php');

class UserController {

    public function listUsers() {
        $sql = "SELECT id_user, name, lastname, email, password, cin, tel, gender, role, 
                       verified, verification_requested, totp_secret, failed_attempts, 
                       locked_until, created_at, last_login, passkey_credential 
                FROM user ORDER BY id_user DESC";
        $db = config::getConnexion();
        try {
            $list = $db->query($sql);
            return $list;
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function deleteUser($id) {
        $sql = "DELETE FROM user WHERE id_user = :id";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':id', $id);

        try {
            $req->execute();
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

   public function addUser(User $user) {
    $sql = "INSERT INTO user 
            (name, lastname, email, password, cin, tel, gender, role, verified, verification_requested, totp_secret, failed_attempts, locked_until, created_at, passkey_credential) 
            VALUES 
            (:name, :lastname, :email, :password, :cin, :tel, :gender, :role, 0, 0, NULL, 0, NULL, NOW(), NULL)";

    $db = config::getConnexion();

    try {
        $query = $db->prepare($sql);
        $query->execute([
            'name'      => $user->getName(),
            'lastname'  => $user->getLastname(),
            'email'     => $user->getEmail(),
            'password'  => $user->getPassword(),
            'cin'       => $user->getCin(),
            'tel'       => $user->getTel(),
            'gender'    => $user->getGender(),
            'role'      => $user->getRole()
        ]);
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();  
    }
}

    public function updateUser(User $user, $id) {
        $sql = "UPDATE user SET
                    name = :name,
                    lastname = :lastname,
                    email = :email,
                    password = :password,
                    cin = :cin,
                    tel = :tel,
                    gender = :gender,
                    role = :role
                WHERE id_user = :id";

        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id'        => $id,
                'name'      => $user->getName(),
                'lastname'  => $user->getLastname(),
                'email'     => $user->getEmail(),
                'password'  => $user->getPassword(),
                'cin'       => $user->getCin(),
                'tel'       => $user->getTel(),
                'gender'    => $user->getGender(),
                'role'      => $user->getRole()
            ]);
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function showUser($id) {
        $sql = "SELECT * FROM user WHERE id_user = $id";
        $db = config::getConnexion();
        $query = $db->prepare($sql);

        try {
            $query->execute();
            $user = $query->fetch();
            return $user;
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    public function getUserById($id) {
        $sql = "SELECT * FROM user WHERE id_user = :id";
        $db = config::getConnexion();
        $query = $db->prepare($sql);

        try {
            $query->execute([":id" => $id]);
            $user = $query->fetch();
            return $user;
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    public function getUserByEmail($email) {
        $sql = "SELECT * FROM user WHERE email = :email";
        $db = config::getConnexion();
        $query = $db->prepare($sql);

        try {
            $query->execute([":email" => $email]);
            $user = $query->fetch();
            return $user;
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    // NEW: Login attempts (you already have these)
    public function incrementFailedAttempts($id) {
        $db = config::getConnexion();
        $db->prepare("UPDATE user SET failed_attempts = failed_attempts + 1 WHERE id_user = ?")->execute([$id]);
        $db->prepare("UPDATE user SET locked_until = DATE_ADD(NOW(), INTERVAL 15 MINUTE) WHERE id_user = ? AND failed_attempts >= 3")->execute([$id]);
    }

    public function resetFailedAttempts($id) {
        $db = config::getConnexion();
        $db->prepare("UPDATE user SET failed_attempts = 0, locked_until = NULL, last_login = NOW() WHERE id_user = ?")->execute([$id]);
    }

    // Optional: log login
    public function logLogin($userId, $email, $success) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $sql = "INSERT INTO login_log (user_id, email, ip, success) VALUES (?, ?, ?, ?)";
        $db = config::getConnexion();
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute([$userId ?: null, $email, $ip, $success ? 1 : 0]);
        } catch (Exception $e) {
            // silent
        }
    }
}
?>