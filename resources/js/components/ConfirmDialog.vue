<template>
  <Dialog
    :open="isOpen"
    :title="title"
    @close="close"
  >
    <div
      class="text-sm p-4"
      v-html="message"
    />
    <div class="flex space-x-2 justify-end">
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
    </div>
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
let rejectCb = null

const ok = () => {
  resolveCb()

  isOpen.value = false
}

const close = () => {
  rejectCb()

  isOpen.value = false
}

const show = (options = {}) => new Promise((resolve, reject) => {
  title.value = options.title || 'Confirm'
  message.value = options.message
  color.value = options.color || 'primary'
  resolveCb = resolve
  rejectCb = reject
  isOpen.value = true
})

defineExpose({ show })
</script>