<?php

class CollabSkillRequired {

    private ?int $id;
    private ?int $collab_id;
    private ?string $skill;

    public function __construct(
        ?int $id,
        ?int $collab_id,
        ?string $skill
    ) {
        $this->id = $id;
        $this->collab_id = $collab_id;
        $this->skill = $skill;
    }

    // GETTERS
    public function getId(): ?int { return $this->id; }
    public function getCollabId(): ?int { return $this->collab_id; }
    public function getSkill(): ?string { return $this->skill; }

}
?>
