<template>
    <div :style="style" :class="$props.class">
        <label
            :for="name"
            class="text-sm text-gray-500"
            :class="{ 'sr-only': !showLabel }"
            v-if="label">
            {{ label }}<span class="text-red-600" v-if="$props.required">*</span>
        </label>
        <input v-if="type !== 'textarea'"
               :id="name"
               :type="type"
               :value="modelValue"
               :required="required"
               :disabled="disabled"
               @input="onInput"
               :placeholder="placeholder"
               :autocomplete="autocomplete"
               :class="inputClass"
               :min="min"
               :max="max"
               class="block w-full px-3 py-2 placeholder-gray-400 border border-gray-300 rounded-md shadow-sm appearance-none focus:outline-none focus:ring-theme-500 focus:border-theme-500 text-sm"/>
        <textarea v-else
            :id="name"
            :value="modelValue"
            :required="required"
            :disabled="disabled"
            @input="onInput"
            :placeholder="placeholder"
            :autocomplete="autocomplete"
            :class="inputClass"
            :rows="rows"
            class="block w-full px-3 py-2 placeholder-gray-400 border border-gray-300 rounded-md shadow-sm appearance-none focus:outline-none focus:ring-theme-500 focus:border-theme-500 text-sm"></textarea>
        <span v-if="errorInput && errorStore.errors[errorInput]" class="text-xs tracking-wide text-red-600">{{
                errorStore.errors[errorInput][0]
            }}</span>
    </div>
</template>

<script>
import {defineComponent} from "vue";
import {useAlertStore} from "@/stores";

export default defineComponent({
    inheritAttrs: false,
    props: {
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
        type: {
            type: String,
            default: "text",
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
        placeholder: {
            type: String,
            default: null,
        },
        autocomplete: {
            type: String,
            default: null,
        },
        rows: {
            type: Number,
            default: 2,
        },
        errorInput: {
            type: String,
        },
        min: {
            type: Number,
        },
        max: {
            type: Number,
        }
    },
    emits: ['update:modelValue'],
    setup(props, {emit}) {

        const errorStore = useAlertStore();
        function onInput(event) {
            emit("update:modelValue", event.target.value);
        }

        return {
            onInput,
            errorStore
        }
    }
});
</script>
