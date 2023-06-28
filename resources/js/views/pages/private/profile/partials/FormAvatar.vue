<template>
    <Panel :title="trans('users.labels.avatar_settings')">
        <Form @submit.prevent="onSubmit">
            <FileInput name="file" :label="trans('users.labels.avatar')" v-model="form.file"
                       :required="false"
                       error-input="avatar"
                       @clear="form.file = ''"
                       accept="image/*"
                       class="mb-4"></FileInput>
            <Button type="submit" :label="trans('global.buttons.upload')"/>
        </Form>
    </Panel>
</template>

<script>
import {reactive, defineComponent} from "vue";
import {useAlertStore, useAuthStore} from "@/stores";
import {trans} from "@/helpers/i18n";
import Button from "@/views/components/input/Button";
import FileInput from "@/views/components/input/FileInput";
import Panel from "@/views/components/Panel";
import Form from "@/views/components/Form.vue";

export default defineComponent({
    emits: ['done', 'error'],
    components: {
        Form,
        Panel,
        FileInput,
        Button
    },
    setup(props, {emit}) {

        const alertStore = useAlertStore();
        const authStore = useAuthStore();
        const form = reactive({
            file: null,
        })

        function onChange(event) {
            alertStore.clear();
            form.file = event.target.files[0];
        }

        function onSubmit() {
            authStore.updateAvatar(authStore.user.id, {'avatar': form.file}).then(() => {
                emit('done');
            }).catch((error) => {
                emit('error');
            });
        }

        return {
            onSubmit,
            onChange,
            form,
            trans
        }
    }
});
</script>
