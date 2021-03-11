<template>
    <div>
        <app-header/>
        <div class="app-body">
            <app-sidebar/>
            <router-view></router-view>
        </div>
        <app-footer/>
    </div>
</template>

<script>
    import AppHeader from '../components/AppHeader'
    import AppFooter from '../components/AppFooter'
    import AppSidebar from '../components/AppSidebar'
    import * as types from '../store/types'
    import { mapActions } from 'vuex'
    import { laravelEcho } from '../utils/laravel-echo'

    export default {
        name: 'DefaultLayout',
        components: {
            AppHeader,
            AppFooter,
            AppSidebar,
        },
        methods: {
            ...mapActions({
                getLoggedUser: types.GET_LOGGED_USER_PROFILE,
                updateUserLastSeenStatus: types.UPDATE_USER_LAST_SEEN_STATUS,
            }),
            async sendData (event) {
                event.preventDefault()
                if (!this.$store.state.isLogout) {
                    try {
                        // API call here
                        this.echo.leave('user-status')
                        await this.updateUserLastSeenStatus(false)
                        return '' // Prompt with default browser message
                    } catch (err) {
                        return undefined // Skip prompt
                    }
                } else {
                    return ''
                }

            },
        },
        created () {
            this.getLoggedUser().then(() => {
                this.updateUserLastSeenStatus(true)
            })
            window.addEventListener('beforeunload', this.sendData)
        },
        mounted () {
            this.echo = laravelEcho()
        },
    }
</script>

<style scoped>

</style>
