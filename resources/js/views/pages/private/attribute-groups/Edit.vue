<template>
  <Page :title="page.title" :breadcrumbs="page.breadcrumbs" :actions="page.actions" @action="onAction"
        :is-loading="page.loading">
    <Panel>
      <Form id="edit-attribute-group">
        <TextInput class="mb-4" type="text" name="name" v-model="form.name" error-input="name"
                   label="Tên"/>
        <TextInput class="mb-4" type="url" error-input="link" name="link" v-model="form.link"
                   label="Đường dẫn liên kết"/>
        <TextInput class="mb-4" type="number" :min="0" :max="10000" name="priority" v-model="form.priority"
                   error-input="priority" :label="trans('labels.priority')"/>
        <Toggle class="mb-4" v-model="form.is_color" :checked="form.is_color" error-input="is_color"
                label="Có phải là nhóm màu không" name="is_color"/>
        <Toggle class="mb-4" v-model="form.is_main" :checked="form.is_main" error-input="is_main"
                label="Có phải là nhóm chính không" name="is_main"/>
      </Form>
    </Panel>
  </Page>
</template>

<script setup>
import {defineComponent, onBeforeMount, reactive, ref} from "vue";
import {trans} from "@/helpers/i18n";
import {useRoute} from "vue-router";
import {toUrl} from "@/helpers/routing";
import AttributeGroupService from "@/services/AttributeGroupService";
import TextInput from "@/views/components/input/TextInput";
import Alert from "@/views/components/Alert";
import Panel from "@/views/components/Panel";
import Page from "@/views/layouts/Page";
import Form from "@/views/components/Form";
import {clearObject, fillObject, reduceProperties} from "@/helpers/data";
import {useAlertStore} from "@/stores";
import Toggle from "@/views/components/input/Toggle.vue";
const alertStore = useAlertStore();


const route = useRoute();
const item = ref(null);
const form = reactive({
  name: '',
  priority: 1000,
  is_color: false,
  is_main: false,
  link: null,
});

const page = reactive({
  id: 'edit-attribute-group',
  title: 'Chỉnh sửa',
  filters: false,
  loading: true,
  breadcrumbs: [
    {
      name: 'Nhóm thuộc tính',
      to: toUrl('/attribute-groups/list'),
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
      to: toUrl('/attribute-groups/list'),
      theme: 'outline',
    },{
      id: 'submit',
      name: trans('global.buttons.save'),
      icon: "fa fa-save",
      type: 'submit',
    }
  ]
});

const service = new AttributeGroupService();

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
  service.handleUpdate('edit-attribute-group', route.params.id, reduceProperties(form, 'roles', 'id')).then((response) => {
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
