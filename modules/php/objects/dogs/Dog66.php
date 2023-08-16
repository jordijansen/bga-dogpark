<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\SearchAndRescue;

class Dog66 extends DogCard {

    use SearchAndRescue;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Irish Wolfhound');
        $this->breeds = [BREED_HOUND];
        $this->costs = [RESOURCE_BALL => 1];
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

