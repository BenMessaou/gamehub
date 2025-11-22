<?php
/**
 * Script qui retourne les événements au format JSON pour FullCalendar
 * 
 * Ce script ne sauvegarde pas les événements (c'est le rôle de addEvent.php)
 * Il ne fait que charger et retourner les événements existants
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../../controller/EventController.php';

$eventController = new EventController();

try {
    // Récupérer tous les événements (pour l'admin, on affiche tous les événements)
    $eventsResult = $eventController->listEvents();
    
    // Formater les événements pour FullCalendar
    $formattedEvents = [];
    
    // Récupérer tous les événements
    $events = $eventsResult->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($events as $event) {
        $formattedEvents[] = [
            'id' => $event['id'],
            'title' => $event['title'],
            'start' => $event['start_date'],
            'end' => $event['end_date'],
            'description' => $event['description'],
            'location' => $event['location'],
            'is_online' => (bool)$event['is_online'],
            'capacity' => (int)$event['capacity'],
            'reserved_count' => (int)$event['reserved_count'],
            'banner' => $event['banner'],
            'status' => $event['status'],
            // Optionnel : couleur personnalisée selon le statut
            'color' => $event['status'] === 'active' ? '#7c00ff' : '#6c757d',
            // Les événements ne sont pas éditables directement depuis le calendrier
            'editable' => false
        ];
    }
    
    // Retourner les événements en JSON
    echo json_encode($formattedEvents);
    
} catch (Exception $e) {
    // En cas d'erreur, retourner un tableau vide
    http_response_code(500);
    echo json_encode([
        'error' => 'Erreur lors du chargement des événements: ' . $e->getMessage()
    ]);
}
?>

