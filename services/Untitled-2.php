<?php
// services/PlagiarismService.php

// ⚠️ N'incluez pas Article.php ici si vous obtenez "Cannot redeclare class Article"

class PlagiarismService {

    private $db;
    private $articleModel;
    private $threshold = 0.7; // Seuil de similarité (70%).

    public function __construct(PDO $db, Article $articleModel) {
        $this->db = $db;
        $this->articleModel = $articleModel; 
    }

    private function calculateJaccardSimilarity(array $setA, array $setB): float {
        $intersection = count(array_intersect($setA, $setB));
        $union = count(array_merge($setA, $setB));
        
        return ($union > 0) ? $intersection / $union : 0.0;
    }

    private function tokenize(string $text): array {
        $text = mb_strtolower($text, 'UTF-8'); 
        $text = preg_replace('/[^a-z0-9\s]/', '', $text);
        $tokens = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        
        return array_unique($tokens);
    }
    
    public function checkPlagiarism(string $newContent, ?int $excludeArticleId = null): ?array {
        
        $newTokens = $this->tokenize($newContent);
        
        if (empty($newTokens)) {
            return null;
        }

        // Utilisation du Modèle Article injecté
        $existingArticles = $this->articleModel->readAllContent($excludeArticleId);

        foreach ($existingArticles as $article) {
            
            $existingTokens = $this->tokenize($article['content']);
            $similarity = $this->calculateJaccardSimilarity($newTokens, $existingTokens);
            
            if ($similarity >= $this->threshold) {
                
                return [
                    'plagiat_article_id' => $article['id'],
                    'plagiat_article_title' => $article['title'],
                    'similarity_score' => round($similarity * 100, 2)
                ];
            }
        }

        return null; 
    }
}