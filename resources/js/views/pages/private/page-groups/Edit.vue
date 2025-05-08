<template>
  <Page :title="page.title" :breadcrumbs="page.breadcrumbs" :actions="page.actions" @action="onAction"
        :is-loading="page.loading">
    <Panel>
      <Form id="edit-page-group">
        <TextInput class="mb-4" type="text" :required="true" name="name" v-model="form.name" error-input="name"
                   label="Tên"/>
        <TextInput class="mb-4" type="text" name="name_en" v-model="form.name_en" error-input="name_en"
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
import {defineComponent, onBeforeMount, reactive, ref} from "vue";
import {trans} from "@/helpers/i18n";
import {useRoute} from "vue-router";
import {toUrl} from "@/helpers/routing";
import TextInput from "@/views/components/input/TextInput";
import Alert from "@/views/components/Alert";
import Panel from "@/views/components/Panel";
import Page from "@/views/layouts/Page";
import Form from "@/views/components/Form";
import {clearObject, fillObject, reduceProperties} from "@/helpers/data";
import {useAlertStore} from "@/stores";
import Dropdown from "@/views/components/input/Dropdown.vue";
import Toggle from "@/views/components/input/Toggle.vue";
import PageGroupService from "@/services/PageGroupService";
const alertStore = useAlertStore();


const route = useRoute();
const item = ref(null);
const form = reactive({
  name: '',
  name_en: '',
  column: 1,
  is_active: false,
});

const page = reactive({
  id: 'edit-page-group',
  title: 'Chỉnh sửa',
  filters: false,
  loading: true,
  breadcrumbs: [
    {
      name: 'Nhóm trang',
      to: toUrl('/page-groups/list'),
    },
    {
      name: 'Chỉnh sửa',
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
    },{
      id: 'submit',
      name: trans('global.buttons.save'),
      icon: "fa fa-save",
      type: 'submit',
    }
  ]
});

const service = new PageGroupService();

onBeforeMount(() => {
  service.edit(route.params.id).then((response) => {
    fillObject(form, response.data.model);
    item.value = response.data.model;
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
  service.handleUpdate('edit-page-group', route.params.id, reduceProperties(form, 'roles', 'id')).then((response) => {
    if (alertStore.type == 'success') {
      clearObject(form)
      fillObject(form, response.data.model);
      item.value = response.data.model;
    }
  });
  return false;
}
</script>

<style scoped>

</style>
