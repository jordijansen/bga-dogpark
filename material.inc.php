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

$this->DOG_CARDS = [
    1   => [ 'breeds' => [BREED_TOY], 'costs' => [RESOURCE_STICK => 1, RESOURCE_BALL => 1, RESOURCE_TREAT => 1], 'name' => clienttranslate('Australian Silky Terrier'), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('') ],
    2   => [ 'breeds' => [BREED_TOY], 'costs' => [RESOURCE_BALL => 2], 'name' => clienttranslate('Lowchen (Little Lion Dog)'), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('') ],
    3   => [ 'breeds' => [BREED_TOY], 'costs' => [RESOURCE_BALL => 1], 'name' => clienttranslate('Coton De Tulear'), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('') ],
    4   => [ 'breeds' => [BREED_TOY], 'costs' => [RESOURCE_STICK => 1, RESOURCE_TREAT => 1], 'name' => clienttranslate('Havanese'), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('') ],
    5   => [ 'breeds' => [BREED_TOY], 'costs' => [RESOURCE_STICK => 1, RESOURCE_BALL => 1, RESOURCE_TOY => 1], 'name' => clienttranslate('English Toy Terrier (Black & Tan)'), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('') ],
    6   => [ 'breeds' => [BREED_TOY], 'costs' => [RESOURCE_BALL => 1, RESOURCE_TOY => 2], 'name' => clienttranslate('Cavalier King Charles Spaniel'), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('') ],
    7   => [ 'breeds' => [BREED_TOY], 'costs' => [RESOURCE_STICK => 2, RESOURCE_TOY => 1], 'name' => clienttranslate('Maltese'), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('') ],
    8   => [ 'breeds' => [BREED_TOY], 'costs' => [RESOURCE_TREAT => 1, RESOURCE_TOY => 1], 'name' => clienttranslate('Chihuahua (Long Coat)'), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('') ],
    9   => [ 'breeds' => [BREED_TOY], 'costs' => [RESOURCE_TREAT => 1, RESOURCE_TOY => 1], 'name' => clienttranslate('Pug'), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('') ],
    10  => [ 'breeds' => [BREED_TOY], 'costs' => [RESOURCE_STICK => 1, RESOURCE_BALL => 1], 'name' => clienttranslate('Miniature Pinscher'), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('') ],
    11  => [ 'breeds' => [BREED_TOY], 'costs' => [RESOURCE_BALL => 2], 'name' => clienttranslate('Pekingese'), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('') ],
    12  => [ 'breeds' => [BREED_TOY], 'costs' => [RESOURCE_STICK => 1], 'name' => clienttranslate('Affenpinscher'), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('') ],
    13  => [ 'breeds' => [BREED_TOY], 'costs' => [RESOURCE_STICK => 2], 'name' => clienttranslate('Bichon Frise'), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('') ],
    14  => [ 'breeds' => [BREED_TOY], 'costs' => [RESOURCE_STICK => 2], 'name' => clienttranslate('Chinese Crested'), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('') ],
    15  => [ 'breeds' => [BREED_TOY], 'costs' => [RESOURCE_STICK => 1, RESOURCE_TREAT => 1], 'name' => clienttranslate('Italian Greyhound'), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('') ],
    16  => [ 'breeds' => [BREED_TOY], 'costs' => [RESOURCE_BALL => 1, RESOURCE_TOY => 1], 'name' => clienttranslate('Papillon'), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('') ],
    17  => [ 'breeds' => [BREED_TOY], 'costs' => [RESOURCE_STICK => 2], 'name' => clienttranslate('Griffon Bruxellois'), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('') ],
    18  => [ 'breeds' => [BREED_TOY], 'costs' => [RESOURCE_TOY => 2], 'name' => clienttranslate('Japanese Chin'), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('') ],
    19  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    20  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    21  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    22  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    23  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    24  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    25  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    26  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    27  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    28  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    29  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    30  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    31  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    32  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    33  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    34  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    35  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    36  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    37  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    38  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    39  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    40  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    41  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    42  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    43  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    44  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    45  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    46  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    47  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    48  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    49  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    50  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    51  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    52  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    53  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    54  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    55  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    56  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    57  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    58  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    59  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    60  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    61  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    62  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    63  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    64  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    65  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    66  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    67  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    68  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    69  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    70  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    71  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    72  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    73  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    74  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    75  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    76  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    77  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    78  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    79  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    80  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    81  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    82  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    83  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    84  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    85  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    86  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    87  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    88  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    89  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    90  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    91  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    92  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    93  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    94  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    95  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    96  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    97  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    98  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    99  => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    100 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    101 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    102 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    103 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    104 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    105 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    106 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    107 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    108 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    109 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    110 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    111 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    112 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    113 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    114 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    115 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    116 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    117 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    118 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    119 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    120 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    121 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    122 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    123 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    124 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    125 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    126 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    127 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    128 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    129 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    130 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    131 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    132 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    133 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    134 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    135 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    136 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    137 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    138 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    139 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    140 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    141 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    142 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    143 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    144 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    145 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    146 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    147 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    148 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    149 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    150 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    151 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    152 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    153 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    154 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    155 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    156 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    157 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    158 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    159 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    160 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    161 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    162 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate('')],
    163 => [ 'breeds' => [], 'costs' => [], 'name' => clienttranslate(''), 'abilityTitle' => clienttranslate(''), 'abilityText' => clienttranslate(' ')],
];
