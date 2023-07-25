<?php

namespace objects;
class SelectionUndo
{
    public int $playerId;
    public array $dogIdsInKennel;

    /**
     * @param int $playerId
     * @param array $dogIdsInKennel
     */
    public function __construct(int $playerId, array $dogIdsInKennel)
    {
        $this->playerId = $playerId;
        $this->dogIdsInKennel = $dogIdsInKennel;
    }
}

?>