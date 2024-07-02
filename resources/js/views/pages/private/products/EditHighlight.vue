<template>
  <div>
    <Form id="create-review" @submit.prevent="onSubmit">
      <div class="p-6 space-y-6">
        <div class="mb-4">
          <CkEditorCustom :content="form.highlight" @updateData="(value) => updateData(value)" />
          <span v-if="alertStore.errors['highlight']" class="text-xs tracking-wide text-red-600">{{
              alertStore.errors['highlight'][0]
            }}</span>
        </div>
        <div class="w-[700px]">
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
              allow-multiple="false"
              v-on:addfile="getImage"
              v-on:removefile="removeImage"
              v-bind:files="images"
          />
          <span v-if="alertStore.errors['highlight_image']" class="text-xs tracking-wide text-red-600">{{
              alertStore.errors['highlight_image'][0]
            }}</span>
        </div>
      </div>


      <div class="flex flex-row justify-start px-6 py-4 bg-gray-100 text-right gap-3">
        <SecondaryButton @click="closeModal">
          Huỷ
        </SecondaryButton>

        <Button @click.prevent="onSubmit" title="Cập nhật" icon="fa fa-save" label="Cập nhật"
                :classes="{'opacity-25': processing }"
                :disabled="processing"/>
      </div>

    </Form>
  </div>
</template>

<script setup>
import {defineComponent, onBeforeMount, reactive, ref, watch} from "vue";
import {trans} from "@/helpers/i18n";
import Datepicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
import TextInput from "@/views/components/input/TextInput";
import {clearObject, fillObject, reduceProperties} from "@/helpers/data";
import {toUrl} from "@/helpers/routing";
import Form from "@/views/components/Form";
import {useAlertStore} from "@/stores/alert";
import productService from "@/services/ProductService";
import Dropdown from "@/views/components/input/Dropdown.vue";
import Vue3starRatings from "vue3-star-ratings";
import Button from "@/views/components/input/Button.vue";
import DialogModal from "@/views/components/DialogModal.vue";
import SecondaryButton from "@/views/components/SecondaryButton.vue";

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
import CkEditorCustom from "@/views/components/CkEditorCustom.vue";

// Create FilePond component
const FilePond = vueFilePond(FilePondPluginFileValidateType, FilePondPluginImagePreview, FilePondPluginFileValidateSize);

const emit = defineEmits(['confirmed', 'close']);
const props = defineProps({
  id: {
    type: String,
  },
  button: {
    type: String,
    default: 'Cập nhật',
  },
  open: {
    type: Boolean,
    default: false,
  },
});

const alertStore = useAlertStore();

const open = ref(false);
const processing = ref(false);
const images = ref([]);
const form = reactive({
  highlight: null
});

const file = ref(null)
const image = ref();
let pondElement = ref(null);
let pondElementThumb = ref(null);
const filePondKey = ref('file-pond');
const maxFileSize = '5MB';
const acceptedFileTypes = 'image/*';

const service = new productService();

function onSubmit() {
  processing.value = true
  service.handleUpdate('create-review', `${props.id}/highlight`, reduceProperties(form, 'roles', 'id')).then((response) => {
    if (alertStore.type == 'success') {
      form.highlight = response.data.model.highlight
      images.value = response.data.model.highlight_image_url
    }
  })
  processing.value = false
  return false;
}

function clearData() {
  form.highlight = null
  processing.value = false
  form.highlight_image = null
}

const closeModal = () => {
  emit('close')
  clearData();
};

function getImage(event) {
  if (pondElement.value.getFile().file instanceof File == true) {
    form.highlight_image = pondElement.value.getFile().file;
  }
  filePondKey.value = pondElement.value.getFile().file.lastModified;
}

function removeImage() {
  form.highlight_image = null;
  form.highlight_image_remove = true;
  image.value = null;
}

function updateData(value) {
  form.highlight = value
}

onBeforeMount(() => {
  service.edit(props.id).then((response) => {
    form.highlight = response.data.model.highlight
    images.value = response.data.model.highlight_image_url
  })
});
</script>

<style scoped>

</style>
