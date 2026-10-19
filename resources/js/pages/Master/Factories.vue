<script setup>
import { usePage } from '@inertiajs/vue3';

const data = usePage().props.data

</script>

<template>
   <AppHeader title="Factory">
   </AppHeader>
   <div class="container">
      <TableContainer>
         <thead>
            <tr>
               <th align="left">#</th>
               <th align="left">Name</th>
               <th align="left">Address</th>
            </tr>
         </thead>
         <tbody>
            <tr v-for="(item, i) in data.data" :key="i">
               <td>{{ data.from + i }}</td>
               <td>{{ item.name }}</td>
               <td>{{ item.addres }}</td>
            </tr>
         </tbody>
      </TableContainer>
      <div v-if="!data.total" class="q-pa-sm">No data available</div>
      <AppPagination v-bind="data"></AppPagination>
   </div>
</template>