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
    BREED_GUNDOG     => clienttranslate('gundog'),
    BREED_HOUND      => clienttranslate('hound'),
    BREED_PASTORAL   => clienttranslate('pastoral'),
    BREED_TERRIER    => clienttranslate('terrier'),
    BREED_TOY        => clienttranslate('toy'),
    BREED_UTILITY    => clienttranslate('utility'),
    BREED_WORKING    => clienttranslate('working')
];

$this->RESOURCES = [
    RESOURCE_STICK  => clienttranslate('stick'),
    RESOURCE_BALL   => clienttranslate('ball'),
    RESOURCE_TREAT  => clienttranslate('treat'),
    RESOURCE_TOY    => clienttranslate('toy'),
];

$this->BREED_EXPERT_CARDS = [
    1 => BREED_GUNDOG   ,
    2 => BREED_HOUND    ,
    3 => BREED_PASTORAL ,
    4 => BREED_TERRIER  ,
    5 => BREED_TOY      ,
    6 => BREED_UTILITY  ,
    7 => BREED_WORKING  ,
];

$this->FORECAST_CARDS = [
    BASE_GAME => [
        1 => [],
        2 => [],
        3 => [],
        4 => [],
        5 => [],
        6 => [],
        7 => [],
        8 => [],
        9 => [],
        10 => [],
        11 => [],
    ]
];

$this->PARK_LOCATIONS = [
    0 =>  ['bonus' =>  [], 'nextLocations' => [1]],
    1 =>  ['bonus' =>  [RESOURCE_STICK], 'nextLocations' => [2]],
    2 =>  ['bonus' =>  [RESOURCE_BALL], 'nextLocations' => [3]],
    3 =>  ['bonus' =>  [RESOURCE_TOY], 'nextLocations' => [4]],
    4 =>  ['bonus' =>  [RESOURCE_TREAT], 'nextLocations' => [6]],
    5 =>  ['bonus' =>  [RESOURCE_STICK], 'nextLocations' => [7]],
    6 =>  ['bonus' =>  [SCOUT], 'nextLocations' => [5, 8]],
    7 =>  ['bonus' =>  [REPUTATION], 'nextLocations' => [9]],
    8 =>  ['bonus' =>  [RESOURCE_BALL], 'nextLocations' => [10]],
    9 =>  ['bonus' =>  [SCOUT], 'nextLocations' => [11]],
    10 => ['bonus' =>  [SWAP], 'nextLocations' => [12]],
    11 => ['bonus' =>  [REPUTATION], 'nextLocations' => [13]],
    12 => ['bonus' =>  [RESOURCE_TOY], 'nextLocations' => [14]],
    13 => ['bonus' =>  [RESOURCE_BALL, RESOURCE_BALL], 'nextLocations' => [91,92,93]],
    14 => ['bonus' =>  [RESOURCE_TREAT], 'nextLocations' => [15]],
    15 => ['bonus' =>  [RESOURCE_STICK, RESOURCE_STICK], 'nextLocations' => [91,92,93]],

    // Leaving the park spots
    91 => ['bonus' => [], 'nextLocations' => []], // Only in 4 player games
    92 => ['bonus' => [], 'nextLocations' => []],
    93 => ['bonus' => [], 'nextLocations' => []],
    // Last out of the park
    94 => ['bonus' => [], 'nextLocations' => []]
];

$this->LOCATION_BONUS_CARDS = [
    LOCATION_BONUS_PLENTIFUL => [
        1 => [],
        2 => [],
        3 => [],
        4 => [],
        5 => [],
        6 => [],
        7 => [],
        8 => [],
    ],
    LOCATION_BONUS_REROUTED => [
        9 => [5 => [BLOCK], 6 => [REPUTATION], 9 => [SWAP]],
        10 => [6 => [REPUTATION], 8 => [BLOCK], 12 => [REPUTATION]],
        11 => [6 => [BLOCK], 7 => [RESOURCE_BALL], 12 => [RESOURCE_STICK]],
        12 => [5 => [RESOURCE_BALL], 6 => [SWAP], 13 => [BLOCK]],
        13 => [6 => [SWAP], 8 => [RESOURCE_STICK], 15 => [BLOCK]],
        14 => [6 => [SWAP], 11 => [RESOURCE_STICK], 14 => [RESOURCE_BALL]],
        15 => [6 => [RESOURCE_TREAT], 11 => [REPUTATION], 12 => [BLOCK]],
        16 => [6 => [RESOURCE_TOY], 10 => [SCOUT], 14 => [RESOURCE_TREAT]],
    ]
];

$this->OBJECTIVE_CARDS = [
    OBJECTIVE_EXPERIENCED => [
        1 => [],
        2 => [],
        3 => [],
        4 => [],
        5 => []
    ],
    OBJECTIVE_STANDARD => [
        6 => [],
        7 => [],
        8 => [],
        9 => [],
        10 => []
    ],
];
