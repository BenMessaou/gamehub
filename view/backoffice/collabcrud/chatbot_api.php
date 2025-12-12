<?php
session_start();
header('Content-Type: application/json');

// Simuler une API de chatbot (vous pouvez remplacer par une vraie API)
// Pour une vraie intégration, utilisez HuggingFace, OpenAI, ou une autre API

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['message']) || empty($data['message'])) {
    echo json_encode(['response' => 'Veuillez fournir un message.']);
    exit;
}

$userMessage = trim($data['message']);
$context = $data['context'] ?? [];

// Réponses intelligentes basées sur le contexte et les mots-clés
$response = generateResponse($userMessage, $context);

// Simuler un délai de traitement (comme une vraie API)
usleep(500000); // 0.5 secondes

echo json_encode([
    'response' => $response,
    'timestamp' => date('Y-m-d H:i:s')
]);

function generateResponse($message, $context) {
    $lowerMessage = strtolower($message);
    
    // Réponses contextuelles intelligentes
    $responses = [
        // Salutations
        'salut' => 'Bonjour ! Comment puis-je vous aider aujourd\'hui ?',
        'bonjour' => 'Bonjour ! Je suis là pour vous assister. Que souhaitez-vous savoir ?',
        'hello' => 'Hello ! Comment puis-je vous aider ?',
        'bonsoir' => 'Bonsoir ! En quoi puis-je vous être utile ?',
        
        // Questions sur la collaboration
        'collaboration' => 'Les collaborations permettent de travailler en équipe sur des projets. Vous pouvez créer des projets, inviter des membres, et collaborer en temps réel.',
        'membre' => 'Les membres peuvent être ajoutés à une collaboration. Le propriétaire peut gérer les membres et leurs rôles.',
        'projet' => 'Un projet de collaboration regroupe plusieurs membres travaillant ensemble. Vous pouvez créer, modifier et gérer vos projets depuis l\'espace de collaboration.',
        
        // Aide
        'aide' => 'Je peux vous aider avec : les collaborations, les projets, les membres, le chat, et bien plus encore. Posez-moi vos questions !',
        'help' => 'I can help you with: collaborations, projects, members, chat, and more. Ask me anything!',
        
        // Remerciements
        'merci' => 'De rien ! N\'hésitez pas si vous avez d\'autres questions.',
        'thanks' => 'You\'re welcome! Feel free to ask if you need anything else.',
        
        // Au revoir
        'au revoir' => 'Au revoir ! À bientôt !',
        'bye' => 'Goodbye! See you soon!',
        
        // Questions techniques
        'avatar' => 'Vous pouvez personnaliser votre avatar dans l\'éditeur d\'avatar. Cliquez sur le bouton Avatar dans la navigation.',
        'chat' => 'Le chat permet de communiquer avec les membres de votre collaboration en temps réel. Vous pouvez envoyer des messages texte ou vocaux.',
        'message' => 'Vous pouvez envoyer des messages dans le chat de collaboration. Utilisez le champ de saisie en bas du chat.',
        
        // Questions générales
        'comment' => 'Je suis là pour répondre à vos questions. Posez-moi ce que vous voulez savoir !',
        'quoi' => 'Je peux vous aider avec diverses questions sur la plateforme. Que voulez-vous savoir ?',
        'pourquoi' => 'Je peux vous expliquer le fonctionnement de la plateforme. Quelle fonctionnalité vous intéresse ?',
    ];
    
    // Chercher une correspondance exacte ou partielle
    foreach ($responses as $keyword => $response) {
        if (strpos($lowerMessage, $keyword) !== false) {
            return $response;
        }
    }
    
    // Réponses contextuelles basées sur les mots-clés
    if (strpos($lowerMessage, 'créer') !== false || strpos($lowerMessage, 'nouveau') !== false) {
        return 'Pour créer un nouveau projet, allez dans la section Collaborations et cliquez sur "Créer une collaboration".';
    }
    
    if (strpos($lowerMessage, 'supprimer') !== false || strpos($lowerMessage, 'effacer') !== false) {
        return 'Pour supprimer un élément, utilisez les options de gestion disponibles dans le menu.';
    }
    
    if (strpos($lowerMessage, 'modifier') !== false || strpos($lowerMessage, 'changer') !== false) {
        return 'Vous pouvez modifier les éléments via les options d\'édition disponibles dans l\'interface.';
    }
    
    // Réponses génériques intelligentes
    $genericResponses = [
        'Je comprends. Pouvez-vous être plus précis dans votre question ?',
        'C\'est intéressant. Pourriez-vous me donner plus de détails ?',
        'Je peux vous aider avec ça. Que souhaitez-vous savoir exactement ?',
        'Bonne question ! Laissez-moi réfléchir... Pouvez-vous reformuler ?',
        'Je vois. Avez-vous une question spécifique sur la plateforme ?',
    ];
    
    // Utiliser le contexte pour une réponse plus pertinente
    if (!empty($context)) {
        $lastMessage = end($context);
        if (isset($lastMessage['sender']) && $lastMessage['sender'] === 'bot') {
            return 'Avez-vous d\'autres questions sur ce sujet ?';
        }
    }
    
    // Réponse aléatoire parmi les génériques
    return $genericResponses[array_rand($genericResponses)];
}

