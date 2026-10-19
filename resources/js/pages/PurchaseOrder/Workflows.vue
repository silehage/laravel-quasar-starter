<script setup>
import { dateFormat, getDiffDate, getNotifClass } from '@/lib/utils';
import { usePage } from '@inertiajs/vue3';


const { data } = usePage().props


</script>

<template>

    <AppHeader title="Item Workflow Detail"></AppHeader>

    <div class="container">
        <TableContainer>
            <thead>
                <tr>
                    <th align="left" class="">Product</th>
                    <th align="left" class="">PO Number</th>
                    <th align="left" class="">PO Item Number</th>
                    <th align="left" class="">Barcode</th>
                    <th align="left" class="">Est Finished Date</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ data.product_name }}</td>
                    <td>{{ data.po_number }}</td>
                    <td>{{ data.po_item_number }}</td>
                    <td>{{ data.barcode }}</td>
                    <td>{{ data.etd_finished_at }} ({{ getDiffDate(data.etd_finished_at) }} day)</td>
                </tr>
            </tbody>
        </TableContainer>
    </div>

    <div class="container q-mt-md">
        <TableContainer>
            <thead>
                <tr>
                    <th align="left" class="">Workflow</th>
                    <th align="left" class="">Worker</th>
                    <th align="left" class="">Cost</th>
                    <th align="left" class="">StartAt</th>
                    <th align="left" class="">FinishedAt</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(row, rowIdx) in data.items" :key="rowIdx">
                    <td>{{ row.workflow }}</td>
                    <td>{{ row.worker }}</td>
                    <td>{{ row.cost }}</td>
                    <td>{{ dateFormat(row.start_at) }}</td>
                    <td>{{ dateFormat(row.finished_a) }}</td>

                </tr>
            </tbody>
        </TableContainer>

        <AppPagination v-bind="data"></AppPagination>
    </div>

</template>