import * as types from './types'

export default {
    [types.MUTATE_CHAT_PROFILE]: (state) => {
        state.isChatUserProfileVisible = !state.isChatUserProfileVisible
    },

    [types.MUTATE_APP_LOGIN]: (state, result) => {
        localStorage.setItem('infyChatAppToken', result.token)
        state.infyChatAppToken = result.token
    },

    [types.MUTATE_APP_REGISTER]: (state, result) => {
        state.infyChatAppToken = result.token
    },

    [types.MUTATE_APP_LOGOUT]: (state) => {
        localStorage.removeItem('infyChatAppToken')
        state.infyChatAppToken = ''
    },

    [types.MUTATE_LOGGED_USER_PROFILE]: (state, result) => {
        state.loggedUser = result.user
    },

    [types.MUTATE_USER_LIST]: (state, result) => {
        state.users = result.users
    },

    [types.MUTATE_USER_LIST_WITH_ROLE]: (state, result) => {
        state.usersWithRole = result.users
    },

    [types.MUTATE_ADD_USER]: (state, res) => {
        state.usersWithRole.push(res.user)
    },

    [types.MUTATE_EDIT_USER]: (state, res) => {
        const user = res.user
        const index = state.usersWithRole.findIndex(u => +u.id === +user.id)
        if (index >= 0) {
            state.usersWithRole.splice(index, 1, user)
        }
    },

    [types.MUTATE_DELETE_USER]: (state, id) => {
        const index = state.usersWithRole.findIndex(u => +u.id === +id)
        if (index >= 0) {
            state.usersWithRole.splice(index, 1)
        }
    },

    [types.MUTATE_UPDATE_USER_STATUS]: (state, id) => {
        const index = state.usersWithRole.findIndex(u => +u.id === +id)
        if (index >= 0) {
            state.usersWithRole[index].is_active = +state.usersWithRole[index].is_active ===
            1
                ? 0
                : 1
        }
    },

    [types.MUTATE_ROLE_LIST]: (state, result) => {
        state.roles = result
    },

    [types.MUTATE_ADD_ROLE]: (state, res) => {
        state.roles.push(res.role)
    },

    [types.MUTATE_EDIT_ROLE]: (state, res) => {
        const role = res.role
        const index = state.roles.findIndex(u => +u.id === +role.id)
        if (index >= 0) {
            state.roles.splice(index, 1, role)
        }
    },

    [types.MUTATE_DELETE_ROLE]: (state, id) => {
        const index = state.roles.findIndex(r => +r.id === +id)
        if (index >= 0) {
            state.roles.splice(index, 1)
        }
    },

    [types.MUTATE_SELECT_USER_TO_CHAT]: (state, user) => {
        state.selectedUserForChat = user
    },

    [types.MUTATE_CONVERSATION_USERS]: (state, response) => {
        const conversationUsers = response.data.conversations
        conversationUsers.sort((a, b) => {
            const keyA = new Date(a.created_at)
            const keyB = new Date(b.created_at)
            if (keyA <= keyB) return 1
            if (keyA > keyB) return -1
            return 0
        })
        state.conversationUsers = conversationUsers
    },
}
