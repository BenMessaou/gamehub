<?php
include_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/Event.php';

class EventC
{
    // -------------------------------
    // Ajouter un event
    // -------------------------------
    function ajouterEvent($event)
    {
        $sql = "INSERT INTO events
                (user_id, title, description, eventType, platform, location, startDate, endDate, ticketPrice, availability, prizePool, imageURL, status)
                VALUES
                (:user_id, :title, :description, :eventType, :platform, :location, :startDate, :endDate, :ticketPrice, :availability, :prizePool, :imageURL, :status)";

        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'user_id'      => $event->getUserId(),
                'title'        => $event->getTitle(),
                'description'  => $event->getDescription(),
                'eventType'    => $event->getEventType(),
                'platform'     => $event->getPlatform(),
                'location'     => $event->getLocation(),
                'startDate'    => $event->getStartDate(),
                'endDate'      => $event->getEndDate(),
                'ticketPrice'  => $event->getTicketPrice(),
                'availability' => $event->getAvailability(),
                'prizePool'    => $event->getPrizePool(),
                'imageURL'     => $event->getImageURL(),
                'status'       => $event->getStatus()
            ]);
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }

    // -------------------------------
    // Afficher tous les events
    // -------------------------------
    function afficherEvents()
    {
        $sql = "SELECT * FROM events";
        $db = config::getConnexion();
        try {
            $list = $db->query($sql);
            return $list;
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    // -------------------------------
    // Supprimer un event
    // -------------------------------
    function supprimerEvent($id)
    {
        $sql = "DELETE FROM events WHERE id = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            return $query->rowCount() > 0;
        } catch (Exception $e) {
            error_log('Erreur suppression: ' . $e->getMessage());
            return false;
        }
    }

    // -------------------------------
    // Récupérer un event par ID
    // -------------------------------
    function recupererEvent($id)
    {
        $sql = "SELECT * FROM events WHERE id = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            return $query->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }

    // -------------------------------
    // Modifier un event
    // -------------------------------
    function modifierEvent($event, $id)
    {
        $sql = "UPDATE events SET
                user_id = :user_id,
                title = :title,
                description = :description,
                eventType = :eventType,
                platform = :platform,
                location = :location,
                startDate = :startDate,
                endDate = :endDate,
                ticketPrice = :ticketPrice,
                availability = :availability,
                prizePool = :prizePool,
                imageURL = :imageURL,
                status = :status
                WHERE id = :id";

        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'user_id'      => $event->getUserId(),
                'title'        => $event->getTitle(),
                'description'  => $event->getDescription(),
                'eventType'    => $event->getEventType(),
                'platform'     => $event->getPlatform(),
                'location'     => $event->getLocation(),
                'startDate'    => $event->getStartDate(),
                'endDate'      => $event->getEndDate(),
                'ticketPrice'  => $event->getTicketPrice(),
                'availability' => $event->getAvailability(),
                'prizePool'    => $event->getPrizePool(),
                'imageURL'     => $event->getImageURL(),
                'status'       => $event->getStatus(),
                'id'           => $id
            ]);
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }

    // -------------------------------
    // Admin: mettre à jour statut event
    // -------------------------------
    function mettreAJourStatutEvent($id, $status)
    {
        $sql = "UPDATE events SET status = :status WHERE id = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id' => $id,
                'status' => $status
            ]);
            return $query->rowCount() > 0;
        } catch (Exception $e) {
            error_log('Erreur mise à jour statut: ' . $e->getMessage());
            return false;
        }
    }

    // -------------------------------
    // Afficher events acceptés
    // -------------------------------
    function afficherEventsAcceptes()
    {
        $sql = "SELECT * FROM events 
                WHERE status = 'accepted' 
                ORDER BY startDate DESC";

        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute();
            return $query->fetchAll();
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
            return [];
        }
    }



    // Add registration
public function inscrireEvent($eventId, $userId = null) {
    $sql = "INSERT INTO event_registrations (event_id, user_id) VALUES (:event_id, :user_id)";
    $db = config::getConnexion();
    $query = $db->prepare($sql);
    $query->execute([
        "event_id" => $eventId,
        "user_id" => $userId
    ]);
}

// Decrease availability if limited
public function decrementAvailability($eventId) {
    $sql = "UPDATE events 
            SET availability = availability - 1
            WHERE id = :id AND availability IS NOT NULL AND availability > 0";
    $db = config::getConnexion();
    $query = $db->prepare($sql);
    $query->execute(["id" => $eventId]);
}

public function afficherEventsParStatut($status) {
    $sql = "SELECT * FROM events WHERE status = :status ORDER BY startDate ASC";
    $db = config::getConnexion();
    $query = $db->prepare($sql);
    $query->execute(["status" => $status]);
    return $query->fetchAll();
}


}
?>