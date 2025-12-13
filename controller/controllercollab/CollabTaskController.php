<?php

require_once __DIR__ . "/../../model/collab/CollabTask.php";
require_once __DIR__ . "/../../config/config.php";

class CollabTaskController {

    private $db;

    public function __construct() {
        $this->db = config::getConnexion();
    }

    // Ajouter une tâche
    public function add(CollabTask $task) {
        $sql = "INSERT INTO collab_tasks (collab_id, task, done)
                VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $task->getCollabId(),
            $task->getTask(),
            $task->isDone() ? 1 : 0
        ]);
    }

    // Récupérer les tâches d'une collab
    public function getTasks($collab_id) {
        $stmt = $this->db->prepare("SELECT * FROM collab_tasks WHERE collab_id = ?");
        $stmt->execute([$collab_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Marquer une tâche comme terminée
    public function markDone($id) {
        $stmt = $this->db->prepare("UPDATE collab_tasks SET done = 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Supprimer une tâche
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM collab_tasks WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

?>
