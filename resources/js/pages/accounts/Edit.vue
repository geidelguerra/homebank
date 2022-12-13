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
        :error="form.errors.currency_code"
      >
        <Select
          v-model="form.currency_code"
          :items="availableCurrencies"
        />
      </FormInput>
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
  currency_code: props.account?.currency_code || ''
})

const submit = () => {
  form.clearErrors()

  if (props.account?.id) {
    return form.put(route('accounts.update', [props.account]))
  }

  return form.post(route('accounts.store'))
}
</script>