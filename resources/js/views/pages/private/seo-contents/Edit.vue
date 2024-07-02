<template>
  <Page :title="page.title" :breadcrumbs="page.breadcrumbs" :actions="page.actions" @action="onAction"
        :is-loading="page.loading">
    <Panel>
      <Form id="edit-post" @submit.prevent="onSubmit">
        <TextInput class="mb-4" type="url" :required="true" error-input="link" name="link" v-model="form.link"
                   label="Đường dẫn"/>
        <div class="mb-4">
          <CkEditorCustom :content="form.description" @updateData="(value) => updateData(value)" />
          <span v-if="alertStore.errors['description']" class="text-xs tracking-wide text-red-600">{{
              alertStore.errors['description'][0]
            }}</span>
        </div>
        <Toggle class="mb-4" v-model="form.is_active" :checked="form.is_active" error-input="is_active"
                name="is_active"
                :label="trans('labels.show')"/>
      </Form>
    </Panel>
  </Page>
</template>

<script setup>
import {defineComponent, onBeforeMount, reactive, ref, watch} from "vue";
import {trans} from "@/helpers/i18n";
import {clearObject, fillObject, reduceProperties} from "@/helpers/data"
import {useRoute} from "vue-router";
import {toUrl} from "@/helpers/routing";
import SeoContentService from "@/services/SeoContentService";
import Button from "@/views/components/input/Button";
import TextInput from "@/views/components/input/TextInput";
import Dropdown from "@/views/components/input/Dropdown";
import Alert from "@/views/components/Alert";
import Panel from "@/views/components/Panel";
import Page from "@/views/layouts/Page";
import FileInput from "@/views/components/input/FileInput";
import Form from "@/views/components/Form";
import Toggle from "@/views/components/input/Toggle.vue";
import {QuillEditor} from "@vueup/vue-quill";
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import {useAlertStore} from "@/stores";

// Import styles

const alertStore = useAlertStore();
const route = useRoute();
const item = ref(null);
const group = ref(null);
const form = reactive({
  link: '',
  description: '',
  is_active: false,
});

const options = ref({
  debug: 'info',
  toolbar: 'full',
  theme: 'snow',
  contentType: 'html',
});

const page = reactive({
  id: 'edit_user',
  title: 'SEO',
  filters: false,
  loading: true,
  breadcrumbs: [
    {
      name: 'SEO',
      to: toUrl('/seo-contents/list'),
    },
    {
      name: 'Chỉnh sửa',
      active: true,
    }
  ],
  actions: [
    {
      id: 'back',
      name: trans('global.buttons.back'),
      icon: "fa fa-angle-left",
      to: toUrl('/seo-contents/list'),
      theme: 'outline',
    },
    {
      id: 'submit',
      name: trans('global.buttons.update'),
      icon: "fa fa-save",
      type: 'submit'
    }
  ]
});

const service = new SeoContentService();

onBeforeMount(() => {
  service.edit(route.params.id).then((response) => {
    fillObject(form, response.data.model);
    item.value = response.data.model;
    page.loading = false;
  })
});

function onAction(data) {
  switch (data.action.id) {
    case 'submit':
      onSubmit();
      break;
  }
}

function onSubmit() {
  service.handleUpdate('edit-post', route.params.id, reduceProperties(form, 'roles', 'id')).then((response) => {
    if (alertStore.type == 'success') {

      item.value = response.data.model;
      page.loading = false;
    }
  });
  return false;
}

function updateData(value) {
  form.description = value
}
</script>

<style scoped>

</style>
<style>
.ql-editor {
  min-height: 200px;
}
</style>
