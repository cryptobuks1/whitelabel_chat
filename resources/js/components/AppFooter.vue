<template>
  <footer class="app-footer row" style="margin-right: 30px;background-color: #fff;border-radius: 5px;margin-left: 30px;">
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
      <p v-if="loggedUser" class="m-0">© 2021 {{ loggedUser.company_name }} | All rights reserved</p>
    </div>
  </footer>
</template>

<script>
import * as types from './../store/types';
import { mapActions, mapGetters } from 'vuex';

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
