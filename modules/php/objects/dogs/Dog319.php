<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\Slowpoke;
use objects\dogtraits\TreatLover;

class Dog319 extends DogCard {
    use Slowpoke;
    public function __construct($dbCard)
    {
        parent::__construct($dbCard);
        $this->name = clienttranslate('Bull Terrier');
        $this->breeds = [BREED_TERRIER];
        $this->costs = [RESOURCE_BALL => 1, RESOURCE_TREAT => 1, RESOURCE_TOY => 1];
        $this->ability = $this->getAbility();
        $this->abilityTitle = $this->getAbilityTitle();
        $this->abilityText = $this->getAbilityText();
    }
}

