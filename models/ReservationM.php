<?php
// models/ReservationM.php

class Reservation {

    public $event_id;
    public $fullName;
    public $email;
    public $phone;
    public $seats;

    public function insert() {
        require_once __DIR__ . '/../config.php';

        $conn = config::getConnexion();

        $sql = "INSERT INTO reservation (event_id, fullName, email, phone, seats)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $this->event_id,
            $this->fullName,
            $this->email,
            $this->phone,
            $this->seats
        ]);
    }

    public static function getAll() {
        require_once __DIR__ . '/../config.php';

        $conn = config::getConnexion();

        $sql = "SELECT * FROM reservation ORDER BY reservationDate DESC";
        return $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
