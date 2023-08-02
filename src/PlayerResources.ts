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
            }
        }
    }

    public async payResourcesForDog(playerId: number, dog: DogCard, resources: string[]) {
        for (const index in resources) {
            const resource = resources[index];
            this.playerResourceStocks[playerId][resource].decValue(1);
            let token = this.game.tokenManager.createToken(resource as any)
            await this.game.dogCardManager.cardTokenVoidStocks[dog.id].addCard(token, {fromStock: this.playerResourceStocks[playerId][resource]})
        }
    }

    public async gainResourcesFromDog(playerId: number, dog: DogCard, resources: string[]) {
        for (const index in resources) {
            const resource = resources[index];
            this.playerResourceStocks[playerId][resource].incValue(1);
            let token = this.game.tokenManager.createToken(resource as any)
            await this.playerResourceStocks[playerId][resource].addCard(token, {fromStock: this.game.dogCardManager.cardTokenVoidStocks[dog.id]})
        }
    }

    public async gainResources(playerId: number, resources: string[]) {
        for (const index in resources) {
            const resource = resources[index];
            this.playerResourceStocks[playerId][resource].incValue(1);
            let token = this.game.tokenManager.createToken(resource as any)
            await this.playerResourceStocks[playerId][resource].addCard(token)
        }
    }
}