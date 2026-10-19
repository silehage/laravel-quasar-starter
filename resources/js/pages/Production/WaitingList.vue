<script setup>
import { getDiffDate } from '@/lib/utils';
import productions from '@/routes/productions';
import { router, useForm, usePage } from '@inertiajs/vue3';
import { computed, reactive, ref } from 'vue';

const { data, categories, users, workflows } = usePage().props

const queryParams = reactive({
    categoryId: usePage().props.query.categoryId || '',
    workflowId: usePage().props.query.workflowId || '',
})

const form = useForm({
    worker_id: '',
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
    return form.worker_id && form.items.length > 0
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

const handleFilterCategory = (id) => {
    queryParams.workflowId = ''
    queryParams.categoryId = id
    router.visit(productions.waitingList({ query: queryParams }))
}
const handleFilterWorkflow = (id) => {
    queryParams.workflowId = id
    router.visit(productions.waitingList({ query: queryParams }))
}

</script>

<template>
    <AppHeader title="Waiting List">
        <q-btn label="Bagikan Tugas" color="primary" @click="handleSelectJob"></q-btn>
    </AppHeader>

    <div class="q-mb-md">

        <div class="bg-white">
            <q-tabs indicator-color="primary" v-model="queryParams.categoryId" align="left"
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


    <div class="container">

        <TableContainer>
            <thead>
                <th align="left" v-if="isSelectJob">
                    <q-checkbox dense v-model="isSelectAll" @update:model-value="selectAllItem"></q-checkbox>
                </th>
                <th align="left">#</th>
                <th align="left">Barcode</th>
                <th align="left">Produk</th>
                <th align="left">status</th>
                <th align="left">Tahapan Selanjutnya</th>
                <th align="left">Est Waktu Selesai</th>
            </thead>
            <tbody>
                <tr v-for="(item, i) in data.data" :key="i">
                    <td style="width:60px" v-if="isSelectJob">
                        <q-checkbox dense :val="item.id" v-model="form.items"
                            @update:model-value="selectItem"></q-checkbox>
                    </td>
                    <td>{{ data.from + i }}</td>
                    <td>{{ item.barcode }}</td>
                    <td>{{ item.product_line.name }}</td>
                    <td>{{ item.workflow_status }}</td>
                    <td>{{ item.current_workflow.name }}</td>
                    <td>{{ item.etd_finished_at }} ({{ getDiffDate(item.etd_finished_at) }} day)</td>
                </tr>
            </tbody>
        </TableContainer>

        <AppPagination v-bind="data"></AppPagination>

        <q-dialog v-model="isSelectJob" seamless persistent position="right">
            <q-card style="width:320px" class="bg-primary">
                <q-card-section class="q-pa-lg bg-white" style="margin: 6px;">

                    <div class="card-title q-pb-lg">
                        <div>Assign Worker</div>
                        <q-btn icon="close" flat round v-close-popup></q-btn>
                    </div>
                    <q-form @submit="submitWorker" class="q-gutter-y-md">
                        <q-select filled :options="users" emit-value map-options v-model="form.worker_id"
                            label="Pilih User">
                        </q-select>
                        <div class="q-pa-sm text-weight-bold">Item Selected: {{ form.items.length }}</div>
                        <q-btn :disable="!isFormReady" label="Submit Tugas" type="submit" color="primary"
                            class="w-full"></q-btn>
                    </q-form>
                </q-card-section>
            </q-card>
        </q-dialog>
    </div>
</template>