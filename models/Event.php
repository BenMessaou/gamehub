<?php
class Event
{
    private $id;
    private $user_id;
    private $title;
    private $description;
    private $eventType;
    private $platform;
    private $location;
    private $startDate;
    private $endDate;
    private $ticketPrice;
    private $availability;
    private $prizePool;
    private $imageURL;
    private $status;

    public function __construct(
        $id,
        $user_id,
        $title,
        $description,
        $eventType,
        $platform,
        $location,
        $startDate,
        $endDate,
        $ticketPrice = 0.00,
        $availability = null,
        $prizePool = 0.00,
        $imageURL = null,
        $status = 'pending'
    ) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->title = $title;
        $this->description = $description;
        $this->eventType = $eventType;
        $this->platform = $platform;
        $this->location = $location;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->ticketPrice = $ticketPrice;
        $this->availability = $availability;
        $this->prizePool = $prizePool;
        $this->imageURL = $imageURL;
        $this->status = $status;
    }

    // -------- GETTERS --------
    public function getId() { return $this->id; }
    public function getUserId() { return $this->user_id; }
    public function getTitle() { return $this->title; }
    public function getDescription() { return $this->description; }
    public function getEventType() { return $this->eventType; }
    public function getPlatform() { return $this->platform; }
    public function getLocation() { return $this->location; }
    public function getStartDate() { return $this->startDate; }
    public function getEndDate() { return $this->endDate; }
    public function getTicketPrice() { return $this->ticketPrice; }
    public function getAvailability() { return $this->availability; }
    public function getPrizePool() { return $this->prizePool; }
    public function getImageURL() { return $this->imageURL; }
    public function getStatus() { return $this->status; }

    // -------- SETTERS --------
    public function setUserId($user_id) { $this->user_id = $user_id; }
    public function setTitle($title) { $this->title = $title; }
    public function setDescription($description) { $this->description = $description; }
    public function setEventType($eventType) { $this->eventType = $eventType; }
    public function setPlatform($platform) { $this->platform = $platform; }
    public function setLocation($location) { $this->location = $location; }
    public function setStartDate($startDate) { $this->startDate = $startDate; }
    public function setEndDate($endDate) { $this->endDate = $endDate; }
    public function setTicketPrice($ticketPrice) { $this->ticketPrice = $ticketPrice; }
    public function setAvailability($availability) { $this->availability = $availability; }
    public function setPrizePool($prizePool) { $this->prizePool = $prizePool; }
    public function setImageURL($imageURL) { $this->imageURL = $imageURL; }
    public function setStatus($status) { $this->status = $status; }
}
?>