class DogWalkerManager extends CardManager<Walker> {

    public static WIDTH = 45;
    public static HEIGHT = 65;

    constructor(private dogParkGame: DogParkGame) {
        super(dogParkGame, {
            getId: (walker) => `dp-dog-walker-${walker.id}`,
            setupDiv: (walker: Walker, div: HTMLElement) => {
                div.classList.add('dp-dog-walker-token')
                div.classList.add('dp-token')
                div.dataset.id = `${walker.id}`
                div.dataset.type = 'walker'
            },
            setupFrontDiv: (walker: Walker, div: HTMLElement) => {
                div.id = `${this.getId(walker)}-front`;
                div.classList.add(`dp-dog-walker`)
                div.dataset.color = `#${walker.type}`;
            },
            cardWidth: DogWalkerManager.WIDTH,
            cardHeight: DogWalkerManager.HEIGHT,
        })
    }
}