<script setup>
import { getDiffDate } from '@/lib/utils';
import productions from '@/routes/productions';
import { router, usePage } from '@inertiajs/vue3';
import { reactive } from 'vue';

const { data, categories, workflows } = usePage().props

const queryParams = reactive({
    categoryId: usePage().props.query.categoryId || '',
    workflowId: usePage().props.query.workflowId || '',
})

const handleFilterCategory = (id) => {
    queryParams.workflowId = ''
    queryParams.categoryId = id
    router.visit(productions.currentProcess({ query: queryParams }))
}
const handleFilterWorkflow = (id) => {
    queryParams.workflowId = id
    router.visit(productions.currentProcess({ query: queryParams }))
}
</script>

<template>
    <AppHeader title="Progress List">
    </AppHeader>

    <div class="q-mb-md">
        <div class="bg-white">
            <q-tabs indicator-color="primary" v-model="queryParams.categoryId" align="left" mobile-arrows outside-arrows
                @update:model-value="handleFilterCategory">
                <q-tab label="ALL" name=""></q-tab>
                <q-tab v-for="item in categories" :label="item.name" :name="item.id">
                    <q-badge floating v-if="item.total_count">{{ item.total_count }}</q-badge>
                </q-tab>
            </q-tabs>
        </div>
        <div class="text-center q-gutter-sm q-my-sm">
    
            <q-btn v-for="item in workflows" :label="item.name" :name="item.id" size="12px" no-caps 
            :color="queryParams.workflowId == item.id ? 'primary' : 'white'" 
            :text-color="queryParams.workflowId == item.id ? 'white' : 'dark'" 
            @click="handleFilterWorkflow(item.id)">
                <q-badge floating v-if="item.total_count">{{ item.total_count }}</q-badge>
            </q-btn>
        </div>
    </div>
    <!-- <div class="q-mb-md">
        <q-tabs v-model="queryParams.workflowId" align="center" dense mobile-arrows outside-arrows no-caps
            @update:model-value="router.visit(productions.currentProcess({ query: queryParams }))">
            <q-tab label="ALL" name=""></q-tab>
            <q-tab v-for="item in workflows" :label="item.name" :name="item.id">
                <q-badge floating v-if="item.total_count">{{ item.total_count }}</q-badge>
            </q-tab>
        </q-tabs>
    </div> -->

    
    <div class="container">

        <TableContainer>
            <thead>
                <th align="left">#</th>
                <th align="left">Barcode</th>
                <th align="left">Produk</th>
                <th align="left">Tahapan</th>
                <th align="left">Status</th>
                <th align="left">Est Waktu Selesai</th>
                <th align="left">Worker</th>
                <th align="left">Tanggal Mulai</th>
                <th align="left">Tanggal Selesai</th>
                <th align="left">Durasi Pengerjaan</th>
            </thead>
            <tbody>
                <tr v-for="(item, i) in data.data" :key="i">
                    <td>{{ data.from + i }}</td>
                    <td>{{ item.barcode }}</td>
                    <td>{{ item.product_line.name }}</td>
                    <td>
                        <q-badge color="grey-7">{{ item.current_workflow.name }}</q-badge>
                    </td>
                    <td>
                        <q-badge :color="item.state_color">{{ item.state_label }}</q-badge>
                    </td>
                    <td>{{ item.etd_finished_at }} ({{ getDiffDate(item.etd_finished_at) }} hari lagi)</td>
                    <td>{{ item.latest_workflow.worker.name }}</td>
                    <td>{{ item.latest_workflow.start_at }}</td>
                    <td>{{ item.latest_workflow.finished_at ? item.latest_workflow.finished_at : '-' }}</td>
                    <td>{{ getDiffDate(item.latest_workflow.start_at, item.latest_workflow.finished_at, true) }} Hari</td>
                </tr>
            </tbody>
        </TableContainer>

        <AppPagination v-bind="data" withEmptyData></AppPagination>

    </div>
</template>