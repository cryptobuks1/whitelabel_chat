import * as types from './types'
import axiosWithoutToken from '../api/axios-without-token'
import axios from '../api/axios-with-token'
import axiosWithFormData from '../api/axios-with-form-data'

export default {
    [types.TOGGLE_CHAT_PROFILE]: ({ commit }) => {
        commit(types.MUTATE_CHAT_PROFILE)
    },

    [types.APP_LOGIN]: async ({ commit }, params) => {
        await axiosWithoutToken.post('login', params).then((response) => {
            commit(types.MUTATE_APP_LOGIN, response.data)
            return true
        })
    },

    [types.APP_REGISTER]: async ({ commit }, params) => {
        await axiosWithoutToken.post('register', params).then(() => {
            return true
        })
    },

    [types.APP_LOGOUT]: async ({ commit }, params) => {
        await axios.get('logout', params).then((response) => {
            commit(types.MUTATE_APP_LOGOUT, response.data)
            return true
        })
    },

    [types.GET_LOGGED_USER_PROFILE]: async ({ commit }, params) => {
        await axios.get('profile', params).then((response) => {
            commit(types.MUTATE_LOGGED_USER_PROFILE, response.data)
            return true
        })
    },

    [types.GET_USER_LIST]: async ({ commit }, params) => {
        return axios.get('users-list', params).then((response) => {
            commit(types.MUTATE_USER_LIST, response.data)
            return response.data
        })
    },

    [types.GET_USER_LIST_WITH_ROLE]: async ({ commit }, params) => {
        return axios.get('users', params).then((response) => {
            commit(types.MUTATE_USER_LIST_WITH_ROLE, response.data)
            return response.data
        })
    },

    [types.ADD_USER]: async ({ commit }, user) => {
        return axiosWithFormData.post('users', user).then((response) => {
            commit(types.MUTATE_ADD_USER, response.data)
            return response.data
        })
    },

    [types.EDIT_USER]: async ({ commit }, user) => {
        const id = user.get('id')
        return axiosWithFormData.post(`users/${id}/update`, user).
            then((response) => {
                commit(types.MUTATE_EDIT_USER, response.data)
                return response.data
            })
    },

    [types.DELETE_USER]: async ({ commit }, id) => {
        return axios.delete(`users/${id}`).
            then(() => {
                commit(types.MUTATE_DELETE_USER, id)
                return id
            })
    },

    [types.UPDATE_USER_STATUS]: async ({ commit }, id) => {
        return axios.post(`/users/${id}/active-de-active`).
            then(() => {
                commit(types.MUTATE_UPDATE_USER_STATUS, id)
                return id
            })
    },

    [types.GET_ROLE_LIST]: async ({ commit }, params) => {
        return axios.get('roles', params).then((response) => {
            commit(types.MUTATE_ROLE_LIST, response.data)
            return response.data
        })
    },

    [types.ADD_ROLE]: async ({ commit }, role) => {
        return axios.post('roles', role).then((response) => {
            commit(types.MUTATE_ADD_ROLE, response.data)
            return response.data
        })
    },

    [types.EDIT_ROLE]: async ({ commit }, role) => {
        return axios.post(`roles/${role.id}/update`, role).
            then((response) => {
                commit(types.MUTATE_EDIT_ROLE, response.data)
                return response.data
            })
    },

    [types.DELETE_ROLE]: async ({ commit }, id) => {
        return axios.delete(`roles/${id}`).
            then(() => {
                commit(types.MUTATE_DELETE_ROLE, id)
                return id
            })
    },

    [types.SELECT_USER_TO_CHAT]: ({ commit }, user) => {
        commit(types.MUTATE_SELECT_USER_TO_CHAT, user)
    },

    [types.SEND_MESSAGE]: async ({ commit }, params) => {
        return axios.post('send-message', params).then((response) => {
            return response.data
        })
    },

    [types.GET_USER_CONVERSATIONS]: async ({ commit }, requestObj) => {
        let url = `users/${requestObj.userId}/conversation?limit=${requestObj.limit}`
        if (requestObj.before) {
            url = `${url}&before=${requestObj.before}`
        }
        if (requestObj.after) {
            url = `${url}&after=${requestObj.after}`
        }

        return axios.get(url).then((response) => {
            return response.data
        })
    },

    [types.UPDATE_USER_LAST_SEEN_STATUS]: async ({ commit }, status) => {
        return axios.post('update-last-seen', { status: status }).
            then((response) => {
                return response.data
            })
    },

    [types.UPDATE_MESSAGE_STATUS_TO_READ]: async ({ commit }, messageId) => {
        return axios.get(`chat/${messageId}/read`).then((response) => {
            return response.data
        })
    },

    [types.GET_CONVERSATION_USERS]: async ({ commit }) => {
        return axios.get('conversations').then((response) => {
            commit(types.MUTATE_CONVERSATION_USERS, response)
            return response.data
        })
    },

    [types.MARK_MESSAGE_STATUS_READ]: async ({ commit }, messageIds) => {
        return axios.post('read-message', { ids: messageIds }).
            then((response) => {
                return response.data
            })
    },

    [types.UPLOAD_FILE]: async ({ commit }, messageIds) => {
        return axios.post('file-upload', { ids: messageIds }).
            then((response) => {
                return response.data
            })
    },

    [types.UPDATE_LOGGED_USER_PROFILE]: async ({ commit }, reqObj) => {
        return axiosWithFormData.post('profile', reqObj).
            then((response) => {
                return response
            })
    },

    [types.APP_FORGOT_PASSWORD]: async ({ commit }, req) => {
        return axiosWithoutToken.post('password/reset', req).
            then((response) => {
                return response
            })
    },

    [types.APP_PASSWORD_RESET]: async ({ commit }, req) => {
        return axiosWithoutToken.post('password/update', req).
            then((response) => {
                return response
            })
    },

    [types.DELETE_CONVERSATION]: async ({ commit }, id) => {
        return axios.get(`conversations/${id}/delete`).
            then((response) => {
                return response
            })
    },
}
