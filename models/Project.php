<?php

class Project {

    private ?int $id;
    private ?string $nom;
    private ?string $developpeur;
    private ?string $date_creation; // format YYYY-MM-DD
    private ?string $categorie;
    private ?string $description;
    private ?string $image;
    private ?string $trailer;
    private ?int $developpeur_id;
    private ?int $age_recommande;
    private ?string $lieu;
    private ?string $lien_telechargement;
    private ?array $plateformes;   // stocké JSON en BD
    private ?array $tags;          // stocké JSON en BD
    private ?array $screenshots;   // stocké JSON en BD

    // ==========================
    // CONSTRUCTEUR
    // ==========================
    public function __construct(
        ?int $id,
        ?string $nom,
        ?string $developpeur,
        ?string $date_creation,
        ?string $categorie,
        ?string $description,
        ?string $image,
        ?string $trailer,
        ?int $developpeur_id,
        ?int $age_recommande = null,
        ?string $lieu = null,
        ?string $lien_telechargement = null,
        ?array $plateformes = [],
        ?array $tags = [],
        ?array $screenshots = []
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->developpeur = $developpeur;
        $this->date_creation = $date_creation;
        $this->categorie = $categorie;
        $this->description = $description;
        $this->image = $image;
        $this->trailer = $trailer;
        $this->developpeur_id = $developpeur_id;
        $this->age_recommande = $age_recommande;
        $this->lieu = $lieu;
        $this->lien_telechargement = $lien_telechargement;
        $this->plateformes = $plateformes;
        $this->tags = $tags;
        $this->screenshots = $screenshots;
    }

    // ==========================
    // AFFICHAGE (comme Book::show)
    // ==========================
    public function show() {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Développeur</th>
                <th>Date création</th>
                <th>Catégorie</th>
                <th>Description</th>
                <th>Image</th>
                <th>Trailer</th>
                <th>Développeur ID</th>
                <th>Âge recommandé</th>
                <th>Lieu</th>
                <th>Lien téléchargement</th>
                <th>Plateformes</th>
                <th>Tags</th>
              </tr>";

        echo "<tr>";
        echo "<td>{$this->id}</td>";
        echo "<td>{$this->nom}</td>";
        echo "<td>{$this->developpeur}</td>";
        echo "<td>{$this->date_creation}</td>";
        echo "<td>{$this->categorie}</td>";
        echo "<td>{$this->description}</td>";
        echo "<td>{$this->image}</td>";
        echo "<td>{$this->trailer}</td>";
        echo "<td>{$this->developpeur_id}</td>";
        echo "<td>{$this->age_recommande}</td>";
        echo "<td>{$this->lieu}</td>";
        echo "<td>{$this->lien_telechargement}</td>";
        echo "<td>" . implode(", ", $this->plateformes) . "</td>";
        echo "<td>" . implode(", ", $this->tags) . "</td>";
        echo "</tr>";

        echo "</table>";
    }

    // ==========================
    // GETTERS / SETTERS
    // ==========================

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }

    public function getNom(): ?string { return $this->nom; }
    public function setNom(?string $nom): void { $this->nom = $nom; }

    public function getDeveloppeur(): ?string { return $this->developpeur; }
    public function setDeveloppeur(?string $developpeur): void { $this->developpeur = $developpeur; }

    public function getDateCreation(): ?string { return $this->date_creation; }
    public function setDateCreation(?string $date): void { $this->date_creation = $date; }

    public function getCategorie(): ?string { return $this->categorie; }
    public function setCategorie(?string $categorie): void { $this->categorie = $categorie; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): void { $this->description = $description; }

    public function getImage(): ?string { return $this->image; }
    public function setImage(?string $image): void { $this->image = $image; }

    public function getTrailer(): ?string { return $this->trailer; }
    public function setTrailer(?string $trailer): void { $this->trailer = $trailer; }

    public function getDeveloppeurId(): ?int { return $this->developpeur_id; }
    public function setDeveloppeurId(?int $id): void { $this->developpeur_id = $id; }

    public function getAgeRecommande(): ?int { return $this->age_recommande; }
    public function setAgeRecommande(?int $age): void { $this->age_recommande = $age; }

    public function getLieu(): ?string { return $this->lieu; }
    public function setLieu(?string $lieu): void { $this->lieu = $lieu; }

    public function getLienTelechargement(): ?string { return $this->lien_telechargement; }
    public function setLienTelechargement(?string $lien): void { $this->lien_telechargement = $lien; }

    public function getPlateformes(): ?array { return $this->plateformes; }
    public function setPlateformes(?array $p): void { $this->plateformes = $p; }

    public function getTags(): ?array { return $this->tags; }
    public function setTags(?array $t): void { $this->tags = $t; }

    public function getScreenshots(): ?array { return $this->screenshots; }
    public function setScreenshots(?array $s): void { $this->screenshots = $s; }

}
?>
