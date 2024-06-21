<template>
    <Page :title="page.title" :breadcrumbs="page.breadcrumbs" :actions="page.actions"
          :is-loading="page.loading">
        <div class="shadow border-b border-gray-200 mb-8 sm:rounded-lg">
            <div class="min-w-full divide-y divide-gray-200">
                <div class="whitespace-nowrap bg-white rounded-lg shadow-md">
                    <div
                        class="px-2 md:px-6 py-4 bg-gradient-to-tr from-blue-500 to-blue-700 rounded text-white text-base font-bold">
                        Thông tin đơn hàng
                    </div>
                    <div class="px-2 md:px-6 py-4">
                        <div class="relative rounded mb-2">
                            <main class="order-main overflow-y-auto md:h-screen">
                                <div class="overflow-y-auto main-invoice pb-5">
                                    <div class="container mx-auto">
                                        <div>
                                            <div class="px-4 overflow-auto">
                                                <div
                                                    class="inline-block min-w-full shadow-md rounded-lg overflow-hidden"
                                                >
                                                    <table class="table-auto">
                                                        <thead>
                                                        <tr>
                                                            <th
                                                                class="w-4/12 px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-bold text-gray-700 uppercase tracking-wider"
                                                            >
                                                                Tên SP
                                                            </th>
                                                            <th
                                                                class="w-2/12 px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-bold text-gray-700 uppercase tracking-wider"
                                                            >
                                                                Giá tiền (giá gốc)
                                                            </th>
                                                            <th
                                                                class="w-2/12 px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-bold text-gray-700 uppercase tracking-wider"
                                                            >
                                                                Giá tiền
                                                            </th>
                                                            <th
                                                                class="w-1/12 px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-bold text-gray-700 uppercase tracking-wider"
                                                            >
                                                                Số lượng
                                                            </th>
                                                            <th
                                                                class="w-w-2/12 px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-bold text-gray-700 uppercase tracking-wider"
                                                            >
                                                                Thành tiền
                                                            </th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr v-for="item in order.details" :key="item.id" class="p-1 border-b border-gray-200">
                                                            <td class="p-2 bg-white text-sm product-name text-gray-600 whitespace-no-wrap">
                                                               {{ item.name }}
                                                            </td>
                                                            <td class="p-2 bg-white text-sm text-right">
                                                                <span
                                                                    class="relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight"
                                                                >
                                                                  <span
                                                                      aria-hidden
                                                                      class="absolute inset-0 bg-green-200 opacity-50 rounded-full"
                                                                  ></span>
                                                                  <span class="relative">{{
                                                                          item.cost.toLocaleString()
                                                                      }}</span>
                                                                 <sup class="sup">đ</sup>
                                                                </span>
                                                            </td>
                                                            <td class="p-2 bg-white text-sm text-right">
                                                                <span
                                                                    class="relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight"
                                                                >
                                                                  <span
                                                                      aria-hidden
                                                                      class="absolute inset-0 bg-green-200 opacity-50 rounded-full"
                                                                  ></span>
                                                                  <span class="relative">{{
                                                                          item.price.toLocaleString()
                                                                      }}</span>
                                                                 <sup class="sup">đ</sup>
                                                                </span>
                                                            </td>
                                                            <td class="p-2 bg-white text-sm text-center">
                                                              <span
                                                                  class="relative inline-block px-3 py-1 font-semibold text-orange-900 leading-tight"
                                                              >
                                                                  <span
                                                                      aria-hidden
                                                                      class="absolute inset-0 bg-orange-200 opacity-50 rounded-full"
                                                                  ></span>
                                                                  <span class="relative">{{ item.qty.toLocaleString() }}</span>
                                                                </span>
                                                            </td>
                                                            <td class="p-2 bg-white text-sm text-right">
                                                                <span
                                                                    class="relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight"
                                                                >
                                                                  <span
                                                                      aria-hidden
                                                                      class="absolute inset-0 bg-blue-200 opacity-50 rounded-full"
                                                                  ></span>
                                                                  <span class="relative">{{
                                                                          (item.price * item.qty).toLocaleString()
                                                                      }}</span>
                                                                   <sup class="sup">đ</sup>
                                                                </span>
                                                            </td>
                                                        </tr>
                                                        <tr class="p-1 border-b border-gray-200">
                                                          <td class="p-2 bg-white text-sm product-name text-gray-600 whitespace-no-wrap">
                                                            Tổng cộng
                                                          </td>
                                                          <td colspan="4" class="p-2 bg-white text-sm text-right">
                                                                <span
                                                                    class="relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight"
                                                                >
                                                                  <span
                                                                      aria-hidden
                                                                      class="absolute inset-0 bg-blue-200 opacity-50 rounded-full"
                                                                  ></span>
                                                                  <span class="relative">{{
                                                                      order.total.toLocaleString()
                                                                    }}</span>
                                                                  <sup class="sup">đ</sup>
                                                                </span>
                                                          </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="px-4">
                                              <Textarea
                                                  autocomplete="description"
                                                  class="mt-1 block w-full resize rounded-md p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                                  type="text"
                                                  autocapitalize="false"
                                              > {{order.note}} </Textarea>
                                            </div>

                                          <div class="px-4 mt-4">
                                            <h2>
                                              <strong>Thông tin khách hàng</strong>
                                            </h2>
                                            <div class="flex flex-row gap-12 overflow-auto">
                                              <div>
                                                <div class="mt-1">Tên khách hàng</div>
                                                <div class="mt-1">Số điện thoại</div>
                                                <div class="mt-1">Email</div>
                                                <div class="mt-1">Địa chỉ</div>
                                                <div class="mt-1">Thời gian đặt hàng</div>
                                              </div>
                                              <div>
                                                <div class="mt-1">{{order.customer_name}}</div>
                                                <div class="mt-1">{{order.customer_phone}}</div>
                                                <div class="mt-1">{{order.customer_email}}</div>
                                                <div class="mt-1">{{order.full_address}}</div>
                                                <div class="mt-1">{{order.time}}</div>
                                              </div>
                                            </div>
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
