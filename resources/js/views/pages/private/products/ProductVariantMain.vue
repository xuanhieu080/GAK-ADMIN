<template>
  <div id="edit-product-variant">
    <TextInput class="mb-4" type="text" disabled v-model="item.name"
               label="Tên sản phẩm"/>
    <div>
      <div class="flex flex-wrap gap-4">
        <div v-if="item.variantMains" v-for="(variant, index) in item.variantMains" class="">
              <Button @click="() =>active = index" :label="variant.option_name" :theme="active != index ? 'outline' : ''"></Button>
        </div>
      </div>
      <div class="grid grid-cols-1">
        <div v-if="item.variantMains" v-for="(variant, index) in item.variantMains" class="py-4" :class="active != index ? 'hidden' : ''">
          <ProductVariantMainItem :item="variant" :index="index" :key="index"/>
        </div>
      </div>
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
import ProductVariantMainItem from "@/views/pages/private/products/ProductVariantMainItem.vue";
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
  service.find(`${props.id}/variant-mains`).then((response) => {
    item.value = response.data.model;
    isFirstLoad.value = false
  })
});




// function informationUpdate(index, data) {
//   form.details[index] = data
// }

watch(() => props.refresh,
    () => {
      service.find(`${props.id}/variant-mains`).then((response) => {
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
