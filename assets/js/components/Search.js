export default class Search extends HTMLFormElement {


    constructor() {
        super()

        this.searchBar = this.querySelector('#js-search')
        this.searchButton = this.querySelector('#js-search-btn')
        this.closeButton = this.querySelector('#js-close-search-btn')
        this.input = this.searchBar.querySelector('input')

        this.open = this.open.bind(this)
        this.close = this.close.bind(this)
        this.toggle = this.toggle.bind(this)
    }

    connectedCallback() {
        this.searchButton.addEventListener('click', () => this.open())
        this.closeButton.addEventListener('click', () => this.close())
        this.input.addEventListener('blur', () => this.close())
    }


    disconnectedCallback() {
        this.searchButton.removeEventListener('click', () => this.open())
        this.closeButton.removeEventListener('click', () => this.close())
        this.input.removeEventListener('blur', () => this.close())
    }

    toggle() {
        this.searchBar.classList.toggle('is-visible')
    }

    open() {
        this.searchBar
            .animate([
                { opacity: 0 },
                { opacity: 1 }
            ], { duration: 200 })
            .ready.then(() => {
                this.toggle()
                this.input.focus()
            })
    }

    close() {
        this.searchBar
            .animate([
                { transform: 'translateY(0)', opacity: 1 },
                { transform: 'translateY(-100vh)', opacity: 0 }
            ], { duration: 300 })
            .finished.then(() => this.toggle())
    }
}