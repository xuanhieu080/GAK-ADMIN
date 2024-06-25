<template>
    <Page :title="page.title" :breadcrumbs="page.breadcrumbs" :actions="page.actions" @action="onAction" :is-loading="page.loading">
        <Panel>
            <Form id="edit-customer" @submit.prevent="onSubmit">
                <TextInput class="mb-4" type="text" :required="true" error-input="name" name="name" v-model="form.name" :label="trans('labels.name')"/>
                <TextInput class="mb-4" input-class="bg-gray-200" type="text" :disabled="true" name="code" v-model="customer.code" :label="trans('labels.code')"/>
                <TextInput class="mb-4" input-class="bg-gray-200" type="text" :disabled="true" name="username" v-model="customer.username" :label="trans('labels.username')"/>
                <TextInput class="mb-4" type="email" :required="true" error-input="email" name="email" v-model="form.email" :label="trans('labels.email')"/>
                <TextInput class="mb-4" type="password" name="password" v-model="form.password" error-input="password" :label="trans('labels.password')"/>
            </Form>
        </Panel>
    </Page>
</template>

<script>
import {defineComponent, onBeforeMount, reactive, ref} from "vue";
import {trans} from "@/helpers/i18n";
import {fillObject, reduceProperties} from "@/helpers/data"
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
        const route = useRoute();
        const form = reactive({
            name: '',
            email: '',
            password: '',
        });
        const customer = ref({})

        const page = reactive({
            id: 'edit_customer',
            title: trans('global.pages.users_edit'),
            filters: false,
            loading: true,
            breadcrumbs: [
                {
                    name: trans('global.pages.users'),
                    to: toUrl('/customers/list'),
                },
                {
                    name: trans('global.pages.users_edit'),
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
                    name: trans('global.buttons.update'),
                    icon: "fa fa-save",
                    type: 'submit'
                }
            ]
        });

        const service = new CustomerService();

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
            service.handleUpdate('edit-customer', route.params.id, reduceProperties(form, 'roles', 'id'));
            return false;
        }

        return {
            trans,
            customer,
            form,
            onSubmit,
            onAction,
            page
        }
    }
})
</script>

<style scoped>

</style>
