<script setup lang="ts">
import { computed } from 'vue';
import { cleaningString, numberFormat } from '@/lib/utils';
import type { HTMLAttributes } from 'vue'

const props = defineProps<{
  defaultValue?: string | number
  modelValue?: string | number
  class?: HTMLAttributes['class']
  label?: string
  stackLabel?: boolean,
  type?: 'text'
  dense?: boolean
  filled?: boolean
  outlined?: boolean
  clearable?: boolean
  required?: boolean
  min?: string
  max?: string
  suffix?:string
  prefix?: string
}>()

const emits = defineEmits<{
  (e: 'update:modelValue', payload: string | number): void
}>()

const modelValue = computed({
   get() {
      return numberFormat(props.modelValue ?? 0)
   },
   set(val) {
      let amount = val ? cleaningString(val) : '0'
      emits('update:modelValue', Number(amount))
   }
})

function optionsFn(d) {

  if (props.max && props.min) {
    return d >= props.min && d <= props.max

  }
  if (props.max) {
    return d <= props.max
  }

  return true

}
</script>

<template>
  <q-input :required="required" :filled="filled" :outlined="outlined" :dense="dense" :type="type" :label="label" :prefix="prefix" :suffix="suffix"
    :clearable="clearable" v-model="modelValue">
  </q-input>
</template>
