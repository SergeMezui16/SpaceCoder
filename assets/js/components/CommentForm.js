export default class CommentForm extends HTMLFormElement
{
    constructor () {
        super()

        this.comments = document.querySelectorAll(this.dataset.comments)

        /** @var HTMLSelectElement */
        this.selectInput = this.querySelector('select')
        /** @var HTMLOptionElement */
        this.options = this.selectInput.querySelectorAll('option')

        this.reply = this.querySelector('.js-input-out')

        this.out = this.reply.querySelector('div.form-input')
    }

    connectedCallback () {
        if(this.selectInput.selectedIndex !== 0) this.reply.removeAttribute('style')

        this.loadInput()

        this.querySelector('input[type="reset"]').addEventListener('click', () => this.handleReset())

        this.comments.forEach(comment => {
            comment.querySelector('.js-btn-reply').addEventListener('click', () => this.handleClick(comment))
        })
    }


    disconnectedCallback () {
        this.querySelector('input[type="reset"]').removeEventListener('click', () => this.handleReset())
        this.comments.forEach(comment => {
            comment.querySelector('.js-btn-reply').removeEventListener('click', () => this.handleClick(comment))
        })
    }

    /**
     * Change value of replyTo field in comment from
     * @param {HTMLElement} element 
     */
    handleClick (element) {
        this.options.forEach(option => {

            if(option.value === element.dataset.id){
                this.selectInput.selectedIndex = option.index
                this.reply.removeAttribute('style')
                this.loadInput()
                this.querySelector('textarea').focus()
            }
        })

        console.log(element, this.options)
        this.goToForm()
    }

    handleReset () {
        this.reply.setAttribute('style', 'display: none;')
        this.selectInput.selectedIndex = 0
    }

    loadInput () {
        this.out.innerText = this.selectInput.selectedOptions[0].innerText
    }

    goToForm () {
        let link = window.location.href

        if(!link.includes(this.id)) link = link + '#' + this.id

        window.location.href = link
    }

}