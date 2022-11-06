import { addToast } from "../functions"

export default class TutoShare extends HTMLAnchorElement{


    constructor () {
        super()
    }

    connectedCallback () {
        this.addEventListener('click', this.handleClick)
    }

    disconnectedCallback () {
        this.removeEventListener('click', this.handleClick)
    }

    /**
     * 
     * @param {Event} event 
     * @returns {void}
     */
    async handleClick (event) {
        event.preventDefault()

        if(!navigator.canShare){
            addToast('Erreur de partage', 'Votre navigateur ne permet pas le partage.', 'danger')
            return
        }

        try {
            await navigator.share({
                title: this.dataset.title,
                text: this.dataset.text,
                url: this.dataset.url
            })

            addToast('Partage reussi', "Merci d'avoir partager", 'success')
        } catch (error) {
            addToast('Erreur de partage', 'Votre navigateur ne permet pas le partage.', 'danger')
            return
        }
    }
}