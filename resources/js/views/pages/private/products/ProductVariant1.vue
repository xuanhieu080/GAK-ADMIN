<template>
  <div id="edit-product-variant">
    <TextInput class="mb-4" type="text" disabled v-model="item.name"
               label="Tên sản phẩm"/>
    <div>
      <div class="flex flex-wrap gap-4">
        <div v-if="item.variants" v-for="(variant, index) in item.variants" class="">
              <Button @click="() =>active = index" :label="variant.option_name" :theme="active != index ? 'outline' : ''"></Button>
        </div>
      </div>
      <div class="grid grid-cols-1 divide-y-4">
        <div v-if="item.variants" v-for="(variant, index) in item.variants" class="py-4" :class="active != index ? 'hidden' : ''">
          <ProductVariantItem :item="variant" :index="index" :key="index"
                              @informationUpdate="(data) => informationUpdate(index, data)"/>
        </div>
      </div>
      <Button @click.prevent="onSubmit" title="Cập nhật" icon="fa fa-save" label="Cập nhật"/>
    </div>
  </div>
</template>

<script setup>
import {defineComponent, reactive, ref, watch, computed, defineProps, onBeforeMount} from "vue";
import {trans} from "@/helpers/i18n";
import TextInput from "@/views/components/input/TextInput";
import Form from "@/views/components/Form";
import {useAlertStore} from "@/stores/alert";
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import {clearObject, fillObject, reduceProperties} from "@/helpers/data";

import ProductService from "@/services/ProductService";
import ProductVariantItem from "@/views/pages/private/products/ProductVariantItem.vue";
import Button from "@/views/components/input/Button.vue";

const alertStore = useAlertStore();

const isFirstLoad = ref(false);

const item = ref({});
const active = ref(0);

const details = ref([])

const form = reactive({
  details: []
});

const props = defineProps({
  id: {
    type: String,
  },
  refresh: {
    type: Number,
    default: 0
  },
});

const service = new ProductService();

onBeforeMount(() => {
  service.find(`${props.id}/variants`).then((response) => {
    item.value = response.data.model;
    isFirstLoad.value = false
  })
});


function onSubmit() {
  service.handleUpdate('edit-product-variant', `${props.id}/variants`, reduceProperties(form, 'roles', 'id')).then((response) => {
    if (alertStore.type == 'success') {
      // fillObject(form, response.data.model);
      // item.value = response.data.model;
      // images.value = item.value.image_url
      // thumbImage.value = item.value.thumb_image
      // form.image = null
      // if (response.data.model.category_id) {
      //   category.value = {
      //     'id': response.data.model.category_id,
      //     'title': response.data.model.category_name
      //   };
      // }
    }
  });
  return false;
}

function informationUpdate(index, data) {
  form.details[index] = data
}

watch(() => props.refresh,
    () => {
      service.find(`${props.id}/variants`).then((response) => {
        item.value = response.data.model;
        isFirstLoad.value = false
      })
    })
</script>

<style scoped lang="scss">
#create-product {
  .product-image {
    max-width: 2000px;
  }
}
</style>
<style lang="scss">
.filepond--root {
  //width:370px;
  //margin: 0 auto;
}

.filepond--drop-label {
  color: #4c4e53;
}

.filepond--label-action {
  text-decoration-color: #babdc0;
}

.filepond--panel-root {
  background-color: #edf0f4;
}
</style>
