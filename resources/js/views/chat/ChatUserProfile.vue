<template>
    <div class="chat-profile chat-profile--active" v-click-outside="clickOutsideOfChatUserProfileDrawer">
        <div class="chat-profile__header">
            <span class="chat-profile__about">{{selectedUserForChat.name}}</span>
            <i class="fa fa-times chat-profile__close-btn cursor-pointer" @click="onToggleChatUserProfile()"></i>
        </div>
        <div class="chat-profile__person chat-profile__person--active">
            <div class="chat-profile__avatar">
                <img :src="selectedUserForChat.photo_url" alt="" class="img-fluid">
            </div>
        </div>
        <div class="chat-profile__person-last-seen mt-2 position-relative">
            <span class="chat__area-header-status"
                  :class="selectedUserForChat.is_online ?
                          'chat__area-header-status--online':'chat__area-header-status--offline'"></span>
            <span class="pl-3" v-if="selectedUserForChat.is_online">{{$t('chat.online')}}</span>
            <span class="pl-3"
                  v-if="!selectedUserForChat.is_online && selectedUserForChat.last_seen">
                  {{$t('chat.lastSeen')}} <i>{{prepareLastSeenDate(selectedUserForChat.last_seen)}}</i>
            </span>
            <span class="pl-3"
                  v-if="!selectedUserForChat.is_online && selectedUserForChat.last_seen === null">{{$t('chat.never')}}
            </span>
        </div>
        <div class="chat-profile__divider"></div>
        <div class="chat-profile__column">
            <h6 class="chat-profile__column-title">{{$t('chat.bio')}}</h6>
            <p class="chat-profile__column-title-detail text-muted mb-0">
                {{selectedUserForChat.about?selectedUserForChat.about:$t('chat.noBioUpdated')}}
            </p>
        </div>
        <div class="chat-profile__divider"></div>
        <div class="chat-profile__column">
            <h6 class="chat-profile__column-title">{{$t('chat.phone')}}</h6>
            <p class="chat-profile__column-title-detail text-muted mb-0">
                {{selectedUserForChat.phone?selectedUserForChat.phone:$t('chat.noPhoneUpdated')}}
            </p>
        </div>
        <div class="chat-profile__divider"></div>
        <div class="chat-profile__column">
            <h6 class="chat-profile__column-title">{{$t('chat.email')}}</h6>
            <p class="chat-profile__column-title-detail text-muted mb-0">
                {{selectedUserForChat.email}}
            </p>
        </div>
    </div>
</template>

<script>
    import { mapActions, mapGetters } from 'vuex'
    import * as types from '../../store/types'
    import { clickOutside } from '../../directives/ClickOutside'
    import { prepareUserLastSeen } from '../../utils/prepare-user-last-seen'

    export default {
        name: 'ChatUserProfile',
        data: () => {
            return {
                shouldHideChatProfileDrawer: false,
            }
        },
        directives: {
            clickOutside,
        },
        computed: {
            ...mapGetters({
                selectedUserForChat: types.SELECTED_USER_FOR_CHAT,
            }),
        },
        methods: {
            ...mapActions({
                toggleChatUserProfile: types.TOGGLE_CHAT_PROFILE,
            }),
            onToggleChatUserProfile () {
                this.toggleChatUserProfile()
            },
            prepareLastSeenDate (dateTime) {
                return prepareUserLastSeen(dateTime)
            },
            handleResize () {
                this.shouldHideChatProfileDrawer = window.innerWidth < 1200
            },
            clickOutsideOfChatUserProfileDrawer () {
                if (this.shouldHideChatProfileDrawer) {
                    this.toggleChatUserProfile()
                }
            },
        },
        created () {
            window.addEventListener('resize', this.handleResize)
            this.handleResize()
        },
        destroyed () {
            window.removeEventListener('resize', this.handleResize)
        },
    }
</script>

<style scoped>

</style>
