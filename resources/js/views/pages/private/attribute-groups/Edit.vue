<template>
  <Page :title="page.title" :breadcrumbs="page.breadcrumbs" :actions="page.actions" @action="onAction"
        :is-loading="page.loading">
    <Panel>
      <Form id="edit-attribute-group">
        <TextInput class="mb-4" type="text" name="name" v-model="form.name" error-input="name"
                   label="Tên"/>
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
const alertStore = useAlertStore();


const route = useRoute();
const item = ref(null);
const form = reactive({
  name: '',
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
