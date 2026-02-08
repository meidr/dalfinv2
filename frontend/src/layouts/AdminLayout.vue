<template>
  <div class="flex h-screen w-full flex-row overflow-hidden bg-background-light text-text-main font-display">
    <Sidebar :is-sidebar-open="isSidebarOpen" />
    
    <!-- Overlay for mobile sidebar -->
    <div v-if="isSidebarOpen" class="fixed inset-0 bg-black/50 z-10 lg:hidden" @click="toggleSidebar"></div>
    
    <main class="flex flex-1 flex-col h-full bg-background-light overflow-hidden w-full">
      <Header @toggle-sidebar="toggleSidebar" />
      <div class="flex-1 overflow-y-auto p-4 md:p-8 scroll-smooth">
        <router-view />
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import Sidebar from '../components/admin/Sidebar.vue'
import Header from '../components/admin/Header.vue'

const isSidebarOpen = ref(false)

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value
}
</script>
