<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\Playmate;

class Dog113 extends DogCard {

    use Playmate;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Norwich Terrier');
        $this->breeds = [BREED_TERRIER];
        $this->costs = [RESOURCE_STICK => 1, RESOURCE_BALL => 1];
        $this->playmateResource = RESOURCE_TREAT;
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

