export default class DomElements extends HTMLSpanElement {
    constructor () {
        super()

        this.comments = document.querySelectorAll('.comment')
        this.backToTop = document.querySelector('.back-to-top')
    }


    connectedCallback() {
        window.addEventListener('scroll', () => this.#showOrHideToTop())
        this.backToTop.addEventListener('click', () => this.#goToTop())
        if(this.comments.length > 0) this.#blinkComment(this.comments)
    }

    disconnectedCallback() {
        window.removeEventListener('scroll', () => this.#showOrHideToTop())
        this.backToTop.removeEventListener('click', () => this.#goToTop())
    }



    /**
     * Move the page to the top
     */
    #goToTop () {
        this.backToTop.animate([
            {transform: 'translateY(0)', opacity: 1},
            {transform: 'translateY(-100vh)', opacity: 0},

        ], {duration: 300}).playState === 'running' ? window.scrollTo(0, 0) : undefined        
    }
    
    /**
     * Show or hide the button to move to the top
     */
    #showOrHideToTop () {
        const scrolled = ((document.body.scrollTop || document.documentElement.scrollTop) / (document.documentElement.scrollHeight - document.documentElement.clientHeight)) * 100

        if(this.backToTop.style.display === 'none'){
            this.backToTop.animate([
                {transform: 'translateY(50px)', opacity: 0},
                {transform: 'translateY(0)', opacity: 1},
            ], {duration: 150, iterations: 1})

        }
        this.backToTop.style.display = (scrolled > 10) ? 'flex' : 'none'
    }

    /**
     * Blink a comment
     * @param {HTMLElement[]} comments 
     */
    #blinkComment (comments) {
       comments.forEach(element => {
            if('#' + element.id === location.hash) element.classList.add('bg-transparent-yellow')
       });  
    }
}