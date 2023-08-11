<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\Playmate;

class Dog64 extends DogCard {

    use Playmate;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Dachshund (Miniature Wire Haired)');
        $this->breeds = [BREED_HOUND];
        $this->costs = [RESOURCE_STICK => 1, RESOURCE_BALL => 1];
        $this->playmateResource = RESOURCE_TREAT;
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

