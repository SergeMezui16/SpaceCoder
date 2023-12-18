export default class AvatarForm extends HTMLDivElement {

    #fileTypes = [
        'image/jpeg',
        'image/pjpeg',
        'image/png',
        'image/gif'
    ]

    constructor() {
        super()

        this.btn = this.querySelector(this.dataset.btn)
        this.img = this.querySelector(this.dataset.img)
        this.input = this.querySelector('input')
        this.handleImage = this.handleImage.bind(this)
        this.openFileInput = this.openFileInput.bind(this)
        this.validFileType = this.validFileType.bind(this)
        this.returnFileSize = this.returnFileSize.bind(this)
    }


    connectedCallback() {
        this.btn.addEventListener('click', () => this.openFileInput())
        this.input.addEventListener('change', () => this.handleImage())
    }


    disconnectedCallback() {
        this.btn.removeEventListener('click', () => this.openFileInput())
        this.input.removeEventListener('change', () => this.handleImage())
    }

    handleImage() {
        const curFiles = this.input.files

        if (curFiles.length === 0) return

        for (let i = 0; i < curFiles.length; i++) {
            if (this.validFileType(curFiles[i])) {
                this.img.src = window.URL.createObjectURL(curFiles[i])
            } else {
                console.log('non valide', curFiles[i].type, 2)
            }
        }

    }

    openFileInput() {
        this.input.click()
    }

    /**
     * Check if the type is correct
     * @param {File} file 
     * @returns {boolean}
     */
    validFileType(file) {
        for (let i = 0; i < this.#fileTypes.length; i++) {
            if (this.#fileTypes[i] === file.type) {
                return true
            }
        }
        return false
    }

    returnFileSize(number) {
        if (number < 1024) {
            return number + ' octets'
        } else if (number >= 1024 && number < 1048576) {
            return (number / 1024).toFixed(1) + ' Ko'
        } else if (number >= 1048576) {
            return (number / 1048576).toFixed(1) + ' Mo'
        }
    }

}