<?php

require_once __DIR__ . "/../../model/collab/CollabMember.php";
require_once __DIR__ . "/../../config/config.php";

class CollabMemberController {

    private $db;

    public function __construct() {
        $this->db = config::getConnexion();
    }

    // ============================================================
    // 1. AJOUTER UN MEMBRE DANS UNE COLLABORATION
    // ============================================================
    public function add(CollabMember $member) {
        $sql = "INSERT INTO collab_members (collab_id, user_id, role)
                VALUES (?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $member->getCollabId(),
            $member->getUserId(),
            $member->getRole()
        ]);
    }

    // ============================================================
    // 2. SUPPRIMER UN MEMBRE (OWNER SEULEMENT)
    // ============================================================
    public function remove($id) {
        $sql = "DELETE FROM collab_members WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    // ============================================================
    // 3. RÉCUPÉRER TOUS LES MEMBRES D'UNE COLLABORATION
    // ============================================================
    public function getMembers($collab_id) {
        $sql = "SELECT * FROM collab_members WHERE collab_id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$collab_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ============================================================
    // 4. VÉRIFIER SI UN USER EST DÉJÀ MEMBRE
    // ============================================================
    public function isMember($collab_id, $user_id) {
        $sql = "SELECT COUNT(*) FROM collab_members 
                WHERE collab_id = ? AND user_id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$collab_id, $user_id]);

        return $stmt->fetchColumn() > 0;
    }

    // ============================================================
    // 5. NOMBRE DE MEMBRES DANS UNE COLLAB
    // ============================================================
    public function countMembers($collab_id) {
        $sql = "SELECT COUNT(*) FROM collab_members WHERE collab_id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$collab_id]);

        return $stmt->fetchColumn();
    }

    // ============================================================
    // 6. METTRE À JOUR LE RÔLE D'UN MEMBRE
    // ============================================================
    public function updateRole($member_id, $role) {
        $sql = "UPDATE collab_members SET role = ? WHERE id = ?";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$role, $member_id]);
    }
}

?>
