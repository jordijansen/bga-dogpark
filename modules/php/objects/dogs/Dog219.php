<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\TreatLover;
use objects\dogtraits\WellTrained;

class Dog219 extends DogCard {
    use WellTrained;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Welsh Terrier');
        $this->breeds = [BREED_TERRIER];
        $this->costs = [RESOURCE_BALL => 1, RESOURCE_TOY => 1];
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

