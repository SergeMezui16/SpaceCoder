export default class Notification extends HTMLAnchorElement {

    constructor() {
        super()
        this.uri = this.dataset.uri
        this.handleClick = this.handleClick.bind(this)
    }

    connectedCallback() {
        this.addEventListener('click', (event) => this.handleClick(event))
    }

    disconnectedCallback() {
        this.removeEventListener('click', (event) => this.handleClick(event))
    }


    /**
     * 
     * @param {MouseEvent} event 
     */
    handleClick(event) {
        event.preventDefault()

        fetch(this.uri, { method: 'GET' })
            .then((response) => response.json())
            .then((target) => window.location.href = target)
    }

}