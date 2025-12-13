<?php
include(__DIR__ . '/../config/config.php');
include(__DIR__ . '/../models/Event.php');

class EventController {

    // ======================
    // LISTER TOUS LES EVENTS
    // ======================
    public function listEvents() {

        $sql = "SELECT * FROM events ORDER BY start_date ASC";
        $db = config::getConnexion();

        try {
            $list = $db->query($sql);
            return $list;
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    // ======================
    // LISTER EVENTS PAR STATUS
    // ======================
    public function listEventsByStatus($status = null) {

        if ($status) {
            $sql = "SELECT * FROM events WHERE status = :status ORDER BY start_date ASC";
            $db = config::getConnexion();

            try {
                $query = $db->prepare($sql);
                $query->execute(['status' => $status]);
                return $query;
            } catch (Exception $e) {
                die('Error:' . $e->getMessage());
            }
        } else {
            return $this->listEvents();
        }
    }

    // ======================
    // SUPPRIMER EVENT
    // ======================
    public function deleteEvent($id) {

        $sql = "DELETE FROM events WHERE id = :id";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':id', $id);

        try {
            $req->execute();
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    // ======================
    // AJOUTER EVENT
    // ======================
    public function addEvent(Event $event) {

        $sql = "INSERT INTO events 
        (title, description, start_date, end_date, location, is_online, capacity, reserved_count, banner, status)
        VALUES 
        (:title, :description, :start_date, :end_date, :location, :is_online, :capacity, :reserved_count, :banner, :status)";

        $db = config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $query->execute([
                'title'         => $event->getTitle(),
                'description'   => $event->getDescription(),
                'start_date'    => $event->getStartDate(),
                'end_date'      => $event->getEndDate(),
                'location'      => $event->getLocation(),
                'is_online'     => $event->isOnline(),
                'capacity'      => $event->getCapacity(),
                'reserved_count'=> $event->getReservedCount(),
                'banner'        => $event->getBanner(),
                'status'        => $event->getStatus()
            ]);

        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    // ======================
    // MODIFIER EVENT
    // ======================
    public function updateEvent(Event $event, $id) {

        $sql = "UPDATE events SET
            title = :title,
            description = :description,
            start_date = :start_date,
            end_date = :end_date,
            location = :location,
            is_online = :is_online,
            capacity = :capacity,
            reserved_count = :reserved_count,
            banner = :banner,
            status = :status
        WHERE id = :id";

        $db = config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id'            => $id,
                'title'         => $event->getTitle(),
                'description'   => $event->getDescription(),
                'start_date'    => $event->getStartDate(),
                'end_date'      => $event->getEndDate(),
                'location'      => $event->getLocation(),
                'is_online'     => $event->isOnline(),
                'capacity'      => $event->getCapacity(),
                'reserved_count'=> $event->getReservedCount(),
                'banner'        => $event->getBanner(),
                'status'        => $event->getStatus()
            ]);

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    // ======================
    // AFFICHER UN EVENT PAR ID
    // ======================
    public function showEvent($id) {

        $sql = "SELECT * FROM events WHERE id = :id";
        $db = config::getConnexion();
        $query = $db->prepare($sql);

        try {
            $query->execute(['id' => $id]);
            $event = $query->fetch();
            return $event;

        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

}

?>
