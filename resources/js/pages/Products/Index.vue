<script setup>
import products from '@/routes/products';
import workflows from '@/routes/workflows';
import { router, useForm, usePage } from '@inertiajs/vue3';
import { reactive, ref } from 'vue';


const { data, categories, query } = usePage().props

const filter = reactive({
    hasWorkflow: query.hasWorkflow || 'all'
})

const editModal = ref(false)

const form = useForm({
    id: '',
    name: '',
    workflow_category_id: ''
})

const handleEditWorkflow = (item) => {
    form.id = item.id
    form.name = item.name
    form.workflow_category_id = item.workflow_category_id ?? ''
    editModal.value = true
}

const updateData = () => {
    form.put(products.update(form.id), {
        preserveState: false
    })
}

</script>

<template>
    <AppHeader title="Product Settings"></AppHeader>
    <div class="q-mb-md bg-white">
        <q-tabs align="left" v-model="filter.hasWorkflow"
            @update:model-value="router.visit(products.index({ query: filter }), { replace: true })">
            <q-tab name="all" label="Semua"></q-tab>
            <q-tab name="1" label="Sudah Input kategori"></q-tab>
            <q-tab name="0" label="Perlu input Kategori"></q-tab>
        </q-tabs>
    </div>
    <div class="container">
        <TableContainer>
            <thead>
                <tr>
                    <th align="left">#</th>
                    <th align="left">Name</th>
                    <th align="left">Code</th>
                    <th align="left">Manufacturer</th>
                    <th align="left">Workflow Category</th>
                    <th align="left">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, i) in data.data" :key="i">
                    <td>{{ data.from + i }}</td>
                    <td>{{ item.name }}</td>
                    <td>{{ item.code }}</td>
                    <td>{{ item.manufactur_name }}</td>
                    <td>{{ item.workflow_category }}</td>
                    <td class="q-gutter-sm">
                        <q-btn icon="edit" color="blue" round size="11px" @click="handleEditWorkflow(item)"></q-btn>
                        <q-btn icon="visibility" color="purple" round size="11px" @click="router.get(products.show(item.id))"></q-btn>
                    </td>
                </tr>
            </tbody>
        </TableContainer>
        <AppPagination v-bind="data"></AppPagination>
        <q-dialog v-model="editModal">
            <div class="container w-md">
                <div class="card-title">
                    <div>Edit kategori</div>
                    <q-btn flat icon="close" v-close-popup></q-btn>
                </div>
                <q-form @submit="updateData">
                    <div class="q-gutter-y-md">
                        <q-input readonly v-model="form.name" label="Product"></q-input>
                        <q-select v-model="form.workflow_category_id" emit-value map-options :options="categories.map(el => ({label: el.name, value: el.id}))" label="Category"></q-select>
                    <q-btn type="submit" label="Submit Data" color="primary" class="w-full"></q-btn>
                    </div>
                </q-form>
            </div>
        </q-dialog>
    </div>
</template>