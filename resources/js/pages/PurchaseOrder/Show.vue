<script setup>
import { dateFormat, getDiffInDays } from '@/lib/utils';
import { usePage } from '@inertiajs/vue3';

const { data, items } = usePage().props
</script>

<template>
    <AppHeader title="Purchase Order items" withBack></AppHeader>
    <div class="container">
        <TableContainer>
            <tr>
                <td>Ref</td>
                <td>{{ data.reference }}</td>
            </tr>
            <tr>
                <td>Desc</td>
                <td>{{ data.description }}</td>
            </tr>
            <tr>
                <td>Fulfillment</td>
                <td>{{ data.fulfillment }}</td>
            </tr>
            <tr>
                <td>Created At</td>
                <td>{{ dateFormat(data.created_at) }}</td>
            </tr>
            <tr>
                <td>Est Finished At</td>
                <td>{{ dateFormat(data.etd_finished_at) }} 
                    <q-badge :color="getDiffInDays(data.etd_finished_at) > 0 ? 'green' : 'red'">{{ getDiffInDays(data.etd_finished_at) }} Hari</q-badge>
                </td>
            </tr>
        </TableContainer>
    </div>
    <div class="container q-mt-md">
        <TableContainer>
            <thead>
                <tr>
                    <th align="left">#</th>
                    <th align="left">Ref</th>
                    <th align="left">Status</th>
                    <th align="left">Product</th>
                    <th align="left">QTY</th>
                    <th align="left">Partial QTY</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, i) in items" :key="i">
                    <td>{{ i + 1 }}</td>
                    <td>{{ item.reference }}</td>
                    <td>
                        <span>
                            <q-badge :color="item.status_color">{{ item.status_label }}</q-badge>
                        </span>
                    </td>
                    <td>{{ item.product_line_id }}</td>
                    <td>{{ item.quantity }}</td>
                    <td>{{ item.partial_qty }}</td>
                </tr>
            </tbody>
        </TableContainer>
    </div>
</template>