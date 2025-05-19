<template>
  <div id="edit-product">
    <h3 class="mb-4">Biến thể - {{index}}     --------- Mã: {{props.item.code}}</h3>
    <Form>
      <TextInput class="mb-4" type="text" :required="true" :error-input="'details.' + index + '.code'" :name="'code-' + index" v-model="form.code"
                 label="Mã sản phẩm"/>
      <TextInput class="mb-4" type="text" :required="true" :error-input="'details.' + index + '.name'" :name="'name-' + index" v-model="form.name"
                 label="Tên biến thể"/>
      <TextInput class="mb-4" type="text" disabled  :name="'name-variant-' + index" v-model="optionName"
                 label="Biến thể"/>
      <TextInput class="mb-4" type="text" :required="true" :error-input="'details.' + index + '.params'" :name="'params-' + index" v-model="form.params"
                 label="Param hiển thị (san-pham?param trong đó param:mau=do&...)"/>

      <TextInput class="mb-4" type="number" :min="0" :max="999999999999" name="price" v-model="form.price"
                 :error-input="'details.' + index + '.price'" :label="trans('labels.price')"/>
      <TextInput class="mb-4" type="number" :min="0" :max="100" name="ratio" v-model="ratio"
                 label="% giảm giá"/>
      <TextInput class="mb-4" type="number" :min="0" :max="999999999999" name="price-discount" v-model="form.discount"
                 :error-input="'details.' + index + '.discount'"
                 label="Tiền giảm giá"/>
      <TextInput class="mb-4" type="number" disabled :min="0" :max="999999999999" name="price-current" v-model="priceCurrent"
                 label="Giá sau khi đã trừ"/>

      <TextInput class="mb-4" type="number" :min="0" :max="999999999999" name="price_en" v-model="form.price_en"
                 :error-input="'details.' + index + '.price_en'" label="Giá tiền tiếng anh"/>
      <TextInput class="mb-4" type="number" :min="0" :max="100" name="ratio" v-model="ratio"
                 label="% giảm giá"/>
      <TextInput class="mb-4" type="number" :min="0" :max="999999999999" name="price_discount_en" v-model="form.discount_en"
                 :error-input="'details.' + index + '.discount_en'"
                 label="Tiền giảm giá tiếng anh"/>
      <TextInput class="mb-4" type="number" disabled :min="0" :max="999999999999" name="price_current_en" v-model="priceCurrentEn"
                 label="Giá sau khi đã trừ tiếng anh"/>
      <TextInput class="mb-4" type="number" :min="0" :max="100000" name="qty" v-model="form.qty"
                 :error-input="'details.' + index + '.qty'"
                 error-input="qty" label="Số lượng"/>
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
const priceCurrentEn = ref(0);

const form = reactive({
  id: props.item.id,
  name: null,
  code: null,
  image: '',
  thumb_image: [],
  // slug: null,
  meta_title: null,
  meta_description: null,
  meta_key: null,
  // description: null,
  // category_id: null,
  price: 0,
  price_en: 0,
  discount: 0,
  discount_en: 0,
  params: null,
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
  form.code = props.item.code
  form.meta_title = props.item.meta_title
  form.meta_description = props.item.meta_description
  form.meta_key = props.item.meta_key
  // form.description = props.item.description
  form.is_active = true
  // form.is_active = props.item.is_active
  form.price = props.item.price
  form.price_en = props.item.price_en
  form.discount = props.item.discount
  form.discount_en = props.item.discount_en
  form.qty = props.item.qty
  form.params = props.item.params
  images.value = props.item.images
  thumbImage.value = props.item.thumb_image
  priceCurrent.value = props.item.price_discount
  priceCurrentEn.value = props.item.price_discount_en
  optionName.value = props.item.option_name

  setTimeout(() => {
    isFirstLoad.value = true
  },5000)
}


watch(() => addFile.value, (data) => {
  form.thumb_image = [];
  pondElementThumb.value.getFiles().forEach(function (file) {
    if (file.file instanceof File == true) {
      form.thumb_image.push(file.file)
    }
  })
});

watch(() => ratio.value, (value) => {
  if (isFirstLoad.value) {
    form.discount = form.price * ratio.value / 100;
    priceCurrent.value = form.price - ratio.value * value / 100;
    form.discount_en = form.price_en * ratio.value / 100;
    priceCurrentEn.value = form.price_en - ratio.value * value / 100;
  }
});

watch(() => form.price, (value) => {
  if (isFirstLoad.value) {
    form.discount = form.price * ratio.value / 100;
    priceCurrent.value = form.price - ratio.value * value / 100;
  }
});
watch(() => form.price_en, (value) => {
  if (isFirstLoad.value) {
    form.discount_en = form.price_en * ratio.value / 100;
    priceCurrentEn.value = form.price_en - ratio.value * value / 100;
  }
});


watch(() => form.discount, (value) => {
  if (isFirstLoad.value) {
    priceCurrent.value = form.price - value ;
  }
});

watch(() => form.discount_en, (value) => {
  if (isFirstLoad.value) {
    priceCurrentEn.value = form.price_en - value ;
  }
});


function onSubmit() {
  service.handleUpdate('edit-product-variant', `product-variants/${props.item.id}`, reduceProperties(form, 'roles', 'id')).then((response) => {
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
