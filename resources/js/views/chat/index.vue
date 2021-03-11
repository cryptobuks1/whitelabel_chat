<template>
    <main class="main">
        <div class="page-container">
            <div class="chat-container chat">
                <div class="chat__inner">
                    <chat-user-list/>
                    <div class="chat__area-wrapper">
                        <chat-conversation/>
                    </div>
                    <chat-user-profile v-if="isVisibleChatUserProfile"/>
                </div>
            </div>
        </div>
    </main>
</template>

<script>
    import ChatUserList from './ChatUserList'
    import ChatUserProfile from './ChatUserProfile'
    import ChatConversation from './ChatConversation'
    import { mapGetters } from 'vuex'
    import * as types from '../../store/types'

    export default {
        name: 'Home',
        components: {
            ChatUserList,
            ChatUserProfile,
            ChatConversation,
        },
        computed: {
            ...mapGetters({
                isVisibleChatUserProfile: types.USER_CHAT_PROFILE,
            }),
        },
    }
</script>

<style scoped>

</style>
