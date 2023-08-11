<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\Playmate;

class Dog35 extends DogCard {

    use Playmate;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('German Longhaired Pointer');
        $this->breeds = [BREED_GUNDOG];
        $this->costs = [RESOURCE_STICK => 1, RESOURCE_BALL => 1];
        $this->playmateResource = RESOURCE_TOY;
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

