class GainResources {

    private selectedResources: string[] = [];

    constructor(private elementId: string,
                private nrOfResourcesToGain: number,
                private resourceOptions: string[],
                private onCancel: () => void,
                private onConfirm: (resources: string[]) => void) {
        dojo.place('<div id="dp-gain-resources-wrapper"></div>', $(this.elementId))

        this.resetSelection();
        this.updateUi();
    }

    private resetSelection() {
        this.selectedResources = [];
        for (let i = 0; i < this.nrOfResourcesToGain; i++) {
            this.selectedResources.push('placeholder');
        }
    }

    private updateUi() {
        const wrapperElement = $('dp-gain-resources-wrapper');
        dojo.empty(wrapperElement);

        if (this.selectedResources.includes('placeholder')) {
            dojo.place(this.createResourceButtons(), wrapperElement)
            this.resourceOptions.forEach((resource) => dojo.connect($(`dp-dog-cost-pay-${resource}-button`), 'onclick', () => this.useResource(resource)))
        }
        dojo.place(this.createCostRow(), wrapperElement)
        dojo.place(this.createMainButtons(), wrapperElement)

        if (this.selectedResources.some(value => value != 'placeholder')) {
            dojo.connect($(`dp-dog-cost-pay-reset-button`), 'onclick', () => {
                this.resetSelection();
                this.updateUi();
            });
        }

        dojo.connect($(`dp-dog-cost-pay-cancel-button`), 'onclick', () => { this.onCancel(); });
        dojo.connect($(`dp-dog-cost-pay-confirm-button`), 'onclick', () => { this.onConfirm(this.selectedResources); });

    }

    private createCostRow() {
        let result = `<div class="dp-dog-cost-pay-row">${_('Gain')}</div>`;
        result += `<div class="dp-dog-cost-pay-row">`
        this.selectedResources.forEach(resource => {
            result += `<span class="dp-token-token" data-type="${resource}"></span>`
        })
        result += '</div>';
        return result;
    }

    private createResourceButtons() {
        let result = `<div class="dp-dog-cost-pay-row">`;
        this.resourceOptions
            .forEach((resource) => {
                result += `<a id="dp-dog-cost-pay-${resource}-button" class="bgabutton bgabutton_blue"><span class="dp-token-token" data-type="${resource}"></span></a>`
            })

        result += '</div>';
        return result;
    }

    private createMainButtons() {
        let result = `<div class="dp-dog-cost-pay-row">`;
        const disabled = this.selectedResources.includes('placeholder');
        result += `<a id="dp-dog-cost-pay-confirm-button" class="bgabutton bgabutton_blue ${disabled ? 'disabled' : ''}">${_('Confirm')}</a>`
        if (this.selectedResources.some(value => value != 'placeholder')) {
            result += `<a id="dp-dog-cost-pay-reset-button" class="bgabutton bgabutton_gray">${_('Reset')}</a>`
        }
        result += `<a id="dp-dog-cost-pay-cancel-button" class="bgabutton bgabutton_gray">${_('Cancel')}</a>`

        result += '</div>';
        return result;
    }


    private useResource(resource: string) {
        const index = this.selectedResources.indexOf('placeholder');
        this.selectedResources[index] = resource;
        this.updateUi();
    }
}