<?php

namespace App\Models;

use JsonSerializable;

class Player implements JsonSerializable {
    public string $name;
    public int $money = 1500;
    public int $position = 0;
    public array $properties = [];

    public function __construct(string $name) {
        $this->name = $name;
    }

    public function move(int $steps): void {
        $this->position = ($this->position + $steps) % 40; // Assuming a board with 40 spaces
    }

    public function addMoney(int $amount): void {
        $this->money += $amount;
    }

    public function subtractMoney(int $amount): bool {
        if ($this->money >= $amount) {
            $this->money -= $amount;
            return true;
        }
        return false;
    }

    public function jsonSerialize(): array {
        return get_object_vars($this);
    }
}
