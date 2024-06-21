<template>
    <Page :title="page.title" :breadcrumbs="page.breadcrumbs" :actions="page.actions" @action="onPageAction">

        <template #filters v-if="page.toggleFilters">
            <Filters @clear="onFiltersClear">
                <FiltersRow>
                    <FiltersCol>
                        <TextInput name="code" :label="trans('labels.code')" v-model="mainQuery.filters.code.value"></TextInput>
                    </FiltersCol>
                    <FiltersCol>
                        <TextInput name="customer" label="Số điện thoại hoặc email" v-model="mainQuery.filters.customer.value"></TextInput>
                    </FiltersCol>
                    <Dropdown class="mb-4" name="status"
                              label="Trạng thái" placeholder="Trạng thái"
                              :options="statusArray"
                              :server-search-min-characters="0" v-model="mainQuery.filters.status.value"></Dropdown>
                </FiltersRow>
            </Filters>
        </template>

        <template #default>
            <Table :id="page.id" v-if="table" :headers="table.headers" :sorting="table.sorting" :actions="table.actions" :records="table.records" :pagination="table.pagination" :is-loading="table.loading" @page-changed="onTablePageChange" @action="onTableAction" @sort="onTableSort">
                <template v-slot:content-discount="props">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800" v-html="props.item.discount.toLocaleString()"></span>
                </template>
                <template v-slot:content-total="props">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800" v-html="props.item.total.toLocaleString()"></span>
                </template>
                <template v-slot:content-status="props">
                    <span v-if="props.item.status == 'pending'" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Đơn hàng mới</span>
                    <span v-if="props.item.status == 'processing'" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-sky-100 text-sky-800">Đang chờ xử lý</span>
                    <span v-if="props.item.status == 'completed'" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Đã hoàn thành</span>
                    <span v-else class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Thất bại</span>
                </template>
            </Table>
        </template>
    </Page>
</template>

<script>

import {trans} from "@/helpers/i18n";
import OrderService from "@/services/OrderService";
import {watch, onMounted, defineComponent, reactive, ref} from 'vue';
import {getResponseError, prepareQuery} from "@/helpers/api";
import {toUrl} from "@/helpers/routing";
import {useAlertStore} from "@/stores";
import alertHelpers from "@/helpers/alert";
import Page from "@/views/layouts/Page";
import Table from "@/views/components/Table";
import Avatar from "@/views/components/icons/Avatar";
import Filters from "@/views/components/filters/Filters";
import FiltersRow from "@/views/components/filters/FiltersRow";
import FiltersCol from "@/views/components/filters/FiltersCol";
import TextInput from "@/views/components/input/TextInput";
import Dropdown from "@/views/components/input/Dropdown";

export default defineComponent({
    components: {
        Dropdown,
        TextInput,
        FiltersCol,
        FiltersRow,
        Filters,
        Page,
        Table,
        Avatar
    },
    setup() {
        const service = new OrderService();
        const statusArray = ref([
            {
                id: 'pending',
                title: 'Đơn hàng mới'
            },
            {
                id: 'processing',
                title: 'Đang chờ xử lý'
            },
            {
                id: 'completed',
                title: 'Đã hoàn thành'
            },
            {
                id: 'cancelled',
                title: 'Đã huỷ'
            }
        ]);
        const alertStore = useAlertStore();
        const mainQuery = reactive({
            page: 1,
            search: '',
            sort: '',
            filters: {
                code: {
                    value: '',
                    comparison: '='
                },
                customer: {
                    value: '',
                    comparison: '='
                },
                status: {
                    value: '',
                    comparison: '='
                },
            }
        });

        const page = reactive({
            id: 'list_orders',
            title: trans('global.pages.orders'),
            breadcrumbs: [
                {
                    name: trans('global.pages.orders'),
                    to: toUrl('/orders/list'),
                    active: true,
                }
            ],
            actions: [
                {
                    id: 'filters',
                    name: trans('global.buttons.filters'),
                    icon: "fa fa-filter",
                    theme: 'outline',
                }
            ],
            toggleFilters: false,
        });

        const table = reactive({
            headers: {
                id: trans('labels.id_pound'),
                code: trans('labels.code'),
                time: trans('labels.date'),
                customer_name: trans('labels.customer_name'),
                customer_phone: 'Số điện thoại',
                customer_email: 'Email',
                discount: 'Giảm giá',
                total: trans('labels.total'),
                status: trans('labels.status'),
            },
            sorting: {
                code: true,
                date: true
            },
            pagination: {
                meta: null,
                links: null,
            },
            actions: {
                edit: {
                    id: 'edit',
                    name: trans('global.actions.edit'),
                    icon: "fa fa-edit",
                    showName: false,
                    to: toUrl('/orders/{id}/edit')
                },
                delete: {
                    id: 'delete',
                    name: trans('global.actions.delete'),
                    icon: "fa fa-trash",
                    showName: false,
                    danger: true,
                }
            },
            loading: false,
            records: null
        })

        function onTableSort(params) {
            mainQuery.sort = params;
        }

        function onTablePageChange(page) {
            mainQuery.page = page;
        }

        function onTableAction(params) {
            switch (params.action.id) {
                case 'delete':
                    alertHelpers.confirmDanger(function () {
                        service.delete(params.item.id).then(function (response) {
                            fetchPage(mainQuery);
                        });
                    })
                    break;
            }
        }

        function onPageAction(params) {
            switch (params.action.id) {
                case 'filters':
                    page.toggleFilters = !page.toggleFilters;
                    break;
            }
        }

        function onFiltersClear() {
            let clonedFilters = mainQuery.filters;
            for(let key in clonedFilters) {
                clonedFilters[key].value = '';
            }
            mainQuery.filters = clonedFilters;
        }

        function fetchPage(params) {
            table.records = [];
            table.loading = true;
            let query = prepareQuery(params);
            service
                .index(query)
                .then((response) => {
                    table.records = response.data.data;
                    table.pagination.meta = response.data.meta;
                    table.pagination.links = response.data.links;
                    table.loading = false;
                })
                .catch((error) => {
                    alertStore.error(getResponseError(error), error.response.status);
                    table.loading = false;
                });
        }

        watch(mainQuery, (newTableState) => {
            fetchPage(mainQuery);
        });

        onMounted(() => {
            fetchPage(mainQuery);
        });

        return {
            trans,
            page,
            table,
            onTablePageChange,
            onTableAction,
            onTableSort,
            onPageAction,
            onFiltersClear,
            mainQuery
        }

    },
});
</script>
