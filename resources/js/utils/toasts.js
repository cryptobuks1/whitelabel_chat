import Vue from 'vue'
import Toasted from 'vue-toasted'

Vue.use(Toasted)

const successToastOptions = {
    theme: 'toasted-primary',
    position: 'top-right',
    duration: 3000,
    icon: 'check',
    iconPack: 'fontawesome',
}

export function successMsgToast (msg) {
    Vue.toasted.success(msg, successToastOptions)
}

const errorToastOptions = {
    theme: 'toasted-primary',
    position: 'top-right',
    duration: 3000,
    icon: 'times',
    iconPack: 'fontawesome',
}

export function errorMsgToast (msg) {
    Vue.toasted.error(msg, errorToastOptions)
}
