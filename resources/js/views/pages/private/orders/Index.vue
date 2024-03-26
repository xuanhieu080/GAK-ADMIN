<template>
    <Page :title="page.title" :breadcrumbs="page.breadcrumbs" :actions="page.actions" @action="onPageAction">

        <template #filters v-if="page.toggleFilters">
            <Filters @clear="onFiltersClear">
                <FiltersRow>
                    <FiltersCol>
                        <TextInput name="code" :label="trans('labels.code')" v-model="mainQuery.filters.code.value"></TextInput>
                    </FiltersCol>
                    <FiltersCol>
                        <Dropdown name="products" server="products" :multiple="true" :label="trans('labels.product')" v-model="mainQuery.filters.product_id.value"></Dropdown>
                    </FiltersCol>
                    <FiltersCol>
                        <Dropdown name="customers" server="customers" :multiple="true" :label="trans('labels.customer')" v-model="mainQuery.filters.customer_id.value"></Dropdown>
                    </FiltersCol>
                </FiltersRow>
            </Filters>
        </template>

        <template #default>
            <Table :id="page.id" v-if="table" :headers="table.headers" :sorting="table.sorting" :actions="table.actions" :records="table.records" :pagination="table.pagination" :is-loading="table.loading" @page-changed="onTablePageChange" @action="onTableAction" @sort="onTableSort">
                <template v-slot:content-price="props">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800" v-html="props.item.price.toLocaleString()"></span>
                </template>
                <template v-slot:content-total="props">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800" v-html="props.item.total.toLocaleString()"></span>
                </template>
                <template v-slot:content-status="props">
                    <span v-if="props.item.status == 'SUCCESS'" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800" v-html="trans('labels.list_status.success')"></span>
                    <span v-else class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800" v-html="trans('labels.list_status.failed')"></span>
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
                customer_id: {
                    value: '',
                    comparison: '='
                },
                product_id: {
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
                title: trans('labels.title'),
                date: trans('labels.date'),
                customer_name: trans('labels.customer_name'),
                customer_code: trans('labels.customer_code'),
                product_name: trans('labels.product'),
                qty: trans('labels.qty'),
                price: trans('labels.price'),
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
                // delete: {
                //     id: 'delete',
                //     name: trans('global.actions.delete'),
                //     icon: "fa fa-trash",
                //     showName: false,
                //     danger: true,
                // }
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
