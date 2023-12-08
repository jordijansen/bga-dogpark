<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\Crafty;
use objects\dogtraits\TreatLover;

class Dog223 extends DogCard {
    use Crafty;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Poodle (Miniature)');
        $this->breeds = [BREED_UTILITY];
        $this->costs = [RESOURCE_TREAT => 1];
        $this->craftyResource = RESOURCE_TOY;
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

