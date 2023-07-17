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

$this->DOG_CARDS = [
    BASE_GAME => [
        1 => [ 'breed' => [DOG_TYPE_TOY] ],
        2 => [ 'breed' => [DOG_TYPE_TOY] ],
        3 => [ 'breed' => [DOG_TYPE_TOY] ],
        4 => [ 'breed' => [DOG_TYPE_TOY] ],
        5 => [ 'breed' => [DOG_TYPE_TOY] ],
        6 => [ 'breed' => [DOG_TYPE_TOY] ],
        7 => [ 'breed' => [DOG_TYPE_TOY] ],
        8 => [ 'breed' => [DOG_TYPE_TOY] ],
        9 => [ 'breed' => [DOG_TYPE_TOY] ],
        10 => [ 'breed' => [DOG_TYPE_TOY] ],
        11 => [ 'breed' => [DOG_TYPE_TOY] ],
        12 => [ 'breed' => [DOG_TYPE_TOY] ],
        13 => [ 'breed' => [DOG_TYPE_TOY] ],
        14 => [ 'breed' => [DOG_TYPE_TOY] ],
        15 => [ 'breed' => [DOG_TYPE_TOY] ],
        16 => [ 'breed' => [DOG_TYPE_TOY] ],
        17 => [ 'breed' => [DOG_TYPE_TOY] ],
        18 => [ 'breed' => [DOG_TYPE_TOY] ],
    ]
];



