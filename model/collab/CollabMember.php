<?php

class CollabMember {

    private ?int $id;
    private ?int $collab_id;
    private ?int $user_id;
    private ?string $role;

    public function __construct(
        ?int $id,
        ?int $collab_id,
        ?int $user_id,
        ?string $role = "membre"
    ) {
        $this->id = $id;
        $this->collab_id = $collab_id;
        $this->user_id = $user_id;
        $this->role = $role;
    }

    // GETTERS
    public function getId(): ?int { return $this->id; }
    public function getCollabId(): ?int { return $this->collab_id; }
    public function getUserId(): ?int { return $this->user_id; }
    public function getRole(): ?string { return $this->role; }

    // SETTERS
    public function setRole(string $role): void { $this->role = $role; }

}
?>
