<script setup lang="ts">
import {ref, onMounted, defineProps, watch, watchEffect} from 'vue';
import * as Editor from '@ckeditor/ckeditor-custom-build/build/ckeditor';

import CustomUploadAdapter from '@/helpers/ckeditor.js';

const props = defineProps({
  content: {
    type: String,
  },
})

const emits = defineEmits(['updateData']);

const editorData = ref(props.content);
const loading = ref(true);
const editorConfig = {
  toolbar: {
    items: [
      'heading', '|',
      'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|',
      'undo', 'redo',
      '-',
      'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', '|',
      'insertTable', 'tableColumn', 'tableRow', 'mergeTableCells', '|',
      'outdent', 'indent', '|',
      'imageUpload', 'mediaEmbed', '|',
      'alignment', 'horizontalLine', 'specialCharacters'
    ]
  },
  menuBar: {
    isVisible: true
  },
  enableGrip: false,
  extraPlugins: [CustomUploader]
};

// Ref for editor instance
const editor = ref();

function CustomUploader(editor) {
  editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
    return new CustomUploadAdapter(loader);
  };
}


watchEffect(() => {
  editorData.value = props.content;
});

watch(() => editorData.value, (newValue) => {
  emits('updateData', newValue);
});

onMounted(() => {
  editor.value = Editor;
  loading.value = false;
});
</script>

<template>
  <div class="ck-editor-component" v-if="!loading">
    <ckeditor :editor="editor" v-model="editorData" :config="editorConfig"></ckeditor>
  </div>
</template>

<style scoped>

</style>