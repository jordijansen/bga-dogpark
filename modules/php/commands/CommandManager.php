<?php

namespace commands;

use APP_DbObject;
use DogPark;
use ReflectionClass;
use ReflectionException;

class CommandManager extends APP_DbObject
{

    function addCommand(int $playerId, BaseCommand $command)
    {
        $command->do();

        $jsonObject = json_encode($this->extractAllPropertyValues($command));
        $className = (new ReflectionClass($command))->getShortName();
        $this->DbQuery("INSERT INTO `command_log`(`player_id`, `name`, `value`) VALUES ($playerId, '$className', '$jsonObject')");
    }

    function hasCommands(int $playerId): bool
    {
        return intval($this->getUniqueValueFromDB("SELECT count(1) from `command_log` WHERE player_id = ".$playerId)) > 0;
    }

    public function removeLastCommand($playerId)
    {
        $idToRemove = intval($this->getUniqueValueFromDB("SELECT id FROM `command_log` WHERE player_id = ". $playerId ." ORDER BY id DESC LIMIT 1"));
        $this->removeCommand($idToRemove);
    }

    public function removeAllCommands($playerId)
    {
        $idsToRemove = $this->getCollectionFromDB("SELECT id FROM `command_log` WHERE player_id = ". $playerId ." ORDER BY id DESC");
        foreach ($idsToRemove as $id => $obj) {
            $this->removeCommand(intval($id));
        }
    }

    public function removeCommand($id) {
        $command = $this->toCommandObject($id);
        $command->undo();
        $this->DbQuery("DELETE FROM `command_log` WHERE id = " .$id);
    }

    private function toCommandObject($id): BaseCommand
    {
        $commandFromDb = current($this->getCollectionFromDB("SELECT * FROM `command_log` WHERE id = ".$id));
        $commandFromDbValue = json_decode($commandFromDb['value']);
        $classId = 'commands\\' .$commandFromDb['name'];
        return $this->rebuildAllPropertyValues($commandFromDbValue, $classId);
    }

    function extractAllPropertyValues($object)
    {
        if (is_array($object)) {
            return array_map(function ($o) {
                return $this->extractAllPropertyValues($o);
            }, $object);
        } else if (!is_object($object)) {
            return $object;
        }
        $allProperties = [
        ];

        $reflect = new ReflectionClass(get_class($object));
        foreach ($reflect->getProperties() as $property) {
            $property->setAccessible(true); // Bypass private or protected
            $value = $property->getValue($object);
            $allProperties[$property->getName()] = $this->extractAllPropertyValues($value);
        }

        return $allProperties;
    }

    /**
     * @throws ReflectionException
     */
    function rebuildAllPropertyValues($values, $classId = null)
    {
        $reflect = new ReflectionClass($classId);
        $object = $reflect->newInstanceWithoutConstructor();
        foreach ($values as $propertyName => $value) {
            $value = $this->rebuildAllSimplePropertyValues($value);
            $property = $reflect->getProperty($propertyName);
            $property->setAccessible(true); // Bypass private or protected
            $property->setValue($object, $value);
        }
        return $object;
    }

    function rebuildAllSimplePropertyValues($values)
    {
        if (!is_array($values)) {
            return $values;
        }
        return array_map(function ($value) {
            return $this->rebuildAllSimplePropertyValues($value);
        }, $values);
    }


}