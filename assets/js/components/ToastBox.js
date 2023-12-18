import { addToast } from "../functions"

export default class ToastBox extends HTMLDivElement {

    constructor() {
        super()
        this.getToasts = this.getToasts.bind(this)
    }

    connectedCallback() {
        this.getToasts().forEach((toast) => {
            addToast(toast.title, toast.content, toast.feed)
        })
    }

    /**
     * Get Toasts injected by server in DOM
     * @returns {Object[]}
     */
    getToasts() {
        return Array.from(document.querySelectorAll('.js-toast'))
            .map((e) => {
                return { title: e.dataset.title, content: e.dataset.content, feed: e.dataset.feed }
            })

    }
}