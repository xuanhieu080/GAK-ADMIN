<template>
  <DialogModal :show="open" @close="closeModal">
    <template #title>
      <div class="flex justify-between items-start p-4 rounded-t border-b">
        <h3 class="text-xl font-semibold text-gray-900">
          Thêm review
        </h3>
        <button
            @click="closeModal"
            type="button"
            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center"
            data-modal-toggle="defaultModal">
          <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
               xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd"
                  d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                  clip-rule="evenodd"></path>
          </svg>
          <span class="sr-only">Đóng</span>
        </button>
      </div>
    </template>

    <template #content>
      <div>
        <Form id="create-review" @submit.prevent="onSubmit">
          <div class="p-6 space-y-6">
            <TextInput class="mb-4" :required="true" name="customer_name" v-model="form.customer_name"
                       error-input="customer_name"
                       label="Tên khách hàng"/>
            <Dropdown class="mb-4" name="product_variant_id" error-input="product_variant_id"
                      label="Biến thể" placeholder="Biến thể"
                      :options="productVariants"
                      :server-search-min-characters="0" v-model="productVariant"></Dropdown>
            <TextInput class="mb-4" type="number" :step="0.1" min="1" max="5" :required="true" name="rate"
                       v-model="form.rate"
                       error-input="rate"
                       label="Điểm"/>
            <div class="mb-4">
              <Vue3starRatings
                  v-model="form.rate"
                  :starSize="32"
                  starColor="green"
                  inactiveColor="#ddd"
                  :numberOfStars="5"
                  :disableClick="false"
              />
            </div>
            <TextInput class="mb-4" type="textarea" :required="true" name="description" v-model="form.description"
                       error-input="description"
                       label="Nội dung"/>
            <TextInput class="mb-4" type="textarea" name="reply" v-model="form.reply" error-input="reply"
                       label="Trả lời"/>
            <div class="md:mt-0 w-full md:mx-1">
              <label class="text-sm text-gray-500">
                Thời gian<span class="text-red-600">*</span>
              </label>
              <Datepicker v-model="form.dateTime"
                          :dayNames="['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN']"
                          :maxDate="new Date()"
                          :format="format"
                          :text-input="true"
                          cancelText="Huỷ"
                          required
                          selectText="Chọn"
              />
            </div>
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
                    max-files="50"
                    v-on:addfile="getThumbImage"
                    v-on:removefile="removeThumbImage"
                />
                <span v-if="alertStore.errors['thumb_image']" class="text-xs tracking-wide text-red-600">{{
                    alertStore.errors['thumb_image'][0]
                  }}</span>
              </div>
            </div>
          </div>


          <div class="flex flex-row justify-end px-6 py-4 bg-gray-100 text-right gap-3">
          <SecondaryButton @click="closeModal">
            Huỷ
          </SecondaryButton>

            <Button @click.prevent="onSubmit" title="Thêm" icon="fa fa-save" label="Thêm"
                    :classes="{'opacity-25': processing }"
                    :disabled="processing"/>
      </div>

        </Form>
      </div>
    </template>
  </DialogModal>
</template>

<script setup>
import {defineComponent, onBeforeMount, reactive, ref, watch} from "vue";
import {trans} from "@/helpers/i18n";
import Datepicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
import TextInput from "@/views/components/input/TextInput";
import {clearObject, reduceProperties} from "@/helpers/data";
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

// Create FilePond component
const FilePond = vueFilePond(FilePondPluginFileValidateType, FilePondPluginImagePreview, FilePondPluginFileValidateSize);

const emit = defineEmits(['confirmed', 'close']);
const props = defineProps({
  id: {
    type: String,
  },
  title: {
    type: String,
    default: 'Tạo review',
  },
  content: {
    type: String,
    default: 'Nội dung',
  },
  button: {
    type: String,
    default: 'Thêm',
  },
  open: {
    type: Boolean,
    default: false,
  },
});

const alertStore = useAlertStore();

const open = ref(false);
const processing = ref(false);
const form = reactive({
  customer_name: null,
  product_variant_id: null,
  rate: 5,
  description: null,
  reply: null,
  dateTime: new Date(),
});
const productVariant = ref();
const productVariants = ref([]);
const reviews = ref([]);

const file = ref(null)
let pondElement = ref(null);
let pondElementThumb = ref(null);
const addFile = ref(0);
const filePondKey = ref('file-pond');
const maxFileSize = '5MB';
const acceptedFileTypes = 'image/*';

const service = new productService();

const format = (date) => {
  return date.getDate().toString().padStart(2, '0') + '/' + (date.getMonth() + 1).toString().padStart(2, '0') + '/' + date.getFullYear().toString().padStart(2, '0');
}

const formatSubmit = (date) => {
  return date.getFullYear().toString().padStart(2, '0') + '-' + (date.getMonth() + 1).toString().padStart(2, '0') + '-' + date.getDate().toString().padStart(2, '0') ;
}


function onSubmit() {
  processing.value = true
  if (productVariant.value) {
    form.product_variant_id = productVariant.value.id
  }
  form.date = formatSubmit(form.dateTime)
  service.handleCreateCustom('create-review',`${props.id}/reviews`, reduceProperties(form, 'roles', 'id')).then(() => {
    if (alertStore.type == 'success') {
      clearObject(form)
      clearData();
      emit('confirmed')
    }
  })
  processing.value = false
  return false;
}

function clearData() {
  form.customer_name = null
  form.product_variant_id = null
  form.rate = 5
  form.reply = null
  form.description = null
  form.dateTime = new Date()
  productVariant.value = null

  processing.value = false
  open.value = false
}

const closeModal = () => {
  emit('close')
  clearData();
};

function getThumbImage() {
  addFile.value++;
}

function removeThumbImage() {
  addFile.value--;
}

watch(
    () => props.open,
    async () => {
      open.value = props.open;
    },
);

watch(() => addFile.value, (data) => {
  form.thumb_image = [];
  pondElementThumb.value.getFiles().forEach(function (file) {
    form.thumb_image.push(file.file)
  })
});

onBeforeMount(() => {
  service.find(`${props.id}/variants`).then((response) => {
    productVariants.value = response.data.model.variants;
  })
});
</script>

<style scoped>

</style>
