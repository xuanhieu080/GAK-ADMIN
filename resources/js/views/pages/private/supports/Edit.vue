<template>
    <Page :title="page.title" :breadcrumbs="page.breadcrumbs" :actions="page.actions" @action="onAction" :is-loading="page.loading">
        <Panel>
            <Form id="edit-category" @submit.prevent="onSubmit">
                <TextInput class="mb-4" type="text" :required="true" error-input="name" name="name" v-model="form.name" :label="trans('labels.name')"/>
                <TextInput class="mb-4" type="text" :required="true" error-input="phone" name="phone" v-model="form.phone" :label="trans('labels.phone')"/>
                <TextInput class="mb-4" type="text" error-input="zalo" name="zalo" v-model="form.zalo" :label="trans('labels.zalo')"/>
                <TextInput class="mb-4" type="text" error-input="telegram" name="telegram" v-model="form.telegram" :label="trans('labels.telegram')"/>
                <FileInput class="mb-4" name="file" v-model="form.file" error-input="file" accept="image/*" :label="trans('labels.avatar')" @click="form.file = ''"></FileInput>
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
import SupportService from "@/services/SupportService";
import Button from "@/views/components/input/Button";
import TextInput from "@/views/components/input/TextInput";
import Dropdown from "@/views/components/input/Dropdown";
import Alert from "@/views/components/Alert";
import Panel from "@/views/components/Panel";
import Page from "@/views/layouts/Page";
import FileInput from "@/views/components/input/FileInput";
import Form from "@/views/components/Form";
import {useAlertStore} from "@/stores/alert";

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
        const item = ref(null);
        const form = reactive({
            name: '',
            telegram: '',
            zalo: '',
            phone: '',
            description: '',
        });
        const file = ref(null)

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

        const service = new SupportService();

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
            alertStore,
            item,
            file
        }
    }
})
</script>

<style scoped>

</style>
