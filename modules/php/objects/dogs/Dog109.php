<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\Obedient;

class Dog109 extends DogCard {

    use Obedient;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Bedlington Terrier');
        $this->breeds = [BREED_TERRIER];
        $this->costs = [RESOURCE_BALL => 1, RESOURCE_TREAT => 2];
        $this->obedientResource = RESOURCE_STICK;
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

