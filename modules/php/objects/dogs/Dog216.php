<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\Crafty;
use objects\dogtraits\TreatLover;

class Dog216 extends DogCard {
    use Crafty;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Norfolk Terrier');
        $this->breeds = [BREED_TERRIER];
        $this->costs = [RESOURCE_TREAT => 1];
        $this->craftyResource = RESOURCE_TOY;
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

