<template>
  <Page :title="page.title" :breadcrumbs="page.breadcrumbs" :actions="page.actions" @action="onAction">
    <Panel>
      <div>
        <Tab :tabs="tabs" @set-index="updateTabIndex">
          <div v-show="activeTab === 0"
               :class="{ hidden: activeTab !== 0 }">
            <p>Content for Tab 1</p>
          </div>
          <div v-show="activeTab === 1"
               :class="{ hidden: activeTab !== 1 }">
            <p>Content for Tab 2</p>
          </div>
          <div v-show="activeTab === 2"
               :class="{ hidden: activeTab !== 2 }">
           <Information />
          </div>
        </Tab>
      </div>

    </Panel>
  </Page>
</template>

<script>
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

export default defineComponent({
  components: {
    Information,
    QuillEditor, Spinner, Toggle, Form, FileInput, Panel, Alert, Dropdown, TextInput, Button, Page, Tab},
  setup() {
    const tabs = ref([
      { title: 'Thông tin chung', href: '/products/create#first', content: '<p>Content for Tab 1</p>' },
      { title: 'Thuộc tính', href: '/products/create#second', content: '<p>Content for Tab 2</p>' },
      { title: 'Biến thể', href: '/products/create#third', content: '<p>Content for Tab 3</p>' }
    ]);
    const alertStore = useAlertStore();
    const activeTab = ref(0)
    const category = ref(null)
    const file = ref(null)
    const form = reactive({
      name: null,
      description: null,
      category_id: null,
      price: 0,
      priority: 100,
      is_active: false,
      details: [],
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
        form.category_id = category.value.id;
      }
      if (file.value != null) {
        form.file = file.value;
      }

      service.handleCreate('create-product', reduceProperties(form, 'roles', 'id')).then(() => {
        if (alertStore.type == 'success') {
          clearObject(form)
          clearData();
        }
      })
      return false;
    }

    function clearImage() {
      file.value = null
      form.file = null
    }

    function addDetail() {
      form.details[form.details.length] = ''
    }

    function deleteDetail(index) {
      if (form.details[index]) {
        form.details.splice(index, 1);
        if (alertStore.errors[`details.${index}`]) {
          delete alertStore.errors[`details.${index}`]
        }
      }
    }

    function clearData() {
      form.details = [];
      file.value = null
      form.file = null
      category.value = null
    }

    return {
      trans,
      form,
      category,
      file,
      page,
      onSubmit,
      onAction,
      clearImage,
      addDetail,
      deleteDetail,
      clearData,
      options,
      alertStore,
      tabs,
      activeTab,
      updateTabIndex
    }
  }
})
</script>

<style scoped>

</style>
