<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\SocialButterfly;

class Dog69 extends DogCard {
    use SocialButterfly;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Basset Griffon Vendeen (Petit)');
        $this->breeds = [BREED_HOUND];
        $this->costs = [RESOURCE_BALL => 1, RESOURCE_TOY => 1];
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

