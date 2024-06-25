<template>
   <div>
      <div class="relative bg-white p-4 w-full shadow border-b border-gray-200 mb-3 sm:rounded-lg">
          <FiltersRow>
              <FiltersCol>
                  <Dropdown name="customers" server="customers" :label="trans('labels.author_comment')" v-model="mainQuery.filters.customer_id.value" :server-search-min-characters="0"></Dropdown>
              </FiltersCol>
          </FiltersRow>
      </div>
       <Table :id="page.id" v-if="table" :headers="table.headers" :sorting="table.sorting" :actions="table.actions" :records="table.records" :pagination="table.pagination" :is-loading="table.loading" @page-changed="onTablePageChange" @action="onTableAction" @sort="onTableSort">
       </Table>
   </div>
</template>

<script>

import {trans} from "@/helpers/i18n";
import CommentService from "@/services/CommentService";
import {watch, onMounted, defineComponent, reactive, ref, defineProps} from 'vue';
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
    props: {
        postId: {
            type: String,
            required: true,
        },
    },
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
    setup(props) {
        const service = new CommentService();
        const alertStore = useAlertStore();
        const mainQuery = reactive({
            page: 1,
            search: '',
            sort: '',
            filters: {
                customer_id: {
                    value: '',
                    comparison: '='
                },
                post_id: {
                    value: props.postId,
                    comparison: '='
                },
            }
        });

        const page = reactive({
            id: 'list_posts',
            title: trans('global.pages.posts'),
            breadcrumbs: [
                {
                    name: trans('global.pages.posts'),
                    to: toUrl('/posts/list'),
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
                    to: toUrl('/posts/create')
                }
            ],
            toggleFilters: false,
        });

        const table = reactive({
            headers: {
                id: trans('labels.id_pound'),
                customer_name: trans('labels.customer_name'),
                content: trans('labels.content'),
                created_at: trans('labels.created_at'),
            },
            sorting: {
                title: true,
            },
            pagination: {
                meta: null,
                links: null,
            },
            actions: {
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
            mainQuery,
            props,
        }

    },
});
</script>
