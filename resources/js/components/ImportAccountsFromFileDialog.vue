<template>
  <Dialog
    v-bind="$attrs"
    :size="parsedData && parsedData.length > 0 ? 'lg': 'xs'"
    title="Import accounts from file"
    @close="close"
  >
    <div
      v-if="parsedData && parsedData.length > 0"
      class="mb-4"
    >
      <h3 class="text-sm font-medium mb-1 text-slate-600">
        Data preview ({{ parsedData.length }} rows)
      </h3>
      <table class="border-separate w-full">
        <tbody class="rounded">
          <template
            v-for="row in parsedData"
            :key="row"
          >
            <tr>
              <template
                v-for="cell in row"
                :key="cell"
              >
                <td
                  class="p-2 text-sm font-medium border-x bg-slate-200 first:rounded-l last:rounded-r"
                >
                  {{ cell }}
                </td>
              </template>
            </tr>
          </template>
        </tbody>
      </table>
    </div>

    <form
      v-if="parsedData && parsedData.length > 0"
      @submit.prevent="submit"
    >
      <div class="grid row-auto grid-cols-3 gap-4">
        <FormElement label="Ignored rows">
          <Input
            v-model="form.ignored_rows"
            :disabled="form.processing"
            type="number"
            step="1"
            min="0"
          />
        </FormElement>
      </div>
      <div class="grid row-auto grid-cols-3 gap-4">
        <FormElement label="Date column">
          <Select
            v-model="form.date_column"
            :items="availableColumns"
            :disabled="form.processing"
            item-key="index"
            item-value="value"
          />
        </FormElement>
        <FormElement label="Date timezone">
          <Select
            v-model="form.date_timezone"
            :items="availableTimezones"
            :disabled="form.processing"
            item-key="index"
            item-value="value"
          />
        </FormElement>
        <FormElement label="Category column">
          <Select
            v-model="form.category_column"
            :items="availableColumns"
            :disabled="form.processing"
            item-key="index"
            item-value="value"
          />
        </FormElement>
        <FormElement label="Description column">
          <Select
            v-model="form.description_column"
            :items="availableColumns"
            :disabled="form.processing"
            item-key="index"
            item-value="value"
          />
        </FormElement>
        <FormElement label="Amount column">
          <Select
            v-model="form.amount_column"
            :items="availableColumns"
            :disabled="form.processing"
            item-key="index"
            item-value="value"
          />
        </FormElement>
        <FormElement label="Account column">
          <Select
            v-model="form.account_column"
            :items="availableColumns"
            :disabled="form.processing"
            item-key="index"
            item-value="value"
          />
        </FormElement>
      </div>
    </form>
    <template #footer>
      <Button
        v-if="parsedData && parsedData.length > 0"
        :disabled="form.processing"
        color="success"
        @click="submit"
      >
        Upload data
      </Button>
      <Button
        color="primary"
        :disabled="form.processing"
        @click="fileInput.click()"
      >
        Select CSV file
        <input
          ref="fileInput"
          accept="text/csv"
          type="file"
          class="hidden"
          @change="form.file = $event.target.files[0]"
        >
      </Button>
      <Button
        :disabled="form.processing"
        @click="close"
      >
        Close
      </Button>
    </template>
  </Dialog>
</template>

<script setup>
import { useForm } from '@inertiajs/inertia-vue3'
import { ref, watch, computed } from 'vue'
import Button from '@/components/Button.vue'
import Dialog from '@/components/Dialog.vue'
import FormElement from '@/components/FormElement.vue'
import Select from '@/components/Select.vue'
import Input from '@/components/Input.vue'
import CSVParser from 'papaparse'

const props = defineProps({
  availableTimezones: { type: Array, default: () => [] }
})
const fileInput = ref(null)

const parsedData = ref([])

const availableColumns = computed(() => parsedData.value.length > 0 ? parsedData.value[0].map((value, index) => ({ index, value })) : [])

const form = useForm({
  file: undefined,
  ignored_rows: 1,
  date_column: undefined,
  date_timezone: 'UTC',
  category_column: undefined,
  description_column: undefined,
  amount_column: undefined,
  account_column: undefined,
})

const parseFile = (file) => CSVParser.parse(file, {
  preview: 3,
  complete: function (results) {
    parsedData.value = results.data
  }
})

const emit = defineEmits(['close'])

const close = () => {
  if (form.processing) {
    return
  }

  emit('close')
}

const submit = () => form.post(route('accounts.importFromFile'), {
  onSuccess: function () { emit('close') }
})

watch(() => form.file, (file) => {
  if (file instanceof File) {
    parseFile(file)
  }
})

watch(availableColumns, (columns) => {
  form.date_column = columns[0].index
  form.category_column = columns[1].index
  form.description_column = columns[2].index
  form.amount_column = columns[3].index
  form.account_column = columns[4].index
})
</script>