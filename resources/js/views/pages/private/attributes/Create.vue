<template>
  <Page :title="page.title" :breadcrumbs="page.breadcrumbs" :actions="page.actions" @action="onAction">
    <Panel>
      <Form id="create-attribute" @submit.prevent="onSubmit">
        <TextInput class="mb-4" :required="true" name="name" v-model="form.name" error-input="name"
                   :label="trans('labels.name')"/>
        <Dropdown class="mb-4" name="category" error-input="group_id" :required="true"
                  server="attribute-groups" label="Nhóm thuộc tính" placeholder="Nhóm thuộc tính"
                  :server-search-min-characters="0" v-model="group"></Dropdown>


        <div v-if="group && group.is_color == true" class="gap-4 inline-flex">
         <div class="w-20">
           <TextInput class="mb-4"
                      :required="true" name="color" v-model="form.color" error-input="color"
                      type="color"
                      label="Chọn màu"/>
         </div>
          <button class="px-4 py-2 w-14 h-14 rounded-md shadow-sm opacity-100" :style="'background-color:' + form.color"/>
        </div>
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
import attributeService from "@/services/AttributeService";
import Dropdown from "@/views/components/input/Dropdown.vue";
import Toggle from "@/views/components/input/Toggle.vue";


const alertStore = useAlertStore();
const form = reactive({
  name: null,
  link: null,
  group_id: null,
  color: '#000000',
});
const group = ref();

const page = reactive({
  id: 'create_attribute',
  title: 'Thuộc tính mới',
  filters: false,
  breadcrumbs: [
    {
      name: 'Thuộc tính',
      to: toUrl('/attributes/list'),

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
      to: toUrl('/attributes/list'),
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

const service = new attributeService();

function onAction(data) {
  switch (data.action.id) {
    case 'submit':
      onSubmit();
      break;
  }
}

function onSubmit() {
  if (group.value) {
    form.group_id = group.value.id
  }
  service.handleCreate('create-attribute', reduceProperties(form, 'roles', 'id')).then(() => {
    if (alertStore.type == 'success') {
      clearObject(form)
      clearData();
    }
  })
  return false;
}

function clearData() {
  form.name = null
  form.link = null
  form.group_id = null
  form.color = null
  group.value = null
}
</script>

<style scoped>

</style>
