<script setup>
import {vOnClickOutside} from '@vueuse/components'
import {useConfig} from '@/stores/config';
import {useStore} from '@/stores/sidebar';
import {useRoute} from 'vue-router';
import {onMounted, ref, watch} from "vue";
import {storeToRefs} from "pinia";

const configState = useConfig();
const {configs} = storeToRefs(configState);

const state = useStore();
const {
  isToggleMenu,
  isToggleDesktop,
} = storeToRefs(state);
const {closeToggleDesktop, closeToggle} = useStore();
const routeStore = useRoute()
const routeName = ref(null)
const isToggleProduct = ref(false)
const isTogglePost = ref(false)
const isTogglePage = ref(false)

function toggleProductMenu() {
  isToggleProduct.value = !isToggleProduct.value;
}

function togglePostMenu() {
  isTogglePost.value = !isTogglePost.value;
}

function togglePageMenu() {
  isTogglePage.value = !isTogglePage.value;
}

function onBackdropMenu(e) {
  if (!e.target.classList.contains('sidebar')) {
    isToggleMenu.value = true;
  }
}

onMounted(() => {
  routeName.value = routeStore.name
  if (routeStore.name === 'categories.list' || routeStore.name === 'categories.create' ||
      routeStore.name === 'categories.edit' || routeStore.name === 'products.list' ||
      routeStore.name === 'products.create' || routeStore.name === 'products.edit' ||
      routeStore.name === 'attribute_group.create' || routeStore.name === 'attribute_group.edit' ||
      routeStore.name === 'attribute_group.list' || routeStore.name === 'attribute.create' ||
      routeStore.name === 'attribute.edit' || routeStore.name === 'attribute.list'
  ) {
    isToggleProduct.value = true
    isTogglePost.value = false
    isTogglePage.value = false
  } else if (routeStore.name === 'posts.list' || routeStore.name === 'posts.create' ||
      routeStore.name === 'posts.edit' || routeStore.name === 'post_groups.list' ||
      routeStore.name === 'post_groups.create' || routeStore.name === 'post_groups.edit'
  ) {
    isToggleProduct.value = false
    isTogglePage.value = false
    isTogglePost.value = true
  } else if (routeStore.name === 'pages.list' || routeStore.name === 'pages.create' ||
      routeStore.name === 'pages.edit' || routeStore.name === 'page_groups.list' ||
      routeStore.name === 'page_groups.create' || routeStore.name === 'page_groups.edit'
  ) {
    isToggleProduct.value = false
    isTogglePage.value = true
    isTogglePost.value = false
  } else {
    isToggleProduct.value = false
    isTogglePage.value = false
    isTogglePost.value = false
  }
})

watch(() => routeStore.name, () => {
  routeName.value = routeStore.name
  if (routeStore.name === 'categories.list' || routeStore.name === 'categories.create' ||
      routeStore.name === 'categories.edit' || routeStore.name === 'products.list' ||
      routeStore.name === 'products.create' || routeStore.name === 'products.edit' ||
      routeStore.name === 'attribute_group.create' || routeStore.name === 'attribute_group.edit' ||
      routeStore.name === 'attribute_group.list' || routeStore.name === 'attribute.create' ||
      routeStore.name === 'attribute.edit' || routeStore.name === 'attribute.list'
  ) {
    isToggleProduct.value = true
    isTogglePost.value = false
    isTogglePage.value = false
  } else if (routeStore.name === 'posts.list' || routeStore.name === 'posts.create' ||
      routeStore.name === 'posts.edit' || routeStore.name === 'post_groups.list' ||
      routeStore.name === 'post_groups.create' || routeStore.name === 'post_groups.edit'
  ) {
    isToggleProduct.value = false
    isTogglePage.value = false
    isTogglePost.value = true
  } else if (routeStore.name === 'pages.list' || routeStore.name === 'pages.create' ||
      routeStore.name === 'pages.edit' || routeStore.name === 'page_groups.list' ||
      routeStore.name === 'page_groups.create' || routeStore.name === 'page_groups.edit'
  ) {
    isToggleProduct.value = false
    isTogglePage.value = true
    isTogglePost.value = false
  } else {
    isToggleProduct.value = false
    isTogglePost.value = false
    isTogglePage.value = false
  }
});

</script>

<template>
  <!-- Desktop sidebar -->
  <aside
      :class="{'toggle-close': isToggleDesktop}"
      class="sidebar fixed top-0 h-full z-50 hidden shadow w-64 overflow-y-auto bg-white dark:bg-gray-800 md:block flex-shrink-0"
  >
    <div class="py-4 text-gray-500 dark:text-gray-400">
      <div class="flex content-center justify-between items-center px-7">
        <router-link class="justify-center flex text-lg font-bold text-gray-800 dark:text-gray-200"
                     to="/"
        >
          <img class="img-fluid w-32 h-14" :src="configs.logo">
        </router-link>

        <div @click="closeToggleDesktop">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5"
               viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
      </div>
      <ul class="mt-6">
        <li class="item menu-item">
          <div class="menu-link px-7 py-3">
                    <span
                        aria-hidden="true"
                        class="absolute inset-y-0 left-0 w-1 rounded-tr-lg rounded-br-lg"
                    ></span>
            <router-link :class="{'text-gray-800': routeName == 'home'}"
                         class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 dark:text-gray-100"
                         :to="{name:'home'}"
            >
                <span class="menu-icon">
                  <svg
                      aria-hidden="true"
                      class="w-5 h-5"
                      fill="none"
                      stroke="currentColor"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      viewBox="0 0 24 24"
                  >
                  <path
                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                  ></path>
                </svg></span>
              <span class="ml-4 menu-name">Trang chủ</span>
            </router-link>
          </div>
        </li>
        <li class="item menu-item cursor-pointer">
          <div class="menu-link">
            <div :class="{'text-gray-800': isToggleProduct}"
                 aria-haspopup="true"
                 class="inline-flex px-7 py-3 items-center justify-between w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                 @click="toggleProductMenu">
                        <span class="inline-flex items-center">

                <span class="menu-icon">
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5"
                               viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                          <path
                              d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"
                              stroke-linecap="round"
                              stroke-linejoin="round"/>
                        </svg>
                </span>
                          <span class="ml-4 menu-name">Sản phẩm</span>
                        </span>

              <svg v-if="isToggleProduct" aria-hidden="true" class="w-4 h-4" fill="currentColor"
                   viewBox="0 0 20 20">
                <path clip-rule="evenodd"
                      d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                      fill-rule="evenodd"></path>
              </svg>
              <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5"
                   viewBox="0 0 25 25" xmlns="http://www.w3.org/2000/svg">
                <path d="M8.25 4.5l7.5 7.5-7.5 7.5" stroke-linejoin="round"/>
              </svg>
            </div>
            <template v-if="isToggleProduct">
              <ul aria-label="submenu" class="submenu overflow-hidden text-sm font-medium text-gray-500">
                <li class="p-2 px-9 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200">
                  <router-link
                      :class="{'active text-gray-800': routeName === 'categories.list' ||  routeName === 'categories.create' || routeName === 'categories.edit'}"
                      class="menu-link w-full"
                      :to="{name: 'categories.list'}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                    <span>Nhóm sản phẩm</span>
                  </router-link>
                </li>
                <li class="p-2 px-9 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200">
                  <router-link
                      :class="{'active text-gray-800': routeName === 'products.list' || routeName === 'products.create' || routeName === 'products.edit'}"
                      class="menu-link w-full"
                      :to="{name: 'products.list'}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                    <span>Sản phẩm</span>
                  </router-link>
                </li>
                <li class="p-2 px-9 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200">
                  <router-link
                      :class="{'active text-gray-800': routeName === 'attribute_group.list' || routeName === 'attribute_group.create' || routeName === 'attribute_group.edit'}"
                      class="menu-link w-full"
                      :to="{name: 'attribute_group.list'}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                    <span>Nhóm thuộc tính</span>
                  </router-link>
                </li>
                <li class="p-2 px-9 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200">
                  <router-link
                      :class="{'active text-gray-800': routeName === 'attribute.list' || routeName === 'attribute.create' || routeName === 'attribute.edit'}"
                      class="menu-link w-full"
                      :to="{name: 'attribute.list'}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                    <span>Thuộc tính</span>
                  </router-link>
                </li>
              </ul>
            </template>
          </div>
        </li>

        <li class="item menu-item cursor-pointer">
          <div class="menu-link">
            <div :class="{'text-gray-800': isTogglePost}"
                 aria-haspopup="true"
                 class="inline-flex px-7 py-3 items-center justify-between w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                 @click="togglePostMenu">
                        <span class="inline-flex items-center">
                          <span class="menu-icon">
                             <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                  stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/>
                              </svg>
                          </span>
                          <span class="ml-4 menu-name">Bài viết</span>
                        </span>

              <svg v-if="isTogglePost" aria-hidden="true" class="w-4 h-4" fill="currentColor"
                   viewBox="0 0 20 20">
                <path clip-rule="evenodd"
                      d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                      fill-rule="evenodd"></path>
              </svg>
              <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5"
                   viewBox="0 0 25 25" xmlns="http://www.w3.org/2000/svg">
                <path d="M8.25 4.5l7.5 7.5-7.5 7.5" stroke-linejoin="round"/>
              </svg>
            </div>
            <template v-if="isTogglePost">
              <ul aria-label="submenu" class="submenu overflow-hidden text-sm font-medium text-gray-500">
                <li class="p-2 px-9 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200">
                  <router-link
                      :class="{'active text-gray-800': routeName === 'post_groups.list' ||  routeName === 'post_groups.create' || routeName === 'post_groups.edit'}"
                      class="menu-link w-full"
                      :to="{name: 'post_groups.list'}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                    <span>Nhóm Bài viết</span>
                  </router-link>
                </li>
                <li class="p-2 px-9 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200">
                  <router-link
                      :class="{'active text-gray-800': routeName === 'posts.list' ||  routeName === 'posts.create' || routeName === 'posts.edit'}"
                      class="menu-link w-full"
                      :to="{name: 'posts.list'}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                    <span>Bài viết</span>
                  </router-link>
                </li>
              </ul>
            </template>
          </div>
        </li>

        <li class="item menu-item cursor-pointer">
          <div class="menu-link">
            <div :class="{'text-gray-800': isTogglePage}"
                 aria-haspopup="true"
                 class="inline-flex px-7 py-3 items-center justify-between w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                 @click="togglePageMenu">
                        <span class="inline-flex items-center">
                          <span class="menu-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                            </svg>
                          </span>
                          <span class="ml-4 menu-name">Trang</span>
                        </span>

              <svg v-if="isTogglePage" aria-hidden="true" class="w-4 h-4" fill="currentColor"
                   viewBox="0 0 20 20">
                <path clip-rule="evenodd"
                      d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                      fill-rule="evenodd"></path>
              </svg>
              <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5"
                   viewBox="0 0 25 25" xmlns="http://www.w3.org/2000/svg">
                <path d="M8.25 4.5l7.5 7.5-7.5 7.5" stroke-linejoin="round"/>
              </svg>
            </div>
            <template v-if="isTogglePage">
              <ul aria-label="submenu" class="submenu overflow-hidden text-sm font-medium text-gray-500">
                <li class="p-2 px-9 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200">
                  <router-link
                      :class="{'active text-gray-800': routeName === 'page_groups.list' ||  routeName === 'page_groups.create' || routeName === 'page_groups.edit'}"
                      class="menu-link w-full"
                      :to="{name: 'page_groups.list'}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                    <span>Nhóm trang</span>
                  </router-link>
                </li>
                <li class="p-2 px-9 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200">
                  <router-link
                      :class="{'active text-gray-800': routeName === 'pages.list' ||  routeName === 'pages.create' || routeName === 'pages.edit'}"
                      class="menu-link w-full"
                      :to="{name: 'pages.list'}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                    <span>Trang</span>
                  </router-link>
                </li>
              </ul>
            </template>
          </div>
        </li>

        <li class="item menu-item">
          <div class="menu-link px-7 py-3">
              <span
                  aria-hidden="true"
                  class="absolute inset-y-0 left-0 w-1 rounded-tr-lg rounded-br-lg"
              ></span>
            <router-link
                :class="{'text-gray-800': routeName === 'users.list' || routeName === 'users.edit' || routeName === 'users.create'}"
                class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 dark:text-gray-100"
                :to="{name: 'users.list'}"
            >

                <span class="menu-icon">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5"
                     viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path
                      d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"
                      stroke-linecap="round"
                      stroke-linejoin="round"/>
                </svg>
                </span>
              <span class="ml-4 menu-name">Tài khoản admin</span>
            </router-link>
          </div>
        </li>

        <li class="item menu-item">
          <div class="menu-link px-7 py-3">
              <span
                  aria-hidden="true"
                  class="absolute inset-y-0 left-0 w-1 rounded-tr-lg rounded-br-lg"
              ></span>
            <router-link
                :class="{'text-gray-800': routeName === 'customers.list' || routeName === 'customers.edit' || routeName === 'customers.create'}"
                class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 dark:text-gray-100"
                :to="{name: 'customers.list'}"
            >

                <span class="menu-icon">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5"
                     viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path
                      d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"
                      stroke-linecap="round"
                      stroke-linejoin="round"/>
                </svg>
                </span>
              <span class="ml-4 menu-name">Khách hàng</span>
            </router-link>
          </div>
        </li>

        <li class="item menu-item">
          <div class="menu-link px-7 py-3">
              <span
                  aria-hidden="true"
                  class="absolute inset-y-0 left-0 w-1 rounded-tr-lg rounded-br-lg"
              ></span>
            <router-link
                :class="{'text-gray-800': routeName === 'orders.list' || routeName === 'orders.edit' || routeName === 'orders.create'}"
                class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 dark:text-gray-100"
                :to="{name: 'orders.list'}"
            >

                <span class="menu-icon">
                   <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-5 h-5">
                      <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/>
                    </svg>
                </span>
              <span class="ml-4 menu-name">Hoá đơn</span>
            </router-link>
          </div>
        </li>

        <li class="item menu-item">
          <div class="menu-link px-7 py-3">
              <span
                  aria-hidden="true"
                  class="absolute inset-y-0 left-0 w-1 rounded-tr-lg rounded-br-lg"
              ></span>
            <router-link
                :class="{'text-gray-800': routeName === 'supports.list' || routeName === 'supports.edit' || routeName === 'supports.create'}"
                class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 dark:text-gray-100"
                :to="{name: 'supports.list'}"
            >
                <span class="menu-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor" class="w-5 h-5">
                      <path stroke-linecap="round" stroke-linejoin="round"
                            d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 01-.825-.242m9.345-8.334a2.126 2.126 0 00-.476-.095 48.64 48.64 0 00-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0011.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155"/>
                    </svg>
                </span>
              <span class="ml-4 menu-name">Hỗ trợ</span>
            </router-link>
          </div>
        </li>

        <li class="item menu-item">
          <div class="menu-link px-7 py-3">
              <span
                  aria-hidden="true"
                  class="absolute inset-y-0 left-0 w-1 rounded-tr-lg rounded-br-lg"
              ></span>
            <router-link :class="{'text-gray-800': routeName === 'configs.list' || routeName === 'configs.edit'}"
                         class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 dark:text-gray-100"
                         :to="{name: 'configs.list'}"
            >
                <span class="menu-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor" class="w-5 h-5">
                      <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </span>
              <span class="ml-4 menu-name">Cài đặt</span>
            </router-link>
          </div>
        </li>
      </ul>
    </div>
  </aside>
  <!-- Mobile sidebar -->
  <!-- Backdrop -->
  <div
      @click="onBackdropMenu"
      v-show="!isToggleMenu"
      x-transition:enter="transition ease-in-out duration-150"
      x-transition:enter-start="opacity-0"
      x-transition:enter-end="opacity-100"
      x-transition:leave="transition ease-in-out duration-150"
      x-transition:leave-start="opacity-100"
      x-transition:leave-end="opacity-0"
      class="fixed inset-0 z-10 flex items-end bg-black bg-opacity-50 sm:items-center sm:justify-center"
  ></div>
  <aside
      v-on-click-outside="closeToggle"
      v-show="!isToggleMenu"
      class="sidebar fixed inset-y-0 z-20 flex-shrink-0 w-64 mt-16 overflow-y-auto bg-white dark:bg-gray-800 md:hidden"
      x-transition:enter="transition ease-in-out duration-150"
      x-transition:enter-end="opacity-100"
      x-transition:enter-start="opacity-0 transform -translate-x-20"
      x-transition:leave="transition ease-in-out duration-150"
      x-transition:leave-end="opacity-0 transform -translate-x-20"
      x-transition:leave-start="opacity-100"
  >
    <div class="py-4 text-gray-500 dark:text-gray-400">
      <div class="flex content-center justify-between items-center px-7">
        <router-link class="justify-center flex text-lg font-bold text-gray-800 dark:text-gray-200"
                     to="/"
        >
          <img class="img-fluid w-32 h-14" :src="configs.logo">
        </router-link>

        <div @click="closeToggleDesktop">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5"
               viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
      </div>
      <ul class="mt-6">
        <li class="item menu-item">
          <div class="menu-link px-7 py-3">
                    <span
                        aria-hidden="true"
                        class="absolute inset-y-0 left-0 w-1 rounded-tr-lg rounded-br-lg"
                    ></span>
            <router-link :class="{'text-gray-800': routeName == 'home'}"
                         class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 dark:text-gray-100"
                         :to="{name:'home'}"
            >
                <span class="menu-icon">
                  <svg
                      aria-hidden="true"
                      class="w-5 h-5"
                      fill="none"
                      stroke="currentColor"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      viewBox="0 0 24 24"
                  >
                  <path
                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                  ></path>
                </svg></span>
              <span class="ml-4 menu-name">Trang chủ</span>
            </router-link>
          </div>
        </li>
        <li class="item menu-item cursor-pointer">
          <div class="menu-link">
            <div :class="{'text-gray-800': isToggleProduct}"
                 aria-haspopup="true"
                 class="inline-flex px-7 py-3 items-center justify-between w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                 @click="toggleProductMenu">
                        <span class="inline-flex items-center">

                <span class="menu-icon">
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5"
                               viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                          <path
                              d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"
                              stroke-linecap="round"
                              stroke-linejoin="round"/>
                        </svg>
                </span>
                          <span class="ml-4 menu-name">Sản phẩm</span>
                        </span>

              <svg v-if="isToggleProduct" aria-hidden="true" class="w-4 h-4" fill="currentColor"
                   viewBox="0 0 20 20">
                <path clip-rule="evenodd"
                      d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                      fill-rule="evenodd"></path>
              </svg>
              <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5"
                   viewBox="0 0 25 25" xmlns="http://www.w3.org/2000/svg">
                <path d="M8.25 4.5l7.5 7.5-7.5 7.5" stroke-linejoin="round"/>
              </svg>
            </div>
            <template v-if="isToggleProduct">
              <ul aria-label="submenu" class="submenu overflow-hidden text-sm font-medium text-gray-500">
                <li class="p-2 px-9 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200">
                  <router-link
                      :class="{'active text-gray-800': routeName === 'categories.list' ||  routeName === 'categories.create' || routeName === 'categories.edit'}"
                      class="menu-link w-full"
                      :to="{name: 'categories.list'}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                    <span>Nhóm sản phẩm</span>
                  </router-link>
                </li>
                <li class="p-2 px-9 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200">
                  <router-link
                      :class="{'active text-gray-800': routeName === 'products.list' || routeName === 'products.create' || routeName === 'products.edit'}"
                      class="menu-link w-full"
                      :to="{name: 'products.list'}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                    <span>Sản phẩm</span>
                  </router-link>
                </li>
                <li class="p-2 px-9 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200">
                  <router-link
                      :class="{'active text-gray-800': routeName === 'attribute_group.list' || routeName === 'attribute_group.create' || routeName === 'attribute_group.edit'}"
                      class="menu-link w-full"
                      :to="{name: 'attribute_group.list'}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                    <span>Nhóm thuộc tính</span>
                  </router-link>
                </li>
                <li class="p-2 px-9 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200">
                  <router-link
                      :class="{'active text-gray-800': routeName === 'attribute.list' || routeName === 'attribute.create' || routeName === 'attribute.edit'}"
                      class="menu-link w-full"
                      :to="{name: 'attribute.list'}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                    <span>Thuộc tính</span>
                  </router-link>
                </li>
              </ul>
            </template>
          </div>
        </li>

        <li class="item menu-item cursor-pointer">
          <div class="menu-link">
            <div :class="{'text-gray-800': isTogglePost}"
                 aria-haspopup="true"
                 class="inline-flex px-7 py-3 items-center justify-between w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                 @click="togglePostMenu">
                        <span class="inline-flex items-center">
                          <span class="menu-icon">
                             <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                  stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/>
                              </svg>
                          </span>
                          <span class="ml-4 menu-name">Bài viết</span>
                        </span>

              <svg v-if="isTogglePost" aria-hidden="true" class="w-4 h-4" fill="currentColor"
                   viewBox="0 0 20 20">
                <path clip-rule="evenodd"
                      d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                      fill-rule="evenodd"></path>
              </svg>
              <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5"
                   viewBox="0 0 25 25" xmlns="http://www.w3.org/2000/svg">
                <path d="M8.25 4.5l7.5 7.5-7.5 7.5" stroke-linejoin="round"/>
              </svg>
            </div>
            <template v-if="isTogglePost">
              <ul aria-label="submenu" class="submenu overflow-hidden text-sm font-medium text-gray-500">
                <li class="p-2 px-9 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200">
                  <router-link
                      :class="{'active text-gray-800': routeName === 'post_groups.list' ||  routeName === 'post_groups.create' || routeName === 'post_groups.edit'}"
                      class="menu-link w-full"
                      :to="{name: 'post_groups.list'}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                    <span>Nhóm Bài viết</span>
                  </router-link>
                </li>
                <li class="p-2 px-9 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200">
                  <router-link
                      :class="{'active text-gray-800': routeName === 'posts.list' ||  routeName === 'posts.create' || routeName === 'posts.edit'}"
                      class="menu-link w-full"
                      :to="{name: 'posts.list'}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                    <span>Bài viết</span>
                  </router-link>
                </li>
              </ul>
            </template>
          </div>
        </li>

        <li class="item menu-item cursor-pointer">
          <div class="menu-link">
            <div :class="{'text-gray-800': isTogglePage}"
                 aria-haspopup="true"
                 class="inline-flex px-7 py-3 items-center justify-between w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200"
                 @click="togglePageMenu">
                        <span class="inline-flex items-center">
                          <span class="menu-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                            </svg>
                          </span>
                          <span class="ml-4 menu-name">Trang</span>
                        </span>

              <svg v-if="isTogglePage" aria-hidden="true" class="w-4 h-4" fill="currentColor"
                   viewBox="0 0 20 20">
                <path clip-rule="evenodd"
                      d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                      fill-rule="evenodd"></path>
              </svg>
              <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5"
                   viewBox="0 0 25 25" xmlns="http://www.w3.org/2000/svg">
                <path d="M8.25 4.5l7.5 7.5-7.5 7.5" stroke-linejoin="round"/>
              </svg>
            </div>
            <template v-if="isTogglePage">
              <ul aria-label="submenu" class="submenu overflow-hidden text-sm font-medium text-gray-500">
                <li class="p-2 px-9 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200">
                  <router-link
                      :class="{'active text-gray-800': routeName === 'page_groups.list' ||  routeName === 'page_groups.create' || routeName === 'page_groups.edit'}"
                      class="menu-link w-full"
                      :to="{name: 'page_groups.list'}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                    <span>Nhóm trang</span>
                  </router-link>
                </li>
                <li class="p-2 px-9 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200">
                  <router-link
                      :class="{'active text-gray-800': routeName === 'pages.list' ||  routeName === 'pages.create' || routeName === 'pages.edit'}"
                      class="menu-link w-full"
                      :to="{name: 'pages.list'}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                    <span>Trang</span>
                  </router-link>
                </li>
              </ul>
            </template>
          </div>
        </li>

        <li class="item menu-item">
          <div class="menu-link px-7 py-3">
              <span
                  aria-hidden="true"
                  class="absolute inset-y-0 left-0 w-1 rounded-tr-lg rounded-br-lg"
              ></span>
            <router-link
                :class="{'text-gray-800': routeName === 'users.list' || routeName === 'users.edit' || routeName === 'users.create'}"
                class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 dark:text-gray-100"
                :to="{name: 'users.list'}"
            >

                <span class="menu-icon">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5"
                     viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path
                      d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"
                      stroke-linecap="round"
                      stroke-linejoin="round"/>
                </svg>
                </span>
              <span class="ml-4 menu-name">Tài khoản admin</span>
            </router-link>
          </div>
        </li>

        <li class="item menu-item">
          <div class="menu-link px-7 py-3">
              <span
                  aria-hidden="true"
                  class="absolute inset-y-0 left-0 w-1 rounded-tr-lg rounded-br-lg"
              ></span>
            <router-link
                :class="{'text-gray-800': routeName === 'customers.list' || routeName === 'customers.edit' || routeName === 'customers.create'}"
                class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 dark:text-gray-100"
                :to="{name: 'customers.list'}"
            >

                <span class="menu-icon">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5"
                     viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path
                      d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"
                      stroke-linecap="round"
                      stroke-linejoin="round"/>
                </svg>
                </span>
              <span class="ml-4 menu-name">Khách hàng</span>
            </router-link>
          </div>
        </li>

        <li class="item menu-item">
          <div class="menu-link px-7 py-3">
              <span
                  aria-hidden="true"
                  class="absolute inset-y-0 left-0 w-1 rounded-tr-lg rounded-br-lg"
              ></span>
            <router-link
                :class="{'text-gray-800': routeName === 'orders.list' || routeName === 'orders.edit' || routeName === 'orders.create'}"
                class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 dark:text-gray-100"
                :to="{name: 'orders.list'}"
            >

                <span class="menu-icon">
                   <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-5 h-5">
                      <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/>
                    </svg>
                </span>
              <span class="ml-4 menu-name">Hoá đơn</span>
            </router-link>
          </div>
        </li>

        <li class="item menu-item">
          <div class="menu-link px-7 py-3">
              <span
                  aria-hidden="true"
                  class="absolute inset-y-0 left-0 w-1 rounded-tr-lg rounded-br-lg"
              ></span>
            <router-link
                :class="{'text-gray-800': routeName === 'supports.list' || routeName === 'supports.edit' || routeName === 'supports.create'}"
                class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 dark:text-gray-100"
                :to="{name: 'supports.list'}"
            >
                <span class="menu-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor" class="w-5 h-5">
                      <path stroke-linecap="round" stroke-linejoin="round"
                            d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 01-.825-.242m9.345-8.334a2.126 2.126 0 00-.476-.095 48.64 48.64 0 00-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0011.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155"/>
                    </svg>
                </span>
              <span class="ml-4 menu-name">Hỗ trợ</span>
            </router-link>
          </div>
        </li>

        <li class="item menu-item">
          <div class="menu-link px-7 py-3">
              <span
                  aria-hidden="true"
                  class="absolute inset-y-0 left-0 w-1 rounded-tr-lg rounded-br-lg"
              ></span>
            <router-link :class="{'text-gray-800': routeName === 'configs.list' || routeName === 'configs.edit'}"
                         class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 dark:text-gray-100"
                         :to="{name: 'configs.list'}"
            >
                <span class="menu-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor" class="w-5 h-5">
                      <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </span>
              <span class="ml-4 menu-name">Cài đặt</span>
            </router-link>
          </div>
        </li>
      </ul>
    </div>
  </aside>
</template>
<style lang="scss" scoped>
.center {
  text-align: center !important;
}

body {
  padding-right: 0px !important;
}

.transparent {
  border-color: transparent !important;
}

.submenu .menu-bullet {
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 1.25rem;
  margin-right: 0.5rem;
}

.submenu .menu-bullet .bullet {
  background-color: #9899ac;
}

.submenu .bullet-dot {
  width: 4px;
  height: 4px;
  border-radius: 100% !important;
}

.submenu .bullet {
  display: inline-block;
  background-color: #b5b5c3;
  border-radius: 6px;
  width: 6px;
  height: 6px;
  flex-shrink: 0;
}

.submenu .menu-link:hover .menu-bullet .bullet,
.submenu .menu-link.active .menu-bullet .bullet {
  background-color: #009ef7;
}

.submenu .menu-link {
  cursor: pointer;
  display: flex;
  align-items: center;
  padding: 0;
  flex: 0 0 100%;
  transition: none;
  outline: 0 !important;
}

.toggle-close {
  width: 75px !important;
  transition: width .3s ease;

  .menu-name {
    display: none;
  }

  .submenu {
    display: none;
  }

  &:hover {
    transition: width .3s ease;
    width: 16rem !important;

    .menu-name {
      display: block;
    }

    .submenu {
      display: block;
    }
  }
}

.menu-item.item {
  cursor: pointer;
  display: flex;
  align-items: center;
  flex: 0 0 100%;
  transition: none;
  outline: 0 !important;
  width: 16rem;

  .menu-link {
    cursor: pointer;
    align-items: center;
    flex: 0 0 100%;
    transition: none;
    outline: 0 !important;

    .inline-flex {
      display: flex;
      align-content: center;
      align-items: center;
    }
  }
}

</style>
