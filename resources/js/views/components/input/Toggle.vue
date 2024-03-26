<script setup lang="ts">
import {defineEmits, defineProps, ref, watch} from "vue";
import {useAlertStore} from "@/stores/alert";

const emit = defineEmits(["update:modelValue"]);

const props = defineProps({
    class: String,
    inputClass: String,
    style: [String, Object],
    name: {
        type: String,
        required: true,
    },
    label: {
        type: String,
        default: "",
    },
    modelValue: {
        default: "",
        type: [String, Number],
    },
    showLabel: {
        type: Boolean,
        default: true,
    },
    required: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    checked: {
        type: Boolean,
        default: false,
    },
    errorInput: {
        type: String,
    }
});

const checkValue = ref(props.checked)

const errorStore = useAlertStore();

function onInput() {
    checkValue.value = !checkValue.value;
    emit("update:modelValue", checkValue.value);
}

watch(() =>props.checked, () => {checkValue.value = props.checked})
</script>

<template>
    <div>
        <div :style="style" :class="$props.class">
            <label class="relative inline-flex items-center cursor-pointer">
                <input
                    :id="name"
                    type="checkbox"
                    :value="modelValue"
                    :required="required"
                    :disabled="disabled"
                    @input="onInput"
                    :class="inputClass"
                    :checked="checkValue"
                    class="sr-only peer"
                >
                <div
                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                <label
                    :for="name"
                    class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300"
                    :class="{ 'sr-only': !showLabel }"
                    v-if="label">
                    {{ label }}
                </label>
            </label>
        </div>
        <span v-if="errorInput && errorStore.errors[errorInput]" class="text-xs tracking-wide text-red-600">{{
                errorStore.errors[errorInput][0]
            }}</span>
    </div>
</template>

<style scoped>

</style>
