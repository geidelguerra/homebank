<template>
  <div class="p-4">
    <form
      class="max-w-md flex flex-col space-y-4"
      @submit.prevent="submit"
    >
      <FormElement
        label="Code (ISO 4217)"
        :error="form.errors.code"
      >
        <Input
          v-model="form.code"
          placeholder="3 letters code"
          autofocus
        />
      </FormElement>
      <FormElement
        label="Base"
        :error="form.errors.base"
      >
        <Input
          v-model="form.base"
          placeholder="10"
        />
      </FormElement>
      <FormElement
        label="Exponent"
        :error="form.errors.exponent"
      >
        <Input
          v-model="form.exponent"
          placeholder="2"
        />
      </FormElement>
      <div class="flex justify-between">
        <Button
          type="submit"
          color="success"
        >
          Save
        </Button>
        <Button @click="$inertia.visit(route('currencies.index'))">
          Cancel
        </Button>
      </div>
      <div v-if="exists">
        <Button
          color="danger"
          class="w-full"
          @click="deleteCurrency"
        >
          Delete currency
        </Button>
      </div>
    </form>
    <ConfirmDialog ref="confirmDialog" />
  </div>
</template>

<script setup>
import FormElement from '@/components/FormElement.vue'
import Input from '@/components/Input.vue'
import Button from '@/components/Button.vue'
import { useForm } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import ConfirmDialog from '@/components/ConfirmDialog.vue'

const props = defineProps({
  currency: { type: Object, default: null },
})

const confirmDialog = ref(null)

const exists = computed(() => props.currency?.code !== undefined)

const form = useForm({
  code: props.currency?.code || '',
  base: props.currency?.base || 10,
  exponent: props.currency?.exponent || 2
})

const submit = () => {
  form.clearErrors().transform((data) => ({
    ...data,
    base: String(data.base).trim().split(',')
  }))

  if (exists.value) {
    return form.put(route('currencies.update', [props.currency.code]))
  }

  return form.post(route('currencies.store'))
}

const deleteCurrency = () => confirmDialog.value
  .show({
    title: 'Warning',
    message: 'Proceed to delete this currency?',
    color: 'danger'
  })
  .then(() => router.delete(route('currencies.destroy', [props.currency.code])))
</script>