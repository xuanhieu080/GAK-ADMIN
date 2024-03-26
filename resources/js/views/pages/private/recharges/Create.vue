<template>
    <Page :title="page.title" :breadcrumbs="page.breadcrumbs" :actions="page.actions" @action="onAction">
        <Panel>
            <Form id="create-recharge" @submit.prevent="onSubmit">
                <TextInput class="mb-4" type="number" :required="true" name="amount" v-model="form.amount" error-input="amount" :label="trans('labels.amount')" :min="0" :max="999999999"/>
                <Dropdown class="mb-4" name="bank" error-input="bank_id" :required="true" :multiple="false" server="banks" :label="trans('labels.bank')" :placeholder="trans('labels.bank')" :server-search-min-characters="0" v-model="bank"></Dropdown>
                <Dropdown class="mb-4" name="customer" error-input="customer_id" :required="true" :multiple="false" server="customers" :label="trans('labels.customer')" :placeholder="trans('labels.customer')" :server-search-min-characters="0" v-model="customer"></Dropdown>
                <TextInput class="mb-4" type="textarea" :rows="5" :required="true" name="description" v-model="form.description" error-input="description" :label="trans('labels.description')"/>
            </Form>
        </Panel>
    </Page>
</template>

<script>
import {defineComponent, reactive, ref} from "vue";
import {trans} from "@/helpers/i18n";
import Button from "@/views/components/input/Button";
import TextInput from "@/views/components/input/TextInput";
import Dropdown from "@/views/components/input/Dropdown";
import Alert from "@/views/components/Alert";
import Panel from "@/views/components/Panel";
import Page from "@/views/layouts/Page";
import FileInput from "@/views/components/input/FileInput";
import rechargeService from "@/services/RechargeService";
import {clearObject, reduceProperties} from "@/helpers/data";
import {toUrl} from "@/helpers/routing";
import Form from "@/views/components/Form";
import {useAlertStore} from "@/stores/alert";
import Toggle from "@/views/components/input/Toggle.vue";
import Spinner from "@/views/components/icons/Spinner.vue";
import RechargeService from "@/services/RechargeService";

export default defineComponent({
    components: {Spinner, Toggle, Form, FileInput, Panel, Alert, Dropdown, TextInput, Button, Page},
    setup() {
        const alertStore = useAlertStore();
        const bank = ref(null)
        const customer = ref(null)
        const form = reactive({
            amount: null,
        });

        const page = reactive({
            id: 'create_recharges',
            title: trans('global.pages.recharges_create'),
            filters: false,
            breadcrumbs: [
                {
                    name: trans('global.pages.recharges'),
                    to: toUrl('/recharges/list'),

                },
                {
                    name: trans('global.pages.recharges_create'),
                    active: true,
                }
            ],
            actions: [
                {
                    id: 'back',
                    name: trans('global.buttons.back'),
                    icon: "fa fa-angle-left",
                    to: toUrl('/recharges/list'),
                    theme: 'outline',
                },
                {
                    id: 'submit',
                    name: trans('global.buttons.save'),
                    icon: "fa fa-save",
                    type: 'submit',
                }
            ]
        });

        const service = new rechargeService();
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

            service.handleCreate('create-recharge', reduceProperties(form, 'roles', 'id')).then(() => {
                if (alertStore.type == 'success') {
                    clearObject(form)
                    clearData();
                }
            })
            return false;
        }

        function addDetail() {
            form.details[form.details.length] = ''
        }

        function clearData() {
            form.amount = null
            bank.value = null
            customer.value = null
        }

        return {
            trans,
            form,
            bank,
            customer,
            page,
            onSubmit,
            onAction,
            addDetail,
            clearData,
        }

    }
})
</script>

<style scoped>

</style>
