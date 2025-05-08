<template>
  <Page :title="page.title" :breadcrumbs="page.breadcrumbs" :actions="page.actions" @action="onAction"
        :is-loading="page.loading">
    <Panel>
      <Form id="edit-attribute-group">
        <TextInput class="mb-4" type="text" name="name" v-model="form.name" error-input="name"
                   label="Tên"/>
        <TextInput class="mb-4" type="text" name="name_en" v-model="form.name_en" error-input="name_en"
                   label="Tên tiếng anh"/>
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
import {defineComponent, onBeforeMount, reactive, ref} from "vue";
import {trans} from "@/helpers/i18n";
import {useRoute} from "vue-router";
import {toUrl} from "@/helpers/routing";
import AttributeService from "@/services/AttributeService";
import TextInput from "@/views/components/input/TextInput";
import Alert from "@/views/components/Alert";
import Panel from "@/views/components/Panel";
import Page from "@/views/layouts/Page";
import Form from "@/views/components/Form";
import {clearObject, fillObject, reduceProperties} from "@/helpers/data";
import {useAlertStore} from "@/stores";
import Dropdown from "@/views/components/input/Dropdown.vue";
import Toggle from "@/views/components/input/Toggle.vue";
const alertStore = useAlertStore();


const route = useRoute();
const item = ref(null);
const group = ref(null);
const form = reactive({
  name: '',
  name_en: '',
  link: '',
  group_id: '',
  color: '#000000',
});

const page = reactive({
  id: 'edit-attribute',
  title: 'Chỉnh sửa',
  filters: false,
  loading: true,
  breadcrumbs: [
    {
      name: 'Thuộc tính',
      to: toUrl('/attributes/list'),
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
      to: toUrl('/attributes/list'),
      theme: 'outline',
    },{
      id: 'submit',
      name: trans('global.buttons.save'),
      icon: "fa fa-save",
      type: 'submit',
    }
  ]
});

const service = new AttributeService();

onBeforeMount(() => {
  service.edit(route.params.id).then((response) => {
    fillObject(form, response.data.model);
    item.value = response.data.model;
    page.loading = false;
    if (response.data.model.group_id) {
      group.value = response.data.model.group
    }
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
  if (group.value) {
    form.group_id = group.value.id
  }
  service.handleUpdate('edit-attribute', route.params.id, reduceProperties(form, 'roles', 'id')).then((response) => {
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
