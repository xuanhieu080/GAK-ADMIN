<script setup>
import Modal from './ModalCustom.vue';

const emit = defineEmits(['close']);

defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    maxWidth: {
        type: String,
        default: '2xl',
    },
    closeable: {
        type: Boolean,
        default: true,
    },
});

const close = () => {
    emit('close');
};
</script>

<template>
    <Modal
        :show="show"
        :max-width="maxWidth"
        :closeable="closeable"
        @close="close"
    >
        <div class="">
            <div class="text-lg">
                <slot name="title" />
            </div>

            <div>
                <slot name="content" />
            </div>
        </div>
      <slot name="footer" />
    </Modal>
</template>
