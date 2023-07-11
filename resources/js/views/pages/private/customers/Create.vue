<template>
    <Page :title="page.title" :breadcrumbs="page.breadcrumbs" :actions="page.actions" @action="onAction">
        <Panel>
            <Form id="create-customer" @submit.prevent="onSubmit">
                <TextInput class="mb-4" type="text" :required="true" name="first_name" v-model="form.name" error-input="name" :label="trans('users.labels.name')"/>
                <TextInput class="mb-4" type="text" :required="true" name="username" v-model="form.username" error-input="username" :label="trans('users.labels.username')"/>
                <TextInput class="mb-4" type="email" :required="true" name="email" v-model="form.email" error-input="email" :label="trans('users.labels.email')"/>
                <TextInput class="mb-4" type="password" :required="true" name="password" v-model="form.password" error-input="password" :label="trans('users.labels.password')"/>
            </Form>
        </Panel>
    </Page>
</template>

<script>
import {defineComponent, reactive} from "vue";
import {trans} from "@/helpers/i18n";
import Button from "@/views/components/input/Button";
import TextInput from "@/views/components/input/TextInput";
import Dropdown from "@/views/components/input/Dropdown";
import Alert from "@/views/components/Alert";
import Panel from "@/views/components/Panel";
import Page from "@/views/layouts/Page";
import FileInput from "@/views/components/input/FileInput";
import CustomerService from "@/services/CustomerService";
import {toUrl} from "@/helpers/routing";
import Form from "@/views/components/Form";
import {clearObject} from "@/helpers/data";
import {useAlertStore} from "@/stores";

export default defineComponent({
    components: {Form, FileInput, Panel, Alert, Dropdown, TextInput, Button, Page},
    setup() {
        const alertStore = useAlertStore();
        const form = reactive({
            username: '',
            name: '',
            email: '',
            password: '',
        });

        const page = reactive({
            id: 'create_customers',
            title: trans('global.pages.customers_create'),
            filters: false,
            breadcrumbs: [
                {
                    name: trans('global.pages.customers'),
                    to: toUrl('/customers/list'),

                },
                {
                    name: trans('global.pages.customers_create'),
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
                    name: trans('global.buttons.save'),
                    icon: "fa fa-save",
                    type: 'submit',
                }
            ]
        });

        const service = new CustomerService();

        function onAction(data) {
            switch(data.action.id) {
                case 'submit':
                    onSubmit();
                    break;
            }
        }

        function onSubmit() {
            service.handleCreate('create-customer', form).then(() => {
                if (alertStore.type == 'success') {
                    clearObject(form)
                }
            })
            return false;
        }

        return {
            trans,
            form,
            page,
            onSubmit,
            onAction,
        }
    }
})
</script>

<style scoped>

</style>
