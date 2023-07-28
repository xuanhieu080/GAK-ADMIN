<script setup>
import Footer from '@/views/layouts/Footer.vue';
import Header from '@/views/layouts/Header.vue';
import Sidebar from '@/views/layouts/Sidebar.vue';

import {useStore} from '@/stores/sidebar';
import {useGlobalStateStore} from '@/stores/global';
import {storeToRefs} from 'pinia';

const state = useStore();
const {isToggleDesktop, isSideMenuOpen} = storeToRefs(state);
import {useAuthStore} from "@/stores/auth";
import Alert from "@/views/components/Alert.vue";
import Notify from "@/views/components/Notify.vue";
import {onMounted} from "vue";
import {useAlertStore} from "@/stores";
const authStore = useAuthStore();
const globalStore = useGlobalStateStore();

onMounted(() => {
    const element = document.querySelector(`.main-container`);

    element.onscroll = () => {
        globalStore.scroll = element.scrollTop;
    };
})

</script>

<template>
    <div>
      <main
          class="h-screen bg-gray-200 dark:bg-gray-900"
          :class="{ 'overflow-hidden': isSideMenuOpen }"
      >
        <Sidebar v-if="authStore.user.email"/>
        <div class="flex flex-col flex-1 w-full h-full relative" :class="{'main-full': isToggleDesktop, 'main': authStore.user.email}">
          <Header v-if="authStore.user.email" class="sticky top-0 z-40 bg-white"/>
          <!--                <nav class="shadow flex px-5 py-3 text-gray-700 border border-gray-200 bg-gray-50 dark:bg-gray-800 dark:border-gray-700" aria-label="Breadcrumb">-->
          <!--                    <ol class="inline-flex items-center space-x-1 md:space-x-3">-->
          <!--                        <li class="inline-flex items-center">-->
          <!--                            <a href="#" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">-->
          <!--                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>-->
          <!--                                Home-->
          <!--                            </a>-->
          <!--                        </li>-->
          <!--                        <li>-->
          <!--                            <div class="flex items-center">-->
          <!--                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>-->
          <!--                                <a href="#" class="ml-1 text-sm font-medium text-gray-700 hover:text-gray-900 md:ml-2 dark:text-gray-400 dark:hover:text-white">Templates</a>-->
          <!--                            </div>-->
          <!--                        </li>-->
          <!--                        <li aria-current="page">-->
          <!--                            <div class="flex items-center">-->
          <!--                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>-->
          <!--                                <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">Flowbite</span>-->
          <!--                            </div>-->
          <!--                        </li>-->
          <!--                    </ol>-->
          <!--                </nav>-->

            <Notify/>
          <div class="overflow-y-hidden h-full">
              <div class="main-container overflow-y-scroll h-full px-2 md:px-8">
                  <router-view></router-view>
              </div>
          </div>
          <Footer  v-if="authStore.user.email"/>
        </div>

      </main>
      <slot name="modal"></slot>
    </div>
</template>

<style lang="scss" scoped>
@media screen and (min-width: 768px) {
.main {padding-left: 16rem}
.main.main-full {padding-left: 75px}
}
</style>
