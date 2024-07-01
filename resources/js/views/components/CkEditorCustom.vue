<script setup lang="ts">
import {ref, onMounted, defineProps, watch, watchEffect} from 'vue';
import '@/ckeditor.css';

import {
  ClassicEditor,
  ImageCaptionEditing,
    ImageCaptionUI,
  AccessibilityHelp,
  Alignment,
  Autoformat,
  AutoImage,
  Image,
  AutoLink,
  Autosave,
  BalloonToolbar,
  BlockQuote,
  Bold,
  Code,
  CodeBlock,
  Essentials,
  FindAndReplace,
  FontBackgroundColor,
  FontColor,
  FontFamily,
  FontSize,
  FullPage,
  GeneralHtmlSupport,
  Heading,
  Highlight,
  HorizontalLine,
  HtmlComment,
  HtmlEmbed,
  ImageBlock,
  ImageCaption,
  ImageInline,
  ImageInsert,
  ImageInsertViaUrl,
  ImageResize,
  ImageStyle,
  ImageTextAlternative,
  ImageToolbar,
  ImageUpload,
  Indent,
  IndentBlock,
  Italic,
  Link,
  LinkImage,
  List,
  ListProperties,
  Markdown,
  MediaEmbed,
  PageBreak,
  Paragraph,
  PasteFromMarkdownExperimental,
  PasteFromOffice,
  RemoveFormat,
  SelectAll,
  ShowBlocks,
  SimpleUploadAdapter,
  SourceEditing,
  SpecialCharacters,
  SpecialCharactersArrows,
  SpecialCharactersCurrency,
  SpecialCharactersEssentials,
  SpecialCharactersLatin,
  SpecialCharactersMathematical,
  SpecialCharactersText,
  Strikethrough,
  Style,
  Subscript,
  Superscript,
  Table,
  TableCaption,
  TableCellProperties,
  TableColumnResize,
  TableProperties,
  TableToolbar,
  TextPartLanguage,
  TextTransformation,
  TodoList,
  Underline,
  Undo
} from 'ckeditor5';

import translations from './vi';
// import * as Editor from 'ckeditor-custom-build/build/ckeditor';
// import Editor from 'ckeditor5-custom-build/build/ckeditor';
// import 'ckeditor5-custom-build/sample/styles.css';
// import {ClassicEditor} from "@ckeditor/ckeditor5-editor-classic";
import CustomUploadAdapter from '@/helpers/ckeditor.js';

const props = defineProps({
  content: {
    type: String,
  },
})

const emits = defineEmits(['updateData']);

const editorData = ref(props.content);
const isLayoutReady = ref(false);
const loading = ref(true);
const editorConfig = {
  toolbar: {
    items: [
      'undo',
      'redo',
      '|',
      'sourceEditing',
      'showBlocks',
      '|',
      'heading',
      'style',
      '|',
      'fontSize',
      'fontFamily',
      'fontColor',
      'fontBackgroundColor',
      '|',
      'bold',
      'italic',
      'underline',
      '|',
      'link',
      'insertImage',
      'insertTable',
      'highlight',
      'blockQuote',
      'codeBlock',
      '|',
      'alignment',
      '|',
      'bulletedList',
      'numberedList',
      'todoList',
      'indent',
      'outdent'
    ],
    shouldNotGroupWhenFull: false
  },
  plugins: [
    AccessibilityHelp,
    Alignment,
    Autoformat,
    AutoImage,
    Image,
    AutoLink,
    Autosave,
    BalloonToolbar,
    BlockQuote,
    Bold,
    Code,
    CodeBlock,
    Essentials,
    FindAndReplace,
    FontBackgroundColor,
    FontColor,
    FontFamily,
    FontSize,
    FullPage,
    GeneralHtmlSupport,
    Heading,
    Highlight,
    HorizontalLine,
    HtmlComment,
    HtmlEmbed,
    ImageBlock,
    // ImageCaption,
    // ImageCaptionUI,
    ImageInline,
    ImageInsert,
    ImageInsertViaUrl,
    ImageResize,
    ImageStyle,
    ImageTextAlternative,
    ImageToolbar,
    ImageUpload,
    Indent,
    IndentBlock,
    Italic,
    Link,
    LinkImage,
    List,
    ListProperties,
    Markdown,
    MediaEmbed,
    PageBreak,
    Paragraph,
    PasteFromMarkdownExperimental,
    PasteFromOffice,
    RemoveFormat,
    SelectAll,
    ShowBlocks,
    SimpleUploadAdapter,
    SourceEditing,
    SpecialCharacters,
    SpecialCharactersArrows,
    SpecialCharactersCurrency,
    SpecialCharactersEssentials,
    SpecialCharactersLatin,
    SpecialCharactersMathematical,
    SpecialCharactersText,
    Strikethrough,
    Style,
    Subscript,
    Superscript,
    Table,
    // TableCaption,
    TableCellProperties,
    TableColumnResize,
    TableProperties,
    TableToolbar,
    TextPartLanguage,
    TextTransformation,
    TodoList,
    Underline,
    Undo,
    // ImageCaptionEditing
  ],
  balloonToolbar: ['bold', 'italic', '|', 'link', 'insertImage', '|', 'bulletedList', 'numberedList'],
  fontFamily: {
    supportAllValues: true
  },
  fontSize: {
    options: [10, 12, 14, 'default', 18, 20, 22],
    supportAllValues: true
  },
  heading: {
    options: [
      {
        model: 'paragraph',
        title: 'Paragraph',
        class: 'ck-heading_paragraph'
      },
      {
        model: 'heading1',
        view: 'h1',
        title: 'Heading 1',
        class: 'ck-heading_heading1'
      },
      {
        model: 'heading2',
        view: 'h2',
        title: 'Heading 2',
        class: 'ck-heading_heading2'
      },
      {
        model: 'heading3',
        view: 'h3',
        title: 'Heading 3',
        class: 'ck-heading_heading3'
      },
      {
        model: 'heading4',
        view: 'h4',
        title: 'Heading 4',
        class: 'ck-heading_heading4'
      },
      {
        model: 'heading5',
        view: 'h5',
        title: 'Heading 5',
        class: 'ck-heading_heading5'
      },
      {
        model: 'heading6',
        view: 'h6',
        title: 'Heading 6',
        class: 'ck-heading_heading6'
      }
    ]
  },
  htmlSupport: {
    allow: [
      {
        name: /^.*$/,
        styles: true,
        attributes: true,
        classes: true
      }
    ]
  },
  image: {
    toolbar: [
      'imageTextAlternative',
      '|',
      'imageStyle:inline',
      'imageStyle:wrapText',
      'imageStyle:breakText',
      '|',
      'resizeImage',
      '|',
      'linkImage'
    ],
    insert: {
      // This is the default configuration, you do not need to provide
      // this configuration key if the list content and order reflects your needs.
      integrations: [ 'upload', 'assetManager', 'url' ]
    }
  },
  initialData:
      '<h2>Congratulations on setting up CKEditor 5! 🎉</h2>\n<p>\n    You\'ve successfully created a CKEditor 5 project. This powerful text editor will enhance your application, enabling rich text editing\n    capabilities that are customizable and easy to use.\n</p>\n<h3>What\'s next?</h3>\n<ol>\n    <li>\n        <strong>Integrate into your app</strong>: time to bring the editing into your application. Take the code you created and add to your\n        application.\n    </li>\n    <li>\n        <strong>Explore features:</strong> Experiment with different plugins and toolbar options to discover what works best for your needs.\n    </li>\n    <li>\n        <strong>Customize your editor:</strong> Tailor the editor\'s configuration to match your application\'s style and requirements. Or even\n        write your plugin!\n    </li>\n</ol>\n<p>\n    Keep experimenting, and don\'t hesitate to push the boundaries of what you can achieve with CKEditor 5. Your feedback is invaluable to us\n    as we strive to improve and evolve. Happy editing!\n</p>\n<h3>Helpful resources</h3>\n<ul>\n    <li>📝 <a href="https://orders.ckeditor.com/trial/premium-features">Trial sign up</a>,</li>\n    <li>📕 <a href="https://ckeditor.com/docs/ckeditor5/latest/installation/index.html">Documentation</a>,</li>\n    <li>⭐️ <a href="https://github.com/ckeditor/ckeditor5">GitHub</a> (star us if you can!),</li>\n    <li>🏠 <a href="https://ckeditor.com">CKEditor Homepage</a>,</li>\n    <li>🧑‍💻 <a href="https://ckeditor.com/ckeditor-5/demo/">CKEditor 5 Demos</a>,</li>\n</ul>\n<h3>Need help?</h3>\n<p>\n    See this text, but the editor is not starting up? Check the browser\'s console for clues and guidance. It may be related to an incorrect\n    license key if you use premium features or another feature-related requirement. If you cannot make it work, file a GitHub issue, and we\n    will help as soon as possible!\n</p>\n',
  language: 'vi',
  link: {
    addTargetToExternalLinks: true,
    defaultProtocol: 'https://',
    decorators: {
      toggleDownloadable: {
        mode: 'manual',
        label: 'Downloadable',
        attributes: {
          download: 'file'
        }
      }
    }
  },
  list: {
    properties: {
      styles: true,
      startIndex: true,
      reversed: true
    }
  },
  menuBar: {
    isVisible: true
  },
  placeholder: 'Nhập hoặc dán nội dung của bạn ở đây!',
  style: {
    definitions: [
      {
        name: 'Article category',
        element: 'h3',
        classes: ['category']
      },
      {
        name: 'Title',
        element: 'h2',
        classes: ['document-title']
      },
      {
        name: 'Subtitle',
        element: 'h3',
        classes: ['document-subtitle']
      },
      {
        name: 'Info box',
        element: 'p',
        classes: ['info-box']
      },
      {
        name: 'Side quote',
        element: 'blockquote',
        classes: ['side-quote']
      },
      {
        name: 'Marker',
        element: 'span',
        classes: ['marker']
      },
      {
        name: 'Spoiler',
        element: 'span',
        classes: ['spoiler']
      },
      {
        name: 'Code (dark)',
        element: 'pre',
        classes: ['fancy-code', 'fancy-code-dark']
      },
      {
        name: 'Code (bright)',
        element: 'pre',
        classes: ['fancy-code', 'fancy-code-bright']
      }
    ]
  },
  table: {
    contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells', 'tableProperties', 'tableCellProperties']
  },
  translations: [translations],
  enableGrip: false,
  extraPlugins: [CustomUploader]
};

// Ref for editor instance
const editor = ref(ClassicEditor);

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
  editor.value = ClassicEditor;
  loading.value = false;
  isLayoutReady.value = true;
});
</script>

<template>
  <div class="main-container ck-editor-component">
    <div class="editor-container editor-container_classic-editor editor-container_include-style"
         ref="editorContainerElement">
      <div class="editor-container__editor">
        <div ref="editorElement">
          <ckeditor v-if="isLayoutReady" v-model="editorData" :editor="editor" :config="editorConfig"/>
        </div>
      </div>
    </div>
  </div>

  <!--  <div class="ck-editor-component" v-if="!loading">-->
  <!--      <ckeditor :editor="editor" v-model="editorData" :config="editorConfig"></ckeditor>-->
  <!--    </div>-->

</template>

<style lang="scss">
.ck-editor-component {
  /**
 * @license Copyright (c) 2014-2024, CKSource Holding sp. z o.o. All rights reserved.
 * This file is licensed under the terms of the MIT License (see LICENSE.md).
 */

  :root {
    --ck-sample-base-spacing: 2em;
    --ck-sample-color-white: #fff;
    --ck-sample-color-green: #279863;
    --ck-sample-color-blue: #1a9aef;
    --ck-sample-container-width: 1285px;
    --ck-sample-sidebar-width: 350px;
    --ck-sample-editor-min-height: 400px;
    --ck-sample-editor-z-index: 10;
  }

  /* --------- EDITOR STYLES  ---------------------------------------------------------------------------------------- */

  .editor__editable,
    /* Classic build. */
  main .ck-editor[role='application'] .ck.ck-content,
    /* Decoupled document build. */
  .ck.editor__editable[role='textbox'],
  .ck.ck-editor__editable[role='textbox'],
    /* Inline & Balloon build. */
  .ck.editor[role='textbox'] {
    width: 100%;
    background: #fff;
    font-size: 1em;
    line-height: 1.6em;
    min-height: var(--ck-sample-editor-min-height);
    padding: 1.5em 2em;
  }

  .ck.ck-editor__editable {
    background: #fff;
    border: 1px solid hsl(0, 0%, 70%);
    width: 100%;
  }

  /* Because of sidebar `position: relative`, Edge is overriding the outline of a focused editor. */
  .ck.ck-editor__editable {
    position: relative;
    z-index: var(--ck-sample-editor-z-index);
  }

  .editor-container {
    display: flex;
    flex-direction: row;
    flex-wrap: nowrap;
    position: relative;
    width: 100%;
    justify-content: center;
  }

  .editor-container--with-sidebar > .ck.ck-editor {
    width: calc(100% - var(--ck-sample-sidebar-width));
  }

  /* --------- DECOUPLED (DOCUMENT) BUILD. ---------------------------------------------*/
  body[data-editor='DecoupledEditor'] .document-editor__toolbar {
    width: 100%;
  }

  body[data-editor='DecoupledEditor'] .collaboration-demo__editable,
  body[data-editor='DecoupledEditor'] .row-editor .editor {
    /* A pixel is added for each of the border. */
    width: calc(21cm + 2px);
    min-height: calc(29.7cm + 2px);
    /* To avoid having extra scrolls inside the editor container. */
    height: fit-content;
    padding: 2cm 1.2cm;
    margin: 2.5rem;
    border: 1px hsl(0, 0%, 82.7%) solid;
    background-color: var(--ck-sample-color-white);
    box-shadow: 0 0 5px hsla(0, 0%, 0%, .1);
    box-sizing: border-box;
  }

  body[data-editor='DecoupledEditor'] .row-editor {
    display: flex;
    position: relative;
    justify-content: center;
    overflow-y: auto;
    background-color: #f2f2f2;
    border: 1px solid hsl(0, 0%, 77%);
    /* Limit the max-height of the editor to avoid scrolling from bottom to top to see the toolbar. */
    max-height: 700px;
  }

  body[data-editor='DecoupledEditor'] .sidebar {
    background: transparent;
    border: 0;
    box-shadow: none;
  }

  /* --------- COMMENTS & TRACK CHANGES FEATURE ---------------------------------------------------------------------- */
  .sidebar {
    padding: 0 15px;
    position: relative;
    min-width: var(--ck-sample-sidebar-width);
    max-width: var(--ck-sample-sidebar-width);
    font-size: 20px;
    border: 1px solid hsl(0, 0%, 77%);
    background: hsl(0, 0%, 98%);
    border-left: 0;
    overflow: hidden;
    min-height: 100%;
    flex-grow: 1;
  }

  /* Do not inherit styles related to the editable editor content. See line 25.*/
  .sidebar .ck-content[role='textbox'],
  .ck.ck-annotation-wrapper .ck-content[role='textbox'] {
    min-height: unset;
    width: unset;
    padding: 0;
    background: transparent;
  }

  .sidebar.narrow {
    min-width: 60px;
    flex-grow: 0;
  }

  .sidebar.hidden {
    display: none !important;
  }

  #sidebar-display-toggle {
    position: absolute;
    z-index: 1;
    width: 30px;
    height: 30px;
    text-align: center;
    left: 15px;
    top: 30px;
    border: 0;
    padding: 0;
    color: hsl(0, 0%, 50%);
    transition: 250ms ease color;
    background-color: transparent;
  }

  #sidebar-display-toggle:hover {
    color: hsl(0, 0%, 30%);
    cursor: pointer;
  }

  #sidebar-display-toggle:focus,
  #sidebar-display-toggle:active {
    outline: none;
    border: 1px solid #a9d29d;
  }

  #sidebar-display-toggle svg {
    fill: currentColor;
  }

  /* --------- COLLABORATION FEATURES (USERS) ------------------------------------------------------------------------ */
  .row-presence {
    width: 100%;
    border: 1px solid hsl(0, 0%, 77%);
    border-bottom: 0;
    background: hsl(0, 0%, 98%);
    padding: var(--ck-spacing-small);

    /* Make `border-bottom` as `box-shadow` to not overlap with the editor border. */
    box-shadow: 0 1px 0 0 hsl(0, 0%, 77%);

    /* Make `z-index` bigger than `.editor` to properly display tooltips. */
    z-index: 20;
  }

  .ck.ck-presence-list {
    flex: 1;
    padding: 1.25rem .75rem;
  }

  .presence .ck.ck-presence-list__counter {
    order: 2;
    margin-left: var(--ck-spacing-large)
  }

  /* --------- REAL TIME COLLABORATION FEATURES (SHARE TOPBAR CONTAINER) --------------------------------------------- */
  .collaboration-demo__row {
    display: flex;
    position: relative;
    justify-content: center;
    overflow-y: auto;
    background-color: #f2f2f2;
    border: 1px solid hsl(0, 0%, 77%);
  }

  body[data-editor='InlineEditor'] .collaboration-demo__row {
    border: 0;
  }

  .collaboration-demo__container {
    max-width: var(--ck-sample-container-width);
    margin: 0 auto;
    padding: 1.25rem;
  }

  .presence, .collaboration-demo__row {
    transition: .2s opacity;
  }

  .collaboration-demo__topbar {
    background: #fff;
    border: 1px solid var(--ck-color-toolbar-border);
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 0;
    border-radius: 4px 4px 0 0;
  }

  .collaboration-demo__topbar .btn {
    margin-right: 1em;
    outline-offset: 2px;
    outline-width: 2px;
    background-color: var(--ck-sample-color-blue);
  }

  .collaboration-demo__topbar .btn:focus,
  .collaboration-demo__topbar .btn:hover {
    border-color: var(--ck-sample-color-blue);
  }

  .collaboration-demo__share {
    display: flex;
    align-items: center;
    padding: 1.25rem .75rem
  }

  .collaboration-demo__share-description p {
    margin: 0;
    font-weight: bold;
    font-size: 0.9em;
  }

  .collaboration-demo__share input {
    height: auto;
    font-size: 0.9em;
    min-width: 220px;
    margin: 0 10px;
    border-radius: 4px;
    border: 1px solid var(--ck-color-toolbar-border)
  }

  .collaboration-demo__share button,
  .collaboration-demo__share input {
    height: 40px;
    padding: 5px 10px;
  }

  .collaboration-demo__share button {
    position: relative;
  }

  .collaboration-demo__share button:focus {
    outline: none;
  }

  .collaboration-demo__share button[data-tooltip]::before,
  .collaboration-demo__share button[data-tooltip]::after {
    position: absolute;
    visibility: hidden;
    opacity: 0;
    pointer-events: none;
    transition: all .15s cubic-bezier(.5, 1, .25, 1);
    z-index: 1;
  }

  .collaboration-demo__share button[data-tooltip]::before {
    content: attr(data-tooltip);
    padding: 5px 15px;
    border-radius: 3px;
    background: #111;
    color: #fff;
    text-align: center;
    font-size: 11px;
    top: 100%;
    left: 50%;
    margin-top: 5px;
    transform: translateX(-50%);
  }

  .collaboration-demo__share button[data-tooltip]::after {
    content: '';
    border: 5px solid transparent;
    width: 0;
    font-size: 0;
    line-height: 0;
    top: 100%;
    left: 50%;
    transform: translateX(-50%);
    border-bottom: 5px solid #111;
    border-top: none;
  }

  .collaboration-demo__share button[data-tooltip]:hover:before,
  .collaboration-demo__share button[data-tooltip]:hover:after {
    visibility: visible;
    opacity: 1;
  }

  .collaboration-demo--ready {
    overflow: visible;
    height: auto;
  }

  .collaboration-demo--ready .presence,
  .collaboration-demo--ready .collaboration-demo__row {
    opacity: 1;
  }

  /* --------- PAGINATION FEATURE ------------------------------------------------------------------------------------ */

  /* Pagination view line must be stacked at least at the same level as the editor,
     otherwise it will be hidden underneath. */
  .ck.ck-pagination-view-line {
    z-index: var(--ck-sample-editor-z-index);
  }

  /* --------- REVISION HISTORY FEATURE ------------------------------------------------------------------------------ */

  .revision-viewer-container {
    display: none;
    max-width: 100%;
    word-wrap: break-word;
  }

  .revision-viewer-sidebar {
    position: relative;
    min-width: 310px;
    overflow: hidden;
    background: var(--ck-color-toolbar-background);
    border: 1px solid var(--ck-color-toolbar-border);
    margin-left: -1px;
  }

  /* A case when Pagination and Revision History features are enabled in the editor. */
  /* Move the square with page number from the Pagination plugin to the left side, so that it does not cover the RH sidebar. */
  body[data-revision-history='true'] .ck.ck-pagination-view-line::after {
    transform: translateX(-100%) !important;
    left: -1px !important;
    right: unset !important;
  }

  /* --------- DOCUMENT OUTLINE FEATURE ------------------------------------------------------------------------------ */

  .document-outline-container {
    max-height: 80vh;
    overflow-y: auto;
    margin-bottom: 1em;
  }

  .document-outline-container .ck.ck-document-outline {
    min-height: 100%;
    border: 1px solid var(--ck-color-base-border);
    background-color: hsl(0, 0%, 96%);
  }

  /* --------- SAMPLE GENERIC STYLES (not related to CKEditor) ------------------------------------------------------- */
  body, html {
    padding: 0;
    margin: 0;

    font-family: sans-serif, Arial, Verdana, "Trebuchet MS", "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
    font-size: 16px;
    line-height: 1.5;
  }

  body {
    height: 100%;
    color: #2D3A4A;
  }

  body * {
    box-sizing: border-box;
  }

  a {
    color: #38A5EE;
  }

  header .centered {
    display: flex;
    flex-flow: row nowrap;
    justify-content: space-between;
    align-items: center;
    min-height: 8em;
  }

  header h1 a {
    font-size: 20px;
    display: flex;
    align-items: center;
    color: #2D3A4A;
    text-decoration: none;
  }

  header h1 img {
    display: block;
    height: 64px;
  }

  header nav ul {
    margin: 0;
    padding: 0;
    list-style-type: none;
  }

  header nav ul li {
    display: inline-block;
  }

  header nav ul li + li {
    margin-left: 1em;
  }

  header nav ul li a {
    font-weight: bold;
    text-decoration: none;
    color: #2D3A4A;
  }

  header nav ul li a:hover {
    text-decoration: underline;
  }

  main .message {
    padding: 0 0 var(--ck-sample-base-spacing);
    background: var(--ck-sample-color-green);
    color: var(--ck-sample-color-white);
  }

  main .message::after {
    content: "";
    z-index: -1;
    display: block;
    height: 10em;
    width: 100%;
    background: var(--ck-sample-color-green);
    position: absolute;
    left: 0;
  }

  main .message h2 {
    position: relative;
    padding-top: 1em;
    font-size: 2em;
  }

  .centered {
    max-width: var(--ck-sample-container-width);
    margin: 0 auto;
    padding: 0 var(--ck-sample-base-spacing);
  }

  .row {
    display: flex;
    position: relative;
  }

  .btn {
    cursor: pointer;
    padding: 8px 16px;
    font-size: 1rem;
    user-select: none;
    border-radius: 4px;
    transition: color .2s ease-in-out, background-color .2s ease-in-out, border-color .2s ease-in-out, opacity .2s ease-in-out;
    background-color: var(--ck-sample-color-button-blue);
    border-color: var(--ck-sample-color-button-blue);
    color: var(--ck-sample-color-white);
    display: inline-block;
  }

  .btn--tiny {
    padding: 6px 12px;
    font-size: .8rem;
  }

  footer {
    margin: calc(2 * var(--ck-sample-base-spacing)) var(--ck-sample-base-spacing);
    font-size: .8em;
    text-align: center;
    color: rgba(0, 0, 0, .4);
  }

  /* --------- RWD --------------------------------------------------------------------------------------------------- */
  @media screen and (max-width: 800px) {
    :root {
      --ck-sample-base-spacing: 1em;
    }

    header h1 {
      width: 100%;
    }

    header h1 img {
      height: 40px;
    }

    header nav ul {
      text-align: right;
    }

    main .message h2 {
      font-size: 1.5em;
    }
  }
}
</style>