<?php

namespace traits;

use objects\AutoWalker;

trait UtilsTrait
{

    /**
     * @return AutoWalker[]
     */
    function getAutoWalkers() {
        $gameinfos = self::getGameinfos();
        $playerColors = $gameinfos['player_colors'];

        $players = $this->loadPlayersBasicInfos();
        $usedPlayerColors = array_map(fn($players) => $players['player_color'], array_values($players));

        $unusedPlayerColors = array_filter($playerColors, function($color) use($usedPlayerColors){return !in_array($color, $usedPlayerColors);});

        $nrOfPlayers = $this->getPlayersNumber();
        $autoWalkers = [];
        if ($nrOfPlayers <= 2) {
            $nrOfAutoWalkers = $nrOfPlayers == 1 ? 2 : 1;
            for ($i = 1; $i <= $nrOfAutoWalkers; $i++) {
                $autoWalkers[] = new AutoWalker($i, array_shift($unusedPlayerColors));
            }
        }
        return $autoWalkers;
    }

    function getNextAutoWalkerDiceValue() {
        $diceValues = [1, 2, 2, 3, 3, 4];
        $index = bga_rand(1, 6);
        return $diceValues[$index - 1];
    }

    //////////////////////////////////////////////////////////////////////////////
    //////////// Generic Utility functions
    ////////////
    ///
    function saveForUndo() {
        // TODO
        $displayCards = $this->cardManager->getCardsInLocation(ZONE_DISPLAY);

        $this->setGlobalVariable(UNDO, new Undo(
            array_map(fn($card) => $card->id, $displayCards),
        ));

        $this->setGameStateValue(CANCELLABLE_MOVES, 0);
    }

    function canCancelMoves() {
        return intval($this->getGameStateValue(CANCELLABLE_MOVES)) > 0;
    }

    function setGlobalVariable(string $name, /*object|array*/ $obj) {
        $jsonObj = json_encode($obj);
        $this->DbQuery("INSERT INTO `global_variables`(`name`, `value`)  VALUES ('$name', '$jsonObj') ON DUPLICATE KEY UPDATE `value` = '$jsonObj'");
    }

    function getGlobalVariable(string $name, $asArray = null) {
        $json_obj = $this->getUniqueValueFromDB("SELECT `value` FROM `global_variables` where `name` = '$name'");
        if ($json_obj) {
            $object = json_decode($json_obj, $asArray);
            return $object;
        } else {
            return null;
        }
    }

    function deleteGlobalVariable(string $name) {
        $this->DbQuery("DELETE FROM `global_variables` where `name` = '$name'");
    }

    function deleteGlobalVariables(array $names) {
        $this->DbQuery("DELETE FROM `global_variables` where `name` in (".implode(',', array_map(fn($name) => "'$name'", $names)).")");
    }

    function getPlayerName(int $playerId) {
        if ($playerId > 2) {
            return self::getUniqueValueFromDB("SELECT player_name FROM player WHERE player_id = $playerId");
        } else {
            return "Autowalker #$playerId";
        }
    }

    function getPlayerColor(int $playerId) {
        return self::getUniqueValueFromDB("SELECT player_color FROM player WHERE player_id = $playerId");
    }

    function getPlayers() {
        return $this->getCollectionFromDB("SELECT * FROM player");
    }

    function getPlayerNo($playerId) {
        return $this->getUniqueValueFromDB("SELECT player_no FROM player WHERE player_id = $playerId");
    }

    function getPlayerScore(int $playerId) {
        return intval($this->getUniqueValueFromDB("SELECT player_score FROM player WHERE player_id = $playerId"));
    }

    function updatePlayerScoreAndAux(int $playerId, int $playerScore, int $playerScoreAux = 0) {
        $this->DbQuery("UPDATE player SET player_score = ".$playerScore.", player_score_aux = ".$playerScoreAux." WHERE player_id = ". $playerId);
    }

    function updatePlayerScore(int $playerId, int $playerScore) {
        $this->DbQuery("UPDATE player SET player_score = ".$playerScore." WHERE player_id = ". $playerId);
    }
}
