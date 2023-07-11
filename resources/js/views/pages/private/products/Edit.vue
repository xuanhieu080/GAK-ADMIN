<template>
    <Page :title="page.title" :breadcrumbs="page.breadcrumbs" :actions="page.actions" @action="onAction"
          :is-loading="page.loading">
        <Panel>
            <Form id="edit-product" @submit.prevent="onSubmit">
                <TextInput class="mb-4" type="text" :required="true" error-input="name" name="name" v-model="form.name"
                           :label="trans('users.labels.name')"/>
                <FileInput class="mb-4" name="file" :multiple="true" v-model="file" :required="true" error-input="file"
                           accept="image/*" :label="trans('users.labels.avatar')" @clear="clearImage"></FileInput>
                <TextInput class="mb-4" type="textarea" :rows="5" name="description" v-model="form.description"
                           error-input="description" :label="trans('labels.description')"/>
                <Dropdown class="mb-4" name="category" :multiple="true" server="categories"
                          :label="trans('labels.categories')" :placeholder="trans('labels.categories')"
                          :server-search-min-characters="0" v-model="category"></Dropdown>
                <TextInput class="mb-4" type="number" :min="0" :max="999999999999" name="price" v-model="form.price"
                           error-input="price" :label="trans('labels.price')"/>
                <TextInput class="mb-4" type="number" :min="0" :max="999999" name="qty" v-model="form.qty"
                           error-input="qty" :label="trans('labels.qty')"/>
                <TextInput class="mb-4" type="number" :min="0" :max="10000" name="priority" v-model="form.priority"
                           error-input="priority" :label="trans('labels.priority')"/>
                <Toggle class="mb-4" v-model="form.is_active" :checked="form.is_active" error-input="is_active"
                        :label="trans('labels.is_active')"/>
                <div v-if="form.details.length > 0 || form.detail_currents.length > 0" class="w-full">
                    <span class="text-sm text-gray-500">{{ trans('labels.detail') }}</span>
                    <div v-for="(detail, index) in form.detail_currents"
                         class="flex flex-row flex-nowrap justify-between items-center mb-4">
                        <TextInput class="w-full" type="text" :min="0" :max="255" :name="'detail-current'+ index"
                                   v-model="form.detail_currents[index].description" :error-input="'detail_currents[' +index +']'"/>
                        <TextInput class="w-full d-none" type="text" :min="0" :max="255" :name="'detail-current-id-'+ index"
                                   v-model="form.detail_currents[index].id" :error-input="'detail_currents.' +index"/>
                        <a @click="deleteDetail(index,'current')" class="uppercase cursor-pointer text-lg ml-3 text-danger-400"
                           :title="trans('labels.delete')">
                            <i class="fa fa-trash"></i>
                        </a>
                    </div>
                    <div v-for="(detail, index) in form.details"
                         class="flex flex-row flex-nowrap justify-between items-center mb-4">
                        <TextInput class="w-full" type="text" :min="0" :max="255" :name="'detail'+ index"
                                   v-model="form.details[index]" :error-input="'details.' +index"/>
                        <a @click="deleteDetail(index)" class="uppercase cursor-pointer text-lg ml-3 text-danger-400"
                           :title="trans('labels.delete')">
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
import {defineComponent, onBeforeMount, reactive, ref} from "vue";
import {trans} from "@/helpers/i18n";
import {clearObject, fillObject, reduceProperties} from "@/helpers/data"
import {useRoute} from "vue-router";
import {toUrl} from "@/helpers/routing";
import ProductService from "@/services/ProductService";
import Button from "@/views/components/input/Button";
import TextInput from "@/views/components/input/TextInput";
import Dropdown from "@/views/components/input/Dropdown";
import Alert from "@/views/components/Alert";
import Panel from "@/views/components/Panel";
import Page from "@/views/layouts/Page";
import FileInput from "@/views/components/input/FileInput";
import Form from "@/views/components/Form";
import Toggle from "@/views/components/input/Toggle.vue";
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
        Page,
        Toggle,
    },
    setup() {
        const alertStore = useAlertStore();
        const route = useRoute();
        const item = ref(null);
        const file = ref(null);
        const category = ref(null);
        const form = reactive({
            name: null,
            description: null,
            category_id: null,
            price: 0,
            qty: 100,
            priority: 100,
            is_active: false,
            details: [],
            detail_currents: [],
        });

        const page = reactive({
            id: 'edit_user',
            title: trans('global.pages.products_edit'),
            filters: false,
            loading: true,
            breadcrumbs: [
                {
                    name: trans('global.pages.products'),
                    to: toUrl('/products/list'),
                },
                {
                    name: trans('global.pages.products_edit'),
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
                    name: trans('global.buttons.update'),
                    icon: "fa fa-save",
                    type: 'submit'
                }
            ]
        });

        const service = new ProductService();

        onBeforeMount(() => {
            service.edit(route.params.id).then((response) => {
                fillObject(form, response.data.model);
                item.value = response.data.model;
                category.value = {
                    'id': response.data.model.category_id,
                    'title': response.data.model.category_name
                };

                form.detail_currents = response.data.model.details
                form.details = []
                page.loading = false;
            })
        });

        function onAction(data) {
            switch (data.action.id) {
                case 'submit':
                    onSubmit();
                    break;
            }
        }

        function onSubmit() {
            if (category.value && category.value.id) {
                form.category_id = category.value.id;
            }

            if (file.value !== null) {
                form.file = file.value;
            }
            service.handleUpdate('edit-product', route.params.id, reduceProperties(form, 'roles', 'id')).then((response) => {
                if (alertStore.type == 'success') {
                    clearObject(form)
                    clearData();
                    fillObject(form, response.data.model);
                    item.value = response.data.model;
                    category.value = {
                        'id': response.data.model.category_id,
                        'title': response.data.model.category_name
                    };

                    form.detail_currents = response.data.model.details
                    form.details = []
                    page.loading = false;
                }
            });
            return false;
        }

        function clearImage() {
            file.value = null
            form.file = null
        }

        function addDetail() {
            form.details[form.details.length] = ''
        }

        function deleteDetail(index, type = 'add') {
            if (type == 'add') {
                if (form.details[index]) {
                    form.details.splice(index, 1);
                    if (alertStore.errors[`details.${index}`]) {
                        delete alertStore.errors[`details.${index}`]
                    }
                }
            } else {
                if (form.detail_currents[index]) {
                    form.detail_currents.splice(index, 1);
                    if (alertStore.errors[`detail_currents.${index}`]) {
                        delete alertStore.errors[`detail_currents.${index}`]
                    }
                }
            }
        }


        function clearData() {
            form.details = [];
            form.detail_currents = [];
            file.value = null
            delete form.file
            category.value = null
        }

        return {
            trans,
            form,
            onSubmit,
            onAction,
            page,
            item,
            category,
            file,
            clearImage,
            addDetail,
            deleteDetail,
        }
    }
})
</script>

<style scoped>

</style>
