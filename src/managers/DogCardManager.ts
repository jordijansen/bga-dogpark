class DogCardManager extends CardManager<Card> {

    public static CARD_WIDTH = 195;
    public static CARD_HEIGHT = 266;

    constructor(private dogParkGame: DogParkGame) {
        super(dogParkGame, {
            getId: (card) => `dp-dog-card-${card.id}`,
            setupDiv: (card: Card, div: HTMLElement) => {
                div.classList.add('dp-dog-card')
                div.dataset.id = `${card.id}`
                div.dataset.type = 'dog'
            },
            setupFrontDiv: (card: Card, div: HTMLElement) => {
                div.id = `${this.getId(card)}-front`;
                div.classList.add(`dog-card-art`)
                div.classList.add(`dog-card-art-${card.typeArg}`)
                div.dataset.set = card.type;
            },
            isCardVisible: (card: Card) => !!card.typeArg,
            cardWidth: DogCardManager.CARD_WIDTH,
            cardHeight: DogCardManager.CARD_HEIGHT,
        })
    }
}