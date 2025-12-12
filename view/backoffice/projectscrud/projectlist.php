<?php
include '../../../controller/ProjectController.php';
$projectC = new ProjectController();

// Get status filter from URL (default to 'en_attente' for pending projects)
$statusFilter = isset($_GET['status']) ? $_GET['status'] : 'en_attente';
$validStatuses = ['en_attente', 'publie'];
if (!in_array($statusFilter, $validStatuses)) {
    $statusFilter = 'en_attente';
}

// Get projects by status
$list = $projectC->listProjectsByStatus($statusFilter);

if ($list instanceof PDOStatement) {
    $list = $list->fetchAll(PDO::FETCH_ASSOC);
} elseif ($list instanceof Traversable) {
    $list = iterator_to_array($list);
}

// Get counts for filter tabs
$pendingList = $projectC->listProjectsByStatus('en_attente');
$acceptedList = $projectC->listProjectsByStatus('publie');

$pendingCount = 0;
$acceptedCount = 0;

if ($pendingList instanceof PDOStatement) {
    $pendingCount = count($pendingList->fetchAll(PDO::FETCH_ASSOC));
} elseif ($pendingList instanceof Traversable) {
    $pendingCount = count(iterator_to_array($pendingList));
}

if ($acceptedList instanceof PDOStatement) {
    $acceptedCount = count($acceptedList->fetchAll(PDO::FETCH_ASSOC));
} elseif ($acceptedList instanceof Traversable) {
    $acceptedCount = count(iterator_to_array($acceptedList));
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filterCategory = isset($_GET['filter_category']) ? $_GET['filter_category'] : '';
$filterLocation = isset($_GET['filter_location']) ? $_GET['filter_location'] : '';
$filterDeveloper = isset($_GET['filter_developer']) ? $_GET['filter_developer'] : '';

// Get unique values for filters
$allCategories = [];
$allLocations = [];
$allDevelopers = [];
foreach ($list as $project) {
    if (!empty($project['categorie'])) {
        $allCategories[$project['categorie']] = true;
    }
    if (!empty($project['lieu'])) {
        $allLocations[$project['lieu']] = true;
    }
    if (!empty($project['developpeur'])) {
        $allDevelopers[$project['developpeur']] = true;
    }
}
$allCategories = array_keys($allCategories);
$allLocations = array_keys($allLocations);
$allDevelopers = array_keys($allDevelopers);
sort($allCategories);
sort($allLocations);
sort($allDevelopers);

// Smart search and filtering
if ($search !== '' || $filterCategory !== '' || $filterLocation !== '' || $filterDeveloper !== '') {
    $list = array_filter($list, function ($project) use ($search, $filterCategory, $filterLocation, $filterDeveloper) {
        // Search filter
        if ($search !== '') {
            $needle = mb_strtolower($search);
            $searchTerms = explode(' ', $needle);
            $haystack = mb_strtolower(
                ($project['nom'] ?? '') . ' ' .
                ($project['developpeur'] ?? '') . ' ' .
                ($project['categorie'] ?? '') . ' ' .
                ($project['lieu'] ?? '') . ' ' .
                ($project['description'] ?? '') . ' ' .
                implode(' ', json_decode($project['plateformes'] ?? '[]', true) ?? [])
            );
            
            // Smart search: all terms must be found (AND logic)
            $allTermsFound = true;
            foreach ($searchTerms as $term) {
                if (trim($term) !== '' && strpos($haystack, trim($term)) === false) {
                    $allTermsFound = false;
                    break;
                }
            }
            if (!$allTermsFound) {
                return false;
            }
        }
        
        // Category filter
        if ($filterCategory !== '' && ($project['categorie'] ?? '') !== $filterCategory) {
            return false;
        }
        
        // Location filter
        if ($filterLocation !== '' && ($project['lieu'] ?? '') !== $filterLocation) {
            return false;
        }
        
        // Developer filter
        if ($filterDeveloper !== '' && ($project['developpeur'] ?? '') !== $filterDeveloper) {
            return false;
        }
        
        return true;
    });
    
    // Re-index array after filtering
    $list = array_values($list);
}

// Calculate statistics (use all projects for stats)
$allProjects = $projectC->listProjects(null);
if ($allProjects instanceof PDOStatement) {
    $allProjects = $allProjects->fetchAll(PDO::FETCH_ASSOC);
} elseif ($allProjects instanceof Traversable) {
    $allProjects = iterator_to_array($allProjects);
}

$stats = [
    'total' => count($allProjects),
    'categories' => [],
    'plateformes' => [],
    'lieux' => [],
    'recent' => 0,
    'thisWeek' => 0,
    'thisMonth' => 0,
    'developers' => [],
    'byMonth' => [],
    'withTrailer' => 0,
    'withDownload' => 0,
    'avgAge' => 0,
    'totalPlatforms' => 0
];

$now = new DateTime();
$weekAgo = clone $now;
$weekAgo->modify('-7 days');
$monthAgo = clone $now;
$monthAgo->modify('-30 days');

$ages = [];
$totalAges = 0;
$ageCount = 0;

foreach ($allProjects as $project) {
    // Par catégorie
    $cat = $project['categorie'] ?? 'Non catégorisé';
    $stats['categories'][$cat] = ($stats['categories'][$cat] ?? 0) + 1;
    
    // Par plateforme
    $platforms = json_decode($project['plateformes'] ?? '[]', true) ?? [];
    $stats['totalPlatforms'] += count($platforms);
    foreach ($platforms as $platform) {
        $platform = trim($platform);
        if ($platform) {
            $stats['plateformes'][$platform] = ($stats['plateformes'][$platform] ?? 0) + 1;
        }
    }
    
    // Par lieu
    $lieu = $project['lieu'] ?? 'Non renseigné';
    $stats['lieux'][$lieu] = ($stats['lieux'][$lieu] ?? 0) + 1;
    
    // Par développeur
    $dev = $project['developpeur'] ?? 'Inconnu';
    $stats['developers'][$dev] = ($stats['developers'][$dev] ?? 0) + 1;
    
    // Projets avec trailer et téléchargement
    if (!empty($project['trailer'])) {
        $stats['withTrailer']++;
    }
    if (!empty($project['lien_telechargement'])) {
        $stats['withDownload']++;
    }
    
    // Par date de création
    if (!empty($project['date_creation'])) {
        try {
            $dateCreation = new DateTime($project['date_creation']);
            $diff = $now->diff($dateCreation);
            
            // Cette semaine
            if ($dateCreation >= $weekAgo) {
                $stats['thisWeek']++;
            }
            
            // Ce mois
            if ($dateCreation >= $monthAgo) {
                $stats['thisMonth']++;
                $stats['recent']++;
            }
            
            // Par mois
            $monthKey = $dateCreation->format('Y-m');
            $stats['byMonth'][$monthKey] = ($stats['byMonth'][$monthKey] ?? 0) + 1;
        } catch (Exception $e) {
            // Ignore invalid dates
        }
    }
    
    // Âge recommandé moyen
    if (!empty($project['age_recommande'])) {
        $age = preg_replace('/[^0-9]/', '', $project['age_recommande']);
        if (is_numeric($age) && $age > 0) {
            $ages[] = (int)$age;
            $totalAges += (int)$age;
            $ageCount++;
        }
    }
}

// Calculer l'âge moyen
if ($ageCount > 0) {
    $stats['avgAge'] = round($totalAges / $ageCount, 1);
}

// Trier les statistiques
arsort($stats['categories']);
arsort($stats['plateformes']);
arsort($stats['lieux']);
arsort($stats['developers']);
ksort($stats['byMonth']); // Trier par date

// Calculer les pourcentages
$stats['recentPercent'] = $stats['total'] > 0 ? round(($stats['recent'] / $stats['total']) * 100, 1) : 0;
$stats['trailerPercent'] = $stats['total'] > 0 ? round(($stats['withTrailer'] / $stats['total']) * 100, 1) : 0;
$stats['downloadPercent'] = $stats['total'] > 0 ? round(($stats['withDownload'] / $stats['total']) * 100, 1) : 0;
$stats['avgPlatforms'] = $stats['total'] > 0 ? round($stats['totalPlatforms'] / $stats['total'], 1) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>GameHub | Projects</title>
    <link rel="stylesheet" href="../../frontoffice/collaborations.css">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/lineicons.css" />
    <link rel="stylesheet" href="styles.css" />
    <style>
        /* Styles pour la table avec le template */
        tbody tr {
            transition: all 0.3s ease;
        }
        tbody tr:hover {
            background: rgba(0, 255, 136, 0.08) !important;
            box-shadow: 0 0 20px rgba(0, 255, 136, 0.1);
        }
        .hero-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 24px;
            padding: 36px;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            background: linear-gradient(130deg, rgba(255,0,199,0.2), rgba(0,255,234,0.12));
            box-shadow: 0 20px 60px rgba(0,0,0,0.45);
        }
        .hero-card h1 {
            font-family: Orbitron, sans-serif;
            font-size: 2.5rem;
            margin: 0;
            background: linear-gradient(90deg, #ff00c7, #00ffea);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .hero-card p { color: var(--text-light); max-width: 540px; }
        .btn-ghost {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 26px;
            border-radius: 999px;
            border: 1px solid var(--border);
            color: var(--text);
            text-decoration: none;
            transition: var(--transition-fast);
        }
        .btn-ghost:hover {
            border-color: var(--primary);
            box-shadow: 0 0 20px rgba(255,0,199,0.35);
        }
        .table-wrapper { margin-top: 32px; }
        .project-id {
            font-family: Orbitron, sans-serif;
            font-size: 0.95rem;
            color: var(--cyan);
        }
        .platform-chip {
            display: inline-flex;
            padding: 4px 12px;
            border-radius: 999px;
            background: rgba(0, 255, 234, 0.12);
            border: 1px solid var(--border-cyan);
            color: var(--cyan);
            font-size: 0.8rem;
            margin: 2px;
        }
        .action-buttons {
            display: flex;
            gap: 8px;
        }
        .action-buttons button,
        .action-buttons a {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: none;
            border-radius: 999px;
            padding: 8px 18px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            color: #fff;
        }
        .btn-outline {
            background: rgba(255, 0, 199, 0.15);
            border: 1px solid rgba(255, 0, 199, 0.6);
            color: #fff;
        }
        .btn-outline:hover {
            border-color: var(--primary);
            color: #fff;
            box-shadow: 0 0 20px rgba(255,0,199,0.4);
        }
        .btn-danger-ghost {
            background: rgba(255, 51, 92, 0.15);
            border: 1px solid rgba(255, 51, 92, 0.5);
            color: #ff6b81;
        }
        .btn-danger-ghost:hover {
            background: rgba(255, 51, 92, 0.3);
            color: #fff;
        }
        .btn-success {
            background: rgba(0, 255, 234, 0.15);
            border: 1px solid rgba(0, 255, 234, 0.6);
            color: #00ffea;
        }
        .btn-success:hover {
            background: rgba(0, 255, 234, 0.3);
            color: #fff;
            box-shadow: 0 0 20px rgba(0, 255, 234, 0.4);
        }
        .btn-warning {
            background: rgba(255, 200, 0, 0.15);
            border: 1px solid rgba(255, 200, 0, 0.6);
            color: #ffc800;
        }
        .btn-warning:hover {
            background: rgba(255, 200, 0, 0.3);
            color: #fff;
            box-shadow: 0 0 20px rgba(255, 200, 0, 0.4);
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #ccc;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            border: 1px solid rgba(0, 255, 136, 0.3);
            box-shadow: 0 10px 30px rgba(0, 255, 136, 0.2);
        }
        .empty-state h3 {
            font-family: Orbitron, sans-serif;
            margin-bottom: 16px;
            color: #00ff88;
            text-shadow: 0 0 20px rgba(0, 255, 136, 0.5);
        }
        .empty-state p {
            color: #ccc;
        }
        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .neon-field {
            background: rgba(5, 0, 20, 0.75);
            border: 1px solid rgba(255, 0, 199, 0.2);
            color: #fff;
            border-radius: var(--radius-sm);
            padding: 10px 16px;
            transition: var(--transition-fast);
        }
        .neon-field:focus {
            border-color: var(--primary);
            box-shadow: 0 0 18px rgba(255, 0, 199, 0.35);
            background: rgba(0, 0, 0, 0.65);
        }
    </style>
</head>
<body>
  <header>
    <div class="container" style="display: flex; justify-content: space-between; align-items: center; position: relative;">
      <h1 class="logo" style="flex-shrink: 0; margin: 0;">GameHub Admin</h1>
      <nav style="flex: 1; display: flex; justify-content: center;">
        <ul style="display: flex; list-style: none; gap: 1rem; margin: 0; padding: 0; flex-wrap: wrap; justify-content: center;">
          <li style="margin: 0;"><a href="#" class="super-button" id="statisticsBtn">Statistiques</a></li>
          <li style="margin: 0;"><a href="projectlist.php" class="super-button">Projects</a></li>
          <li style="margin: 0;"><a href="addProject.php" class="super-button">Add Project</a></li>
          <li style="margin: 0;"><a href="../collabcrud/collaboration.php" class="super-button">🤝 Collab</a></li>
        </ul>
      </nav>
      <button id="sidebar-toggle" class="sidebar-toggle" style="flex-shrink: 0; margin-left: 1rem;">☰</button>
    </div>
  </header>

  <aside id="sidebar" class="sidebar">
    <nav>
      <ul>
        <li><a href="#" id="statisticsBtnSidebar">Statistiques</a></li>
        <li><a href="projectlist.php">Projects</a></li>
        <li><a href="addProject.php">Add Project</a></li>
        <li><a href="../collabcrud/collaboration.php">🤝 Collab</a></li>
      </ul>
    </nav>
  </aside>

  <main id="main-content" class="main-content">
    <!-- Images décoratives flottantes en arrière-plan -->
    <div class="decorative-images">
      <img src="../../frontoffice/assests/logo.png" alt="Decor" class="decor-img decor-img-1">
      <img src="../../frontoffice/assests/game5.png" alt="Decor" class="decor-img decor-img-2">
      <img src="../../frontoffice/assests/logo.png" alt="Decor" class="decor-img decor-img-3">
    </div>

    <div class="animated-strip">
      <div class="strip-content">
        <img src="../../frontoffice/assests/nim.jpg" />
        <img src="../../frontoffice/assests/rambling.jpg" />
        <img src="../../frontoffice/assests/house.jpg" />
        <img src="../../frontoffice/assests/planet.jpg"  />
        <img src="../../frontoffice/assests/girl.jpg"  />

        <img src="../../frontoffice/assests/nim.jpg" />
        <img src="../../frontoffice/assests/rambling.jpg" />
        <img src="../../frontoffice/assests/3.png" />
        <img src="../../frontoffice/assests/planet.jpg"  />
        <img src="../../frontoffice/assests/1.png"  />
        <img src="../../frontoffice/assests/nim.jpg" />
        <img src="../../frontoffice/assests/rambling.jpg" />
        <img src="../../frontoffice/assests/house.jpg" />
        <img src="../../frontoffice/assests/planet.jpg"  />
        <img src="../../frontoffice/assests/girl.jpg"  />
        <img src="../../frontoffice/assests/nim.jpg" />
        <img src="../../frontoffice/assests/rambling.jpg" />
        <img src="../../frontoffice/assests/house.jpg" />
        <img src="../../frontoffice/assests/planet.jpg"  />
        <img src="../../frontoffice/assests/girl.jpg"  />
        <img src="../../frontoffice/assests/nim.jpg" />
        <img src="../../frontoffice/assests/1.png" />
        <img src="../../frontoffice/assests/house.jpg" />
        <img src="../../frontoffice/assests/5.png"  />
        <img src="../../frontoffice/assests/girl.jpg"  />
      </div>
    </div>

    <div class="container">

        <div class="collabs-container">
            <div class="collabs-header">
                <h1><?= $statusFilter === 'en_attente' ? 'Pending Projects' : 'Accepted Projects' ?></h1>
                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a href="addProject.php" class="super-button">➕ Add Project</a>
                    <a href="index1.html" class="super-button">← Back to Dashboard</a>
                </div>
            </div>

        <div class="collabs-container">
            <div class="collabs-header" style="margin-bottom: 2rem;">
                <h2 style="color: #fff; font-size: 2rem; text-shadow: 0 0 20px rgba(0, 255, 136, 0.5); margin: 0;">
                    Liste des projets
                </h2>
                <p style="color: #ccc; margin-top: 0.5rem;">
                    <?= count($list) ?> entr&eacute;es affich&eacute;es<?= $search !== '' ? " &bull; filtre &laquo; " . htmlspecialchars($search) . " &raquo;" : ''; ?>
                </p>
            </div>

            <!-- Filtres avancés -->
            <div style="background: rgba(0, 0, 0, 0.6); backdrop-filter: blur(10px); border-radius: 15px; padding: 1.5rem; border: 1px solid rgba(0, 255, 136, 0.3); margin-bottom: 2rem; box-shadow: 0 10px 30px rgba(0, 255, 136, 0.2);">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem;">
                    <!-- Status filter tabs -->
                    <div style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center;">
                        <a href="?status=en_attente<?= $search ? '&search=' . urlencode($search) : '' ?><?= $filterCategory ? '&filter_category=' . urlencode($filterCategory) : '' ?><?= $filterLocation ? '&filter_location=' . urlencode($filterLocation) : '' ?><?= $filterDeveloper ? '&filter_developer=' . urlencode($filterDeveloper) : '' ?>" 
                           class="super-button <?= $statusFilter === 'en_attente' ? 'active' : '' ?>" 
                           style="<?= $statusFilter === 'en_attente' ? 'background: rgba(255, 0, 199, 0.3); border-color: rgba(255, 0, 199, 0.8);' : '' ?>">
                            Pending (<?= $pendingCount ?>)
                        </a>
                        <a href="?status=publie<?= $search ? '&search=' . urlencode($search) : '' ?><?= $filterCategory ? '&filter_category=' . urlencode($filterCategory) : '' ?><?= $filterLocation ? '&filter_location=' . urlencode($filterLocation) : '' ?><?= $filterDeveloper ? '&filter_developer=' . urlencode($filterDeveloper) : '' ?>" 
                           class="super-button <?= $statusFilter === 'publie' ? 'active' : '' ?>"
                           style="<?= $statusFilter === 'publie' ? 'background: rgba(0, 255, 234, 0.3); border-color: rgba(0, 255, 234, 0.8);' : '' ?>">
                            Accepted (<?= $acceptedCount ?>)
                        </a>
                    </div>
                    
                    <!-- Search bar -->
                    <form id="searchForm" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center; flex: 1; justify-content: flex-end; min-width: 300px;" method="GET" action="">
                        <input type="hidden" name="status" value="<?= $statusFilter ?>">
                        <input type="hidden" name="filter_category" id="filter_category" value="<?= htmlspecialchars($filterCategory); ?>">
                        <input type="hidden" name="filter_location" id="filter_location" value="<?= htmlspecialchars($filterLocation); ?>">
                        <input type="hidden" name="filter_developer" id="filter_developer" value="<?= htmlspecialchars($filterDeveloper); ?>">
                        <input type="text" name="search" id="searchInput" value="<?= htmlspecialchars($search); ?>" 
                               style="padding: 12px 20px; background: rgba(0, 0, 0, 0.6); border: 2px solid rgba(0, 255, 136, 0.3); border-radius: 50px; color: #fff; font-size: 14px; flex: 1; min-width: 200px; max-width: 400px; transition: all 0.3s ease;" 
                               placeholder="Rechercher un jeu, un studio, catégorie..."
                               onfocus="this.style.borderColor='rgba(0, 255, 136, 0.6)'; this.style.boxShadow='0 0 20px rgba(0, 255, 136, 0.3)';"
                               onblur="this.style.borderColor='rgba(0, 255, 136, 0.3)'; this.style.boxShadow='none';">
                        <button type="submit" class="super-button" style="padding: 12px 24px; white-space: nowrap;">🔍 Rechercher</button>
                    </form>
                </div>
                
                <!-- Filtres avancés -->
                <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid rgba(0, 255, 136, 0.2);">
                    <div style="flex: 1; min-width: 200px;">
                        <label style="display: block; color: #00ff88; margin-bottom: 0.5rem; font-size: 0.9rem; font-weight: 600;">Catégorie</label>
                        <select id="categoryFilter" style="width: 100%; padding: 10px 15px; background: rgba(0, 0, 0, 0.6); border: 2px solid rgba(0, 255, 136, 0.3); border-radius: 50px; color: #fff; font-size: 14px; cursor: pointer; transition: all 0.3s ease;"
                                onchange="updateFilters()"
                                onfocus="this.style.borderColor='rgba(0, 255, 136, 0.6)'; this.style.boxShadow='0 0 20px rgba(0, 255, 136, 0.3)';"
                                onblur="this.style.borderColor='rgba(0, 255, 136, 0.3)'; this.style.boxShadow='none';">
                            <option value="">Toutes les catégories</option>
                            <?php foreach ($allCategories as $cat): ?>
                                <option value="<?= htmlspecialchars($cat); ?>" <?= $filterCategory === $cat ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div style="flex: 1; min-width: 200px;">
                        <label style="display: block; color: #00ff88; margin-bottom: 0.5rem; font-size: 0.9rem; font-weight: 600;">Lieu</label>
                        <select id="locationFilter" style="width: 100%; padding: 10px 15px; background: rgba(0, 0, 0, 0.6); border: 2px solid rgba(0, 255, 136, 0.3); border-radius: 50px; color: #fff; font-size: 14px; cursor: pointer; transition: all 0.3s ease;"
                                onchange="updateFilters()"
                                onfocus="this.style.borderColor='rgba(0, 255, 136, 0.6)'; this.style.boxShadow='0 0 20px rgba(0, 255, 136, 0.3)';"
                                onblur="this.style.borderColor='rgba(0, 255, 136, 0.3)'; this.style.boxShadow='none';">
                            <option value="">Tous les lieux</option>
                            <?php foreach ($allLocations as $loc): ?>
                                <option value="<?= htmlspecialchars($loc); ?>" <?= $filterLocation === $loc ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($loc); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div style="flex: 1; min-width: 200px;">
                        <label style="display: block; color: #00ff88; margin-bottom: 0.5rem; font-size: 0.9rem; font-weight: 600;">Développeur</label>
                        <select id="developerFilter" style="width: 100%; padding: 10px 15px; background: rgba(0, 0, 0, 0.6); border: 2px solid rgba(0, 255, 136, 0.3); border-radius: 50px; color: #fff; font-size: 14px; cursor: pointer; transition: all 0.3s ease;"
                                onchange="updateFilters()"
                                onfocus="this.style.borderColor='rgba(0, 255, 136, 0.6)'; this.style.boxShadow='0 0 20px rgba(0, 255, 136, 0.3)';"
                                onblur="this.style.borderColor='rgba(0, 255, 136, 0.3)'; this.style.boxShadow='none';">
                            <option value="">Tous les développeurs</option>
                            <?php foreach ($allDevelopers as $dev): ?>
                                <option value="<?= htmlspecialchars($dev); ?>" <?= $filterDeveloper === $dev ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($dev); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <?php if ($search !== '' || $filterCategory !== '' || $filterLocation !== '' || $filterDeveloper !== ''): ?>
                        <div style="display: flex; align-items: flex-end;">
                            <a href="?status=<?= $statusFilter ?>" class="super-button" style="padding: 10px 20px; background: rgba(255, 51, 92, 0.2); color: #ff335c; border-color: rgba(255, 51, 92, 0.5);">
                                ✕ Réinitialiser
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (count($list) === 0): ?>
                <div class="empty-state">
                    <h3>Aucun projet trouv&eacute;</h3>
                    <p>Essayez de retirer le filtre ou ajoutez une nouvelle fiche.</p>
                    <a href="addProject.php" class="super-button" style="margin-top: 1.5rem; display: inline-flex; align-items: center; gap: 0.5rem;">
                        ➕ Cr&eacute;er un projet
                    </a>
                </div>
            <?php else: ?>
                <div style="background: rgba(0, 0, 0, 0.6); backdrop-filter: blur(10px); border-radius: 15px; padding: 2rem; border: 1px solid rgba(0, 255, 136, 0.3); box-shadow: 0 10px 30px rgba(0, 255, 136, 0.2); overflow-x: auto; position: relative;">
                    <div style="position: absolute; top: -50%; left: -50%; width: 200%; height: 200%; background: conic-gradient(from 0deg, #00ffff, #ff00ff, #00ffff); animation: rotate 4s linear infinite; z-index: -1; opacity: 0.05; pointer-events: none;"></div>
                    <table style="width: 100%; border-collapse: collapse; color: #fff; position: relative; z-index: 1;">
                        <thead>
                        <tr>
                            <th style="padding: 1rem; text-align: left; background: rgba(0, 255, 136, 0.1); color: #00ff88; border-bottom: 2px solid rgba(0, 255, 136, 0.3);">ID</th>
                            <th style="padding: 1rem; text-align: left; background: rgba(0, 255, 136, 0.1); color: #00ff88; border-bottom: 2px solid rgba(0, 255, 136, 0.3);">Nom</th>
                            <th style="padding: 1rem; text-align: left; background: rgba(0, 255, 136, 0.1); color: #00ff88; border-bottom: 2px solid rgba(0, 255, 136, 0.3);">D&eacute;veloppeur</th>
                            <th style="padding: 1rem; text-align: left; background: rgba(0, 255, 136, 0.1); color: #00ff88; border-bottom: 2px solid rgba(0, 255, 136, 0.3);">Date cr&eacute;ation</th>
                            <th style="padding: 1rem; text-align: left; background: rgba(0, 255, 136, 0.1); color: #00ff88; border-bottom: 2px solid rgba(0, 255, 136, 0.3);">Cat&eacute;gorie</th>
                            <th style="padding: 1rem; text-align: left; background: rgba(0, 255, 136, 0.1); color: #00ff88; border-bottom: 2px solid rgba(0, 255, 136, 0.3);">&Acirc;ge</th>
                            <th style="padding: 1rem; text-align: left; background: rgba(0, 255, 136, 0.1); color: #00ff88; border-bottom: 2px solid rgba(0, 255, 136, 0.3);">Lieu</th>
                            <th style="padding: 1rem; text-align: left; background: rgba(0, 255, 136, 0.1); color: #00ff88; border-bottom: 2px solid rgba(0, 255, 136, 0.3);">Plateformes</th>
                            <th style="padding: 1rem; text-align: right; background: rgba(0, 255, 136, 0.1); color: #00ff88; border-bottom: 2px solid rgba(0, 255, 136, 0.3);">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($list as $project) : ?>
                            <?php $plateformes = json_decode($project['plateformes'], true) ?? []; ?>
                            <tr style="border-bottom: 1px solid rgba(0, 255, 136, 0.1); transition: all 0.3s ease;" 
                                onmouseover="this.style.background='rgba(0, 255, 136, 0.05)'; this.style.transform='scale(1.01)';" 
                                onmouseout="this.style.background='transparent'; this.style.transform='scale(1)';">
                                <td style="padding: 1rem;">
                                    <span style="font-family: 'Orbitron', sans-serif; font-size: 0.95rem; color: #00ffea; font-weight: 600;">
                                        #<?= $project['id']; ?>
                                    </span>
                                </td>
                                <td style="padding: 1rem; color: #fff;"><?= htmlspecialchars($project['nom']); ?></td>
                                <td style="padding: 1rem; color: #ccc;"><?= htmlspecialchars($project['developpeur']); ?></td>
                                <td style="padding: 1rem; color: #ccc;"><?= htmlspecialchars($project['date_creation']); ?></td>
                                <td style="padding: 1rem;">
                                    <span style="display: inline-block; padding: 4px 12px; background: rgba(0, 255, 136, 0.2); color: #00ff88; border: 1px solid rgba(0, 255, 136, 0.3); border-radius: 15px; font-size: 0.85rem;">
                                        <?= htmlspecialchars($project['categorie']); ?>
                                    </span>
                                </td>
                                <td style="padding: 1rem; color: #ccc;"><?= $project['age_recommande'] ?? "--"; ?></td>
                                <td style="padding: 1rem; color: #ccc;"><?= $project['lieu'] ?? "--"; ?></td>
                                <td style="padding: 1rem;">
                                    <?php if (count($plateformes) > 0): ?>
                                        <?php foreach ($plateformes as $platform): ?>
                                            <span style="display: inline-block; padding: 4px 12px; background: rgba(0, 255, 234, 0.12); border: 1px solid rgba(0, 255, 234, 0.3); color: #00ffea; border-radius: 15px; font-size: 0.8rem; margin: 2px;">
                                                <?= htmlspecialchars(trim($platform)); ?>
                                            </span>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span style="color: #888;">--</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 1rem; text-align: right;">
                                    <div style="display: flex; gap: 0.5rem; justify-content: flex-end; flex-wrap: wrap; align-items: center;">
                                        <a href="showproject.php?id=<?= $project['id']; ?>" class="btn-view" title="View" style="padding: 8px 16px; font-size: 0.85rem; white-space: nowrap;">
                                            👁️ View
                                        </a>
                                        <?php if ($statusFilter === 'en_attente'): ?>
                                            <!-- Actions for pending projects -->
                                            <a href="updateproject.php?id=<?= $project['id']; ?>" class="btn-view" title="Edit" style="padding: 8px 16px; font-size: 0.85rem; background: rgba(255, 200, 0, 0.2); color: #ffc800; border-color: rgba(255, 200, 0, 0.5); white-space: nowrap;">
                                                ✏️ Edit
                                            </a>
                                            <form method="POST" action="approveProject.php" style="display: inline; margin: 0;">
                                                <input type="hidden" name="id" value="<?= $project['id']; ?>">
                                                <button type="submit" class="btn-view" title="Accept" style="padding: 8px 16px; font-size: 0.85rem; background: rgba(0, 255, 136, 0.2); color: #00ff88; border-color: rgba(0, 255, 136, 0.5); cursor: pointer; white-space: nowrap; font-family: inherit;">
                                                    ✅ Accept
                                                </button>
                                            </form>
                                            <a href="deleteproject.php?id=<?= $project['id']; ?>" class="btn-view" onclick="return confirm('Reject and delete this project?')" style="padding: 8px 16px; font-size: 0.85rem; background: rgba(255, 51, 92, 0.2); color: #ff335c; border-color: rgba(255, 51, 92, 0.5); white-space: nowrap;">
                                                ❌ Reject
                                            </a>
                                        <?php else: ?>
                                            <!-- Actions for accepted projects -->
                                            <a href="updateproject.php?id=<?= $project['id']; ?>" class="btn-view" title="Edit" style="padding: 8px 16px; font-size: 0.85rem; background: rgba(255, 200, 0, 0.2); color: #ffc800; border-color: rgba(255, 200, 0, 0.5); white-space: nowrap;">
                                                ✏️ Edit
                                            </a>
                                            <a href="deleteproject.php?id=<?= $project['id']; ?>" class="btn-view" onclick="return confirm('Delete this project?')" style="padding: 8px 16px; font-size: 0.85rem; background: rgba(255, 51, 92, 0.2); color: #ff335c; border-color: rgba(255, 51, 92, 0.5); white-space: nowrap;">
                                                🗑️ Delete
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
  </main>

  <footer class="footer">
    <div class="footer-content">
      <div class="footer-section about">
        <h3>GameHub Admin</h3>
        <p>Propulsé par la team XR Labs</p>
      </div>
      <div class="footer-section links">
        <h3>Quick Links</h3>
        <ul>
          <li><a href="projectlist.php">Projects</a></li>
          <li><a href="addProject.php">Add Project</a></li>
          <li><a href="../collabcrud/collaboration.php">Collaborations</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <p>&copy; 2025 GameHub Pro | All rights reserved | Tunis, Tunisia</p>
    </div>
  </footer>

<!-- Modale des statistiques -->
<div id="statisticsModal" class="statistics-modal" style="display: none;">
    <div class="statistics-modal-content">
        <div class="statistics-modal-header">
            <div>
                <h2>📈 Tableau de bord statistiques</h2>
                <p class="statistics-subtitle">Analyse complète de votre plateforme GameHub</p>
            </div>
            <button class="statistics-modal-close" id="closeStatisticsBtn">&times;</button>
        </div>
        <div class="statistics-modal-body">
            <!-- Cartes statistiques principales -->
            <div class="statistics-grid-main">
                <div class="stat-card stat-card-primary">
                    <div class="stat-card-icon">🎮</div>
                    <div class="stat-card-content">
                        <div class="stat-card-value" data-target="<?= $stats['total']; ?>">0</div>
                        <div class="stat-card-label">Total des projets</div>
                        <div class="stat-card-trend">
                            <span class="trend-up">↗</span> <?= $stats['thisWeek']; ?> cette semaine
                        </div>
                    </div>
                </div>
                <div class="stat-card stat-card-success">
                    <div class="stat-card-icon">🆕</div>
                    <div class="stat-card-content">
                        <div class="stat-card-value" data-target="<?= $stats['thisMonth']; ?>">0</div>
                        <div class="stat-card-label">Nouveaux ce mois</div>
                        <div class="stat-card-trend">
                            <span class="trend-up">↗</span> <?= $stats['recentPercent']; ?>% du total
                        </div>
                    </div>
                </div>
                <div class="stat-card stat-card-info">
                    <div class="stat-card-icon">🎬</div>
                    <div class="stat-card-content">
                        <div class="stat-card-value" data-target="<?= $stats['withTrailer']; ?>">0</div>
                        <div class="stat-card-label">Avec trailer</div>
                        <div class="stat-card-progress">
                            <div class="progress-bar" style="width: <?= $stats['trailerPercent']; ?>%"></div>
                        </div>
                        <div class="stat-card-percent"><?= $stats['trailerPercent']; ?>%</div>
                    </div>
                </div>
                <div class="stat-card stat-card-warning">
                    <div class="stat-card-icon">⬇️</div>
                    <div class="stat-card-content">
                        <div class="stat-card-value" data-target="<?= $stats['withDownload']; ?>">0</div>
                        <div class="stat-card-label">Téléchargeables</div>
                        <div class="stat-card-progress">
                            <div class="progress-bar" style="width: <?= $stats['downloadPercent']; ?>%"></div>
                        </div>
                        <div class="stat-card-percent"><?= $stats['downloadPercent']; ?>%</div>
                    </div>
                </div>
                <div class="stat-card stat-card-purple">
                    <div class="stat-card-icon">👥</div>
                    <div class="stat-card-content">
                        <div class="stat-card-value" data-target="<?= count($stats['developers']); ?>">0</div>
                        <div class="stat-card-label">Développeurs</div>
                        <div class="stat-card-trend">
                            <span class="trend-info">ℹ</span> <?= $stats['avgPlatforms']; ?> plateformes/projet
                        </div>
                    </div>
                </div>
                <div class="stat-card stat-card-cyan">
                    <div class="stat-card-icon">🎯</div>
                    <div class="stat-card-content">
                        <div class="stat-card-value" data-target="<?= $stats['avgAge']; ?>">0</div>
                        <div class="stat-card-label">Âge moyen</div>
                        <div class="stat-card-trend">
                            <span class="trend-info">ℹ</span> Recommandation
                        </div>
                    </div>
                </div>
            </div>

            <!-- Graphiques -->
            <div class="statistics-charts-grid">
                <div class="chart-container">
                    <h3 class="chart-title">📊 Répartition par catégorie</h3>
                    <canvas id="categoryChart"></canvas>
                </div>
                <div class="chart-container">
                    <h3 class="chart-title">📱 Répartition par plateforme</h3>
                    <canvas id="platformChart"></canvas>
                </div>
            </div>

            <div class="chart-container-full">
                <h3 class="chart-title">📈 Évolution mensuelle des projets</h3>
                <canvas id="monthlyChart"></canvas>
            </div>

            <!-- Sections détaillées -->
            <div class="statistics-sections-grid">
                <div class="statistics-section">
                    <h3><span class="section-icon">🏷️</span> Top catégories</h3>
                    <div class="stat-list-enhanced">
                        <?php if (count($stats['categories']) > 0): ?>
                            <?php $topCategories = array_slice($stats['categories'], 0, 8, true); ?>
                            <?php foreach ($topCategories as $cat => $count): ?>
                                <?php $percent = $stats['total'] > 0 ? round(($count / $stats['total']) * 100, 1) : 0; ?>
                                <div class="stat-item-enhanced">
                                    <div class="stat-item-info">
                                        <span class="stat-item-label"><?= htmlspecialchars($cat); ?></span>
                                        <span class="stat-item-percent"><?= $percent; ?>%</span>
                                    </div>
                                    <div class="stat-item-bar-container">
                                        <div class="stat-item-bar" style="width: <?= $percent; ?>%"></div>
                                    </div>
                                    <span class="stat-item-value"><?= $count; ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">Aucune catégorie</p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="statistics-section">
                    <h3><span class="section-icon">🌍</span> Top lieux de développement</h3>
                    <div class="stat-list-enhanced">
                        <?php if (count($stats['lieux']) > 0): ?>
                            <?php $topLieux = array_slice($stats['lieux'], 0, 8, true); ?>
                            <?php foreach ($topLieux as $lieu => $count): ?>
                                <?php $percent = $stats['total'] > 0 ? round(($count / $stats['total']) * 100, 1) : 0; ?>
                                <div class="stat-item-enhanced">
                                    <div class="stat-item-info">
                                        <span class="stat-item-label"><?= htmlspecialchars($lieu); ?></span>
                                        <span class="stat-item-percent"><?= $percent; ?>%</span>
                                    </div>
                                    <div class="stat-item-bar-container">
                                        <div class="stat-item-bar" style="width: <?= $percent; ?>%"></div>
                                    </div>
                                    <span class="stat-item-value"><?= $count; ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">Aucun lieu renseigné</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="statistics-section">
                <h3><span class="section-icon">👨‍💻</span> Top développeurs</h3>
                <div class="stat-list-enhanced">
                    <?php if (count($stats['developers']) > 0): ?>
                        <?php $topDevs = array_slice($stats['developers'], 0, 10, true); ?>
                        <?php foreach ($topDevs as $dev => $count): ?>
                            <div class="stat-item-enhanced">
                                <div class="stat-item-info">
                                    <span class="stat-item-label"><?= htmlspecialchars($dev); ?></span>
                                </div>
                                <div class="stat-item-bar-container">
                                    <?php $maxDev = max($stats['developers']); ?>
                                    <?php $devPercent = $maxDev > 0 ? round(($count / $maxDev) * 100, 1) : 0; ?>
                                    <div class="stat-item-bar" style="width: <?= $devPercent; ?>%"></div>
                                </div>
                                <span class="stat-item-value"><?= $count; ?> projet<?= $count > 1 ? 's' : ''; ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">Aucun développeur</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<style>
.statistics-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.95);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    animation: fadeInModal 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    overflow: hidden;
}

.statistics-modal::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255, 0, 199, 0.1) 0%, transparent 70%);
    animation: rotateGlow 20s linear infinite;
    pointer-events: none;
}

@keyframes fadeInModal {
    from { 
        opacity: 0;
        backdrop-filter: blur(0px);
    }
    to { 
        opacity: 1;
        backdrop-filter: blur(20px);
    }
}

@keyframes rotateGlow {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.statistics-modal-content {
    background: linear-gradient(135deg, rgba(20, 10, 50, 0.98), rgba(10, 5, 30, 0.99));
    border: 2px solid rgba(255, 0, 199, 0.5);
    border-radius: 24px;
    max-width: 1200px;
    width: 100%;
    max-height: 95vh;
    overflow-y: auto;
    box-shadow: 
        0 25px 80px rgba(0, 0, 0, 0.9),
        0 0 60px rgba(255, 0, 199, 0.4),
        inset 0 0 40px rgba(0, 255, 234, 0.05);
    animation: slideUpScale 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
    position: relative;
    z-index: 1;
}

.statistics-modal-content::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(255, 0, 199, 0.03), rgba(0, 255, 234, 0.03));
    border-radius: 24px;
    pointer-events: none;
    z-index: -1;
}

@keyframes slideUpScale {
    from { 
        transform: translateY(50px) scale(0.9); 
        opacity: 0;
    }
    to { 
        transform: translateY(0) scale(1); 
        opacity: 1;
    }
}

.statistics-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 28px 36px;
    border-bottom: 2px solid rgba(255, 0, 199, 0.3);
    background: linear-gradient(90deg, rgba(255, 0, 199, 0.15), rgba(0, 255, 234, 0.08));
    position: relative;
    overflow: hidden;
    animation: headerSlideIn 0.6s ease 0.2s both;
}

.statistics-modal-header::after {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
    animation: headerShine 3s infinite;
}

@keyframes headerSlideIn {
    from {
        transform: translateY(-20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

@keyframes headerShine {
    0% { left: -100%; }
    50% { left: 100%; }
    100% { left: 100%; }
}

.statistics-modal-header h2 {
    font-family: Orbitron, sans-serif;
    font-size: 2rem;
    background: linear-gradient(90deg, #ff00c7, #00ffea, #ff00c7);
    background-size: 200% 100%;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin: 0 0 8px 0;
    animation: gradientShift 3s ease infinite;
    text-shadow: 0 0 30px rgba(255, 0, 199, 0.5);
    position: relative;
    z-index: 1;
}

@keyframes gradientShift {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

.statistics-subtitle {
    color: var(--text-light);
    font-size: 0.95rem;
    margin: 0;
}

.statistics-modal-close {
    background: rgba(255, 0, 199, 0.2);
    border: 2px solid rgba(255, 0, 199, 0.5);
    color: #fff;
    font-size: 1.8rem;
    cursor: pointer;
    padding: 0;
    width: 44px;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    line-height: 1;
    position: relative;
    overflow: hidden;
}

.statistics-modal-close::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 0, 199, 0.3);
    transform: translate(-50%, -50%);
    transition: width 0.4s ease, height 0.4s ease;
}

.statistics-modal-close:hover::before {
    width: 100%;
    height: 100%;
}

.statistics-modal-close:hover {
    background: rgba(255, 0, 199, 0.4);
    transform: rotate(90deg) scale(1.15);
    box-shadow: 
        0 0 25px rgba(255, 0, 199, 0.7),
        inset 0 0 20px rgba(255, 0, 199, 0.2);
    border-color: rgba(255, 0, 199, 0.8);
}


.statistics-modal-body {
    padding: 36px;
}

/* Grille principale des cartes */
.statistics-grid-main {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
    animation: gridFadeIn 0.8s ease 0.3s both;
}

@keyframes gridFadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.statistics-grid-main .stat-card {
    animation: cardPopIn 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) both;
}

.statistics-grid-main .stat-card:nth-child(1) { animation-delay: 0.1s; }
.statistics-grid-main .stat-card:nth-child(2) { animation-delay: 0.2s; }
.statistics-grid-main .stat-card:nth-child(3) { animation-delay: 0.3s; }
.statistics-grid-main .stat-card:nth-child(4) { animation-delay: 0.4s; }
.statistics-grid-main .stat-card:nth-child(5) { animation-delay: 0.5s; }
.statistics-grid-main .stat-card:nth-child(6) { animation-delay: 0.6s; }

@keyframes cardPopIn {
    from {
        opacity: 0;
        transform: scale(0.8) translateY(30px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

.stat-card {
    background: rgba(0, 0, 0, 0.5);
    border: 2px solid rgba(255, 0, 199, 0.2);
    border-radius: 18px;
    padding: 24px;
    display: flex;
    align-items: center;
    gap: 16px;
    transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    position: relative;
    overflow: hidden;
    backdrop-filter: blur(10px);
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.15), transparent);
    transition: left 0.8s ease;
    z-index: 0;
}

.stat-card::after {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255, 0, 199, 0.1) 0%, transparent 70%);
    opacity: 0;
    transition: opacity 0.5s ease;
    z-index: 0;
}

.stat-card:hover::before {
    left: 100%;
}

.stat-card:hover::after {
    opacity: 1;
}

.stat-card:hover {
    transform: translateY(-8px) scale(1.03);
    box-shadow: 
        0 15px 50px rgba(0, 0, 0, 0.7),
        0 0 40px rgba(255, 0, 199, 0.3);
    border-width: 2px;
}

.stat-card-primary { 
    border-color: rgba(255, 0, 199, 0.4);
    background: linear-gradient(135deg, rgba(255, 0, 199, 0.05), rgba(0, 0, 0, 0.5));
}
.stat-card-primary:hover { 
    border-color: rgba(255, 0, 199, 0.8); 
    box-shadow: 0 0 40px rgba(255, 0, 199, 0.5), inset 0 0 20px rgba(255, 0, 199, 0.1);
    background: linear-gradient(135deg, rgba(255, 0, 199, 0.1), rgba(0, 0, 0, 0.6));
}

.stat-card-success { 
    border-color: rgba(0, 255, 234, 0.4);
    background: linear-gradient(135deg, rgba(0, 255, 234, 0.05), rgba(0, 0, 0, 0.5));
}
.stat-card-success:hover { 
    border-color: rgba(0, 255, 234, 0.8); 
    box-shadow: 0 0 40px rgba(0, 255, 234, 0.5), inset 0 0 20px rgba(0, 255, 234, 0.1);
    background: linear-gradient(135deg, rgba(0, 255, 234, 0.1), rgba(0, 0, 0, 0.6));
}

.stat-card-info { 
    border-color: rgba(0, 200, 255, 0.4);
    background: linear-gradient(135deg, rgba(0, 200, 255, 0.05), rgba(0, 0, 0, 0.5));
}
.stat-card-info:hover { 
    border-color: rgba(0, 200, 255, 0.8); 
    box-shadow: 0 0 40px rgba(0, 200, 255, 0.5), inset 0 0 20px rgba(0, 200, 255, 0.1);
    background: linear-gradient(135deg, rgba(0, 200, 255, 0.1), rgba(0, 0, 0, 0.6));
}

.stat-card-warning { 
    border-color: rgba(255, 200, 0, 0.4);
    background: linear-gradient(135deg, rgba(255, 200, 0, 0.05), rgba(0, 0, 0, 0.5));
}
.stat-card-warning:hover { 
    border-color: rgba(255, 200, 0, 0.8); 
    box-shadow: 0 0 40px rgba(255, 200, 0, 0.5), inset 0 0 20px rgba(255, 200, 0, 0.1);
    background: linear-gradient(135deg, rgba(255, 200, 0, 0.1), rgba(0, 0, 0, 0.6));
}

.stat-card-purple { 
    border-color: rgba(150, 0, 255, 0.4);
    background: linear-gradient(135deg, rgba(150, 0, 255, 0.05), rgba(0, 0, 0, 0.5));
}
.stat-card-purple:hover { 
    border-color: rgba(150, 0, 255, 0.8); 
    box-shadow: 0 0 40px rgba(150, 0, 255, 0.5), inset 0 0 20px rgba(150, 0, 255, 0.1);
    background: linear-gradient(135deg, rgba(150, 0, 255, 0.1), rgba(0, 0, 0, 0.6));
}

.stat-card-cyan { 
    border-color: rgba(0, 255, 234, 0.4);
    background: linear-gradient(135deg, rgba(0, 255, 234, 0.05), rgba(0, 0, 0, 0.5));
}
.stat-card-cyan:hover { 
    border-color: rgba(0, 255, 234, 0.8); 
    box-shadow: 0 0 40px rgba(0, 255, 234, 0.5), inset 0 0 20px rgba(0, 255, 234, 0.1);
    background: linear-gradient(135deg, rgba(0, 255, 234, 0.1), rgba(0, 0, 0, 0.6));
}

.stat-card-icon {
    font-size: 3.5rem;
    flex-shrink: 0;
    filter: drop-shadow(0 0 15px rgba(255, 255, 255, 0.4));
    animation: iconPulse 2s ease-in-out infinite;
    position: relative;
    z-index: 1;
    transition: all 0.4s ease;
}

.stat-card:hover .stat-card-icon {
    transform: scale(1.15) rotate(5deg);
    filter: drop-shadow(0 0 25px rgba(255, 255, 255, 0.6));
    animation: iconPulseHover 0.6s ease-in-out infinite;
}

@keyframes iconPulse {
    0%, 100% { 
        transform: scale(1);
        filter: drop-shadow(0 0 15px rgba(255, 255, 255, 0.4));
    }
    50% { 
        transform: scale(1.05);
        filter: drop-shadow(0 0 20px rgba(255, 255, 255, 0.5));
    }
}

@keyframes iconPulseHover {
    0%, 100% { 
        transform: scale(1.15) rotate(5deg);
    }
    50% { 
        transform: scale(1.2) rotate(-5deg);
    }
}

.stat-card-content {
    flex: 1;
    position: relative;
    z-index: 1;
}

.stat-card-value {
    font-family: Orbitron, sans-serif;
    font-size: 2.8rem;
    font-weight: 900;
    background: linear-gradient(135deg, #00ffea, #00c7ff, #00ffea);
    background-size: 200% 100%;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 6px;
    line-height: 1;
    animation: valueGlow 2s ease-in-out infinite;
    text-shadow: 0 0 20px rgba(0, 255, 234, 0.5);
    position: relative;
}

.stat-card-value::after {
    content: attr(data-target);
    position: absolute;
    top: 0;
    left: 0;
    background: linear-gradient(135deg, #00ffea, #00c7ff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    opacity: 0.3;
    filter: blur(8px);
    z-index: -1;
}

@keyframes valueGlow {
    0%, 100% { 
        background-position: 0% 50%;
        filter: drop-shadow(0 0 10px rgba(0, 255, 234, 0.5));
    }
    50% { 
        background-position: 100% 50%;
        filter: drop-shadow(0 0 20px rgba(0, 255, 234, 0.8));
    }
}

.stat-card-label {
    color: var(--text-light);
    font-size: 0.9rem;
    margin-bottom: 8px;
}

.stat-card-trend {
    color: var(--text-light);
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    gap: 4px;
}

.trend-up { color: #00ffea; }
.trend-info { color: #00c7ff; }

.stat-card-progress {
    height: 6px;
    background: rgba(0, 0, 0, 0.3);
    border-radius: 999px;
    overflow: hidden;
    margin: 8px 0;
}

.progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #ff00c7, #00ffea, #ff00c7);
    background-size: 200% 100%;
    border-radius: 999px;
    transition: width 1.5s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 
        0 0 15px rgba(0, 255, 234, 0.6),
        inset 0 0 10px rgba(255, 255, 255, 0.2);
    animation: progressShimmer 2s ease-in-out infinite;
    position: relative;
    overflow: hidden;
}

.progress-bar::after {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
    animation: progressShine 2s ease-in-out infinite;
}

@keyframes progressShimmer {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

@keyframes progressShine {
    0% { left: -100%; }
    50% { left: 100%; }
    100% { left: 100%; }
}

.stat-card-percent {
    color: #00ffea;
    font-size: 0.85rem;
    font-weight: 600;
}

/* Graphiques */
.statistics-charts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 24px;
    margin-bottom: 32px;
}

.chart-container, .chart-container-full {
    background: rgba(0, 0, 0, 0.4);
    border: 2px solid rgba(255, 0, 199, 0.3);
    border-radius: 18px;
    padding: 24px;
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    animation: chartFadeIn 0.8s ease 0.7s both;
}

.chart-container::before, .chart-container-full::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(255, 0, 199, 0.05), rgba(0, 255, 234, 0.05));
    opacity: 0;
    transition: opacity 0.5s ease;
    pointer-events: none;
}

.chart-container:hover::before, .chart-container-full:hover::before {
    opacity: 1;
}

.chart-container:hover, .chart-container-full:hover {
    border-color: rgba(0, 255, 234, 0.6);
    box-shadow: 
        0 0 35px rgba(0, 255, 234, 0.3),
        inset 0 0 30px rgba(0, 255, 234, 0.05);
    transform: translateY(-4px);
}

@keyframes chartFadeIn {
    from {
        opacity: 0;
        transform: translateY(30px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.chart-container-full {
    grid-column: 1 / -1;
}

.chart-title {
    font-family: Orbitron, sans-serif;
    font-size: 1.3rem;
    color: #fff;
    margin: 0 0 20px 0;
    padding-bottom: 12px;
    border-bottom: 1px solid rgba(255, 0, 199, 0.2);
}

.chart-container canvas, .chart-container-full canvas {
    max-height: 300px;
}

.chart-container-full canvas {
    max-height: 400px;
}

/* Sections améliorées */
.statistics-sections-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 24px;
    margin-bottom: 32px;
}

.statistics-section {
    background: rgba(0, 0, 0, 0.3);
    border: 2px solid rgba(255, 0, 199, 0.2);
    border-radius: 18px;
    padding: 24px;
    margin-bottom: 24px;
    transition: all 0.4s ease;
    position: relative;
    overflow: hidden;
    animation: sectionSlideIn 0.6s ease 0.9s both;
}

.statistics-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 0, 199, 0.1), transparent);
    transition: left 0.8s ease;
}

.statistics-section:hover::before {
    left: 100%;
}

.statistics-section:hover {
    border-color: rgba(0, 255, 234, 0.4);
    box-shadow: 0 0 30px rgba(0, 255, 234, 0.2);
    transform: translateX(4px);
}

@keyframes sectionSlideIn {
    from {
        opacity: 0;
        transform: translateX(-30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.statistics-section h3 {
    font-family: Orbitron, sans-serif;
    font-size: 1.4rem;
    color: #fff;
    margin: 0 0 20px 0;
    padding-bottom: 12px;
    border-bottom: 1px solid rgba(255, 0, 199, 0.2);
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-icon {
    font-size: 1.5rem;
}

.stat-list-enhanced {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.stat-item-enhanced {
    display: flex;
    flex-direction: column;
    gap: 8px;
    padding: 14px 18px;
    background: rgba(0, 0, 0, 0.5);
    border: 1px solid rgba(255, 0, 199, 0.2);
    border-radius: 14px;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    animation: itemFadeIn 0.5s ease both;
}

.stat-item-enhanced:nth-child(1) { animation-delay: 1s; }
.stat-item-enhanced:nth-child(2) { animation-delay: 1.1s; }
.stat-item-enhanced:nth-child(3) { animation-delay: 1.2s; }
.stat-item-enhanced:nth-child(4) { animation-delay: 1.3s; }
.stat-item-enhanced:nth-child(5) { animation-delay: 1.4s; }
.stat-item-enhanced:nth-child(6) { animation-delay: 1.5s; }
.stat-item-enhanced:nth-child(7) { animation-delay: 1.6s; }
.stat-item-enhanced:nth-child(8) { animation-delay: 1.7s; }

.stat-item-enhanced::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(0, 255, 234, 0.1), transparent);
    transition: left 0.6s ease;
}

.stat-item-enhanced:hover::before {
    left: 100%;
}

.stat-item-enhanced:hover {
    border-color: rgba(0, 255, 234, 0.6);
    background: rgba(0, 255, 234, 0.12);
    transform: translateX(6px) scale(1.02);
    box-shadow: 0 4px 20px rgba(0, 255, 234, 0.2);
}

@keyframes itemFadeIn {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.stat-item-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.stat-item-label {
    color: var(--text);
    font-weight: 500;
    font-size: 0.95rem;
}

.stat-item-percent {
    color: #00ffea;
    font-size: 0.85rem;
    font-weight: 600;
}

.stat-item-bar-container {
    height: 8px;
    background: rgba(0, 0, 0, 0.4);
    border-radius: 999px;
    overflow: hidden;
    position: relative;
}

.stat-item-bar {
    height: 100%;
    background: linear-gradient(90deg, #ff00c7, #00ffea, #ff00c7);
    background-size: 200% 100%;
    border-radius: 999px;
    transition: width 1.5s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 
        0 0 15px rgba(0, 255, 234, 0.5),
        inset 0 0 8px rgba(255, 255, 255, 0.2);
    animation: barShimmer 2s ease-in-out infinite;
    position: relative;
    overflow: hidden;
}

.stat-item-bar::after {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.5), transparent);
    animation: barShine 2s ease-in-out infinite;
}

@keyframes barShimmer {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

@keyframes barShine {
    0% { left: -100%; }
    50% { left: 100%; }
    100% { left: 100%; }
}

.stat-item-value {
    font-family: Orbitron, sans-serif;
    font-size: 1.1rem;
    font-weight: 700;
    color: #00ffea;
    text-align: right;
    background: linear-gradient(135deg, rgba(0, 255, 234, 0.15), rgba(0, 199, 255, 0.1));
    padding: 6px 14px;
    border-radius: 999px;
    border: 1px solid rgba(0, 255, 234, 0.4);
    align-self: flex-end;
    transition: all 0.3s ease;
    box-shadow: 0 0 10px rgba(0, 255, 234, 0.2);
    position: relative;
    overflow: hidden;
}

.stat-item-value::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.5s ease;
}

.stat-item-enhanced:hover .stat-item-value {
    border-color: rgba(0, 255, 234, 0.6);
    box-shadow: 0 0 20px rgba(0, 255, 234, 0.4);
    transform: scale(1.05);
}

.stat-item-enhanced:hover .stat-item-value::before {
    left: 100%;
}

/* Scrollbar personnalisée */
.statistics-modal-content::-webkit-scrollbar {
    width: 10px;
}

.statistics-modal-content::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.3);
    border-radius: 10px;
}

.statistics-modal-content::-webkit-scrollbar-thumb {
    background: linear-gradient(180deg, #ff00c7, #00ffea);
    border-radius: 10px;
}

.statistics-modal-content::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(180deg, #ff00c7, #00ffea);
    box-shadow: 0 0 10px rgba(0, 255, 234, 0.5);
}
</style>

  <script src="../../frontoffice/collaborations.js"></script>
  <script src="assets/js/bootstrap.bundle.min.js"></script>
  <script>
  // Sidebar toggle pour les statistiques
  document.addEventListener('DOMContentLoaded', function() {
    const statisticsBtnSidebar = document.getElementById('statisticsBtnSidebar');
    if (statisticsBtnSidebar) {
      statisticsBtnSidebar.addEventListener('click', function(e) {
        e.preventDefault();
        const statisticsBtn = document.getElementById('statisticsBtn');
        if (statisticsBtn) {
          statisticsBtn.click();
        }
      });
    }
  });
  
  </script>
  <script>
  // Smart Search and Filter System
  function updateFilters() {
    const category = document.getElementById('categoryFilter').value;
    const location = document.getElementById('locationFilter').value;
    const developer = document.getElementById('developerFilter').value;
    const search = document.getElementById('searchInput').value;
    const status = '<?= $statusFilter ?>';
    
    // Update hidden inputs
    document.getElementById('filter_category').value = category;
    document.getElementById('filter_location').value = location;
    document.getElementById('filter_developer').value = developer;
    
    // Build URL
    const params = new URLSearchParams();
    params.set('status', status);
    if (search) params.set('search', search);
    if (category) params.set('filter_category', category);
    if (location) params.set('filter_location', location);
    if (developer) params.set('filter_developer', developer);
    
    // Submit form or update URL
    window.location.href = '?' + params.toString();
  }
  
  // Real-time search with debounce
  let searchTimeout;
  document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
      searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const searchValue = this.value.trim();
        
        // Show search suggestions or perform live search
        if (searchValue.length >= 2) {
          searchTimeout = setTimeout(() => {
            // Optionally perform live search without page reload
            // For now, we'll just highlight matching terms
            highlightSearchTerms(searchValue);
          }, 500);
        } else {
          clearHighlights();
        }
      });
      
      // Submit on Enter
      searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
          e.preventDefault();
          document.getElementById('searchForm').submit();
        }
      });
    }
  });
  
  function highlightSearchTerms(term) {
    const rows = document.querySelectorAll('tbody tr');
    const terms = term.toLowerCase().split(' ').filter(t => t.length > 0);
    
    rows.forEach(row => {
      const text = row.textContent.toLowerCase();
      let matches = true;
      
      terms.forEach(t => {
        if (text.indexOf(t) === -1) {
          matches = false;
        }
      });
      
      if (matches) {
        row.style.opacity = '1';
        row.style.display = '';
      } else {
        row.style.opacity = '0.3';
      }
    });
  }
  
  function clearHighlights() {
    const rows = document.querySelectorAll('tbody tr');
    rows.forEach(row => {
      row.style.opacity = '1';
      row.style.display = '';
    });
  }
  </script>
  <script>
// Données des statistiques depuis PHP
const statsData = {
    categories: <?= json_encode(array_slice($stats['categories'], 0, 8, true)); ?>,
    plateformes: <?= json_encode(array_slice($stats['plateformes'], 0, 8, true)); ?>,
    byMonth: <?= json_encode($stats['byMonth']); ?>
};

// Fonction d'animation de comptage améliorée avec easing
function animateValue(element, start, end, duration) {
    let startTimestamp = null;
    const easeOutCubic = (t) => 1 - Math.pow(1 - t, 3);
    
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const elapsed = timestamp - startTimestamp;
        const progress = Math.min(elapsed / duration, 1);
        const easedProgress = easeOutCubic(progress);
        const current = Math.floor(easedProgress * (end - start) + start);
        element.textContent = current;
        
        // Ajouter un effet de pulse pendant l'animation
        if (progress < 1) {
            const scale = 1 + (Math.sin(progress * Math.PI * 4) * 0.05);
            element.style.transform = `scale(${scale})`;
            window.requestAnimationFrame(step);
        } else {
            element.textContent = end;
            element.style.transform = 'scale(1)';
        }
    };
    window.requestAnimationFrame(step);
}

// Animation des barres de progression
function animateProgressBars() {
    const progressBars = document.querySelectorAll('.progress-bar');
    progressBars.forEach((bar, index) => {
        const width = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.width = width;
        }, index * 100);
    });
}

// Animation des barres de statistiques
function animateStatBars() {
    const statBars = document.querySelectorAll('.stat-item-bar');
    statBars.forEach((bar, index) => {
        const width = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.width = width;
        }, index * 50);
    });
}

// Gestion de la modale des statistiques
document.addEventListener('DOMContentLoaded', function() {
    const statisticsBtn = document.getElementById('statisticsBtn');
    const statisticsModal = document.getElementById('statisticsModal');
    const closeBtn = document.getElementById('closeStatisticsBtn');
    let categoryChart = null;
    let platformChart = null;
    let monthlyChart = null;
    
    function openModal() {
        if (statisticsModal) {
            statisticsModal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            
            // Animer les valeurs avec délai séquentiel
            setTimeout(() => {
                document.querySelectorAll('.stat-card-value').forEach((el, index) => {
                    setTimeout(() => {
                        const target = parseInt(el.getAttribute('data-target')) || 0;
                        animateValue(el, 0, target, 1800);
                    }, index * 100);
                });
            }, 300);
            
            // Animer les barres de progression
            setTimeout(() => {
                animateProgressBars();
            }, 500);
            
            // Animer les barres de statistiques
            setTimeout(() => {
                animateStatBars();
            }, 800);
            
            // Initialiser les graphiques avec animation
            setTimeout(() => {
                initCharts();
            }, 400);
        }
    }
    
    function closeModal() {
        if (statisticsModal) {
            statisticsModal.style.display = 'none';
            document.body.style.overflow = '';
            
            // Détruire les graphiques
            if (categoryChart) categoryChart.destroy();
            if (platformChart) platformChart.destroy();
            if (monthlyChart) monthlyChart.destroy();
        }
    }
    
    function initCharts() {
        // Graphique en camembert pour les catégories
        const categoryCtx = document.getElementById('categoryChart');
        if (categoryCtx && statsData.categories) {
            const categoryLabels = Object.keys(statsData.categories);
            const categoryValues = Object.values(statsData.categories);
            
            categoryChart = new Chart(categoryCtx, {
                type: 'doughnut',
                data: {
                    labels: categoryLabels,
                    datasets: [{
                        data: categoryValues,
                        backgroundColor: [
                            'rgba(255, 0, 199, 0.8)',
                            'rgba(0, 255, 234, 0.8)',
                            'rgba(0, 200, 255, 0.8)',
                            'rgba(150, 0, 255, 0.8)',
                            'rgba(255, 200, 0, 0.8)',
                            'rgba(255, 100, 100, 0.8)',
                            'rgba(100, 255, 100, 0.8)',
                            'rgba(255, 150, 0, 0.8)'
                        ],
                        borderColor: [
                            '#ff00c7',
                            '#00ffea',
                            '#00c8ff',
                            '#9600ff',
                            '#ffc800',
                            '#ff6464',
                            '#64ff64',
                            '#ff9600'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#fff',
                                font: { size: 11 },
                                padding: 15
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#00ffea',
                            bodyColor: '#fff',
                            borderColor: '#ff00c7',
                            borderWidth: 1
                        }
                    },
                    animation: {
                        animateRotate: true,
                        duration: 2000,
                        easing: 'easeOutQuart'
                    },
                    hover: {
                        animationDuration: 300
                    }
                }
            });
        }
        
        // Graphique en barres pour les plateformes
        const platformCtx = document.getElementById('platformChart');
        if (platformCtx && statsData.plateformes) {
            const platformLabels = Object.keys(statsData.plateformes);
            const platformValues = Object.values(statsData.plateformes);
            
            platformChart = new Chart(platformCtx, {
                type: 'bar',
                data: {
                    labels: platformLabels,
                    datasets: [{
                        label: 'Projets',
                        data: platformValues,
                        backgroundColor: 'rgba(0, 255, 234, 0.6)',
                        borderColor: '#00ffea',
                        borderWidth: 2,
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#00ffea',
                            bodyColor: '#fff',
                            borderColor: '#ff00c7',
                            borderWidth: 1
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: '#fff',
                                stepSize: 1
                            },
                            grid: {
                                color: 'rgba(255, 0, 199, 0.2)'
                            }
                        },
                        x: {
                            ticks: {
                                color: '#fff'
                            },
                            grid: {
                                color: 'rgba(255, 0, 199, 0.2)'
                            }
                        }
                    },
                    animation: {
                        duration: 2000,
                        easing: 'easeOutQuart'
                    },
                    hover: {
                        animationDuration: 300
                    },
                    onHover: (event, activeElements) => {
                        if (activeElements.length > 0) {
                            event.native.target.style.cursor = 'pointer';
                        } else {
                            event.native.target.style.cursor = 'default';
                        }
                    }
                }
            });
        }
        
        // Graphique linéaire pour l'évolution mensuelle
        const monthlyCtx = document.getElementById('monthlyChart');
        if (monthlyCtx && statsData.byMonth) {
            const monthLabels = Object.keys(statsData.byMonth).map(month => {
                const [year, m] = month.split('-');
                const months = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
                return months[parseInt(m) - 1] + ' ' + year;
            });
            const monthValues = Object.values(statsData.byMonth);
            
            monthlyChart = new Chart(monthlyCtx, {
                type: 'line',
                data: {
                    labels: monthLabels,
                    datasets: [{
                        label: 'Projets créés',
                        data: monthValues,
                        borderColor: '#00ffea',
                        backgroundColor: 'rgba(0, 255, 234, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#ff00c7',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            labels: {
                                color: '#fff',
                                font: { size: 12 }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#00ffea',
                            bodyColor: '#fff',
                            borderColor: '#ff00c7',
                            borderWidth: 1
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: '#fff',
                                stepSize: 1
                            },
                            grid: {
                                color: 'rgba(255, 0, 199, 0.2)'
                            }
                        },
                        x: {
                            ticks: {
                                color: '#fff'
                            },
                            grid: {
                                color: 'rgba(255, 0, 199, 0.2)'
                            }
                        }
                    },
                    animation: {
                        duration: 2500,
                        easing: 'easeOutQuart'
                    },
                    hover: {
                        animationDuration: 300
                    },
                    onHover: (event, activeElements) => {
                        if (activeElements.length > 0) {
                            event.native.target.style.cursor = 'pointer';
                        } else {
                            event.native.target.style.cursor = 'default';
                        }
                    }
                }
            });
        }
    }
    
    if (statisticsBtn) {
        statisticsBtn.addEventListener('click', function(e) {
            e.preventDefault();
            openModal();
        });
    }
    
    if (closeBtn) {
        closeBtn.addEventListener('click', closeModal);
    }
    
    // Fermer en cliquant en dehors de la modale
    if (statisticsModal) {
        statisticsModal.addEventListener('click', function(e) {
            if (e.target === statisticsModal) {
                closeModal();
            }
        });
    }
    
    // Fermer avec la touche Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && statisticsModal && statisticsModal.style.display === 'flex') {
            closeModal();
        }
    });
});
</script>
</body>
</html>
