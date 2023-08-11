<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\Obedient;

class Dog91 extends DogCard {

    use Obedient;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Pyrenean Mountain Dog');
        $this->breeds = [BREED_PASTORAL];
        $this->costs = [RESOURCE_BALL => 2, RESOURCE_TOY => 1];
        $this->obedientResource = RESOURCE_TREAT;
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

