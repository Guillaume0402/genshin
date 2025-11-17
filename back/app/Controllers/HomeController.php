<?php
require_once __DIR__.'/../Models/User.php';

class HomeController {
    public function index() {
        $user = new User(1, 'test@example.com', 'Jean');
        echo "Bienvenue " . $user->name . " sur l’API Genshin !";
    }
}
