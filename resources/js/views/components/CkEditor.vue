<script setup lang="ts">
import { ref, onMounted } from 'vue';
import CKEditor from '@ckeditor/ckeditor5-vue';
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

const editorData = ref('');
const editorConfig = {
  toolbar: [
    'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|',
    'indent', 'outdent', '|', 'imageUpload', 'blockQuote',
    'insertTable', 'mediaEmbed', 'undo', 'redo' // Add more items as needed
  ]
  // Add more configuration as needed
};

// Ref for editor instance
const editor = ref(ClassicEditor);

onMounted(() => {
  // Editor initialization and logic go here
});
</script>

<template>
  <CKEditor :editor="editor" v-model="editorData" :config="editorConfig"></CKEditor>
</template>

<style scoped>

</style>