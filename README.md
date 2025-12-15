
# GameHub – Gaming Community & Marketplace Platform

## Project Overview

GameHub is a web-based gaming platform designed to centralize multiple services for gamers and game developers within a single, secure environment. The platform enables users to shop for games, publish and promote their own gaming projects, explore projects created by others, read gaming-related articles, and interact through feedback and discussions.

The project was developed in an academic context and emphasizes user-centered design, platform security, and full-stack web development.

---

## Project Objectives

* Provide an intuitive platform for gamers and project creators
* Encourage collaboration and visibility for independent projects
* Offer structured access to games, articles, and community feedback
* Integrate advanced security mechanisms for user authentication
* Allow administrators to monitor and manage platform activity

---

## User Roles

### User

Registered users can:

* Create and manage a personal account
* Access all platform features after secure authentication
* Interact with community content and services

### Administrator

Administrators are responsible for:

* Managing user accounts
* Monitoring activity logs
* Ensuring system security and correct platform usage

---

## Main User Tasks and Features

### 1. Game Marketplace

Users can browse a catalog of available games presented with clear descriptions and relevant details. This feature allows users to view games in an organized manner and make informed choices when exploring gaming content.

### 2. Project Management and Promotion

Users can add their own gaming projects through a dedicated submission interface. Each project includes descriptive information that allows other users to understand its purpose and functionality. Once published, projects are visible to the community and can be browsed by other users.

In addition, a chatbot is integrated into the projects section to assist users by answering questions, guiding navigation, and improving overall accessibility and user support.

### 3. Browsing Community Projects

Users can explore projects submitted by other members of the platform. This task focuses on discovery and inspiration, allowing users to view different ideas, concepts, and implementations created by the community.

### 4. Feedback System

The feedback section enables users to browse existing feedback related to games and projects. Users can also post their own feedback, contributing opinions and evaluations that help others make informed decisions. This system encourages interaction and continuous improvement within the platform.

### 5. Gaming Articles

The platform includes a dedicated articles section managed by administrators. Administrators can add and publish gaming-related articles through the backoffice, while users can browse and read these articles in the frontoffice. This feature provides users with curated content related to games, technologies, and trends in the gaming industry.

---

## Security Features

Security is a core component of GameHub. The platform includes:

* Fingerprint-based login for enhanced identity verification
* Two-Factor Authentication (2FA) using Google Authenticator
* Secure session handling and authentication processes
* Administrator access to system logs for monitoring user actions and system events

---

## Technologies Used

### Frontend

* HTML
* CSS
* JavaScript

### Backend

* PHP

### Database

* MySQL

---

## Project Structure (Example)

```
/gamehub
│── /assets
│   ├── /css
│   ├── /js
│   └── /images
│── /views
│   ├── /frontoffice
│   │   ├── user pages (games, projects, feedback, articles)
│   └── /backoffice
│       ├── admin pages (users, articles, logs)
│── /controllers
│── /models
│── /admin
│── /database
│── index.php
```

/gamehub
│── /assets
│   ├── /css
│   ├── /js
│   └── /images
│── /views
│── /controllers
│── /models
│── /admin
│── /database
│── index.php
└── README.md

```

---

## Installation and Setup
1. Install a local PHP server environment such as XAMPP
2. Clone or download the project repository
3. Import the MySQL database located in the database folder
4. Configure database connection settings in the PHP configuration file
5. Start Apache and MySQL services
6. Access the application via http://localhost/gamehub

---

## Learning Outcomes
- Design and implementation of user-centered web applications
- Full-stack development using PHP and MySQL
- Integration of security mechanisms such as 2FA and biometric login
- Development of admin monitoring and logging systems
- Practical application of academic web development concepts

---

## Academic Context
This project was developed as part of coursework at ESPRIT and aims to demonstrate the application of theoretical knowledge in a practical web platform.

---

## Feedback and Improvement
Feedback and suggestions are encouraged to further improve the platform and enhance the overall user experience.

---

GameHub is a platform dedicated to connecting gamers and creators through secure, interactive, and community-driven features.

```
