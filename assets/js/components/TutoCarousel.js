/**
 * @typedef {Object} TutoImage
 * @property {number} id
 * @property {string} url
 * @property {number} description
 */


export default class TuToCarousel extends HTMLDivElement {

    constructor() {
        super()

        this.figures = document.querySelectorAll('.tuto figure')
        /**
         * @type {TutoImage[]}
         */
        this.images = this.getTutoImages(this.figures)


        this.srcInput = this.querySelector('.content img')
        this.captionInput = this.querySelector('.content span.title')

        this.nextBtn = this.querySelector('.next')
        this.prevBtn = this.querySelector('.prev')
        this.closeBtn = this.querySelector('.carousel .close')

        this.img = null

        this.getTutoImages = this.getTutoImages.bind(this)
        this.getTutoImage = this.getTutoImage.bind(this)
        this.toggle = this.toggle.bind(this)
        this.load = this.load.bind(this)
        this.getNextImg = this.getNextImg.bind(this)
        this.getPrevImg = this.getPrevImg.bind(this)
    }


    connectedCallback() {

        this.figures.forEach((figure) => {
            figure.addEventListener('click', (e) => {
                e.preventDefault()

                this.img = this.getTutoImage(e.currentTarget)
                if (this.img === undefined) return
                this.load(this.img)
                this.toggle()
            })
        })


        this.nextBtn.addEventListener('click', (e) => {
            e.preventDefault()
            this.img = this.getNextImg()
            this.load()
        })


        this.prevBtn.addEventListener('click', (e) => {
            e.preventDefault()
            this.img = this.getPrevImg()
            this.load()
        })

        this.closeBtn.addEventListener('click', (e) => {
            e.preventDefault()
            this.toggle()
        })

    }


    disconnectedCallback() {
        this.figures.forEach((figure) => {
            figure.removeEventListener('click', () => { })
        })

        this.nextBtn.removeEventListener('click', () => { })
        this.prevBtn.removeEventListener('click', () => { })
        this.closeBtn.removeEventListener('click', (e) => { })
    }




    /**
     * Return a TutoImage Array from the NodeList
     * @param {NodeList} figures
     * @returns {TutoImage[]}
     */
    getTutoImages(figures) {
        /**
         * @type {TutoImage[]}
         */
        const images = []
        Array.from(figures).flatMap((el, i) => {
            images.push({
                id: i,
                url: el.querySelector('img').src,
                description: el.querySelector('figcaption').textContent
            })
        })
        return images;
    }

    /**
     * Transform HTMLElement to TutoImage
     * 
     * @param {HTMLElement} figure 
     * @returns {TutoImage|undefined}
     */
    getTutoImage(figure) {
        const imgUrl = figure.querySelector('img').src
        return this.images.find((el) => el.url === imgUrl)
    }

    /**
     * Open or Close the Carousel
     */
    toggle() {
        this.animate([
            { transform: 'scale(.1)', opacity: 0 },
            { transform: 'scale(1)', opacity: 1 }
        ], { duration: 250 })
        this.classList.toggle('open')
    }

    /**
     * Load the Carousel datas With current TutoImage
     */
    load() {
        this.srcInput.setAttribute('src', this.img.url)
        this.srcInput.setAttribute('alt', this.img.description)
        this.srcInput.setAttribute('title', this.img.description)
        this.captionInput.innerText = this.img.description + `  (${this.img.id + 1} / ${this.images.length})`
    }


    getNextImg() {
        const element = this.images.find((el) => el.id === (this.img.id + 1))
        return element === undefined ? this.images[0] : element
    }

    getPrevImg() {
        const element = this.images.find((el) => el.id === (this.img.id - 1))
        return element === undefined ? this.images[this.images.length - 1] : element
    }
}