<template>
    <Page :title="page.title" :breadcrumbs="page.breadcrumbs" :actions="page.actions"
          :is-loading="page.loading">
        <div class="shadow border-b border-gray-200 mb-8 sm:rounded-lg">
            <div class="min-w-full divide-y divide-gray-200">
                <div class="whitespace-nowrap bg-white rounded-lg shadow-md">
                    <div class="px-2 md:px-6 py-4 bg-gradient-to-tr from-blue-500 to-blue-700 rounded text-white text-base font-bold">
                        Thông tin đơn hàng
                    </div>
                    <div class="px-2 md:px-6 py-4">
                        <div class="relative px-2 md:px-10 rounded mb-2">
                            <main class="order-main overflow-y-auto md:h-screen">
                                <div class="overflow-y-auto main-invoice pb-5">
                                    <div class="container mx-auto">
                                        <div>
                                            <div class="px-4 overflow-auto">
                                                <div
                                                    class="inline-block min-w-full shadow-md rounded-lg overflow-hidden"
                                                >
                                                    <table class="min-w-full leading-normal">
                                                        <thead>
                                                        <tr class="block">
                                                            <th
                                                                class="w-3/12 px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-bold text-gray-700 uppercase tracking-wider"
                                                            >
                                                                Tên SP
                                                            </th>
                                                            <th
                                                                class="w-2/12 px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-bold text-gray-700 uppercase tracking-wider"
                                                            >
                                                                Giá tiền (giá gốc)
                                                            </th>
                                                            <th
                                                                class="w-2/12 px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-bold text-gray-700 uppercase tracking-wider"
                                                            >
                                                                Giá tiền
                                                            </th>
                                                            <th
                                                                class="w-1/12 px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-bold text-gray-700 uppercase tracking-wider"
                                                            >
                                                                Đơn vị tính
                                                            </th>
                                                            <th
                                                                class="w-1/12 px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-bold text-gray-700 uppercase tracking-wider"
                                                            >
                                                                Số lượng
                                                            </th>
                                                            <th
                                                                class="w-w-2/12 px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-bold text-gray-700 uppercase tracking-wider"
                                                            >
                                                                Thành tiền
                                                            </th>
                                                            <th
                                                                class="w-1/12 px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-bold text-gray-700 uppercase tracking-wider"
                                                            >
                                                                Xoá
                                                            </th>
                                                        </tr>
                                                        </thead>
                                                        <tbody class="flex flex-col">
                                                        <tr class="block p-1 border-b border-gray-200">
                                                            <td
                                                                class="w-3/12 p-2 bg-white text-sm"
                                                            >
                                                                <div class="product-name text-gray-600 whitespace-no-wrap">
                                                                    {{ 'df' }}
                                                                </div>
                                                            </td>
                                                            <td class="w-2/12 p-2 bg-white text-sm text-right">
                                                                <span
                                                                    class="relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight"
                                                                >
                                                                  <span
                                                                      aria-hidden
                                                                      class="absolute inset-0 bg-green-200 opacity-50 rounded-full"
                                                                  ></span>
                                                                  <span class="relative">{{ 'asd' }}</span>
                                                                </span>
                                                            </td>
                                                            <td class="w-2/12 p-2 bg-white text-sm text-right">
                                                                <span
                                                                    class="relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight"
                                                                >
                                                                  <span
                                                                      aria-hidden
                                                                      class="absolute inset-0 bg-green-200 opacity-50 rounded-full"
                                                                  ></span>
                                                                  <span class="relative">{{ 'asd' }}</span>
                                                                </span>
                                                            </td>
                                                            <th
                                                                class="w-1/12 px-5 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider"
                                                            >
                                                                Đơn vị tính
                                                            </th>
                                                            <th
                                                                class="w-1/12 px-5 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider"
                                                            >
                                                                Số lượng
                                                            </th>
                                                            <th
                                                                class="w-w-2/12 px-5 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider"
                                                            >
                                                                Thành tiền
                                                            </th>
                                                            <th
                                                                class="w-1/12 px-5 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider"
                                                            >
                                                                Xoá
                                                            </th>
                                                        </tr>
                                                        <tr v-for="(item, index) in order.details" class="p-1 border-b border-gray-200 ">
                                                            <td class="w-3/12 p-2 bg-white text-sm">
                                                                <div>
                                                                    <div class="product-name text-gray-600 whitespace-no-wrap">
                                                                        {{ item.name }}
                                                                    </div>
                                                                </div>
                                                            </td>

                                                            <td class="w-2/12 p-2 bg-white text-sm text-right">
                                                                <span
                                                                    class="relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight"
                                                                >
                                                                  <span
                                                                      aria-hidden
                                                                      class="absolute inset-0 bg-green-200 opacity-50 rounded-full"
                                                                  ></span>
                                                                  <span class="relative">{{ item.cost.toLocaleString() }}</span>
                                                                </span>
                                                            </td>
                                                            <td class="w-2/12 p-2 bg-white text-sm">
                                                                 <span
                                                                     class="relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight"
                                                                 >
                                                                  <span
                                                                      aria-hidden
                                                                      class="absolute inset-0 bg-green-200 opacity-50 rounded-full"
                                                                  ></span>
                                                                  <span class="relative">{{ item.cost.toLocaleString() }}</span>
                                                                </span>
                                                            </td>
                                                            <td class="w-1/12 p-2 bg-white text-sm text-center">
                                                              <span
                                                                  class="relative inline-block px-3 py-1 font-semibold text-orange-900 leading-tight"
                                                              >
                                                                  <span
                                                                      aria-hidden
                                                                      class="absolute inset-0 bg-orange-200 opacity-50 rounded-full"
                                                                  ></span>
                                                                  <span class="relative">456</span>
                                                                </span>
                                                            </td>
                                                            <td class="w-1/12 p-2 bg-white text-sm">
                                                                <span>sdf</span>
                                                            </td>

                                                            <td class="w-2/12 p-2 bg-white text-sm text-right">
                                                                <span
                                                                    class="relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight"
                                                                >
                                                                  <span
                                                                      aria-hidden
                                                                      class="absolute inset-0 bg-blue-200 opacity-50 rounded-full"
                                                                  ></span>
                                                                  <span class="relative">{{ (item.price * item.qty).toLocaleString() }}</span>
                                                                </span>
                                                            </td>
                                                            <td
                                                                class="w-1/12 p-2 bg-white text-sm text-right"
                                                            >
                                                                <button
                                                                    class="inline-block text-gray-500 hover:text-gray-700"
                                                                    type="button"
                                                                >
                                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5"
                                                                         viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                                        <path
                                                                            d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"
                                                                            stroke-linecap="round"
                                                                            stroke-linejoin="round"/>
                                                                    </svg>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="px-4">
                          <Textarea
                              autocomplete="description"
                              class="mt-1 block w-full"
                              placeholder="Nhập mô tả"
                              type="text"
                          />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </main>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Page>
</template>

<script>
import {defineComponent, onBeforeMount, reactive, ref} from "vue";
import {trans} from "@/helpers/i18n";
import {useRoute} from "vue-router";
import {toUrl} from "@/helpers/routing";
import OrderService from "@/services/OrderService";
import Button from "@/views/components/input/Button";
import TextInput from "@/views/components/input/TextInput";
import Dropdown from "@/views/components/input/Dropdown";
import Alert from "@/views/components/Alert";
import Panel from "@/views/components/Panel";
import Page from "@/views/layouts/Page";
import Form from "@/views/components/Form";
import FiltersRow from "@/views/components/filters/FiltersRow.vue";
import Filters from "@/views/components/filters/Filters.vue";
import FiltersCol from "@/views/components/filters/FiltersCol.vue";
import Detail from "@/views/pages/private/orders/Detail.vue";

export default defineComponent({
    components: {
        Detail,
        FiltersCol, Filters, FiltersRow,
        Form,
        Panel,
        Alert,
        Dropdown,
        TextInput,
        Button,
        Page,
    },
    setup() {
        const route = useRoute();
        const order = ref(null);

        const page = reactive({
            id: 'edit_user',
            title: trans('global.pages.orders_edit'),
            filters: false,
            loading: true,
            breadcrumbs: [
                {
                    name: trans('global.pages.orders'),
                    to: toUrl('/orders/list'),
                },
                {
                    name: trans('global.pages.orders_edit'),
                    active: true,
                }
            ],
            actions: [
                {
                    id: 'back',
                    name: trans('global.buttons.back'),
                    icon: "fa fa-angle-left",
                    to: toUrl('/orders/list'),
                    theme: 'outline',
                }
            ]
        });

        const service = new OrderService();

        onBeforeMount(() => {
            service.edit(route.params.id).then((response) => {
                order.value = response.data.model;
                page.loading = false;
            })
        });

        return {
            trans,
            page,
            order,
        }
    }
})
</script>

<style scoped>

</style>
