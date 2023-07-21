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