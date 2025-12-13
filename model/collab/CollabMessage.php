<?php

class CollabMessage {

    private ?int $id;
    private ?int $collab_id;
    private ?int $user_id;
    private ?string $message;
    private ?string $date_message;
    private ?string $audio_path;
    private ?int $audio_duration;

    public function __construct(
        ?int $id,
        ?int $collab_id,
        ?int $user_id,
        ?string $message,
        ?string $date_message = null,
        ?string $audio_path = null,
        ?int $audio_duration = null
    ) {
        $this->id = $id;
        $this->collab_id = $collab_id;
        $this->user_id = $user_id;
        $this->message = $message;
        $this->date_message = $date_message;
        $this->audio_path = $audio_path;
        $this->audio_duration = $audio_duration;
    }

    // GETTERS
    public function getId(): ?int { return $this->id; }
    public function getCollabId(): ?int { return $this->collab_id; }
    public function getUserId(): ?int { return $this->user_id; }
    public function getMessage(): ?string { return $this->message; }
    public function getDateMessage(): ?string { return $this->date_message; }
    public function getAudioPath(): ?string { return $this->audio_path; }
    public function getAudioDuration(): ?int { return $this->audio_duration; }

    // SETTERS
    public function setAudioPath(?string $audio_path): void { $this->audio_path = $audio_path; }
    public function setAudioDuration(?int $audio_duration): void { $this->audio_duration = $audio_duration; }

}
?>

