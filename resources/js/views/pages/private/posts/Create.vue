<template>
  <Page :title="page.title" :breadcrumbs="page.breadcrumbs" :actions="page.actions" @action="onAction">
    <Panel>
      <Form id="create-post" @submit.prevent="onSubmit">
        <TextInput class="mb-4" type="text" :required="true" error-input="title" name="title" v-model="form.title"
                   :label="trans('labels.title')"/>
        <TextInput class="mb-4" type="text" :required="true" error-input="slug" name="slug" v-model="form.slug"
                   label="Slug"/>
        <Dropdown class="mb-4" name="group" error-input="group_id" :multiple="false"
                  server="post-groups" :label="trans('Nhóm bài viết')" :placeholder="trans('Nhóm bài viết')"
                  :server-search-min-characters="0"
                  v-model="group"></Dropdown>
        <div class="flex justify-center">
          <div class="w-[500px]">
            <FilePond
                ref="pondElement"
                class="product-image"
                label-idle="Kéo thả hoặc chọn hình ảnh tại đây"
                accepted-file-types="image/*"
                label-max-file-size-exceeded="File quá lớn"
                :max-file-size="maxFileSize"
                allow-file-size-validation="true"
                class-name="upload-job-image flex items-center justify-center w-full"
                name="image"
                :label-max-file-size="'Kích thước tệp tối đa là ' +  maxFileSize"
                required="true"
                credits="false"
                :accepted-file-types="acceptedFileTypes"
                :label-file-type-not-allowed="'Invalid file format'"
                :file-validate-type-label-expected-types="'Định dạng cho phép {format}'"
                v-on:addfile="getImage"
                v-on:removefile="removeImage"
            />
            <span v-if="alertStore.errors['image']" class="text-xs tracking-wide text-red-600">{{
                alertStore.errors['image'][0]
              }}</span>
          </div>
        </div>
        <div class="mb-4">
          <CkEditorCustom :content="form.content" @updateData="(value) => updateData(value)" />
          <span v-if="alertStore.errors['content']" class="text-xs tracking-wide text-red-600">{{
              alertStore.errors['content'][0]
            }}</span>
        </div>
        <TextInput class="mb-4" type="text" :required="true" error-input="meta_title" name="name"
                   v-model="form.meta_title"
                   label="Meta title"/>
        <TextInput class="mb-4" type="textarea" :required="true" :rows="5" name="meta_description"
                   v-model="form.meta_description"
                   error-input="meta_description" label="Meta description"/>
        <TextInput class="mb-4" type="text" error-input="meta_key" name="name" v-model="form.meta_key"
                   label="Meta key"/>
        <TextInput class="mb-4" type="number" :min="0" :max="999999999999" name="view" v-model="form.view"
                    error-input="view" label="Lượt view"/>
        <Toggle class="mb-4" v-model="form.is_new" :checked="form.is_new" error-input="is_new"
                name="is_new"
                label="Bài viết có nội dung mới"/>
        <Toggle class="mb-4" v-model="form.is_hot" :checked="form.is_hot" error-input="is_hot"
                name="is_hot"
                label="Nổi bật"/>
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
import PostService from "@/services/PostService";
import {clearObject, reduceProperties} from "@/helpers/data";
import {toUrl} from "@/helpers/routing";
import Form from "@/views/components/Form";
import {useAlertStore} from "@/stores/alert";
import Toggle from "@/views/components/input/Toggle.vue";
import {QuillEditor} from "@vueup/vue-quill";
import '@vueup/vue-quill/dist/vue-quill.snow.css';

/// file pond
import vueFilePond from 'vue-filepond';

// Import plugins
import FilePondPluginFileValidateType
  from 'filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.esm.js';
import FilePondPluginImagePreview from 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.esm.js';
import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size';

// Import styles
import 'filepond/dist/filepond.min.css';
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css';
import QuillEditorWrapper from "@/views/components/QuillEditorWrapper.vue";
import CkEditorCustom from "@/views/components/CkEditorCustom.vue";


// Create FilePond component
const FilePond = vueFilePond(FilePondPluginFileValidateType, FilePondPluginImagePreview, FilePondPluginFileValidateSize);

let pondElement = ref(null);
const filePondKey = ref('file-pond');
const maxFileSize = '5MB';
const acceptedFileTypes = 'image/*';


const alertStore = useAlertStore();
const form = reactive({
  title: '',
  slug: '',
  image: '',
  content: '',
  group_id: null,
  is_active: false,
  is_hot: false,
  is_new: false,
  view: 0,
  meta_title: null,
  meta_description: null,
  meta_key: null,
});

const group = ref();

const options = ref({
  debug: 'info',
  toolbar: 'full',
  theme: 'snow',
  contentType: 'html',
});

const page = reactive({
  id: 'create_posts',
  title: trans('global.pages.posts_create'),
  filters: false,
  breadcrumbs: [
    {
      name: trans('global.pages.posts'),
      to: toUrl('/posts/list'),

    },
    {
      name: trans('global.pages.posts_create'),
      active: true,
    }
  ],
  actions: [
    {
      id: 'back',
      name: trans('global.buttons.back'),
      icon: "fa fa-angle-left",
      to: toUrl('/posts/list'),
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

const service = new PostService();

function onAction(data) {
  switch (data.action.id) {
    case 'submit':
      onSubmit();
      break;
  }
}

function onSubmit() {
  if (group.value) {
    form.group_id = group.value.id
  }
  service.handleCreate('create-post', reduceProperties(form, 'roles', 'id')).then(() => {
    if (alertStore.type == 'success') {
      clearObject(form)
      group.value = null
    }
  })
  return false;
}

function getImage(event) {
  form.image = pondElement.value.getFile().file;
  filePondKey.value = pondElement.value.getFile().file.lastModified;
}

function removeImage() {
  form.image = null;
}

function updateData(value) {
  form.content = value
}

watch(() => group.value, (data) => {
  if (group.value) {
    form.group_id = group.value.id
  } else {
    form.group_id = null
  }
});
</script>

<style scoped>

</style>
<style>
.ql-editor {
  min-height: 200px;
}
</style>
