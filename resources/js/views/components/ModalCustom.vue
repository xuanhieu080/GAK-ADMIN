<script setup>
import {computed, onMounted, onUnmounted, watch} from 'vue';

const props = defineProps({
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

const emit = defineEmits(['close']);

watch(() => props.show, () => {
  if (props.show) {
    document.body.style.overflow = 'hidden';
  } else {
    document.body.style.overflow = null;
  }
});

const close = () => {
  if (props.closeable) {
    emit('close');
  }
};

const closeOnEscape = (e) => {
  if (e.key === 'Escape' && props.show) {
    close();
  }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));

onUnmounted(() => {
  document.removeEventListener('keydown', closeOnEscape);
  document.body.style.overflow = null;
});

const maxWidthClass = computed(() => {
  return {
    'sm': 'sm:max-w-sm',
    'md': 'sm:max-w-md',
    'lg': 'sm:max-w-lg',
    'xl': 'sm:max-w-xl',
    '2xl': 'sm:max-w-2xl',
  }[props.maxWidth];
});
</script>

<template>
  <teleport to="body">
    <transition leave-active-class="duration-200">
      <div v-show="show"
           class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50 overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 md:inset-0 h-modal md:h-full justify-center items-center flex"
           scroll-region>
        <transition
            enter-active-class="ease-out duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="ease-in duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
          <div v-show="show" class="fixed inset-0 transform transition-all modal-container">
            <div class="absolute inset-0 bg-gray-500 opacity-75"/>
          </div>
        </transition>

        <transition
            enter-active-class="ease-out duration-300"
            enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            enter-to-class="opacity-100 translate-y-0 sm:scale-100"
            leave-active-class="ease-in duration-200"
            leave-from-class="opacity-100 translate-y-0 sm:scale-100"
            leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        >
          <div v-show="show"
               class="relative p-4 w-full max-w-md modal-container overflow-auto"
               :class="maxWidthClass">
            <div class="relative bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:mx-auto">
              <slot v-if="show"/>
            </div>
          </div>
        </transition>
      </div>
    </transition>
  </teleport>
</template>

<style scoped>
.modal-container {
  max-height: 100%;
}
</style>
