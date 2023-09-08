class PlayerArea {

    private walkerStocks: {[playerId: number]: LineStock<DogWalker>} = {}
    public kennelStocks: {[playerId: number]: LineStock<DogCard>} = {}
    public leadStocks: {[playerId: number]: LineStock<DogCard>} = {}
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

            this.playerDials[Number(player.id)] = new DogOfferDial({
                elementId: `dp-game-board-offer-dial-${player.id}`,
                parentId: `dp-player-token-wrapper-${player.id}`,
                player: player,
                readOnly: true,
                initialValue: player.offerValue
            });

            const dogWalkerStockId = `dp-walker-rest-area-${player.id}`;
            dojo.place(`<div id="${dogWalkerStockId}"></div>`, $(`dp-player-token-wrapper-${player.id}`))
            this.walkerStocks[Number(player.id)] = new LineStock<DogWalker>(this.game.dogWalkerManager, $(dogWalkerStockId), {center: false})
            this.moveWalkerToPlayer(Number(player.id), player.walker);

            const kennelStockId = `dp-player-area-${player.id}-kennel`;
            this.kennelStocks[Number(player.id)] = new LineStock<DogCard>(this.game.dogCardManager, $(kennelStockId), {center: true})
            this.moveDogsToKennel(Number(player.id), player.kennelDogs);

            const leadStockId = `dp-player-area-${player.id}-lead`;
            this.leadStocks[Number(player.id)] = new LineStock<DogCard>(this.game.dogCardManager, $(leadStockId), {center: false})
            this.moveDogsToLead(Number(player.id), player.leadDogs);

            const objectiveStockId = `dp-player-objective-card-${player.id}`;
            this.playerObjective[Number(player.id)] = new LineStock(this.game.objectiveCardManager, $(objectiveStockId), {})
            this.moveObjectiveToPlayer(Number(player.id), player.chosenObjective);

            if (player.orderNo === 1) {
                dojo.place(this.createFirsPlayerMarker(), $(`dp-player-token-wrapper-${player.id}`))
            }
        }

        for (const autoWalker of gameData.autoWalkers) {
            dojo.place(`<div id="dp-player-area-${autoWalker.id}" class="whiteblock dp-player-area" style="background-color: #${autoWalker.color};">
                            <div class="label-wrapper">
                                <h2 style="color: #${autoWalker.color};">${autoWalker.name}</h2>
                            </div>
                            <div class="dp-player-area-section-wrapper">
                                <div class="label-wrapper vertical">
                                    <h2 style="color: #${autoWalker.color};">${_('Kennel')}</h2>
                                </div>
                                <div id="dp-player-area-${autoWalker.id}-kennel" class="dp-player-area-kennel">
                                </div>
                            </div>
                        </div>`, "dp-player-areas")

            const kennelStockId = `dp-player-area-${autoWalker.id}-kennel`;
            this.kennelStocks[Number(autoWalker.id)] = new LineStock<DogCard>(this.game.dogCardManager, $(kennelStockId), {center: true})
            this.moveDogsToKennel(Number(autoWalker.id), autoWalker.kennelDogs);
        }
    }

    public moveObjectiveToPlayer(playerId: number, objectiveCard: ObjectiveCard) {
        if (objectiveCard) {
            dojo.connect($('player-table-objective-button'), 'onclick', (event) => this.game.helpDialogManager.showObjectiveHelpDialog(event, objectiveCard))
            return this.playerObjective[playerId].addCard(objectiveCard);
        }
        return Promise.resolve(true);
    }

    public moveWalkerToPlayer(playerId: number, walker?: DogWalker) {
        if (walker && walker.location == 'player') {
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

    public getSelectedKennelDog(playerId: number) {
        const selection = this.kennelStocks[playerId].getSelection();
        if (selection.length > 0) {
            return selection[0];
        }
        return null;
    }

    public setNewFirstWalker(playerId: number) {
        const element = $('dp-first-player-marker');
        return this.game.animationManager.play(
                new BgaAttachWithAnimation({
                    animation: new BgaSlideAnimation({ element, transitionTimingFunction: 'ease-out' }),
                    attachElement: $(`dp-player-token-wrapper-${playerId}`)
                }));
    }

    public initAutoWalkers(autoWalker: AutoWalker) {
        dojo.place(`<div id="overall_auto_walker_${autoWalker.id}_board" class="player-board" style="height: auto;">
                                    <div class="player_board_inner">
                                        <div class="player-name" id="player_name_${autoWalker.id}" style="color: #${autoWalker.color}">
                                            ${autoWalker.name}                    
                                        </div>
                                        <div class="player_board_content">
                                            <div id="dp-player-token-wrapper-${autoWalker.id}" class="dp-player-token-wrapper"><div id="dp-walker-rest-area-${autoWalker.id}"></div></div>
                                        </div>
                                    </div>
                                </div>`, `player_boards`);

        this.playerDials[Number(autoWalker.id)] = new DogOfferDial({
            elementId: `dp-game-board-offer-dial-${autoWalker.id}`,
            parentId: `dp-player-token-wrapper-${autoWalker.id}`,
            player: autoWalker,
            readOnly: true,
            initialValue: autoWalker.offerValue
        });

        const dogWalkerStockId = `dp-walker-rest-area-${autoWalker.id}`;
        dojo.place(`<div id="${dogWalkerStockId}"></div>`, $(`dp-player-token-wrapper-${autoWalker.id}`))
        this.walkerStocks[Number(autoWalker.id)] = new LineStock<DogWalker>(this.game.dogWalkerManager, $(dogWalkerStockId), {center: false})
        this.moveWalkerToPlayer(autoWalker.id, autoWalker.walker)
    }

    private createPlayerArea(player: DogParkPlayer) {
        return `<div id="player-table-${player.id}" class="whiteblock dp-player-area" style="background-color: #${player.color};">
                    <div id="player-table-${player.id}-resources" class="player-table-resources">
                        ${['stick', 'ball', 'toy', 'treat'].map(resource => `<span><span class="dp-token-token small" data-type="${resource}"></span><span id="player-table-${resource}-counter-${player.id}" style="vertical-align: middle; padding: 0 5px;"></span></span>`).join('')}
                        ${ this.game.getPlayerId() == Number(player.id) ? `<a id="player-table-objective-button" class="bgabutton bgabutton_gray">${_('Objective')}</a>` : ''}
                    </div>
                    <div class="label-wrapper">
                        <h2 style="color: #${player.color};">${player.name}</h2>
                    </div>
                    <div class="dp-player-area-section-wrapper">
                        <div class="label-wrapper vertical">
                            <h2 style="color: #${player.color};">${_('Lead')}</h2>
                        </div>
                        <div class="dp-lead-board dp-board" data-color="#${player.color}">
                            <div id="dp-player-area-${player.id}-lead" class="dp-lead-board-lead"></div>
                        </div>
                    </div>
                    <div class="dp-player-area-section-wrapper">
                        <div class="label-wrapper vertical">
                            <h2 style="color: #${player.color};">${_('Kennel')}</h2>
                        </div>
                        <div id="dp-player-area-${player.id}-kennel" class="dp-player-area-kennel"> 
                        </div>
                    </div>
                </div>`;
    }

    private createPlayerPanels(player: DogParkPlayer) {
        dojo.place( `<div id="dp-player-resources-${player.id}" class="dp-player-resources">
                            <div id="dp-player-dummy-resources-${player.id}" style="height: 0; width: 0; overflow: hidden;"></div>
                          </div>
                          <div id="dp-player-token-wrapper-${player.id}" class="dp-player-token-wrapper"></div>
                          <div id="dp-player-objective-card-${player.id}"  class="dp-player-objective-card"></div>`, `player_board_${player.id}`);
    }

    private createFirsPlayerMarker() {
        return `<div id="dp-first-player-marker" class="dp-token dp-first-player-marker"></div>`;
    }


}