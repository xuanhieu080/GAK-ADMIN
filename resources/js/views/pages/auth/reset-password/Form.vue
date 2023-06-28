<template>
    <div>
        <Alert class="mb-4"/>
        <Form id="reset-password-form" @submit.prevent="onFormSubmit">
            <div class="mb-2">
                <TextInput type="email" error-input="email" :required="true" :label="trans('users.labels.email')" name="email" v-model="form.email" class="mb-2"/>
            </div>
            <div class="mb-2">
                <TextInput type="password" error-input="password" :required="true" :label="trans('users.labels.password')" name="password" v-model="form.password" class="mb-2"/>
            </div>
            <div class="mb-4">
                <TextInput type="password" error-input="password_confirmation" :required="true" :label="trans('users.labels.confirm_password')" name="password-confirm" v-model="form.passwordConfirm" class="mb-2"/>
            </div>
            <div class="text-center">
                <Button type="submit" :label="trans('global.buttons.submit')"/>
            </div>
        </Form>
    </div>
</template>

<script>
import AuthService from "@/services/AuthService";
import {reactive, defineComponent} from "vue";
import {useRoute} from "vue-router"
import {trans} from "@/helpers/i18n"
import {useAlertStore} from "@/stores";
import {getResponseError} from "@/helpers/api";
import Button from "@/views/components/input/Button";
import Alert from "@/views/components/Alert";
import Form from "@/views/components/Form";
import TextInput from "@/views/components/input/TextInput.vue";

export default defineComponent({
    name: "ResetPasswordForm",
    components: {
        TextInput,
        Form,
        Alert,
        Button,
    },
    setup() {

        const authService = new AuthService();
        const alertStore = useAlertStore();
        const route = useRoute();
        const form = reactive({
            email: null,
            password: null,
            passwordConfirm: null,
        })

        function onFormSubmit() {
            const payload = {
                email: form.email,
                password: form.password,
                password_confirmation: form.passwordConfirm,
                token: route.query.token,
            };
            authService.resetPassword(payload)
                .then((response) => (alertStore.success(response.data.message)))
                .catch((error) => (alertStore.error(getResponseError(error))));
        }

        return {
            onFormSubmit,
            form,
            trans
        }
    },
});
</script>
