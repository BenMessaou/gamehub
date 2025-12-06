<?php
require_once __DIR__ . '/../config.php';
include(__DIR__ . '/../model/User.php');

class UserController {

    // ==================== LIST ALL USERS ====================
    public function listUsers() {
        $sql = "SELECT * FROM user ORDER BY id_user DESC";
        $db = config::getConnexion();
        try {
            $list = $db->query($sql);
            return $list;
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    // ==================== GET USER BY ID ====================
    public function getUserById($id) {
        $sql = "SELECT * FROM user WHERE id_user = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([':id' => $id]);
            return $query->fetch();
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    // ==================== GET USER BY EMAIL ====================
    public function getUserByEmail($email) {
        $sql = "SELECT * FROM user WHERE email = :email";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([':email' => $email]);
            return $query->fetch();
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    // ==================== ADD NEW USER (add_user.php & signup) ====================
    public function addUser(User $user) {
        $sql = "INSERT INTO user 
                (name, lastname, email, password, cin, tel, gender, role, 
                 verified, verification_requested, totp_secret, failed_attempts, locked_until, created_at) 
                VALUES 
                (:name, :lastname, :email, :password, :cin, :tel, :gender, :role, 
                 0, 0, NULL, 0, NULL, NOW())";

        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'name'      => $user->getName(),
                'lastname'  => $user->getLastname(),
                'email'     => $user->getEmail(),
                'password'  => $user->getPassword(), // plain text (as you had)
                'cin'       => $user->getCin(),
                'tel'       => $user->getTel(),
                'gender'    => $user->getGender(),
                'role'      => $user->getRole()
            ]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    // ==================== UPDATE USER (update_user.php) ====================
    public function updateUser(User $user, $id) {
        $sql = "UPDATE user SET
                    name = :name,
                    lastname = :lastname,
                    email = :email,
                    cin = :cin,
                    tel = :tel,
                    gender = :gender,
                    role = :role
                WHERE id_user = :id";

        // Only update password if a new one is provided
        if (!empty($user->getPassword())) {
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
        }

        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $params = [
                ':id'       => $id,
                ':name'     => $user->getName(),
                ':lastname' => $user->getLastname(),
                ':email'    => $user->getEmail(),
                ':cin'      => $user->getCin(),
                ':tel'      => $user->getTel(),
                ':gender'   => $user->getGender(),
                ':role'     => $user->getRole()
            ];

            if (!empty($user->getPassword())) {
                $params[':password'] = $user->getPassword(); // plain text as before
            }

            $query->execute($params);
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    // ==================== DELETE USER ====================
    public function deleteUser($id) {
        $sql = "DELETE FROM user WHERE id_user = :id";
        $db = config::getConnexion();
        try {
            $req = $db->prepare($sql);
            $req->bindValue(':id', $id);
            $req->execute();
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    // ==================== LOGIN ATTEMPTS MANAGEMENT ====================
    public function incrementFailedAttempts($id) {
        $db = config::getConnexion();
        try {
            // Increment counter
            $db->prepare("UPDATE user SET failed_attempts = failed_attempts + 1 WHERE id_user = ?")->execute([$id]);
            // Lock for 15 minutes after 3 attempts
            $db->prepare("UPDATE user SET locked_until = DATE_ADD(NOW(), INTERVAL 15 MINUTE) 
                          WHERE id_user = ? AND failed_attempts >= 3")->execute([$id]);
        } catch (Exception $e) {
            error_log("Failed attempts error: " . $e->getMessage());
        }
    }

    public function resetFailedAttempts($id) {
        $db = config::getConnexion();
        try {
            $db->prepare("UPDATE user SET 
                          failed_attempts = 0, 
                          locked_until = NULL, 
                          last_login = NOW() 
                          WHERE id_user = ?")->execute([$id]);
        } catch (Exception $e) {
            error_log("Reset attempts error: " . $e->getMessage());
        }
    }

    // ==================== LOGIN LOG (optional but recommended) ====================
    public function logLogin($userId, $email, $success) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $sql = "INSERT INTO login_log (user_id, email, ip, success) VALUES (?, ?, ?, ?)";
        $db = config::getConnexion();
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute([$userId ?: null, $email, $ip, $success ? 1 : 0]);
        } catch (Exception $e) {
            // Silent fail – not critical
        }
    }
}
?>