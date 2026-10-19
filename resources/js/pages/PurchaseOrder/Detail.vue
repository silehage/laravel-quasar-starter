<script setup>
import { Dialog, LocalStorage } from "quasar";
import { computed, reactive, ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { dateFormat, formatQueryparams, getNotifColor } from '@/lib/utils';
import purchaseOrders from "@/routes/purchase-orders";

const { statuses, factories, categories } = usePage().props

const data = ref(usePage().props.data.data)
const progressdata = ref(usePage().props.data.progress)

const item_selected = ref(null)

const hide_statuses = ref([])
const query = reactive({
    per_page: usePage().props.query.per_page || 10,
    page: usePage().props.query.page || 1,
    categoryId: usePage().props.query.categoryId || "ALL",
    status: usePage().props.query.status || "",
    factoryId: usePage().props.query.factoryId || '',
    search: usePage().props.query.search || ''
})

const selected_status_item = ref(null)
const formFinishedEtdModal = ref(false)
const formFinishedEtd = reactive({
    entitiable_id: "",
    date: "",
    row_id: "",
})

const formFinishedEtdBulkModal = ref(false)
const formFinishedEtdBulk = reactive({
    entitiable_id: "",
    date: "",
    row_ids: [],
})


const progressValue = computed(() => {
    let pr = 0
    if (progressdata && progressdata.total_qty > 0) {
        pr = Number(progressdata.partial_qty) / Number(progressdata.total_qty)
    }
    return pr
})
const progressLabel = computed(() => {
    return `${(progressValue.value * 100).toFixed(2)}'%'`
})

const handleSearchData = () => {
    query.page = "1"
    query.categoryId = "ALL"
    query.status = ""
    query.factoryId = ''
    getData()

}
const handleFilterData = () => {
    query.search = ''
    query.page = "1"
    getData()
}

const removeHideStatus = (index) => {
    data.value.data[index].line_items.map((p) => (p.is_selected = false));
}


const showing = ref(null)
const expanded = (id) => {
    if (showing.value) {
        return showing.value == id;
    }

    return false;

}
const handleFilter = (cat = null) => {
    query.categoryId = cat?.categoryId;
    query.page = "1";
    query.search = "";
    getData();
}
const getData = () => {
    router.visit(purchaseOrders.detail({ query: query }))

}

</script>


<template>
    <div>
        <AppHeader title="Manage Purchase Order">
        </AppHeader>

        <div class="container">

            <q-expansion-item label="Filter">
                <q-card-section>
                    <div>
                        <q-input filled v-model="query.search" label="Search" :debounce="1000"
                            @update:model-value="handleSearchData" clearable @clear="handleFilterData"
                            placeholder="Search by PO Number, Product Name or SKU"></q-input>
                    </div>
                    <div class="q-mt-sm">
                        <q-select label="Filter By Factory" filled :options="[
                            ...[
                                { label: 'ALL', value: '' },
                            ],
                            ...factories.map((el) => ({ label: el.name, value: el.id })),
                        ]" v-model="query.factoryId" @update:model-value="handleFilterData" map-options
                            emit-value></q-select>
                    </div>

                    <div class="q-mt-sm">
                        <q-select label="Filter By Status" filled :options="[
                            ...[
                                { label: 'ACTIVE', value: '' },
                                { label: 'Partial', value: 'partial' },
                            ],
                            ...statuses
                        ]" v-model="query.status" @update:model-value="handleFilterData" map-options
                            emit-value></q-select>
                    </div>


                </q-card-section>
            </q-expansion-item>
        </div>
        <div class="container q-mt-md">

            <div>
                <div>
                    <div class="q-mt-xs q-gutter-sm">
                        <q-btn size="13px" v-for="(cat, idx) in categories" :key="idx" unelevated color="primary"
                            @click="handleFilter(cat)" :outline="query.categoryId != cat.categoryId">{{
                                cat.categoryName
                            }}
                            <q-badge floating color="teal" v-if="cat.itemCount > 0">{{
                                cat.itemCount
                            }}</q-badge>
                        </q-btn>
                    </div>
                </div>
                <div class="q-my-md" v-if="progressdata">
                    <div class="text-weight-medium">Progress {{ progressdata.partial_qty }} / {{ progressdata.total_qty
                    }}
                        ({{ progressLabel }})</div>
                    <q-linear-progress size="30px" :value="progressValue" color="accent" class="q-mt-xs" stripe rounded>
                        <div class="absolute-full flex flex-center">
                            <q-badge color="white" text-color="accent" :label="progressLabel" />
                        </div>
                    </q-linear-progress>
                </div>
                <div class="q-mt-md table-responsive">
                    <table class="table bordered">
                        <thead>
                            <tr>
                                <th align="left" class="table-line-color"></th>
                                <th align="left">#</th>
                                <th align="left">Product</th>
                                <th align="left">Po Number</th>
                                <th align="left">Progress</th>
                                <th align="left">Created</th>
                                <th align="left">ETD Finished</th>
                                <th align="left">Status</th>
                                <th align="left">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-for="(item, idx) in data.data" :key="item.id">
                                <tr :class="{ selected: showing == item.id }" @click="() =>
                                    showing == item.id ? (showing = null) : (showing = item.id)
                                " class="cursor-pointer">
                                    <td class="table-line-color" :class="`bg-${getNotifColor(item.etd_finished_at)}`">
                                    </td>
                                    <td>{{ data.from + idx }}</td>
                                    <td>{{ item.product_name }}</td>
                                    <td>{{ item.po_number }}</td>
                                    <td>{{ item.partial_qty }} / {{ item.quantity }}</td>
                                    <td>{{ dateFormat(item.created_at) }}</td>
                                    <td>{{ dateFormat(item.etd_finished_at) }}</td>
                                    <td>
                                        <q-badge :color="item.status_color">
                                            {{ item.status_label }}
                                        </q-badge>
                                    </td>
                                    <td>
                                        <div class="q-gutter-xs">
                                            <q-btn dense :icon="showing == item.id
                                                ? 'keyboard_arrow_down'
                                                : 'keyboard_arrow_right'
                                                " flat></q-btn>
                                        </div>
                                    </td>
                                </tr>

                                <tr class="no-padding flat">
                                    <td colspan="18">
                                        <q-slide-transition>
                                            <div v-show="expanded(item.id)"
                                                class="bg-grey-3 w-full q-px-md q-pt-md q-pb-xl" flat>
                                                <!-- <q-separator color="primary" size="2px"></q-separator> -->
                                                <div class="q-mb-sm row justify-between items-center">
                                                    <q-select dense outlined class="text-xs w-md" bg-color="white"
                                                        :options="statuses" emit-value map-options
                                                        v-model="hide_statuses" label="Hidding Status" multiple
                                                        use-chips
                                                        @update:model-value="removeHideStatus(idx)"></q-select>
                                                </div>
                                                <table class="table bordered table-sm q-pb-sm bg-white">
                                                    <thead>
                                                        <tr>
                                                            <th align="left" class="table-line-color"></th>
                                                            <th align="left" class="">PO Number</th>
                                                            <th align="left" class="">Production Code</th>
                                                            <th align="left">Workflow</th>
                                                            <th align="left" class="">Status</th>
                                                            <th align="left" class="">ETD Finished</th>
                                                            <th align="left" class="">Finished</th>
                                                            <th align="left" class="">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <template v-for="(row, rowIdx) in item.line_items"
                                                            :key="rowIdx">

                                                            <tr v-if="!hide_statuses.includes(row.status)">
                                                                <td class="table-line-color"
                                                                    :class="`bg-${row.status_color}`">
                                                                </td>
                                                                <td>{{ item.po_number }}</td>
                                                                <td>{{ row.barcode }}</td>
                                                                <td>{{ row.workflow }}</td>
                                                                <td>

                                                                    <q-badge :color="row.status_color">
                                                                        {{ row.status_label }}
                                                                    </q-badge>

                                                                </td>
                                                                <td>
                                                                    <div>
                                                                        {{
                                                                            row.etd_finished_at
                                                                                ? dateFormat(row.etd_finished_at)
                                                                                : "-"
                                                                        }}
                                                                    </div>
                                                                    <div v-if="row.etd_finished_formated"
                                                                        class="text-amber-9">
                                                                        {{ row.etd_finished_formated }}
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div>
                                                                        {{
                                                                            row.finished_at
                                                                                ? dateFormat(row.finished_at)
                                                                                : "-"
                                                                        }}
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </template>
                                                    </tbody>
                                                </table>
                                                <!-- <q-separator color="primary" size="2px"></q-separator> -->
                                            </div>
                                        </q-slide-transition>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                    <AppPagination v-bind="data" />
                </div>
            </div>

        </div>

    </div>
</template>