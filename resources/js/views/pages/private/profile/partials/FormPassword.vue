<template>
    <Panel :title="trans('users.labels.password_settings')">
        <form @submit.prevent="onFormSubmit">
            <div class="mb-2">
                <TextInput type="password" name="current-password" :required="true" :label="trans('users.labels.current_password')" error-input="current_password" v-model="form.currentPassword" class="mb-4"/>
            </div>
            <div class="mb-2">
                <TextInput type="password" name="password" :required="true" :label="trans('users.labels.new_password')" error-input="password" v-model="form.password" class="mb-4"/>
            </div>
            <div class="mb-4">
                <TextInput type="password" name="password-confirm" :required="true" :label="trans('users.labels.confirm_password')" error-input="password_confirmation" v-model="form.passwordConfirm" class="mb-4"/>
            </div>
            <Button type="submit" :label="trans('global.buttons.update')"/>
        </form>
    </Panel>
</template>

<script>

import AuthService from "@/services/AuthService";
import {trans} from "@/helpers/i18n";
import {reactive, defineComponent} from "vue";
import {useAlertStore} from "@/stores";
import {getResponseError} from "@/helpers/api";
import Button from "@/views/components/input/Button";
import Panel from "@/views/components/Panel";
import TextInput from "@/views/components/input/TextInput.vue";

export default defineComponent({
    components: {
        TextInput,
        Panel,
        Button,
    },
    setup() {

        const authService = new AuthService();
        const alertStore = useAlertStore();
        const form = reactive({
            currentPassword: null,
            password: null,
            passwordConfirm: null,
        })

        function onFormSubmit() {
            const payload = {
                current_password: form.currentPassword,
                password: form.password,
                password_confirmation: form.passwordConfirm,
            };
            authService.updatePassword(payload)
                .then((response) => (alertStore.success(trans('global.phrases.password_updated'))))
                .catch((error) => (alertStore.error(getResponseError(error))));
        }

        return {
            onFormSubmit,
            form,
            trans
        }
    }
});
</script>
