<?php

require_once __DIR__ . "/../../config/config.php";

class MessageModerationController {
    
    private $db;
    
    // Liste de mots interdits (français et anglais)
    private $forbiddenWords = [
        // Insultes françaises
        'connard', 'salope', 'putain', 'merde', 'con', 'enculé', 'fdp', 'pd', 'pédé',
        'bite', 'couille', 'chier', 'baiser', 'nique', 'niquer',
        // Insultes anglaises
        'fuck', 'shit', 'bitch', 'asshole', 'damn', 'hell', 'crap',
        // Spam
        'spam', 'scam', 'fraud', 'click here', 'buy now', 'free money',
        // Grossièretés
        'criss', 'tabarnak', 'crisse', 'calice', 'calisse',
        // Mots à caractère discriminatoire
        'nazi', 'hitler', 'kkk',
    ];
    
    public function __construct() {
        $this->db = config::getConnexion();
    }
    
    /**
     * Modère un message avec les deux niveaux de filtrage
     * @param string $message - Le message à modérer
     * @return array - Résultat de la modération
     */
    public function moderateMessage($message) {
        $result = [
            'approved' => false,
            'blocked' => false,
            'moderated' => false,
            'reason' => '',
            'level' => 0,
            'scores' => []
        ];
        
        // Niveau 1 : Filtre simple (mots interdits)
        $level1Result = $this->level1Filter($message);
        
        if ($level1Result['blocked']) {
            $result['blocked'] = true;
            $result['reason'] = $level1Result['reason'];
            $result['level'] = 1;
            return $result;
        }
        
        // Niveau 2 : IA de modération
        $level2Result = $this->level2AIModeration($message);
        
        if ($level2Result['blocked']) {
            $result['blocked'] = true;
            $result['reason'] = $level2Result['reason'];
            $result['level'] = 2;
            $result['scores'] = $level2Result['scores'];
            return $result;
        }
        
        // Message approuvé
        $result['approved'] = true;
        $result['scores'] = $level2Result['scores'];
        
        return $result;
    }
    
    /**
     * Niveau 1 : Filtre simple - Liste de mots interdits
     */
    private function level1Filter($message) {
        $messageLower = mb_strtolower($message, 'UTF-8');
        $foundWords = [];
        
        foreach ($this->forbiddenWords as $word) {
            // Recherche insensible à la casse et aux variations
            $pattern = '/\b' . preg_quote($word, '/') . '\b/i';
            if (preg_match($pattern, $messageLower)) {
                $foundWords[] = $word;
            }
        }
        
        if (!empty($foundWords)) {
            return [
                'blocked' => true,
                'reason' => 'Message contient des mots interdits : ' . implode(', ', $foundWords),
                'found_words' => $foundWords
            ];
        }
        
        return [
            'blocked' => false,
            'reason' => ''
        ];
    }
    
    /**
     * Niveau 2 : Modération IA
     * Analyse : haine, violence, harcèlement, sexualité, discrimination, toxicité
     */
    private function level2AIModeration($message) {
        // Simuler l'analyse IA (dans un vrai projet, utiliser une API comme Perspective API, OpenAI Moderation, etc.)
        $scores = $this->analyzeWithAI($message);
        
        // Seuils de tolérance (0.00 à 1.00)
        $thresholds = [
            'hate' => 0.7,           // Haine
            'violence' => 0.8,        // Violence
            'harassment' => 0.75,     // Harcèlement
            'sexual' => 0.8,          // Sexualité
            'discrimination' => 0.7,  // Discrimination
            'toxicity' => 0.75,       // Toxicité
            'dangerous' => 0.8        // Contenu dangereux
        ];
        
        $blocked = false;
        $reasons = [];
        
        foreach ($scores as $category => $score) {
            if (isset($thresholds[$category]) && $score >= $thresholds[$category]) {
                $blocked = true;
                $reasons[] = ucfirst($category) . " (score: " . number_format($score, 2) . ")";
            }
        }
        
        return [
            'blocked' => $blocked,
            'reason' => $blocked ? 'Contenu inapproprié détecté : ' . implode(', ', $reasons) : '',
            'scores' => $scores
        ];
    }
    
    /**
     * Analyse le message avec IA (simulation)
     * Dans un vrai projet, utiliser une API comme :
     * - Google Perspective API
     * - OpenAI Moderation API
     * - Azure Content Moderator
     */
    private function analyzeWithAI($message) {
        // SIMULATION - À remplacer par une vraie API
        
        // Analyser le contenu du message
        $messageLower = mb_strtolower($message, 'UTF-8');
        
        // Patterns de détection (simplifiés pour la démo)
        $patterns = [
            'hate' => ['haine', 'déteste', 'hate', 'kill', 'mort', 'crève', 'sale', 'dégage'],
            'violence' => ['tuer', 'violence', 'frapper', 'battre', 'kill', 'fight', 'war', 'arme'],
            'harassment' => ['harceler', 'menacer', 'intimider', 'harass', 'threat', 'bully'],
            'sexual' => ['sexe', 'sexuel', 'sex', 'porn', 'xxx', 'adult'],
            'discrimination' => ['race', 'religion', 'ethnie', 'discriminer', 'raciste', 'nazi'],
            'toxicity' => ['idiot', 'stupide', 'débile', 'imbécile', 'moron', 'stupid'],
            'dangerous' => ['suicide', 'bombe', 'attentat', 'terrorisme', 'bomb', 'terror']
        ];
        
        $scores = [
            'hate' => 0.0,
            'violence' => 0.0,
            'harassment' => 0.0,
            'sexual' => 0.0,
            'discrimination' => 0.0,
            'toxicity' => 0.0,
            'dangerous' => 0.0
        ];
        
        // Calculer les scores basés sur les patterns
        foreach ($patterns as $category => $keywords) {
            $matches = 0;
            foreach ($keywords as $keyword) {
                if (stripos($messageLower, $keyword) !== false) {
                    $matches++;
                }
            }
            
            // Score basé sur le nombre de correspondances
            if ($matches > 0) {
                $scores[$category] = min(0.3 + ($matches * 0.2), 0.95);
            }
        }
        
        // Ajouter un peu d'aléatoire pour simuler l'IA (dans un vrai projet, l'API le fait)
        foreach ($scores as $category => $score) {
            if ($score > 0) {
                // Variation de ±0.1 pour simuler l'incertitude de l'IA
                $scores[$category] = max(0, min(1, $score + (rand(-10, 10) / 100)));
            }
        }
        
        return $scores;
    }
    
    /**
     * Enregistrer un message modéré (pour statistiques)
     */
    public function logModeration($message, $result, $userId, $collabId) {
        try {
            $sql = "INSERT INTO message_moderation_logs 
                    (user_id, collab_id, message, moderation_result, scores, created_at) 
                    VALUES (?, ?, ?, ?, ?, NOW())";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $userId,
                $collabId,
                $message,
                json_encode($result),
                json_encode($result['scores'] ?? [])
            ]);
        } catch (PDOException $e) {
            // Table n'existe peut-être pas encore
            error_log("Erreur log modération: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Récupérer les statistiques de modération
     */
    public function getModerationStats($collabId = null) {
        try {
            $sql = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN JSON_EXTRACT(moderation_result, '$.blocked') = true THEN 1 ELSE 0 END) as blocked,
                        SUM(CASE WHEN JSON_EXTRACT(moderation_result, '$.level') = 1 THEN 1 ELSE 0 END) as level1,
                        SUM(CASE WHEN JSON_EXTRACT(moderation_result, '$.level') = 2 THEN 1 ELSE 0 END) as level2
                    FROM message_moderation_logs";
            
            if ($collabId) {
                $sql .= " WHERE collab_id = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$collabId]);
            } else {
                $stmt = $this->db->prepare($sql);
                $stmt->execute();
            }
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [
                'total' => 0,
                'blocked' => 0,
                'level1' => 0,
                'level2' => 0
            ];
        }
    }
}

?>

