<?php
include(__DIR__ . '/../config/config.php');
include(__DIR__ . '/../model/Project.php');

class ProjectController {

    // ======================
    // LISTE DES PROJECTS
    // ======================
    public function listProjects() {
        $sql = "SELECT * FROM projects";
        $db = config::getConnexion();
        try {
            $list = $db->query($sql);
            return $list;
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    // ======================
    // SUPPRIMER PROJECT
    // ======================
    public function deleteProject($id) {
        $sql = "DELETE FROM projects WHERE id = :id";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':id', $id);
        try {
            $req->execute();
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    // ======================
    // AJOUTER PROJECT
    // ======================
    public function addProject(Project $project) {

        $sql = "INSERT INTO projects 
        (nom, developpeur, date_creation, categorie, description, image, trailer, developpeur_id, age_recommande, lieu, lien_telechargement, plateformes, tags, screenshots)
        VALUES 
        (:nom, :developpeur, :date_creation, :categorie, :description, :image, :trailer, :developpeur_id, :age_recommande, :lieu, :lien_telechargement, :plateformes, :tags, :screenshots)";

        $db = config::getConnexion();

        try {
            $query = $db->prepare($sql);

            $query->execute([
                'nom'                 => $project->getNom(),
                'developpeur'         => $project->getDeveloppeur(),
                'date_creation'       => $project->getDateCreation(),
                'categorie'           => $project->getCategorie(),
                'description'         => $project->getDescription(),
                'image'               => $project->getImage(),
                'trailer'             => $project->getTrailer(),
                'developpeur_id'      => $project->getDeveloppeurId(),
                'age_recommande'      => $project->getAgeRecommande(),
                'lieu'                => $project->getLieu(),
                'lien_telechargement' => $project->getLienTelechargement(),
                'plateformes'         => json_encode($project->getPlateformes()),
                'tags'                => json_encode($project->getTags()),
                'screenshots'         => json_encode($project->getScreenshots())
            ]);

        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    // ======================
    // MODIFIER PROJECT
    // ======================
    public function updateProject(Project $project, $id) {

        $sql = "UPDATE projects SET 
            nom = :nom,
            developpeur = :developpeur,
            date_creation = :date_creation,
            categorie = :categorie,
            description = :description,
            image = :image,
            trailer = :trailer,
            developpeur_id = :developpeur_id,
            age_recommande = :age_recommande,
            lieu = :lieu,
            lien_telechargement = :lien_telechargement,
            plateformes = :plateformes,
            tags = :tags,
            screenshots = :screenshots
        WHERE id = :id";

        $db = config::getConnexion();

        try {
            $query = $db->prepare($sql);

            $query->execute([
                'id'                  => $id,
                'nom'                 => $project->getNom(),
                'developpeur'         => $project->getDeveloppeur(),
                'date_creation'       => $project->getDateCreation(),
                'categorie'           => $project->getCategorie(),
                'description'         => $project->getDescription(),
                'image'               => $project->getImage(),
                'trailer'             => $project->getTrailer(),
                'developpeur_id'      => $project->getDeveloppeurId(),
                'age_recommande'      => $project->getAgeRecommande(),
                'lieu'                => $project->getLieu(),
                'lien_telechargement' => $project->getLienTelechargement(),
                'plateformes'         => json_encode($project->getPlateformes()),
                'tags'                => json_encode($project->getTags()),
                'screenshots'         => json_encode($project->getScreenshots())
            ]);

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    // ======================
    // AFFICHER PROJECT
    // ======================
    public function showProject($id) {
        $sql="SELECT * FROM projects WHERE id = :id";
        $db = config::getConnexion();
        $query = $db->prepare($sql);

        try {
            $query->execute(['id' => $id]);
            $project = $query->fetch();
            return $project;
        } catch (Exception $e) {
            die('Error: '. $e->getMessage());
        }
    }
}
?>
