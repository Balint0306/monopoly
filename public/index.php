<?php

// Autoloader is required for both API and serving the page
require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\GameController;

// If an API action is requested, handle it.
if (isset($_GET['action'])) {
    $controller = new GameController();
    $action = $_GET['action'];

    switch ($action) {
        case 'newGame':
            $controller->newGame();
            break;
        case 'getGameState':
            $controller->getGameState();
            break;
        case 'rollDice':
            $controller->rollDice();
            break;
        default:
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Action not found']);
            break;
    }
    // Stop execution after handling the API request
    exit();
}

// If no action is specified, serve the main HTML file.
readfile(__DIR__ . '/index.html');
