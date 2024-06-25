<script setup lang="ts">
import { ref, onMounted } from 'vue';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
import CustomUploadAdapter from '@/helpers/ckeditor.js';
import UploadAdapter from '@/helpers/upload';

const editorData = ref('');
const editorConfig = {
  toolbar: [
    'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|',
    'indent', 'outdent', '|', 'imageUpload', 'blockQuote',
    'insertTable', 'mediaEmbed', 'undo', 'redo' // Add more items as needed
  ],
  extraPlugins: [CustomUploader]
  // Add more configuration as needed
};

// Ref for editor instance
const editor = ref(ClassicEditor);

function CustomUploader(editor) {
  editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
    return new CustomUploadAdapter(loader);
  };
}

onMounted(() => {
  editor.value = ClassicEditor;
});
</script>

<template>
  <ckeditor :editor="editor" v-model="editorData" :config="editorConfig"></ckeditor>
</template>

<style scoped>

</style>