<script setup>
import { numberFormat } from '@/lib/utils';
import workflows from '@/routes/workflows';
import { router, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { Dialog } from 'quasar'

const { data, categories } = usePage().props

const formModal = ref(false)
const form = useForm({
    itemId: "",
    name: "",
    cost: 0,
    category_id: '',
    sort: 1
})

const defaultOpened = ref(localStorage.getItem('showItem') || null)

const showItem = (val) => {
    defaultOpened.value = val
    localStorage.setItem('showItem', val)
}
</script>

<template>
    <AppHeader title="Production Workflows">
    </AppHeader>
    <div class="container">
        <q-expansion-item v-for="list in data" :key="list.id" group="workflow"
            :default-opened="defaultOpened == list.id" @show="(e) => showItem(list.id)" :label="list.name"
            header-class="text-sm text-weight-bold">
            <!-- <template #header>

                <q-item-section>{{ list.name }}</q-item-section>

                <q-item-section side>
                    <div class="row items-center">
                        <q-icon name="star" color="red" size="24px" />
                        <q-icon name="star" color="red" size="24px" />
                        <q-icon name="star" color="red" size="24px" />
                    </div>
                </q-item-section>
            </template> -->

            <div class="flex justify-end q-pt-sm">
                <q-btn icon="edit" label="Edit Item" color="blue" no-caps class="btn-action"
                    @click="router.visit(workflows.edit(list.id))"></q-btn>
            </div>

            <TableContainer>
                <thead>
                    <tr>
                        <th align="left">Flow Name</th>
                        <th align="left">DefaultCost</th>
                    </tr>

                </thead>
                <tbody>
                    <tr v-for="item in list.items" :key="item.id">
                            <td>{{ item.name }}</td>
                            <td>{{ numberFormat(item.cost) }}</td>
                    </tr>
                </tbody>
            </TableContainer>

        </q-expansion-item>

        <q-dialog v-model="formModal">
            <div class="container w-md">
                <div class="card-title">
                    <div>Form Edit</div>
                    <q-btn icon="close" dense flat v-close-popup></q-btn>
                </div>
                <q-form @submit="submitData">
                    <div class="q-gutter-y-md">
                        <q-select :options="categories" label="Category" v-model="form.category_id" emit-value
                            map-options :readonly="isEdit"></q-select>
                        <q-input label="Name" v-model="form.name"></q-input>
                        <InputNumber label="Urutan" v-model="form.sort"></InputNumber>
                        <InputNumber label="Komisi" v-model="form.cost"></InputNumber>
                        <q-btn type="submit" color="primary" class="w-full" label="Submit Data"></q-btn>

                    </div>
                </q-form>

            </div>
        </q-dialog>
    </div>

</template>