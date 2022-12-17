<template>
  <TransitionRoot
    appear
    :show="open"
    as="template"
  >
    <Dialog
      as="div"
      class="relative z-50"
      @close="close"
    >
      <TransitionChild
        as="template"
        enter="duration-300 ease-out"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="duration-200 ease-in"
        leave-from="opacity-100"
        leave-to="opacity-0"
      >
        <div class="fixed inset-0 bg-black bg-opacity-25" />
      </TransitionChild>

      <div class="fixed inset-0 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-2 text-center">
          <TransitionChild
            as="template"
            enter="duration-300 ease-out"
            enter-from="opacity-0 scale-95"
            enter-to="opacity-100 scale-100"
            leave="duration-200 ease-in"
            leave-from="opacity-100 scale-100"
            leave-to="opacity-0 scale-95"
          >
            <DialogPanel
              :class="{
                'absolute w-screen h-screen': size === 'full',
                'w-full max-w-screen-lg rounded': size === 'lg',
                'w-full max-w-screen-md rounded': size === 'md',
                'w-full max-w-screen-sm rounded': size === 'sm',
                'w-full max-w-md rounded': size === 'xs',
              }"
              class="transform bg-white p-4 text-left align-middle shadow-xl transition-all flex flex-col"
            >
              <DialogTitle
                v-if="title"
                as="h3"
                class="text-lg font-medium leading-6 text-gray-900 mb-2"
              >
                {{ title }}
              </DialogTitle>
              <div class="text-sm p-4 flex-1">
                <slot />
              </div>
              <div class="flex space-x-2 justify-end">
                <slot name="footer">
                  <Button @click="close">
                    Close
                  </Button>
                </slot>
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script setup>
import {
  TransitionRoot,
  TransitionChild,
  Dialog,
  DialogPanel,
  DialogTitle,
} from '@headlessui/vue'
import Button from './Button.vue';

const props = defineProps({
  open: Boolean,
  title: { type: String, default: null },
  size: { type: String, default: 'md' }
})

const emit = defineEmits(['close'])

const close = () => emit('close')
</script>
