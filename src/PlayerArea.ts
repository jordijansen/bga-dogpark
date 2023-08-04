class PlayerArea {

    private walkerStocks: {[playerId: number]: LineStock<DogWalker>} = {}
    private kennelStocks: {[playerId: number]: LineStock<DogCard>} = {}
    private leadStocks: {[playerId: number]: LineStock<DogCard>} = {}
    private playerDials: {[playerId: number]: DogOfferDial} = {}
    private playerObjective: {[playerId: number]: LineStock<Card>} = {}

    constructor(private game: DogParkGame) {}

    public setUp(gameData: DogParkGameData) {
        for (const playerId in gameData.players) {
            const player = gameData.players[playerId];

            this.createPlayerPanels(player);
            const playerArea= this.createPlayerArea(player);
            if (Number(player.id) === this.game.getPlayerId()) {
                dojo.place(playerArea, "dp-own-player-area")
            } else {
                dojo.place(playerArea, "dp-player-areas")
            }
        }

        for (const playerId in gameData.players) {
            const player = gameData.players[playerId];
            const dogWalkerStockId = `dp-player-walker-area-${player.id}`;

            dojo.place(`<div id="${dogWalkerStockId}" class="dp-player-walker-area"></div>`, $('dp-game-board-offer-dials'))
            this.walkerStocks[Number(player.id)] = new LineStock<DogWalker>(this.game.dogWalkerManager, $(dogWalkerStockId), {center: false})
            this.moveWalkerToPlayer(Number(player.id), player.walker);

            const kennelStockId = `dp-player-area-${player.id}-kennel`;
            this.kennelStocks[Number(player.id)] = new LineStock<DogCard>(this.game.dogCardManager, $(kennelStockId), {center: true})
            this.moveDogsToKennel(Number(player.id), player.kennelDogs);

            const leadStockId = `dp-player-area-${player.id}-lead`;
            this.leadStocks[Number(player.id)] = new LineStock<DogCard>(this.game.dogCardManager, $(leadStockId), {center: false})
            this.moveDogsToLead(Number(player.id), player.leadDogs);

            this.playerDials[Number(player.id)] = new DogOfferDial({
                elementId: `dp-game-board-offer-dial-${player.id}`,
                parentId: 'dp-game-board-offer-dials',
                player,
                readOnly: true,
                initialValue: player.offerValue
            });

            const objectiveStockId = `dp-player-objective-card-${player.id}`;
            this.playerObjective[Number(player.id)] = new LineStock(this.game.objectiveCardManager, $(objectiveStockId), {})
            this.moveObjectiveToPlayer(Number(player.id), player.chosenObjective);

            if (player.orderNo === 1) {
                dojo.place(this.createFirsPlayerMarker(), $(`dp-player-first-player-marker-wrapper-${player.id}`))
            }
        }
    }

    public moveObjectiveToPlayer(playerId: number, objectiveCard: Card) {
        if (objectiveCard) {
            return this.playerObjective[playerId].addCard(objectiveCard);
        }
        return Promise.resolve(true);
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

    public moveDogsToLead(playerId: number, dogs: DogCard[]) {
        return this.leadStocks[playerId].addCards(dogs);
    }

    public setPlayerOfferValue(playerId: number, offerValue: number) {
        this.playerDials[playerId].currentValue = offerValue;
    }

    public resetAllOfferValues() {
        for (const playerId in this.playerDials) {
            this.playerDials[playerId].currentValue = null;
        }
    }

    public setSelectionModeForKennel(selectionMode: CardSelectionMode, playerId: number, selectableDogs?: DogCard[], onSelect?: (selection: DogCard[]) => void) {
        this.kennelStocks[playerId].setSelectionMode(selectionMode);
        if (selectionMode != 'none') {
            this.kennelStocks[playerId].setSelectableCards(selectableDogs)
            this.kennelStocks[playerId].onSelectionChange = onSelect
        } else {
            this.kennelStocks[playerId].onSelectionChange = undefined;
        }
    }

    public setNewFirstWalker(playerId: number) {
        const element = $('dp-first-player-marker');
        return this.game.animationManager.play(
                new BgaAttachWithAnimation({
                    animation: new BgaSlideAnimation({ element, transitionTimingFunction: 'ease-out' }),
                    attachElement: $(`dp-player-first-player-marker-wrapper-${playerId}`)
                }));
    }

    private createPlayerArea(player: DogParkPlayer) {
        return `<div id="dp-player-area-${player.id}" class="whiteblock dp-player-area" style="background-color: #${player.color};">
                    <div class="player-name-wrapper">
                        <h2 style="color: #${player.color};">${player.name}</h2>
                    </div>
                    <div class="dp-lead-board dp-board" data-color="#${player.color}">
                        <div id="dp-player-area-${player.id}-lead" class="dp-lead-board-lead"></div>
                    </div>
                    <div id="dp-player-area-${player.id}-kennel">
                    </div>
                </div>`;
    }

    private createPlayerPanels(player: DogParkPlayer) {
        dojo.place( `<div id="dp-player-resources-${player.id}" class="dp-player-resources">
                            <div id="dp-player-dummy-resources-${player.id}" style="height: 0; width: 0; overflow: hidden;"></div>
                          </div>
                          <div id="dp-player-first-player-marker-wrapper-${player.id}" class="dp-player-first-player-marker-wrapper"></div>
                          <div id="dp-player-objective-card-${player.id}"  class="dp-player-objective-card"></div>`, `player_board_${player.id}`);
    }

    private createFirsPlayerMarker() {
        return `<div id="dp-first-player-marker" class="dp-token dp-first-player-marker"></div>`;
    }


}