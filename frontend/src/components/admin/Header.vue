<template>
  <header class="flex items-center justify-between border-b border-border-light px-8 py-4 bg-surface-light dark:bg-sidebar-light z-10 shrink-0 sticky top-0">
    <div class="flex items-center gap-6">
      <button class="text-text-main lg:hidden p-2 rounded-lg hover:bg-sidebar-light transition-colors" @click="$emit('toggle-sidebar')">
        <span class="material-symbols-outlined">menu</span>
      </button>
      <div class="hidden md:flex flex-col min-w-80 h-10">
        <div class="flex w-full flex-1 items-stretch rounded-full h-full bg-sidebar-light border border-border-light group focus-within:border-primary focus-within:bg-white transition-all shadow-sm">
          <div class="text-text-secondary flex border-none items-center justify-center pl-4 pr-2">
            <span class="material-symbols-outlined text-[20px] group-focus-within:text-primary transition-colors">search</span>
          </div>
          <input class="flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-full text-text-main bg-transparent border-none focus:ring-0 placeholder:text-text-secondary text-sm font-normal leading-normal focus:outline-none" placeholder="Search by name, NIM, or title..." value=""/>
        </div>
      </div>
    </div>
    <div class="flex items-center gap-4">
      <button @click="toggleTheme" class="flex items-center justify-center size-10 rounded-full hover:bg-sidebar-light text-text-secondary transition-colors group">
        <span class="material-symbols-outlined transition-transform duration-500 rotate-0 dark:-rotate-180 group-hover:text-primary">
          {{ isDark ? 'light_mode' : 'dark_mode' }}
        </span>
      </button>
      <button class="flex items-center justify-center size-10 rounded-full hover:bg-sidebar-light text-text-secondary relative transition-colors group">
        <span class="material-symbols-outlined group-hover:scale-110 transition-transform">notifications</span>
        <span class="absolute top-2.5 right-2.5 size-2 bg-red-500 rounded-full border-2 border-white animate-pulse"></span>
      </button>
      <div class="h-6 w-px bg-border-light"></div>
      <button class="flex items-center gap-3 hover:bg-sidebar-light rounded-full p-1 pr-3 transition-colors border border-transparent hover:border-border-light group">
        <div class="bg-center bg-no-repeat bg-cover rounded-full size-8 border border-border-light transition-transform group-hover:scale-105" data-alt="Admin User Profile Picture" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuC6fj58EKDs12x21HTipDxffh3oyce58_8S36-1PZUEVSH0Apn9oH-MWwAXvFvCgfIwMBuY4TFQOBUpWvQvZCM2piBln7AVu9-OInn3YgI6I3HUUkanApn6BfexGFdWOiebeMvZYStp-ck_aZoHx2O-q5487Oop3186czgCW16wwA4Q7x-GmuXdolN1KCu6sXA7yEmVmPK-YYpc9A0S1I-DIt6LbQnenGu2khwnzRerxEQMeTs5KE-0nGmWzphphsHQujdpNoH7ZUP_");'></div>
        <div class="hidden md:flex flex-col items-start">
          <span class="text-xs font-bold text-text-main leading-tight group-hover:text-primary transition-colors">Admin Staff</span>
          <span class="text-[10px] text-text-secondary leading-tight">Administrator</span>
        </div>
      </button>
    </div>
  </header>
</template>

<script setup>
import { ref, onMounted } from 'vue'

defineEmits(['toggle-sidebar'])

const isDark = ref(false)

const toggleTheme = () => {
  isDark.value = !isDark.value
  if (isDark.value) {
    document.documentElement.classList.add('dark')
    localStorage.setItem('theme', 'dark')
  } else {
    document.documentElement.classList.remove('dark')
    localStorage.setItem('theme', 'light')
  }
}

onMounted(() => {
  const storedTheme = localStorage.getItem('theme')
  const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches
  
  if (storedTheme === 'dark' || (!storedTheme && systemDark)) {
    isDark.value = true
    document.documentElement.classList.add('dark')
  } else {
    isDark.value = false
    document.documentElement.classList.remove('dark')
  }
})
</script>
