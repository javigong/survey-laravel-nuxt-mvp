<template>
  <button
    :class="cn(buttonVariants({ variant, size }), $attrs.class)"
    v-bind="$attrs"
    @click="$emit('click', $event)"
  >
    <slot />
  </button>
</template>

<script setup lang="ts">
import { cn } from '../../../lib/utils'
import { buttonVariants } from '../../../lib/variants'

interface Props {
  variant?: 'default' | 'destructive' | 'outline' | 'secondary' | 'ghost' | 'link'
  size?: 'default' | 'sm' | 'lg' | 'icon'
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'default',
  size: 'default',
})

defineEmits<{
  click: [event: Event]
}>()
</script>
