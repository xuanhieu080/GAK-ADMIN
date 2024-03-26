<template>
  <Page :title="page.title" :breadcrumbs="page.breadcrumbs" :actions="page.actions" @action="onPageAction">

    <template #filters v-if="page.toggleFilters">
      <Filters @clear="onFiltersClear">
        <FiltersRow>
          <FiltersCol>
            <TextInput name="name" label="Tên" v-model="mainQuery.filters.name.value"></TextInput>
          </FiltersCol>
        </FiltersRow>
      </Filters>
    </template>

    <template #default>
      <Table :id="page.id" v-if="table" :headers="table.headers" :sorting="table.sorting" :actions="table.actions"
             :records="table.records" :pagination="table.pagination" :is-loading="table.loading"
             @page-changed="onTablePageChange" @action="onTableAction" @sort="onTableSort"/>
    </template>
  </Page>
</template>

<script setup>

import {trans} from "@/helpers/i18n";
import AttributeGroupService from "@/services/AttributeGroupService";
import {watch, onMounted, defineComponent, reactive, ref} from 'vue';
import {getResponseError, prepareQuery} from "@/helpers/api";
import {toUrl} from "@/helpers/routing";
import {useAlertStore} from "@/stores";
import alertHelpers from "@/helpers/alert";
import Page from "@/views/layouts/Page";
import Table from "@/views/components/Table";
import Filters from "@/views/components/filters/Filters";
import FiltersRow from "@/views/components/filters/FiltersRow";
import FiltersCol from "@/views/components/filters/FiltersCol";
import TextInput from "@/views/components/input/TextInput";

const service = new AttributeGroupService();
const alertStore = useAlertStore();
const mainQuery = reactive({
  page: 1,
  search: '',
  sort: '',
  filters: {
    name: {
      value: '',
      comparison: '='
    }
  }
});

const page = reactive({
  id: 'list-attribute-group',
  title: 'Nhóm thuộc tính',
  breadcrumbs: [
    {
      name: 'Nhóm thuộc tính',
      to: toUrl('/attribute-groups/list'),
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
      to: toUrl('/attribute-groups/create')
    }
  ],
  toggleFilters: false,
});

const table = reactive({
  headers: {
    name: 'Tên',
  },
  sorting: {
    name: true,
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
      to: toUrl('/attribute-groups/{id}/edit')
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
