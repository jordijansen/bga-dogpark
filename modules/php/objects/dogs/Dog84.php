<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\GoFetch;

class Dog84 extends DogCard {

    use GoFetch;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Bearded Collie');
        $this->breeds = [BREED_PASTORAL];
        $this->costs = [RESOURCE_BALL => 2, RESOURCE_TOY => 1];
        $this->goFetchResource = RESOURCE_STICK;
        $this->goFetchBonusResource = RESOURCE_BALL;
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

