<?php

require_once __DIR__ . "/../../models/collab/CollabProject.php";
require_once __DIR__ . "/../../config/config.php";


class CollabProjectController {

    private $db;

    public function __construct() {
        $this->db = config::getConnexion();
    }

    // ============================================================
    // 1. CRÉER UN PROJET COLLABORATIF (OWNER)
    // ============================================================
    public function create(CollabProject $collab) {
        $sql = "INSERT INTO collab_project 
                (owner_id, titre, description, date_creation, statut, max_membres, image)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);

        $success = $stmt->execute([
            $collab->getOwnerId(),
            $collab->getTitre(),
            $collab->getDescription(),
            $collab->getDateCreation(),
            $collab->getStatut(),
            $collab->getMaxMembres(),
            $collab->getImage()
        ]);

        if ($success) {
            // Récupérer l'ID du projet qui vient d'être créé
            $newId = $this->db->lastInsertId();

            // Ajouter automatiquement le créateur comme membre OWNER
            $sqlMember = "INSERT INTO collab_members (collab_id, user_id, role)
                          VALUES (?, ?, 'owner')";
            $stmt2 = $this->db->prepare($sqlMember);
            $stmt2->execute([$newId, $collab->getOwnerId()]);

            return $newId;
        }

        return false;
    }

    // ============================================================
    // 2. METTRE À JOUR UN PROJET COLLABORATIF
    // ============================================================
    public function update(CollabProject $collab) {
        $sql = "UPDATE collab_project 
                SET titre = ?, description = ?, statut = ?, max_membres = ?, image = ?
                WHERE id = ? AND owner_id = ?"; // owner_id permet la sécurité

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            $collab->getTitre(),
            $collab->getDescription(),
            $collab->getStatut(),
            $collab->getMaxMembres(),
            $collab->getImage(),
            $collab->getId(),
            $collab->getOwnerId()
        ]);
    }

    // ============================================================
    // 3. SUPPRIMER UN PROJET COLLABORATIF
    // ============================================================
    public function delete($id, $ownerId = null) {
        // Si ownerId est null, suppression en mode développeur (sans vérification)
        if ($ownerId === null) {
            // Supprimer d'abord les membres associés
            $sqlMembers = "DELETE FROM collab_members WHERE collab_id = ?";
            $stmtMembers = $this->db->prepare($sqlMembers);
            $stmtMembers->execute([$id]);
            
            // Supprimer le projet
            $sql = "DELETE FROM collab_project WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id]);
        }
        
        // Mode normal : vérifier que l'utilisateur est le propriétaire
        $sql = "DELETE FROM collab_project WHERE id = ? AND owner_id = ?";
        $stmt = $this->db->prepare($sql);
        
        if ($stmt->execute([$id, $ownerId])) {
            // Supprimer aussi les membres associés
            $sqlMembers = "DELETE FROM collab_members WHERE collab_id = ?";
            $stmtMembers = $this->db->prepare($sqlMembers);
            $stmtMembers->execute([$id]);
            return true;
        }
        
        return false;
    }

    // ============================================================
    // 4. RÉCUPÉRER UN PROJET PAR ID (POUR L'OWNER)
    // ============================================================
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM collab_project WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ============================================================
    // 5. RÉCUPÉRER TOUS LES PROJETS D'UN OWNER
    // ============================================================
    public function getByOwner($ownerId) {
        $stmt = $this->db->prepare("SELECT * FROM collab_project WHERE owner_id = ?");
        $stmt->execute([$ownerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ============================================================
    // 6. RÉCUPÉRER TOUS LES PROJETS OUVERTS (pour les autres users)
    // ============================================================
    public function getAllOpen() {
        $stmt = $this->db->query("SELECT * FROM collab_project WHERE statut = 'ouvert' ORDER BY date_creation DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ============================================================
    // 7. METTRE À JOUR UNIQUEMENT LE STATUT D'UN PROJET
    // ============================================================
    public function updateStatus($id, $statut, $ownerId = null) {
        // Mode développeur : pas de vérification owner
        if ($ownerId === null) {
            $sql = "UPDATE collab_project SET statut = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$statut, $id]);
        }
        
        // Mode normal : vérifier que l'utilisateur est le propriétaire
        $sql = "UPDATE collab_project SET statut = ? WHERE id = ? AND owner_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$statut, $id, $ownerId]);
    }
}

?>
