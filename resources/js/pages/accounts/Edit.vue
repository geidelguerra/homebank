<template>
  <div class="p-4">
    <form
      class="max-w-md flex flex-col space-y-4"
      @submit.prevent="submit"
    >
      <FormElement
        label="Name"
        :error="form.errors.name"
      >
        <Input
          v-model="form.name"
          autofocus
        />
      </FormElement>
      <FormElement
        label="Currency"
        :error="form.errors.currency_code"
      >
        <Select
          v-model="form.currency_code"
          :items="availableCurrencies"
        />
      </FormElement>
      <div class="flex justify-between">
        <Button
          type="submit"
          color="success"
        >
          Save
        </Button>
        <Button @click="$inertia.visit(route('accounts.index'))">
          Cancel
        </Button>
      </div>
      <div v-if="exists">
        <Button
          color="danger"
          class="w-full"
          @click="deleteAccount"
        >
          Delete account
        </Button>
      </div>
    </form>
    <ConfirmDialog ref="confirmDialog" />
  </div>
</template>

<script setup>
import FormElement from '@/components/FormElement.vue'
import Input from '@/components/Input.vue'
import Select from '@/components/Select.vue'
import Button from '@/components/Button.vue'
import { useForm } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import ConfirmDialog from '@/components/ConfirmDialog.vue'

const props = defineProps({
  account: { type: Object, default: null },
  availableCurrencies: { type: Array, default: () => [] }
})

const confirmDialog = ref(null)

const exists = computed(() => props.account?.id !== undefined)

const form = useForm({
  name: props.account?.name || '',
  currency_code: props.account?.currency_code || ''
})

const submit = () => {
  form.clearErrors()

  if (exists.value) {
    return form.put(route('accounts.update', [props.account]))
  }

  return form.post(route('accounts.store'))
}

const deleteAccount = () => confirmDialog.value
  .show({
    title: 'Warning',
    message: 'Deleting this account will delete any associated transactions. Proceed?',
    color: 'danger'
  })
  .then(() => router.delete(route('accounts.destroy', [props.account])))
</script>