<template>
    <div class="d-inline">
        <fragment v-if="!selectedUserForChat">
            <div class="chat__not-selected">
                <div class="text-center"><i class="fa fa-comments font-4xl" aria-hidden="true"></i></div>
                {{$t('chat.noConversationSelMsg')}}
            </div>
        </fragment>
        <fragment v-else>
            <!-- chat area header-->
            <LightGallery
                :images="imageMessages"
                :index="index"
                :disable-scroll="true"
                @close="index = null"
            />
            <div class="chat__area-header position-relative">
                <div class="d-flex justify-content-between align-items-center flex-1">
                    <div class="d-flex">
                        <div class="chat__area-header-avatar">
                            <img :src="selectedUserForChat.photo_url" alt="personImage" class="img-fluid">
                        </div>
                        <div class="pl-3">
                            <h5 class="my-0 chat__area-title">{{selectedUserForChat.name}}</h5>
                            <div class="typing position-relative" v-if="!isTyping">
                                <span class="chat__area-header-status"
                                      :class="selectedUserForChat.is_online ?
                                      'chat__area-header-status--online':'chat__area-header-status--offline'"></span>
                                <span class="pl-3" v-if="selectedUserForChat.is_online">Online</span>
                                <span class="pl-3"
                                      v-if="!selectedUserForChat.is_online && selectedUserForChat.last_seen">
                                    {{$t('chat.lastSeen')}} <i>{{prepareLastSeenDate(selectedUserForChat.last_seen)}}</i>
                                </span>
                                <span class="pl-3"
                                      v-if="!selectedUserForChat.is_online && selectedUserForChat.last_seen === null">
                                    {{$t('chat.never')}}
                                </span>
                            </div>
                            <div v-if="isTyping">{{$t('chat.typing')}}</div>
                        </div>
                    </div>
                    <div class="chat__area-action">
                        <div class="chat__area-icon open-profile-menu" @click.stop="onToggleChatUserProfile()">
                            <i class="fa fa-cog" aria-hidden="true"></i>
                        </div>
                    </div>
                    <div class="dropdown cursor-pointer d-xl-none chat__profile-dropdown"
                         @click="isShowProfileDropdwon = !isShowProfileDropdwon">
                        <i class="fa fa-bars"></i>
                        <div class="dropdown-menu" :class="isShowProfileDropdwon?'show': ''">
                            <a class="dropdown-item open-profile-menu"
                               @click.stop="onToggleChatUserProfile(); isShowProfileDropdwon=!isShowProfileDropdwon">
                                {{$t('chat.profile')}}</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- //chat area header-->
            <div class="chat-conversation" id="chatConversation" @scroll="onScrollMessages"
                 @dragover="mediaMessageModal= true">
                <div v-if="isLoading" class="chat__people-body--loader mt-3 d-flex justify-content-center">
                    <InfyLoader/>
                </div>
                <div v-else-if="conversationsGroupByDate.length>0">
                    <div v-for="(conversationsGroup, groupIndex) in  conversationsGroupByDate" :key="groupIndex">
                        <div class="chat__msg-day-divider position-relative">
                            <span class="chat__msg-day-title position-absolute">
                                {{conversationsGroup.key}}
                            </span>
                        </div>
                        <div v-for="(chat, index) in conversationsGroup.conversations" :key="index">
                            <div v-if="firstUnreadMessageId === chat.id"
                                 class="chat__msg-day-divider position-relative">
                                <span class="chat__msg-day-new-msg position-absolute">new messages</span>
                            </div>
                            <div
                                :class="loggedUser && chat.from_id === loggedUser.id?
                                'chat-conversation__sender': 'chat-conversation__receiver'"
                                :id="`message_${+chat.id}`">
                                <div class="chat-conversation__avatar">
                                    <img :src="chat.sender.photo_url" :alt="chat.sender.name" class="img-fluid">
                                </div>
                                <div class="chat-conversation__bubble clearfix">
                                    <div class="chat-conversation__bubble-text">
                                        <span v-if="chat.message_type === messageType.text" class="word-break"
                                              v-html="linkifyTextMessage(chat.message)">
                                        </span>
                                        <span v-if="chat.message_type === messageType.image" class="cursor-pointer"
                                              @click="openMediaInFullSize(chat)">
                                            <img :src="chat.message" alt="Chat Message"/>
                                        </span>
                                        <span v-if="chat.message_type === messageType.video">
                                            <video-player :options="chat.videoOptions"/>
                                        </span>
                                        <span v-if="chat.message_type === messageType.voice">
                                            <audio controls>
                                              <source :src="chat.message" type="audio/ogg">
                                              <source :src="chat.message" type="audio/mpeg">
                                              {{$t('chat.browserNotSupportAudio')}}
                                            </audio>
                                        </span>
                                        <span v-if="chat.message_type === messageType.youtubeUrl">
                                            <iframe width="250" height="250" :src="getYoutubeEmbedURL(chat.message)"
                                                    frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope;
                                                    picture-in-picture" allowfullscreen></iframe>
                                        </span>
                                        <span v-if="chat.message_type === messageType.pdf">
                                            <div class="media-wrapper d-flex align-items-center">
                                                <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                                <a :href="chat.message" target="blank"
                                                   class="item">{{chat.file_name}}</a>
                                            </div>
                                        </span>
                                        <span
                                            v-if="chat.message_type === messageType.doc">
                                            <div class="media-wrapper d-flex align-items-center">
                                                <i class="fa fa-file-word-o" aria-hidden="true"></i>
                                                <a :href="chat.message" target="_blank">{{chat.file_name}}</a>
                                            </div>
                                        </span>
                                        <span
                                            v-if="chat.message_type === messageType.xls">
                                            <div class="media-wrapper d-flex align-items-center">
                                                <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                                                <a :href="chat.message" target="_blank">{{chat.file_name}}</a>
                                            </div>
                                        </span>
                                        <span
                                            v-if="chat.message_type === messageType.text_file">
                                            <div class="media-wrapper d-flex align-items-center">
                                                <i class="fa fa fa-file-text-o" aria-hidden="true"></i>
                                                <a :href="chat.message" target="_blank">{{chat.file_name}}</a>
                                            </div>
                                        </span>
                                    </div>
                                    <div class="chat-container__time text-nowrap">
                                        {{convertTime(chat.created_at)}}
                                    </div>
                                    <div
                                        class="chat-container__read-status position-absolute"
                                        :class="chat.status?'chat-container__read-status--read':''">
                                        <i class="fa fa-check" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else class="chat__not-selected">
                    <div class="text-center"><i class="fa fa-envelope-o" aria-hidden="true"></i></div>
                    {{$t('chat.noMsgYet')}}
                </div>
            </div>
            <!--send msg-->
            <div class="chat__area-text">
                <div class="w-100 flex-1 chat__area-form">
                    <input type="text" :placeholder="$t('chat.typeMsg')" v-model="chatText" @keyup="onStartedTyping"
                           @keydown.stop="sendMessageOnEnterKey" ref="chatInputText" class="chat-input-text"
                           @blur="storeMessageToLocal">
                    <picker set="emojione"
                            v-show="showEmojiPicker"
                            @select="addEmoji"/>
                </div>
                <div class="flex-1 d-flex chat__area-btn-group">
                    <div class="chat__area-media-btn mr-2" @click.stop="openMediaMessageModal()">
                        <i class="cui-paperclip"></i>
                    </div>
                    <div class="emoji-trigger" @click="showEmojiPicker=!showEmojiPicker"
                         v-click-outside="closeEmojiPopover">
                        <i class="fa fa-smile-o"></i>
                    </div>
                    <div class="chat__area-send-btn" :class="!chatText?'chat__area-send-btn--disable': ''"
                         @click.prevent="onSendTextMessage">
                        <i class="fa fa-paper-plane-o"></i>
                    </div>
                </div>
            </div>
            <!--// send msg-->
        </fragment>
        <b-modal centered size="lg" :title="$t('chat.uploadFiles')" v-model="mediaMessageModal"
                 hide-footer default="false">
            <FileUploadDropzone v-if="mediaMessageModal" @fileUploaded="onFileUploaded"
                                @onClose="mediaMessageModal=false"/>
        </b-modal>
    </div>
</template>

<script>
    import { mapActions, mapGetters } from 'vuex'
    import * as types from '../../store/types'
    import { Fragment } from 'vue-fragment'
    import FileUploadDropzone from '../../components/FileUploadDropzone'
    import { MESSAGE_TYPE } from '../../constants/app-constants'
    import _ from 'lodash'
    import { LightGallery } from 'vue-light-gallery'
    import VideoPlayer from '../../components/VideoPlayer'
    import { laravelEcho } from '../../utils/laravel-echo'
    import { prepareUserLastSeen } from '../../utils/prepare-user-last-seen'
    import { clickOutside } from '../../directives/ClickOutside'
    import InfyLoader from '../../components/InfyLoader'
    import { Picker } from 'emoji-mart-vue'

    const KEY_DATE_FORMAT = 'dddd, MMM Do'
    const DATE_TIME_FORMAT = 'YYYY-M-D HH:mm:ss'
    export default {
        name: 'ChatConversation',
        data: () => {
            return {
                chatText: '',
                conversations: [],
                echo: null,
                isTyping: false,
                conversationSkip: null,
                conversationLimit: 20,
                isLoading: true,
                mediaMessageModal: false,
                messageType: MESSAGE_TYPE,
                isLoadBeforeData: false,
                isLoadAfterData: false,
                firstUnreadMessageId: null,
                imageMessages: [],
                index: null,
                isShowProfileDropdwon: false,
                conversationsGroupByDate: [],
                showEmojiPicker: false,
            }
        },
        components: {
            Fragment,
            FileUploadDropzone,
            LightGallery,
            VideoPlayer,
            InfyLoader,
            Picker,
        },
        directives: {
            clickOutside,
        },
        computed: {
            ...mapGetters({
                selectedUserForChat: types.SELECTED_USER_FOR_CHAT,
                loggedUser: types.LOGGED_USER,
                conversationUsers: types.CONVERSATION_USERS,
            }),
        },
        watch: {
            selectedUserForChat (chatUser) {
                this.isLoadBeforeData = false
                this.isLoadAfterData = false
                this.conversations = []
                const index = this.conversationUsers.findIndex(u => +u.user.id === +chatUser.id)
                if (index >= 0) {
                    this.$store.state.conversationUsers[index].unread_count = 0
                }
                this.onGetUserConversations(chatUser)
                this.getDraftMessageAndAssignToInput()
                return chatUser
            },
            loggedUser (user) {
                this.onListenForMessageReceive(user)
                this.onListenForTyping(user)
                this.onListenForMessageRead(user)
                return user
            },
        },
        methods: {
            ...mapActions({
                toggleChatUserProfile: types.TOGGLE_CHAT_PROFILE,
                sendMessage: types.SEND_MESSAGE,
                getUserConversations: types.GET_USER_CONVERSATIONS,
                onMarkMessageStatusRead: types.MARK_MESSAGE_STATUS_READ,
            }),
            addEmoji (emoji) {
                this.chatText = `${this.chatText} ${emoji.native}`
                this.showEmojiPicker = false
                this.$refs.chatInputText.focus()
            },
            closeEmojiPopover () {
                this.showEmojiPicker = false
            },
            openMediaInFullSize (imageMsg) {
                this.index = imageMsg.imageIndex
            },
            onToggleChatUserProfile () {
                this.toggleChatUserProfile()
            },
            sendMessageOnEnterKey (event) {
                if (event.key === 'Enter' || event.which === 13) {
                    this.onSendTextMessage()
                    return false
                }
            },
            onSendTextMessage: _.debounce(function () {
                this.onSendMessage()
            }, 300),
            onSendMessage (req = null) {
                if (!req) {
                    req = {
                        to_id: this.selectedUserForChat.id,
                        message: this.chatText,
                        message_type: this.messageType.text,
                    }
                }

                if (this.chatText && this.chatText.indexOf('www.youtube.com/watch?v') >= 0) {
                    req.message_type = this.messageType.youtubeUrl
                }

                if (req.message_type === this.messageType.text && !this.chatText) {
                    return
                }

                this.sendMessage(req).then((message) => {
                    // update last message on send message
                    this.updateConversationUser(message.message, 'receiver')
                    if (this.conversationsGroupByDate.length > 0) {
                        this.conversationsGroupByDate[this.conversationsGroupByDate.length - 1].conversations.push(
                            message.message)
                    } else {
                        this.conversationsGroupByDate.push({ conversations: [message.message] })
                    }
                    this.conversationsGroupByDate = this.prepareMediaMessages(this.conversationsGroupByDate)
                    this.chatText = ''
                    this.removeMessageToLocal()
                    setTimeout(() => {
                        let ele = document.getElementById('chatConversation')
                        ele.scrollTop = ele.scrollHeight
                    }, 100)
                })
            },
            fireMessageStatusEvent (unReadMsgIds) {
                this.echo.private(`message-status`).
                    whisper(`message-read.${this.selectedUserForChat.id}`, unReadMsgIds)
            },
            async onListenForMessageRead (loggedUser) {
                this.echo.private('message-status').
                    listenForWhisper(`message-read.${loggedUser.id}`, (readMsgIds) => {
                        setTimeout(() => {
                            this.conversationsGroupByDate.forEach((cg) => {
                                cg.conversations.forEach((c, index) => {
                                    if (readMsgIds.indexOf(c.id) >= 0) {
                                        cg.conversations[index].status = 1
                                    }
                                })
                            })
                            this.conversationsGroupByDate = this.conversationsGroupByDate.slice()
                        }, 1000)
                    })
            },
            onGetUserConversations (user, direction = '') {
                this.isLoading = true
                this.firstUnreadMessageId = null
                this.conversationSkip = Math.min.apply(Math, this.conversations.map((m) => { return m.id }))
                const req = {
                    userId: user.id,
                    before: '',
                    after: '',
                    limit: this.conversationLimit,
                }

                if (direction === 'before') {
                    req.before = Math.min.apply(Math, this.conversations.map((m) => { return m.id }))
                } else if (direction === 'after') {
                    req.after = Math.max.apply(Math, this.conversations.map((m) => { return m.id }))
                }

                this.getUserConversations(req).then((response) => {
                    this.isLoading = false
                    // should allow to load more conversations
                    if (response.conversations.length === this.conversationLimit) {
                        this.isLoadBeforeData = true
                        this.isLoadAfterData = true
                    }
                    // process on conversations
                    if (response.conversations) {
                        this.conversations.push(...response.conversations)
                        this.conversations = _.sortBy(this.conversations, (c) => { return c.id })
                        this.conversationsGroupByDate = this.prepareConversationsGroupByDate(this.conversations)
                        this.conversationsGroupByDate = this.prepareMediaMessages(this.conversationsGroupByDate)
                        setTimeout(() => {
                            const ele = document.getElementById('chatConversation')

                            const lastMessageEle = document.getElementById(
                                `message_${+Math.min.apply(Math, this.conversations.map((m) => { return m.id }))}`)

                            const unReadMsgIds = this.prepareUnreadMessageIds(this.conversationsGroupByDate)

                            if (direction === 'before') {
                                if (response.conversations.length === 0) {
                                    this.isLoadBeforeData = false
                                    ele.scrollTop = 0
                                } else {
                                    ele.scrollTop = lastMessageEle.scrollHeight - 10
                                }
                            } else if (direction === 'after') {
                                if (response.conversations.length === 0) {
                                    this.isLoadAfterData = false
                                } else {
                                    ele.scrollTop = ele.scrollTop - 50
                                }
                                if (unReadMsgIds.length > 0) {
                                    // make message status read
                                    this.makeReadMsgsAndUpdateUnreadMsgsCount(unReadMsgIds)
                                    this.fireMessageStatusEvent(unReadMsgIds)
                                }
                            } else {
                                if (unReadMsgIds.length > 0) {
                                    this.firstUnreadMessageId = unReadMsgIds[0]
                                    const firstUnreadEle = document.getElementById(
                                        `message_${this.firstUnreadMessageId}`)
                                    let ele = document.getElementById('chatConversation')
                                    ele.scrollTop = firstUnreadEle.offsetTop - 100
                                    // make message status read
                                    this.makeReadMsgsAndUpdateUnreadMsgsCount(unReadMsgIds)
                                    this.fireMessageStatusEvent(unReadMsgIds)
                                } else {
                                    ele.scrollTop = ele.scrollHeight
                                    this.isLoadAfterData = false
                                }
                            }
                        })
                    }
                    this.$refs.chatInputText.focus()
                }).catch(() => {
                    this.isLoading = false
                })
            },
            makeReadMsgsAndUpdateUnreadMsgsCount (unReadMsgIds) {
                this.onMarkMessageStatusRead(unReadMsgIds).then((response) => {
                    const index = this.conversationUsers.findIndex(
                        u => +u.user.id === +this.selectedUserForChat.id)
                    if (index >= 0) {
                        this.$store.state.conversationUsers[index].unread_count = +response.remainingUnread
                    }
                })
            },
            prepareConversationsGroupByDate (conversations) {
                const conversationArr = []
                conversations.forEach(c => {
                    c.day = this.$moment(c.created_at, DATE_TIME_FORMAT).format('YYYY-M-D h:mma')
                    c.key = this.getFormattedDayName(c.created_at)
                    conversationArr.push(c)
                })
                return this.getSortedConversationsByGroups(conversationArr)
            },
            getFormattedDayName (day) {
                return (this.$moment(day, DATE_TIME_FORMAT).utc(day).local().calendar(null, {
                        lastDay: '[Yesterday]',
                        sameDay: '[Today]',
                        nextDay: KEY_DATE_FORMAT,
                        lastWeek: KEY_DATE_FORMAT,
                        nextWeek: KEY_DATE_FORMAT,
                        sameElse: () => {
                            if (this.$moment().year() === this.$moment(day).year()) {
                                return KEY_DATE_FORMAT
                            } else {
                                return 'dddd, MMM Do YYYY'
                            }
                        },
                    })
                )
            },
            getSortedConversationsByGroups (conversationsWithKey) {
                // group by key
                const conversationsByGroup = _.groupBy(conversationsWithKey, 'key')
                const conversationsGroup = []
                for (const key in conversationsByGroup) {
                    if (key) { // this is needs to check for remove null key
                        conversationsGroup.push({ key: key, conversations: conversationsByGroup[key] })
                    }
                }
                return conversationsGroup
            },
            onScrollMessages: _.debounce(function (event) {
                if (this.selectedUserForChat) {
                    const ele = event.target
                    if (ele.scrollTop === 0 && this.isLoadBeforeData) {
                        this.onGetUserConversations(this.selectedUserForChat, 'before')
                    }
                    const elemScrollPosition = ele.scrollHeight - ele.scrollTop - ele.clientHeight
                    if (elemScrollPosition === 0) {
                        if (this.isLoadAfterData) {
                            this.onGetUserConversations(this.selectedUserForChat, 'after')
                        } else {
                            setTimeout(() => {
                                const unReadMsgIds = this.prepareUnreadMessageIds(this.conversationsGroupByDate)
                                if (unReadMsgIds.length > 0) {
                                    // make message status read
                                    this.makeReadMsgsAndUpdateUnreadMsgsCount(unReadMsgIds)
                                    this.fireMessageStatusEvent(unReadMsgIds)
                                }
                            }, 1000)
                        }
                    }
                }
            }, 500),
            onListenForMessageReceive (user) {
                this.echo.private(`conversation.${user.id}`).listen('ConversationEvent', (conversation) => {
                    // update last message and user image(if it is changed) in conversations
                    this.updateConversationUser(conversation)
                    // update last message and user image(if it is changed) in user list
                    this.updateUserList(conversation)
                    const index = this.conversationUsers.findIndex((cu) => +cu.user.id === +conversation.sender.id)
                    if (index === -1) {
                        const msgObj = Object.assign({}, conversation)
                        msgObj.user = conversation.sender
                        msgObj.unread_count = 0
                        const conversationUsers = []
                        conversationUsers.push(msgObj)
                        conversationUsers.push(...this.$store.state.conversationUsers)
                        this.$store.state.conversationUsers = conversationUsers
                    }
                    if (!this.selectedUserForChat) {
                        const index = this.conversationUsers.findIndex(u => +u.user.id === +conversation.sender.id)
                        if (index >= 0) {
                            this.$store.state.conversationUsers[index].unread_count =
                                +this.$store.state.conversationUsers[index].unread_count + 1
                        }
                    } else {
                        if (+this.selectedUserForChat.id !== +conversation.sender.id) {
                            const index = this.conversationUsers.findIndex(u => +u.user.id === +conversation.sender.id)
                            if (index >= 0) {
                                this.$store.state.conversationUsers[index].unread_count =
                                    this.$store.state.conversationUsers[index].unread_count + 1
                            }
                        } else {
                            this.isTyping = false
                            this.conversations.push(conversation)
                            this.conversationsGroupByDate[this.conversationsGroupByDate.length - 1].conversations.push(
                                conversation)
                            this.conversationsGroupByDate = this.conversationsGroupByDate.slice()
                            this.conversationsGroupByDate = this.prepareMediaMessages(this.conversationsGroupByDate)
                            // for new messages label
                            const unReadMsgIds = this.prepareUnreadMessageIds(this.conversationsGroupByDate)
                            if (unReadMsgIds.length > 0) {
                                this.firstUnreadMessageId = unReadMsgIds[0]
                            }
                            let ele = document.getElementById('chatConversation')
                            const elemScrollPosition = ele.scrollHeight - ele.scrollTop - ele.clientHeight
                            if (elemScrollPosition < 200) {
                                this.onScrollToBottom()
                                let unReadMsgIds = this.prepareUnreadMessageIds(this.conversationsGroupByDate)
                                this.onMarkMessageStatusRead(unReadMsgIds)
                                this.fireMessageStatusEvent(unReadMsgIds)
                            }
                        }
                        setTimeout(() => {
                            const index = this.conversationUsers.findIndex(
                                u => +u.user.id === +this.selectedUserForChat.id)
                            if (index >= 0) {
                                let ele = document.getElementById('chatConversation')
                                const elemScrollPosition = ele.scrollHeight - ele.scrollTop - ele.clientHeight
                                if (elemScrollPosition > 200) {
                                    this.$store.state.conversationUsers[index].unread_count =
                                        +this.$store.state.conversationUsers[index].unread_count + 1
                                }
                            }
                        }, 500)
                    }
                })
            },
            updateConversationUser (conversation, updateBy = 'sender') {
                const index = this.conversationUsers.findIndex(cu => +cu.user.id === +conversation[updateBy].id)
                if (index >= 0) {
                    this.conversationUsers[index].message = conversation.message
                    this.conversationUsers[index].message_type = conversation.message_type
                    this.conversationUsers[index].created_at = conversation.created_at
                    this.conversationUsers[index].user.photo_url = conversation[updateBy].photo_url
                    this.$store.state.conversationUsers = this.sortConversationsByDate(this.conversationUsers)
                }
            },
            updateUserList (conversation) {
                const users = this.$store.state.users
                const index = users.findIndex(u => u.id === +conversation.sender.id)
                if (index >= 0) {
                    users[index] = Object.assign(users[index], conversation.sender)
                    this.$store.state.users = users
                }
            },
            sortConversationsByDate (conversationUsers) {
                conversationUsers.sort((a, b) => {
                    const keyA = this.$moment(a.created_at, DATE_TIME_FORMAT)
                    const keyB = this.$moment(b.created_at, DATE_TIME_FORMAT)
                    if (keyA <= keyB) return 1
                    if (keyA > keyB) return -1
                    return 0
                })
                return conversationUsers
            },
            prepareUnreadMessageIds (conversationsGroupByDate) {
                let unReadMsgIds = []
                conversationsGroupByDate.forEach((cg) => {
                    cg.conversations.forEach((c) => {
                        if (!c.status && +c.receiver.id === +this.loggedUser.id) {
                            unReadMsgIds.push(c.id)
                        }
                    })
                })
                return unReadMsgIds
            },
            prepareMediaMessages (conversationsGroupByDate) {
                this.imageMessages = []
                let index = 0
                conversationsGroupByDate.forEach((cg) => {
                    cg.conversations.forEach((c) => {
                        if (c.message_type === this.messageType.image) {
                            c.imageIndex = index
                            this.imageMessages.push(c.message)
                            index++
                        } else if (c.message_type === this.messageType.video) {
                            c.videoOptions = {
                                muted: false,
                                language: 'en',
                                playbackRates: [0.7, 1.0, 1.5, 2.0],
                                autoplay: false,
                                controls: true,
                                sources:
                                    {
                                        src: c.message,
                                        type: 'video/mp4',
                                    },
                            }
                        }
                    })
                })
                return conversationsGroupByDate
            },
            onScrollToBottom () {
                setTimeout(() => {
                    let ele = document.getElementById('chatConversation')
                    ele.scrollTop = ele.scrollHeight
                }, 100)
            },
            onStartedTyping () {
                let channel = this.echo.private('chat')
                channel.whisper(`typing.${this.selectedUserForChat.id}`, {
                    user: this.loggedUser,
                    typing: true,
                })
            },
            onListenForTyping (loggedUser) {
                this.echo.private('chat').
                    listenForWhisper(`typing.${loggedUser.id}`, (e) => {
                        if (this.selectedUserForChat && this.selectedUserForChat.id === e.user.id) {
                            this.isTyping = true
                            setTimeout(() => {
                                this.isTyping = false
                            }, 4000)
                        }
                    })
            },
            openMediaMessageModal () {
                this.mediaMessageModal = true
            },
            onFileUploaded (fileObj) {

                this.mediaMessageModal = false
                let req = {
                    to_id: this.selectedUserForChat.id,
                    message: fileObj.attachment,
                    message_type: fileObj.message_type,
                    file_name: fileObj.file_name,
                }
                this.onSendMessage(req)
            },
            getYoutubeEmbedURL (url) {
                let newUrl = url
                let regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/
                let match = url.match(regExp)
                if (match && match[2].length === 11) {
                    newUrl = 'https://www.youtube.com/embed/' + match[2] + ''
                }
                return newUrl
            },
            linkifyTextMessage (text) {
                const urlRegex = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig
                return text.replace(urlRegex, (url) => {
                    return '<a href="' + url + '" target="_blank">' + url + '</a>'
                })
            },
            convertTime (dateTime, format = 'h:mma') {
                if (dateTime) {
                    return this.$moment.utc(dateTime).local().format(format)
                }
            },
            prepareLastSeenDate (dateTime) {
                return prepareUserLastSeen(dateTime)
            },
            storeMessageToLocal () {
                if (this.chatText) {
                    sessionStorage.setItem(`userDraftMessage_${this.selectedUserForChat.id}`, this.chatText)
                }
            },
            removeMessageToLocal () {
                sessionStorage.removeItem(`userDraftMessage_${this.selectedUserForChat.id}`)
            },
            getDraftMessageAndAssignToInput () {
                this.chatText = JSON.parse(sessionStorage.getItem(`userDraftMessage_${this.selectedUserForChat.id}`))
            },
        },
        mounted () {
            this.echo = laravelEcho()
            if (this.loggedUser) {
                this.onListenForMessageReceive(this.loggedUser)
                this.onListenForTyping(this.loggedUser)
                this.onListenForMessageRead(this.loggedUser)
                if (this.isLoading && this.selectedUserForChat) {
                    this.onGetUserConversations(this.selectedUserForChat)
                }
            }
            // disable drop event on chat conversation container
            window.addEventListener('dragover', (e) => {
                e.preventDefault()
            }, false)
            window.addEventListener('drop', (e) => {
                e.preventDefault()
            }, false)
        },
    }
</script>

<style lang="scss">
    .emoji-mart {
        position: absolute;
        right: 0;
        bottom: 70px;
    }

    .emoji-trigger {
        position: absolute;
        right: 140px;
        padding-top: 5px;
        cursor: pointer;
        font-size: 20px;
    }

    .word-break {
        word-break: break-word;
    }

    .chat-input-text {
        padding-right: 50px;
    }

    .chat-conversation {
        audio {
            outline: none;
        }

        video {
            border-radius: 8px;
        }
    }
</style>
