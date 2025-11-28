<?php

class CollabMessage {

    private ?int $id;
    private ?int $collab_id;
    private ?int $user_id;
    private ?string $message;
    private ?string $date_message;

    public function __construct(
        ?int $id,
        ?int $collab_id,
        ?int $user_id,
        ?string $message,
        ?string $date_message = null
    ) {
        $this->id = $id;
        $this->collab_id = $collab_id;
        $this->user_id = $user_id;
        $this->message = $message;
        $this->date_message = $date_message;
    }

    // GETTERS
    public function getId(): ?int { return $this->id; }
    public function getCollabId(): ?int { return $this->collab_id; }
    public function getUserId(): ?int { return $this->user_id; }
    public function getMessage(): ?string { return $this->message; }
    public function getDateMessage(): ?string { return $this->date_message; }

}
?>

