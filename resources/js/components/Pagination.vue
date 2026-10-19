<script setup>
import { router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
   total: Number,
   per_page: Number,
   current_page: Number,
   last_page: Number,
   next_page_url: String,
   prev_page_url: String,
   first_page_url: String,
   last_page_url: String,
   from: Number,
   to: Number,
   path: String,
   withEmptyData: Boolean
})

const perPage = ref(usePage().props.query.per_page || props.per_page)

const getData = () => {
   const url = new URL(props.first_page_url)
   url.searchParams.set('per_page', perPage.value)

   router.get(url)
}

</script>

<template>
   <div>
      <div class="q-px-md q-py-sm flex items-center q-gutter-x-sm no-wrap" v-if="withEmptyData && total == 0">
         <q-icon name="warning" size="sm"></q-icon>
         <span>No data available</span>
      </div>
      <div class="flex justify-between items-center q-gutter-x-xs no-wrap q-py-xs" v-if="total > 0">
         <div>
            <q-select :options="[5, 10, 20, 50, 100, 500]" filled style="width:150px" dense label="Showing" v-model="perPage"
               @update:model-value="getData"></q-select>
         </div>
         <div class="flex items-center q-gutter-x-xs no-wrap q-py-xs">

            <div>Page</div>
            <div>{{ current_page }} / {{ last_page }}</div>
            <div>Total</div>
            <div>{{ total }}</div>
            <q-btn color="grey-8" dense icon="first_page" :disable="current_page == 1" size="13px"
               @click="router.get(first_page_url)">
               <q-tooltip>First Page</q-tooltip>
            </q-btn>
            <q-btn color="grey-8" dense icon="chevron_left" :disable="!prev_page_url" size="13px"
               @click="router.get(prev_page_url)">
               <q-tooltip>Previous Page</q-tooltip>
            </q-btn>
            <q-btn color="grey-8" dense icon="chevron_right" :disable="!next_page_url" size="13px"
               @click="router.get(next_page_url)">
               <q-tooltip>Next Page</q-tooltip>
            </q-btn>
            <q-btn color="grey-8" dense icon="last_page" :disable="current_page == last_page" size="13px"
               @click="router.get(last_page_url)">
               <q-tooltip>Last Page</q-tooltip>
            </q-btn>
         </div>
      </div>
   </div>
</template>