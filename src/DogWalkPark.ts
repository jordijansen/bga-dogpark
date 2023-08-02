class DogWalkPark {
    private element: HTMLElement;
    private walkerSpots: {[spotId: number]: LineStock<DogWalker>} = {}

    constructor(private game: DogParkGame) {
        this.element = $("dp-game-board-park");
    }

    public setUp(gameData: DogParkGameData) {
        dojo.place(`<div id="dp-walk-trail-start" class="dp-walk-trail start"></div>`, this.element);
        dojo.place(`<div id="dp-walk-trail" class="dp-walk-trail"></div>`, this.element);
        const trailWrapper = $('dp-walk-trail');
        for (let i = 1; i <= 10; i++) {
            dojo.place(`<div id="park-column-${i}" class="dp-park-column"></div>`, trailWrapper)
        }

        dojo.place(`<div id="dp-walk-spot-1" class="dp-walk-spot" data-spot-id="1">1</div>`, _(`park-column-1`))
        dojo.place(`<div id="dp-walk-spot-2" class="dp-walk-spot" data-spot-id="2">2</div>`, _(`park-column-2`))
        dojo.place(`<div id="dp-walk-spot-3" class="dp-walk-spot" data-spot-id="3">3</div>`, _(`park-column-3`))
        dojo.place(`<div id="dp-walk-spot-4" class="dp-walk-spot" data-spot-id="4">4</div>`, _(`park-column-4`))
        dojo.place(`<div id="dp-walk-spot-5" class="dp-walk-spot" data-spot-id="5">5</div>`, _(`park-column-5`))
        dojo.place(`<div id="dp-walk-spot-6" class="dp-walk-spot" data-spot-id="6">6</div>`, _(`park-column-5`))
        dojo.place(`<div id="dp-walk-spot-7" class="dp-walk-spot" data-spot-id="7">7</div>`, _(`park-column-6`))
        dojo.place(`<div id="dp-walk-spot-8" class="dp-walk-spot" data-spot-id="8">8</div>`, _(`park-column-6`))
        dojo.place(`<div id="dp-walk-spot-9" class="dp-walk-spot" data-spot-id="9">9</div>`, _(`park-column-7`))
        dojo.place(`<div id="dp-walk-spot-10" class="dp-walk-spot" data-spot-id="10">10</div>`, _(`park-column-7`))
        dojo.place(`<div id="dp-walk-spot-11" class="dp-walk-spot" data-spot-id="11">11</div>`, _(`park-column-8`))
        dojo.place(`<div id="dp-walk-spot-12" class="dp-walk-spot" data-spot-id="12">12</div>`, _(`park-column-8`))
        dojo.place(`<div id="dp-walk-spot-13" class="dp-walk-spot" data-spot-id="13">13</div>`, _(`park-column-9`))
        dojo.place(`<div id="dp-walk-spot-14" class="dp-walk-spot" data-spot-id="14">14</div>`, _(`park-column-9`))
        dojo.place(`<div id="dp-walk-spot-15" class="dp-walk-spot" data-spot-id="15">15</div>`, _(`park-column-10`))

        this.walkerSpots[0] = new LineStock<DogWalker>(this.game.dogWalkerManager, $("dp-walk-trail-start"), {direction: "column"})
        for (let i = 1; i <= 15; i++) {
            this.walkerSpots[i] = new LineStock<DogWalker>(this.game.dogWalkerManager, $(`dp-walk-spot-${i}`), {direction: "column"})
        }

        this.moveWalkers(gameData.park.walkers);
    }

    public moveWalkers(walkers: DogWalker[]) {
        return Promise.all(walkers.map(walker => this.walkerSpots[walker.locationArg].addCard(walker)));
    }
}