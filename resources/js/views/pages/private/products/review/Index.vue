<script setup>
import Vue3starRatings from "vue3-star-ratings";
import {onBeforeMount, ref} from "vue";
import Create from "@/views/pages/private/products/review/Create.vue";
import productService from "@/services/ProductService";
import Edit from "@/views/pages/private/products/review/Edit.vue";

const props = defineProps({
  id: {
    type: String,
  }
})

const service = new productService();

const product = ref({});
const reviewId = ref();
const rating = ref(0);
const reviews = ref([]);

const modalCreate = ref(false)
const modalEdit = ref(false)

function openModalCreate() {
  modalCreate.value = true;
}

function closeEditModal() {
  modalEdit.value = false;
}

function closeOpenModal() {
  modalCreate.value = false;
}

const addItem = (item) => {
  closeOpenModal();
  service.find(`${props.id}`).then((response) => {
    product.value = response.data.model;
    rating.value = response.data.model.average_rate;
  })
  service.find(`${props.id}/reviews`).then((response) => {
    reviews.value = response.data.data;
  })
}

const updateItem = (item) => {
  closeEditModal();
  // tableData.value[tableData.value.findIndex(it => it.id === item.id)] = item;
}

function remove(id) {
  service.delete(`${props.id}/reviews/${id}`).then((response) => {
    getData();
  })
}

function editItem(id) {
  reviewId.value = id;
  modalEdit.value = true
}

onBeforeMount(() => {
  getData();
});


function getData() {
  service.find(`${props.id}`).then((response) => {
    product.value = response.data.model;
    rating.value = response.data.model.average_rate;
  })
  service.find(`${props.id}/reviews`).then((response) => {
    reviews.value = response.data.data;
  })
}
</script>

<template>
  <div class="product-reviews flex flex-col lg:flex-row items-center lg:items-start gap-6 mt-[10px]">
    <div class="product-rating flex flex-col gap-4 items-center bg-gray-100 rounded-md p-8 w-max lg:sticky top-[130px]">
      <div class="uppercase font-bold">Đánh giá sản phẩm</div>
      <div class="font-bold text-[4rem]">{{ product.average_rate }}</div>
      <Vue3starRatings
          v-model="rating"
          :starSize="32"
          starColor="green"
          inactiveColor="#ddd"
          :numberOfStars="5"
          :disableClick="true"
      />
      <div v-if="product.rate_count > 0" class="italic fs-14 leading-relaxed font-medium">
        {{ product.rate_count }} Đánh giá
      </div>
      <button
          class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
          type="button"
          @click="openModalCreate">Thêm
      </button>
    </div>
    <div v-if="reviews.length > 0" class="grid grid-cols-1 flex-1 gap-8">
      <div
          v-for="review in reviews"
          class="review-item flex flex-col justify-start gap-4 py-4 fs-14 font-medium border-b">
        <div class="flex flex-col gap-2">
          <div class="flex flex-row flex-nowrap items-center justify-between">
            <Vue3starRatings
                class="w-[220px]"
                :read-only="true"
                :model-value="review.rate"
                :starSize="32"
                starColor="green"
                inactiveColor="#ddd"
                :numberOfStars="5"
                :disableClick="true"
            />
            <div class="whitespace-nowrap text-right text-sm font-medium">
              <a @click="editItem(review.id)" class="uppercase cursor-pointer text-lg mr-3" title="Chỉnh sửa">
                <i class="fa fa-edit"></i>
              </a>
              <a @click="remove(review.id)" class="uppercase cursor-pointer text-lg mr-3 text-danger-400" title="Xóa">
                <i class="fa fa-trash"></i>
              </a>
            </div>
          </div>
          <div class="review-name font-bold capitalize">
            {{ review.customer_name }}
          </div>
          <div v-if="review.option_name" class="review-collection-product fs-12 italic">
            {{ review.option_name }}
          </div>
        </div>
        <div class="review-content whitespace-normal">
          {{ review.description }}
        </div>
        <div v-if="review.thumb.length > 0" class="flex whitespace-normal gap-4">
          <div v-for="image in review.thumb" class="image-review">
            <img class="object-cover" height="60" width="60" :src="image"/>
          </div>
        </div>
        <div v-if="review.reply" class="feedback-review whitespace-normal bg-gray-300 p-4 rounded-lg font-semibold">
          {{ review.reply }}
        </div>
        <div class="review-date text-gray-500">
          {{ review.date }}
        </div>
      </div>
    </div>
    <Create :open="modalCreate" @close="closeOpenModal" @confirmed="addItem" :id="props.id"></Create>
    <Edit :open="modalEdit" @close="closeEditModal" @confirmed="addItem" :id="props.id" :review_id="reviewId"></Edit>
  </div>
</template>

<style scoped>
.image-review {
  width: 60px;
  height: 60px;
  border-radius: 10px;

  img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 10px;
    object-position: center;
  }
}
</style>