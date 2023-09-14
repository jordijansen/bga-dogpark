class FinalScoringPad {

    private scoringPadRows = [
        { key: 'parkBoardScore',  getLabel: () => this.getLabel('parkBoardScore') },
        { key: 'dogFinalScoringScore',  getLabel: () => this.getLabel('dogFinalScoringScore') },
        { key: 'breedExpertAwardScore',  getLabel: () => this.getLabel('breedExpertAwardScore') },
        { key: 'objectiveCardScore',  getLabel: () => this.getLabel('objectiveCardScore') },
        { key: 'remainingResourcesScore',  getLabel: () => this.getLabel('remainingResourcesScore') },
        { key: 'score',  getLabel: () => this.getLabel('score') }
    ];
    constructor(private game: DogPark,
                private elementId: string) {
    }

    private getLabel(category: string) {
        switch (category) {
            case 'parkBoardScore':
                return _('<icon-reputation> during game');
            case 'dogFinalScoringScore':
                return _('<icon-reputation> from dogs with <b>FINAL SCORING</b> abilities');
            case 'breedExpertAwardScore':
                return _('<icon-reputation> from won Breed Expert awards');
            case 'objectiveCardScore':
                return _('<icon-reputation> from completed Objective Card')
            case 'remainingResourcesScore':
                return _('Remaining resources = <icon-reputation> for every 5')
            case 'score':
                return _('Total <icon-reputation>')

        }
    }

    setUp(gamedatas: DogParkGameData) {
        if (gamedatas.scoreBreakdown) {
            this.showPad(gamedatas.scoreBreakdown, false)
        }
    }

    public showPad(scoreBreakdown: FinalScoringBreakdown, animate: boolean) {
        if (Object.keys(scoreBreakdown).length == 1) {
            const playerScore =  scoreBreakdown[Object.keys(scoreBreakdown)[0]];
            dojo.place(`<div class="dp-solo-rating dp-board">
                                <h1>${dojo.string.substitute(_('Solo Rating: ${soloStarRating} <i class="fa fa-star" aria-hidden="true"></i>'), { soloStarRating: playerScore.soloStarRating })}</h1>
                                ${SoloRatings.getSoloRatingsTable()}
                             </div>`, $(this.elementId))
        }

        dojo.place(this.createPad(scoreBreakdown, animate), $(this.elementId))
        if (animate) {
            const elementsToReveal = document.getElementById('dp-final-scoring-pad').querySelectorAll('.reveal-score-value') as NodeListOf<HTMLElement>;
            let currentIndex = 0;
            return new Promise(function(resolve, reject) {

                const interval = setInterval(function () {
                    const element = elementsToReveal[currentIndex];
                    element.style.opacity = '1';
                    currentIndex++
                    if (currentIndex >= elementsToReveal.length) {
                        clearInterval(interval)
                        resolve('');
                    }
                }, 500);
            })
        }

        return Promise.resolve('');
    }

    private createPad(scoreBreakdown: FinalScoringBreakdown, animate: boolean) {
        let result =  `
            <div id="dp-final-scoring-pad" class="dp-board">
                    <table>
                        <thead>
                        <th></th>
`;

        Object.keys(scoreBreakdown).forEach(playerId => {
            result += `<th style="color: #${this.game.getPlayer(Number(playerId)).color}">${this.game.getPlayer(Number(playerId)).name}</th>`
        });

        result += `</thead><tbody>`;

        this.scoringPadRows.forEach(row => {
            result += `<tr>`;
            result += `<td>${this.game.formatWithIcons(row.getLabel())}</td>`;
            Object.keys(scoreBreakdown).forEach(playerId => {
                result += `<td class="reveal-score-value" style="opacity: ${animate ? 0 : 1};">${scoreBreakdown[playerId][row.key]} ${row.key == 'breedExpertAwardScore' ? '<span class="breed-expert-additional-text">(' +scoreBreakdown[playerId]['scoreAux'] +'&uarr;)*</span>': ''}</td>`
            });
            result += `</tr>`;
        })

        result += `</tbody>
                    </table>
                    <p class="dp-final-scoring-pad-tiebreaker-explanation">* ${_('If there is a tie, the player who won the highest value Breed Expert award wins. If this is a tie, the winning players share the victory.')}</p>
                </div>`;
        return result;
    }


}