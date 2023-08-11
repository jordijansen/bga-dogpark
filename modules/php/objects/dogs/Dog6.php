<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\Obedient;

class Dog6 extends DogCard {

    use Obedient;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Cavalier King Charles Spaniel');
        $this->breeds = [BREED_TOY];
        $this->costs = [RESOURCE_BALL => 1, RESOURCE_TOY => 2];
        $this->obedientResource = RESOURCE_STICK;
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

