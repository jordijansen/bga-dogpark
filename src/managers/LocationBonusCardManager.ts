class LocationBonusCardManager extends CardManager<Card> {

    public static CARD_WIDTH = 266;
    public static CARD_HEIGHT = 195;

    constructor(private dogParkGame: DogParkGame) {
        super(dogParkGame, {
            getId: (card) => `dp-location-bonus-card-${card.id}`,
            setupDiv: (card: Card, div: HTMLElement) => {
                div.classList.add('blackjack-size-landscape')
            },
            setupBackDiv: (card: Card, div: HTMLElement) => {
                div.id = `${this.getId(card)}-back`;
                div.classList.add(`location-bonus-art`)
                div.classList.add(`location-bonus-art-background`)
            },
            setupFrontDiv: (card: Card, div: HTMLElement) => {
                div.id = `${this.getId(card)}-front`;
                div.classList.add(`location-bonus-art`)
                div.classList.add(`location-bonus-art-${card.typeArg}`)
            },
            isCardVisible: (card) => !!card.typeArg,
            cardWidth: LocationBonusCardManager.CARD_WIDTH,
            cardHeight: LocationBonusCardManager.CARD_HEIGHT,
        })
    }
}