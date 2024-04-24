<template>
    <div :style="style" :class="$props.class">
        <label :for="name" class="text-sm text-gray-500" :class="{ 'sr-only': !showLabel }" v-if="label">
            {{ label }}<span class="text-red-600" v-if="$props.required">*</span>
        </label>
        <Multiselect track-by="id" :label="labelName" v-model="value" :id="$props.name"
                     :name="$props.name"
                     :disabled="disabled"
                     :placeholder="$props.placeholder"
                     :options="selectOptions"
                     :searchable="!!$props.server"
                     :loading="isLoading"
                     :internal-search="false"
                     :clear-on-select="true"
                     :close-on-select="true"
                     :max-height="400"
                     :show-no-results="false"
                     :hide-selected="false"
                     :filter-results="true"
                     :create-option="true"
                     :select-label="'Chọn'"
                     :deselect-label="'Chọn để xoá'"
                     :preserveSearch="true"
                     :showLabels="true"
                     :allow-empty="true"
                     open-direction="bottom" @search-change="handleSearch">
        </Multiselect>
        <span v-if="errorInput && errorStore.errors[errorInput]" class="text-xs tracking-wide text-red-600">{{
                errorStore.errors[errorInput][0]
            }}</span>
    </div>
</template>

<script>

import {computed, defineComponent, ref, watch} from "vue";
import {useAlertStore} from "@/stores";

import SearchService from "@/services/SearchService";
import Multiselect from 'vue-multiselect';

export default defineComponent({
    components: {Multiselect},
    inheritAttrs: false,
    props: {
        class: String,
        style: [String, Object],
        name: {
            type: String,
            required: true,
        },
        labelName: {
            type: String,
            default: 'title',
        },
        options: {
            required: false,
        },
        label: {
            type: String,
            default: "",
        },
        modelValue: {
            type: [Object, String],
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
            default: false
        },
        placeholder: {
            type: String,
            default: null,
        },
        multiple: {
            type: [Boolean, String, Number],
            default: false,
        },
        server: {
            type: String,
            default: null,
        },
        serverPerPage: {
            type: Number,
            default: 20
        },
        serverSearchMinCharacters: {
            type: Number,
            default: 3
        },
        errorInput: {
            type: String,
        },
        params: {
            type: String,
        }
    },
    emits: ['update:modelValue', 'input'],
    setup(props, {emit}) {
        const errorStore = useAlertStore();
        let selectOptionsArr = ref(props.options);
        let isLoading = ref(false);

        const selectOptions = computed(() => {
            let val = [];
            for (let i in selectOptionsArr.value) {
                if (typeof selectOptionsArr.value[i] === 'object') {
                    val.push(selectOptionsArr.value[i]);
                } else {
                    val.push(selectOptionsArr.value[i])
                }
            }
            return val;
        });

        const value = computed({
            get() {
                return props.modelValue;
            },
            set(newValue) {
                emit("update:modelValue", newValue);
                emit('input', value);
            },
        })

        function handleSearch(search = '') {
            if (!props.server) {
                return;
            }
            if(search.length < props.serverSearchMinCharacters) {
                return;
            }
            const service = new SearchService(props.server);
            isLoading.value = true;
            service.begin(search, 1, props.serverPerPage, props.params).then((response) => {
                selectOptionsArr.value = [];
                for (let i in response.data.data) {
                    selectOptionsArr.value.push(response.data.data[i]);
                }
                isLoading.value = false;
            }).catch((error) => {
                isLoading.value = false;
            })
        }

        handleSearch();

      watch(props, (newTableState) => {
        if (newTableState.params) {
          handleSearch();
        }
      });
        return {
            value,
            selectOptions,
            handleSearch,
            isLoading,
            errorStore
        }
    }
});
</script>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>
<style lang="scss">
.multiselect__input {
    max-width: 300px !important;
    border: 0 !important;
}
.multiselect__input:focus, .multiselect__input:active, .multiselect__input:hover {
    border: 0 !important;
    outline: none !important;
    outline-offset: 0 !important;
    box-shadow: none !important;
}
</style>
