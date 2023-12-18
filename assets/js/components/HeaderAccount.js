export default class HeaderAccount extends HTMLDivElement {
    constructor() {
        super()

        this.toggleButton = this.querySelector('#js-account-toggle')
        this.account = this.querySelector('#js-header-account')

        this.toggle = this.toggle.bind(this)
    }

    connectedCallback() {
        this.toggleButton.addEventListener('click', () => this.toggle())
        this.account.addEventListener('mouseleave', () => this.toggle())
    }

    disconnectedCallback() {
        this.toggleButton.removeEventListener('click', () => this.toggle())
        this.account.removeEventListener('mouseleave', () => this.toggle())
    }

    toggle() {
        if (this.account.classList.contains('hidden')) {
            this.account
                .animate([
                    { opacity: 0 },
                    { opacity: 1 }
                ], { duration: 200 })
                .ready.then(() => { this.account.classList.replace('hidden', 'flex') })
        } else {
            this.account
                .animate([
                    { opacity: 1 },
                    { opacity: 0 }
                ], { duration: 100 })
                .finished.then(() => { this.account.classList.replace('flex', 'hidden') })
        }
    }
}