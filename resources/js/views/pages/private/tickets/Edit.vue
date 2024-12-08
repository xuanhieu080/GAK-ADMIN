<template>
  <Page :title="page.title" :breadcrumbs="page.breadcrumbs" :actions="page.actions"
        :is-loading="page.loading">
    <Panel>
      <Form id="edit-ticket">
        <TextInput class="mb-4" type="text" name="name" disabled v-model="form.name"
                   label="Tên"/>
        <TextInput class="mb-4" type="text" name="phone" disabled v-model="form.phone"
                   label="Số điện thoại"/>
        <TextInput class="mb-4" type="text" name="email" disabled v-model="form.email"
                   label="Email"/>
        <TextInput class="mb-4" type="textarea" name="description" disabled v-model="form.description"
                   label="Nội dung"/>
        <TextInput class="mb-4" type="textarea" name="created_at" disabled v-model="form.created_at"
                   label="Thời gian"/>
      </Form>
    </Panel>
  </Page>
</template>

<script setup>
import { onBeforeMount, reactive, ref} from "vue";
import {trans} from "@/helpers/i18n";
import {useRoute} from "vue-router";
import {toUrl} from "@/helpers/routing";
import TicketService from "@/services/TicketService";
import TextInput from "@/views/components/input/TextInput";
import Panel from "@/views/components/Panel";
import Page from "@/views/layouts/Page";
import Form from "@/views/components/Form";
import { fillObject} from "@/helpers/data";


const route = useRoute();
const item = ref(null);
const form = reactive({
  name: '',
  phone: '',
  email: '',
  description: '',
  created_at: '',
});

const page = reactive({
  id: 'edit-ticket',
  title: 'Thông tin',
  filters: false,
  loading: true,
  breadcrumbs: [
    {
      name: 'Đặt may',
      to: toUrl('/tickets/list'),
    }
  ],
  actions: [
    {
      id: 'back',
      name: trans('global.buttons.back'),
      icon: "fa fa-angle-left",
      to: toUrl('/tickets/list'),
      theme: 'outline',
    }
  ]
});

const service = new TicketService();

onBeforeMount(() => {
  service.find(route.params.id).then((response) => {
    fillObject(form, response.data.model);
    item.value = response.data.model;
    page.loading = false;
  })
});


</script>

<style scoped>

</style>
