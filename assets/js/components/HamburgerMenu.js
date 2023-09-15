export default class HamburgerMenu extends HTMLElement {

    constructor () {
        super()

        this.menuButton = document.getElementById('js-menu-btn')
        this.closeButton = this.querySelector('#js-close-menu-btn')

        this.observer = new IntersectionObserver(this.visibleCallback, {
            root: this,
            rootMargin: '0px',
            threshold: 1.0
        })
    }


    connectedCallback () {
        this.menuButton.addEventListener('click', () => this.open())
        this.closeButton.addEventListener('click', () => this.close())
    }


    disconnectedCallback () {
        this.menuButton.removeEventListener('click', () => this.open())
        this.closeButton.removeEventListener('click', () => this.close())
    }

    toggle () {
        this.classList.toggle('hidden')
    }


    /**
     * Open the Menu
     */
    open () {        
        this
            .animate([
                {transform: 'translateX(-100%)', opacity: 0},
                {transform: 'translateX(0)', opacity: 1}
            ], {duration: 300})
            .ready.then(() => this.toggle())

        this.querySelectorAll('li').forEach( (li) => {
            this.observer.observe(li)
        })
    }

    /**
     * Close the Menu
     */
    close () {
        this
            .animate([
                {transform: 'translateX(0)', opacity: 1},
                {transform: 'translateX(-100%)', opacity: 0}
            ], {duration: 200})
            .finished.then(() => this.toggle())        
    }


    /**
     * 
     * @param {any} entries 
     */
    visibleCallback (entries) {
        entries.forEach( (entry) => {

            if (entry.isIntersecting) {

                entry.target.animate([
                    {transform: 'translateX(-30px)', opacity: 0},
                    {transform: 'translateX(0)', opacity: 1}
                ], {
                    duration: 300, 
                    iterations: 1, 
                    delay: 100,
                    easing: 'cubic-bezier(0.8, 0.04, 0.98, 0.335)'
                })
            }
        })
    }
}