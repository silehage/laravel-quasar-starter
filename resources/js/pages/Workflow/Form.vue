<script setup>
import workflows from '@/routes/workflows';
import { router, useForm, usePage } from '@inertiajs/vue3';
import { Dialog, Loading } from 'quasar'
import { ref } from 'vue';

import draggable from 'vuedraggable'

const { categories, data } = usePage().props

const form = useForm({
    workflows: data.items || []
})

const submitData = () => {
    Loading.show()
    form.post(workflows.updateItems(), {
        preserveState: false,
        onFinish: () => {
            Loading.hide()
        }
    })
}

const handleDelete = (item) => {
    Dialog.create({
        title: 'Konfirmasi penghapusan data',
        cancel: true,
        ok: { label: 'Hapus' }
    }).onOk(() => {
        router.delete(workflows.destroy(item.id), {
            preserveState: false
        })
    })
}

const formItemModal = ref(false)
const formItem = useForm({
    name: '',
    category_id: data.id,
    default_cost: 0,
})

const handleAddItem = () => {
    formItem.reset()
    formItemModal.value = true
}

const submitItem = () => {
    formItem.post(workflows.store(), {
        preserveState: false
    })

}

</script>

<template>
    <AppHeader title="Workflow Form" withBack>
       
    </AppHeader>
    <div class="container">
        <TableContainer>
            <tr>
                <td>Category Name</td>
                <td>{{ data.name }}</td>
            </tr>
        </TableContainer>
    </div>
    <div class="container q-mt-md">
        <div class="card-title">
            <div>Workflows</div>
            <q-btn label="Add Item" @click="handleAddItem" color="teal"></q-btn>
        </div>
        <q-form @submit="submitData">
            <TableContainer>
                <thead>
                    <tr>
                        <th align="left">#</th>
                        <th align="left">Flow Name</th>
                        <th align="left">Default Cost</th>
                        <th align="right"></th>
                    </tr>

                </thead>
                <draggable v-model="form.workflows" tag="tbody" item-key="id" handle=".handle">
                    <template #item="{ element, index }">
                        <tr>
                            <td>
                                <q-icon name="drag_indicator" class="handle" size="sm"></q-icon>
                            </td>
                            <td>{{ element.name }}</td>
                            <td>
                                <InputNumber dense square filled style="width:150px"
                                    v-model="form.workflows[index].default_cost">
                                </InputNumber>
                            </td>
                            <td>
                                <q-btn icon="delete" color="red" size="11px" round
                                    @click="handleDelete(element)"></q-btn>
                            </td>
                        </tr>
                    </template>
                </draggable>
            </TableContainer>
            <div class="q-pt-lg">
                 <q-btn label="Update Data" class="w-full" type="submit" color="primary" :disable="!form.isDirty" :loading="form.processing"
            @click="submitData"></q-btn>
            </div>
        </q-form>

        <q-dialog v-model="formItemModal">
            <div class="container w-md">
                <div class="card-title">
                    <div>Form Item</div>
                    <q-btn icon="close" dense flat v-close-popup></q-btn>
                </div>
                <q-form @submit="submitItem">
                    <div class="q-gutter-y-md">
                        <q-select readonly :options="categories" label="Category" v-model="formItem.category_id"
                            emit-value map-options :readonly="isEdit"></q-select>
                        <q-input label="Name" v-model="formItem.name"></q-input>
                        <InputNumber label="Default Cost" v-model="formItem.default_cost"></InputNumber>
                        <q-btn type="submit" color="primary" class="w-full" label="Submit Data"></q-btn>

                    </div>
                </q-form>

            </div>
        </q-dialog>
    </div>
</template>