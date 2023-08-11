<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\Eager;

class Dog82 extends DogCard {

    use Eager;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Picardy Sheepdog');
        $this->breeds = [BREED_PASTORAL];
        $this->costs = [RESOURCE_TREAT => 1];
        $this->eagerResource = RESOURCE_STICK;
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

