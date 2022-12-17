<template>
  <div class="p-4">
    <form
      class="max-w-md flex flex-col space-y-4"
      @submit.prevent="submit"
    >
      <FormInput
        label="Date"
        :error="form.errors.date"
      >
        <Input
          :value="formatDate(form.date, 'yyyy-MM-dd')"
          type="date"
          @input="form.date = $event.target.value"
        />
      </FormInput>
      <FormInput
        label="Account"
        :error="form.errors.account_id"
      >
        <Select
          v-model="form.account_id"
          :items="availableAccounts"
        />
      </FormInput>
      <FormInput
        label="Amount"
        :error="form.errors.amount"
      >
        <Input
          v-model="form.amount"
          type="number"
          step="0.01"
        />
      </FormInput>
      <FormInput
        label="Category"
        :error="form.errors.category_id"
      >
        <Select
          v-model="form.category_id"
          :items="availableCategories"
        />
      </FormInput>
      <div class="flex justify-between">
        <Button
          type="submit"
          color="success"
        >
          Save
        </Button>
        <Button @click="$inertia.visit(route('transactions.index'))">
          Cancel
        </Button>
      </div>
      <div v-if="exists">
        <Button
          color="danger"
          class="w-full"
          @click="deleteTransaction"
        >
          Delete transaction
        </Button>
      </div>
    </form>
    <ConfirmDialog ref="confirmDialog" />
  </div>
</template>

<script setup>
import FormInput from '@/components/FormInput.vue'
import Input from '@/components/Input.vue'
import Select from '@/components/Select.vue'
import Button from '@/components/Button.vue'
import ConfirmDialog from '@/components/ConfirmDialog.vue'
import { useForm } from '@inertiajs/inertia-vue3'
import { computed, ref } from 'vue'
import { Inertia } from '@inertiajs/inertia'

const props = defineProps({
  type: { type: String, default: 'expense' },
  transaction: { type: Object, default: null },
  availableAccounts: { type: Array, default: () => [] },
  availableCategories: { type: Array, default: () => [] },
})

const confirmDialog = ref(null)

const exists = computed(() => props.transaction?.id !== undefined)

const form = useForm({
  date: (props.transaction ? new Date(props.transaction.date) : new Date()).toISOString().split('T')[0],
  amount: props.transaction ? props.transaction.amount / 100 : '',
  category_id: props.transaction?.category_id,
  account_id: props.transaction?.account_id,
})

const submit = () => {
  form.clearErrors().transform((data) => ({
    ...data,
    amount: data.amount * 100
  }))

  if (exists.value) {
    return form.put(route('transactions.update', [props.transaction]))
  }

  return form.post(route('transactions.store'))
}

const deleteTransaction = () => confirmDialog.value
  .show({
    title: 'Warning',
    message: 'Proceed to delete this transaction?',
    color: 'danger'
  })
  .then(() => Inertia.delete(route('transactions.destroy', [props.transaction])))
  .catch(() => {
    // Do nothing
  })
</script>