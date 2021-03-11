<template>
  <footer class="app-footer">
    <div>
      <a href="http://www.infyom.com/" target="_blank">{{$t('footer.infyom')}}</a>
      <span>{{$t('footer.copyYear')}}</span>
    </div>
    <div class="ml-auto">
      <span>{{$t('footer.powerBy')}}</span>
      <a href="https://coreui.io">{{$t('footer.coreUI')}}</a>
    </div>
  </footer>
</template>

<script>
  export default {
    name: 'AppFooter',
  }
</script>

<style scoped>

</style>
