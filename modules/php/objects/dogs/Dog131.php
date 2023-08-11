<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\Obedient;

class Dog131 extends DogCard {

    use Obedient;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Shih Tzu');
        $this->breeds = [BREED_UTILITY];
        $this->costs = [RESOURCE_BALL => 2, RESOURCE_TOY => 1];
        $this->obedientResource = RESOURCE_TOY;
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

