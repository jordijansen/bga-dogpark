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

    public setFocusToField() {
        $('dp-game-board-field-wrapper').style.order = 1;
    }

    public removeFocusToField() {
        $('dp-game-board-field-wrapper').style.order = 12;
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
}