<script setup>
import { usePage } from '@inertiajs/vue3';

const data = usePage().props.data
</script>

<template>
    <AppHeader title="Product Lines"></AppHeader>
    <div class="container">
        <TableContainer>
            <thead>
                <tr>
                    <th align="left">#</th>
                    <th align="left">Name</th>
                    <th align="left">SKU</th>
                    <th align="left">BasePrice</th>
                    <th align="left">RetailPrice</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, i) in data.data" :key="i">
                    <td>{{ data.from + i }}</td>
                    <td>{{ item.name_override }}</td>
                    <td>{{ item.sku_number }}</td>
                    <td>{{ item.base_price }}</td>
                    <td>{{ item.retail_price }}</td>
                </tr>
            </tbody>
        </TableContainer>
        <AppPagination v-bind="data"></AppPagination>
    </div>
</template>