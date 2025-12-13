<?php

require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../models/collab/CollabMessage.php";

class CollabMessageController {

    private $db;

    public function __construct() {
        $this->db = config::getConnexion();
    }

    // ============================================
    // 1. ENVOYER UN MESSAGE (CREATE)
    // ============================================
    public function send(CollabMessage $msg) {
        $sql = "INSERT INTO collab_messages (collab_id, user_id, message)
                VALUES (?, ?, ?)";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            $msg->getCollabId(),
            $msg->getUserId(),
            $msg->getMessage()
        ]);
    }

    // ============================================
    // 2. RÉCUPÉRER LES MESSAGES D'UN PROJET (READ)
    // ============================================
    public function getMessages($collab_id) {
        $sql = "SELECT id, collab_id, user_id, message, audio_path, audio_duration, date_message 
                FROM collab_messages 
                WHERE collab_id = ?
                ORDER BY date_message ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$collab_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ============================================
    // 3. SUPPRIMER UN MESSAGE (DELETE)
    // ============================================
    public function delete($id) {
        $sql = "DELETE FROM collab_messages WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    // ============================================
    // 5. RÉCUPÉRER UN MESSAGE PAR ID
    // ============================================
    public function getMessageById($id) {
        $sql = "SELECT * FROM collab_messages WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ============================================
    // 4. MODIFIER UN MESSAGE (UPDATE) — optionnel
    // ============================================
    public function updateMessage($id, $newMessage) {
        $sql = "UPDATE collab_messages SET message = ? WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$newMessage, $id]);
    }
}

?>
