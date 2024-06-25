<template>
        <Table v-if="table" :id="page.id" :headers="table.headers" :sorting="table.sorting" :actions="table.actions" :records="table.records" :pagination="table.pagination" :is-loading="table.loading" @page-changed="onTablePageChange" @action="onTableAction">
<!--            <template v-slot:content-status="props">-->
<!--                <span v-if="props.item.status == 'PENDING'" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800" v-html="trans('labels.list_status.success')"></span>-->
<!--                <span v-else class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800" v-html="trans('labels.list_status.failed')"></span>-->
<!--            </template>-->
        </Table>
</template>

<script>

import {trans} from "@/helpers/i18n";
import OrderService from "@/services/OrderService";
import {watch, onMounted, defineComponent, reactive, ref} from 'vue';
import {getResponseError, prepareQuery} from "@/helpers/api";
import {toUrl} from "@/helpers/routing";
import {useAlertStore} from "@/stores";
import Page from "@/views/layouts/Page";
import Table from "@/views/components/Table";
import { notify } from "notiwind"

export default defineComponent({
    components: {
        Page,
        Table,
    },
    props: {
        orderId: {
            type: String,
            required: true,
        },
    },
    setup(props) {
        const service = new OrderService();
        const alertStore = useAlertStore();
        const mainQuery = reactive({
            page: 1,
            search: '',
            sort: '',
        });
        const deboundTime = ref({
            timeOut: null,
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
            actions: [],
            toggleFilters: false,
        });

        const table = reactive({
            headers: {
                product_name: trans('labels.product'),
                product_description: trans('labels.description'),
            },
            sorting: {},
            pagination: {
                meta: null,
                links: null,
            },
            actions: {
                copy: {
                    id: 'copy',
                    name: 'Copy',
                    icon: "fa fa-clone",
                    showName: false,
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
            records: []
        })


        function onTablePageChange(page) {
            mainQuery.page = page;
        }

        function fetchPage(params) {
            table.records = [];
            table.loading = true;
            let query = prepareQuery(params);
            service
                .indexDetail(`/${props.orderId}/details`,query)
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

        function onTableAction(params) {
            switch (params.action.id) {
                case 'copy':
                    handleCopyLink(params.item.product_description)
                    break;
            }
        }

        const handleCopyLink = (text) => {
            const textArea = document.createElement('textarea');
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                document.execCommand('copy');
                notify({
                    text: "Copy thành công",
                    group: "success",
                }, 5000)

            } catch (err) {
                notify({
                    text: "Không thể sao chép vào khay nhớ tạm",
                    group: "failed",
                }, 5000)
            }
            document.body.removeChild(textArea);
        };

        return {
            trans,
            page,
            table,
            onTablePageChange,
            onTableAction,
            mainQuery,
        }

    },
});
</script>
