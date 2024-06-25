<template>
  <div class="rounded w-full mx-auto">
    <!-- Tabs -->
    <ul class="inline-flex px-1 w-full border-b">
      <li v-for="(tab, index) in tabs" :key="index" :class="{ 'bg-white px-4 text-gray-800 font-semibold py-2 rounded-t border-t border-r border-l -mb-px': activeTab === index, 'px-4 text-gray-800 font-semibold py-2 rounded-t': activeTab !== index }">
        <router-link :to="tab.href" @click.prevent="activateTab(index)">{{ tab.title }}</router-link>
      </li>
    </ul>

    <!-- Tab Contents -->
    <div class="p-4">
      <slot></slot>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';

export default {
  props: {
    tabs: {
      type: Array,
      required: true
    },
    activeIndex: {
      type: Number,
      required: true,
      default: 0,
    }
  },
  emits: ['setIndex', 'close'],
  setup(props,{emit}) {
    const activeTab = ref(props.activeIndex);

    const activateTab = (index) => {
      activeTab.value = index;
      emit("setIndex", index);
    };

    return { activeTab, activateTab };
  }
};
</script>

<style scoped>
</style>