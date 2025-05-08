<template>
  <Page :title="page.title" :breadcrumbs="page.breadcrumbs" :actions="page.actions" @action="onAction"
        :is-loading="page.loading">
    <Panel>
      <Form id="edit-category" @submit.prevent="onSubmit">
<<<<<<< HEAD
        <TextInput class="mb-4" type="text" :required="true" error-input="name" name="name" v-model="form.name"
                   :label="trans('labels.name')"/>
        <TextInput class="mb-4" type="text" :required="true" error-input="slug" name="slug" v-model="form.slug"
                   label="Slug"/>
        <TextInput class="mb-4" type="number" min="1" max="1000" error-input="order" name="order" v-model="form.order"
                   label="Thứ tự ưu tiên"/>
        <TextInput class="mb-4" type="text" :disabled="true" name="code" v-model="item.code"
                   :label="trans('labels.code')"/>
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
                :files="myFile"
            />
            <span v-if="alertStore.errors['image']" class="text-xs tracking-wide text-red-600">{{
                alertStore.errors['image'][0]
              }}</span>
          </div>
        </div>
        <Dropdown class="mb-4" name="category" error-input="parent_id" :multiple="true"
                  server="categories" :label="trans('labels.categories')" :placeholder="trans('labels.categories')"
                  :server-search-min-characters="0"
                  :params="'&category_id='+ item.id"
                  v-model="category"></Dropdown>
        <TextInput class="mb-4" type="textarea" :rows="5" name="description" v-model="form.description"
                   error-input="description" :label="trans('labels.description')"/>
        <TextInput class="mb-4" type="text" :required="true" error-input="meta_title" name="name"
                   v-model="form.meta_title"
                   label="Meta title"/>
        <TextInput class="mb-4" type="textarea" :required="true" :rows="5" name="meta_description"
                   v-model="form.meta_description"
                   error-input="meta_description" label="Meta description"/>
        <TextInput class="mb-4" type="text" error-input="meta_key" name="name" v-model="form.meta_key"
                   label="Meta key"/>
        <div class="mb-4">
          <CkEditorCustom :content="form.content_seo" @updateData="(value) => updateData(value)" />
          <span v-if="alertStore.errors['content_seo']" class="text-xs tracking-wide text-red-600">{{
              alertStore.errors['content_seo'][0]
            }}</span>
        </div>
        <Toggle class="mb-4" v-model="form.show_header" :checked="form.show_header" error-input="show_header"
                label="Hiển thị ở header" name="show_header"/>
        <Toggle class="mb-4" v-model="form.show_dashboard" :checked="form.show_dashboard" error-input="show_dashboard"
                label="Hiển thị ở trang chủ" name="show_dashboard"/>
        <Toggle class="mb-4" v-model="form.is_active" :checked="form.is_active" error-input="is_active"
                label="Hiển thị" name="is_active"/>
=======
        <Tab :tabs="tabs" @set-index="updateTabIndex" :active-index="activeTab">
          <div v-show="activeTab === 0">
            <TextInput class="mb-4" type="text" :disabled="true" name="code" v-model="item.code"
                       :label="trans('labels.code')"/>
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
                    :files="myFile"
                />
                <span v-if="alertStore.errors['image']" class="text-xs tracking-wide text-red-600">{{
                    alertStore.errors['image'][0]
                  }}</span>
              </div>
            </div>
            <Dropdown class="mb-4" name="category" error-input="parent_id" :multiple="true"
                      server="categories" :label="trans('labels.categories')" :placeholder="trans('labels.categories')"
                      :server-search-min-characters="0"
                      :params="'&category_id='+ item.id"
                      v-model="category"></Dropdown>

            <Toggle class="mb-4" v-model="form.show_header" :checked="form.show_header" error-input="show_header"
                    label="Hiển thị ở header" name="show_header"/>
            <Toggle class="mb-4" v-model="form.show_dashboard" :checked="form.show_dashboard"
                    error-input="show_dashboard"
                    label="Hiển thị ở trang chủ" name="show_dashboard"/>
            <Toggle class="mb-4" v-model="form.is_active" :checked="form.is_active" error-input="is_active"
                    label="Hiển thị" name="is_active"/>
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
            <TextInput class="mb-4" type="text" error-input="meta_key" name="name" v-model="form.meta_key"
                       label="Meta key"/>
          </div>
          <div v-show="activeTab === 2">
            <TextInput class="mb-4" type="text" error-input="name_en" name="name_en" v-model="form.name_en"
                       :label="trans('labels.name')"/>
            <TextInput class="mb-4" type="text" error-input="slug_en" name="slug_en" v-model="form.slug_en"
                       label="Slug"/>

            <TextInput class="mb-4" type="textarea" :rows="5" name="description_en" v-model="form.description_en"
                       error-input="description_en" :label="trans('labels.description')"/>
            <TextInput class="mb-4" type="text" error-input="meta_title_en" name="meta_title_en"
                       v-model="form.meta_title_en"
                       label="Meta title"/>
            <TextInput class="mb-4" type="textarea" :rows="5" name="meta_description_en"
                       v-model="form.meta_description_en"
                       error-input="meta_description_en" label="Meta description"/>
            <TextInput class="mb-4" type="text" error-input="meta_key_en" name="meta_key_en" v-model="form.meta_key_en"
                       label="Meta key"/>
          </div>
        </Tab>
>>>>>>> 2807034678122ce1cd4c00da686b57b2f5806f16
      </Form>
    </Panel>
  </Page>
</template>

<script setup>
import {computed, onBeforeMount, reactive, ref} from "vue";
import {trans} from "@/helpers/i18n";
import {clearObject, fillObject, reduceProperties} from "@/helpers/data"
import {useRoute} from "vue-router";
import {toUrl} from "@/helpers/routing";
import CategoryService from "@/services/CategoryService";
import Button from "@/views/components/input/Button";
import TextInput from "@/views/components/input/TextInput";
import Dropdown from "@/views/components/input/Dropdown";
import Alert from "@/views/components/Alert";
import Panel from "@/views/components/Panel";
import Page from "@/views/layouts/Page";
import FileInput from "@/views/components/input/FileInput";
import Form from "@/views/components/Form";
import {useAlertStore} from "@/stores";

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
import Toggle from "@/views/components/input/Toggle.vue";
<<<<<<< HEAD
import CkEditorCustom from "@/views/components/CkEditorCustom.vue";
=======
import Tab from "@/views/components/Tab.vue";
>>>>>>> 2807034678122ce1cd4c00da686b57b2f5806f16


// Create FilePond component
const FilePond = vueFilePond(FilePondPluginFileValidateType, FilePondPluginImagePreview, FilePondPluginFileValidateSize);
const filePondKey = ref('file-pond');
const maxFileSize = '5MB';
const acceptedFileTypes = 'image/*';
let pondElement = ref(null);

computed(() => pondElement.value);


const route = useRoute();
const item = ref(null);
const file = ref(null);
const isFirstLoad = ref(false);
const alertStore = useAlertStore();
const form = reactive({
  name: '',
  name_en: '',
  image: '',
  description: '',
  description_en: '',
  remove_image: false,
  meta_title: '',
  meta_title_en: '',
  meta_key: '',
  meta_key_en: '',
  meta_description: '',
  meta_description_en: '',
  show_header: false,
  show_dashboard: false,
  is_active: false,
  order: 1000,
  slug: '',
<<<<<<< HEAD
  content_seo: '',
=======
  slug_en: '',
>>>>>>> 2807034678122ce1cd4c00da686b57b2f5806f16
});

const myFile = ref(null);
const category = ref(null);

const page = reactive({
  id: 'edit_user',
  title: trans('global.pages.categories_edit'),
  filters: false,
  loading: true,
  breadcrumbs: [
    {
      name: trans('global.pages.categories'),
      to: toUrl('/categories/list'),
    },
    {
      name: trans('global.pages.categories_edit'),
      active: true,
    }
  ],
  actions: [
    {
      id: 'back',
      name: trans('global.buttons.back'),
      icon: "fa fa-angle-left",
      to: toUrl('/categories/list'),
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

const tabs = ref([
  {title: 'Thông tin chung', content: '<p>Content for Tab 1</p>'},
  {title: 'Tiếng Việt', content: '<p>Content for Tab 2</p>'},
  {title: 'Tiếng Anh', content: '<p>Content for Tab 3</p>'},
]);

const activeTab = ref(0)

function updateTabIndex(index) {
  activeTab.value = index
}

const service = new CategoryService();

onBeforeMount(() => {
  service.edit(route.params.id).then((response) => {
    fillObject(form, response.data.model);
    form.image = null
    item.value = response.data.model;
    myFile.value = response.data.model.image_url;
    if (response.data.model.parent_id) {
      category.value = {
        'id': response.data.model.parent_id,
        'title': response.data.model.parent_name
      };
    }
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
  if (category.value) {
    form.parent_id = category.value.id
  }
  service.handleUpdate('edit-category', route.params.id, reduceProperties(form, 'roles', 'id')).then((response) => {
    if (alertStore.type == 'success') {
      clearObject(form)
      fillObject(form, response.data.model);
      item.value = response.data.model;
      myFile.value = response.data.model.image_url;
      if (response.data.model.parent_id) {
        category.value = {
          'id': response.data.model.parent_id,
          'title': response.data.model.parent_name
        };
      }
      form.image = null
      file.value = null;
      page.loading = false;
      form.remove_image = false;
    }
    isFirstLoad.value = false;
  });
  return false;
}

function getImage(event) {
  if (pondElement.value.getFile().file instanceof File == true) {
    form.image = pondElement.value.getFile().file;
    form.remove_image = false;
  }
  filePondKey.value = pondElement.value.getFile().file.lastModified;
  isFirstLoad.value = true
}

function removeImage() {
  form.image = null;
  form.remove_image = true;
  myFile.value = null;
}

function updateData(value) {
  form.content_seo = value
}
</script>

<style scoped>

</style>
