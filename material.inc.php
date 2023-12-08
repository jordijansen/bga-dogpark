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
    BREED_GUNDOG     => clienttranslate('Gundog'),
    BREED_HOUND      => clienttranslate('Hound'),
    BREED_PASTORAL   => clienttranslate('Pastoral'),
    BREED_TERRIER    => clienttranslate('Terrier'),
    BREED_TOY        => clienttranslate('Toy'),
    BREED_UTILITY    => clienttranslate('Utility'),
    BREED_WORKING    => clienttranslate('Working')
];

$this->RESOURCES = [
    RESOURCE_STICK  => clienttranslate('stick'),
    RESOURCE_BALL   => clienttranslate('ball'),
    RESOURCE_TREAT  => clienttranslate('treat'),
    RESOURCE_TOY    => clienttranslate('toy'),
];

$this->ABILITIES = [
    EAGER => clienttranslate('Eager'),
    CRAFTY => clienttranslate('Crafty'),
    GO_FETCH => clienttranslate('Go Fetch!'),
    OBEDIENT => clienttranslate('Obedient'),
    PLAYMATE => clienttranslate('Playmate'),
    SOCIAL_BUTTERFLY => clienttranslate('Social Butterfly'),
    SEARCH_AND_RESCUE => clienttranslate('Search and Rescue'),
    PACK_DOG => clienttranslate('Pack Dog'),
    RARING_TO_GO => clienttranslate('Raring to Go'),
    SOCIABLE => clienttranslate('Sociable'),
    BALL_HOG => clienttranslate('Ball Hog'),
    STICK_CHASER => clienttranslate('Stick Chaser'),
    TOY_COLLECTOR => clienttranslate('Toy Collector'),
    TREAT_LOVER => clienttranslate('Treat Lover'),
    // EUROPEAN
    LONE_WOLF => clienttranslate('Lone Wolf'),
    WELL_TRAINED => clienttranslate('Well Trained')
];

$this->SPECIAL_ICONS = [
    SWAP => clienttranslate('swap'),
    SCOUT => clienttranslate('scout'),
    WALKED => clienttranslate('walked'),
    REPUTATION => clienttranslate('reputation')
];

$this->OBJECTIVE_CARD_TYPES = [
    OBJECTIVE_STANDARD => clienttranslate('standard'),
    OBJECTIVE_EXPERIENCED => clienttranslate('experienced'),
    OBJECTIVE_SOLO => clienttranslate('solo')
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
        1 =>    ['description' => clienttranslate('During <b>SELECTION<b>, gain 1 Location Bonus for each <b>GUNDOG</b> in your Kennel or on your Lead. (Excludes <icon-swap> and <icon-scout>).')],
        2 =>    ['description' => clienttranslate('During <b>HOME TIME<b>, gain 2 <icon-all-resources> for each <b>TERRIER</b> in your Kennel or on your Lead.')],
        3 =>    ['description' => clienttranslate('During <b>SELECTION</b>, when you place a <b>PASTORAL</b> dog on the Lead, you may place another dog without paying the walking cost.')],
        4 =>    ['description' => clienttranslate('During <b>HOME TIME</b>, gain 1 <icon-reputation> and 1 <icon-all-resources> for each <b>WORKING</b> dog in your Kennel or on your Lead.')],
        5 =>    ['description' => clienttranslate('During <b>HOME TIME</b>, gain 3 <icon-reputation> for each <b>TOY</b> dog in your Kennel or on your Lead.')],
        6 =>    ['description' => clienttranslate('During <b>SELECTION</b>, gain 2 <icon-reputation> for each <b>HOUND</b> you place on the Lead.')],
        7 =>    ['description' => clienttranslate('During <b>THIS ROUND</b>, gain 1 <icon-reputation> and 1 <icon-all-resources> for each <b>UTILITY</b> dog placed into your Kennel.')],
        8 =>    ['description' => clienttranslate('During <b>HOME TIME</b>, Dogs without <icon-walked> lose 2 <icon-reputation> instead of 1 <icon-reputation>.')],
        9 =>    ['description' => clienttranslate('During <b>HOME TIME</b>, Dogs without <icon-walked> do not lose 1 <icon-reputation>.')],
        10 =>   ['description' => clienttranslate('During <b>THIS ROUND</b>, whenever you <icon-swap>, place <icon-walked> on the newly acquired Dog in your Kennel.')],
        11 =>   ['description' => clienttranslate('During <b>THIS ROUND</b>, you may walk 4 Dogs if you are able to pay the required walking cost. <i>This card can only be placed in the 2nd, 3rd, or 4th position on the round tracker.</i>')],
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
        1 => [5 => [RESOURCE_STICK], 6 => [REPUTATION], 9 => [SWAP]],
        2 => [6 => [REPUTATION], 8 => [RESOURCE_BALL], 12 => [REPUTATION]],
        3 => [6 => [REPUTATION], 7 => [RESOURCE_BALL], 12 => [RESOURCE_STICK]],
        4 => [5 => [RESOURCE_BALL], 6 => [SWAP]],
        5 => [6 => [SWAP], 8 => [RESOURCE_STICK]],
        6 => [6 => [SWAP], 11 => [RESOURCE_STICK], 14 => [RESOURCE_BALL]],
        7 => [6 => [RESOURCE_TREAT], 11 => [REPUTATION, REPUTATION]],
        8 => [6 => [RESOURCE_TOY], 10 => [SCOUT]],
    ],
    LOCATION_BONUS_REROUTED => [
        9 =>  [5 => [BLOCK], 6 => [REPUTATION], 9 => [SWAP]],
        10 => [6 => [REPUTATION], 8 => [BLOCK], 12 => [REPUTATION]],
        11 => [6 => [BLOCK], 7 => [RESOURCE_BALL], 12 => [RESOURCE_STICK]],
        12 => [5 => [RESOURCE_BALL], 6 => [SWAP], 13 => [BLOCK]],
        13 => [6 => [SWAP], 8 => [RESOURCE_STICK], 15 => [BLOCK]],
        14 => [6 => [SWAP], 11 => [RESOURCE_STICK], 14 => [RESOURCE_BALL]],
        15 => [6 => [RESOURCE_TREAT], 11 => [REPUTATION], 12 => [BLOCK]],
        16 => [6 => [RESOURCE_TOY], 10 => [SCOUT], 14 => [BLOCK]],
    ]
];

$this->OBJECTIVE_CARDS = [
    1 =>    ['type' => OBJECTIVE_EXPERIENCED, 'name' => clienttranslate('Four of a Kind'), 'description' => clienttranslate('During <b>FINAL SCORING</b>, gain 7 <icon-reputation> if you have at least 4 Dogs of 1 breed category in your kennel, e.g. 4 GUNDOGS. <i>You may only score this objective once. Only for use in a 4 player game.</i>')],
    2 =>    ['type' => OBJECTIVE_EXPERIENCED, 'name' => clienttranslate('Super Stamina'), 'description' => clienttranslate('During <b>FINAL SCORING</b>, gain 7 <icon-reputation> if you have 3 different Dogs who have at least 2 <icon-walked> each.')],
    3 =>    ['type' => OBJECTIVE_EXPERIENCED, 'name' => clienttranslate('Seasoned Walker'), 'description' => clienttranslate('During <b>FINAL SCORING</b>, gain 7 <icon-reputation> if you have at least 10 <icon-walked> across your Kennel.')],
    4 =>    ['type' => OBJECTIVE_EXPERIENCED, 'name' => clienttranslate('Expert Collector'), 'description' => clienttranslate('During <b>FINAL SCORING</b>, gain 7 <icon-reputation> if you have won at least 3 Breed Expert awards (4 player game) or 4 Breed Expert awards (2 and 3 player game). <i>Any awards where the victory is shared will still count towards this objective.</i>')],
    5 =>    ['type' => OBJECTIVE_EXPERIENCED, 'name' => clienttranslate('Well Walked Kennel'), 'description' => clienttranslate('During <b>FINAL SCORING</b>, gain 7 <icon-reputation> if you have <icon-walked> on at least 7 different Dogs.')],
    6 =>    ['type' => OBJECTIVE_STANDARD, 'name' => clienttranslate('High Energy'), 'description' => clienttranslate('During <b>FINAL SCORING</b>, gain 3 <icon-reputation> if you have 2 different Dogs who have at least 2 <icon-walked> each.')],
    7 =>    ['type' => OBJECTIVE_STANDARD, 'name' => clienttranslate('Three of a Kind'), 'description' => clienttranslate('During <b>FINAL SCORING</b>, gain 3 <icon-reputation> if you have at least 3 Dogs of 1 breed category in your Kennel, e.g. 3 HOUNDS. <i>You may only score this objective once.</i>')],
    8 =>    ['type' => OBJECTIVE_STANDARD, 'name' => clienttranslate('Kennel Diversity'), 'description' => clienttranslate('During <b>FINAL SCORING</b>, gain 3 <icon-reputation> if you have at least 1 Dog of 4 breed categories in your Kennel, e.g. 1 GUNDOG, 1 HOUND, 1 TERRIER ,and 1 PASTORAL Dog. <i>You may only score this objective once.</i>')],
    9 =>    ['type' => OBJECTIVE_STANDARD, 'name' => clienttranslate('Capable Collector'), 'description' => clienttranslate('During <b>FINAL SCORING</b>, gain 3 <icon-reputation> if you have won at least 2 Breed Expert awards (4 player game) or 3 Breed Expert awards (2 and 3 player game). <i>Any awards where the victory is shared will still count towards this objective.</i>')],
    10 =>   ['type' => OBJECTIVE_STANDARD, 'name' => clienttranslate('Walked Kennel'), 'description' => clienttranslate('During <b>FINAL SCORING</b>, gain 3 <icon-reputation> if you have <icon-walked> on at least 6 different Dogs.')],

    20 =>   ['type' => OBJECTIVE_SOLO, 'name' => clienttranslate('Novice Solo Objective'), 'description' => clienttranslate('To win the solo game, you must complete the requirements on this objective.<p><b>Stars:</b> 0</p><p><b>Requirements:</b></p> <p>4 <icon-all-resources></p><p>8 <icon-walked></p><p>Win at least 3 Breed Expert awards.</p>')],
    21 =>   ['type' => OBJECTIVE_SOLO, 'name' => clienttranslate('Intermediate Solo Objective'), 'description' => clienttranslate('To win the solo game, you must complete the requirements on this objective.<p><b>Stars:</b> 2</p><p><b>Requirements:</b></p> <p>1 <icon-stick> 1 <icon-ball> 1 <icon-toy> 1 <icon-treat></p><p>9 <icon-walked></p><p>Win at least 3 Breed Expert awards, 1 outright</p>')],
    22 =>   ['type' => OBJECTIVE_SOLO, 'name' => clienttranslate('Expert Solo Objective'), 'description' => clienttranslate('To win the solo game, you must complete the requirements on this objective.<p><b>Stars:</b> 4</p><p><b>Requirements:</b></p> <p>8 <icon-all-resources></p><p>9 <icon-walked></p><p>Win at least 3 Breed Expert awards, 2 outright</p>')],
    23 =>   ['type' => OBJECTIVE_SOLO, 'name' => clienttranslate('Legend Solo Objective'), 'description' => clienttranslate('To win the solo game, you must complete the requirements on this objective.<p><b>Stars:</b> 6</p><p><b>Requirements:</b></p> <p>2 <icon-stick> 2 <icon-ball> 2 <icon-toy> 2 <icon-treat></p><p>10 <icon-walked></p><p>Win at least 3 Breed Expert awards, 3 outright</p>')],
];
