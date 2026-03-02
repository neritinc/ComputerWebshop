<template>
  <div class="container-fluid my-container app-shell mt-2">
    <!-- Head -->
    <Header/>

    <!-- Menü -->
    <Menu class="my-border"/>
    <Breadcrumb class="my-border"/>

    <!-- Ide töltődnek be az oldalak -->
    <main class="app-main">
      <RouterView />
    </main>


    <Footer
      :content="footerContent"
    />
    <ToastContanier/>
  </div>
</template>

<script>
import Menu from './components/Layout/Menu.vue';
import Header from './components/Layout/Header.vue';
import Footer from './components/Layout/Footer.vue';
import Breadcrumb from './components/Layout/Breadcrumb.vue';
import ToastContanier from './components/Message/ToastContanier.vue';
export default {
  components:{
    Menu,
    Header,
    Footer,
    Breadcrumb,
    ToastContanier,
  },
  data() {
    return {
      footerContent: "Ez egy másik lábléc szöveg",
    }
  }
};
</script>

<style>

</style>
