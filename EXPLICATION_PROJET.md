# 🎮 EXPLICATION DU PROJET GAMEHUB PRO

## 📋 QU'EST-CE QUE GAMEHUB PRO ?

**GameHub Pro** est une **plateforme web complète** dédiée aux **jeux vidéo indépendants**. C'est un site qui permet de :
- **Découvrir** des jeux créés par des développeurs indépendants
- **Soumettre** ses propres jeux pour qu'ils soient publiés
- **Collaborer** en équipe sur des projets de développement de jeux
- **Communiquer** via un système de chat avancé avec modération automatique

---

## 🎯 UTILITÉ PRINCIPALE DU SITE

### Pour les Joueurs 👾
- **Découvrir** de nouveaux jeux indépendants
- **Explorer** une bibliothèque de jeux par catégories
- **Télécharger** des jeux gratuits
- **Voir** les détails complets (trailers, screenshots, descriptions)

### Pour les Développeurs Indépendants 💻
- **Publier** leurs jeux facilement via un formulaire
- **Obtenir de la visibilité** pour leurs créations
- **Collaborer** avec d'autres développeurs sur des projets
- **Gérer** leurs projets de manière professionnelle

### Pour les Administrateurs 🔧
- **Modérer** les jeux soumis (approuver/rejeter)
- **Gérer** le contenu de la plateforme
- **Surveiller** les collaborations
- **Contrôler** la qualité des publications

---

## 🏗️ ARCHITECTURE DU CODE

Le projet utilise l'**architecture MVC** (Model-View-Controller) :

```
📁 gamehubprjt/
├── 📁 config/          → Configuration (connexion base de données)
├── 📁 controller/      → Logique métier (traitement des données)
├── 📁 model/           → Modèles de données (classes PHP)
├── 📁 view/            → Interface utilisateur (HTML/PHP)
│   ├── frontoffice/    → Site public (visiteurs)
│   └── backoffice/     → Administration et collaborations
└── 📁 uploads/         → Fichiers uploadés (images, audio, PDF)
```

---

## 🔧 FONCTIONNALITÉS DÉTAILLÉES

### 1. 🎮 GESTION DES JEUX (CRUD Complet)

#### **CREATE - Création de jeux**
**Fichiers concernés :**
- `view/frontoffice/addgame.html` - Formulaire de soumission
- `view/frontoffice/control/add_game.php` - Traitement de la soumission
- `controller/ProjectController.php` - Logique d'ajout

**Ce que fait le code :**
```php
// L'utilisateur remplit un formulaire avec :
- Nom du jeu
- Développeur
- Catégorie
- Description
- Image principale
- Screenshots
- Trailer (lien YouTube)
- Lien de téléchargement
- Plateformes supportées
- Tags

// Le système :
1. Valide les données (côté client et serveur)
2. Upload les images dans le dossier uploads/
3. Crée un projet avec statut "en_attente"
4. Attend l'approbation de l'administrateur
```

**Utilité :** Permet à n'importe quel développeur de soumettre son jeu facilement.

---

#### **READ - Affichage des jeux**
**Fichiers concernés :**
- `view/frontoffice/index.php` - Page d'accueil avec liste des jeux
- `view/frontoffice/detail.php` - Page de détail d'un jeu
- `controller/ProjectController.php` - Récupération des données

**Ce que fait le code :**
```php
// Sur la page d'accueil :
1. Récupère tous les jeux avec statut "publie"
2. Affiche les 5 jeux les plus récents en vedette
3. Affiche une grille de cartes de jeux avec :
   - Image
   - Nom
   - Catégorie
   - Âge recommandé
   - Lieu (pays du développeur)
   - Date de création

// Sur la page de détail :
1. Récupère toutes les informations du jeu
2. Affiche les screenshots
3. Intègre le trailer YouTube
4. Affiche le lien de téléchargement
```

**Utilité :** Les visiteurs peuvent découvrir et explorer les jeux disponibles.

---

#### **UPDATE - Modification de jeux**
**Fichiers concernés :**
- `view/backoffice/projectscrud/updateproject.php` - Formulaire de modification
- `controller/ProjectController.php` - Logique de mise à jour

**Ce que fait le code :**
```php
// L'administrateur peut :
1. Modifier les informations d'un jeu
2. Changer l'image
3. Mettre à jour la description
4. Modifier les tags et plateformes
```

**Utilité :** Permet de corriger ou améliorer les informations des jeux.

---

#### **DELETE - Suppression de jeux**
**Fichiers concernés :**
- `view/backoffice/projectscrud/deleteproject.php` - Suppression
- `controller/ProjectController.php` - Logique de suppression

**Ce que fait le code :**
```php
// Supprime un jeu de la base de données
// Optionnellement : supprime aussi les fichiers associés
```

**Utilité :** Permet de retirer des jeux obsolètes ou inappropriés.

---

### 2. 🤝 SYSTÈME DE COLLABORATION

C'est l'une des **fonctionnalités les plus avancées** du projet !

#### **Création de projets collaboratifs**
**Fichiers concernés :**
- `view/backoffice/collabcrud/create_collab.php` - Formulaire de création
- `controller/controllercollab/CollabProjectController.php` - Logique

**Ce que fait le code :**
```php
// Un utilisateur peut créer un projet collaboratif :
1. Définit un titre et une description
2. Choisit le nombre maximum de membres (1-20)
3. Upload une image de présentation
4. Le système crée automatiquement :
   - Un projet avec statut "ouvert"
   - Le créateur devient automatiquement "owner" (propriétaire)
   - Le projet apparaît dans la liste des collaborations ouvertes
```

**Utilité :** Permet à des développeurs de se regrouper pour travailler ensemble sur un jeu.

---

#### **Rejoindre une collaboration**
**Fichiers concernés :**
- `view/frontoffice/collaborations.php` - Liste des collaborations
- `view/backoffice/collabcrud/join_collab.php` - Traitement de l'adhésion

**Ce que fait le code :**
```php
// Un utilisateur peut :
1. Voir toutes les collaborations "ouvertes"
2. Voir le nombre de membres actuels / maximum
3. Cliquer sur "Rejoindre" pour devenir membre
4. Le système vérifie :
   - Si le projet n'est pas déjà plein
   - Si l'utilisateur n'est pas déjà membre
5. Si tout est OK, l'utilisateur devient "membre"
6. Si le projet est maintenant complet, redirection vers la "room"
```

**Utilité :** Permet de trouver et rejoindre des équipes de développement.

---

#### **Gestion des membres**
**Fichiers concernés :**
- `controller/controllercollab/CollabMemberController.php` - Gestion des membres
- `view/backoffice/collabcrud/view_collab.php` - Affichage des membres

**Ce que fait le code :**
```php
// Système de rôles :
- "owner" (propriétaire) : Peut tout faire (modifier, supprimer, gérer membres)
- "moderateur" : Peut modérer les messages
- "membre" : Peut participer au chat et aux tâches

// Le propriétaire peut :
1. Voir tous les membres avec leurs avatars
2. Supprimer des membres (sauf lui-même)
3. Changer les rôles
4. Voir qui a rejoint quand
```

**Utilité :** Permet une gestion organisée des équipes de collaboration.

---

### 3. 💬 SYSTÈME DE CHAT EN TEMPS RÉEL

#### **Envoi de messages**
**Fichiers concernés :**
- `view/backoffice/collabcrud/room_collab.php` - Interface de chat
- `view/backoffice/collabcrud/send_message.php` - Traitement des messages
- `controller/controllercollab/CollabMessageController.php` - Logique

**Ce que fait le code :**
```php
// Dans la "room" de collaboration :
1. Affichage de tous les messages précédents
2. Zone de saisie pour nouveaux messages
3. Support de :
   - Messages texte
   - Emojis (via un sélecteur)
   - Fichiers (images, PDF)
   - Messages vocaux (enregistrement audio)
4. Affichage en temps réel (rafraîchissement automatique)
5. Affichage de l'avatar de chaque utilisateur
6. Horodatage des messages
```

**Utilité :** Permet aux membres d'une collaboration de communiquer efficacement.

---

#### **Messages vocaux**
**Fichiers concernés :**
- `view/backoffice/collabcrud/send_voice_message.php` - Upload audio
- `view/backoffice/collabcrud/get_audio.php` - Lecture audio

**Ce que fait le code :**
```php
// Fonctionnalité avancée :
1. L'utilisateur enregistre un message vocal (format WebM)
2. Le fichier est uploadé dans uploads/voices/
3. Le message est sauvegardé avec le chemin du fichier audio
4. Les autres membres peuvent écouter le message directement dans le chat
5. Affichage d'un indicateur de durée
```

**Utilité :** Permet une communication plus naturelle et rapide que le texte.

---

### 4. 🛡️ SYSTÈME DE MODÉRATION AUTOMATIQUE

C'est une **fonctionnalité très sophistiquée** !

**Fichiers concernés :**
- `controller/controllercollab/MessageModerationController.php` - Moteur de modération

**Ce que fait le code :**
```php
// Système à 2 niveaux :

// NIVEAU 1 : Filtre simple (mots interdits)
1. Liste de mots interdits (français, anglais, québécois)
2. Détection de leetspeak (c0n4rd, f*ck, etc.)
3. Normalisation du texte (enlève caractères spéciaux)
4. Si mot interdit trouvé → Message BLOQUÉ

// NIVEAU 2 : IA de modération
1. Analyse sémantique du message
2. Détection de :
   - Insultes et grossièretés
   - Menaces personnelles
   - Contenu discriminatoire
   - Spam et fraudes
   - Contenu sexuel explicite
3. Calcul de scores de dangerosité (0.0 à 1.0)
4. Si score > seuil → Message BLOQUÉ

// Résultat :
- Message approuvé → Envoyé normalement
- Message bloqué → Supprimé, fichier uploadé supprimé, redirection avec erreur
- Log de modération → Enregistré pour statistiques
```

**Utilité :** Maintient un environnement sain et professionnel dans les collaborations.

---

#### **Dashboard de modération**
**Fichiers concernés :**
- `view/backoffice/collabcrud/moderation_dashboard.php` - Interface admin

**Ce que fait le code :**
```php
// L'administrateur peut :
1. Voir tous les messages modérés
2. Voir les statistiques (nombre de messages bloqués, par niveau)
3. Réviser manuellement les messages bloqués
4. Approuver ou rejeter définitivement
5. Voir les scores de modération IA
```

**Utilité :** Permet un contrôle humain sur la modération automatique.

---

### 5. 🎨 SYSTÈME D'AVATARS PERSONNALISABLES

**Fichiers concernés :**
- `view/backoffice/avatar_shop.php` - Interface de création
- `view/backoffice/avatar_cartoon_renderer.js` - Rendu des avatars
- `view/backoffice/save_avatar.php` - Sauvegarde

**Ce que fait le code :**
```php
// Création d'avatar personnalisé :
1. L'utilisateur choisit :
   - Type de cheveux
   - Couleur de cheveux
   - Type d'yeux
   - Couleur de peau
   - Vêtements
   - Accessoires
   - Expressions faciales
2. Rendu en temps réel (canvas HTML5)
3. Possibilité de capturer l'avatar en image
4. Sauvegarde dans la base de données (JSON)
5. Affichage dans le chat et les profils
```

**Utilité :** Permet aux utilisateurs de se personnaliser et de s'identifier visuellement.

---

### 6. 🤖 CHATBOT IA

**Fichiers concernés :**
- `view/backoffice/collabcrud/chatbot.html` - Interface
- `view/backoffice/collabcrud/chatbot.js` - Logique client
- `view/backoffice/collabcrud/chatbot_api.php` - API backend

**Ce que fait le code :**
```php
// Assistant virtuel intelligent :
1. Répond aux questions sur :
   - Les collaborations
   - Les projets
   - Les membres
   - Le fonctionnement du site
2. Analyse le contexte de la conversation
3. Réponses contextuelles (pas juste des mots-clés)
4. Support multilingue (français/anglais)
5. Suggestions intelligentes
```

**Utilité :** Aide les utilisateurs à comprendre et utiliser la plateforme.

---

### 7. 📊 DASHBOARD D'ADMINISTRATION

**Fichiers concernés :**
- `view/backoffice/projectscrud/admindashboard.php` - Dashboard principal
- `view/backoffice/projectscrud/projectlist.php` - Liste des projets

**Ce que fait le code :**
```php
// Vue d'ensemble pour l'admin :
1. Statistiques :
   - Nombre total de jeux
   - Jeux publiés
   - Jeux en attente
   - Jeux rejetés
2. Liste des derniers jeux soumis
3. Actions rapides :
   - Voir les détails
   - Approuver/Rejeter
   - Modifier
   - Supprimer
4. Filtres par statut
```

**Utilité :** Permet à l'administrateur de gérer efficacement le contenu.

---

### 8. ✅ GESTION DES TÂCHES COLLABORATIVES

**Fichiers concernés :**
- `view/backoffice/collabcrud/room_collab.php` - Interface des tâches
- `controller/controllercollab/CollabTaskController.php` - Logique

**Ce que fait le code :**
```php
// Dans chaque collaboration :
1. Liste des tâches à faire
2. Ajout de nouvelles tâches
3. Marquage des tâches comme "faites"
4. Suppression de tâches
5. Affichage visuel (checkboxes)
```

**Utilité :** Permet de suivre l'avancement du projet collaboratif.

---

## 🔄 FLUX DE TRAVAIL PRINCIPAUX

### Flux 1 : Soumission d'un jeu
```
1. Visiteur → addgame.html
2. Remplit le formulaire
3. Upload des images
4. Soumet → add_game.php
5. Validation des données
6. Création du projet (statut: "en_attente")
7. Redirection avec message de succès
8. Admin voit le jeu dans le dashboard
9. Admin approuve → Statut devient "publie"
10. Le jeu apparaît sur la page d'accueil
```

### Flux 2 : Collaboration
```
1. Utilisateur crée une collaboration
2. Collaboration apparaît dans la liste (statut: "ouvert")
3. Autres utilisateurs peuvent rejoindre
4. Quand le groupe est complet → Redirection vers "room"
5. Dans la room :
   - Chat en temps réel
   - Gestion des tâches
   - Partage de fichiers
   - Messages vocaux
6. Modération automatique des messages
7. Gestion des membres par le propriétaire
```

### Flux 3 : Modération de message
```
1. Utilisateur envoie un message
2. send_message.php reçoit le message
3. MessageModerationController.moderateMessage()
4. Niveau 1 : Vérification mots interdits
   → Si bloqué : Arrêt, message supprimé
5. Niveau 2 : Analyse IA
   → Calcul de scores de dangerosité
   → Si score élevé : Arrêt, message supprimé
6. Si approuvé : Message sauvegardé et affiché
7. Log de modération enregistré
```

---

## 💾 STRUCTURE DE LA BASE DE DONNÉES

### Tables principales :

1. **`projects`** - Les jeux vidéo
   - id, nom, developpeur, description, image, etc.
   - statut (en_attente, publie, rejete)

2. **`collab_project`** - Projets collaboratifs
   - id, owner_id, titre, description, statut, max_membres

3. **`collab_members`** - Membres des collaborations
   - id, collab_id, user_id, role (owner/moderateur/membre)

4. **`collab_messages`** - Messages du chat
   - id, collab_id, user_id, message, audio_path, date_message

5. **`collab_task`** - Tâches collaboratives
   - id, collab_id, task, done, date_creation

6. **`user_avatars`** - Avatars des utilisateurs
   - id, user_id, avatar_data (JSON), profile_image

7. **`message_moderation_logs`** - Logs de modération
   - id, user_id, message, moderation_result, scores

8. **`moderated_messages`** - Messages modérés (pour révision)
   - id, original_message, moderated_message, status

---

## 🎨 TECHNOLOGIES UTILISÉES

### Backend
- **PHP 7+** - Langage serveur
- **MySQL** - Base de données
- **PDO** - Accès base de données sécurisé
- **Sessions PHP** - Gestion des utilisateurs

### Frontend
- **HTML5** - Structure
- **CSS3** - Styles (design moderne avec gradients, animations)
- **JavaScript (Vanilla)** - Interactivité
- **Canvas API** - Rendu des avatars
- **Web Audio API** - Enregistrement vocal
- **Fetch API** - Requêtes AJAX

### Fonctionnalités avancées
- **Modération IA** - Analyse sémantique en PHP
- **Chat en temps réel** - Rafraîchissement automatique
- **Upload de fichiers** - Images, PDF, audio
- **Génération d'avatars** - Système de personnalisation

---

## 🎯 CAS D'USAGE CONCRETS

### Scénario 1 : Développeur indépendant
```
Marie a créé un jeu de puzzle. Elle :
1. Va sur GameHub Pro
2. Clique sur "Add your game"
3. Remplit le formulaire avec les infos de son jeu
4. Upload des screenshots
5. Soumet son jeu
6. Attend l'approbation de l'admin
7. Une fois approuvé, son jeu est visible par tous
```

### Scénario 2 : Équipe de développement
```
Jean veut créer un jeu de stratégie mais a besoin d'aide :
1. Crée une collaboration "Jeu de stratégie médiéval"
2. Décrit le projet et les compétences recherchées
3. 5 développeurs rejoignent la collaboration
4. Ils utilisent la "room" pour :
   - Discuter des fonctionnalités (chat)
   - Partager des fichiers de code (upload)
   - S'envoyer des messages vocaux rapides
   - Créer des tâches (TODO list)
5. Le système modère automatiquement les messages inappropriés
6. Ils travaillent ensemble efficacement
```

### Scénario 3 : Administrateur
```
L'admin gère la plateforme :
1. Se connecte au dashboard
2. Voit 10 jeux en attente d'approbation
3. Examine chaque jeu (détails, screenshots, trailer)
4. Approuve les jeux de qualité
5. Rejette ceux qui ne respectent pas les règles
6. Consulte les statistiques de modération
7. Gère les collaborations problématiques si nécessaire
```

---

## 🌟 POINTS FORTS DU CODE

1. **Architecture MVC propre** - Séparation claire des responsabilités
2. **Système de modération sophistiqué** - Double niveau de filtrage
3. **Chat en temps réel** - Communication fluide
4. **Gestion des rôles** - Système de permissions
5. **Personnalisation** - Avatars et profils
6. **Interface moderne** - Design attractif et responsive
7. **Fonctionnalités avancées** - Messages vocaux, upload fichiers, chatbot

---

## 📈 VALEUR AJOUTÉE

Ce projet apporte :

1. **Pour l'écosystème des jeux indépendants :**
   - Plateforme de visibilité gratuite
   - Facilite la découverte de nouveaux talents
   - Encourage la création

2. **Pour les développeurs :**
   - Outil de collaboration professionnel
   - Communication structurée
   - Gestion de projet intégrée

3. **Pour la communauté :**
   - Environnement modéré et sûr
   - Expérience utilisateur agréable
   - Support multilingue

---

## 🎓 CONCLUSION

**GameHub Pro** est bien plus qu'un simple catalogue de jeux. C'est une **plateforme complète** qui combine :
- 📚 **Découverte de contenu** (jeux)
- 🤝 **Collaboration** (projets d'équipe)
- 💬 **Communication** (chat avancé)
- 🛡️ **Modération** (sécurité automatique)
- 🎨 **Personnalisation** (avatars)
- 📊 **Administration** (gestion de contenu)

Le code est **bien structuré**, **modulaire**, et implémente des **fonctionnalités avancées** qui démontrent une bonne compréhension du développement web moderne.

---

*Document créé pour expliquer le fonctionnement et l'utilité du projet GameHub Pro*


