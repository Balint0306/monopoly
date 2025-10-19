<?php

namespace App\Controllers;

use App\Models\Game;
use App\Services\DatabaseService;

class GameController {

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function getGameLogic(): ?Game {
        DatabaseService::initDatabase(); // Ensure table exists

        if (isset($_SESSION['game_id'])) {
            $gameStateJson = DatabaseService::getGame($_SESSION['game_id']);
            if ($gameStateJson) {
                return Game::fromJson($gameStateJson);
            }
        }

        // If no game in session or DB, create a new one.
        $playerNames = ['Player 1', 'AI'];
        $game = new Game($playerNames);
        $gameId = DatabaseService::createGame(json_encode($game));
        $_SESSION['game_id'] = $gameId;

        return $game;
    }

    public function newGame() {
        // Unset the old game ID to force creation of a new one
        unset($_SESSION['game_id']); 
        $game = $this->getGameLogic();
        $this->sendJsonResponse($game);
    }

    public function getGameState() {
        $game = $this->getGameLogic();
        if ($game) {
            $this->sendJsonResponse($game);
        } else {
            $this->sendJsonError('Could not load or create game.', 500);
        }
    }

    public function rollDice() {
        $game = $this->getGameLogic();
        if (!$game) {
            $this->sendJsonError('No active game found.', 404);
            return;
        }

        $player = $game->getCurrentPlayer();
        
        $die1 = rand(1, 6);
        $die2 = rand(1, 6);
        $totalRoll = $die1 + $die2;

        $player->move($totalRoll);
        
        $game->nextTurn();

        DatabaseService::saveGame($_SESSION['game_id'], json_encode($game));

        $this->sendJsonResponse($game);
    }

    private function sendJsonResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    private function sendJsonError(string $message, int $statusCode) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode(['error' => $message]);
    }
}
