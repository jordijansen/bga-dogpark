<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\GoFetch;
use objects\dogtraits\TreatLover;

class Dog207 extends DogCard {
    use GoFetch;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Dachshund (Miniature Long Haired)');
        $this->breeds = [BREED_HOUND];
        $this->costs = [RESOURCE_STICK => 2, RESOURCE_TREAT => 1];
        $this->goFetchResource = RESOURCE_BALL;
        $this->goFetchBonusResource = RESOURCE_STICK;
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

