<?php
class Card {

    public int $id;
    public string $location;
    public int $location_arg;
    public /*int|null*/ $type;
    public /*int|null*/ $type_arg;

    public function __construct($dbCard)
    {
        $this->id = intval($dbCard['card_id'] ?? $dbCard['id']);
        $this->location = $dbCard['card_location'] ?? $dbCard['location'];
        $this->location_arg = intval($dbCard['card_location_arg'] ?? $dbCard['location_arg']);
        $this->type = array_key_exists('card_type', $dbCard) || array_key_exists('type', $dbCard) ? intval($dbCard['card_type'] ?? $dbCard['type']) : null;
        $this->type_arg = array_key_exists('card_type_arg', $dbCard) || array_key_exists('type_arg', $dbCard) ? intval($dbCard['card_type_arg'] ?? $dbCard['type_arg']) : null;
    }
}

class Token {

    public int $id;
    public string $location;
    public int $location_arg;
    public /*int|null*/ $type;
    public /*int|null*/ $type_arg;
    public function __construct($dbCard)
    {
        $this->id = intval($dbCard['card_id'] ?? $dbCard['id']);
        $this->location = $dbCard['card_location'] ?? $dbCard['location'];
        $this->location_arg = intval($dbCard['card_location_arg'] ?? $dbCard['location_arg']);
        $this->type = array_key_exists('card_type', $dbCard) || array_key_exists('type', $dbCard) ? $dbCard['card_type'] ?? $dbCard['type'] : null;
        $this->type_arg = array_key_exists('card_type_arg', $dbCard) || array_key_exists('type_arg', $dbCard) ? $dbCard['card_type_arg'] ?? $dbCard['type_arg'] : null;
    }
}