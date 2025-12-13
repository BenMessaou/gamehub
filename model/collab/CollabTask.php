<?php

class CollabTask {

    private ?int $id;
    private ?int $collab_id;
    private ?string $task;
    private ?bool $done;

    public function __construct(
        ?int $id,
        ?int $collab_id,
        ?string $task,
        ?bool $done = false
    ) {
        $this->id = $id;
        $this->collab_id = $collab_id;
        $this->task = $task;
        $this->done = $done;
    }

    // GETTERS
    public function getId(): ?int { return $this->id; }
    public function getCollabId(): ?int { return $this->collab_id; }
    public function getTask(): ?string { return $this->task; }
    public function isDone(): ?bool { return $this->done; }

    // SETTERS
    public function setDone(bool $done): void { $this->done = $done; }

}
?>
