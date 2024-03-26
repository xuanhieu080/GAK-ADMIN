<template>
  <div id="edit-product">
    <Form>
      <TextInput class="mb-4" type="text" :required="true" error-input="name" name="name" v-model="form.name"
                 :label="trans('labels.name')"/>
      <TextInput class="mb-4" type="text" :required="true" error-input="slug" name="slug" v-model="form.slug"
                 label="Slug"/>
      <TextInput class="mb-4" type="url" error-input="video_link" name="video_link" v-model="form.video_link"
                 label="Đường dẫn video"/>
      <Dropdown class="mb-4" name="group" error-input="group_id" :multiple="false"
                server="groups" :label="trans('Nhóm bài viết')" :placeholder="trans('Nhóm bài viết')"
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
              v-bind:files="images"
          />
          <span v-if="alertStore.errors['image']" class="text-xs tracking-wide text-red-600">{{
              alertStore.errors['image'][0]
            }}</span>
        </div>
      </div>
      <div class="mb-4">
        <QuillEditorWrapper
            v-model:content="form.description"
            :toolbar="'full'"
            contentType="html"
            :placeholder="trans('labels.description')"
            :required="true"
        />
        <span v-if="alertStore.errors['description']" class="text-xs tracking-wide text-red-600">{{
            alertStore.errors['description'][0]
          }}</span>
      </div>
      <Dropdown class="mb-4" name="category" error-input="category_id" :required="true" :multiple="true"
                server="categories" :label="trans('labels.categories')" :placeholder="trans('labels.categories')"
                :server-search-min-characters="0" v-model="category"></Dropdown>
      <TextInput class="mb-4" type="number" :min="0" :max="999999999999" name="price" v-model="form.price"
                 error-input="price" :label="trans('labels.price')"/>
      <TextInput class="mb-4" type="number" :min="0" :max="10000" name="priority" v-model="form.priority"
                 error-input="priority" :label="trans('labels.priority')"/>

      <TextInput type="textarea" class="mb-4" :minlength="0" :maxlength="200"
                 :rows="1" name="meta_title" v-model="form.meta_title"
                 :required="true"
                 error-input="meta_title" label="SEO tiêu đề"/>
      <TextInput type="textarea" class="mb-4" :minlength="0" :maxlength="300"
                 :rows="5" name="meta_description" v-model="form.meta_description"
                 :required="true"
                 error-input="meta_description" label="SEO nội dung"/>
      <TextInput type="textarea" class="mb-4" :minlength="0" :maxlength="200"
                 :rows="3" name="meta_key" v-model="form.meta_key"
                 :required="true"
                 error-input="meta_key" label="SEO từ khoá"/>

      <div class="flex justify-center">
        <div class="w-[700px]">
          <FilePond
              ref="pondElementThumb"
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
              allow-multiple="true"
              max-files="10"
              v-on:addfile="getThumbImage"
              v-on:removefile="removeThumbImage"
              v-bind:files="thumbImage"
          />
          <span v-if="alertStore.errors['thumb_image']" class="text-xs tracking-wide text-red-600">{{
              alertStore.errors['thumb_image'][0]
            }}</span>
        </div>
      </div>
      <Toggle class="mb-4" v-model="form.is_active" :checked="form.is_active" error-input="is_active"
              :label="trans('labels.show')" name="status"/>

      <Button @click.prevent="onSubmit" title="Cập nhật" icon="fa fa-save" label="Cập nhật"/>
    </Form>
  </div>
</template>

<script setup>
import {defineComponent, reactive, ref, watch, computed, defineProps, onBeforeMount} from "vue";
import {trans} from "@/helpers/i18n";
import Button from "@/views/components/input/Button";
import TextInput from "@/views/components/input/TextInput";
import Dropdown from "@/views/components/input/Dropdown";
import Alert from "@/views/components/Alert";
import Panel from "@/views/components/Panel";
import Page from "@/views/layouts/Page";
import FileInput from "@/views/components/input/FileInput";
import Form from "@/views/components/Form";
import {useAlertStore} from "@/stores/alert";
import Toggle from "@/views/components/input/Toggle.vue";
import Spinner from "@/views/components/icons/Spinner.vue";
import Tab from "@/views/components/Tab.vue";
import {QuillEditor} from "@vueup/vue-quill";
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import {clearObject, fillObject, reduceProperties} from "@/helpers/data";

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


// Create FilePond component
const FilePond = vueFilePond(FilePondPluginFileValidateType, FilePondPluginImagePreview, FilePondPluginFileValidateSize);


import ProductService from "@/services/ProductService";

const emit = defineEmits(['informationUpdate']);
const alertStore = useAlertStore();
const category = ref(null)
const file = ref(null)
let pondElement = ref(null);
let pondElementThumb = ref(null);
const addFile = ref(0);
const filePondKey = ref('file-pond');
const maxFileSize = '5MB';
const acceptedFileTypes = 'image/*';

const isFirstLoad = ref(false);

const item = ref();

const form = reactive({
  name: null,
  image: null,
  thumb_image: [],
  slug: null,
  meta_title: null,
  meta_description: null,
  meta_key: null,
  description: null,
  category_id: null,
  video_link: null,
  price: 0,
  priority: 100,
  is_active: false,
  thumb_image_remove: [],
});

const props = defineProps({
  id: {
    type: String,
  },
});

const group = ref(null);
const image = ref();
const images = ref([]);
const thumbImage = ref([]);
const options = ref({
  debug: 'info',
  toolbar: 'full',
  theme: 'snow',
  contentType: 'html',
});

const service = new ProductService();

onBeforeMount(() => {
  service.edit(props.id).then((response) => {
    fillObject(form, response.data.model);
    item.value = response.data.model;
    images.value = item.value.image_url
    thumbImage.value = item.value.thumb_image
    form.image = null
    if (response.data.model.category_id) {
      category.value = {
        'id': response.data.model.category_id,
        'title': response.data.model.category_name
      };
    }
    isFirstLoad.value = false
  })
});

function getImage(event) {
  if (pondElement.value.getFile().file instanceof File == true) {
    form.image = pondElement.value.getFile().file;
  }
  image.value = pondElement.value.getFile().file;
  filePondKey.value = pondElement.value.getFile().file.lastModified;
}

function removeImage() {
  form.image = null;
  form.file = null;
  image.value = null;
}

function getThumbImage() {
  addFile.value++;
}

function removeThumbImage(event, file) {
  if (file.file instanceof File == false) {
    form.thumb_image_remove.push(file.file.name)
  }
  addFile.value--;
}

watch(() => category.value, (data) => {
  if (category.value) {
    form.category_id = category.value.id
  } else {
    form.category_id = null
  }
});

watch(() => addFile.value, (data) => {
  form.thumb_image = [];
  pondElementThumb.value.getFiles().forEach(function (file) {
    if (file.file instanceof File == true) {
      form.thumb_image.push(file.file)
    }
  })
});

function onSubmit() {
  if (category.value && category.value.id) {
    form.category_id = category.value.id;
  }
  service.handleUpdate('edit-product', props.id, reduceProperties(form, 'roles', 'id')).then((response) => {
    if (alertStore.type == 'success') {
      // fillObject(form, response.data.model);
      // item.value = response.data.model;
      // images.value = item.value.image_url
      // thumbImage.value = item.value.thumb_image
      // form.image = null
      // if (response.data.model.category_id) {
      //   category.value = {
      //     'id': response.data.model.category_id,
      //     'title': response.data.model.category_name
      //   };
      // }
    }
  });
  return false;
}
</script>

<style scoped lang="scss">
#create-product {
  .product-image {
    max-width: 2000px;
  }
}
</style>
<style lang="scss">
.filepond--root {
  //width:370px;
  //margin: 0 auto;
}

.filepond--drop-label {
  color: #4c4e53;
}

.filepond--label-action {
  text-decoration-color: #babdc0;
}

.filepond--panel-root {
  background-color: #edf0f4;
}
</style>
