<template>
  <Page :title="page.title" :breadcrumbs="page.breadcrumbs" :actions="page.actions" @action="onPageAction">
    <template #filters v-if="page.toggleFilters">
      <Filters @clear="onFiltersClear">
        <FiltersRow>
          <FiltersCol>
            <TextInput name="slug" label="Đường dẫn" v-model="mainQuery.filters.slug.value"></TextInput>
          </FiltersCol>
          <FiltersCol>
            <TextInput name="name" :label="trans('labels.name')" v-model="mainQuery.filters.name.value"></TextInput>
          </FiltersCol>
        </FiltersRow>
      </Filters>
    </template>

    <template #default>
      <Table :id="page.id" v-if="table" :headers="table.headers" :sorting="table.sorting" :actions="table.actions"
             :records="table.records" :pagination="table.pagination" :is-loading="table.loading"
             @page-changed="onTablePageChange" @action="onTableAction" @sort="onTableSort">
        <template v-slot:content-id="props">
          <div class="flex items-center">
            <div class="flex-shrink-0 h-10 w-10">
              <img v-if="props.item.image_url" :src="props.item.image_url" class="h-10 w-10 rounded-full" alt=""/>
              <Avatar v-else class="w-10 h-10 text-gray-400 rounded-full"/>
            </div>
          </div>
        </template>
        <template v-slot:content-is_active="props">
          <Toggle class="mb-4" disabled :model-value="props.item.is_active" :checked="props.item.is_active"/>
        </template>
      </Table>
    </template>
  </Page>
</template>

<script>

import {trans} from "@/helpers/i18n";
import CategoryService from "@/services/CategoryService";
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
import Toggle from "@/views/components/input/Toggle.vue";

export default defineComponent({
  components: {
    Toggle,
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
    const service = new CategoryService();
    const alertStore = useAlertStore();
    const mainQuery = reactive({
      page: 1,
      search: '',
      sort: '',
      filters: {
        slug: {
          value: '',
          comparison: '='
        },
        name: {
          value: '',
          comparison: '='
        },
      }
    });

    const page = reactive({
      id: 'list_categories',
      title: trans('global.pages.categories'),
      breadcrumbs: [
        {
          name: trans('global.pages.categories'),
          to: toUrl('/categories/list'),
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
          to: toUrl('/categories/create')
        }
      ],
      toggleFilters: false,
    });

    const table = reactive({
      headers: {
        id: trans('labels.id_pound'),
        slug: 'Đường dẫn',
        name: trans('labels.name'),
        is_active: 'Hiển thị',
      },
      sorting: {
        code: true,
        name: true
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
          to: toUrl('/categories/{id}/edit')
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
      for (let key in clonedFilters) {
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
