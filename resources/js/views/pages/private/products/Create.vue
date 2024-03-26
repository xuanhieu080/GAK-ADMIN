<template>
  <Page :title="page.title" :breadcrumbs="page.breadcrumbs" :actions="page.actions" @action="onAction">
    <Panel>
      <div>
        <Information :information="informationCreate" @information-update="informationUpdate"/>
        <!--        <Tab :tabs="tabs" @set-index="updateTabIndex">-->
        <!--          <div v-show="activeTab === 0"-->
        <!--               :class="{ hidden: activeTab !== 0 }">-->
        <!--            <Information @information-update="informationUpdate"/>-->
        <!--          </div>-->
        <!--          <div v-show="activeTab === 1"-->
        <!--               :class="{ hidden: activeTab !== 1 }">-->
        <!--            <p>Content for Tab 2</p>-->
        <!--          </div>-->
        <!--          <div v-show="activeTab === 2"-->
        <!--               :class="{ hidden: activeTab !== 2 }">-->
        <!--&lt;!&ndash;            <Information />&ndash;&gt;-->
        <!--          </div>-->
        <!--        </Tab>-->
      </div>

    </Panel>
  </Page>
</template>

<script setup>
import {defineComponent, reactive, ref} from "vue";
import {trans} from "@/helpers/i18n";
import Button from "@/views/components/input/Button";
import TextInput from "@/views/components/input/TextInput";
import Dropdown from "@/views/components/input/Dropdown";
import Alert from "@/views/components/Alert";
import Panel from "@/views/components/Panel";
import Page from "@/views/layouts/Page";
import FileInput from "@/views/components/input/FileInput";
import ProductService from "@/services/ProductService";
import {clearObject, reduceProperties} from "@/helpers/data";
import {toUrl} from "@/helpers/routing";
import Form from "@/views/components/Form";
import {useAlertStore} from "@/stores/alert";
import Toggle from "@/views/components/input/Toggle.vue";
import Spinner from "@/views/components/icons/Spinner.vue";
import Tab from "@/views/components/Tab.vue";
import {QuillEditor} from "@vueup/vue-quill";
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import Information from "@/views/pages/private/products/Information.vue";
import {rand} from "@vueuse/core";

const alertStore = useAlertStore();
const activeTab = ref(0)
const category = ref(null)
const file = ref(null)
const information = reactive({
  name: null,
  description: null,
  category_id: null,
  price: 0,
  priority: 100,
  is_active: false,
  meta_title: null,
  meta_description: null,
  meta_key: null,
  slug: null,
  video_link: null,
  thumb_image: [],
});
const informationCreate = ref({
  name: null,
  description: null,
  category_id: null,
  price: 0,
  priority: 100,
  is_active: false,
  meta_title: null,
  meta_description: null,
  meta_key: null,
  video_link: null,
  slug: null,
});

const options = ref({
  debug: 'info',
  toolbar: 'full',
  theme: 'snow',
  contentType: 'html',
});

const page = reactive({
  id: 'create_products',
  title: trans('global.pages.products_create'),
  filters: false,
  breadcrumbs: [
    {
      name: trans('global.pages.products'),
      to: toUrl('/products/list'),

    },
    {
      name: trans('global.pages.products_create'),
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
    },
    {
      id: 'submit',
      name: trans('global.buttons.save'),
      icon: "fa fa-save",
      type: 'submit',
    }
  ]
});

const service = new ProductService();

function onAction(data) {
  switch (data.action.id) {
    case 'submit':
      onSubmit();
      break;
  }
}

function updateTabIndex(index) {
  activeTab.value = index
}

function onSubmit() {
  if (category.value && category.value.id) {
    information.category_id = category.value.id;
  }
  service.handleCreate('create-product', reduceProperties(information, 'roles', 'id')).then(() => {
    if (alertStore.type == 'success') {
      clearObject(information)
      clearData();
    }
  })
  return false;
}

function clearData() {
  information.name = null;
  information.file = null;
  information.image = null;
  information.description = null;
  information.category_id = null;
  information.price = 0
  information.priority = 100;
  information.is_active = false;
  information.meta_title = null;
  information.meta_description = null;
  information.slug = null;
  information.meta_key = null;
  information.video_link = null;
  information.thumb_image = [];
  informationCreate.value = information;
}

function informationUpdate(data) {
  information.name = data.name;
  information.file = data.image;
  information.image = data.image;
  information.description = data.description;
  information.category_id = data.category_id;
  information.price = data.price;
  information.priority = data.priority;
  information.is_active = data.is_active;
  information.meta_title = data.meta_title;
  information.meta_description = data.meta_description;
  information.meta_key = data.meta_key;
  information.slug = data.slug;
  information.video_link = data.video_link;
  information.thumb_image = data.thumb_image;
}
</script>

<style scoped>

</style>
