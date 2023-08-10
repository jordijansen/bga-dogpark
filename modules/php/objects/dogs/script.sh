for i in {001..163}
do
    echo """<?php
namespace objects\dogs;

use objects\DogCard;

class Dog${i} extends DogCard {

    public function __construct(\$dbCard)
    {
        parent::__construct(\$dbCard);
    }
}
""" > "Dog${i}.php"
done