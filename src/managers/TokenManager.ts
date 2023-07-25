interface Token {
    id: number,
    type: 'reputation'|'stick'|'toy'|'treat'|'ball'
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
                div.classList.add('small')
                div.dataset.type = token.type
            },
            setupFrontDiv: (token: Token, div: HTMLElement) => {
            },
            cardWidth: TokenManager.TOKEN_WIDTH,
            cardHeight: TokenManager.TOKEN_HEIGHT
        })
    }

    public createToken(type: Token['type']) {
        return {id: this.idSequence++, type};
    }
}