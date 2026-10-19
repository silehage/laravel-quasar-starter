<script setup>
import { getDiffDate } from '@/lib/utils';
import revision from '@/routes/revision';
import { router, useForm, usePage } from '@inertiajs/vue3';
import { computed, reactive, ref } from 'vue';

const { data, categories, users } = usePage().props

const queryParams = reactive({
    categoryId: usePage().props.query.categoryId || ''
})

const modal = ref(false)
const selectedItem = ref(null)
const targetWorkflows = ref([])

const form = useForm({
    product: '',
    line_item_id: '',
    target_workflow_id: ''
})

const isFormReady = computed(() => {
    return form.line_item_id && form.target_workflow_id
})

const rollbackRevision = () => {
    form.post(revision.rollbackRevision(), {
        preserveState: false
    })
}

const handleSelectItem = (item) => {
    selectedItem.value = item
    targetWorkflows.value = item.workflows
    form.line_item_id = item.id
    form.target_workflow_id = ''
    form.product = item.product_line.name

    modal.value = true
}

</script>

<template>
    <AppHeader title="Revision List">
    </AppHeader>

    <div class="q-mb-md bg-white">
        <q-tabs indicator-color="primary" v-model="queryParams.categoryId" align="left"
            @update:model-value="router.visit(revision.list({ query: queryParams }))">
            <q-tab label="ALL" name=""></q-tab>
            <q-tab v-for="item in categories" :label="item.name" :name="item.id">
                <q-badge floating v-if="item.total_pending">{{ item.total_pending }}</q-badge>
            </q-tab>
        </q-tabs>
    </div>

    <div class="container">

        <TableContainer>
            <thead>
                <th align="left">#</th>
                <th align="left">Barcode</th>
                <th align="left">Produk</th>
                <th align="left">status</th>
                <!-- <th align="left">Reason</th> -->
                <th align="left">Est Waktu Selesai</th>
            </thead>
            <tbody>
                <tr v-for="(item, i) in data.data" :key="i">
                    <td>{{ data.from + i }}</td>
                    <td>{{ item.barcode }}</td>
                    <td>{{ item.product_line.name }}</td>
                    <td>{{ item.workflow_status }}</td>
                    <td>{{ item.etd_finished_at }} ({{ getDiffDate(item.etd_finished_at) }} day)</td>
                    <td>
                        <q-btn icon="edit" color="blue" class="btn-action" @click="handleSelectItem(item)"></q-btn>
                    </td>
                </tr>
            </tbody>
        </TableContainer>

        <AppPagination v-bind="data"></AppPagination>

        <q-dialog v-model="modal" persistent>
            <q-card class="w-md">
                <q-card-section>

                    <div class="card-title q-pb-lg">
                        <div>Rollback Revision</div>
                        <q-btn icon="close" flat round v-close-popup></q-btn>
                    </div>
                    <q-form @submit="rollbackRevision" class="q-gutter-y-md">
                        <q-input v-model="form.product" label="Produk" readonly></q-input>
                        <q-select filled :options="targetWorkflows" emit-value map-options
                            v-model="form.target_workflow_id" label="Target Workflow">
                        </q-select>
                        <q-btn :disable="!isFormReady" label="Submit Tugas" type="submit" color="primary"
                            class="w-full"></q-btn>
                    </q-form>
                </q-card-section>
            </q-card>
        </q-dialog>
    </div>
</template>