<template>
  <div class="flex flex-col min-h-screen">
    <div
      v-if="auth"
      class="flex items-center bg-indigo-600 px-4 py-2 fixed top-0 w-full z-40"
    >
      <Breadcrumbs :items="breadcrumbs" />
      <div class="flex-1" />
      <div class="flex justify-end items-center space-x-2">
        <div class="text-white text-sm">
          Welcome back <span class="font-medium">{{ auth.name }}</span>
        </div>
        <Button @click="logout">
          Log out
        </Button>
      </div>
    </div>

    <div
      class="flex flex-1"
      :class="{
        'pt-[52px]': auth
      }"
    >
      <div
        v-if="auth"
        class="w-80 p-4 fixed"
      >
        <nav>
          <ul class="flex flex-col space-y-1">
            <template
              v-for="item in menuItems"
              :key="item.url"
            >
              <li>
                <Link
                  :href="item.url"
                  class="flex p-2 rounded transition-all text-sm text-slate-500 hover:text-slate-800 hover:bg-slate-300"
                  :class="{
                    'bg-slate-300 text-slate-800': item.active()
                  }"
                >
                  {{ item.text }}
                </Link>
              </li>
            </template>
          </ul>
        </nav>
      </div>

      <main
        class="flex-1 flex flex-col"
        :class="{
          'pl-80': auth
        }"
      >
        <slot />
      </main>
    </div>

    <Dialog
      title="Info"
      :open="isMessageDialogOpen"
      @close="isMessageDialogOpen = false"
    >
      {{ message }}
    </Dialog>
  </div>
</template>

<script setup>
import Breadcrumbs from '@/components/Breadcrumbs.vue'
import Button from '@/components/Button.vue'
import Dialog from '@/components/Dialog.vue';
import { Link } from '@inertiajs/vue3'
import { ref, watch, onMounted, onBeforeUnmount } from 'vue';
import { router } from '@inertiajs/core';

const props = defineProps({
  auth: { type: Object, default: null },
  breadcrumbs: { type: Array, default: () => []},
  message: { type: String, default: null }
})

const isMessageDialogOpen = ref(props.message !== null)

const menuItems = [
  {
    text: 'Home',
    url: route('home'),
    active: () => route().current('home')
  },
  {
    text: 'Accounts',
    url: route('accounts.index'),
    active: () => route().current('accounts.index')
  },
  {
    text: 'Transactions',
    url: route('transactions.index'),
    active: () => route().current('transactions.index')
  },
  {
    text: 'Transfers',
    url: route('transfers.index'),
    active: () => route().current('transfers.index')
  },
  {
    text: 'Currencies',
    url: route('currencies.index'),
    active: () => route().current('currencies.index')
  }
]

const logout = () => router.post(route('logout'))

const subscribeToRealtimeEvents = (auth) => {
  window.Echo.private(`users.${auth.id}`)
    .listenToAll((eventName, data) => {
      console.log("Event ::  " + eventName + ", data is ::" + JSON.stringify(data));
    })
    .listen('RefreshUI', () => {
      router.reload({ preserveScroll: true, preserveState: true })
    })
}

const unsubscribeFromRealtimeEvents = (auth) => {
  window.Echo.leaveChannel(`private:users.${auth.id}`)
}

watch(() => props.message, (val) => {
  isMessageDialogOpen.value = val !== null
})

watch(() => props.auth, (auth, oldAuth) => {
  if (auth?.id === oldAuth?.id) {
    return
  }

  if (oldAuth) {
    unsubscribeFromRealtimeEvents(oldAuth)
  }

  if (auth) {
    subscribeToRealtimeEvents(auth)
  }
})

onMounted(() => {
  if (props.auth) {
    subscribeToRealtimeEvents(props.auth)
  }
})

onBeforeUnmount(() => {
  if (props.auth) {
    unsubscribeFromRealtimeEvents(props.auth)
  }
})
</script>