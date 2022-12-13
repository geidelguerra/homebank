<template>
  <div class="min-h-screen flex flex-col text-slate-900">
    <div
      v-if="auth"
      class="flex justify-end items-center p-2 space-x-2"
    >
      <div>Welcome back <span class="font-medium">{{ auth.name }}</span></div>
      <Button @click="logout">
        Log out
      </Button>
    </div>
    <slot />
  </div>
</template>

<script setup>
import Button from '@/components/Button.vue'
import { Inertia } from '@inertiajs/inertia'

const props = defineProps({
  auth: { type: Object, default: null }
})

const logout = () => Inertia.post(route('logout'))

</script>