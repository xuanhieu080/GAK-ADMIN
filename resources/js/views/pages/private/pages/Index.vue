<template>
  <Page :title="page.title" :breadcrumbs="page.breadcrumbs" :actions="page.actions" @action="onPageAction">

    <template #filters v-if="page.toggleFilters">
      <Filters @clear="onFiltersClear">
        <FiltersRow>
          <FiltersCol>
            <TextInput name="title" :label="trans('labels.title')" v-model="mainQuery.filters.title.value"></TextInput>
          </FiltersCol>
        </FiltersRow>
      </Filters>
    </template>

    <template #default>
      <Table :id="page.id" v-if="table" :headers="table.headers" :sorting="table.sorting" :actions="table.actions"
             :records="table.records" :pagination="table.pagination" :is-loading="table.loading"
             @page-changed="onTablePageChange" @action="onTableAction" @sort="onTableSort">
        <template v-slot:content-is_active="props">
          <Toggle class="mb-4" disabled :model-value="props.item.is_active" :checked="props.item.is_active"/>
        </template>
        <template v-slot:content-show_header="props">
          <Toggle class="mb-4" disabled :model-value="props.item.show_header" :checked="props.item.show_header"/>
        </template>
        <template v-slot:content-is_button="props">
          <Toggle class="mb-4" disabled :model-value="props.item.is_button" :checked="props.item.is_button"/>
        </template>
        <template v-slot:content-group_name="props">
          <span>{{ props.item.group_name }}</span>
        </template>
      </Table>
    </template>
  </Page>
</template>

<script setup>

import {trans} from "@/helpers/i18n";
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
import PageService from "@/services/PageService";

const service = new PageService();
const alertStore = useAlertStore();
const mainQuery = reactive({
  page: 1,
  search: '',
  sort: '',
  filters: {
    title: {
      value: '',
      comparison: '='
    },
  }
});

const page = reactive({
  id: 'list_pages',
  title: 'Trang',
  breadcrumbs: [
    {
      name: 'Trang',
      to: toUrl('/pages/list'),
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
      to: toUrl('/pages/create')
    }
  ],
  toggleFilters: false,
});

const table = reactive({
  headers: {
    id: trans('labels.id_pound'),
    name: 'Tên',
    slug: 'Đường dẫn',
    link: 'Liên kết với button',
    group_name: 'Nhóm',
    is_active: 'Hiển thị',
    show_header: 'Hiển thị ở header',
    is_button: 'Loại button',
  },
  sorting: {
    name: true,
    slug: true,
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
      to: toUrl('/pages/{id}/edit')
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

</script>
