<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\Slowpoke;
use objects\dogtraits\TreatLover;

class Dog315 extends DogCard {
    use Slowpoke;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Basset Hound');
        $this->breeds = [BREED_HOUND];
        $this->costs = [RESOURCE_BALL => 1, RESOURCE_TREAT => 1, RESOURCE_TOY => 1];
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

