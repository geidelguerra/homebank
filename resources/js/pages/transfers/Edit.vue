<template>
  <div class="p-4">
    <form
      class="max-w-lg"
      @submit.prevent="submit"
    >
      <div class="flex flex-col space-y-4">
        <div class="grid grid-cols-2 gap-4">
          <FormElement
            label="Date"
            :error="form.errors.date"
          >
            <Input
              :value="$date(form.date).format('yyyy-MM-dd')"
              type="date"
              @input="form.date = $event.target.value"
            />
          </FormElement>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <FormElement
            label="Source account"
            :error="form.errors.source_account_id"
          >
            <Select
              v-model="form.source_account_id"
              :items="availableAccounts"
            />
          </FormElement>

          <FormElement
            label="Destination account"
            :error="form.errors.destination_account_id"
          >
            <Select
              v-model="form.destination_account_id"
              :disabled="!selectedSourceAccount"
              :items="availableDestinationAccounts"
            />
          </FormElement>
        </div>

        <div class="flex justify-center">
          <Button
            color="primary"
            icon="fa-solid fa-right-left"
            :disabled="!selectedSourceAccount || !selectedDestinationAccount"
            @click="swapAccounts"
          >
            Swap accounts
          </Button>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <FormElement
            label="Amount"
            :error="form.errors.amount"
          >
            <Input
              v-model="form.amount"
              :disabled="!selectedSourceAccount"
              type="number"
              min="0"
              step="1"
            />
          </FormElement>

          <FormElement
            label="Exchange rate"
            :error="form.errors.exchange_rate"
          >
            <Input
              v-model="form.exchange_rate"
              :disabled="!selectedSourceAccount"
              type="number"
              min="0.01"
              step="0.01"
            />
          </FormElement>
        </div>
        <div class="flex justify-end">
          <Button
            color="success"
            type="submit"
            :disabled="form.processing"
            :loading="form.processing"
          >
            Save
          </Button>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup>
import Button from '@/components/Button.vue'
import FormElement from '@/components/FormElement.vue'
import Input from '@/components/Input.vue'
import Select from '@/components/Select.vue'
import { useForm } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
  transfer: { type: Object, default: null },
  availableCategories: { type: Array, default: () => [] },
  availableAccounts: { type: Array, default: () => [] }
})

const exists = computed(() => props.transfer?.id !== undefined)

const form = useForm({
  date: new Date(),
  source_account_id: undefined,
  destination_account_id: undefined,
  amount: props.transfer?.amount,
  exchange_rate: props.transfer?.exchange_rate || 1
})

const selectedSourceAccount = computed(() => props.availableAccounts.find((other) => other.id === form.source_account_id))

const selectedDestinationAccount = computed(() => props.availableAccounts.find((other) => other.id === form.destination_account_id))

const availableDestinationAccounts = computed(() => props.availableAccounts.filter((other) => other.id !== selectedSourceAccount.value?.id ))

const swapAccounts = () => {
  const tempSourceAccountId = form.source_account_id

  form.source_account_id = form.destination_account_id
  form.destination_account_id = tempSourceAccountId
}

const submit = () => {
  form.clearErrors()

  if (exists.value) {
    return form.put(route('transfers.update', props.transfer.id))
  }

  return form.post(route('transfers.store'))
}
</script>