<template>
  <div id="create-product">
    <Form>
      <TextInput class="mb-4" type="text" :required="true" error-input="name" name="name" v-model="form.name"
                 :label="trans('labels.name')"/>
      <TextInput class="mb-4" type="text" :required="true" error-input="slug" name="slug" v-model="form.slug"
                 label="Slug"/>
<!--      <FileInput class="mb-4" name="file" v-model="form.image" error-input="file" accept="image/*"-->
<!--                 img-style="width:200px;height:200px"-->
<!--                 :label="trans('labels.avatar')" @clear="clearImage"></FileInput>-->

      <div>
        <file-pond
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
      <div class="mb-4">
        <QuillEditor
            v-model:content="form.description"
            :options="options"
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
      <Toggle class="mb-4" v-model="form.is_active" :checked="form.is_active" error-input="is_active"
              :label="trans('labels.show')" name="status"/>
    </Form>
  </div>
</template>

<script>
import {defineComponent, reactive, ref, watch, computed} from "vue";
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

import vueFilePond from 'vue-filepond';

// Import plugins
import FilePondPluginFileValidateType
  from 'filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.esm.js';
import FilePondPluginImagePreview from 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.esm.js';

// Import styles
import 'filepond/dist/filepond.min.css';
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css';
import {useGlobalStateStore} from "@/stores";

// Create FilePond component
const FilePond = vueFilePond(FilePondPluginFileValidateType, FilePondPluginImagePreview);

export default defineComponent({
  components: {
    QuillEditor,
    Spinner,
    Toggle,
    Form,
    FileInput,
    Panel,
    Alert,
    Dropdown,
    TextInput,
    Button,
    Page,
    Tab,
    FilePond
  },
  emits: ['informationUpdate'],
  setup(props, {emit}) {
    const alertStore = useAlertStore();
    const category = ref(null)
    const file = ref(null)
    let pondElement = ref(null);
    const filePondKey = ref('file-pond');
    const maxFileSize = '5MB';
    const acceptedFileTypes = 'image/*';
    const isElementLoading = computed(() => {
      return pondElement.value;
    });

    // computed(() => pondElement.value);

    const FilePond = vueFilePond(
        FilePondPluginFileValidateType,
        FilePondPluginImagePreview,
    );
    const form = reactive({
      name: null,
      image: null,
      slug: null,
      meta_title: null,
      meta_description: null,
      meta_key: null,
      description: null,
      category_id: null,
      price: 0,
      priority: 100,
      is_active: false,
    });

    const image = ref();

    const options = ref({
      debug: 'info',
      toolbar: 'full',
      theme: 'snow',
      contentType: 'html',
    });


    function clearImage() {
      form.image = null
      emit("informationUpdate", form);
    }

    function clearData() {
      file.value = null
      form.image = null
      category.value = null
      clearObject(form)
      emit("informationUpdate", form);
    }

    function getImage(event) {

      console.log(isElementLoading.value)
      // image.value = pondElement.value.getFile().file;
      // form.image = pondElement.value.getFile().file;
      // filePondKey.value = pondElement.value.getFile().file.lastModified;
    }

    function removeImage() {
      form.image = null;
      image.value = null;
    }

    watch(form, (data) => {
      emit("informationUpdate", data);
    });

    return {
      trans,
      form,
      category,
      file,
      getImage,
      maxFileSize,
      acceptedFileTypes,
      clearImage,
      removeImage,
      clearData,
      options,
      alertStore,
      FilePond,
    }
  }
})
</script>

<style scoped lang="scss">
#create-product {
  .product-image {
    max-width: 400px;
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
