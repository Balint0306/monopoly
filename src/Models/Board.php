<?php

namespace App\Models;

use JsonSerializable;

class Board implements JsonSerializable {
    public array $spaces;

    public function __construct() {
        $this->spaces = $this->createBoardSpaces();
    }

    private function createBoardSpaces(): array {
        return [
            new Space("Go", "corner"),
            new Space("Mediterranean Avenue", "property", 60, "brown"),
            new Space("Community Chest", "community-chest"),
            new Space("Baltic Avenue", "property", 60, "brown"),
            new Space("Income Tax", "tax", 200),
            new Space("Reading Railroad", "railroad", 200),
            new Space("Oriental Avenue", "property", 100, "light-blue"),
            new Space("Chance", "chance"),
            new Space("Vermont Avenue", "property", 100, "light-blue"),
            new Space("Connecticut Avenue", "property", 120, "light-blue"),
            new Space("Jail", "corner"),
            new Space("St. Charles Place", "property", 140, "pink"),
            new Space("Electric Company", "utility", 150),
            new Space("States Avenue", "property", 140, "pink"),
            new Space("Virginia Avenue", "property", 160, "pink"),
            new Space("Pennsylvania Railroad", "railroad", 200),
            new Space("St. James Place", "property", 180, "orange"),
            new Space("Community Chest", "community-chest"),
            new Space("Tennessee Avenue", "property", 180, "orange"),
            new Space("New York Avenue", "property", 200, "orange"),
            new Space("Free Parking", "corner"),
            new Space("Kentucky Avenue", "property", 220, "red"),
            new Space("Chance", "chance"),
            new Space("Indiana Avenue", "property", 220, "red"),
            new Space("Illinois Avenue", "property", 240, "red"),
            new Space("B. & O. Railroad", "railroad", 200),
            new Space("Atlantic Avenue", "property", 260, "yellow"),
            new Space("Ventnor Avenue", "property", 260, "yellow"),
            new Space("Water Works", "utility", 150),
            new Space("Marvin Gardens", "property", 280, "yellow"),
            new Space("Go to Jail", "corner"),
            new Space("Pacific Avenue", "property", 300, "green"),
            new Space("North Carolina Avenue", "property", 300, "green"),
            new Space("Community Chest", "community-chest"),
            new Space("Pennsylvania Avenue", "property", 320, "green"),
            new Space("Short Line", "railroad", 200),
            new Space("Chance", "chance"),
            new Space("Park Place", "property", 350, "dark-blue"),
            new Space("Luxury Tax", "tax", 100),
            new Space("Boardwalk", "property", 400, "dark-blue"),
        ];
    }

    public function jsonSerialize(): array {
        return get_object_vars($this);
    }
}
