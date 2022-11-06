/**
 * Add a toast on the DOM
 * @param {string} title 
 * @param {string} content
 * @param {string|undefined} feed
 */
export const addToast = (title, content, feed = undefined) => {

    let layout = document.querySelector('#toast-layout').content.cloneNode(true).firstElementChild

    layout.querySelector('.toast-title').textContent = title
    layout.querySelector('.toast-content').textContent = content
    if(feed !== undefined) layout.classList.add(feed)
    layout.querySelector('.toast-close').addEventListener('click', () => handleCloseToast(layout))
    document.querySelector('.toast-box').append(layout)
}

/**
 * Close a toast
 * @param {HTMLElement} element 
 */
export const handleCloseToast = (element) => {
    element
        .animate([
            {transform: 'translateX(0px)', opacity: 1},
            {transform: 'translateX(200px)', opacity: 0}
        ], {duration: 200})
        .finished.then(() => element.remove())
}
