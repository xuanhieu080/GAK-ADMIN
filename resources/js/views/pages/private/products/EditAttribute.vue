<template>
  <div>
    <Form id="edit-attribute-group">
      <div v-if="form.details.length > 0 || form.detail_currents.length > 0" class="w-full">
        <div v-for="(detail, index) in form.detail_currents"
             class="flex flex-row flex-nowrap gap-4 items-center mb-4">
          <Dropdown class="mb-4" :name="'detail-current-'+ index + '.-attribute-group'"
                    :error-input="'detail_currents.' + index + '.attribute_group_id'" :required="true" :multiple="false"
                    server="attribute-groups" label="Nhóm thuôc tính" placeholder="Nhóm thuôc tính"
                    :server-search-min-characters="0"
                    v-model="form.detail_currents[index].attribute_group"></Dropdown>
          <Dropdown v-if="form.detail_currents[index].attribute_group" class="mb-4"
                    :name="'detail-current-'+ index + '.-attribute'"
                    :error-input="'detail_currents.' + index + '.attribute_id'" :required="true" :multiple="false"
                    server="attributes"
                    :params="'&group_id='+ form.detail_currents[index].attribute_group_id"
                    label="Thuôc tính" placeholder="Thuôc tính"
                    :server-search-min-characters="0"
                    v-model="form.detail_currents[index].attribute"></Dropdown>
          <a @click="deleteDetail(index,'current')" class="uppercase cursor-pointer text-lg text-danger-400"
             :title="trans('labels.delete')">
            <i class="fa fa-trash"></i>
          </a>
        </div>
        <div v-for="(detail, index) in form.details"
             class="flex flex-row flex-nowrap gap-4 items-center mb-4">
          <Dropdown class="mb-4" :name="'detail-'+ index + '.-attribute-group'"
                    :error-input="'details.' + index + '.attribute_group_id'" :required="true" :multiple="false"
                    server="attribute-groups" label="Nhóm thuôc tính" placeholder="Nhóm thuôc tính"
                    :server-search-min-characters="0" v-model="form.details[index].attribute_group"></Dropdown>
          <Dropdown v-if="form.details[index].attribute_group" class="mb-4" :name="'detail-'+ index + '.-attribute'"
                    :error-input="'details.' + index + '.attribute_id'" :required="true" :multiple="false"
                    server="attributes"
                    :params="'&group_id='+ form.details[index].attribute_group.id"
                    label="Thuôc tính" placeholder="Thuôc tính"
                    :server-search-min-characters="0" v-model="form.details[index].attribute"></Dropdown>
          <a @click="deleteDetail(index)" class="uppercase cursor-pointer text-lg text-danger-400"
             :title="trans('labels.delete')">
            <i class="fa fa-trash"></i>
          </a>
        </div>
      </div>
      <div class="inline-flex gap-4">
        <Button type="button" @click="addDetail()" :label="trans('labels.add_detail')"/>
        <Button type="button" @click="onSubmit" label="Lưu"/>
      </div>
    </Form>
  </div>
</template>

<script setup>
import {defineComponent, defineProps, onBeforeMount, reactive, ref} from "vue";
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
import Button from "@/views/components/input/Button.vue";
import ProductService from "@/services/ProductService";

const alertStore = useAlertStore();

const props = defineProps({
  id: {
    type: String,
  },
})


const route = useRoute();
const item = ref(null);
const group = ref(null);
const form = reactive({
  details: [],
  detail_currents: [],
});
const data = reactive({
  details: [],
  detail_currents: [],
});


const service = new ProductService();

onBeforeMount(() => {
  service.get(`products/${route.params.id}/attribute`).then((response) => {
    form.detail_currents = response.data.data;
  })
});


function onSubmit() {
  form.details.forEach(function (item, index) {
    data.details[index] = {
      attribute_id: item.attribute?.id,
      attribute_group_id: item.attribute_group?.id,
    }
  })
  form.detail_currents.forEach(function (item, index) {
    data.detail_currents[index] = {
      id: item?.id,
      attribute_id: item.attribute?.id,
      attribute_group_id: item.attribute_group?.id,
    }
  })
  service.handleUpdate('edit-attribute', `${props.id}/attribute`, reduceProperties(data, 'roles', 'id')).then((response) => {
    if (alertStore.type == 'success') {
      service.get(`products/${route.params.id}/attribute`).then((response) => {
        form.detail_currents = response.data.data;
        form.details = [];
      })
    }
  });
  return false;
}

function addDetail() {
  form.details[form.details.length] = {}
}

function deleteDetail(index, type = 'add') {
  if (type == 'add') {
    if (form.details[index] == '' || form.details[index]) {
      form.details.splice(index, 1);
      if (alertStore.errors[`details.${index}`]) {
        delete alertStore.errors[`details.${index}`]
      }
    }
  } else {
    if (form.detail_currents[index] == '' || form.detail_currents[index]) {
      form.detail_currents.splice(index, 1);
      if (alertStore.errors[`detail_currents.${index}`]) {
        delete alertStore.errors[`detail_currents.${index}`]
      }
    }
  }
}
</script>

<style scoped>

</style>
