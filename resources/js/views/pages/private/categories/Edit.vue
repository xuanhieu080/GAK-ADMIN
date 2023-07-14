<template>
    <Page :title="page.title" :breadcrumbs="page.breadcrumbs" :actions="page.actions" @action="onAction" :is-loading="page.loading">
        <Panel>
            <Form id="edit-category" @submit.prevent="onSubmit">
                <TextInput class="mb-4" type="text" :required="true" error-input="name" name="name" v-model="form.name" :label="trans('labels.name')"/>
                <TextInput class="mb-4" type="text" :disabled="true" name="code" v-model="item.code" :label="trans('labels.code')"/>
                <FileInput class="mb-4" name="file" :required="true" v-model="file" error-input="file" accept="image/*" :label="trans('labels.avatar')" @click="form.file = ''"></FileInput>
                <TextInput class="mb-4" type="textarea" :rows="5" name="description" v-model="form.description" error-input="description" :label="trans('labels.description')"/>
            </Form>
        </Panel>
    </Page>
</template>

<script>
import {defineComponent, onBeforeMount, reactive, ref} from "vue";
import {trans} from "@/helpers/i18n";
import {clearObject, fillObject, reduceProperties} from "@/helpers/data"
import {useRoute} from "vue-router";
import {toUrl} from "@/helpers/routing";
import CategoryService from "@/services/CategoryService";
import Button from "@/views/components/input/Button";
import TextInput from "@/views/components/input/TextInput";
import Dropdown from "@/views/components/input/Dropdown";
import Alert from "@/views/components/Alert";
import Panel from "@/views/components/Panel";
import Page from "@/views/layouts/Page";
import FileInput from "@/views/components/input/FileInput";
import Form from "@/views/components/Form";
import {useAlertStore} from "@/stores";

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
        const item = ref(null);
        const file = ref(null);
        const alertStore = useAlertStore();
        const form = reactive({
            name: '',
            file: '',
            description: '',
        });

        const page = reactive({
            id: 'edit_user',
            title: trans('global.pages.categories_edit'),
            filters: false,
            loading: true,
            breadcrumbs: [
                {
                    name: trans('global.pages.categories'),
                    to: toUrl('/categories/list'),
                },
                {
                    name: trans('global.pages.categories_edit'),
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
                    name: trans('global.buttons.update'),
                    icon: "fa fa-save",
                    type: 'submit'
                }
            ]
        });

        const service = new CategoryService();

        onBeforeMount(() => {
            service.edit(route.params.id).then((response) => {
                fillObject(form, response.data.model);
                item.value = response.data.model;
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
            if (file.value != null) {
                form.file = file.value;
            }
            service.handleUpdate('edit-category', route.params.id, reduceProperties(form, 'roles', 'id')).then((response) => {
                if (alertStore.type == 'success') {
                    clearObject(form)
                    fillObject(form, response.data.model);
                    item.value = response.data.model;
                    file.value = null;
                    page.loading = false;
                }
            });
            return false;
        }

        return {
            trans,
            form,
            onSubmit,
            onAction,
            page,
            file,
            alertStore,
            item
        }
    }
})
</script>

<style scoped>

</style>
