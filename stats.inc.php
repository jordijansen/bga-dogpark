<?php

/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * DogPark implementation : © Jordi Jansen <jordi@itbyjj.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * stats.inc.php
 *
 */

/*
    In this file, you are describing game statistics, that will be displayed at the end of the
    game.
    
    !! After modifying this file, you must use "Reload  statistics configuration" in BGA Studio backoffice
    ("Control Panel" / "Manage Game" / "Your Game")
    
    There are 2 types of statistics:
    _ table statistics, that are not associated to a specific player (ie: 1 value for each game).
    _ player statistics, that are associated to each players (ie: 1 value for each player in the game).

    Statistics types can be "int" for integer, "float" for floating point values, and "bool" for boolean
    
    Once you defined your statistics there, you can start using "initStat", "setStat" and "incStat" method
    in your game logic, using statistics names defined below.
    
    !! It is not a good idea to modify this file when a game is running !!

    If your game is already public on BGA, please read the following before any change:
    http://en.doc.boardgamearena.com/Post-release_phase#Changes_that_breaks_the_games_in_progress
    
    Notes:
    * Statistic index is the reference used in setStat/incStat/initStat PHP method
    * Statistic index must contains alphanumerical characters and no space. Example: 'turn_played'
    * Statistics IDs must be >=10
    * Two table statistics can't share the same ID, two player statistics can't share the same ID
    * A table statistic can have the same ID than a player statistics
    * Statistics ID is the reference used by BGA website. If you change the ID, you lost all historical statistic data. Do NOT re-use an ID of a deleted statistic
    * Statistic name is the English description of the statistic as shown to players
    
*/
require_once(__DIR__.'/modules/php/Constants.inc.php');

$stats_type = [

    // Statistics global to table
    "table" => [
    ],

    // Statistics existing for each player
    "player" => [
        PLAYER_PARK_BOARD_REPUTATION => [
            "id"=> PLAYER_PARK_BOARD_REPUTATION_ID,
            "name" => totranslate("Reputation gained during the game"),
            "type" => "int"
        ],
        PLAYER_DOGS_FINAL_SCORING_REPUTATION => [
            "id"=> PLAYER_DOGS_FINAL_SCORING_REPUTATION_ID,
            "name" => totranslate("Reputation from dogs with FINAL SCORING abilities"),
            "type" => "float"
        ],
        PLAYER_BREED_EXPERT_WON => [
            "id"=> PLAYER_BREED_EXPERT_WON_ID,
            "name" => totranslate("Number of Breed Expert Awards"),
            "type" => "int"
        ],
        PLAYER_BREED_EXPERT_REPUTATION => [
            "id"=> PLAYER_BREED_EXPERT_REPUTATION_ID,
            "name" => totranslate("Reputation from Breed Expert Awards"),
            "type" => "int"
        ],
        PLAYER_OBJECTIVE_CARD_REPUTATION => [
            "id"=> PLAYER_OBJECTIVE_CARD_REPUTATION_ID,
            "name" => totranslate("Reputation from Objective Card"),
            "type" => "int"
        ],
        PLAYER_REMAINING_RESOURCES_REPUTATION => [
            "id"=> PLAYER_REMAINING_RESOURCES_REPUTATION_ID,
            "name" => totranslate("Reputation from remaining resources"),
            "type" => "int"
        ]
    ]

];

