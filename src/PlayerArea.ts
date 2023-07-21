class PlayerArea {

    private walkerStocks: {[playerId: number]: LineStock<DogWalker>} = {}
    private kennelStocks: {[playerId: number]: LineStock<DogCard>} = {}
    private playerDials: {[playerId: number]: DogOfferDial} = {}

    constructor(private game: DogParkGame) {}

    public setUp(gameData: DogParkGameData) {
        const playerAreas = [];

        for (const playerId in gameData.players) {
            const player = gameData.players[playerId];

            const playerArea= this.createPlayerArea(player);
            if (Number(player.id) === this.game.getPlayerId()) {
                playerAreas.unshift(playerArea);
            } else {
                playerAreas.push(playerArea);
            }
        }
        playerAreas.forEach(playerArea => dojo.place(playerArea, "dp-player-areas"))

        for (const playerId in gameData.players) {
            const player = gameData.players[playerId];
            const dogWalkerStockId = `dp-player-area-${player.id}-dog-walker`;

            this.walkerStocks[Number(player.id)] = new LineStock<DogWalker>(this.game.dogWalkerManager, $(dogWalkerStockId), {center: false})
            this.moveWalkerToPlayer(Number(player.id), player.walker);

            const kennelStockId = `dp-player-area-${player.id}-kennel`;
            this.kennelStocks[Number(player.id)] = new LineStock<DogCard>(this.game.dogCardManager, $(kennelStockId), {center: false})
            this.moveDogsToKennel(Number(player.id), player.kennelDogs);

            this.playerDials[Number(player.id)] = new DogOfferDial({
                elementId: `dp-game-board-offer-dial-${player.id}`,
                parentId: 'dp-game-board-offer-dials',
                player,
                readOnly: true,
                initialValue: player.offerValue
            });
        }
    }

    public moveWalkerToPlayer(playerId: number, walker?: DogWalker) {
        if (walker) {
            return this.walkerStocks[playerId].addCard(walker);
        }
        return Promise.resolve(true);
    }

    public moveDogsToKennel(playerId: number, dogs: DogCard[]) {
        return this.kennelStocks[playerId].addCards(dogs);
    }

    public setPlayerOfferValue(playerId: number, offerValue: number) {
        this.playerDials[playerId].currentValue = offerValue;
    }

    public resetAllOfferValues() {
        for (const playerId in this.playerDials) {
            this.playerDials[playerId].currentValue = null;
        }
    }

    private createPlayerArea(player: DogParkPlayer) {
        return `<div id="dp-player-area-${player.id}" class="whiteblock">
                    <h2>${player.name}</h2>
                    <div class="dp-lead-board dp-board" data-color="#${player.color}">
                        <div id="dp-player-area-${player.id}-dog-walker"></div>
                    </div>
                    <div id="dp-player-area-${player.id}-kennel">
                    
                    </div>
                </div>`;
    }



}