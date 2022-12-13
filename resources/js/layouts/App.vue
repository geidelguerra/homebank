<template>
  <div class="flex flex-col min-h-screen">
    <div
      v-if="auth"
      class="flex justify-end items-center p-2 space-x-2"
    >
      <div>Welcome back <span class="font-medium">{{ auth.name }}</span></div>
      <Button @click="logout">
        Log out
      </Button>
    </div>
    <div class="flex flex-1">
      <div class="w-80 p-4">
        <nav>
          <ul class="flex flex-col space-y-2">
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
      <main class="flex-1">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import Button from '@/components/Button.vue'
import { Inertia } from '@inertiajs/inertia'
import { InertiaLink } from '@inertiajs/inertia-vue3'

const props = defineProps({
  auth: { type: Object, default: null }
})

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

</script>