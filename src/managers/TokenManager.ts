interface Token {
    id: number,
    type: 'reputation'|'stick'|'toy'|'treat'|'ball'|'walked'
}

class TokenManager extends CardManager<Token> {

    private idSequence = 0;

    static TOKEN_WIDTH = 39.375;
    static TOKEN_HEIGHT: 33.75;

    constructor(private dogParkGame: DogParkGame) {
        super(dogParkGame, {
            getId: (token) => `dp-token-${token.id}`,
            setupDiv: (token: Token, div: HTMLElement) => {
                div.classList.add('dp-card-token')
                div.classList.add('dp-token-token')
                div.classList.add(`dp-token-${token.type}`)
                div.classList.add('small')
                div.dataset.type = token.type
            },
            setupFrontDiv: (token: Token, div: HTMLElement) => {
            },
            cardWidth: TokenManager.TOKEN_WIDTH,
            cardHeight: TokenManager.TOKEN_HEIGHT
        })
    }

    public setUp() {
        const swapHtml = `<p>${_('Swap: You may exchange 1 Dog from your Kennel with a Dog in the Field')}</p>`;
        const swapPlusWalkedHtml = `<p>${_('Choose between 1 reputation or Swap.')}</p>` + swapHtml + `<p>${_('The new Dog also receives a walked token.')}</p>`;
        const scoutHtml = `<p>${_('Scout: You may reveal the top two cards of the deck. You may replace a Dog in the Field with 1 of the Dogs drawn. Unselected cards are discarded.')}</p>`;
        (this.dogParkGame as any).addTooltipHtmlToClass(`dp-token-swap`, swapHtml, TOOLTIP_DELAY);
        (this.dogParkGame as any).addTooltipHtmlToClass(`dp-token-scout`, scoutHtml, TOOLTIP_DELAY);

        (this.dogParkGame as any).addTooltipHtml(`dp-walk-spot-6`, scoutHtml, TOOLTIP_DELAY);
        (this.dogParkGame as any).addTooltipHtml(`dp-walk-spot-9`, scoutHtml, TOOLTIP_DELAY);
        (this.dogParkGame as any).addTooltipHtml(`dp-walk-spot-10`, swapHtml, TOOLTIP_DELAY);
        (this.dogParkGame as any).addTooltipHtml(`dp-walk-spot-93`, swapPlusWalkedHtml, TOOLTIP_DELAY);
    }

    public createToken(type: Token['type']) {
        return {id: this.idSequence++, type};
    }
}