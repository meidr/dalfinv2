<template>
  <SimpleBar
    class="data-table-scroll"
    :class="{ 'has-y-scroll': !!maxHeight }"
    :style="scrollStyle"
    data-simplebar-auto-hide="false"
  >
    <slot />
  </SimpleBar>
</template>

<script setup>
import { computed } from "vue";
import SimpleBar from "simplebar-vue";

const props = defineProps({
  maxHeight: {
    type: String,
    default: "",
  },
});

const scrollStyle = computed(() =>
  props.maxHeight ? { maxHeight: props.maxHeight } : undefined,
);
</script>
