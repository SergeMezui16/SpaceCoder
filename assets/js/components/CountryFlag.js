export default class CountryFlag extends HTMLImageElement {

    #api = "https://countryflagsapi.com"

    constructor() {
        super()

        this.country = this.dataset.country.toLocaleLowerCase()
        this.type = this.dataset.type.toLocaleLowerCase()
    }

    connectedCallback() {
        this.setAttribute('src', `${this.#api}/${this.type}/${this.country}`)
    }

    disconnectedCallback() {}
}