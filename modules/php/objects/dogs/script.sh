for i in {301..329}
do
    echo """<?php
namespace objects\dogs;

use objects\DogCard;
use objects\dogtraits\TreatLover;

class Dog${i} extends DogCard {
    use TreatLover;
    public function __construct(\$dbCard)
    {
        parent::__construct(\$dbCard);
        \$this->name = clienttranslate('Russian Black Terrier');
        \$this->breeds = [BREED_WORKING];
        \$this->costs = [RESOURCE_STICK => 2];
        \$this->eagerResource = RESOURCE_TOY;
        \$this->ability = \$this->getAbility();
        \$this->abilityTitle = \$this->getAbilityTitle();
        \$this->abilityText = \$this->getAbilityText();
    }
}
""" > "Dog${i}.php"
done