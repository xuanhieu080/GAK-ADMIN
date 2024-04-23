<template>
  <div id="edit-product">
    <h3 class="mb-4">Biến thể - {{index}}</h3>
    <Form>
      <TextInput class="mb-4" type="text" :required="true" :error-input="'details.' + index + '.name'" :name="'name-' + index" v-model="form.name"
                 label="Tên biến thể"/>
      <TextInput class="mb-4" type="text" disabled  :name="'name-variant-' + index" v-model="optionName"
                 label="Biến thể"/>
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
<!--          <span v-if="alertStore.errors['image']" class="text-xs tracking-wide text-red-600">{{-->
<!--              alertStore.errors['image'][0]-->
<!--            }}</span>-->
        </div>
      </div>
<!--      <div class="mb-4">-->
<!--        <QuillEditorWrapper-->
<!--            v-model:content="form.description"-->
<!--            :toolbar="'full'"-->
<!--            contentType="html"-->
<!--            :placeholder="trans('labels.description')"-->
<!--            :required="true"-->
<!--        />-->
<!--        <span v-if="alertStore.errors[`details.${index}.description`]" class="text-xs tracking-wide text-red-600">{{-->
<!--            alertStore.errors[`details.${index}.description`][0]-->
<!--          }}</span>-->
<!--      </div>-->

      <TextInput class="mb-4" type="number" :min="0" :max="999999999999" name="price" v-model="form.price"
                 :error-input="'details.' + index + '.price'" :label="trans('labels.price')"/>
      <TextInput class="mb-4" type="number" :min="0" :max="100" name="ratio" v-model="ratio"
                 label="% giảm giá"/>
      <TextInput class="mb-4" type="number" :min="0" :max="999999999999" name="price-discount" v-model="form.discount"
                 :error-input="'details.' + index + '.discount'"
                 label="Tiền giảm giá"/>
      <TextInput class="mb-4" type="number" disabled :min="0" :max="999999999999" name="price-current" v-model="priceCurrent"
                 label="Giá sau khi đã trừ"/>
      <TextInput class="mb-4" type="number" :min="0" :max="100000" name="qty" v-model="form.qty"
                 :error-input="'details.' + index + '.qty'"
                 error-input="qty" label="Số lượng"/>


<!--      <TextInput type="textarea" class="mb-4" :minlength="0" :maxlength="200"-->
<!--                 :rows="1" name="meta_title" v-model="form.meta_title"-->
<!--                 :required="true"-->
<!--                 :error-input="'details.' + index + '.meta_detail'" label="SEO tiêu đề"/>-->
<!--      <TextInput type="textarea" class="mb-4" :minlength="0" :maxlength="300"-->
<!--                 :rows="5" name="meta_description" v-model="form.meta_description"-->
<!--                 :required="true"-->
<!--                 :error-input="'details.' + index + '.meta_description'" label="SEO nội dung"/>-->
<!--      <TextInput type="textarea" class="mb-4" :minlength="0" :maxlength="200"-->
<!--                 :rows="3" name="meta_key" v-model="form.meta_key"-->
<!--                 :required="true"-->
<!--                 :error-input="'details.' + index + '.meta_key'" label="SEO từ khoá"/>-->

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
          <span v-if="alertStore.errors[`details.${index}.thumb_image`]" class="text-xs tracking-wide text-red-600">{{
              alertStore.errors['thumb_image'][0]
            }}</span>
        </div>
      </div>
<!--      <Toggle v-model="form.is_active" :checked="form.is_active"-->
<!--              :error-input="'details.' + index + '.is_active'"-->
<!--              :label="trans('labels.show')" name="status"/>-->
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
const optionName = ref(null)
let pondElement = ref(null);
let pondElementThumb = ref(null);
const addFile = ref(0);
const filePondKey = ref('file-pond');
const maxFileSize = '5MB';
const acceptedFileTypes = 'image/*';

const isFirstLoad = ref(false);

const props = defineProps({
  item: {
    type: Object,
  },
  index: {
    type: Number,
    default: 0
  }
});

const ratio = ref(0);
const priceCurrent = ref(0);

const form = reactive({
  id: props.item.id,
  name: null,
  image: '',
  thumb_image: [],
  // slug: null,
  // meta_title: null,
  // meta_description: null,
  // meta_key: null,
  // description: null,
  // category_id: null,
  price: 0,
  discount: 0,
  qty: 0,
  // priority: 100,
  is_active: true,
  thumb_image_remove: [],
});

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

if (props.item) {
  form.name = props.item.name
  // form.meta_title = props.item.meta_title
  // form.meta_description = props.item.meta_description
  // form.meta_key = props.item.meta_key
  // form.description = props.item.description
  form.is_active = true
  // form.is_active = props.item.is_active
  form.price = props.item.price
  form.discount = props.item.discount
  form.qty = props.item.qty
  images.value = props.item.images
  thumbImage.value = props.item.thumb_image
  priceCurrent.value = props.item.price_discount
  optionName.value = props.item.option_name

  emit('informationUpdate', form)

  setTimeout(() => {
    isFirstLoad.value = true
  },5000)
}

// onBeforeMount(() => {
//   service.edit(props.id).then((response) => {
//     fillObject(form, response.data.model);
//     item.value = response.data.model;
//     images.value = item.value.image_url
//     thumbImage.value = item.value.thumb_image
//     form.image = null
//     if (response.data.model.category_id) {
//       category.value = {
//         'id': response.data.model.category_id,
//         'title': response.data.model.category_name
//       };
//     }
//     isFirstLoad.value = false
//   })
// });

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

watch(() => addFile.value, (data) => {
  form.thumb_image = [];
  pondElementThumb.value.getFiles().forEach(function (file) {
    if (file.file instanceof File == true) {
      form.thumb_image.push(file.file)
    }
  })
});

watch(form, (data) => {
  emit('informationUpdate', form)
})



watch(() => [form.price,ratio.value], (value) => {
  if (isFirstLoad.value) {
    form.discount = form.price * ratio.value / 100;
    priceCurrent.value = form.price - ratio.value * value / 100;
  }
});


watch(() => form.discount, (value) => {
  if (isFirstLoad.value) {
    priceCurrent.value = form.price - value ;
  }
});

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
