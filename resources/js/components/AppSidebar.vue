<template>
    <div class="sidebar">
        <nav class="sidebar-nav">
            <ul class="nav">
                <li class="nav-item">
                    <b-link class="nav-link" to="/conversations">
                        <i class="icon-speech mr-3 align-middle"></i>{{$t('sidebar.conversations')}}
                    </b-link>
                    <div v-if="loggedUser && loggedUser.role_name === 'Admin'">
                        <b-link class="nav-link" to="/users">
                            <i class="fa fa-users mr-4 align-middle"></i>{{$t('sidebar.users')}}
                        </b-link>
                        <b-link class="nav-link" to="/roles">
                            <i class="fa fa-user mr-4 align-middle"></i>{{$t('sidebar.roles')}}
                        </b-link>
                    </div>
                </li>
            </ul>
        </nav>
        <SidebarMinimizer/>
    </div>
</template>

<script>
    import { SidebarMinimizer } from '@coreui/vue'
    import * as types from '../store/types'
    import { mapGetters } from 'vuex'

    export default {
        name: 'AppSidebar',
        computed: {
            ...mapGetters({
                loggedUser: types.LOGGED_USER,
            }),
        },
        components: {
            SidebarMinimizer,
        },
    }
</script>

<style scoped>

</style>
