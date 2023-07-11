<template>
    <Page :title="page.title" :breadcrumbs="page.breadcrumbs" :actions="page.actions" @action="onAction">
        <Panel>
            <Form id="create-category" @submit.prevent="onSubmit">
                <TextInput class="mb-4" type="text" :required="true" error-input="name" name="name" v-model="form.name" :label="trans('users.labels.name')"/>
                <FileInput class="mb-4" name="file" v-model="file" error-input="file" accept="image/*" :label="trans('users.labels.avatar')" @click="clearImage"></FileInput>
                <TextInput class="mb-4" type="textarea" :required="true" :rows="5" name="description" v-model="form.description" error-input="description" :label="trans('labels.description')"/>
                <Dropdown class="mb-4" name="category" error-input="category_id" :required="true" :multiple="true" server="categories" :label="trans('labels.categories')" :placeholder="trans('labels.categories')" :server-search-min-characters="0" v-model="category"></Dropdown>
                <TextInput class="mb-4" type="number" :min="0" :max="999999999999" name="price" v-model="form.price" error-input="price" :label="trans('labels.price')"/>
                <TextInput class="mb-4" type="number" :min="0" :max="999999" name="qty" v-model="form.qty" error-input="qty" :label="trans('labels.qty')"/>
                <TextInput class="mb-4" type="number" :min="0" :max="10000" name="priority" v-model="form.priority" error-input="priority" :label="trans('labels.priority')"/>
                <Toggle class="mb-4" v-model="form.is_active" :checked="form.is_active" error-input="is_active" :label="trans('labels.is_active')"/>
                <div v-if="form.details.length > 0" class="w-full">
                    <span class="text-sm text-gray-500">{{trans('labels.detail')}}</span>
                    <div v-for="(detail, index) in form.details" class="flex flex-row flex-nowrap justify-between items-center mb-4">
                        <TextInput class="w-full" type="text" :min="0" :max="255" :name="'detail'+ index" v-model="form.details[index]" :error-input="'details.' +index"/>
                        <a @click="deleteDetail(index)" class="uppercase cursor-pointer text-lg ml-3 text-danger-400" :title="trans('labels.delete')">
                            <i class="fa fa-trash"></i>
                        </a>
                    </div>
                </div>
                <Button type="button" @click="addDetail()" :label="trans('labels.add_detail')"/>
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
import ProductService from "@/services/ProductService";
import {clearObject, reduceProperties} from "@/helpers/data";
import {toUrl} from "@/helpers/routing";
import Form from "@/views/components/Form";
import {useAlertStore} from "@/stores/alert";
import Toggle from "@/views/components/input/Toggle.vue";
import Spinner from "@/views/components/icons/Spinner.vue";

export default defineComponent({
    components: {Spinner, Toggle, Form, FileInput, Panel, Alert, Dropdown, TextInput, Button, Page},
    setup() {
        const alertStore = useAlertStore();
        const category = ref(null)
        const file = ref(null)
        const form = reactive({
            name: null,
            description: null,
            category_id: null,
            price: 0,
            qty: 100,
            priority: 100,
            is_active: false,
            details: [],
        });

        const page = reactive({
            id: 'create_products',
            title: trans('global.pages.products_create'),
            filters: false,
            breadcrumbs: [
                {
                    name: trans('global.pages.products'),
                    to: toUrl('/products/list'),

                },
                {
                    name: trans('global.pages.products_create'),
                    active: true,
                }
            ],
            actions: [
                {
                    id: 'back',
                    name: trans('global.buttons.back'),
                    icon: "fa fa-angle-left",
                    to: toUrl('/products/list'),
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

        const service = new ProductService();
        function onAction(data) {
            switch(data.action.id) {
                case 'submit':
                    onSubmit();
                    break;
            }
        }

        function onSubmit() {
            if (category.value && category.value.id) {
                form.category_id = category.value.id;
            }
            if (file.value != null) {
                form.file = file.value;
            }

            service.handleCreate('create-product', reduceProperties(form, 'roles', 'id')).then(() => {
                if (alertStore.type == 'success') {
                    clearObject(form)
                    clearData();
                }
            })
            return false;
        }

        function clearImage() {
            file.value = null
            form.file = null
        }

        function addDetail() {
            form.details[form.details.length] = ''
        }
        function deleteDetail(index) {
            if (form.details[index]) {
                form.details.splice(index, 1);
                if (alertStore.errors[`details.${index}`]) {
                    delete alertStore.errors[`details.${index}`]
                }
            }
        }

        function clearData() {
            form.details = [];
            file.value = null
            form.file = null
            category.value = null
        }

        return {
            trans,
            form,
            category,
            file,
            page,
            onSubmit,
            onAction,
            clearImage,
            addDetail,
            deleteDetail,
            clearData,
        }

    }
})
</script>

<style scoped>

</style>
