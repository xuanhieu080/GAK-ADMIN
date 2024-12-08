<template>
    <span class="text-sm font-medium mr-2 px-2.5 py-0.5 rounded" :class="classes"><slot></slot></span>
</template>

<script>

import {computed, defineComponent} from "vue";

export default defineComponent({
    props: {
        theme: {
            type: String,
            default: "success",
        },
    },
    setup(props) {
        const classes = computed(() => {
            let value = '';
            if (props.hasOwnProperty('class')) {
                value += props.class;
            }
            switch (props.theme) {
                case 'success':
                    value = 'bg-green-100 text-green-800';
                    break;
                case 'info':
                    value = 'bg-blue-100 text-blue-800';
                    break;
                case 'warning':
                    value = 'bg-orange-100 text-orange-800';
                    break;
                case 'danger':
                case 'alert':
                case 'error':
                    value = 'bg-red-100 text-red-800';
                    break;
                default:
                    value = 'bg-theme-100 text-theme-800 ';
                    break;
            }
            return value;
        })

        return {
            classes
        }
    }
});
</script>
