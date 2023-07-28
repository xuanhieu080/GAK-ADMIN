<template>
    <Page :title="page.title" :breadcrumbs="page.breadcrumbs" :actions="page.actions" @action="onAction">
        <Panel>
            <Form id="create-post" @submit.prevent="onSubmit">
                <TextInput class="mb-4" type="text" :required="true" error-input="title" name="title" v-model="form.title" :label="trans('labels.title')"/>
                <FileInput class="mb-4" name="file" v-model="form.file" error-input="file" accept="image/*" :label="trans('labels.avatar')" @click="form.file = ''"></FileInput>
                <div class="mb-4">
                    <QuillEditor
                        v-model:content="form.content"
                        :options="options"
                        :toolbar="'full'"
                        contentType="html"
                        :placeholder="trans('labels.content')"
                    />
                    <span v-if="alertStore.errors['content']" class="text-xs tracking-wide text-red-600">{{
                            alertStore.errors['content'][0]
                        }}</span>
                </div>
                <Toggle class="mb-4" v-model="form.is_active" :checked="form.is_active" error-input="is_active" :label="trans('labels.show')"/>
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
import PostService from "@/services/PostService";
import {clearObject, reduceProperties} from "@/helpers/data";
import {toUrl} from "@/helpers/routing";
import Form from "@/views/components/Form";
import {useAlertStore} from "@/stores/alert";
import Toggle from "@/views/components/input/Toggle.vue";
import {QuillEditor} from "@vueup/vue-quill";
import '@vueup/vue-quill/dist/vue-quill.snow.css';

export default defineComponent({
    components: {QuillEditor, Toggle, Form, FileInput, Panel, Alert, Dropdown, TextInput, Button, Page},
    setup() {
        const alertStore = useAlertStore();
        const form = reactive({
            title: '',
            file: '',
            content: '',
            is_active: false,
        });

        const options = ref({
            debug: 'info',
            toolbar: 'full',
            theme: 'snow',
            contentType: 'html',
        });

        const page = reactive({
            id: 'create_posts',
            title: trans('global.pages.posts_create'),
            filters: false,
            breadcrumbs: [
                {
                    name: trans('global.pages.posts'),
                    to: toUrl('/posts/list'),

                },
                {
                    name: trans('global.pages.posts_create'),
                    active: true,
                }
            ],
            actions: [
                {
                    id: 'back',
                    name: trans('global.buttons.back'),
                    icon: "fa fa-angle-left",
                    to: toUrl('/posts/list'),
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

        const service = new PostService();

        function onAction(data) {
            switch(data.action.id) {
                case 'submit':
                    onSubmit();
                    break;
            }
        }

        function onSubmit() {
            service.handleCreate('create-post', reduceProperties(form, 'roles', 'id')).then(() => {
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
            options,
            alertStore
        }
    }
})
</script>

<style scoped>

</style>
<style>
.ql-editor {
    min-height: 200px;
}
</style>
