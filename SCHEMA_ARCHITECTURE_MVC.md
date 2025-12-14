# 🏗️ SCHÉMA DE L'ARCHITECTURE MVC - GAMEHUB PRO

## 📐 VUE GLOBALE DE L'ARCHITECTURE MVC

```
┌─────────────────────────────────────────────────────────────────┐
│                         UTILISATEUR                              │
│                    (Navigateur Web)                              │
└───────────────────────────┬─────────────────────────────────────┘
                            │
                            │ Requêtes HTTP (GET/POST)
                            │
        ┌───────────────────┴───────────────────┐
        │                                       │
        ▼                                       ▼
┌───────────────┐                      ┌───────────────┐
│   FRONTOFFICE │                      │   BACKOFFICE  │
│  (Public)     │                      │  (Admin)      │
└───────┬───────┘                      └───────┬───────┘
        │                                       │
        └───────────────┬───────────────────────┘
                        │
                        ▼
        ┌───────────────────────────────┐
        │          VIEW (V)              │
        │   Interface Utilisateur        │
        │   - index.php                  │
        │   - detail.php                 │
        │   - collaborations.php         │
        │   - room_collab.php            │
        │   - admindashboard.php         │
        └───────────────┬────────────────┘
                        │
                        │ Appelle
                        ▼
        ┌───────────────────────────────┐
        │       CONTROLLER (C)           │
        │   Logique Métier               │
        │   - ProjectController         │
        │   - CollabProjectController   │
        │   - CollabMessageController   │
        │   - MessageModerationController│
        └───────────────┬────────────────┘
                        │
                        │ Utilise
                        ▼
        ┌───────────────────────────────┐
        │         MODEL (M)              │
        │   Données / Entités            │
        │   - Project                    │
        │   - CollabProject              │
        │   - CollabMessage              │
        │   - CollabMember               │
        └───────────────┬────────────────┘
                        │
                        │ Accède à
                        ▼
        ┌───────────────────────────────┐
        │      CONFIG / DATABASE         │
        │   - config.php                │
        │   - MySQL (bdgamehub)         │
        └───────────────────────────────┘
```

---

## 🔄 FLUX DE DONNÉES MVC

### Exemple 1 : Affichage de la liste des jeux

```
┌─────────────┐
│  UTILISATEUR │
│  Visite      │
│  index.php   │
└──────┬───────┘
       │
       │ 1. Requête HTTP GET
       ▼
┌─────────────────────────────────────┐
│         VIEW (index.php)             │
│  <?php                               │
│  $projectC = new ProjectController();│
│  $projects = $projectC->listProjects();│
│  ?>                                  │
│  <html>...affichage...</html>        │
└──────┬───────────────────────────────┘
       │
       │ 2. Appel du Controller
       ▼
┌─────────────────────────────────────┐
│   CONTROLLER                         │
│   (ProjectController.php)           │
│                                      │
│   public function listProjects() {   │
│     $db = config::getConnexion();   │
│     $sql = "SELECT * FROM projects";│
│     return $db->query($sql);        │
│   }                                  │
└──────┬───────────────────────────────┘
       │
       │ 3. Accès à la base de données
       ▼
┌─────────────────────────────────────┐
│         DATABASE                    │
│   (MySQL - bdgamehub)               │
│   Table: projects                   │
└──────┬───────────────────────────────┘
       │
       │ 4. Retour des données
       │    (Array de projets)
       ▼
┌─────────────────────────────────────┐
│   CONTROLLER                        │
│   Retourne $projects                │
└──────┬───────────────────────────────┘
       │
       │ 5. Retour des données
       ▼
┌─────────────────────────────────────┐
│         VIEW (index.php)             │
│   <?php foreach($projects as $p): ?> │
│     <div>Jeu: <?= $p['nom'] ?></div>│
│   <?php endforeach; ?>              │
└──────┬───────────────────────────────┘
       │
       │ 6. HTML rendu
       ▼
┌─────────────┐
│  UTILISATEUR │
│  Voit la     │
│  liste HTML  │
└─────────────┘
```

---

### Exemple 2 : Création d'un projet collaboratif

```
┌─────────────┐
│  UTILISATEUR │
│  Remplit     │
│  formulaire  │
└──────┬───────┘
       │
       │ 1. POST /create_collab.php
       │    {titre, description, max_membres}
       ▼
┌─────────────────────────────────────┐
│         VIEW                        │
│   (create_collab.php)               │
│   <?php                             │
│   if ($_POST) {                     │
│     $controller = new                │
│       CollabProjectController();    │
│     $collab = new CollabProject(...);│
│     $controller->create($collab);     │
│   }                                  │
│   ?>                                │
└──────┬───────────────────────────────┘
       │
       │ 2. Appel du Controller
       ▼
┌─────────────────────────────────────┐
│   CONTROLLER                        │
│   (CollabProjectController.php)     │
│                                     │
│   public function create($collab) { │
│     $sql = "INSERT INTO ...";      │
│     $stmt->execute([                │
│       $collab->getTitre(),         │
│       $collab->getDescription()     │
│     ]);                             │
│   }                                 │
└──────┬───────────────────────────────┘
       │
       │ 3. Utilise le Model
       ▼
┌─────────────────────────────────────┐
│         MODEL                       │
│   (CollabProject.php)              │
│                                     │
│   class CollabProject {             │
│     private $titre;                 │
│     private $description;           │
│     public function getTitre() {...}│
│   }                                 │
└──────┬───────────────────────────────┘
       │
       │ 4. Insertion en DB
       ▼
┌─────────────────────────────────────┐
│         DATABASE                    │
│   INSERT INTO collab_project        │
└──────┬───────────────────────────────┘
       │
       │ 5. Retour ID du projet créé
       ▼
┌─────────────────────────────────────┐
│   CONTROLLER                        │
│   return $newId;                    │
└──────┬───────────────────────────────┘
       │
       │ 6. Redirection
       ▼
┌─────────────────────────────────────┐
│         VIEW                        │
│   header("Location: view_collab.php│
│            ?id=" . $newId);         │
└──────┬───────────────────────────────┘
       │
       │ 7. Affichage du nouveau projet
       ▼
┌─────────────┐
│  UTILISATEUR │
│  Voit le     │
│  projet créé │
└─────────────┘
```

---

## 📁 STRUCTURE DÉTAILLÉE DES DOSSIERS

```
gamehubprjt/
│
├── 📁 config/                          [CONFIGURATION]
│   └── config.php                      → Connexion à la base de données
│
├── 📁 controller/                      [CONTROLLER - Logique Métier]
│   │
│   ├── ProjectController.php           → Gestion des jeux (CRUD)
│   │   ├── listProjects()              → Liste tous les jeux
│   │   ├── addProject()                 → Ajoute un jeu
│   │   ├── updateProject()              → Modifie un jeu
│   │   ├── deleteProject()              → Supprime un jeu
│   │   └── getProjectById()             → Récupère un jeu
│   │
│   ├── EventController.php             → Gestion des événements
│   │
│   └── 📁 controllercollab/             → Controllers des collaborations
│       ├── CollabProjectController.php → Gestion projets collaboratifs
│       │   ├── create()                → Crée une collaboration
│       │   ├── update()                → Modifie une collaboration
│       │   ├── delete()                → Supprime une collaboration
│       │   └── getById()               → Récupère une collaboration
│       │
│       ├── CollabMemberController.php  → Gestion des membres
│       │   ├── add()                    → Ajoute un membre
│       │   ├── delete()                 → Supprime un membre
│       │   ├── getMembers()             → Liste les membres
│       │   └── isMember()               → Vérifie si membre
│       │
│       ├── CollabMessageController.php → Gestion des messages
│       │   ├── send()                   → Envoie un message
│       │   ├── getMessages()            → Récupère les messages
│       │   ├── delete()                 → Supprime un message
│       │   └── updateMessage()           → Modifie un message
│       │
│       ├── CollabTaskController.php     → Gestion des tâches
│       │   ├── addTask()                 → Ajoute une tâche
│       │   ├── getTasks()                → Liste les tâches
│       │   └── markDone()                → Marque comme fait
│       │
│       └── MessageModerationController.php → Modération automatique
│           ├── moderateMessage()        → Modère un message
│           ├── level1Filter()          → Filtre niveau 1
│           ├── level2AIModeration()     → Filtre niveau 2 (IA)
│           └── logModeration()          → Enregistre les logs
│
├── 📁 model/                           [MODEL - Entités/Données]
│   │
│   ├── Project.php                     → Modèle d'un jeu
│   │   ├── private $id, $nom, $developpeur...
│   │   ├── __construct()                → Constructeur
│   │   ├── getters (getId(), getNom()...)
│   │   └── setters (setNom()...)
│   │
│   └── 📁 collab/                      → Modèles des collaborations
│       ├── CollabProject.php            → Modèle projet collaboratif
│       │   ├── private $id, $owner_id, $titre...
│       │   └── getters/setters
│       │
│       ├── CollabMember.php             → Modèle membre
│       ├── CollabMessage.php            → Modèle message
│       ├── CollabTask.php               → Modèle tâche
│       └── CollabSkillRequired.php      → Modèle compétence requise
│
└── 📁 view/                            [VIEW - Interface Utilisateur]
    │
    ├── 📁 frontoffice/                 → Site public (visiteurs)
    │   │
    │   ├── index.php                    → Page d'accueil
    │   │   └── Affiche la liste des jeux
    │   │
    │   ├── detail.php                   → Détail d'un jeu
    │   │   └── Affiche toutes les infos d'un jeu
    │   │
    │   ├── addgame.html                 → Formulaire soumission jeu
    │   ├── addgame.php                  → Traitement soumission
    │   │
    │   ├── collaborations.php            → Liste des collaborations
    │   │   └── Affiche les projets collaboratifs ouverts
    │   │
    │   ├── 📁 control/                  → Traitement des formulaires
    │   │   └── add_game.php              → Traite l'ajout de jeu
    │   │
    │   └── 📁 assests/                  → Ressources statiques
    │       └── (images, logos...)
    │
    └── 📁 backoffice/                  → Administration
        │
        ├── 📁 projectscrud/             → CRUD des projets
        │   ├── admindashboard.php       → Dashboard admin
        │   ├── projectlist.php           → Liste des projets
        │   ├── showproject.php           → Affiche un projet
        │   ├── addProject.php            → Formulaire ajout
        │   ├── updateproject.php         → Formulaire modification
        │   └── deleteproject.php        → Suppression
        │
        ├── 📁 collabcrud/                → CRUD des collaborations
        │   ├── create_collab.php         → Créer collaboration
        │   ├── view_collab.php           → Voir collaboration
        │   ├── room_collab.php           → Room de chat
        │   ├── update_collab.php         → Modifier collaboration
        │   ├── delete_collab.php         → Supprimer collaboration
        │   │
        │   ├── send_message.php          → Envoyer message
        │   ├── get_messages.php          → Récupérer messages
        │   ├── send_voice_message.php    → Message vocal
        │   │
        │   ├── task_add.php              → Ajouter tâche
        │   ├── task_done.php              → Marquer tâche faite
        │   ├── task_delete.php            → Supprimer tâche
        │   │
        │   ├── moderation_dashboard.php  → Dashboard modération
        │   │
        │   └── chatbot.html              → Interface chatbot
        │   └── chatbot_api.php           → API chatbot
        │
        ├── avatar_shop.php                → Création d'avatar
        └── save_avatar.php                → Sauvegarde avatar
```

---

## 🔗 INTERACTIONS ENTRE LES COMPOSANTS

### Schéma des dépendances

```
                    ┌─────────────────┐
                    │   DATABASE      │
                    │   (MySQL)       │
                    └────────┬────────┘
                             │
                             │ Accès via
                             ▼
                    ┌─────────────────┐
                    │   CONFIG       │
                    │  config.php    │
                    │  (PDO)         │
                    └────────┬────────┘
                             │
                             │ Utilisé par
                             ▼
        ┌────────────────────┴────────────────────┐
        │                                         │
        ▼                                         ▼
┌───────────────┐                        ┌───────────────┐
│  CONTROLLER   │                        │    MODEL      │
│               │                        │               │
│  - Utilise    │◄──────────────────────│  - Entités    │
│    Config     │   Instancie            │  - Données    │
│  - Appelle     │                       │  - Getters/  │
│    Models     │                       │    Setters    │
│  - Retourne    │                       └───────────────┘
│    données    │
└───────┬───────┘
        │
        │ Appelé par
        ▼
┌───────────────┐
│     VIEW      │
│               │
│  - HTML       │
│  - PHP        │
│  - CSS/JS     │
│  - Appelle    │
│    Controllers│
└───────┬───────┘
        │
        │ Rendu pour
        ▼
┌───────────────┐
│  UTILISATEUR  │
└───────────────┘
```

---

## 📊 EXEMPLE CONCRET : FLUX COMPLET

### Scénario : Envoi d'un message dans une collaboration

```
┌─────────────────────────────────────────────────────────────┐
│                    UTILISATEUR                               │
│  Tape un message dans room_collab.php                        │
│  Clique sur "Envoyer"                                        │
└────────────────────┬────────────────────────────────────────┘
                     │
                     │ POST /send_message.php
                     │ {collab_id: 5, message: "Bonjour!"}
                     ▼
┌─────────────────────────────────────────────────────────────┐
│                    VIEW                                      │
│  send_message.php                                            │
│  ┌──────────────────────────────────────────────────────┐  │
│  │ require_once CollabMessageController.php;            │  │
│  │ require_once MessageModerationController.php;        │  │
│  │                                                       │  │
│  │ $message = $_POST['message'];                        │  │
│  │ $moderation = new MessageModerationController();     │  │
│  │ $result = $moderation->moderateMessage($message);    │  │
│  │                                                       │  │
│  │ if ($result['blocked']) {                            │  │
│  │   // Message bloqué                                  │  │
│  │ } else {                                             │  │
│  │   $msg = new CollabMessage(...);                     │  │
│  │   $controller = new CollabMessageController();      │  │
│  │   $controller->send($msg);                           │  │
│  │ }                                                     │  │
│  └──────────────────────────────────────────────────────┘  │
└────────────────────┬────────────────────────────────────────┘
                     │
                     │ 1. Appel modération
                     ▼
┌─────────────────────────────────────────────────────────────┐
│              CONTROLLER                                     │
│  MessageModerationController.php                            │
│  ┌──────────────────────────────────────────────────────┐  │
│  │ public function moderateMessage($message) {          │  │
│  │   // Niveau 1 : Filtre mots interdits                │  │
│  │   $level1 = $this->level1Filter($message);          │  │
│  │   if ($level1['blocked']) return $level1;          │  │
│  │                                                       │  │
│  │   // Niveau 2 : IA                                   │  │
│  │   $level2 = $this->level2AIModeration($message);    │  │
│  │   return $level2;                                    │  │
│  │ }                                                     │  │
│  └──────────────────────────────────────────────────────┘  │
└────────────────────┬────────────────────────────────────────┘
                     │
                     │ 2. Retour {approved: true}
                     ▼
┌─────────────────────────────────────────────────────────────┐
│                    VIEW                                      │
│  send_message.php (suite)                                    │
│  ┌──────────────────────────────────────────────────────┐  │
│  │ // Message approuvé                                  │  │
│  │ $msg = new CollabMessage(                             │  │
│  │   null,                                               │  │
│  │   $collab_id,                                        │  │
│  │   $user_id,                                          │  │
│  │   $message,                                          │  │
│  │   null                                               │  │
│  │ );                                                    │  │
│  │                                                       │  │
│  │ $controller = new CollabMessageController();         │  │
│  │ $controller->send($msg);                             │  │
│  └──────────────────────────────────────────────────────┘  │
└────────────────────┬────────────────────────────────────────┘
                     │
                     │ 3. Appel controller
                     ▼
┌─────────────────────────────────────────────────────────────┐
│              CONTROLLER                                     │
│  CollabMessageController.php                                │
│  ┌──────────────────────────────────────────────────────┐  │
│  │ public function send(CollabMessage $msg) {           │  │
│  │   $sql = "INSERT INTO collab_messages                 │  │
│  │           (collab_id, user_id, message)               │  │
│  │           VALUES (?, ?, ?)";                          │  │
│  │   $stmt = $this->db->prepare($sql);                  │  │
│  │   return $stmt->execute([                              │  │
│  │     $msg->getCollabId(),                              │  │
│  │     $msg->getUserId(),                               │  │
│  │     $msg->getMessage()                               │  │
│  │   ]);                                                  │  │
│  │ }                                                       │  │
│  └──────────────────────────────────────────────────────┘  │
└────────────────────┬────────────────────────────────────────┘
                     │
                     │ 4. Utilise le Model
                     ▼
┌─────────────────────────────────────────────────────────────┐
│                    MODEL                                     │
│  CollabMessage.php                                           │
│  ┌──────────────────────────────────────────────────────┐  │
│  │ class CollabMessage {                                 │  │
│  │   private $id, $collab_id, $user_id, $message;       │  │
│  │                                                       │  │
│  │   public function getCollabId() {                     │  │
│  │     return $this->collab_id;                          │  │
│  │   }                                                    │  │
│  │   // ... autres getters                              │  │
│  │ }                                                       │  │
│  └──────────────────────────────────────────────────────┘  │
└────────────────────┬────────────────────────────────────────┘
                     │
                     │ 5. Insertion en DB
                     ▼
┌─────────────────────────────────────────────────────────────┐
│                    DATABASE                                 │
│  MySQL - Table: collab_messages                              │
│  ┌──────────────────────────────────────────────────────┐  │
│  │ INSERT INTO collab_messages                          │  │
│  │ (collab_id, user_id, message, date_message)          │  │
│  │ VALUES (5, 12, 'Bonjour!', NOW());                  │  │
│  └──────────────────────────────────────────────────────┘  │
└────────────────────┬────────────────────────────────────────┘
                     │
                     │ 6. Retour succès
                     ▼
┌─────────────────────────────────────────────────────────────┐
│                    VIEW                                      │
│  send_message.php (fin)                                      │
│  ┌──────────────────────────────────────────────────────┐  │
│  │ header("Location: room_collab.php?id=5&success=1");  │  │
│  └──────────────────────────────────────────────────────┘  │
└────────────────────┬────────────────────────────────────────┘
                     │
                     │ 7. Redirection
                     ▼
┌─────────────────────────────────────────────────────────────┐
│                    VIEW                                      │
│  room_collab.php                                             │
│  ┌──────────────────────────────────────────────────────┐  │
│  │ // Rafraîchit les messages                           │  │
│  │ $messages = $messageController->getMessages(5);      │  │
│  │ // Affiche le nouveau message                        │  │
│  └──────────────────────────────────────────────────────┘  │
└────────────────────┬────────────────────────────────────────┘
                     │
                     │ 8. HTML rendu
                     ▼
┌─────────────────────────────────────────────────────────────┐
│                    UTILISATEUR                               │
│  Voit son message affiché dans le chat                      │
└─────────────────────────────────────────────────────────────┘
```

---

## 🎯 RÉSUMÉ DES RESPONSABILITÉS

### 📄 VIEW (Vue)
**Rôle :** Présentation et interaction utilisateur
- ✅ Affiche les données (HTML)
- ✅ Reçoit les entrées utilisateur (formulaires)
- ✅ Appelle les Controllers
- ✅ Gère l'affichage (CSS, JavaScript)
- ❌ Ne contient PAS de logique métier
- ❌ Ne contient PAS d'accès direct à la base de données

**Exemples :**
- `index.php` → Affiche la liste des jeux
- `room_collab.php` → Interface de chat
- `create_collab.php` → Formulaire de création

---

### 🎮 CONTROLLER (Contrôleur)
**Rôle :** Logique métier et coordination
- ✅ Reçoit les requêtes de la View
- ✅ Appelle les Models pour les données
- ✅ Traite la logique métier
- ✅ Valide les données
- ✅ Retourne les résultats à la View
- ❌ Ne contient PAS de HTML
- ❌ Ne contient PAS de requêtes SQL directes (sauf exceptions)

**Exemples :**
- `ProjectController::listProjects()` → Récupère tous les jeux
- `CollabMessageController::send()` → Envoie un message
- `MessageModerationController::moderateMessage()` → Modère un message

---

### 📦 MODEL (Modèle)
**Rôle :** Représentation des données
- ✅ Définit la structure des entités
- ✅ Contient les getters/setters
- ✅ Représente les données de la base
- ❌ Ne contient PAS de logique métier complexe
- ❌ Ne contient PAS de HTML

**Exemples :**
- `Project` → Représente un jeu vidéo
- `CollabProject` → Représente un projet collaboratif
- `CollabMessage` → Représente un message

---

### ⚙️ CONFIG
**Rôle :** Configuration et accès aux ressources
- ✅ Connexion à la base de données
- ✅ Configuration de l'application
- ✅ Utilitaires partagés

**Exemple :**
- `config.php` → Singleton de connexion PDO

---

## 🔄 PRINCIPE DE SÉPARATION

```
┌─────────────────────────────────────────────────────────┐
│                    SÉPARATION DES CONCERNS               │
└─────────────────────────────────────────────────────────┘

VIEW          →  "QUOI afficher ?"
              →  Interface utilisateur
              →  Présentation

CONTROLLER    →  "COMMENT traiter ?"
              →  Logique métier
              →  Coordination

MODEL         →  "QUOI sont les données ?"
              →  Structure des données
              →  Entités

CONFIG        →  "OÙ sont les ressources ?"
              →  Configuration
              →  Accès aux services
```

---

## 📈 AVANTAGES DE CETTE ARCHITECTURE

1. **Séparation des responsabilités**
   - Chaque composant a un rôle clair
   - Facilite la maintenance

2. **Réutilisabilité**
   - Les Controllers peuvent être utilisés par plusieurs Views
   - Les Models sont indépendants

3. **Testabilité**
   - Chaque composant peut être testé séparément
   - Facilite les tests unitaires

4. **Évolutivité**
   - Facile d'ajouter de nouvelles fonctionnalités
   - Modifications isolées

5. **Collaboration**
   - Plusieurs développeurs peuvent travailler en parallèle
   - Frontend/Backend séparés

---

## 🎨 DIAGRAMME DE CLASSES SIMPLIFIÉ

```
┌─────────────────────┐
│   ProjectController │
├─────────────────────┤
│ +listProjects()      │
│ +addProject()        │
│ +updateProject()     │
│ +deleteProject()     │
└──────────┬───────────┘
           │ utilise
           ▼
┌─────────────────────┐
│      Project        │
├─────────────────────┤
│ -id: int            │
│ -nom: string        │
│ -developpeur: string│
│ +getId(): int       │
│ +getNom(): string   │
└─────────────────────┘

┌─────────────────────┐
│ CollabProjectCtrl   │
├─────────────────────┤
│ +create()           │
│ +update()           │
│ +delete()           │
└──────────┬───────────┘
           │ utilise
           ▼
┌─────────────────────┐
│   CollabProject     │
├─────────────────────┤
│ -id: int            │
│ -owner_id: int      │
│ -titre: string      │
│ +getTitre(): string │
└─────────────────────┘
```

---

*Schéma créé pour documenter l'architecture MVC du projet GameHub Pro*



