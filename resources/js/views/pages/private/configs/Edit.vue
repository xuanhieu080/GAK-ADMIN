<template>
    <Page :title="page.title" :breadcrumbs="page.breadcrumbs" :actions="page.actions" @action="onAction" :is-loading="page.loading">
        <Panel>
            <Form id="edit-config" @submit.prevent="onSubmit">
                <TextInput class="mb-4" type="text" :disabled="true" name="code" v-model="item.code" :label="trans('labels.name')"/>
                <FileInput v-if="item.is_file" class="mb-4" name="file" :required="true" v-model="file" error-input="file" accept="image/*" :label="trans('labels.avatar')" @click="form.file = ''"></FileInput>
                <TextInput v-else class="mb-4" type="text" :required="true" error-input="value" name="value" v-model="form.value" :label="trans('labels.value')"/>
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
import ConfigService from "@/services/ConfigService";
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
            value: '',
        });

        const page = reactive({
            id: 'edit_user',
            title: trans('global.pages.configs_edit'),
            filters: false,
            loading: true,
            breadcrumbs: [
                {
                    name: trans('global.pages.configs'),
                    to: toUrl('/configs/list'),
                },
                {
                    name: trans('global.pages.configs_edit'),
                    active: true,
                }
            ],
            actions: [
                {
                    id: 'back',
                    name: trans('global.buttons.back'),
                    icon: "fa fa-angle-left",
                    to: toUrl('/configs/list'),
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

        const service = new ConfigService();

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
            if (item.value.is_file && file.value != null) {
                form.file = file.value;
            }
            service.handleUpdate('edit-config', route.params.id, reduceProperties(form, 'roles', 'id')).then((response) => {
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
