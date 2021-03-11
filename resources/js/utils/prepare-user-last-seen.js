import Vue from 'vue'
import VueMoment from 'vue-moment'
import moment from 'moment-timezone'

Vue.use(VueMoment, {
    moment,
})

const DATE_TIME_FORMAT = 'YYYY-M-D HH:mm:ss'
const TIMELINE_DATE_FORMAT = 'dddd, MMM Do'

export function prepareUserLastSeen (dateTime) {
    if (dateTime) {
        const format = ' hh:mm a'
        return (Vue.moment(dateTime, DATE_TIME_FORMAT).
                utc(dateTime).
                local().
                calendar(null, {
                    sameDay: '[Today]' + format,
                    lastDay: '[Yesterday]' + format,
                    lastWeek: TIMELINE_DATE_FORMAT + format,
                    sameElse: () => {
                        if (Vue.moment().year() ===
                            Vue.moment(dateTime).year()) {
                            return TIMELINE_DATE_FORMAT + format
                        } else {
                            return 'dddd, MMM Do YYYY' + format
                        }
                    },
                })
        )
    }
}
