class RoundTracker {

    private static readonly elementId = 'dp-round-tracker';

    constructor(private game: DogParkGame) {}

    public setUp(gameData: DogParkGameData) {
        this.updateRound(gameData.currentRound);
        this.updatePhase(gameData.currentPhase);
    }

    public updateRound(round: number) {
        $(RoundTracker.elementId).dataset.round = round;
    }

    public updatePhase(phase: string) {
        $(RoundTracker.elementId).dataset.phase = this.toPhaseId(phase);

        this.resetFocus();
        this.setFocus(phase);
    }

    private resetFocus() {
        $('dp-game-board-park-wrapper').style.order = 10;
        $('dp-game-board-field-wrapper').style.order = 11;
        $('dp-own-player-area').style.order = 12;
        $('dp-game-board-field-scout-wrapper').style.order = 13;

    }

    private setFocus(phase: string) {
        switch (phase) {
            case 'PHASE_RECRUITMENT_1':
            case 'PHASE_RECRUITMENT_2':
                $('dp-game-board-field-wrapper').style.order = 2;
                $('dp-own-player-area').style.order = 3;
                break;
            case 'PHASE_SELECTION':
                $('dp-own-player-area').style.order = 1;
                break;
            case 'PHASE_WALKING':
                $('dp-game-board-park-wrapper').style.order = 1;
                $('dp-own-player-area').style.order = 2;
                break;

        }
    }

    public setScoutFocus() {
        this.resetFocus();
        $('dp-game-board-field-scout-wrapper').style.order = 1;
        $('dp-game-board-field-wrapper').style.order = 2;
        $('dp-game-board-park-wrapper').style.order = 3;
        $('dp-own-player-area').style.order = 4;
    }

    public setSwapFocus() {
        this.resetFocus();
        $('dp-own-player-area').style.order = 1;
        $('dp-game-board-field-wrapper').style.order = 2;
        $('dp-game-board-park-wrapper').style.order = 3;
    }

    public unsetFocus() {
        this.resetFocus();
        this.setFocus(this.toPhase(Number($(RoundTracker.elementId).dataset.phase)));
    }



    private toPhaseId(phase: string) {
        switch (phase) {
            case 'PHASE_RECRUITMENT_1':
            case 'PHASE_RECRUITMENT_2':
                return 1;
            case 'PHASE_SELECTION':
                return 2;
            case 'PHASE_WALKING':
                return 3;
            case 'PHASE_HOME_TIME':
                return 4;
        }
    }

    private toPhase(phaseId: number) {
        switch (phaseId) {
            case 1:
                return 'PHASE_RECRUITMENT_1';
            case 2:
                return 'PHASE_SELECTION';
            case 3:
                return 'PHASE_WALKING';
            case 4:
                return 'PHASE_HOME_TIME';
        }
    }
}