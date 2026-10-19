<script setup>
import { getDiffDate } from '@/lib/utils';
import productions from '@/routes/productions';
import { router, useForm, usePage } from '@inertiajs/vue3';
import { computed, reactive, ref } from 'vue';

const { data, statuses } = usePage().props

const queryParams = reactive({
    status: usePage().props.query.status || 'finished'
})

const status = [
    {
        label: 'Siap Dikirim',
        value: 'finished'
    },
    {
        label: 'Sedang Dikirim',
        value: 'shipped'
    },
    {
        label: 'Revisi',
        value: 'revision'
    },
    {
        label: 'Selesai',
        value: 'completed'
    },
]


const form = useForm({
    items: []
})

const isSelectJob = ref(false)

const handleSelectJob = () => {
    isSelectJob.value = !isSelectJob.value
    if (isSelectJob.value == false) {
        form.worker_id = ''
        form.items = []
    }
}

const isFormReady = computed(() => {
    return form.items.length > 0
})

const submitWorker = () => {
    form.post(productions.assignWorker(), {
        preserveState: false
    })
}

const isSelectAll = ref(false)

const selectAllItem = () => {
    if (isSelectAll.value) {

        if (!form.items.length) {
            form.items = data.data.map(el => el.id)
        } else {
            form.items = []
        }
    } else {

        form.items = []
    }

}

const selectItem = () => {
    isSelectJob.value = true
}

const title = computed(() => {
    return status.find(el => el.value == queryParams.status)?.label || 'Selesai'
})


</script>

<template>
    <AppHeader :title="title">
         <q-btn v-if="queryParams.status == 'finished'" label="Kirim Barang" color="primary" @click="handleSelectJob"></q-btn>
    </AppHeader>

    <div class="q-mb-md bg-white">
        <q-tabs indicator-color="primary" v-model="queryParams.status" align="left"
            @update:model-value="router.visit(productions.readyToShip({ query: queryParams }))">
            <q-tab v-for="item in status" :label="item.label" :name="item.value">
            </q-tab>
        </q-tabs>
    </div>
     
    <div class="container">

        <TableContainer>
            <thead>
                 <th align="left" v-if="isSelectJob">
                    <q-checkbox dense v-model="isSelectAll" @update:model-value="selectAllItem"></q-checkbox>
                </th>
                <th align="left">#</th>
                <th align="left">Barcode</th>
                <th align="left">Produk</th>
                <th align="left">Status</th>
                <!-- <th align="left">Workflow Status</th> -->
                <th align="left">Est Waktu Selesai</th>
                <th align="left">Worker</th>
                <th align="left">Tanggal Mulai</th>
                <th align="left">Tanggal Selesai</th>
                <th align="left">Durasi Pengerjaan</th>
            </thead>
            <tbody>
                <tr v-for="(item, i) in data.data" :key="i">
                     <td style="width:60px" v-if="isSelectJob">
                        <q-checkbox dense :val="item.id" v-model="form.items" @update:model-value="selectItem"></q-checkbox>
                    </td>
                    <td>{{ data.from + i }}</td>
                    <td>{{ item.barcode }}</td>
                    <td>{{ item.product_line.name }}</td>
                    <td>
                        <q-badge :color="item.status_color">{{ item.status_label }}</q-badge>
                    </td>
                    <!-- <td>
                        <q-badge :color="item.state_color">{{ item.state_label }}</q-badge>
                    </td> -->
                    <td>{{ item.etd_finished_at }} ({{ getDiffDate(item.etd_finished_at) }} hari lagi)</td>
                    <td>{{ item.latest_workflow.worker.name }}</td>
                    <td>{{ item.latest_workflow.start_at }}</td>
                    <td>{{ item.latest_workflow.finished_at ? item.latest_workflow.finished_at : '-' }}</td>
                    <td>{{ getDiffDate(item.latest_workflow.start_at, item.latest_workflow.finished_at, true) }} Hari</td>
                </tr>
            </tbody>
        </TableContainer>

        <AppPagination v-bind="data" withEmptyData></AppPagination>

          <q-dialog v-model="isSelectJob" seamless persistent position="right" >
            <q-card style="width:320px" class="bg-primary">
                <q-card-section class="q-pa-lg bg-white" style="margin: 6px;">

                    <div class="card-title q-pb-lg">
                        <div>Pilih Barang</div>
                        <q-btn icon="close" flat round v-close-popup></q-btn>
                    </div>
                    <q-form @submit="submitWorker" class="q-gutter-y-md">
                    <div class="q-pa-sm text-weight-bold">Item Selected: {{ form.items.length }}</div>
                        <q-btn :disable="!isFormReady" label="Kirim Sekarang" type="submit" color="primary" class="w-full"></q-btn>
                    </q-form>
                </q-card-section>
            </q-card>
        </q-dialog>

    </div>
</template>