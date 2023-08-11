<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\Eager;

class Dog80 extends DogCard {

    use Eager;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Hungarian Kuvasz');
        $this->breeds = [BREED_PASTORAL];
        $this->costs = [RESOURCE_TREAT => 1];
        $this->eagerResource = RESOURCE_BALL;
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

