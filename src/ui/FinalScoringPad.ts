class FinalScoringPad {

    private scoringPadRows = [
        { key: 'parkBoardScore',  label: _('_icon-reputation_ during game') },
        { key: 'dogFinalScoringScore',  label: _('_icon-reputation_ from dogs with <b>FINAL SCORING</b> abilities') },
        { key: 'breedExpertAwardScore',  label: _('_icon-reputation_ from won Breed Expert awards') },
        { key: 'objectiveCardScore',  label: _('_icon-reputation_ from completed Objective Card') },
        { key: 'remainingResourcesScore',  label: _('Remaining resources = _icon-reputation_ for every 5') },
        { key: 'score',  label: _('Total _icon-reputation_') }
    ];
    constructor(private game: DogPark,
                private elementId: string) {
    }

    setUp(gamedatas: DogParkGameData) {
        if (gamedatas.scoreBreakdown) {
            this.showPad(gamedatas.scoreBreakdown, false)
        }
    }

    public showPad(scoreBreakdown: FinalScoringBreakdown, animate: boolean) {
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
            result += `<td>${this.game.formatWithIcons(row.label)}</td>`;
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