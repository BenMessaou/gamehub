<?php

require_once __DIR__ . '/../../app/models/FeedbackModel.php';

class FeedbackController {

    public function index() {
        $model = new FeedbackModel();
        $avis = $model->getAll();
        require_once __DIR__ . '/../../public/avis.php';
    }
}
