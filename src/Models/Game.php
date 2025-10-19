<?php

namespace App\Models;

use JsonSerializable;

class Game implements JsonSerializable {
    public array $players;
    public Board $board;
    public int $currentTurn = 0;
    public array $propertyOwnership;

    public function __construct(array $playerNames) {
        $this->board = new Board();
        $this->players = [];
        foreach ($playerNames as $name) {
            $this->players[] = new Player($name);
        }
        $this->propertyOwnership = array_fill(0, count($this->board->spaces), -1);
    }

    public function nextTurn(): void {
        $this->currentTurn = ($this->currentTurn + 1) % count($this->players);
    }

    public function getPlayer(int $playerIndex): ?Player {
        return $this->players[$playerIndex] ?? null;
    }

    public function getCurrentPlayer(): ?Player {
        return $this->getPlayer($this->currentTurn);
    }

    public function jsonSerialize(): array {
        return [
            'players' => $this->players,
            'board' => $this->board,
            'currentTurn' => $this->currentTurn,
            'propertyOwnership' => $this->propertyOwnership
        ];
    }

    public static function fromJson(string $json): self {
        $data = json_decode($json);

        // Create a new Game instance without calling the constructor
        $game = new self([]);

        $game->board = new Board(); // The board is always the same
        $game->currentTurn = $data->currentTurn;
        $game->propertyOwnership = $data->propertyOwnership;

        $game->players = [];
        foreach ($data->players as $playerData) {
            $player = new Player($playerData->name);
            $player->money = $playerData->money;
            $player->position = $playerData->position;
            $player->properties = $playerData->properties;
            $game->players[] = $player;
        }

        return $game;
    }
}
