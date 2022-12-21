<template>
  <div class="p-4">
    <div class="mb-4 max-w-md">
      <ToggleButtonGroup
        v-model="form.type"
        :items="availableTypes"
      />
    </div>

    <form
      class="max-w-md flex flex-col space-y-4"
      @submit.prevent="submit"
    >
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

      <FormElement
        label="Account"
        :error="form.errors.account_id"
      >
        <Select
          v-model="form.account_id"
          :items="availableAccounts"
        />
      </FormElement>

      <FormElement
        label="Amount"
        :error="form.errors.amount"
      >
        <Input
          v-model="form.amount"
          type="number"
          min="0.01"
          step="0.01"
        />
      </FormElement>

      <FormElement
        label="Category"
        :error="form.errors.category_id"
      >
        <Select
          v-model="form.category_id"
          :items="availableCategories"
        />
      </FormElement>

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
import FormElement from '@/components/FormElement.vue'
import Input from '@/components/Input.vue'
import Select from '@/components/Select.vue'
import Button from '@/components/Button.vue'
import ConfirmDialog from '@/components/ConfirmDialog.vue'
import { useForm } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import ToggleButtonGroup from '@/components/ToggleButtonGroup.vue'
import { money, parseMoney } from '@/utils'
import { USD } from '@dinero.js/currencies'

const props = defineProps({
  transaction: { type: Object, default: null },
  availableTypes: { type: Array, default: () => [] },
  availableAccounts: { type: Array, default: () => [] },
  availableCategories: { type: Array, default: () => [] },
})

const confirmDialog = ref(null)

const exists = computed(() => props.transaction?.id !== undefined)

const selectedAccount = computed(() => props.availableAccounts.find((account) => account.id === form.account_id))

const form = useForm({
  date: (props.transaction ? new Date(props.transaction.date) : new Date()).toISOString().split('T')[0],
  amount: money((props.transaction?.amount || 0) * (props.transaction?.type === 'Income' ? 1 : -1), props.transactions?.currency || USD).toDecimal(),
  type: props.transaction?.type || 'Expense',
  category_id: props.transaction?.category_id,
  account_id: props.transaction?.account_id,
})

const submit = () => {
  form.clearErrors().transform((data) => ({
    ...data,
    amount: parseMoney(data.amount * (data.type === 'Income' ? 1 : -1), selectedAccount.value?.currency || USD).toSnapshot().amount
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
  .then(() => router.delete(route('transactions.destroy', [props.transaction])))
  .catch(() => {
    // Do nothing
  })
</script>