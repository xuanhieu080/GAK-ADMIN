<template>
    <Page :title="page.title" :breadcrumbs="page.breadcrumbs" :actions="page.actions" @action="onAction">
        <Panel>
            <Form id="create-category" @submit.prevent="onSubmit">
                <TextInput class="mb-4" type="text" :required="true" error-input="name" name="name" v-model="form.name" :label="trans('users.labels.name')"/>
                <FileInput class="mb-4" name="file" v-model="form.file" :required="true" error-input="file" accept="image/*" :label="trans('users.labels.avatar')" @click="form.file = ''"></FileInput>
                <TextInput class="mb-4" type="textarea" :rows="5" name="description" v-model="form.description" error-input="description" :label="trans('labels.description')"/>
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
import CategoryService from "@/services/CategoryService";
import {clearObject, reduceProperties} from "@/helpers/data";
import {toUrl} from "@/helpers/routing";
import Form from "@/views/components/Form";
import {useAlertStore} from "@/stores/alert";

export default defineComponent({
    components: {Form, FileInput, Panel, Alert, Dropdown, TextInput, Button, Page},
    setup() {
        const alertStore = useAlertStore();
        const form = reactive({
            name: '',
            file: '',
            description: '',
        });

        const page = reactive({
            id: 'create_categories',
            title: trans('global.pages.categories_create'),
            filters: false,
            breadcrumbs: [
                {
                    name: trans('global.pages.users'),
                    to: toUrl('/categories/list'),

                },
                {
                    name: trans('global.pages.categories_create'),
                    active: true,
                }
            ],
            actions: [
                {
                    id: 'back',
                    name: trans('global.buttons.back'),
                    icon: "fa fa-angle-left",
                    to: toUrl('/categories/list'),
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

        const service = new CategoryService();

        function onAction(data) {
            switch(data.action.id) {
                case 'submit':
                    onSubmit();
                    break;
            }
        }

        function onSubmit() {
            service.handleCreate('create-category', reduceProperties(form, 'roles', 'id')).then(() => {
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
