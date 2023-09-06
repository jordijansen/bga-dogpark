class PlayerResources {

    private playerResourceStocks: {[playerId: number]: {[resource: string]: CounterVoidStock<Token>}} = {}

    constructor(private game: DogParkGame) {}

    public setUp(gameData: DogParkGameData) {
        for (const playerId in gameData.players) {
            const player = gameData.players[playerId];
            const resources = player.resources;
            this.playerResourceStocks[playerId] = {};
            for (const resource in resources) {
                this.playerResourceStocks[playerId][resource] = new CounterVoidStock<Token>(this.game, this.game.tokenManager, {
                    counter: new ebg.counter(),
                    targetElement: `dp-player-resources-${player.id}`,
                    counterId: `dp-player-${resource}-counter-${player.id}`,
                    initialCounterValue: resources[resource],
                    setupIcon: (element) => {
                        element.classList.add("dp-token-token");
                        element.classList.add("small");
                        element.dataset.type = resource;
                    }
                });

                this.playerResourceStocks[playerId][resource].counter.addTarget($(`player-table-${resource}-counter-${player.id}`))
            }
        }
    }

    public async payResourcesToDog(playerId: number, dog: DogCard, resources: string[]) {
        resources.forEach(resource => this.playerResourceStocks[playerId][resource].decValue(1));

        for (const resource of resources) {
            let token = this.game.tokenManager.createToken(resource as any)
            await this.game.dogCardManager.cardTokenVoidStocks[dog.id].addCard(token, {fromStock: this.playerResourceStocks[playerId][resource]})
        }
    }

    public async gainResourcesFromDog(playerId: number, dog: DogCard, resources: string[]) {
        resources.forEach(resource => this.playerResourceStocks[playerId][resource].incValue(1));

        for (const resource of resources) {
            let token = this.game.tokenManager.createToken(resource as any)
            await this.playerResourceStocks[playerId][resource].addCard(token, {fromStock: this.game.dogCardManager.cardTokenVoidStocks[dog.id]})
        }
    }

    public async gainResourcesFromForecastCard(playerId: number, foreCastCard: ForecastCard, resources: string[]) {
        resources.forEach(resource => this.playerResourceStocks[playerId][resource].incValue(1));
        for (const resource of resources) {
            let token = this.game.tokenManager.createToken(resource as any)
            await this.playerResourceStocks[playerId][resource].addCard(token, {fromElement: this.game.forecastManager.getCardElement(foreCastCard)})
        }
    }

    public async gainResourceFromLocation(playerId: number, locationId: number, resource: string) {
        this.playerResourceStocks[playerId][resource].incValue(1);
        const stock = this.game.dogWalkPark.resourceSpots[locationId];

        const token = this.game.tokenManager.createToken(resource as any)
        await this.playerResourceStocks[playerId][resource].addCard(token, {fromStock: stock})
    }

    public async gainResources(playerId: number, resources: string[], fromElementId?: string) {
        resources.forEach(resource => this.playerResourceStocks[playerId][resource].incValue(1));

        for (const resource of resources) {
            let token = this.game.tokenManager.createToken(resource as any)
            await this.playerResourceStocks[playerId][resource].addCard(token)
        }
    }

    public async payResources(playerId: number, resources: string[]) {
        resources.forEach(resource => this.playerResourceStocks[playerId][resource].decValue(1));
    }



}