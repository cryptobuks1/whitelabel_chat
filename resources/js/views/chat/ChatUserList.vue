<template>
    <div class="chat__people-wrapper" :class="toggleSearchIcon?'chat__people-wrapper--responsive':''">
        <div class="chat__people-wrapper-header">
            <span class="h3 mb-0">{{$t('chat.conversations')}}</span>
            <div class="d-flex chat__people-wrapper-btn-group">
                <i class="nav-icon fa align-top chat__people-wrapper-bar" :class="toggleSearchIcon?'fa-bars':'fa-times'"
                   @click.stop="toggleSearchIcon=!toggleSearchIcon"></i>
                <div class="chat__people-wrapper-button" @click="onOpenUserModal">
                    <i class="nav-icon icon-speech" data-toggle="tooltip" data-placement="bottom"
                       :title="$t('chat.addNewChat')"></i>
                </div>
            </div>
        </div>
        <div class="chat__search-wrapper" v-if="conversationUsers.length>0">
            <div class="chat__search clearfix chat__search--responsive" @click.stop="">
                <i class="fa fa-search"></i>
                <input type="text" :placeholder="$t('chat.search')" class="chat__search-input"
                       v-model="searchConversationUsersText" ref="searchConversationText">
                <i class="fa fa-search d-lg-none chat__search-responsive-icon"
                   @click.stop="onClickSearchIcon()"></i>
            </div>
        </div>
        <div class="chat__people-body" id="chatPeopleBody" v-click-outside="clickOutsideOfConversationDrawer">
            <div v-if="isLoading" class="chat__people-body--loader mt-3 d-flex justify-content-center">
                <InfyLoader/>
            </div>
            <fragment v-if="filteredConversationUsers.length>0 && !isLoading">
                <div class="chat__person-box"
                     v-for="(conversation, index) in filteredConversationUsers" :key="index"
                     @click.stop="onSelectUserForChat(conversation.user)"
                     :class="selectedUserForChat && selectedUserForChat.email === conversation.user.email ? 'chat__person-box--active':''">
                    <div class="position-relative chat__person-box-status-wrapper">
                        <div class="chat__person-box-status"
                             :class="conversation.user.is_online? 'chat__person-box-status--online': 'chat__person-box-status--offline'">
                        </div>
                        <div class="chat__person-box-avtar">
                            <img :src="conversation.user.photo_url" alt="person image" class="img-fluid">
                        </div>
                    </div>
                    <div class="chat__person-box-detail">
                        <h5 class="mb-1 chat__person-box-name">{{conversation.user.name}}</h5>
                        <i v-if="conversation.message_type === messageType.image" class="fa fa-camera"
                           aria-hidden="true"></i>
                        <i v-if="conversation.message_type === messageType.pdf" class="fa fa-file-pdf-o"
                           aria-hidden="true"></i>
                        <i v-if="conversation.message_type === messageType.doc" class="fa fa-file-word-o"
                           aria-hidden="true"></i>
                        <i v-if="conversation.message_type === messageType.voice" class="fa fa-file-audio-o"
                           aria-hidden="true"></i>
                        <i v-if="conversation.message_type === messageType.video" class="fa fa-file-video-o"
                           aria-hidden="true"></i>
                        <i v-if="conversation.message_type === messageType.xls" class="fa fa-file-excel-o"
                           aria-hidden="true"></i>
                        <i v-if="conversation.message_type === messageType.text_file" class="fa fa-file-text-o"
                           aria-hidden="true"></i>
                        <span class="mb-0"
                              v-if="conversation.message_type === messageType.image">{{$t('chat.photo')}}</span>
                        <span class="mb-0"
                              v-else-if="conversation.message_type === messageType.pdf">{{$t('chat.pdf')}}</span>
                        <span class="mb-0" v-else-if="conversation.message_type === messageType.doc">{{$t('chat.document')}}</span>
                        <span class="mb-0" v-else-if="conversation.message_type === messageType.voice">{{$t('chat.audio')}}</span>
                        <span class="mb-0" v-else-if="conversation.message_type === messageType.video">{{$t('chat.video')}}</span>
                        <span class="mb-0" v-else>{{conversation.message}}</span>
                    </div>
                    <div class="chat__person-box-msg-time">
                        <div class="chat__person-box-time" :title="conversation.created_at">
                            {{convertTime(conversation.created_at)}}
                        </div>
                        <div class="d-flex">
                            <div class="chat__person-box-count" v-if="+conversation.unread_count >0">
                                {{conversation.unread_count}}
                            </div>
                            <span class="float-right delete-conv-icon" @click.stop="onDeleteConversation(conversation)">
                                <i class="fa fa-times"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </fragment>
            <fragment v-if="filteredConversationUsers.length === 0 && !isLoading">
                <div class="chat__not-selected">
                    <div class="text-center"><i class="fa fa-user font-4xl" aria-hidden="true"></i></div>
                    {{$t('chat.noConversationMsg')}}
                </div>
            </fragment>
        </div>

        <!-- modal start (new conversation)-->
        <b-modal title="New Conversation" centered v-model="showUsersModal" hide-footer default="false">
            <form class="mb-2" v-if="users.length>0">
                <input type="text" class="form-control" id="searchContact" v-model="searchUserText"
                       placeholder="Search" ref="searchUserTextField">
            </form>
            <div v-if="filteredUsers.length>0" class="list-group-wrapper">
                <ul class="list-group user-list-chat-select" v-for="user in filteredUsers"
                    @click="onSelectUserForChat(user); showUsersModal=false">
                    <li class="list-group-item user-list-chat-select__list-item align-items-center">
                        <div class="new-conversation-img-status position-relative mr-2">
                            <div class="chat__person-box-status"
                                 :class="user.is_online?'chat__person-box-status--online': 'chat__person-box-status--offline'"></div>
                            <div class="new-conversation-img-status__inner">
                                <img :src="user.photo_url" alt="user-avatar-img" class="user-avatar-img">
                            </div>
                        </div>
                        <span class="chat__person-box-name">{{user.name}}</span>
                    </li>
                </ul>
            </div>
            <div v-else class="no-user-found">
                <div class="chat__not-selected" :class="filteredUsers.length>0 && !searchUserText?'mt-4':''">
                    <div class="text-center"><i class="fa fa-user font-4xl" aria-hidden="true"></i></div>
                    {{$t('chat.noUserFound')}}
                </div>
            </div>
        </b-modal>
        <!-- modal end (add new conversation)-->
    </div>
</template>

<script>
    import { mapActions, mapGetters } from 'vuex'
    import * as types from '../../store/types'
    import { MESSAGE_TYPE } from '../../constants/app-constants'
    import { laravelEcho } from '../../utils/laravel-echo'
    import { Fragment } from 'vue-fragment'
    import { clickOutside } from '../../directives/ClickOutside'
    import InfyLoader from '../../components/InfyLoader'

    const DATE_TIME_FORMAT = 'YYYY-M-D HH:mm:ss'
    export default {
        name: 'ChatUserList',
        data: () => {
            return {
                isLoading: true,
                showUsersModal: false,
                searchUserText: '',
                searchConversationUsersText: '',
                messageType: MESSAGE_TYPE,
                toggleSearchIcon: true,
            }
        },
        components: {
            Fragment,
            InfyLoader,
        },
        directives: {
            clickOutside,
        },
        computed: {
            filteredUsers () {
                return this.users.filter((element) => {
                    if (JSON.stringify(element).toLowerCase().indexOf(this.searchUserText.toLowerCase()) > -1) {
                        return element
                    }
                })
            },
            filteredConversationUsers () {
                return this.conversationUsers.filter((element) => {
                    if (JSON.stringify(element).toLowerCase().indexOf(this.searchConversationUsersText.toLowerCase()) >
                        -1) {
                        return element
                    }
                })
            },
            ...mapGetters({
                users: types.USER_LIST,
                selectedUserForChat: types.SELECTED_USER_FOR_CHAT,
                conversationUsers: types.CONVERSATION_USERS,
            }),
        },
        methods: {
            ...mapActions({
                getUsers: types.GET_USER_LIST,
                selectUserForChat: types.SELECT_USER_TO_CHAT,
                getConversationUsers: types.GET_CONVERSATION_USERS,
                deleteConversation: types.DELETE_CONVERSATION,
            }),
            onSelectUserForChat (user) {
                const index = this.conversationUsers.findIndex(cu => +cu.user.id === +user.id)
                const conversationUsers = this.$store.state.conversationUsers
                if (index === -1) {
                    conversationUsers.push({ user: user, conversations: [] })
                }
                if (this.showUsersModal) {
                    conversationUsers.sort((a, b) => {
                        if (!a.created_at || !b.created_at) {
                            return -1
                        } else {
                            const keyA = this.$moment(a.created_at, DATE_TIME_FORMAT)
                            const keyB = this.$moment(b.created_at, DATE_TIME_FORMAT)
                            if (keyA <= keyB) return 1
                            if (keyA > keyB) return -1
                            return 0
                        }
                    })
                }
                this.$store.state.conversationUsers = conversationUsers
                this.selectUserForChat(user)
            },
            checkForUserStatus () {
                this.echo.join(`user-status`).here((users) => {
                    setTimeout(() => {
                        users.forEach((user) => {
                            this.updateUserStatus(user, true)
                        })
                    }, 1000)
                }).joining((user) => {
                    this.updateUserStatus(user, true)
                }).leaving((user) => {
                    this.updateUserStatus(user, false)
                })
            },
            updateUserStatus (user, isOnline) {
                // update status of conversation user
                if (!isOnline) {
                    user.last_seen = this.$moment().utc()
                }
                const index = this.conversationUsers.findIndex(u => u.user.id === user.id)
                if (index >= 0) {
                    this.$store.state.conversationUsers[index].user.is_online = isOnline
                    this.$store.state.conversationUsers[index].user.last_seen = user.last_seen
                }
                if (this.selectedUserForChat && +user.id === +this.selectedUserForChat.id) {
                    let selectedUserForChat = this.selectedUserForChat
                    selectedUserForChat.is_online = isOnline
                    selectedUserForChat.last_seen = user.last_seen
                    this.onSelectUserForChat(selectedUserForChat)
                }
                // update status of all users
                const userIndex = this.users.findIndex(u => u.id === user.id)
                if (userIndex >= 0) {
                    this.users[userIndex].is_online = isOnline
                    this.users[userIndex].last_seen = user.last_seen
                }
            },
            onGetConversationUsers () {
                this.isLoading = true
                this.getConversationUsers().then(() => {
                    this.isLoading = false
                    setTimeout(() => {
                        this.checkForUserStatus()
                    }, 1000)
                }).catch(() => {
                    this.isLoading = false
                })
            },
            onGetUsers () {
                this.getUsers().then(() => {
                }).catch(() => {
                    this.isLoading = false
                })
            },
            convertTime (dateTime, format = 'hh:mma') {
                if (dateTime) {
                    let date = this.$moment(dateTime).utc(dateTime).local()
                    return date.calendar(null, {
                        sameDay: format,
                        lastDay: '[Yesterday]',
                        lastWeek: 'M/D/YY',
                        sameElse: 'M/D/YY',
                    })
                }
            },
            clickOutsideOfConversationDrawer () {
                this.toggleSearchIcon = true
            },
            onClickSearchIcon () {
                this.toggleSearchIcon = !this.toggleSearchIcon
                setTimeout(() => {
                    this.$refs.searchConversationText.focus()
                })
            },
            onOpenUserModal () {
                this.showUsersModal = true
                this.searchUserText = ''
                setTimeout(() => {
                    this.$refs.searchUserTextField.focus()
                })
            },
            onDeleteConversation (conversation) {
                this.$swal.fire({
                    title: 'Are you sure?',
                    type: 'warning',
                    html: `Delete chat with <strong class="text-capitalize">${conversation.user.name}</strong>?`,
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: 'lightgray',
                    confirmButtonText: 'Yes, delete it!',
                    focusCancel: true,
                }).then((result) => {
                    if (result.value) {
                        this.deleteConversation(conversation.user.id).then(() => {
                            if (+this.selectedUserForChat.id === +conversation.user.id) {
                                this.$store.state.selectedUserForChat = null
                            }
                            this.onGetConversationUsers()
                            this.$swal.fire(
                                {
                                    title: 'Deleted!',
                                    text: 'Conversation has been deleted!',
                                    type: 'success',
                                    timer: 1500,
                                },
                            )
                        })
                    }
                })
            },
        },
        created () {
            this.onGetUsers()
            this.onGetConversationUsers()
        },
        mounted () {
            this.echo = laravelEcho()
        },
    }
</script>

<style scoped lang="scss">
    .no-user-found {
        position: relative;
        height: 110px;
    }

    .list-group-wrapper {
        max-height: 27vh;
        overflow: auto;
    }

    #searchContact {
        box-shadow: none;
        outline: 0;
    }

    .delete-conv-icon {
        min-height: 20px;
        margin-left: auto;
        min-width: 15px;

        i {
            font-size: 18px;
            opacity: 0.5;
        }
    }

    .chat__person-box {

        .delete-conv-icon {
            i {
                display: none;
            }
        }

        &:hover {
            .delete-conv-icon {
                i {
                    display: inline-block;
                }
            }
        }
    }
</style>
