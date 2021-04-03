<template>
  <footer class="app-footer row" style="margin: 0;">
    <div class="col-md-6">
      <img
        v-if="loggedUser"
        class="navbar-brand-full"
        :src="loggedUser.logo_path"
        style="height: 30px;"
        alt="Logo"
      />
      <img
        v-else
        class="navbar-brand-full"
        src="https://creditor.smashmybill.com/storage/logo/logo.png"
        style="height: 30px;"
        alt="Logo"
      />
    </div>
    <div class="col-md-6 text-right">
      <p class="m-0">© 2021 {{ loggedUser.company_name }} | All rights reserved</p>
    </div>
  </footer>
</template>

<script>
import * as types from '../store/types'
import { mapActions, mapGetters } from 'vuex'

export default {
  name: 'AppFooter',

  computed: {
    ...mapGetters({
      loggedUser: types.LOGGED_USER,
    }),
  },
}
</script>

<style scoped></style>
