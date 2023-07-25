class DogCardManager extends CardManager<DogCard> {

    public static CARD_WIDTH = 195;
    public static CARD_HEIGHT = 266;

    public cardTokenVoidStocks: {[id: number]: VoidStock<Token>} = {}

    constructor(private dogParkGame: DogParkGame) {
        super(dogParkGame, {
            getId: (card) => `dp-dog-card-${card.id}`,
            setupDiv: (card: DogCard, div: HTMLElement) => {
                div.classList.add('dp-dog-card')
                div.dataset.id = `${card.id}`
                div.dataset.type = 'dog'

                const cardTokenVoidStockElement = document.createElement("div");
                cardTokenVoidStockElement.id = `dp-dog-card-token-void-stock-${card.id}`;
                div.appendChild(cardTokenVoidStockElement);
                this.cardTokenVoidStocks[card.id] = new VoidStock<Token>(dogParkGame.tokenManager, $(cardTokenVoidStockElement.id))
            },
            setupFrontDiv: (card: DogCard, div: HTMLElement) => {
                div.id = `${this.getId(card)}-front`;
                div.classList.add(`dog-card-art`)
                div.classList.add(`dog-card-art-${card.typeArg}`)
                div.dataset.set = card.type;
            },
            isCardVisible: (card: DogCard) => !!card.typeArg,
            cardWidth: DogCardManager.CARD_WIDTH,
            cardHeight: DogCardManager.CARD_HEIGHT,
        })
    }
}