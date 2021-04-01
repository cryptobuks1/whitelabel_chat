<template>
  <footer class="app-footer row" style="margin: 0; border: 1px solid blue;">
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
        src="http://yn/storage/logo/logo.png"
        style="height: 30px;"
        alt="Logo"
      />
    </div>
    <div class="col-md-6 text-right">
      <p class="m-0">© 2021 Sunny Test Comp | All rights reserved</p>
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
