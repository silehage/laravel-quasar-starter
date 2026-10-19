<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { useVModel } from '@vueuse/core'

const props = defineProps<{
  defaultValue?: string | number
  modelValue?: string | number
  class?: HTMLAttributes['class']
  label?: string
  type?: 'text'
  dense?: boolean
  filled?: boolean
  outlined?: boolean
  clearable?: boolean
  required?: boolean,
  max?: string
  min?: string
}>()

const emits = defineEmits<{
  (e: 'update:modelValue', payload: string | number): void
}>()

const modelValue = useVModel(props, 'modelValue', emits, {
  passive: true,
  defaultValue: props.defaultValue,
})

function optionsFn(d) {
  
  if(props.max && props.min) {
    return d >= props.min && d <= props.max

  }
  if (props.max) {
    return d <= props.max
  }

  return true

}
</script>

<template>
    {{ modelValue }}
  <q-input :required="required" :filled="filled" :outlined="outlined" :dense="dense" :type="type" :label="label"
    :clearable="clearable" v-model="modelValue" mask="date" debounce="200">
    <template v-slot:append>
      <q-icon name="event" class="cursor-pointer">
        <q-popup-proxy cover transition-show="scale" transition-hide="scale">
          <q-date v-model="modelValue" today-btn :options="optionsFn">
            <div class="row items-center justify-end">
              <q-btn v-close-popup label="Close" color="primary" flat />
            </div>
          </q-date>
        </q-popup-proxy>
      </q-icon>
    </template>
  </q-input>
</template>
