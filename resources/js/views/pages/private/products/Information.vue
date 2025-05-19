<template>
  <div id="create-product">
    <Form>
      <Tab :tabs="tabs" @set-index="updateTabIndex" :active-index="activeTab">
        <div v-show="activeTab === 0">
          <TextInput class="mb-4" type="url" error-input="video_link" name="video_link" v-model="form.video_link"
                     label="Đường dẫn video"/>
          <div class="flex justify-center">
            <div class="w-[500px]">
              <label>Hình Ảnh Chính</label>
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
          <Dropdown class="mb-4" name="category" error-input="category_id" :required="true" :multiple="true"
                    server="categories" :label="trans('labels.categories')" :placeholder="trans('labels.categories')"
                    :server-search-min-characters="0" v-model="category"></Dropdown>
          <TextInput class="mb-4" type="number" :min="0" :max="10000" name="priority" v-model="form.priority"
                     error-input="priority" :label="trans('labels.priority')"/>
          <TextInput class="mb-4" type="number" :min="0" :max="100000" name="qty" v-model="form.qty"
                     error-input="qty" label="Số lượng"/>


          <div class="flex justify-center">
            <div class="w-[700px]">
              <label>Hình Ảnh Khác</label>
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
                  v-bind:files="thumbImage"
              />
              <span v-if="alertStore.errors['thumb_image']" class="text-xs tracking-wide text-red-600">{{
                  alertStore.errors['thumb_image'][0]
                }}</span>
            </div>
          </div>
          <Toggle class="mb-4" v-model="form.is_hot" :checked="form.is_hot" error-input="is_hot"
                  label="Nổi bật" name="is_hot"/>
          <Toggle class="mb-4" v-model="form.is_upcoming" :checked="form.is_upcoming" error-input="is_upcoming"
                  label="Sắp ra mắt" name="is_upcoming"/>
          <Toggle class="mb-4" v-model="form.is_new" :checked="form.is_new" error-input="is_new"
                  label="Mới" name="is_new"/>
          <Toggle class="mb-4" v-model="form.is_uniform" :checked="form.is_uniform" error-input="is_uniform"
                  label="Đồng phục" name="is_uniform"/>
          <Toggle class="mb-4" v-model="form.is_active" :checked="form.is_active" error-input="is_active"
                  :label="trans('labels.show')" name="is_active"/>
        </div>
        <div v-show="activeTab === 1">
          <TextInput class="mb-4" type="text" :required="true" error-input="name" name="name" v-model="form.name"
                     :label="trans('labels.name')"/>
          <TextInput class="mb-4" type="text" :required="true" error-input="slug" name="slug" v-model="form.slug"
                     label="Slug"/>

          <TextInput class="mb-4" type="number" :min="0" :max="999999999999" name="price" v-model="form.price"
                     error-input="price" :label="trans('labels.price')"/>
          <TextInput class="mb-4" type="number" :min="0" :max="100" name="ratio" v-model="ratio"
                     label="% giảm giá"/>
          <TextInput class="mb-4" type="number" :min="0" :max="999999999999" name="price_discount"
                     v-model="form.discount"
                     label="Tiền giảm giá"/>
          <TextInput class="mb-4" type="number" :min="0" :max="999999999999" name="price_current" disabled
                     v-model="priceCurrent"
                     label="Giá sau khi đã trừ"/>

          <div class="mb-4">
            <CkEditorCustom :content="form.description" @updateData="(value) => updateData(value)"/>
            <span v-if="alertStore.errors['description']" class="text-xs tracking-wide text-red-600">{{
                alertStore.errors['description'][0]
              }}</span>
          </div>


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
        </div>
        <div v-show="activeTab === 2">
          <TextInput class="mb-4" type="text" error-input="name_en" name="name_en" v-model="form.name_en"
                     :label="trans('labels.name')"/>
          <TextInput class="mb-4" type="text" error-input="slug_en" name="slug_en" v-model="form.slug_en"
                     label="Slug"/>

          <TextInput class="mb-4" type="number" :min="0" :max="999999999999" name="price_en" v-model="form.price_en"
                     error-input="price_en" :label="trans('labels.price')"/>
          <TextInput class="mb-4" type="number" :min="0" :max="100" name="ratio" v-model="ratio"
                     label="% giảm giá"/>
          <TextInput class="mb-4" type="number" :min="0" :max="999999999999" name="price_discount_en"
                     v-model="form.discount_en"
                     label="Tiền giảm giá"/>
          <TextInput class="mb-4" type="number" :min="0" :max="999999999999" name="price-current-en" disabled
                     v-model="priceCurrentEn"
                     label="Giá sau khi đã trừ"/>

          <div class="mb-4">
            <CkEditorCustom :content="form.description_en" @updateData="(value) => updateDataEn(value)"/>
            <span v-if="alertStore.errors['description_en']" class="text-xs tracking-wide text-red-600">{{
                alertStore.errors['description_en'][0]
              }}</span>
          </div>

          <TextInput type="textarea" class="mb-4" :minlength="0" :maxlength="250"
                     :rows="1" name="meta_title_en" v-model="form.meta_title_en"
                     error-input="meta_title_en" label="SEO tiêu đề"/>
          <TextInput type="textarea" class="mb-4" :minlength="0" :maxlength="300"
                     :rows="5" name="meta_description_en" v-model="form.meta_description_en"
                     error-input="meta_description_en" label="SEO nội dung"/>
          <TextInput type="textarea" class="mb-4" :minlength="0" :maxlength="250"
                     :rows="3" name="meta_key_en" v-model="form.meta_key_en"
                     error-input="meta_key_en" label="SEO từ khoá"/>
        </div>
      </Tab>
    </Form>
  </div>
</template>

<script setup>
import {defineComponent, reactive, ref, watch, computed, defineProps} from "vue";
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
import {clearObject} from "@/helpers/data";

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
import CkEditorCustom from "@/views/components/CkEditorCustom.vue";


// Create FilePond component
const FilePond = vueFilePond(FilePondPluginFileValidateType, FilePondPluginImagePreview, FilePondPluginFileValidateSize);

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

const ratio = ref(0);
const priceCurrent = ref(0);
const priceCurrentEn = ref(0);

const form = reactive({
  name: null,
  name_en: null,
  image: null,
  thumb_image: [],
  slug: null,
  slug_en: null,
  meta_title: null,
  meta_title_en: null,
  meta_description: null,
  meta_description_en: null,
  meta_key: null,
  meta_key_en: null,
  description: null,
  description_en: null,
  category_id: null,
  price: 0,
  price_en: 0,
  qty: 100000,
  discount: 0,
  discount_en: 0,
  priority: 100,
  is_active: false,
  is_hot: false,
  is_upcoming: false,
  is_new: false,
  is_uniform: false,
  video_link: null
});

const props = defineProps({
  information: {
    type: Object,
  },
});

if (props.information) {
  form.name = props.information.name;
  form.name_en = props.information.name_en;
  form.file = props.information.file;
  form.description = props.information.description;
  form.description_en = props.information.description_en;
  form.category_id = props.information.category_id;
  form.price = props.information.price;
  form.price_en = props.information.price_en;
  form.discount = props.information.discount;
  form.discount_en = props.information.discount_en;
  form.priority = props.information.priority;
  form.is_active = props.information.is_active;
  form.is_hot = props.information.is_hot;
  form.is_uniform = props.information.is_uniform;
  form.is_new = props.information.is_new;
  form.is_upcoming = props.information.is_upcoming;
  form.meta_title = props.information.meta_title;
  form.meta_title_en = props.information.meta_title_en;
  form.meta_description = props.information.meta_description;
  form.meta_description_en = props.information.meta_description_en;
  form.meta_key = props.information.meta_key;
  form.meta_key_en = props.information.meta_key_en;
  form.slug = props.information.slug;
  form.slug_en = props.information.slug_en;
  form.video_link = props.information.video_link;
  form.qty = props.information.qty;
}

const image = ref();
const images = ref([]);
const thumbImage = ref([]);

const tabs = ref([
  {title: 'Thông tin chung', content: '<p>Content for Tab 1</p>'},
  {title: 'Tiếng Việt', content: '<p>Content for Tab 2</p>'},
  {title: 'Tiếng Anh', content: '<p>Content for Tab 3</p>'},
]);

const activeTab = ref(0)

function updateTabIndex(index) {
  activeTab.value = index
}

const options = ref({
  debug: 'info',
  toolbar: 'full',
  theme: 'snow',
  contentType: 'html',
});

function getImage(event) {
  image.value = pondElement.value.getFile().file;
  form.image = pondElement.value.getFile().file;
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

function removeThumbImage() {
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
    form.thumb_image.push(file.file)
  })
});

watch(form, (data) => {
  emit("informationUpdate", data);
});

watch(() => props.information, (data) => {
  form.name = props.information.name;
  form.name_en = props.information.name_en;
  form.file = props.information.file;
  form.image = props.information.image;
  form.thumb_image = props.information.thumb_image;
  form.description = props.information.description;
  form.description_en = props.information.description_en;
  form.category_id = props.information.category_id;
  form.price = props.information.price;
  form.price_en = props.information.price_en;
  form.discount = props.information.discount;
  form.discount_en = props.information.discount_en;
  form.priority = props.information.priority;
  form.is_active = props.information.is_active;
  form.is_hot = props.information.is_hot;
  form.is_uniform = props.information.is_uniform;
  form.is_new = props.information.is_new;
  form.is_upcoming = props.information.is_upcoming;
  form.meta_title = props.information.meta_title;
  form.meta_title_en = props.information.meta_title_en;
  form.slug = props.information.slug;
  form.slug_en = props.information.slug_en;
  form.meta_description = props.information.meta_description;
  form.meta_description_en = props.information.meta_description_en;
  form.meta_key = props.information.meta_key;
  form.meta_key_en = props.information.meta_key_en;
  form.video_link = props.information.video_link;
  form.qty = props.information.qty;
  if (!form.category_id) {
    category.value = null
  }
  if (!form.image) {
    images.value = []
  }
  if (!form.thumb_image) {
    thumbImage.value = []
  }
})

function updateData(value) {
  form.description = value
}

function updateDataEn(value) {
  form.description_en = value
}


watch(() => form.price, (value) => {
  form.discount = ratio.value * value / 100;
  priceCurrent.value = value - ratio.value * value / 100;
});
watch(() => form.price_en, (value) => {
  form.discount_en = ratio.value * value / 100;
  priceCurrentEn.value = value - ratio.value * value / 100;
});

watch(() => ratio.value, (value) => {
  form.discount = form.price * value / 100;
  priceCurrent.value = form.price - form.price * value / 100;
  form.discount_en = form.price_en * value / 100;
  priceCurrentEn.value = form.price_en - form.price_en * value / 100;
});

watch(() => form.discount, (value) => {
  priceCurrent.value = form.price - value;
});

watch(() => form.discount_en, (value) => {
  priceCurrentEn.value = form.price_en - value;
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
