<script setup>
import { usePage } from '@inertiajs/vue3';

const data = usePage().props.data

</script>

<template>
   <AppHeader title="Marketplace Stores">
   </AppHeader>
   <div class="container">
      <TableContainer>
         <thead>
            <tr>
               <th align="left">#</th>
               <th align="left">Platform</th>
               <th align="left">Name</th>
               <th align="left">ID</th>
               <th align="left">Store</th>
               <!-- <th align="right">Action</th> -->
            </tr>
         </thead>
         <tbody>
            <tr v-for="(item, i) in data.data" :key="i">
               <td>{{ data.from + i }}</td>
               <td>{{ item.platform }}</td>
               <td>{{ item.name }}</td>
               <td>{{ item.vendor_id }}</td>
               <td>{{ item.trader_name }}</td>
      
            </tr>
         </tbody>
      </TableContainer>
      <div v-if="!data.total" class="q-pa-sm">No items found</div>
      <AppPagination v-bind="data"></AppPagination>
   </div>
  
</template>