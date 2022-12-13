<template>
  <div class="h-screen flex items-center justify-center">
    <Card class="max-w-sm w-full">
      <h1 class="text-xl mb-4">
        Login
      </h1>
      <form
        class="flex flex-col space-y-4"
        @submit.prevent="submit"
      >
        <FormInput
          label="Email"
          :error="form.errors.email"
        >
          <Input
            v-model="form.email"
            type="text"
          />
        </FormInput>
        <FormInput
          label="Password"
          :error="form.errors.password"
        >
          <Input
            v-model="form.password"
            type="password"
          />
        </FormInput>
        <div>
          <Switch
            v-model="form.remember"
            label="Remember"
          />
        </div>
        <div class="flex justify-end">
          <Button
            type="submit"
            :disabled="form.processing"
          >
            Login
          </Button>
        </div>
      </form>
    </Card>
  </div>
</template>

<script setup>
import Card from '@/components/Card.vue'
import FormInput from '@/components/FormInput.vue'
import Input from '@/components/Input.vue'
import Button from '@/components/Button.vue'
import Switch from '@/components/Switch.vue'
import { useForm } from '@inertiajs/inertia-vue3'

const form = useForm({
  email: '',
  password: '',
  remember: true
})

const submit = () => form.clearErrors().post(route('login'))
</script>