<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\Eager;
use objects\dogtraits\Globetrotter;

class Dog409 extends DogCard {
    use Globetrotter;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Pudelpointer');
        $this->breeds = [BREED_GUNDOG];
        $this->costs = [RESOURCE_BALL => 1];
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

