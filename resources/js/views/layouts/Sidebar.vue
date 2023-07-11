<script setup>
import {vOnClickOutside} from '@vueuse/components'
import {useConfig} from '@/stores/config';
import {useStore} from '@/stores/sidebar';
import {useRoute} from 'vue-router';
import {onMounted, ref, watch } from "vue";
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

function toggleProductMenu() {
  isToggleProduct.value = !isToggleProduct.value;
}

function onBackdropMenu(e) {
  if(!e.target.classList.contains('sidebar')) {
    isToggleMenu.value = true;
  }
}

onMounted(() => {
  routeName.value = routeStore.name
  if (routeStore.name === 'categories.list' || routeStore.name === 'categories.create' || routeStore.name === 'categories.edit' || routeStore.name === 'products.list' || routeStore.name === 'products.create' || routeStore.name === 'products.edit') {
    isToggleProduct.value = true
  } else {
    isToggleProduct.value = false
  }
})

watch(() => routeStore.name, () => {
  routeName.value = routeStore.name
    if (routeStore.name === 'categories.list' || routeStore.name === 'categories.create' || routeStore.name === 'categories.edit' || routeStore.name === 'products.list' || routeStore.name === 'products.create' || routeStore.name === 'products.edit') {    isToggleProduct.value = true
  } else {
    isToggleProduct.value = false
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
                        class="absolute inset-y-0 left-0 w-1 bg-purple-600 rounded-tr-lg rounded-br-lg"
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
                  <router-link :class="{'active text-gray-800': routeName === 'categories.list' ||  routeName === 'categories.create' || routeName === 'categories.edit'}" class="menu-link w-full"
                               :to="{name: 'categories.list'}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                    <span>Nhóm sản phẩm</span>
                  </router-link>
                </li>
                <li class="p-2 px-9 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200">
                  <router-link :class="{'active text-gray-800': routeName === 'products.list' || routeName === 'products.create' || routeName === 'products.edit'}" class="menu-link w-full"
                               :to="{name: 'products.list'}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                    <span>Sản phẩm</span>
                  </router-link>
                </li>
                <li class="p-2 px-9 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200">
                  <router-link :class="{'active text-gray-800': routeName === 'unit'}" class="menu-link w-full"
                               to="/units">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                    <span>Đơn vị tính</span>
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
                  class="absolute inset-y-0 left-0 w-1 bg-purple-600 rounded-tr-lg rounded-br-lg"
              ></span>
            <router-link :class="{'text-gray-800': routeName === 'users.list'}"
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
              <span class="ml-4 menu-name">Admin</span>
            </router-link>
          </div>
        </li>

        <li class="item menu-item">
          <div class="menu-link px-7 py-3">
              <span
                  aria-hidden="true"
                  class="absolute inset-y-0 left-0 w-1 bg-purple-600 rounded-tr-lg rounded-br-lg"
              ></span>
            <router-link :class="{'text-gray-800': routeName === 'customers.list'}"
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
      <div>
        <router-link class="justify-center flex text-lg font-bold text-gray-800 dark:text-gray-200"
                     to="/"
        >
          <img class="img-fluid w-32 h-14" :src="configs.logo">
        </router-link>
      </div>
      <ul class="mt-6">
        <li class="item menu-item">
          <div class="menu-link px-7 py-3">
                    <span
                        aria-hidden="true"
                        class="absolute inset-y-0 left-0 w-1 bg-purple-600 rounded-tr-lg rounded-br-lg"
                    ></span>
            <router-link :class="{'text-gray-800': routeName === 'home'}"
                         class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 dark:text-gray-100"
                         :to="{name: 'home'}"
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
        <li class="items menu-item">
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
                  <router-link :class="{'active text-gray-800': routeName === 'category'}" class="menu-link w-full"
                               to="/categories">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                    <span>Nhóm sản phẩm</span>
                  </router-link>
                </li>
                <li class="p-2 px-9 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200">
                  <router-link :class="{'active text-gray-800': routeName === 'product'}" class="menu-link w-full"
                               to="/products">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                    <span>Sản phẩm</span>
                  </router-link>
                </li>
                <li class="p-2 px-9 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200">
                  <router-link :class="{'active text-gray-800': routeName === 'unit'}" class="menu-link w-full"
                               to="/units">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                    <span>Đơn vị tính</span>
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
                  class="absolute inset-y-0 left-0 w-1 bg-purple-600 rounded-tr-lg rounded-br-lg"
              ></span>
            <router-link :class="{'text-gray-800': routeName === 'customer'}"
                         class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 dark:text-gray-100"
                         to="/customers"
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
