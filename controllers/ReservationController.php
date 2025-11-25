<?php
// controllers/ReservationController.php

class ReservationController {

    public function form() {
        require __DIR__ . '/../views/front/reservationForm.php';
    }

    public function add() {
        require_once __DIR__ . '/../models/ReservationM.php';
        require_once __DIR__ . '/EventController.php';

        $reservation = new Reservation();
        $reservation->event_id = $_POST['event_id'] ?? null;
        $reservation->fullName = $_POST['fullName'] ?? '';
        $reservation->email = $_POST['email'] ?? '';
        $reservation->phone = $_POST['phone'] ?? '';
        $reservation->seats = intval($_POST['seats'] ?? 1);

        $eventC = new EventC();
        $event = $eventC->recupererEvent($reservation->event_id);

        if (!$event) {
            die("Event not found.");
        }

        // Check availability if limited
        if ($event["availability"] !== null && intval($event["availability"]) < $reservation->seats) {
            die("Not enough places left for this event.");
        }

        // Insert reservation
        $reservation->insert();

        // Decrement availability if limited
        if ($event["availability"] !== null) {
            $eventC->decrementAvailability($reservation->event_id, $reservation->seats);
        }

        header("Location: index.php?controller=reservation&action=success");
        exit;
    }

    public function adminList() {
        require_once __DIR__ . '/../models/ReservationM.php';
        $reservations = Reservation::getAll();
        require __DIR__ . '/../views/admin/reservationsList.php';
    }

    public function success() {
        echo "Reservation successfully added.";
    }
}
?>
