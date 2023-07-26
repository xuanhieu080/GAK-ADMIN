<template>
    <Page :title="page.title" :breadcrumbs="page.breadcrumbs" :actions="page.actions" @action="onAction" :is-loading="page.loading">
        <Panel>
            <div class="grid grid-cols-2 gap-8">
                <div>
                    <Form id="customer">
                        <TextInput class="mb-4" input-class="bg-gray-200" type="text" name="name"  v-model="customer.name" :label="trans('labels.name')"/>
                        <TextInput class="mb-4" input-class="bg-gray-200" type="text" name="customer-code" v-model="customer.code" :label="trans('labels.code')"/>
                        <TextInput class="mb-4" input-class="bg-gray-200" type="text" name="username" v-model="customer.username" :label="trans('labels.username')"/>
                        <TextInput class="mb-4" input-class="bg-gray-200" type="email" name="email" v-model="customer.email" :label="trans('labels.email')"/>
                    </Form>
                </div>
                <div>
                    <Form id="recharge" @submit.prevent="onSubmit">
                        <Dropdown class="mb-4" name="bank" error-input="bank_id" :required="true" :multiple="false" server="banks" :label="trans('labels.bank')" :placeholder="trans('labels.bank')" :server-search-min-characters="0" v-model="bank"></Dropdown>
                        <TextInput class="mb-4" type="number" :required="true" error-input="amount" name="amount" v-model="form.amount" :label="trans('labels.amount') +' (vnđ)'"/>
                        <TextInput class="mb-4" type="textarea" :rows="5" :required="true" name="description" v-model="form.description" error-input="description" :label="trans('labels.description')"/>
                    </Form>
                </div>
            </div>
        </Panel>
    </Page>
</template>

<script>
import {defineComponent, onBeforeMount, reactive, ref} from "vue";
import {trans} from "@/helpers/i18n";
import {clearObject, fillObject, reduceProperties} from "@/helpers/data"
import {useRoute} from "vue-router";
import {useAuthStore} from "@/stores/auth";
import {toUrl} from "@/helpers/routing";
import CustomerService from "@/services/CustomerService";
import Button from "@/views/components/input/Button";
import TextInput from "@/views/components/input/TextInput";
import Dropdown from "@/views/components/input/Dropdown";
import Alert from "@/views/components/Alert";
import Panel from "@/views/components/Panel";
import Page from "@/views/layouts/Page";
import FileInput from "@/views/components/input/FileInput";
import Form from "@/views/components/Form";
import {useAlertStore} from "@/stores";
import RechargeService from "@/services/RechargeService";

export default defineComponent({
    components: {
        Form,
        FileInput,
        Panel,
        Alert,
        Dropdown,
        TextInput,
        Button,
        Page
    },
    setup() {
        const alertStore = useAlertStore();
        const route = useRoute();
        const bank = ref(null)
        const form = reactive({
            name: '',
            email: '',
            password: '',
        });
        const customer = ref({})

        const page = reactive({
            id: 'edit_customer',
            title: trans('global.pages.recharge'),
            filters: false,
            loading: true,
            breadcrumbs: [
                {
                    name: trans('global.pages.users'),
                    to: toUrl('/customers/list'),
                },
                {
                    name: trans('global.pages.recharge'),
                    active: true,
                }
            ],
            actions: [
                {
                    id: 'back',
                    name: trans('global.buttons.back'),
                    icon: "fa fa-angle-left",
                    to: toUrl('/customers/list'),
                    theme: 'outline',
                },
                {
                    id: 'submit',
                    name: trans('global.buttons.recharge'),
                    icon: "fa fa-save",
                    type: 'submit'
                }
            ]
        });

        const service = new CustomerService();
        const rechargeService = new RechargeService();

        onBeforeMount(() => {
            service.edit(route.params.id).then((response) => {
                fillObject(form, response.data.model);
                customer.value = response.data.model;
                page.loading = false;
            })
        });

        function onAction(data) {
            switch(data.action.id) {
                case 'submit':
                    onSubmit();
                    break;
            }
        }

        function onSubmit() {
            if (bank.value && bank.value.id) {
                form.bank_id = bank.value.id;
            }

            if (customer.value && customer.value.id) {
                form.customer_id = customer.value.id;
            }

            rechargeService.handleCreate('create-recharge', reduceProperties(form, 'roles', 'id')).then(() => {
                if (alertStore.type == 'success') {
                    clearObject(form)
                    clearData();
                }
            })
            return false;
        }

        function clearData() {
            form.amount = null
            form.description = null
            bank.value = null
        }

        return {
            trans,
            customer,
            form,
            onSubmit,
            onAction,
            page,
            bank
        }
    }
})
</script>

<style scoped>

</style>
