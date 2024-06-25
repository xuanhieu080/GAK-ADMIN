<template>
  <div :class="$props.class">
    <label class="text-sm text-gray-500" :class="{ 'sr-only': !$props.showLabel }"
           v-if="$props.showLabel && $props.label">
      {{ $props.label }}<span class="text-red-600" v-if="$props.required">*</span>
    </label>
    <div :style="imgStyle" :class="classes" @click="onClick" @mouseover="hover = true" @mouseleave="hover = false"
         @drop.prevent="onDrop" @dragover.prevent="hover = true" @dragleave.prevent="hover = false">
      <button class="file-input__clear" type="button" @click.stop="onClear" v-if="canClear">
        <i class="fa fa-times"></i></button>
      <div v-if="!files.length">
        <p class="text-2xl"><i class="fa fa-cloud-upload"></i></p>
        <p class="whitespace-normal">{{ placeholderMessage }}</p>
      </div>
      <div v-else>
        <template v-if="$props.multiple">
          <span>{{ trans('global.phrases.input_files_selected', {count: files.length}) }}</span>
          <div v-for="(image, index) in imagePreviews" :key="index">
            <img :src="image" alt="Preview">
          </div>
        </template>
        <template v-else>
          <img v-if="imagePreview" :src="imagePreview" alt="Preview">
        </template>
      </div>
      <input type="file" ref="input" :required="required" :disabled="disabled" :multiple="multiple"
             :accept="$props.accept" @input="onChange" @change="previewImages"/>
    </div>
    <span v-if="errorInput && errorStore.errors[errorInput]" class="text-xs tracking-wide text-red-600">{{
        errorStore.errors[errorInput][0]
      }}</span>
  </div>
</template>

<script>
import {computed, defineComponent, ref, watch} from "vue";
import {trans} from "@/helpers/i18n";
import {useAlertStore} from "@/stores";

export default defineComponent({
  props: {
    class: String,
    style: [String, Object],
    imgStyle: [String, Object],
    name: {
      type: String,
      required: true,
    },
    label: {
      type: String,
      default: "",
    },
    showLabel: {
      type: Boolean,
      default: true,
    },
    placeholder: {
      type: String,
      default: "",
    },
    modelValue: {
      default: null,
      type: [Array, Object, String],
    },
    multiple: {
      type: [Boolean, String],
      default: false,
    },
    required: {
      type: Boolean,
      default: false,
    },
    disabled: {
      type: Boolean,
      default: false,
    },
    accept: {
      type: String,
      default: '*/*'
    },
    errorInput: {
      type: String,
    }
  },
  emits: ['update:modelValue', 'change', 'click', 'input', 'error', 'clear'],
  setup(props, {emit}) {
    const errorStore = useAlertStore();
    let hover = ref(false);

    const input = ref(null);
    const imagePreview = ref(null);
    const imagePreviews = ref([]);

    const placeholderMessage = computed(() => {
      if (props.placeholder) {
        return props.placeholder;
      } else {
        return trans('global.phrases.input_files_select', {count: props.multiple ? 2 : 1});
      }
    })

    const files = computed(() => {
      if (props.modelValue) {
        if (Array.isArray(props.modelValue)) {
          return Array.from(props.modelValue);
        } else {
          return [props.modelValue];
        }
      }
      return [];
    })

    const classes = computed(() => {
      let classes = ['file-input', 'text-gray-600', 'rounded-md'];
      var isError = props.disabled && hover.value;

      if (isError) {
        classes.push('cursor-not-allowed');
        classes.push('bg-red-50');
        classes.push('border-red-500');
      } else if (props.disabled) {
        classes.push('disabled');
        classes.push('cursor-default');
        classes.push('bg-gray-50');
        classes.push('border-gray-500')
      } else {
        classes.push('cursor-pointer');
        if (hover.value) {
          classes.push('bg-theme-50');
          classes.push('border-theme-500');
        } else {
          classes.push('border-gray-300');
        }
      }

      return classes;
    })

    const acceptMimes = computed(() => {
      return props.accept.split(',').map(item => item.trim().split('/'));
    })

    const canClear = computed(() => {
      return !(props.disabled || (!files.value || !files.value.length));
    })

    function onChange(e) {
      emitEvent(Array.from(e.target.files));
      emit('change');
    }

    function onDrop(e) {
      emitEvent(Array.from(e.dataTransfer.files));
      emit('change');
      hover = false;
    }

    function onClick(e) {
      if (props.disabled) {
        return;
      }
      input.value.click();
      emit('click', e)
    }

    function onClear() {
      if (canClear) {
        clear();
      }
    }

    function clear() {
      emit('clear');
      if (props.multiple) {
        emit('input', []);
        emit('change');
      } else {
        emit('input', null);
        emit('change');
      }
    }

    function emitEvent(filesArr) {

      if (!filesArr || !filesArr.length) {
        return false;
      }
      if (!filesAccept(filesArr)) {
        return false;
      }
      if (!props.multiple) {
        emit('update:modelValue', filesArr[0]);
        return emit('input', filesArr[0]);
      } else {
        emit('update:modelValue', filesArr);
        return emit('input', filesArr);
      }
    }

    function previewImage(event) {
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = () => {
          imagePreview.value = reader.result;
        };
        reader.readAsDataURL(file);
      }
    }

    function previewImages(event) {
      if (props.multiple) {
        const files = event.target.files;
        if (files) {
          for (let i = 0; i < files.length; i++) {
            const reader = new FileReader();
            reader.onload = () => {
              imagePreviews.value.push(reader.result);
            };
            reader.readAsDataURL(files[i]);
          }
        }
      } else  {
        const file = event.target.files[0];
        if (file) {
          const reader = new FileReader();
          reader.onload = () => {
            imagePreview.value = reader.result;
          };
          reader.readAsDataURL(file);
        }
      }
    }

    function filesAccept(files) {
      return files.every((file) => {
        let type = file.type.split('/');
        return acceptMimes.value.some((accepted) => {
          if (
              accepted[0] === '*' ||
              (accepted[0] === type[0] && accepted[1] === '*') ||
              (accepted[0] === type[0] && accepted[1] === type[1])
          ) {
            return true;
          }

          let error = new Error(`File ${file.name} has not accepted type ${file.type}`);
          emit('error', error);
          throw error;
        });
      });
    }

    return {
      files,
      input,
      classes,
      hover,
      onClick,
      onDrop,
      onClear,
      canClear,
      onChange,
      placeholderMessage,
      trans,
      errorStore,
      previewImages,
      imagePreview,
      imagePreviews,
    }

  }
});
</script>

<style lang="scss" scoped>
.file-input {
  padding: .5em .5em;
  width: 100%;
  box-sizing: border-box;
  border-width: 2px;
  border-style: dashed;
  display: flex;
  justify-content: center;
  align-items: center;
  text-align: center;
  min-height: 6em;
  position: relative;

  &__clear {
    position: absolute;
    right: 8px;
    top: 4px;
    background: none;
    border: none;
    color: inherit;
    font-size: 1em;
    cursor: pointer;

    &:focus {
      outline: none;
    }
  }

  input {
    display: none;
  }
}
</style>
