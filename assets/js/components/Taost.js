import { addToast } from "../functions"

export default class Taost extends HTMLElement {

    constructor () {
        super()
    }


    connectedCallback() {

        addToast(
            this.title = this.querySelector('.toast-title').textContent,
            this.content = this.querySelector('.toast-content').textContent
        )
    }
}