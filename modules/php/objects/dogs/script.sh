for i in {001..163}
do
    echo """<?php
namespace objects\dogs;

use objects\DogCard;

class Dog${i} extends DogCard {

    public function __construct(\$dbCard)
    {
        parent::__construct(\$dbCard);
        \$this->name = '';
        \$this->breeds = [];
        \$this->costs = [];
        \$this->ability = $this->getAbility();
        \$this->abilityTitle = \$this->getAbilityTitle();
        \$this->abilityText = \$this->getAbilityText();
    }
}
""" > "Dog${i}.php"
done