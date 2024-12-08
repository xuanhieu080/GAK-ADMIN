<template>
  <Page :title="page.title" :breadcrumbs="page.breadcrumbs" :actions="page.actions" @action="onPageAction">

    <template #filters>
      <Filters @clear="onFiltersClear">
        <FiltersRow>
          <FiltersCol>
            <TextInput name="name" :label="trans('labels.name')" v-model="mainQuery.filters.name.value"></TextInput>
          </FiltersCol>
          <FiltersCol>
            <TextInput name="phone" :label="trans('labels.phone')" v-model="mainQuery.filters.phone.value"></TextInput>
          </FiltersCol>
          <FiltersCol>
            <TextInput name="email" :label="trans('labels.email')" v-model="mainQuery.filters.email.value"></TextInput>
          </FiltersCol>
        </FiltersRow>
      </Filters>
    </template>

    <template #default>
      <Table :id="page.id" v-if="table" :headers="table.headers" :sorting="table.sorting" :actions="table.actions"
             :records="table.records" :pagination="table.pagination" :is-loading="table.loading"
             @page-changed="onTablePageChange" @sort="onTableSort"/>
    </template>
  </Page>
</template>

<script setup>

import {trans} from "@/helpers/i18n";
import TicketService from "@/services/TicketService";
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

const service = new TicketService();
const alertStore = useAlertStore();
const mainQuery = reactive({
  page: 1,
  search: '',
  sort: '',
  filters: {
    phone: {
      value: '',
      comparison: '='
    },
    name: {
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
  id: 'list-ticket-group',
  title: 'Đặt may',
  breadcrumbs: [],
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
    name: 'Tên',
    phone: 'Số điện thoại',
    email: 'Email',
    created_at: 'Thời gian',
  },
  pagination: {
    meta: null,
    links: null,
  },
  sorting: {
    name: true,
    email: true,
    phone: true,
    created_at: true,
  },
  actions: {
    edit: {
      id: 'edit',
      name: trans('global.actions.edit'),
      icon: "fa fa-edit",
      showName: false,
      to: toUrl('/tickets/{id}/edit')
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
