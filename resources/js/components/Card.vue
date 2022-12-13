<template>
  <div
    v-bind="$attrs"
    class="relative bg-white p-6 shadow-md shadow-violet-900/5 rounded-xl overflow-hidden"
  >
    <main>
      <slot />
    </main>
  </div>
</template>