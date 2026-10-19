<template>

  <q-layout view="lHh LpR fFf">

    <q-page-container>
     <q-page padding class="flex flex-center bg-grey-2">
      <slot />
     </q-page>
    </q-page-container>

  </q-layout>
</template>

<script setup lang="ts">

defineProps<{
  title?: string;
  description?: string;
}>();

</script>