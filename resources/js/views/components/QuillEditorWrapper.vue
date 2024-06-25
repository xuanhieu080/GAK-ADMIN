<template>
  <QuillEditor
      ref="myEditor"
      v-model="content"
      :modules="modules"
      :toolbar="options"
      :value="value"
      :placeholder="placeholder"
      content-type="html"
      contentType="html"
      theme="snow"
      @textChange="ev => changeEditor()"/>
</template>

<script>
//Import editor
import {QuillEditor} from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import ImageUploader from "quill-image-uploader";
import htmlEditButton from "quill-html-edit-button";
import MagicUrl from 'quill-magic-url'
import AutoLinks from 'quill-auto-links';
import {ref, watch} from "vue";
import axios from "@/plugins/axios"

export default {
  name: 'QuillEditorWrapper',
  components: {
    QuillEditor
  },
  props: {
    value: [String],
    placeholder: [String],
  },
  setup: (props) => {
    const myEditor = ref()
    const options = ref([
      [{font: []}],
      [{size: ['small', false, 'large', 'huge']}], // custom dropdown
      [{header: [1, 2, 3, 4, 5, 6, false]}],

      ['bold', 'italic', 'underline', 'strike'], // toggled buttons
      ['blockquote', 'code-block'],

      [{header: 1}, {header: 2}], // custom button values
      [{list: 'ordered'}, {list: 'bullet'}],
      [{script: 'sub'}, {script: 'super'}], // superscript/subscript
      [{indent: '-1'}, {indent: '+1'}], // outdent/indent
      [{direction: 'rtl'}], // text direction
      ['link', 'image', 'video'], // link & image

      [{color: []}, {background: []}], // dropdown with defaults from theme
      [{align: []}],

      ['clean'], // remove formatting button
    ])
    const modules = [{
      name: "imageUploader",
      module: ImageUploader,
      options: {
        upload: (file) => {
          const result = new Promise((resolve, reject) => {
            const formData = new FormData();
            formData.append("image", file);
            const axiosInstance = axios.create({
              baseURL: axios.defaults.baseURL + '/api',
              withCredentials: true,
            });

            axiosInstance.post('/upload-image', formData, {
              headers: {
                'Content-Type': 'multipart/form-data'
              }
            }).then((res) => {
              resolve(res.data.link);
            }).catch((err) => {
              reject("Upload failed");
              console.error("Error:", err);
            });
          });

          return result;
        },
      },
    },
      {
        name: 'htmlEditButton',
        module: htmlEditButton,
        options: {
          debug: true
        },
      },
      {
        name: 'MagicUrl',
        module: MagicUrl,
        options: {
          magicUrl: true,
          // Regex used to check URLs during typing
          urlRegularExpression: /(https?:\/\/[\S]+)|(www.[\S]+)|(tel:[\S]+)/g,
          // Regex used to check URLs on paste
          globalRegularExpression: /(https?:\/\/|www\.|tel:)[\S]+/g,
        },
      }, {
        name: 'AutoLinks',
        module: AutoLinks,
        options: {
          autoLinks: true,
          paste: true,
          type: true
        },
      }];

    function changeEditor() {
      this.$emit('input-editor', this.$refs.myEditor.getHTML())
    }


    return {modules, options, changeEditor, watch, myEditor};
  },


  data() {
    return {
      content: ''
    }
  },
  mounted() {
    this.content = this.value // khi này this.$el đã gắn kết với DOM, lúc này có thể truy cập được tới các thành phần trong DOM

    setTimeout(() => {
      this.$emit('input-editor', this.value)
      // myEditor.value.pasteHTML(description.value)
      // console.log(articleContent.value )
      // articleContent.value = myEditor.value.getHTML()
    }, 500)
  }
};
</script>
