<script setup>
import productions from '@/routes/productions';
import { useForm } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';


const input = ref(null)

const form = useForm({
    barcode: ''
})

const submitProcess = () => {
    form.post(productions.completeCurrentProcess(), {
        preserveState: false,
    })
}

onMounted(() => {
    input.value.focus()
})
</script>

<template>
    <AppHeader title="Finished Current Process"></AppHeader>

    <div class="container">
        <div class="label">Scan Or Input Barcode</div>
        <q-input outlined ref="input" v-model="form.barcode" @keyup.enter="submitProcess" clearable></q-input>
    </div>
</template>