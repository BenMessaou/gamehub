<?php

class Event {

    private ?int $id;
    private ?string $title;
    private ?string $description;
    private ?string $start_date;
    private ?string $end_date;
    private ?string $location;
    private ?bool $is_online;
    private ?int $capacity;
    private ?int $reserved_count;
    private ?string $banner;
    private ?string $status;
    private ?string $created_at;
    private ?string $updated_at;

    // ==========================
    // CONSTRUCTEUR
    // ==========================
    public function __construct(
        ?int $id,
        ?string $title,
        ?string $description,
        ?string $start_date,
        ?string $end_date,
        ?string $location,
        ?bool $is_online = false,
        ?int $capacity = 0,
        ?int $reserved_count = 0,
        ?string $banner = null,
        ?string $status = "active",
        ?string $created_at = null,
        ?string $updated_at = null
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->location = $location;
        $this->is_online = $is_online;
        $this->capacity = $capacity;
        $this->reserved_count = $reserved_count;
        $this->banner = $banner;
        $this->status = $status;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    // ==========================
    // MÉTHODE SHOW (OPTIONNELLE)
    // ==========================
    public function show() {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr>
                <th>ID</th>
                <th>Titre</th>
                <th>Description</th>
                <th>Date Début</th>
                <th>Date Fin</th>
                <th>Lieu</th>
                <th>En ligne ?</th>
                <th>Capacité</th>
                <th>Réservées</th>
                <th>Banner</th>
                <th>Status</th>
                <th>Création</th>
                <th>MAJ</th>
              </tr>";

        echo "<tr>";
        echo "<td>{$this->id}</td>";
        echo "<td>{$this->title}</td>";
        echo "<td>{$this->description}</td>";
        echo "<td>{$this->start_date}</td>";
        echo "<td>{$this->end_date}</td>";
        echo "<td>{$this->location}</td>";
        echo "<td>" . ($this->is_online ? "Oui" : "Non") . "</td>";
        echo "<td>{$this->capacity}</td>";
        echo "<td>{$this->reserved_count}</td>";
        echo "<td>{$this->banner}</td>";
        echo "<td>{$this->status}</td>";
        echo "<td>{$this->created_at}</td>";
        echo "<td>{$this->updated_at}</td>";
        echo "</tr>";

        echo "</table>";
    }

    // ==========================
    // GETTERS / SETTERS
    // ==========================

    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }

    public function getTitle(): ?string { return $this->title; }
    public function setTitle(?string $title): void { $this->title = $title; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): void { $this->description = $description; }

    public function getStartDate(): ?string { return $this->start_date; }
    public function setStartDate(?string $start_date): void { $this->start_date = $start_date; }

    public function getEndDate(): ?string { return $this->end_date; }
    public function setEndDate(?string $end_date): void { $this->end_date = $end_date; }

    public function getLocation(): ?string { return $this->location; }
    public function setLocation(?string $location): void { $this->location = $location; }

    public function isOnline(): ?bool { return $this->is_online; }
    public function setIsOnline(?bool $is_online): void { $this->is_online = $is_online; }

    public function getCapacity(): ?int { return $this->capacity; }
    public function setCapacity(?int $capacity): void { $this->capacity = $capacity; }

    public function getReservedCount(): ?int { return $this->reserved_count; }
    public function setReservedCount(?int $reserved_count): void { $this->reserved_count = $reserved_count; }

    public function getBanner(): ?string { return $this->banner; }
    public function setBanner(?string $banner): void { $this->banner = $banner; }

    public function getStatus(): ?string { return $this->status; }
    public function setStatus(?string $status): void { $this->status = $status; }

    public function getCreatedAt(): ?string { return $this->created_at; }
    public function setCreatedAt(?string $created_at): void { $this->created_at = $created_at; }

    public function getUpdatedAt(): ?string { return $this->updated_at; }
    public function setUpdatedAt(?string $updated_at): void { $this->updated_at = $updated_at; }

}

?>
