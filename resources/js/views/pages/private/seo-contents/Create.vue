<template>
  <Page :title="page.title" :breadcrumbs="page.breadcrumbs" :actions="page.actions" @action="onAction">
    <Panel>
      <Form id="create-post" @submit.prevent="onSubmit">
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
import {defineComponent, reactive, ref, watch} from "vue";
import {trans} from "@/helpers/i18n";
import Button from "@/views/components/input/Button";
import TextInput from "@/views/components/input/TextInput";
import Dropdown from "@/views/components/input/Dropdown";
import Alert from "@/views/components/Alert";
import Panel from "@/views/components/Panel";
import Page from "@/views/layouts/Page";
import FileInput from "@/views/components/input/FileInput";
import SeoContentService from "@/services/SeoContentService";
import {clearObject, reduceProperties} from "@/helpers/data";
import {toUrl} from "@/helpers/routing";
import Form from "@/views/components/Form";
import {useAlertStore} from "@/stores/alert";
import Toggle from "@/views/components/input/Toggle.vue";
import {QuillEditor} from "@vueup/vue-quill";
import '@vueup/vue-quill/dist/vue-quill.snow.css';

// Import styles
import CkEditorCustom from "@/views/components/CkEditorCustom.vue";

const alertStore = useAlertStore();
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
  id: 'create_posts',
  title: 'SEO',
  filters: false,
  breadcrumbs: [
    {
      name: 'SEO',
      to: toUrl('/seo-contents/list'),

    },
    {
      name: 'Thêm',
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
      name: trans('global.buttons.save'),
      icon: "fa fa-save",
      type: 'submit',
    }
  ]
});

const service = new SeoContentService();

function onAction(data) {
  switch (data.action.id) {
    case 'submit':
      onSubmit();
      break;
  }
}

function onSubmit() {
  service.handleCreate('create-post', reduceProperties(form, 'roles', 'id')).then(() => {
    if (alertStore.type == 'success') {
      clearObject(form)
    }
  })
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
