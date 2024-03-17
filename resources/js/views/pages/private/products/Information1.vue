<template>
  <div>
    <Form id="create-category">
      <TextInput class="mb-4" type="text" :required="true" error-input="name" name="name" v-model="form.name"
                 :label="trans('labels.name')"/>
      <FileInput class="mb-4" name="file" v-model="file" error-input="file" accept="image/*"
                 :label="trans('labels.avatar')" @click="clearImage"></FileInput>
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
      <Toggle class="mb-4" v-model="form.is_active" :checked="form.is_active" error-input="is_active"
              :label="trans('labels.show')" name="status"/>
      <div v-if="form.details.length > 0" class="w-full">
        <span class="text-sm text-gray-500">{{ trans('labels.detail') }}</span>
        <div v-for="(detail, index) in form.details"
             class="flex flex-row flex-nowrap justify-between items-center mb-4">
          <TextInput class="w-full" type="text" :min="0" :max="255" :name="'detail'+ index"
                     v-model="form.details[index]" :error-input="'details.' +index"/>
          <a @click="deleteDetail(index)" class="uppercase cursor-pointer text-lg ml-3 text-danger-400"
             :title="trans('labels.delete')">
            <i class="fa fa-trash"></i>
          </a>
        </div>
      </div>
      <Button type="button" @click="addDetail()" :label="trans('labels.add_detail')"/>
    </Form>
  </div>
</template>

<script>
import {defineComponent, reactive, ref} from "vue";
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

export default defineComponent({
  components: {QuillEditor, Spinner, Toggle, Form, FileInput, Panel, Alert, Dropdown, TextInput, Button, Page, Tab},
  setup() {
    const alertStore = useAlertStore();
    const category = ref(null)
    const file = ref(null)
    const form = reactive({
      name: null,
      description: null,
      category_id: null,
      price: 0,
      priority: 100,
      is_active: false,
      details: [],
    });

    const options = ref({
      debug: 'info',
      toolbar: 'full',
      theme: 'snow',
      contentType: 'html',
    });


    function clearImage() {
      file.value = null
      form.file = null
    }

    function addDetail() {
      form.details[form.details.length] = ''
    }

    function deleteDetail(index) {
      if (form.details[index]) {
        form.details.splice(index, 1);
        if (alertStore.errors[`details.${index}`]) {
          delete alertStore.errors[`details.${index}`]
        }
      }
    }

    function clearData() {
      form.details = [];
      file.value = null
      form.file = null
      category.value = null
    }

    return {
      trans,
      form,
      category,
      file,
      clearImage,
      addDetail,
      deleteDetail,
      clearData,
      options,
      alertStore,
    }
  }
})
</script>

<style scoped>

</style>
