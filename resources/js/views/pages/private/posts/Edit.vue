<template>
    <Page :title="page.title" :breadcrumbs="page.breadcrumbs" :actions="page.actions" @action="onAction" :is-loading="page.loading">
        <Panel>
            <Form id="edit-post" @submit.prevent="onSubmit">
                <TextInput class="mb-4" type="text" :required="true" error-input="title" name="title" v-model="form.title" :label="trans('labels.title')"/>
                <FileInput class="mb-4" name="file" :required="true" v-model="form.file" error-input="file" accept="image/*" :label="trans('labels.avatar')" @click="form.file = ''"></FileInput>
                <div class="mb-4">
                    <QuillEditor
                        v-model:content="form.content"
                        :options="options"
                        :toolbar="'full'"
                        contentType="html"
                        :placeholder="trans('labels.content')"
                    />
                </div>
                <Toggle class="mb-4" v-model="form.is_active" :checked="form.is_active" error-input="is_active" :label="trans('labels.is_active')"/>
            </Form>
        </Panel>
        <Comment v-if="item.id && item.comment_count > 0" :post-id="item.id"/>
    </Page>
</template>

<script>
import {defineComponent, onBeforeMount, reactive, ref} from "vue";
import {trans} from "@/helpers/i18n";
import {fillObject, reduceProperties} from "@/helpers/data"
import {useRoute} from "vue-router";
import {toUrl} from "@/helpers/routing";
import PostService from "@/services/PostService";
import Button from "@/views/components/input/Button";
import TextInput from "@/views/components/input/TextInput";
import Dropdown from "@/views/components/input/Dropdown";
import Alert from "@/views/components/Alert";
import Panel from "@/views/components/Panel";
import Page from "@/views/layouts/Page";
import FileInput from "@/views/components/input/FileInput";
import Form from "@/views/components/Form";
import Toggle from "@/views/components/input/Toggle.vue";
import {QuillEditor} from "@vueup/vue-quill";
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import Comment from "@/views/pages/private/posts/Comment.vue";

export default defineComponent({
    components: {
        Comment,
        Toggle,
        Form,
        FileInput,
        Panel,
        Alert,
        Dropdown,
        TextInput,
        Button,
        Page,
        QuillEditor
    },
    setup() {
        const route = useRoute();
        const item = ref(null);
        const form = reactive({
            title: '',
            file: '',
            content: '',
            is_active: false,
        });

        const page = reactive({
            id: 'edit_user',
            title: trans('global.pages.posts_edit'),
            filters: false,
            loading: true,
            breadcrumbs: [
                {
                    name: trans('global.pages.posts'),
                    to: toUrl('/posts/list'),
                },
                {
                    name: trans('global.pages.posts_edit'),
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
                    name: trans('global.buttons.update'),
                    icon: "fa fa-save",
                    type: 'submit'
                }
            ]
        });

        const service = new PostService();

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
            service.handleUpdate('edit-post', route.params.id, reduceProperties(form, 'roles', 'id'));
            return false;
        }

        return {
            trans,
            form,
            onSubmit,
            onAction,
            page,
            item
        }
    }
})
</script>

<style scoped>

</style>
