interface DogOfferDialSettings {
    elementId: string,
    parentId: string,
    player: { id: any, color: string},
    initialValue: number,
    maxOfferValue?: number,
    readOnly: boolean
}

class DogOfferDial {

    private _currentValue: number = 1;
    private readonly increaseButton: HTMLElement;
    private readonly decreaseButton: HTMLElement;
    constructor(private settings: DogOfferDialSettings) {
        dojo.place(this.createDial(), settings.parentId);

        this.currentValue = settings.initialValue;

        if (!this.settings.readOnly) {
            this.increaseButton = $('dp-dial-button-increase');
            this.decreaseButton = $('dp-dial-button-decrease');

            this.updateDial(settings.initialValue);

            dojo.connect(this.increaseButton, 'onclick', () => this.updateDial(this._currentValue + 1));
            dojo.connect(this.decreaseButton, 'onclick', () => this.updateDial(this._currentValue - 1));
        }
    }

    private updateDial(newValue: number) {
        this.currentValue = newValue;

        this.increaseButton.classList.remove('disabled');
        this.decreaseButton.classList.remove('disabled');

        if (this._currentValue === 1) {
            this.decreaseButton.classList.add('disabled');
        }
        if (this._currentValue === this.settings.maxOfferValue) {
            this.increaseButton.classList.add('disabled');
        }
    }

    private createDial() {
        let result = '';
        if (!this.settings.readOnly) {
            result += `<a id="dp-dial-button-decrease" class="bgabutton bgabutton_blue"><i class="fa fa-minus" aria-hidden="true"></i></a>`
        }
        result += `<div id="${this.settings.elementId}" class="dp-dial side-front" data-color="#${this.settings.player.color}" data-value="${this._currentValue}">
                    <div class="side-front-numbers"></div>
                    <div class="side-front-overlay"><div id="dp-walker-rest-area-${this.settings.player.id}"></div></div>
                </div>`
        if (!this.settings.readOnly) {
            result += `<a id="dp-dial-button-increase" class="bgabutton bgabutton_blue"><i class="fa fa-plus" aria-hidden="true"></i></a>`
        }
        return result;
    }

    public get currentValue() {
        return this._currentValue;
    }

    public set currentValue(value) {
        this._currentValue = value;

        $(this.settings.elementId).dataset.value = value;
    }
}