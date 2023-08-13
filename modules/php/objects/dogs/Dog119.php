<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\SocialButterfly;

class Dog119 extends DogCard {

    use SocialButterfly;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Dandie Dinmont Terrier');
        $this->breeds = [BREED_TERRIER];
        $this->costs = [RESOURCE_BALL => 1, RESOURCE_TOY => 1];
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

