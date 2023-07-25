<?php
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * dogpark implementation : © <Your name here> <Your email address here>
 * 
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * material.inc.php
 *
 * dogpark game material description
 *
 * Here, you can describe the material of your game with PHP variables.
 *   
 * This file is loaded in your game logic class constructor, ie these variables
 * are available everywhere in your game logic code.
 *
 */


/*

Example:

$this->card_types = array(
    1 => array( "card_name" => ...,
                ...
              )
);

*/

require_once(__DIR__.'/modules/php/Constants.inc.php');

$this->BREEDS = [
  BREED_TOY => clienttranslate('toy')
];

$this->RESOURCES = [
    RESOURCE_STICK => clienttranslate('stick'),
    RESOURCE_BALL => clienttranslate('ball'),
    RESOURCE_TREAT => clienttranslate('treat'),
    RESOURCE_TOY => clienttranslate('toy'),
];

$this->DOG_CARDS = [
    BASE_GAME => [
        1 => [  ],
        2 => [  ],
        3 => [  ],
        4 => [  ],
        5 => [  ],
        6 => [  ],
        7 => [  ],
        8 => [  ],
        9 => [  ],
        10 => [ ],
        11 => [ ],
        12 => [ ],
        13 => [ ],
        14 => [ ],
        15 => [ ],
        16 => [ ],
        17 => [ ],
        18 => [ ],
    ]
];



