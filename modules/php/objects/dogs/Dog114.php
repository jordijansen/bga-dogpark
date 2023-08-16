<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\RaringToGo;

class Dog114 extends DogCard {

    use RaringToGo;

    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Scottish Terrier');
        $this->breeds = [BREED_TERRIER];
        $this->costs = [RESOURCE_STICK => 2];
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

