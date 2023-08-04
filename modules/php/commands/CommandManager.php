<?php

namespace commands;

use APP_DbObject;
use DogPark;
use ReflectionClass;
use ReflectionException;
use ReflectionUtils;

class CommandManager extends APP_DbObject
{

    function addCommand(int $playerId, BaseCommand $command)
    {
        $command->do();

        $jsonObject = json_encode(ReflectionUtils::extractAllPropertyValues($command));
        $className = (new ReflectionClass($command))->getShortName();
        $this->DbQuery("INSERT INTO `command_log`(`player_id`, `name`, `value`) VALUES ($playerId, '$className', '$jsonObject')");
    }

    function hasCommands(int $playerId): bool
    {
        return intval($this->getUniqueValueFromDB("SELECT count(1) from `command_log` WHERE player_id = ".$playerId)) > 0;
    }

    public function removeLastCommand($playerId): BaseCommand
    {
        $idToRemove = intval($this->getUniqueValueFromDB("SELECT id FROM `command_log` WHERE player_id = ". $playerId ." ORDER BY id DESC LIMIT 1"));
        return $this->removeCommand($idToRemove);
    }

    public function removeAllCommands($playerId)
    {
        $idsToRemove = $this->getCollectionFromDB("SELECT id FROM `command_log` WHERE player_id = ". $playerId ." ORDER BY id DESC");
        foreach ($idsToRemove as $id => $obj) {
            $this->removeCommand(intval($id));
        }
    }

    public function clearCommands()
    {
        $this->DbQuery("DELETE FROM `command_log`");
    }

    public function removeCommand($id) {
        $command = $this->toCommandObject($id);
        $command->undo();
        $this->DbQuery("DELETE FROM `command_log` WHERE id = " .$id);
        return $command;
    }

    private function toCommandObject($id): BaseCommand
    {
        $commandFromDb = current($this->getCollectionFromDB("SELECT * FROM `command_log` WHERE id = ".$id));
        $commandFromDbValue = json_decode($commandFromDb['value']);
        $classId = 'commands\\' .$commandFromDb['name'];
        return ReflectionUtils::rebuildAllPropertyValues($commandFromDbValue, $classId);
    }
}