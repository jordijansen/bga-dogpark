class ObjectiveCardManager extends CardManager<ObjectiveCard> {

    public static CARD_HEIGHT = 195;
    public static CARD_WIDTH = 266;

    constructor(private dogParkGame: DogParkGame) {
        super(dogParkGame, {
            getId: (card) => `dp-objective-card-${card.id}`,
            setupDiv: (card, div: HTMLElement) => {
                div.classList.add('blackjack-size-landscape')

                if (this.isCardVisible(card)) {
                    const helpButtonElement = document.createElement("div");
                    helpButtonElement.classList.add('dp-help-button-wrapper')
                    helpButtonElement.classList.add(card.typeArg >= 20 ? 'position-bottom-right' : 'position-top-right')
                    helpButtonElement.innerHTML = `<i id="dp-help-objective-${card.id}" class="dp-help-button fa fa-question-circle"  aria-hidden="true"></i>`
                    div.appendChild(helpButtonElement);

                    dojo.connect($(`dp-help-objective-${card.id}`), 'onclick', (event) => this.dogParkGame.helpDialogManager.showObjectiveHelpDialog(event, card));
                }
            },
            setupBackDiv: (card, div: HTMLElement) => {
                div.id = `${this.getId(card)}-back`;
                div.classList.add(`objective-art`)
                div.classList.add(`objective-art-background`)
            },
            setupFrontDiv: (card, div: HTMLElement) => {
                div.id = `${this.getId(card)}-front`;
                div.classList.add(`objective-art`)
                div.classList.add(`objective-art-${card.typeArg}`)
            },
            isCardVisible: (card) => !!card.typeArg,
            cardWidth: ObjectiveCardManager.CARD_WIDTH,
            cardHeight: ObjectiveCardManager.CARD_HEIGHT,
        })
    }
}