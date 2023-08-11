<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\Obedient;

class Dog110 extends DogCard {

    use Obedient;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Skye Terrier');
        $this->breeds = [BREED_TERRIER];
        $this->costs = [RESOURCE_BALL => 2, RESOURCE_TREAT => 1];
        $this->obedientResource = RESOURCE_TOY;
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

