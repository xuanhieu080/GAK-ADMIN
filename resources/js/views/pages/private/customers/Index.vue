<template>
    <Page :title="page.title" :breadcrumbs="page.breadcrumbs" :actions="page.actions" @action="onPageAction">

        <template #filters v-if="page.toggleFilters">
            <Filters @clear="onFiltersClear">
                <FiltersRow>
                    <FiltersCol>
                        <TextInput name="name" :label="trans('labels.name')" v-model="mainQuery.filters.name.value"></TextInput>
                    </FiltersCol>
                    <FiltersCol>
                        <TextInput name="username" :label="trans('labels.username')" v-model="mainQuery.filters.username.value"></TextInput>
                    </FiltersCol>
                    <FiltersCol>
                        <TextInput name="email" type="email" :label="trans('labels.email')" v-model="mainQuery.filters.email.value"></TextInput>
                    </FiltersCol>
                </FiltersRow>
            </Filters>
        </template>

        <template #default>
            <Table :id="page.id" v-if="table" :headers="table.headers" :sorting="table.sorting" :actions="table.actions" :records="table.records" :pagination="table.pagination" :is-loading="table.loading" @page-changed="onTablePageChange" @action="onTableAction" @sort="onTableSort">
                <template v-slot:content-id="props">
                    <div class="flex items-center">
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">
                                {{ props.item.name }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ trans('labels.code') + ': ' + props.item.code }}
                            </div>
                        </div>
                    </div>
                </template>
                <template v-slot:content-balance="props">
                    <div class="flex items-center justify-end">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">{{props.item.balance.toLocaleString()}}</span>
                    </div>
                </template>
                <template v-slot:content-status="props">
                    <span v-if="props.item.email_verified_at" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800" v-html="trans('users.status.verified')"></span>
                    <span v-else class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800" v-html="trans('users.status.not_verified')"></span>
                </template>
            </Table>
        </template>
    </Page>
</template>

<script>

import {trans} from "@/helpers/i18n";
import CustomerService from "@/services/CustomerService";
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
        const service = new CustomerService();
        const alertStore = useAlertStore();
        const mainQuery = reactive({
            page: 1,
            search: '',
            sort: '',
            filters: {
                name: {
                    value: '',
                    comparison: '='
                },
                username: {
                    value: '',
                    comparison: '='
                },
                email: {
                    value: '',
                    comparison: '='
                }
            }
        });

        const page = reactive({
            id: 'list_customers',
            title: trans('global.pages.users'),
            breadcrumbs: [
                {
                    name: trans('global.pages.users'),
                    to: toUrl('/customers/list'),
                    active: true,
                }
            ],
            actions: [
                {
                    id: 'filters',
                    name: trans('global.buttons.filters'),
                    icon: "fa fa-filter",
                    theme: 'outline',
                },
                {
                    id: 'new',
                    name: trans('global.buttons.add_new'),
                    icon: "fa fa-plus",
                    to: toUrl('/customers/create')
                }
            ],
            toggleFilters: false,
        });

        const table = reactive({
            headers: {
                id: trans('labels.id_pound'),
                name: trans('labels.name'),
                email: trans('labels.email'),
                balance: trans('labels.balance'),
                status: trans('labels.status'),
            },
            sorting: {
                name: true,
                username: true
            },
            pagination: {
                meta: null,
                links: null,
            },
            actions: {
                recharge: {
                    id: 'recharge',
                    name: trans('global.actions.recharge'),
                    icon: "fa fa-money",
                    showName: false,
                    to: toUrl('/customers/{id}/recharge')
                },
                edit: {
                    id: 'edit',
                    name: trans('global.actions.edit'),
                    icon: "fa fa-edit",
                    showName: false,
                    to: toUrl('/customers/{id}/edit')
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
