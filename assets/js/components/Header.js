export default class Header extends HTMLElement {


    #keyframe = [
        { transform: 'translateY(-' + this.offsetHeight + 'px)', opacity: '0' },
        { transform: 'translateY(0px)', opacity: '1' }
    ]


    #options = {
        duration: 200
    }

    constructor() {
        super()

        this.show = false
        this.prevScrollpos = window.pageYOffset;
        this.handleScroll = this.handleScroll.bind(this)
    }

    connectedCallback() {
        window.addEventListener('scroll', this.handleScroll)
    }

    disconnectedCallback() {
        window.removeEventListener('scroll', this.handleScroll)
    }

    handleScroll() {

        // Dont show the header if menu is open
        if (!document.querySelector('[is="hamburger-menu"]').classList.contains('hidden')) return

        const currentScrollPos = window.pageYOffset;

        if (this.prevScrollpos > currentScrollPos && !this.show) {
            this.classList.add('show-header')
            this.animate(this.#keyframe, this.#options)
            this.show = true

        } else if (this.prevScrollpos < currentScrollPos && this.show) {

            let anim = this.animate([
                { transform: 'translateY(0px)', opacity: '1' },
                { transform: 'translateY(-' + this.offsetHeight + 'px)', opacity: '0' }
            ], { duration: 300 })
            anim.finished.then(() => this.classList.remove('show-header'))
            this.show = false

        } else if (currentScrollPos === 0) {
            this.classList.remove('show-header')
            this.show = true
        }

        this.prevScrollpos = currentScrollPos;
    }
}