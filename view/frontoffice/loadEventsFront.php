<?php
/**
 * Script qui retourne les événements actifs au format JSON pour FullCalendar (Frontoffice)
 * Affiche uniquement les événements avec le statut 'active'
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../controller/EventController.php';

$eventController = new EventController();

try {
    // Récupérer uniquement les événements actifs pour le frontoffice
    $eventsResult = $eventController->listEventsByStatus('active');
    
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
            // Couleur pour les événements actifs
            'color' => '#7c00ff',
            'textColor' => '#ffffff',
            // Les événements ne sont pas éditables depuis le frontoffice
            'editable' => false,
            // URL pour voir les détails
            'url' => 'eventDetails.php?id=' . $event['id']
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

