<?php

namespace App\Models;

use JsonSerializable;

class Space implements JsonSerializable {
    public string $name;
    public string $type;
    public ?int $price;
    public ?string $color;

    public function __construct(string $name, string $type, ?int $price = null, ?string $color = null) {
        $this->name = $name;
        $this->type = $type;
        $this->price = $price;
        $this->color = $color;
    }

    public function jsonSerialize(): array {
        return get_object_vars($this);
    }
}
