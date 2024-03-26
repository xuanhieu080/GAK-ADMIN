<template>
    <Page :title="page.title" :breadcrumbs="page.breadcrumbs" :actions="page.actions"
          :is-loading="page.loading">
        <Panel>
            <Form id="edit-recharge">
                <TextInput class="mb-4" type="text" :disabled="true"  name="amount" v-model="item.amount" error-input="amount" :label="trans('labels.amount')"/>
                <TextInput class="mb-4" type="text" :disabled="true" name="bank_code" v-model="item.bank_code" error-input="amount" :label="trans('labels.bank')"/>
                <TextInput class="mb-4" type="text" :disabled="true" name="customer_name" v-model="item.customer_name" error-input="amount" :label="trans('labels.customer')"/>
                <TextInput class="mb-4" type="textarea" :rows="5" :disabled="true" name="description" v-model="item.description" error-input="description" :label="trans('labels.description')"/>
            </Form>
        </Panel>
    </Page>
</template>

<script>
import {defineComponent, onBeforeMount, reactive, ref} from "vue";
import {trans} from "@/helpers/i18n";
import {useRoute} from "vue-router";
import {toUrl} from "@/helpers/routing";
import RechargeService from "@/services/RechargeService";
import Button from "@/views/components/input/Button";
import TextInput from "@/views/components/input/TextInput";
import Dropdown from "@/views/components/input/Dropdown";
import Alert from "@/views/components/Alert";
import Panel from "@/views/components/Panel";
import Page from "@/views/layouts/Page";
import Form from "@/views/components/Form";

export default defineComponent({
    components: {
        Form,
        Panel,
        Alert,
        Dropdown,
        TextInput,
        Button,
        Page,
    },
    setup() {
        const route = useRoute();
        const item = ref(null);

        const page = reactive({
            id: 'edit_user',
            title: trans('global.pages.recharges_edit'),
            filters: false,
            loading: true,
            breadcrumbs: [
                {
                    name: trans('global.pages.recharges'),
                    to: toUrl('/recharges/list'),
                },
                {
                    name: trans('global.pages.recharges_edit'),
                    active: true,
                }
            ],
            actions: [
                {
                    id: 'back',
                    name: trans('global.buttons.back'),
                    icon: "fa fa-angle-left",
                    to: toUrl('/recharges/list'),
                    theme: 'outline',
                }
            ]
        });

        const service = new RechargeService();

        onBeforeMount(() => {
            service.edit(route.params.id).then((response) => {
                item.value = response.data.model;
                page.loading = false;
            })
        });

        return {
            trans,
            page,
            item,
        }
    }
})
</script>

<style scoped>

</style>
