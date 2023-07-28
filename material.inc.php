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
        9 => [],
        10 => [],
        11 => [],
        12 => [],
        13 => [],
        14 => [],
        15 => [],
        16 => [],
    ]
];

$this->DOG_CARDS = [
    BASE_GAME => [
        1 => [ 'name' => clienttranslate('Australian Silky Terrier'), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('') ],
        2 => [ 'name' => clienttranslate('Lowchen (Little Lion Dog)'), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('') ],
        3 => [ 'name' => clienttranslate('Coton De Tulear'), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('') ],
        4 => [ 'name' => clienttranslate('Havanese'), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('') ],
        5 => [ 'name' => clienttranslate('English Toy Terrier (Black & Tan)'), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('') ],
        6 => [ 'name' => clienttranslate('Cavalier King Charles Spaniel'), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('') ],
        7 => [ 'name' => clienttranslate('Maltese'), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('') ],
        8 => [ 'name' => clienttranslate('Chihuahua (Long Coat)'), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('') ],
        9 => [ 'name' => clienttranslate('Pug'), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('') ],
        10 => ['name' => clienttranslate('Miniature Pinscher'), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('') ],
        11 => ['name' => clienttranslate('Pekingese'), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('') ],
        12 => ['name' => clienttranslate('Affenpinscher'), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('') ],
        13 => ['name' => clienttranslate('Bichon Frise'), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('') ],
        14 => ['name' => clienttranslate('Chinese Crested'), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('') ],
        15 => ['name' => clienttranslate('Italian Greyhound'), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('') ],
        16 => ['name' => clienttranslate('Papillon'), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('') ],
        17 => ['name' => clienttranslate('Griffon Bruxellois'), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('') ],
        18 => ['name' => clienttranslate('Japanese Chin'), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('') ],
    ]
];



