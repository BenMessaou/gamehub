<?php
/**
 * GameHub Pro - ProjectManager
 * CRUD complet pour l'entité Project (Jeux vidéo)
 * 
 * @author  GameHub Pro Team
 * @version 1.1 - 16/11/2025
 */

require_once 'Database.php';
require_once 'projects.php';

class ProjectManager
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // ========================================
    // CREATE - Ajouter un nouveau jeu
    // ========================================
    public function ajouter(Project $project): bool
    {
        $sql = "INSERT INTO projects (
                    nom, developpeur, developpeur_id, date_creation, categorie,
                    age_recommande, lieu, description, image, screenshots,
                    trailer, lien_telechargement, plateformes, tags,
                    statut, date_soumission
                ) VALUES (
                    :nom, :developpeur, :developpeur_id, :date_creation, :categorie,
                    :age_recommande, :lieu, :description, :image, :screenshots,
                    :trailer, :lien_telechargement, :plateformes, :tags,
                    :statut, :date_soumission
                )";

        $stmt = $this->db->prepare($sql);

        $screenshots = json_encode($project->getScreenshots());
        $plateformes = json_encode($project->getPlateformes());
        $tags = json_encode($project->getTags());

        return $stmt->execute([
            ':nom'              => $project->getNom(),
            ':developpeur'      => $project->getDeveloppeur(),
            ':developpeur_id'   => $project->getDeveloppeurId(),
            ':date_creation'    => $project->getDateCreation(),
            ':categorie'        => $project->getCategorie(),
            ':age_recommande'   => $project->getAgeRecommande(),
            ':lieu'             => $project->getLieu(),
            ':description'      => $project->getDescription(),
            ':image'            => $project->getImage(),
            ':screenshots'      => $screenshots,
            ':trailer'          => $project->getTrailer(),
            ':lien_telechargement' => $project->getLienTelechargement(),
            ':plateformes'      => $plateformes,
            ':tags'             => $tags,
            ':statut'           => $project->getStatut(),
            ':date_soumission'  => $project->getDateSoumission()
        ]);
    }

    // ========================================
    // READ - Récupérer un jeu par ID
    // ========================================
    public function getById(int $id): ?Project
    {
        $sql = "SELECT * FROM projects WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? $this->hydrate($data) : null;
    }

    // ========================================
    // READ - Tous les jeux (avec filtres)
    // ========================================
    public function getAll(
        ?string $statut = null,
        ?string $categorie = null,
        ?int $limit = null,
        ?int $offset = null,
        string $orderBy = 'date_soumission',
        string $orderDir = 'DESC'
    ): array {
        $sql = "SELECT * FROM projects WHERE 1=1";
        $params = [];

        if ($statut) {
            $sql .= " AND statut = :statut";
            $params[':statut'] = $statut;
        }
        if ($categorie) {
            $sql .= " AND categorie = :categorie";
            $params[':categorie'] = $categorie;
        }

        $sql .= " ORDER BY " . $this->secureOrderBy($orderBy) . " " . ($orderDir === 'ASC' ? 'ASC' : 'DESC');

        if ($limit !== null) {
            $sql .= " LIMIT :limit";
            if ($offset !== null) $sql .= " OFFSET :offset";
        }

        $stmt = $this->db->prepare($sql);

        if ($limit !== null) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            if ($offset !== null) $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        }

        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([$this, 'hydrate'], $results);
    }

    // ========================================
    // READ - Jeux en attente
    // ========================================
    public function getEnAttente(int $limit = 50): array
    {
        return $this->getAll('en_attente', null, $limit, null, 'date_soumission', 'DESC');
    }

    // ========================================
    // READ - Jeux publiés (pour frontend)
    // ========================================
    public function getPublies(int $limit = 12, int $offset = 0): array
    {
        return $this->getAll('publie', null, $limit, $offset, 'date_publication', 'DESC');
    }

    // ========================================
    // READ - Top téléchargés
    // ========================================
    public function getTopTelecharges(int $limit = 5): array
    {
        return $this->getAll('publie', null, $limit, null, 'telechargements', 'DESC');
    }

    // ========================================
    // UPDATE - Mettre à jour un jeu
    // ========================================
    public function update(Project $project): bool
    {
        $sql = "UPDATE projects SET
                    nom = :nom,
                    developpeur = :developpeur,
                    date_creation = :date_creation,
                    categorie = :categorie,
                    age_recommande = :age_recommande,
                    lieu = :lieu,
                    description = :description,
                    image = :image,
                    screenshots = :screenshots,
                    trailer = :trailer,
                    lien_telechargement = :lien_telechargement,
                    plateformes = :plateformes,
                    tags = :tags,
                    statut = :statut,
                    date_publication = :date_publication
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        $screenshots = json_encode($project->getScreenshots());
        $plateformes = json_encode($project->getPlateformes());
        $tags = json_encode($project->getTags());

        return $stmt->execute([
            ':id'                 => $project->getId(),
            ':nom'                => $project->getNom(),
            ':developpeur'        => $project->getDeveloppeur(),
            ':date_creation'      => $project->getDateCreation(),
            ':categorie'          => $project->getCategorie(),
            ':age_recommande'     => $project->getAgeRecommande(),
            ':lieu'               => $project->getLieu(),
            ':description'        => $project->getDescription(),
            ':image'              => $project->getImage(),
            ':screenshots'        => $screenshots,
            ':trailer'            => $project->getTrailer(),
            ':lien_telechargement'=> $project->getLienTelechargement(),
            ':plateformes'        => $plateformes,
            ':tags'               => $tags,
            ':statut'             => $project->getStatut(),
            ':date_publication'   => $project->getDatePublication()
        ]);
    }

    // ========================================
    // UPDATE - Changer le statut
    // ========================================
    public function changerStatut(int $id, string $nouveauStatut): bool
    {
        $project = $this->getById($id);
        if (!$project) return false;

        $project->setStatut($nouveauStatut);
        if ($nouveauStatut === 'publie') {
            $project->setDatePublication(date('Y-m-d H:i:s'));
        }

        $sql = "UPDATE projects SET statut = :statut, date_publication = :date_publication WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':statut' => $nouveauStatut,
            ':date_publication' => $project->getDatePublication()
        ]);
    }

    // ========================================
    // UPDATE - Incrémenter téléchargements
    // ========================================
    public function incrementerTelechargements(int $id): bool
    {
        $sql = "UPDATE projects SET telechargements = telechargements + 1 WHERE id = :id AND statut = 'publie'";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    // ========================================
    // DELETE - Supprimer un jeu
    // ========================================
    public function supprimer(int $id): bool
    {
        // Supprimer les fichiers associés
        $project = $this->getById($id);
        if ($project) {
            @unlink('../uploads/games/' . $project->getImage());
            foreach ($project->getScreenshots() as $screen) {
                @unlink('../uploads/screenshots/' . $screen);
            }
        }

        $sql = "DELETE FROM projects WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    // ========================================
    // UTILS - Hydratation
    // ========================================
    private function hydrate(array $data): Project
    {
        $project = new Project(
            $data['nom'],
            $data['developpeur'],
            $data['date_creation'],
            $data['categorie'],
            $data['description'],
            $data['image'],
            $data['trailer'],
            $data['developpeur_id']
        );

        $project->setId($data['id'])
                ->setAgeRecommande($data['age_recommande'])
                ->setLieu($data['lieu'])
                ->setScreenshots(json_decode($data['screenshots'], true) ?? [])
                ->setLienTelechargement($data['lien_telechargement'])
                ->setPlateformes(json_decode($data['plateformes'], true) ?? [])
                ->setTags(json_decode($data['tags'], true) ?? [])
                ->setStatut($data['statut'])
                ->setDateSoumission($data['date_soumission'])
                ->setDatePublication($data['date_publication'])
                ->setTelechargements((int)$data['telechargements']);

        return $project;
    }

    // ========================================
    // UTILS - Sécurité ORDER BY
    // ========================================
    private function secureOrderBy(string $column): string
    {
        $allowed = ['id', 'nom', 'date_soumission', 'date_publication', 'telechargements', 'categorie'];
        return in_array($column, $allowed) ? $column : 'date_soumission';
    }

    // ========================================
    // STATS - Compteurs rapides
    // ========================================
    public function getStats(): array
    {
        $stats = [];

        $sql = "SELECT 
                    COUNT(*) as total_jeux,
                    SUM(CASE WHEN statut = 'publie' THEN 1 ELSE 0 END) as jeux_publies,
                    SUM(CASE WHEN statut = 'en_attente' THEN 1 ELSE 0 END) as en_attente,
                    SUM(telechargements) as total_telechargements
                FROM projects";
        
        $stmt = $this->db->query($sql);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $stats['total_jeux'] = (int)($row['total_jeux'] ?? 0);
        $stats['jeux_publies'] = (int)($row['jeux_publies'] ?? 0);
        $stats['en_attente'] = (int)($row['en_attente'] ?? 0);
        $stats['total_telechargements'] = (int)($row['total_telechargements'] ?? 0);

        return $stats;
    }
}