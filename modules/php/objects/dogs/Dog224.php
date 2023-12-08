<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\GoFetch;
use objects\dogtraits\TreatLover;

class Dog224 extends DogCard {
    use GoFetch;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('German Spitz (Mittel)');
        $this->breeds = [BREED_UTILITY];
        $this->costs = [RESOURCE_BALL => 2, RESOURCE_TREAT => 1];
        $this->goFetchResource = RESOURCE_STICK;
        $this->goFetchBonusResource = RESOURCE_BALL;
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

