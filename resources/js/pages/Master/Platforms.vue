<script setup>
import { usePage } from '@inertiajs/vue3';

const data = usePage().props.data
</script>

<template>
    <AppHeader title="Platform"></AppHeader>
    <div class="container">
        <TableContainer>
            <thead>
                <tr>
                    <th align="left">#</th>
                    <th align="left">Name</th>
                    <th align="left">Code</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, i) in data.data" :key="i">
                    <td>{{ data.from + i }}</td>
                    <td>{{ item.name }}</td>
                    <td>{{ item.code }}</td>
                </tr>
            </tbody>
        </TableContainer>
        <AppPagination v-bind="data"></AppPagination>
    </div>
</template>