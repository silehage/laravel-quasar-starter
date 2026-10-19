<script setup>
import { dateFormat } from '@/lib/utils';
import purchaseOrders from '@/routes/purchase-orders';
import { router, usePage } from '@inertiajs/vue3';
import { reactive, ref } from 'vue';

const { data } = usePage().props

const queryParams = reactive({
    status: usePage().props.status || '',
    factoryId: usePage().props.factoryId || '',
})
const getData = () => {
    router.get(purchaseOrders.handling({ query: queryParams }))
}

const list = ref([])

</script>

<template>
    <AppHeader title="Handle Purchase Order"></AppHeader>
<div v-for="(item, key ) in data" class="q-pb-lg">
    <div class="card-title">{{ key }}</div>

    <div class="grid-auto">
        <div class="container" v-for="(val, i) in item">
                <div class="text-weight-bold">{{ val.workflow_name }}</div>
                <div class="q-mt-sm text-md">{{ val.total }}</div>
                <!-- <div class="q-mt-md">
                    <q-btn @click="router.visit(purchaseOrders.handling(val.workflow_id))" label="Handle" color="primary" class="w-full"></q-btn>
                </div> -->
        </div>
    </div>
</div>

   
</template>