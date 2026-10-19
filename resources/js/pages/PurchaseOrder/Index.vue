<script setup>
import purchaseOrders from '@/routes/purchase-orders';
import { router, usePage } from '@inertiajs/vue3';

const { data } = usePage().props

</script>

<template>
    <AppHeader title="Purchase Order"></AppHeader>
    <div class="container">
        <TableContainer>
            <thead>
                <tr>
                    <th align="left">#</th>
                    <th align="left">Ref</th>
                    <th align="left">Status</th>
                    <th align="left">Description</th>
                    <th align="left">Fulfillment</th>
                    <th align="left">Pending</th>
                    <th align="left">Processed</th>
                    <th align="left">Shipped</th>
                    <th align="left">Revisi</th>
                    <th align="left">Completed</th>
                    <th align="left">Cancelled</th>
                    <th align="left">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, i) in data.data" :key="i">
                    <td>{{ data.from + i }}</td>
                    <td>{{ item.reference }}</td>
                    <td>
                        <span>
                            <q-badge :color="item.status_color">{{ item.status_label }}</q-badge>
                        </span>
                    </td>
                    <td>{{ item.description }}</td>
                    <td>{{ item.fulfillment }}</td>
                    <td>{{ item.item_pending_count }}</td>
                    <td>{{ item.item_processed_count }}</td>
                    <td>{{ item.item_shipped_count }}</td>
                    <td>{{ item.item_revision_count }}</td>
                    <td>{{ item.item_revision_count }}</td>
                    <td>{{ item.item_cancelled_count }}</td>
                    <td>
                        <q-btn label="Show" color="teal" class="btn-action" @click="router.get(purchaseOrders.show(item.id))"></q-btn>
                    </td>
                </tr>
            </tbody>
        </TableContainer>
    </div>
    <AppPagination v-bind="data"></AppPagination>
</template>