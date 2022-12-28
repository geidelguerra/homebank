<template>
  <FontAwesomeIcon
    v-bind="$attrs"
    :icon="icon"
    :spin="spin"
  />
</template>

<script setup>
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

import {
  faCaretDown,
  faGear,
  faRotateRight,
  faCircleNotch,
  faCaretRight,
  faRightLeft
} from '@fortawesome/free-solid-svg-icons'

library.add(
  faCaretDown,
  faGear,
  faRotateRight,
  faCircleNotch,
  faCaretRight,
  faRightLeft
)

defineProps({
  icon: { type: String, required: true },
  spin: Boolean
})
</script>