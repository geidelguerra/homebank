<template>
  <div>
    <form
      class="max-w-md flex flex-col space-y-4"
      @submit.prevent="submit"
    >
      <FormInput
        label="Name"
        :error="form.errors.name"
      >
        <Input
          v-model="form.name"
        />
      </FormInput>
      <FormInput
        label="Currency"
        :error="form.errors.currency"
      >
        <Select
          v-model="form.currency"
          :items="availableCurrencies"
        />
      </FormInput>
      <div>
        <Button type="submit">
          Save
        </Button>
      </div>
    </form>
  </div>
</template>

<script setup>
import FormInput from '@/components/FormInput.vue'
import Input from '@/components/Input.vue'
import Select from '@/components/Select.vue'
import Button from '@/components/Button.vue'
import { useForm } from '@inertiajs/inertia-vue3'

const props = defineProps({
  account: { type: Object, default: null },
  availableCurrencies: { type: Array, default: () => [] }
})

const form = useForm({
  name: props.account?.name || '',
  currency: props.account?.currency || ''
})

const submit = () => {
  form.clearErrors()

  if (props.account?.id) {
    return form.put(route('accounts.update', [props.account]))
  }

  return form.post(route('transactions.store'))
}
</script>