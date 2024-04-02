<template>
  <Page :title="page.title" :breadcrumbs="page.breadcrumbs" :actions="page.actions" @action="onAction">
    <Panel>
      <Form id="create-attribute-group" @submit.prevent="onSubmit">
        <TextInput class="mb-4" :required="true" name="name" v-model="form.name" error-input="name"
                   :label="trans('labels.name')"/>
        <TextInput class="mb-4" type="number" :min="0" :max="10000" name="priority" v-model="form.priority"
                   error-input="priority" :label="trans('labels.priority')"/>
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
import attributeGroupService from "@/services/AttributeGroupService";


const alertStore = useAlertStore();
const form = reactive({
  name: null,
  priority: 1000,
});

const page = reactive({
  id: 'create_attribute_group',
  title: 'Nhóm thuộc tính mới',
  filters: false,
  breadcrumbs: [
    {
      name: 'Nhóm thuộc tính',
      to: toUrl('/attribute-groups/list'),

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
      to: toUrl('/attribute-groups/list'),
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

const service = new attributeGroupService();

function onAction(data) {
  switch (data.action.id) {
    case 'submit':
      onSubmit();
      break;
  }
}

function onSubmit() {

  service.handleCreate('create-attribute-group', reduceProperties(form, 'roles', 'id')).then(() => {
    if (alertStore.type == 'success') {
      clearObject(form)
      clearData();
    }
  })
  return false;
}

function clearData() {
  form.name = null
  form.priority = 1000
}
</script>

<style scoped>

</style>
