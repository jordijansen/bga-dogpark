interface DogCard extends Card {

}

interface Walker extends Card {
}

interface DogParkGame extends Game {
    dogCardManager: DogCardManager;
    dogWalkerManager: DogWalkerManager;
    gamedatas: DogParkGameData;
    getPlayerId(): number;
    getPlayer(playerId: number): DogParkPlayer;
    isReadOnly(): boolean;
    setTooltipToClass(className: string, html: string): void;
}

interface DogParkPlayer extends Player {
    walker: Walker
}

interface DogParkGameData extends GameData {
    field: { nrOfFields: number, dogs: DogCard[] }
}