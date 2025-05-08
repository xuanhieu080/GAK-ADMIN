<template>
  <Page :title="page.title" :breadcrumbs="page.breadcrumbs" :actions="page.actions" @action="onAction">
    <Panel>
      <Form id="create- post-group" @submit.prevent="onSubmit">
        <Tab :tabs="tabs" @set-index="updateTabIndex" :active-index="activeTab">
          <div v-show="activeTab === 0">
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
            <Toggle class="mb-4" v-model="form.is_hot" :checked="form.is_hot" error-input="is_hot" name="is_hot"
                    label="Nổi bật"/>
            <Toggle class="mb-4" v-model="form.is_active" :checked="form.is_active" error-input="is_active"
                    name="is_active"
                    :label="trans('labels.show')"/>
          </div>
          <div v-show="activeTab === 1">
            <TextInput class="mb-4" type="text" :required="true" error-input="name" name="name" v-model="form.name"
                       :label="trans('labels.name')"/>
            <TextInput class="mb-4" type="text" :required="true" error-input="slug" name="slug" v-model="form.slug"
                       label="Slug"/>
            <TextInput class="mb-4" type="textarea" :rows="5" name="description" v-model="form.description"
                       error-input="description" :label="trans('labels.description')"/>
            <TextInput class="mb-4" type="text" :required="true" error-input="meta_title" name="meta_title"
                       v-model="form.meta_title"
                       label="Meta title"/>
            <TextInput class="mb-4" type="textarea" :required="true" :rows="5" name="meta_description"
                       v-model="form.meta_description"
                       error-input="meta_description" label="Meta description"/>
            <TextInput class="mb-4" type="text" error-input="meta_key" name="meta_key" v-model="form.meta_key"
                       label="Meta key"/>
          </div>
          <div v-show="activeTab === 2">
            <TextInput class="mb-4" type="text" error-input="name_en" name="name_en" v-model="form.name_en"
                       label="Tên tiếng anh"/>
            <TextInput class="mb-4" type="text" error-input="slug_en" name="slug_en" v-model="form.slug_en"
                       label="Slug tiếng anh"/>
            <TextInput class="mb-4" type="textarea" :rows="5" name="description_en" v-model="form.description_en"
                       error-input="description_en" :label="trans('labels.description_en')"/>
            <TextInput class="mb-4" type="text" error-input="meta_title_en" name="meta_title_en"
                       v-model="form.meta_title_en"
                       label="Meta title tiếng anh"/>
            <TextInput class="mb-4" type="textarea" :rows="5" name="meta_description_en"
                       v-model="form.meta_description_en"
                       error-input="meta_description_en" label="Meta description tiếng anh"/>
            <TextInput class="mb-4" type="text" error-input="meta_key_en" name="meta_key_en" v-model="form.meta_key_en"
                       label="Meta key tiếng anh"/>
          </div>
        </Tab>
      </Form>
    </Panel>
  </Page>
</template>

<script setup>
import {reactive, ref, computed} from "vue";
import {trans} from "@/helpers/i18n";
import TextInput from "@/views/components/input/TextInput";
import Panel from "@/views/components/Panel";
import FileInput from "@/views/components/input/FileInput";
import {clearObject, reduceProperties} from "@/helpers/data";
import {toUrl} from "@/helpers/routing";
import Form from "@/views/components/Form";
import Page from "@/views/layouts/Page";
import {useAlertStore} from "@/stores/alert";


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
import Dropdown from "@/views/components/input/Dropdown.vue";
import Toggle from "@/views/components/input/Toggle.vue";
import PostGroupService from "@/services/PostGroupService";
import Tab from "@/views/components/Tab.vue";


// Create FilePond component
const FilePond = vueFilePond(FilePondPluginFileValidateType, FilePondPluginImagePreview, FilePondPluginFileValidateSize);
const filePondKey = ref('file-pond');
const maxFileSize = '5MB';
const acceptedFileTypes = 'image/*';
const file = ref(null)
let pondElement = ref(null);

computed(() => pondElement.value);


const alertStore = useAlertStore();
const form = reactive({
  name: '',
  name_en: '',
  image: '',
  description: '',
  description_en: '',
  meta_title: '',
  meta_title_en: '',
  meta_key: '',
  meta_key_en: '',
  meta_description: '',
  meta_description_en: '',
  is_active: false,
  is_hot: false,
  slug: '',
});

const page = reactive({
  id: 'create_post_group',
  title: 'Thêm mới',
  filters: false,
  breadcrumbs: [
    {
      name: 'Nhóm bài viết',
      to: toUrl('/post-groups/list'),

    },
    {
      name: 'Thêm mới',
      active: true,
    }
  ],
  actions: [
    {
      id: 'back',
      name: trans('global.buttons.back'),
      icon: "fa fa-angle-left",
      to: toUrl('/post-groups/list'),
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
const tabs = ref([
  {title: 'Thông tin chung', content: '<p>Content for Tab 1</p>'},
  {title: 'Tiếng Việt', content: '<p>Content for Tab 2</p>'},
  {title: 'Tiếng Anh', content: '<p>Content for Tab 3</p>'},
]);
const activeTab = ref(0)

function updateTabIndex(index) {
  activeTab.value = index
}

const service = new PostGroupService();

function onAction(data) {
  switch (data.action.id) {
    case 'submit':
      onSubmit();
      break;
  }
}

function onSubmit() {
  service.handleCreate('create- post-group', reduceProperties(form, 'roles', 'id')).then(() => {
    if (alertStore.type == 'success') {
      clearObject(form)
      form.is_active = false;
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
</script>

<style scoped>

</style>
