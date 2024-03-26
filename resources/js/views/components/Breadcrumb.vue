<script setup lang="ts">

import {useGlobalStateStore} from '@/stores/global';
import {watch,onMounted} from "vue";
const globalStore = useGlobalStateStore();

watch(() => globalStore.scroll ,
    () => {
        setTimeout(() =>{
            let breadcrumb = document.querySelector(`.breadcrumb`);
            if (breadcrumb) {
                if (globalStore.scroll >= 68 && breadcrumb) {
                    breadcrumb.classList.add('top-0');
                    breadcrumb.classList.add('md:-mx-8');
                    breadcrumb.classList.add('-mx-2');
                    breadcrumb.classList.add('z-40');
                } else {
                    breadcrumb.classList.remove('top-0');
                    breadcrumb.classList.remove('md:-mx-8');
                    breadcrumb.classList.remove('-mx-2');
                    breadcrumb.classList.remove('z-40');
                }
            }
        })
    });
// setTimeout(() => {
//     const element = document.querySelector(`.main-container`);
//     // const breadcrumb = document.querySelector(`.breadcrumb`);
//     console.log(element)
//     element.onscroll = () => {
//         console.log(breadcrumb)
//         if (element && element.scrollTop >= 68 && breadcrumb) {
//             console.log('sdf')
//             breadcrumb.style.position = "sticky";
//             breadcrumb.classList.add('top-0 -mx-2 md:-mx-8');
//         } else {
//             breadcrumb.classList.remove('top-0 -mx-2 md:-mx-8');
//         }
//     };
// })
</script>

<template>
    <div class="sticky breadcrumb flex flex-wrap justify-between p-4 text-sm font-semibold bg-white rounded-lg shadow-md focus:outline-none focus:shadow-outline-purple mb-6">
        <slot></slot>
    </div>
</template>

<style scoped lang="scss">

</style>
