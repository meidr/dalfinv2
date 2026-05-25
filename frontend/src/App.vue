<template>
  <div
    :class="isDark ? 'dark' : 'light'"
    class="min-h-screen flex flex-col font-display"
  >
    <router-view v-slot="{ Component, route }">
      <Transition name="page-fade" mode="out-in" appear>
        <component
          :is="Component"
          :key="route.matched[0]?.path || route.fullPath"
        />
      </Transition>
    </router-view>
  </div>
</template>

<script setup>
import { ref } from "vue";

const isDark = ref(false);
</script>
