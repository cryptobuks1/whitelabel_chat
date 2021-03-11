import Vue from 'vue'
import VueMoment from 'vue-moment'
import moment from 'moment-timezone'

Vue.use(VueMoment, {
    moment,
})

/**
 * utc to local time conversion
 * @param date
 * @param format
 * @returns {string}
 */
export function utcToLocalDatetime (date, format = 'YYYY-MM-DD HH:mm:ss') {
    const stillUtc = Vue.moment.utc(date, format).toDate()
    return Vue.moment(stillUtc).local().format(format)
}
