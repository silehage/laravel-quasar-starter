<script setup>
import products from '@/routes/products';
import { router, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const { data, categories } = usePage().props


const isDirty = ref(false)

const form = useForm({
    workflows: data.items ?? []
})

const updateData = () => {

    form.post(products.updateWorkflow(data.id), {
        preserveState: false,
        replace: true
    })
}

</script>

<template>
    <AppHeader title="Product Workflow Detail" withBack>
        <q-btn label="Update" color="primary" :disable="!form.isDirty" :loading="form.processing" @click="updateData"></q-btn>
    </AppHeader>
    <div class="container">
        <TableContainer>
            <tr>
                <td>Product</td>
                <td>{{ data.name }}</td>
            </tr>
            <tr>
                <td>Workflow Category</td>
                <td>
                    <span>
                        <q-badge>{{ data.workflow_category }}</q-badge>
                    </span>
                </td>
            </tr>
        </TableContainer>
    </div>

    <div class="container q-mt-md">
        <div class="card-title">Workflows</div>
        <TableContainer>
            <thead>
                <tr>
                    <th align="left">No</th>
                    <th align="left">Name</th>
                    <th align="left">Cost</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(flow, i) in form.workflows" :key="i">
                    <td>{{ flow.sort }}</td>
                    <td>{{ flow.workflow_name }}</td>
                    <td>
                        <InputNumber dense square style="width: 125px;" filled v-model="form.workflows[i].cost" @update:model-value="isUpdating"></InputNumber>
                    </td>
                </tr>
            </tbody>
        </TableContainer>
    </div>
</template>