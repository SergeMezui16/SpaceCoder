import { addToast } from "../functions"

export default class ToastBox extends HTMLDivElement {

    constructor () {
        super()
    }

    connectedCallback () {
        this.getToasts().forEach((toast) => {
            addToast(toast.title, toast.content, toast.feed)
        })
    }

    /**
     * Get Toasts injected by server in DOM
     * @returns {Object[]}
     */
    getToasts () {
        return Array.from(document.querySelectorAll('.js-toast'))
            .map( (e) => {
                return {title: document.title, content: e.dataset.content, feed: e.dataset.feed}
        })

    } 
}