<?php

class CollabProject {

    private ?int $id;
    private ?int $owner_id;
    private ?string $titre;
    private ?string $description;
    private ?string $date_creation;
    private ?string $statut;
    private ?int $max_membres;
    private ?string $image;

    public function __construct(
        ?int $id,
        ?int $owner_id,
        ?string $titre,
        ?string $description,
        ?string $date_creation,
        ?string $statut = "ouvert",
        ?int $max_membres = 10,
        ?string $image = null
    ) {
        $this->id = $id;
        $this->owner_id = $owner_id;
        $this->titre = $titre;
        $this->description = $description;
        $this->date_creation = $date_creation;
        $this->statut = $statut;
        $this->max_membres = $max_membres;
        $this->image = $image;
    }

    // GETTERS
    public function getId(): ?int { return $this->id; }
    public function getOwnerId(): ?int { return $this->owner_id; }
    public function getTitre(): ?string { return $this->titre; }
    public function getDescription(): ?string { return $this->description; }
    public function getDateCreation(): ?string { return $this->date_creation; }
    public function getStatut(): ?string { return $this->statut; }
    public function getMaxMembres(): ?int { return $this->max_membres; }
    public function getImage(): ?string { return $this->image; }

    // SETTERS
    public function setStatut(string $statut): void { $this->statut = $statut; }
    public function setImage(?string $image): void { $this->image = $image; }

}
?>
