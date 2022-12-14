<template>
  <Dialog
    :open="isOpen"
    :title="title"
    @close="close"
  >
    <div v-html="message" />
    <template #footer>
      <Button
        :color="color"
        class="w-24"
        @click="ok"
      >
        Yes
      </Button>
      <Button
        class="w-24"
        @click="close"
      >
        No
      </Button>
    </template>
  </Dialog>
</template>

<script setup>
import { ref } from 'vue';
import Button from './Button.vue'
import Dialog from './Dialog.vue'

const isOpen = ref(false)
const title = ref('')
const message = ref('')
const color = ref(null)

let resolveCb = null

const ok = () => {
  resolveCb()

  isOpen.value = false
}

const close = () => {
  isOpen.value = false
}

const show = (options = {}) => new Promise((resolve) => {
  title.value = options.title || 'Confirm'
  message.value = options.message
  color.value = options.color || 'primary'
  resolveCb = resolve
  isOpen.value = true
})

defineExpose({ show })
</script>