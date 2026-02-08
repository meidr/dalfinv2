<template>
  <div :class="isDark ? 'dark' : 'light'" class="min-h-screen flex flex-col font-display">
    <router-view />
  </div>
</template>

<script setup>
import { ref } from 'vue'

const isDark = ref(false)
</script>
