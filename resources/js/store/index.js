import Vue from 'vue'
import Vuex from 'vuex'

import getters from './getters'
import state from './state'
import actions from './actions'
import mutations from './mutations'

Vue.use(Vuex)

export const store = new Vuex.Store({
    getters,
    state,
    mutations,
    actions,
})
