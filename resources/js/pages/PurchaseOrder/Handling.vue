<script setup>
import { dateFormat, getDiffDate, getNotifClass, getNotifColor } from '@/lib/utils';
import purchaseOrders from '@/routes/purchase-orders';
import { router, usePage } from '@inertiajs/vue3';


const { data } = usePage().props

const getBgClass = (row) => {

}

</script>

<template>
    
    <AppHeader title="Handle Work"></AppHeader>

    <div class="container">
        <TableContainer>
            <thead>
                <tr>
                    <th align="left" class="table-line-color"></th>
                    <th align="left" class="">Product</th>
                    <th align="left" class="">PO Number</th>
                    <th align="left" class="">PO Item Number</th>
                    <th align="left" class="">Barcode</th>
                    <!-- <th align="left" class="">Status</th> -->
                    <th align="left" class="">Est Finished Date</th>
                    <th align="left" class="">Action</th>
                </tr>
            </thead>
            <tbody>
                <template v-for="(row, rowIdx) in data.data" :key="rowIdx">

                    <tr>
                        <td class="table-line-color" :class="getNotifClass(row.etd_finished_at)">
                        </td>
                        <td>{{ row.product_name }}</td>
                        <td>{{ row.po_number }}</td>
                        <td>{{ row.po_item_number }}</td>
                        <td>{{ row.barcode }}</td>
                        <!-- <td>

                            <q-badge :color="row.status_color">
                                {{ row.status }}
                            </q-badge>

                        </td> -->
                        <td>
                            <div>
                                {{
                                    row.etd_finished_at
                                        ? dateFormat(row.etd_finished_at)
                                        : "-"
                                }}
                                ({{ getDiffDate(row.etd_finished_at) }} day)
                            </div>
                        </td>
                        <td>
                            <q-btn icon="visibility" color="blue" class="btn-action" @click="router.visit(purchaseOrders.workflows(row.id))"></q-btn>
                        </td>
                    </tr>
                </template>
            </tbody>
        </TableContainer>

        <AppPagination v-bind="data"></AppPagination>
    </div>

</template>