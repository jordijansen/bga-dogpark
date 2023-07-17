class PlayerArea {

    private walkerStocks: {[playerId: number]: LineStock<Walker>} = {}

    constructor(private game: DogParkGame) {}

    public setUp(gameData: DogParkGameData) {
        const playerAreas = [];
        for (const playerId in gameData.players) {
            const player = gameData.players[playerId];

            const playerArea=this.createPlayerArea(player);
            if (Number(player.id) === this.game.getPlayerId()) {
                playerAreas.unshift(playerArea);
            } else {
                playerAreas.push(playerArea);
            }
        }
        playerAreas.forEach(playerArea => dojo.place(playerArea, "dp-player-areas"))

        for (const playerId in gameData.players) {
            const player = gameData.players[playerId];
            const stockId = `dp-player-area-${player.id}-dog-walker`;
            this.walkerStocks[Number(player.id)] = new LineStock<Walker>(this.game.dogWalkerManager, $(stockId), {})
            this.moveWalkerToPlayer(Number(player.id), player.walker);
        }
    }

    public moveWalkerToPlayer(playerId: number, walker: Walker) {
        return this.walkerStocks[playerId].addCard(walker);
    }

    private createPlayerArea(player: DogParkPlayer) {
        return `<div id="dp-player-area-${player.id}">
                    <h2>${player.name}</h2>
                    <div class="dp-lead-board dp-board" data-color="#${player.color}">
                        <div id="dp-player-area-${player.id}-dog-walker"></div>
                    </div>
                </div>`;
    }
}