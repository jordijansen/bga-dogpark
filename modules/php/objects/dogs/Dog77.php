<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\Crafty;

class Dog77 extends DogCard {

    use Crafty;

    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Turkish Kangal Dog');
        $this->breeds = [BREED_PASTORAL];
        $this->costs = [RESOURCE_BALL => 1];
        $this->craftyResource = RESOURCE_STICK;
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

