export default class CountryFlag extends HTMLImageElement {

    #api = "/bundles/easyadmin/images/flags"

    constructor() {
        super()

        this.country = this.dataset.country
        this.type = this.dataset.type.toLocaleLowerCase()
    }

    connectedCallback() {
        this.setAttribute('src', `${this.#api}/${this.country}.svg`)
    }

    disconnectedCallback() {}
}