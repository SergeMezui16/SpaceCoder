import HamburgerMenu from "./components/HamburgerMenu"
import HeaderAccount from "./components/HeaderAccount"
import PasswordInput from "./components/PasswordInput"
import TuToCarousel from "./components/TutoCarousel"
import TutoShare from "./components/TutoShare"
import ToastBox from "./components/ToastBox"
import CopyForm from "./components/CopyForm"
import Search from "./components/Search"
import Header from "./components/Header"
// import Taost from "./components/Taost"
import { addToast } from "./functions"
import DomElements from "./components/DomElements"


// Web Components
customElements.define('hamburger-menu', HamburgerMenu, {extends: 'nav'})
customElements.define('search-bar', Search, {extends: 'form'})
customElements.define('password-input', PasswordInput, {extends: 'div'})
customElements.define('header-account', HeaderAccount, {extends: 'div'})
customElements.define('tuto-carousel', TuToCarousel, {extends: 'div'})
customElements.define('header-componnent', Header, {extends: 'header'})
customElements.define('tuto-share', TutoShare, {extends: 'a'})
customElements.define('toast-box', ToastBox, {extends: 'div'})
customElements.define('copy-form', CopyForm, {extends: 'div'})
customElements.define('dom-elements', DomElements, {extends: 'span'})
// customElements.define('toast-item', Taost)

// TEST ADD TOAST - UI
document.querySelector('.js-test-toast')?.addEventListener('click', () => addToast('Lorem', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.'))