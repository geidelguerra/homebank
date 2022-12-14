<template>
  <div class="flex flex-col min-h-screen">
    <div class="flex items-center bg-indigo-600 px-4 py-2 fixed top-0 w-full z-40">
      <Breadcrumbs :items="breadcrumbs" />
      <div class="flex-1" />
      <div
        v-if="auth"
        class="flex justify-end items-center space-x-2"
      >
        <div class="text-white text-sm">
          Welcome back <span class="font-medium">{{ auth.name }}</span>
        </div>
        <Button @click="logout">
          Log out
        </Button>
      </div>
    </div>
    <div class="flex flex-1 pt-[52px]">
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
                <InertiaLink
                  :href="item.url"
                  class="flex p-2 rounded transition-all text-sm text-slate-500 hover:text-slate-800 hover:bg-slate-300"
                  :class="{
                    'bg-slate-300 text-slate-800': item.active()
                  }"
                >
                  {{ item.text }}
                </InertiaLink>
              </li>
            </template>
          </ul>
        </nav>
      </div>
      <main class="flex-1 p-4 flex flex-col pl-80">
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
import { Inertia } from '@inertiajs/inertia'
import { InertiaLink } from '@inertiajs/inertia-vue3'
import { ref, watch } from 'vue';

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
  }
]

const logout = () => Inertia.post(route('logout'))

watch(() => props.message, (val) => {
  isMessageDialogOpen.value = val !== null
})
</script>