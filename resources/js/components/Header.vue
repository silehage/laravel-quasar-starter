<template>

   <Head :title="title"></Head>

   <div class="page-header">
     <div class="flex q-gutter-x-xs no-wrap items-center">
         <q-btn v-if="back || withBack || routeBack" @click="backTo" flat dense icon="arrow_back"></q-btn>
         <h1>{{ title }}</h1>
    
      </div>
      <slot></slot>
   </div>

</template>


<script setup lang="ts">

import { Head, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
   title?: string;
   caption?: string;
   back?: boolean;
   withBack?: boolean;
   routeBack?: string | Object;
}>();

const previousUrl = computed(() => usePage().props.previousUrl)
const currentUrl = computed(() => usePage().props.currentUrl)

const backTo = () => {
   if (props.routeBack) {
      router.get(props.routeBack)
      return
   }
   window.history.back()
   // if (previousUrl.value && previousUrl.value != currentUrl.value) {
   //    router.get(previousUrl.value)
   // } else {
   //    window.history.back()
   // }
}

</script>
