<template>
  <Page :title="page.title" :breadcrumbs="page.breadcrumbs" :actions="page.actions"
        :is-loading="page.loading">
    <Panel>

      <Tab :tabs="tabs" @set-index="updateTabIndex" :active-index="activeTab">
        <div v-if="activeTab === 0">
          <EditInformation :id="id"/>
        </div>
        <div v-if="activeTab === 1">
         <EditAttribute :id="id"  @information-attribute="informationAttribute"/>
        </div>
        <div v-if="activeTab === 2">
          <ProductVariantMain :id="id" :refresh="refresh"/>
        </div>
        <div v-if="activeTab === 3">
          <ProductVariant :id="id" :refresh="refresh"/>
        </div>
      </Tab>
    </Panel>
  </Page>
</template>

<script setup>
import {defineComponent, onBeforeMount, reactive, ref} from "vue";
import {trans} from "@/helpers/i18n";
import {useRoute} from "vue-router";
import {toUrl} from "@/helpers/routing";

import Panel from "@/views/components/Panel";
import Page from "@/views/layouts/Page";
import {useAlertStore} from "@/stores/alert";
import {QuillEditor} from "@vueup/vue-quill"
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import EditInformation from "@/views/pages/private/products/EditInformation.vue";

import Tab from "@/views/components/Tab.vue";
import EditAttribute from "@/views/pages/private/products/EditAttribute.vue";
import ProductVariant from "@/views/pages/private/products/ProductVariant.vue";
import ProductVariantMain from "@/views/pages/private/products/ProductVariantMain.vue";


const alertStore = useAlertStore();
const route = useRoute();
const id = ref(route.params.id);

const options = ref({
  debug: 'info',
  toolbar: 'full',
  theme: 'snow',
  contentType: 'html',
});
const page = reactive({
  id: 'edit_user',
  title: trans('global.pages.products_edit'),
  filters: false,
  loading: false,
  breadcrumbs: [
    {
      name: trans('global.pages.products'),
      to: toUrl('/products/list'),
    },
    {
      name: trans('global.pages.products_edit'),
      active: true,
    }
  ],
  actions: [
    {
      id: 'back',
      name: trans('global.buttons.back'),
      icon: "fa fa-angle-left",
      to: toUrl('/products/list'),
      theme: 'outline',
    }
  ]
});


const activeTab = ref(0)
const refresh = ref(0)

function updateTabIndex(index) {
  activeTab.value = index
}

function informationUpdate(bool) {
  page.loading = bool
}

const tabs = ref([
  {title: 'Thông tin chung', href: `/products/${route.params.id}/edit#first`, content: '<p>Content for Tab 1</p>'},
  {title: 'Thuộc tính', href: `/products/${route.params.id}/edit#second`, content: '<p>Content for Tab 2</p>'},
  {title: 'Biến thể chính', href: `/products/${route.params.id}/edit#third`, content: '<p>Content for Tab 3</p>'},
  {title: 'Biến thể', href: `/products/${route.params.id}/edit#four`, content: '<p>Content for Tab 3</p>'}
]);

onBeforeMount(() => {
  if (route.hash) {
    if (route.hash == '#second') {
      activeTab.value = 1
    } else if (route.hash == '#third') {
      activeTab.value = 2
    } else if (route.hash == '#four') {
      activeTab.value = 3
    }
  }
})

function informationAttribute() {
  refresh.value++
}
</script>

<style scoped>

</style>
