<?php

require_once __DIR__ . "/../../config/config.php";

class MessageModerationController {
    
    private $db;
    
    // Liste de mots interdits (français et anglais) - Liste étendue
    private $forbiddenWords = [
        // Insultes françaises
        'connard', 'salope', 'putain', 'merde', 'con', 'enculé', 'fdp', 'pd', 'pédé',
        'bite', 'couille', 'chier', 'baiser', 'nique', 'niquer', 'pute', 'putes',
        'salaud', 'salauds', 'sale con', 'fils de pute', 'fdp', 'tg', 'ta gueule',
        'crève', 'crève la', 'va crever', 'va te faire', 'va chier',
        // Insultes anglaises
        'fuck', 'shit', 'bitch', 'asshole', 'damn', 'hell', 'crap', 'bastard',
        'motherfucker', 'fucker', 'dick', 'cock', 'pussy', 'cunt', 'whore',
        // Spam et fraudes
        'spam', 'scam', 'fraud', 'click here', 'buy now', 'free money', 'get rich',
        'make money fast', 'winner', 'prize', 'lottery', 'congratulations',
        // Grossièretés québécoises
        'criss', 'tabarnak', 'crisse', 'calice', 'calisse', 'osti', 'ostie',
        // Mots à caractère discriminatoire
        'nazi', 'hitler', 'kkk', 'fasciste', 'fascism', 'supremacist',
        // Menaces
        'je vais te tuer', 'je vais te frapper', 'je vais te battre',
        'i will kill you', 'i will hurt you', 'i will beat you',
        // Contenu sexuel explicite
        'porn', 'porno', 'xxx', 'sexe', 'sexuel', 'nude', 'nudes', 'sexy',
    ];
    
    // Patterns de leetspeak et tentatives de contournement
    private $leetSpeakPatterns = [
        'a' => ['4', '@', 'а'],
        'e' => ['3', '€', 'е'],
        'i' => ['1', '!', '|', 'і'],
        'o' => ['0', 'о'],
        's' => ['5', '$', 'ѕ'],
        't' => ['7', 'т'],
        'l' => ['1', '|', 'l'],
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
     * Niveau 1 : Filtre simple - Liste de mots interdits avec détection de contournement
     */
    private function level1Filter($message) {
        $messageLower = mb_strtolower($message, 'UTF-8');
        $foundWords = [];
        
        // Normaliser le message (enlever les caractères spéciaux pour contournement)
        $normalizedMessage = $this->normalizeMessage($messageLower);
        
        // Vérifier les mots interdits directs
        foreach ($this->forbiddenWords as $word) {
            // Recherche insensible à la casse et aux variations
            $pattern = '/\b' . preg_quote($word, '/') . '\b/i';
            if (preg_match($pattern, $messageLower)) {
                $foundWords[] = $word;
            }
        }
        
        // Vérifier les tentatives de contournement avec leetspeak
        foreach ($this->forbiddenWords as $word) {
            $leetVariations = $this->generateLeetVariations($word);
            foreach ($leetVariations as $variation) {
                if (stripos($normalizedMessage, $variation) !== false || 
                    stripos($messageLower, $variation) !== false) {
                    $foundWords[] = $word . ' (contournement détecté)';
                }
            }
        }
        
        // Détecter les répétitions excessives (spam)
        if ($this->detectSpamRepetition($message)) {
            $foundWords[] = 'spam (répétition excessive)';
        }
        
        // Détecter les caractères spéciaux utilisés pour masquer des mots
        if ($this->detectCharacterMasking($message)) {
            $foundWords[] = 'contournement (masquage de caractères)';
        }
        
        if (!empty($foundWords)) {
            return [
                'blocked' => true,
                'reason' => 'Message contient des mots interdits : ' . implode(', ', array_unique($foundWords)),
                'found_words' => array_unique($foundWords)
            ];
        }
        
        return [
            'blocked' => false,
            'reason' => ''
        ];
    }
    
    /**
     * Normaliser un message pour détecter les tentatives de contournement
     */
    private function normalizeMessage($message) {
        // Remplacer les variations de caractères par leurs équivalents
        $normalized = $message;
        foreach ($this->leetSpeakPatterns as $original => $replacements) {
            foreach ($replacements as $replacement) {
                $normalized = str_ireplace($replacement, $original, $normalized);
            }
        }
        // Enlever les caractères spéciaux utilisés pour masquer
        $normalized = preg_replace('/[^a-z0-9\s]/i', '', $normalized);
        return $normalized;
    }
    
    /**
     * Générer des variations leetspeak d'un mot
     */
    private function generateLeetVariations($word) {
        $variations = [];
        // Générer quelques variations communes
        $variations[] = str_ireplace('a', '4', $word);
        $variations[] = str_ireplace('e', '3', $word);
        $variations[] = str_ireplace('i', '1', $word);
        $variations[] = str_ireplace('o', '0', $word);
        $variations[] = str_ireplace('s', '5', $word);
        // Variations avec caractères spéciaux
        $variations[] = str_ireplace('a', '@', $word);
        $variations[] = str_ireplace('s', '$', $word);
        return array_unique($variations);
    }
    
    /**
     * Détecter les répétitions excessives (spam)
     */
    private function detectSpamRepetition($message) {
        // Vérifier si un mot est répété plus de 5 fois
        $words = preg_split('/\s+/', mb_strtolower($message));
        $wordCounts = array_count_values($words);
        foreach ($wordCounts as $word => $count) {
            if (strlen($word) > 2 && $count > 5) {
                return true;
            }
        }
        // Vérifier les répétitions de caractères (ex: "aaaaaa")
        if (preg_match('/(.)\1{4,}/', $message)) {
            return true;
        }
        return false;
    }
    
    /**
     * Détecter le masquage de caractères avec des symboles
     */
    private function detectCharacterMasking($message) {
        // Détecter des patterns comme "f*u*c*k" ou "f@ck"
        $maskingPatterns = [
            '/\b\w+[*\-_\.]\w+[*\-_\.]\w+[*\-_\.]\w+/i', // Mot avec séparateurs
            '/\w+[@#$%^&*]\w+/i', // Mot avec symboles au milieu
        ];
        foreach ($maskingPatterns as $pattern) {
            if (preg_match($pattern, $message)) {
                // Vérifier si le mot normalisé correspond à un mot interdit
                $normalized = $this->normalizeMessage($message);
                foreach ($this->forbiddenWords as $word) {
                    if (stripos($normalized, $word) !== false) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
    
    /**
     * Niveau 2 : Modération IA
     * Analyse : haine, violence, harcèlement, sexualité, discrimination, toxicité
     */
    private function level2AIModeration($message) {
        // Simuler l'analyse IA (dans un vrai projet, utiliser une API comme Perspective API, OpenAI Moderation, etc.)
        $scores = $this->analyzeWithAI($message);
        
        // Seuils de tolérance améliorés (0.00 à 1.00)
        // Seuils plus stricts pour une meilleure protection
        $thresholds = [
            'hate' => 0.6,           // Haine (plus strict)
            'violence' => 0.65,      // Violence (plus strict)
            'harassment' => 0.6,      // Harcèlement (plus strict)
            'sexual' => 0.75,        // Sexualité
            'discrimination' => 0.55, // Discrimination (très strict)
            'toxicity' => 0.7,       // Toxicité
            'dangerous' => 0.6       // Contenu dangereux (très strict)
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
     * Analyse le message avec IA (simulation améliorée)
     * Dans un vrai projet, utiliser une API comme :
     * - Google Perspective API
     * - OpenAI Moderation API
     * - Azure Content Moderator
     */
    private function analyzeWithAI($message) {
        // Analyser le contenu du message
        $messageLower = mb_strtolower($message, 'UTF-8');
        $normalizedMessage = $this->normalizeMessage($messageLower);
        
        // Patterns de détection étendus avec contextes
        $patterns = [
            'hate' => [
                'keywords' => ['haine', 'déteste', 'hate', 'kill', 'mort', 'crève', 'sale', 'dégage', 
                               'je te déteste', 'je te hais', 'i hate you', 'you suck', 'va te faire',
                               'fuck you', 'go to hell', 'damn you'],
                'phrases' => ['je te déteste', 'je te hais', 'va crever', 'crève la', 'i hate you',
                             'you are worthless', 'you are nothing', 'tu es nul', 'tu es inutile'],
                'weight' => 1.0
            ],
            'violence' => [
                'keywords' => ['tuer', 'violence', 'frapper', 'battre', 'kill', 'fight', 'war', 'arme',
                               'casser', 'détruire', 'exploser', 'bomb', 'weapon', 'knife', 'gun',
                               'couteau', 'pistolet', 'fusil', 'agresser', 'attaquer'],
                'phrases' => ['je vais te tuer', 'je vais te frapper', 'je vais te battre',
                             'i will kill you', 'i will hurt you', 'i will beat you',
                             'je vais te casser', 'je vais te détruire', 'je vais t\'agresser',
                             'je vais t\'attaquer', 'je vais te faire mal'],
                'weight' => 1.2
            ],
            'harassment' => [
                'keywords' => ['harceler', 'menacer', 'intimider', 'harass', 'threat', 'bully',
                               'menace', 'intimidation', 'harcèlement', 'stalking', 'stalker'],
                'phrases' => ['je vais te harceler', 'je vais te menacer', 'je vais t\'intimider',
                             'i will harass you', 'i will threaten you', 'i will bully you',
                             'tu vas payer', 'tu vas regretter', 'you will pay', 'you will regret'],
                'weight' => 1.1
            ],
            'sexual' => [
                'keywords' => ['sexe', 'sexuel', 'sex', 'porn', 'xxx', 'adult', 'nude', 'nudes',
                               'sexy', 'pornographie', 'érotique', 'hardcore', 'explicit'],
                'phrases' => ['envie de sexe', 'faire l\'amour', 'make love', 'want sex',
                             'sexual content', 'adult content'],
                'weight' => 0.9
            ],
            'discrimination' => [
                'keywords' => ['race', 'religion', 'ethnie', 'discriminer', 'raciste', 'nazi',
                               'hitler', 'supremacist', 'fasciste', 'fascism', 'kkk',
                               'antisémite', 'antisemit', 'homophobe', 'homophobic'],
                'phrases' => ['tu es un raciste', 'you are racist', 'you are a nazi',
                             'discrimination raciale', 'racial discrimination'],
                'weight' => 1.3
            ],
            'toxicity' => [
                'keywords' => ['idiot', 'stupide', 'débile', 'imbécile', 'moron', 'stupid',
                               'crétin', 'abruti', 'con', 'connard', 'salaud', 'fool',
                               'loser', 'perdant', 'nul', 'inutile', 'worthless'],
                'phrases' => ['tu es un idiot', 'you are stupid', 'you are an idiot',
                             'tu es nul', 'you are worthless', 'you are useless'],
                'weight' => 0.8
            ],
            'dangerous' => [
                'keywords' => ['suicide', 'bombe', 'attentat', 'terrorisme', 'bomb', 'terror',
                               'explosif', 'explosive', 'tuer', 'kill', 'murder', 'meurtre',
                               'violence extrême', 'extreme violence'],
                'phrases' => ['je vais me suicider', 'i will kill myself', 'je vais faire sauter',
                             'i will bomb', 'je vais tuer', 'i will murder'],
                'weight' => 1.5
            ]
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
        foreach ($patterns as $category => $patternData) {
            $score = 0.0;
            
            // Vérifier les mots-clés
            $keywordMatches = 0;
            foreach ($patternData['keywords'] as $keyword) {
                // Vérifier dans le message original et normalisé
                if (stripos($messageLower, $keyword) !== false || 
                    stripos($normalizedMessage, $keyword) !== false) {
                    $keywordMatches++;
                }
            }
            
            // Vérifier les phrases complètes (plus grave)
            $phraseMatches = 0;
            foreach ($patternData['phrases'] as $phrase) {
                if (stripos($messageLower, $phrase) !== false) {
                    $phraseMatches++;
                }
            }
            
            // Calculer le score avec pondération
            if ($phraseMatches > 0) {
                // Les phrases complètes sont plus graves
                $score = min(0.5 + ($phraseMatches * 0.15), 0.95);
            } elseif ($keywordMatches > 0) {
                // Score basé sur le nombre de mots-clés trouvés
                $score = min(0.2 + ($keywordMatches * 0.1), 0.9);
            }
            
            // Appliquer le poids de la catégorie
            $scores[$category] = min($score * $patternData['weight'], 1.0);
        }
        
        // Détection contextuelle supplémentaire
        $scores = $this->analyzeContext($message, $scores);
        
        // Détection de menaces personnelles
        $threatScore = $this->detectPersonalThreats($message);
        if ($threatScore > 0) {
            $scores['violence'] = max($scores['violence'], $threatScore);
            $scores['harassment'] = max($scores['harassment'], $threatScore * 0.9);
        }
        
        // Normaliser les scores (s'assurer qu'ils sont entre 0 et 1)
        foreach ($scores as $category => $score) {
            $scores[$category] = max(0.0, min(1.0, $score));
        }
        
        return $scores;
    }
    
    /**
     * Analyser le contexte du message pour détecter des patterns plus subtils
     */
    private function analyzeContext($message, $scores) {
        $messageLower = mb_strtolower($message, 'UTF-8');
        
        // Détecter les messages en majuscules (cris/agressivité)
        $uppercaseRatio = strlen(preg_replace('/[^A-Z]/', '', $message)) / max(strlen($message), 1);
        if ($uppercaseRatio > 0.5 && strlen($message) > 10) {
            // Augmenter légèrement les scores de toxicité et haine
            $scores['toxicity'] = min(1.0, $scores['toxicity'] + 0.1);
            $scores['hate'] = min(1.0, $scores['hate'] + 0.05);
        }
        
        // Détecter les points d'exclamation multiples (agressivité)
        $exclamationCount = substr_count($message, '!');
        if ($exclamationCount > 3) {
            $scores['toxicity'] = min(1.0, $scores['toxicity'] + 0.1);
        }
        
        // Détecter les combinaisons de mots négatifs
        $negativeWords = ['pas', 'ne', 'jamais', 'rien', 'aucun', 'not', 'never', 'nothing', 'no'];
        $negativeCount = 0;
        foreach ($negativeWords as $word) {
            if (stripos($messageLower, $word) !== false) {
                $negativeCount++;
            }
        }
        if ($negativeCount > 3) {
            $scores['toxicity'] = min(1.0, $scores['toxicity'] + 0.15);
        }
        
        return $scores;
    }
    
    /**
     * Détecter les menaces personnelles spécifiques
     */
    private function detectPersonalThreats($message) {
        $messageLower = mb_strtolower($message, 'UTF-8');
        
        $threatPatterns = [
            '/je (vais|vais te|vais t\') (tuer|frapper|battre|casser|détruire|agresser|attaquer)/i',
            '/i (will|am going to) (kill|hurt|beat|attack|destroy) (you|u)/i',
            '/tu (vas|vas te) (mourir|crever|payer|regretter)/i',
            '/you (will|are going to) (die|pay|regret)/i',
            '/je (vais|vais te) (faire|donner) (mal|du mal)/i',
        ];
        
        foreach ($threatPatterns as $pattern) {
            if (preg_match($pattern, $message)) {
                return 0.85; // Score élevé pour les menaces personnelles
            }
        }
        
        return 0.0;
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

