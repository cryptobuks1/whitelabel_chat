import * as types from './types'

export default {
    [types.USER_CHAT_PROFILE]: (state) => {
        return state.isChatUserProfileVisible
    },

    [types.LOGGED_USER_TOKEN]: (state) => {
        return state.infyChatAppToken
    },

    [types.LOGGED_USER]: (state) => {
        return state.loggedUser
    },

    [types.USER_LIST]: (state) => {
        return state.users
    },

    [types.USER_LIST_WITH_ROLE]: (state) => {
        return state.usersWithRole
    },

    [types.ROLE_LIST]: (state) => {
        return state.roles
    },

    [types.SELECTED_USER_FOR_CHAT]: (state) => {
        return state.selectedUserForChat
    },

    [types.CONVERSATION_USERS]: (state) => {
        return state.conversationUsers
    },
}
