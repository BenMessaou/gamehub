<?php
require_once __DIR__ . '/../../controller/EventController.php';

$eventC = new EventController();
// Récupérer uniquement les événements actifs
$eventsResult = $eventC->listEventsByStatus('active');
$events = $eventsResult->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Événements – Plateforme Gaming</title>
  
  <!-- FullCalendar -->
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/main.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/main.min.js"></script>
  
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #0f0f17 0%, #1b1b29 100%);
      color: #fff;
      min-height: 100vh;
    }
    
    header {
      background: rgba(27, 27, 41, 0.9);
      padding: 20px 40px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.3);
      position: sticky;
      top: 0;
      z-index: 1000;
    }
    
    header h1 {
      font-size: 2rem;
      margin-bottom: 15px;
      background: linear-gradient(120deg, #ff00c7, #7c00ff, #00ffea);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    
    nav {
      display: flex;
      gap: 20px;
      flex-wrap: wrap;
    }
    
    nav a {
      color: #fff;
      text-decoration: none;
      padding: 8px 16px;
      border-radius: 20px;
      transition: all 0.3s;
      border: 1px solid rgba(124, 0, 255, 0.3);
    }
    
    nav a:hover {
      background: linear-gradient(120deg, #ff00c7, #7c00ff);
      transform: translateY(-2px);
      box-shadow: 0 4px 15px rgba(124, 0, 255, 0.4);
    }
    
    .container {
      max-width: 1400px;
      margin: 0 auto;
      padding: 40px 20px;
    }
    
    .events-list {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
      gap: 30px;
      margin-bottom: 60px;
    }
    
    .event-card {
      background: rgba(27, 27, 41, 0.8);
      border-radius: 15px;
      overflow: hidden;
      transition: all 0.3s;
      border: 1px solid rgba(124, 0, 255, 0.2);
      box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    }
    
    .event-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 30px rgba(124, 0, 255, 0.4);
      border-color: #7c00ff;
    }
    
    .event-card img {
      width: 100%;
      height: 200px;
      object-fit: cover;
      background: linear-gradient(120deg, #ff00c7, #7c00ff);
    }
    
    .event-card-content {
      padding: 20px;
    }
    
    .event-card h2 {
      font-size: 1.5rem;
      margin-bottom: 10px;
      color: #fff;
    }
    
    .event-card p {
      color: #aaa;
      margin: 8px 0;
      font-size: 0.95rem;
    }
    
    .event-card .btn {
      display: inline-block;
      margin-top: 15px;
      padding: 10px 20px;
      background: linear-gradient(120deg, #ff00c7, #7c00ff);
      color: #fff;
      text-decoration: none;
      border-radius: 25px;
      transition: all 0.3s;
      font-weight: 600;
    }
    
    .event-card .btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 15px rgba(124, 0, 255, 0.5);
    }
    
    .calendar-section {
      margin-top: 60px;
      padding: 40px;
      background: rgba(27, 27, 41, 0.8);
      border-radius: 20px;
      box-shadow: 0 8px 40px rgba(0,0,0,0.4);
      border: 1px solid rgba(124, 0, 255, 0.2);
    }
    
    .calendar-section h2 {
      font-size: 2rem;
      margin-bottom: 30px;
      text-align: center;
      background: linear-gradient(120deg, #ff00c7, #7c00ff, #00ffea);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    
    #calendar {
      background: rgba(15, 15, 23, 0.6);
      padding: 20px;
      border-radius: 15px;
    }
    
    /* Styles FullCalendar personnalisés */
    .fc {
      color: #fff;
    }
    
    .fc-toolbar-title {
      color: #fff !important;
    }
    
    .fc-button {
      background: linear-gradient(120deg, #ff00c7, #7c00ff) !important;
      border: none !important;
    }
    
    .fc-button:hover {
      opacity: 0.8;
    }
    
    .fc-daygrid-event {
      background: linear-gradient(120deg, #7c00ff, #ff00c7) !important;
      border: none !important;
      padding: 5px !important;
      border-radius: 5px !important;
    }
    
    .fc-event-title {
      color: #fff !important;
      font-weight: 600;
    }
    
    .no-events {
      text-align: center;
      padding: 40px;
      color: #aaa;
      font-size: 1.2rem;
    }
    
    .badge-online {
      display: inline-block;
      padding: 4px 12px;
      background: linear-gradient(120deg, #00ffea, #7c00ff);
      border-radius: 15px;
      font-size: 0.8rem;
      margin-left: 10px;
    }
    
    .badge-offline {
      display: inline-block;
      padding: 4px 12px;
      background: #6c757d;
      border-radius: 15px;
      font-size: 0.8rem;
      margin-left: 10px;
    }
  </style>
</head>
<body>
<header>
  <h1>🎮 Explore les Événements</h1>
  <nav>
    <a href="index.php">Accueil</a>
    <a href="#events-list">Liste des Événements</a>
    <a href="#calendar">Calendrier</a>
    <a href="myEvents.html">Mes événements</a>
  </nav>
</header>

<div class="container">
  <section id="events-list" class="events-list">
    <?php if (count($events) > 0): ?>
      <?php foreach ($events as $event): 
        $remaining = $event['capacity'] - $event['reserved_count'];
        $startDate = new DateTime($event['start_date']);
        $endDate = new DateTime($event['end_date']);
      ?>
        <div class="event-card">
          <?php if (!empty($event['banner'])): ?>
            <img src="<?= htmlspecialchars($event['banner']) ?>" alt="<?= htmlspecialchars($event['title']) ?>" />
          <?php else: ?>
            <div style="width: 100%; height: 200px; background: linear-gradient(120deg, #ff00c7, #7c00ff); display: flex; align-items: center; justify-content: center; font-size: 3rem;">
              🎮
            </div>
          <?php endif; ?>
          
          <div class="event-card-content">
            <h2>
              <?= htmlspecialchars($event['title']) ?>
              <?php if ($event['is_online']): ?>
                <span class="badge-online">🌐 En ligne</span>
              <?php else: ?>
                <span class="badge-offline">📍 Présentiel</span>
              <?php endif; ?>
            </h2>
            
            <p>📍 <?= htmlspecialchars($event['location']) ?></p>
            <p>🗓️ <?= $startDate->format('d/m/Y à H:i') ?> - <?= $endDate->format('H:i') ?></p>
            <p>🎫 <?= $remaining ?> places disponibles sur <?= $event['capacity'] ?></p>
            
            <?php if (!empty($event['description'])): ?>
              <p style="margin-top: 10px; color: #ccc;"><?= htmlspecialchars(substr($event['description'], 0, 100)) ?><?= strlen($event['description']) > 100 ? '...' : '' ?></p>
            <?php endif; ?>
            
            <a href="eventDetails.php?id=<?= $event['id'] ?>" class="btn">Voir Détails</a>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="no-events" style="grid-column: 1 / -1;">
        <p>📅 Aucun événement disponible pour le moment.</p>
        <p style="margin-top: 10px; font-size: 1rem;">Revenez bientôt pour découvrir nos prochains événements !</p>
      </div>
    <?php endif; ?>
  </section>

  <section id="calendar" class="calendar-section">
    <h2>📅 Calendrier des Événements</h2>
    <div id="calendar"></div>
  </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  let calendarEl = document.getElementById('calendar');
  
  let calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    locale: 'fr',
    height: 'auto',
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,timeGridDay'
    },
    events: 'loadEventsFront.php',
    eventClick: function(info) {
      // Rediriger vers les détails de l'événement
      if (info.event.url) {
        window.location.href = info.event.url;
        return false;
      }
    },
    eventDisplay: 'block',
    eventTextColor: '#fff',
    eventBackgroundColor: '#7c00ff',
    eventBorderColor: '#ff00c7',
    dayMaxEvents: true,
    moreLinkClick: 'popover'
  });
  
  calendar.render();
});
</script>

</body>
</html>

