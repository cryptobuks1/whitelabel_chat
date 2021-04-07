<template>
  <Header fixed>
    <SidebarToggler class="d-lg-none" display="md" mobile />
    <b-link class="navbar-brand" :to="'/'">
      <img
        v-if="loggedUser"
        class="navbar-brand-full"
        :src="loggedUser.logo_path"
        style="max-height: 95%;"
        alt="Logo"
      />
      <img
        v-else
        class="navbar-brand-full"
        src="https://creditor.smashmybill.com/storage/logo/logo.png"
        style="max-height: 95%;"
        alt="Logo"
      />
    </b-link>
    <!-- <SidebarToggler class="d-md-down-none" display="lg"/> -->
    <b-navbar-nav class="ml-auto">
         <li>
          <a href="https://consumer.smashmybill.com" class="nobd mr-2">
            <img src="https://consumer.smashmybill.com/assets/img/consumerlink.png" width="150" height="40" />
          </a>
        </li>

        <li>
          <a href="https://creditor.smashmybill.com" class="nobd mr-4">
            <img src="https://consumer.smashmybill.com/assets/img/creditorlink.png" width="150" height="40"/>
          </a>
        </li>
      <AppHeaderDropdown right no-caret>
        <template slot="header">
          <a
            class="nav-link"
            data-toggle="dropdown"
            href="#"
            role="button"
            aria-haspopup="true"
            aria-expanded="false"
          >
            <span v-if="loggedUser">{{ loggedUser.name }}</span>
            <img
              v-if="loggedUser"
              class="img-avatar"
              :src="loggedUser.photo_url"
              :alt="loggedUser.name"
            />
          </a>
        </template>
        <template slot="dropdown">
          <div class="dropdown-header text-center">
            <strong>{{ $t('header.account') }}</strong>
          </div>
          <!-- <b-dropdown-item @click="goToUserProfile()">
            <i class="fa fa-user" />
            {{ $t('header.profile') }}
          </b-dropdown-item> -->
          <b-dropdown-item @click="onLogout">
            <i class="fa fa-lock" />
            {{ $t('header.logout') }}
          </b-dropdown-item>
        </template>
      </AppHeaderDropdown>
    </b-navbar-nav>
  </Header>
</template>

<script>
import {
  Header,
  HeaderDropdown as AppHeaderDropdown,
  SidebarToggler,
} from '@coreui/vue'
import * as types from './../store/types'
import { mapActions, mapGetters } from 'vuex'

export default {
  name: 'AppHeader',
  data: () => {
    return { itemsCount: 42 }
  },
  components: {
    SidebarToggler,
    Header,
    AppHeaderDropdown,
  },
  computed: {
    ...mapGetters({
      loggedUser: types.LOGGED_USER,
    }),
  },
  methods: {
    ...mapActions({
      logout: types.APP_LOGOUT,
      updateUserLastSeenStatus: types.UPDATE_USER_LAST_SEEN_STATUS,
    }),
    goToUserProfile() {
      this.$router.push('/profile')
    },
    onLogout() {
      this.$store.state.isLogout = true
      this.updateUserLastSeenStatus().then(() => {
        this.logout().then(() => {
          this.$router.go('/login')
          localStorage.removeItem('pusherTransportTLS')
        })
      })
    },
  },
}
</script>

<style scoped lang="scss">
.app-header .navbar-toggler {
  outline: none;
}

.header-fixed .app-header {
    padding-right: 20px;
    padding-left: 55px;
}

@media (min-width: 992px) {
  .brand-minimized .app-header .navbar-brand .navbar-brand-full {
    display: inline-block;
  }
}
</style>
