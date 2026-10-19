<template>
  <q-layout view="lhh LpR lff" class="dashboard">

    <q-header>
      <q-toolbar class="bg-white box-shadow text-dark">
        <q-btn dense flat round icon="menu" @click="toggleLeftDrawer" />
        <q-toolbar-title>
          <Link href="/" class="text-dark">
            {{ usePage().props.name }}
          </Link>
        </q-toolbar-title>

        <q-btn icon="account_circle" flat round>
          <q-menu auto-close>
            <q-list separator>
              <q-item clickable @click="router.visit(logout())">
                <q-item-section side>
                  <q-icon name="logout"></q-icon>
                </q-item-section>
                <q-item-section>Logout</q-item-section>
              </q-item>
            </q-list>
          </q-menu>
        </q-btn>
      </q-toolbar>
    </q-header>

    <q-drawer show-if-above v-model="leftDrawerOpen" side="left" :mini="is_mini" :width="240" dark class="bg-grey-10">
      <q-list>
        <q-item-label>
          <div class="q-px-md q-mt-sm">
            <q-select outlined dark label="Manufaktur" v-model:model-value="manufacturerId" 
            :options="manufacturerList.map(e => ({label: e.name, value: e.id}))"
              emit-value map-options @update:model-value="setCurrentId"></q-select>

          </div>

        </q-item-label>
        <q-separator class="q-mt-md"></q-separator>
        <!-- <q-item header>Navigation</q-item> -->
        <template v-for="(nav, idx) in navigations" :key="idx">
          <q-expansion-item v-if="nav.items && nav.items.length > 0" group="menu">

            <template v-slot:header>
              <!-- <q-item-section side>
                <q-icon :name="nav.icon" color="grey-4"></q-icon>
              </q-item-section> -->
              <q-item-section>
                <q-item-label>{{ nav.title }}</q-item-label>
              </q-item-section>
              <q-tooltip v-if="is_mini" anchor="center right" self="center left" :offset="[10, 10]"
                class="bg-purple text-white">{{ nav.title }}</q-tooltip>

            </template>
            <q-list>
              <template v-for="(navItem, i) in nav.items" :key="i">
                <q-item v-if="navItem.visible" clickable :href="navItem.href.url"
                  @click.prevent="handleClickMenu(navItem.href)">
                  <q-item-section side class="text-grey-6">
                    <q-icon name="remove"></q-icon>
                  </q-item-section>
                  <q-item-section>
                    <q-item-label>{{ navItem.title }}</q-item-label>
                  </q-item-section>
                  <q-tooltip v-if="is_mini" anchor="center right" self="center left" :offset="[10, 10]"
                    class="bg-purple text-white">{{ navItem.title }}</q-tooltip>

                </q-item>
              </template>
            </q-list>
          </q-expansion-item>
          <template v-else>

            <q-item v-if="nav.visible" clickable :href="nav.href.url" @click.prevent="handleClickMenu(nav.href)">
              <!-- <q-item-section side>
                <q-icon :name="nav.icon" color="grey-4"></q-icon>
              </q-item-section> -->
              <q-item-section>
                <q-item-label>{{ nav.title }}</q-item-label>
              </q-item-section>
              <q-tooltip v-if="is_mini" anchor="center right" self="center left" :offset="[10, 10]"
                class="bg-purple text-white">{{ nav.title }}</q-tooltip>

            </q-item>
          </template>
        </template>
      </q-list>
    </q-drawer>

    <q-page-container>
      <q-page padding class="bg-grey-2">
        <slot />
      </q-page>
    </q-page-container>

    <q-footer class="bg-transparent">
      <q-toolbar>
        <q-toolbar-title>
          <div class="q-px-md text-xs text-grey-6"> &copy; 2025 Allrights Reserved</div>
        </q-toolbar-title>
      </q-toolbar>
    </q-footer>

  </q-layout>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { dashboard, logout } from '@/routes';
import { router, Link, usePage } from '@inertiajs/vue3';
import { useQuasar } from 'quasar';
import accounts from '@/routes/accounts';
import roles from '@/routes/roles';
import permissions from '@/routes/permissions';
import master from '@/routes/master';
import purchaseOrders from '@/routes/purchase-orders';
import workflows from '@/routes/workflows';
import manufacturer from '@/routes/manufacturer';
import products from '@/routes/products';
import productions from '@/routes/productions';
import revision from '@/routes/revision';

const { manufacturerId, manufacturerList } = usePage().props

const $q = useQuasar()

defineProps<{
  title?: string;
  description?: string;
}>();
const leftDrawerOpen = ref(false)
const is_mini = ref(false)

const toggleLeftDrawer = () => {

  leftDrawerOpen.value = !leftDrawerOpen.value
  return

  if ($q.platform.is.desktop) {
    leftDrawerOpen.value = true
    is_mini.value = !is_mini.value
  } else {
    leftDrawerOpen.value = !leftDrawerOpen.value
  }
}

const setCurrentId = (manufacturerId: String) => {
  router.post(manufacturer.setCurrent(manufacturerId), null, {
    onFinish: () => {
      location.reload()
    }
  })
}

const handleClickMenu = (href) => {
  if ($q.platform.is.mobile) {
    leftDrawerOpen.value = !leftDrawerOpen.value
  }
  router.visit(href)
}

const navigations = [
  {
    title: 'Dashboard',
    href: dashboard(),
    icon: 'dashboard',
    ability: 'All',
    visible: true,
    items: []
  },
  {
    title: 'Purchase Order',
    icon: 'dashboard',
    ability: 'All',
    visible: true,
    items: [
      {
        title: 'List',
        href: purchaseOrders.index(),
        ability: 'All',
        visible: true,
      },
      {
        title: 'Detail',
        href: purchaseOrders.detail(),
        ability: 'All',
        visible: true,
      },
      {
        title: 'Report',
        href: purchaseOrders.report(),
        ability: 'All',
        visible: true,
      },
    ]
  },
  {
    title: 'Production',
    icon: 'dashboard',
    ability: 'All',
    visible: true,
    items: [
      {
        title: 'Waiting List',
        href: productions.waitingList(),
        ability: 'All',
        visible: true,
      },
      {
        title: 'Progress List',
        href: productions.currentProcess(),
        ability: 'All',
        visible: true,
      },
      {
        title: 'Finishing Process',
        href: productions.scanCurrentProcess(),
        ability: 'All',
        visible: true,
      },
      {
        title: 'Finished',
        href: productions.readyToShip(),
        ability: 'All',
        visible: true,
      },
    ]
  },
  {
    title: 'Revisi',
    icon: 'dashboard',
    ability: 'All',
    visible: true,
    items: [
      {
        title: 'Revision List',
        href: revision.list(),
        ability: 'All',
        visible: true,
      },
    ]
  },
  {
    title: 'Workflows',
    icon: 'dashboard',
    ability: 'All',
    visible: true,
    items: [
      {
        title: 'Category Config',
        href: workflows.index(),
        ability: 'All',
        visible: true,
      },
      {
        title: 'Product Config',
        href: products.index(),
        ability: 'All',
        visible: true,
      },
    ]
  },
  {
    title: 'Users',
    icon: 'groups',
    ability: 'All',
    visible: true,
    items: [
      {
        title: 'Users',
        href: accounts.index(),
        icon: 'groups',
        ability: 'All',
        visible: true,
      },
      {
        title: 'Roles',
        href: roles.index(),
        icon: 'groups',
        ability: 'All',
        visible: true,
      },
      {
        title: 'Permissions',
        href: permissions.index(),
        icon: 'groups',
        ability: 'All',
        visible: true,
      },
    ]
  },
  {
    title: 'Master Data',
    icon: 'folder',
    ability: 'view-master-data',
    visible: true,
    items: [
      {
        title: 'Manufaktur',
        href: master.manufacturer(),
        icon: 'store',
        ability: 'All',
        visible: true,
      },
      {
        title: 'Factory',
        href: master.factories(),
        icon: 'store',
        ability: 'All',
        visible: true,
      },
      {
        title: 'Fulfillments',
        href: master.fulfillments(),
        icon: 'store',
        ability: 'All',
        visible: true,
      },
      {
        title: 'Product',
        href: master.products(),
        icon: 'store',
        ability: 'All',
        visible: true,
      },
      {
        title: 'Product Lines',
        href: master.productLines(),
        icon: 'store',
        ability: 'All',
        visible: true,
      },
      {
        title: 'Product Categories',
        href: master.productCategories(),
        icon: 'store',
        ability: 'All',
        visible: true,
      },

    ]
  },



]

</script>