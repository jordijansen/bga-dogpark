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
 * gameoptions.inc.php
 *
 * dogpark game options description
 * 
 * In this file, you can define your game options (= game variants).
 *   
 * Note: If your game has no variant, you don't have to modify this file.
 *
 * Note²: All options defined in this file should have a corresponding "game state labels"
 *        with the same ID (see "initGameStateLabels" in dogpark.game.php)
 *
 * !! It is not a good idea to modify this file when a game is running !!
 *
 */

require_once(__DIR__.'/modules/php/Constants.inc.php');

$game_options = [

    EXPANSION_EUROPEAN_DOGS_OPTION_ID => [
        'name' => totranslate('Expansion: European Dogs'),
        'values' => [
            EXPANSION_EXCLUDED => [
                'default' => true,
                'name' => 'disabled',
            ],
            EXPANSION_INCLUDED => [
                'name' => 'enabled',
                'tmdisplay' => totranslate('Expansion: European Dogs'),
                'description' => totranslate("It's time to take some of Europe's finest dogs for a walk. The European Dogs expansion includes 29 cards with 2 new abilities: Lone Wolf and Well Trained."),
            ]
        ],
    ],

    EXPANSION_FAMOUS_DOGS_OPTION_ID => [
        'name' => totranslate('Expansion: Famous Dogs'),
        'values' => [
            EXPANSION_EXCLUDED => [
                'default' => true,
                'name' => 'disabled',
            ],
            EXPANSION_INCLUDED => [
                'name' => 'enabled',
                'tmdisplay' => totranslate('Expansion: Famous Dogs'),
                'description' => totranslate("The dogs of page, stage, and screen have come to Dog Park. The Famous Dogs expansion includes 29 dog cards with 3 new abilities: Hoarder, Show Off, and Slowpoke."),
            ]
        ],
    ],
    EXPANSION_WORLD_DOGS_OPTION_ID => [
        'name' => totranslate('Expansion: Dogs of the World'),
        'values' => [
            EXPANSION_EXCLUDED => [
                'default' => true,
                'name' => 'disabled',
            ],
            EXPANSION_INCLUDED => [
                'name' => 'enabled',
                'tmdisplay' => totranslate('Expansion: Dogs of the World'),
                'description' => totranslate("Dogs from Kennel Clubs across the world are coming to Dog Park! This expansion includes 29 cards and 3 new abilities: Friendly, Fusspot, and Globetrotter."),
            ]
        ],
    ],

    VARIANT_PACKED_PARK_OPTION_ID => [
        'name' => totranslate('Variant: Packed Park'),
        'values' => [
            VARIANT_PACKED_PARK_OPTION_EXCLUDED => [
                'default' => true,
                'name' => 'disabled',
            ],
            VARIANT_PACKED_PARK_OPTION_INCLUDED => [
                'name' => 'enabled',
                'tmdisplay' => totranslate('Variant: Packed Park'),
                'description' => totranslate('In this variant, you will use the Rerouted Park Location Bonus card deck making resources more scarce.'),
            ]
        ],
        'displaycondition' => [
            [
                'type' => 'minplayers',
                'value' => [4]
            ],
            [
                'type' => 'otheroptionisnot',
                'id' => VARIANT_GENTLE_WALK_OPTION_ID,
                'value' => VARIANT_GENTLE_WALK_OPTION_INCLUDED
            ]
        ]
    ],

    VARIANT_GENTLE_WALK_OPTION_ID => [
        'name' => totranslate('Variant: Gentle Walk'),
        'values' => [
            VARIANT_GENTLE_WALK_OPTION_EXCLUDED => [
                'default' => true,
                'name' => 'disabled',
            ],
            VARIANT_GENTLE_WALK_OPTION_INCLUDED => [
                'name' => 'enabled',
                'tmdisplay' => totranslate('Variant: Gentle Walk'),
                'description' => totranslate('Recommended for younger or less confident players. In this variant, you will use the Plentiful Park Location Bonus card deck making resources more plentiful. Each player starts with an additional Toy and Treat.'),
            ]
        ],
        'displaycondition' => [
            [
                'type' => 'otheroptionisnot',
                'id' => VARIANT_PACKED_PARK_OPTION_ID,
                'value' => VARIANT_PACKED_PARK_OPTION_INCLUDED
            ]
        ]
    ],
    VARIANT_PREDICTABLE_FORECAST_OPTION_ID => [
        'name' => totranslate('Variant: Predictable Forecast'),
        'values' => [
            VARIANT_PREDICTABLE_FORECAST_OPTION_EXCLUDED => [
                'default' => true,
                'name' => 'disabled',
            ],
            VARIANT_PREDICTABLE_FORECAST_OPTION_INCLUDED => [
                'name' => 'enabled',
                'tmdisplay' => totranslate('Variant: Predictable Forecast'),
                'description' => totranslate('In this variant, you will use Forecast cards 8, 9, 10 and 11. These are still placed in random Forecast locations.'),
            ]
        ]
    ],
];


