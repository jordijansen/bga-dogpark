<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\Obedient;

class Dog89 extends DogCard {

    use Obedient;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Belgian Shepherd Dog (Groenendael)');
        $this->breeds = [BREED_PASTORAL];
        $this->costs = [RESOURCE_BALL => 1, RESOURCE_TOY => 2];
        $this->obedientResource = RESOURCE_BALL;
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

