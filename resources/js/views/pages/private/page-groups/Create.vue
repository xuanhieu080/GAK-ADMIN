<template>
  <Page :title="page.title" :breadcrumbs="page.breadcrumbs" :actions="page.actions" @action="onAction">
    <Panel>
      <Form id="create-page-group" @submit.prevent="onSubmit">
        <TextInput class="mb-4" :required="true" name="name" v-model="form.name" error-input="name"
                   :label="trans('labels.name')"/>
        <TextInput class="mb-4" name="name_en" v-model="form.name_en" error-input="name_en"
                   label="Tên tiếng anh"/>

        <TextInput class="mb-4" type="number" :min="1" :max="100" name="column" v-model="form.column"
                   error-input="column" label="Thứ tự cột"/>
        <Toggle class="mb-4" v-model="form.is_active" :checked="form.is_active" error-input="is_active"
                label="Hiển thị" name="is_active"/>
      </Form>
    </Panel>
  </Page>
</template>

<script setup>
import {defineComponent, reactive, ref} from "vue";
import {trans} from "@/helpers/i18n";
import TextInput from "@/views/components/input/TextInput";
import Alert from "@/views/components/Alert";
import Panel from "@/views/components/Panel";
import Page from "@/views/layouts/Page";
import {clearObject, reduceProperties} from "@/helpers/data";
import {toUrl} from "@/helpers/routing";
import Form from "@/views/components/Form";
import {useAlertStore} from "@/stores/alert";
import Dropdown from "@/views/components/input/Dropdown.vue";
import Toggle from "@/views/components/input/Toggle.vue";
import PageGroupService from "@/services/PageGroupService";


const alertStore = useAlertStore();
const form = reactive({
  name: null,
  name_en: null,
  column: 1,
  is_active: false,
});
const group = ref();

const page = reactive({
  id: 'create_page-group',
  title: 'Nhóm trang',
  filters: false,
  breadcrumbs: [
    {
      name: 'Nhóm trang',
      to: toUrl('/page-groups/list'),

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
      to: toUrl('/page-groups/list'),
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

const service = new PageGroupService();

function onAction(data) {
  switch (data.action.id) {
    case 'submit':
      onSubmit();
      break;
  }
}

function onSubmit() {
  service.handleCreate('create-page-group', reduceProperties(form, 'roles', 'id')).then(() => {
    if (alertStore.type == 'success') {
      clearObject(form)
      clearData();
    }
  })
  return false;
}

function clearData() {
  form.name = null
  form.name_en = null
  form.is_active = false
}
</script>

<style scoped>

</style>
