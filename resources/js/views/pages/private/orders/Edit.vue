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
                            <div class="flex flex-col space-y-3 xl:flex-row xl:space-x-4 xl:space-y-0 xl:items-center">
                                <div class="relative w-full text-left"><!--v-if-->
                                    <div class="flex flex-col mt-1">
                                        <div>
                                            <div class="mb-3 flex">
                                                <span class="mr-5 w-1/3">Mã đơn:</span>
                                                <span class="w-2/3">{{ item.code }}</span>
                                            </div>
                                            <div class="mb-3 flex">
                                                <span class="mr-5 w-1/3">Sản phẩm:</span>
                                                <span class="w-2/3">{{ item.product_name }}</span>
                                            </div>
                                            <div class="mb-3 flex">
                                                <span class="mr-5 w-1/3">Người mua:</span>
                                                <span class="w-2/3">{{ item.customer_name }}</span>
                                            </div>
                                            <div class="mb-3 flex">
                                                <span class="mr-5 w-1/3">Mã người mua:</span>
                                                <span class="w-2/3">{{ item.customer_code }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="relative w-full text-left"><!--v-if-->
                                    <div class="flex flex-col mt-1">
                                        <div>
                                            <div class="mb-3 flex">
                                                <span class="mr-5 w-1/3">Số lượng:</span>
                                                <span class="w-2/3">{{ item.qty.toLocaleString() }}</span>
                                            </div>
                                            <div class="mb-3 flex">
                                                <span class="mr-5 w-1/3">Tổng tiền:</span>
                                                <span class="w-2/3">{{ item.total.toLocaleString() }}</span>
                                            </div>
                                            <div class="mb-3 flex">
                                                <span class="mr-5 w-1/3">Thời gian:</span>
                                                <span class="w-2/3">{{ item.time }}</span>
                                            </div>
                                            <div class="mb-3 flex">
                                                <span class="mr-5 w-1/3">Trạng thái:</span>
                                                <div>
                                                    <span v-if="item.status == 'SUCCESS'"
                                                          class="w-2/3 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800"
                                                          v-html="trans('labels.list_status.success')"></span>
                                                    <span v-else
                                                          class="w-2/3 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800"
                                                          v-html="trans('labels.list_status.failed')"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <Detail :order-id="item.id" />
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
        const item = ref(null);

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
                item.value = response.data.model;
                page.loading = false;
            })
        });

        return {
            trans,
            page,
            item,
        }
    }
})
</script>

<style scoped>

</style>
