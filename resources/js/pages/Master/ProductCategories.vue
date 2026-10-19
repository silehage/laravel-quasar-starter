<script setup>
import { usePage } from '@inertiajs/vue3';

const data = usePage().props.data
</script>

<template>
    <AppHeader title="Product Categories"></AppHeader>
    <div class="container">
        <TableContainer>
            <thead>
                <tr>
                    <th align="left">#</th>
                    <th align="left">Name</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, i) in data.data" :key="i">
                    <td>{{ data.from + i }}</td>
                    <td>{{ item.name }}</td>
                </tr>
            </tbody>
        </TableContainer>
        <AppPagination v-bind="data" withEmptyData></AppPagination>
    </div>
</template>