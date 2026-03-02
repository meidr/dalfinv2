<template>
  <div
    class="min-h-screen flex flex-col bg-background-light dark:bg-background-dark font-display text-text-main"
  >
    <!-- Navigation Bar -->
    <header
      class="sticky top-0 z-50 bg-surface-light dark:bg-sidebar-light border-b border-border-light shadow-sm transition-colors duration-300"
    >
      <div
        class="max-w-7xl mx-auto px-4 md:px-6 h-16 flex items-center justify-between"
      >
        <!-- Logo Section -->
        <div class="flex items-center gap-3">
          <div
            class="bg-primary/10 p-2 rounded-lg text-primary flex items-center justify-center"
          >
            <span class="material-symbols-outlined text-2xl">school</span>
          </div>
          <h1 class="text-xl font-bold tracking-tight text-text-main">
            {{ authStore.profile?.nama || "SISKRIPSI" }}
          </h1>
        </div>

        <!-- Desktop Menu -->
        <nav class="hidden md:flex items-center gap-8">
          <router-link
            to="/mahasiswa/dashboard"
            active-class="text-primary after:scale-x-100"
            class="relative group flex items-center gap-2 text-sm font-bold text-text-secondary hover:text-primary transition-colors py-5 after:absolute after:bottom-0 after:left-0 after:w-full after:h-0.5 after:bg-primary after:rounded-t-full after:scale-x-0 after:transition-transform after:duration-300"
          >
            <span class="material-symbols-outlined text-[20px]">dashboard</span>
            <span>Dashboard</span>
          </router-link>

          <router-link
            to="/mahasiswa/skripsi"
            active-class="text-primary after:scale-x-100"
            class="relative group flex items-center gap-2 text-sm font-bold text-text-secondary hover:text-primary transition-colors py-5 after:absolute after:bottom-0 after:left-0 after:w-full after:h-0.5 after:bg-primary after:rounded-t-full after:scale-x-0 after:transition-transform after:duration-300"
          >
            <span class="material-symbols-outlined text-[20px]">book</span>
            <span>Skripsi Saya</span>
          </router-link>
          <router-link
            to="/mahasiswa/informasi"
            active-class="text-primary after:scale-x-100"
            class="relative group flex items-center gap-2 text-sm font-bold text-text-secondary hover:text-primary transition-colors py-5 after:absolute after:bottom-0 after:left-0 after:w-full after:h-0.5 after:bg-primary after:rounded-t-full after:scale-x-0 after:transition-transform after:duration-300"
          >
            <span class="material-symbols-outlined text-[20px]">info</span>
            <span>Informasi</span>
          </router-link>
          <router-link
            to="/mahasiswa/profil"
            active-class="text-primary after:scale-x-100"
            class="relative group flex items-center gap-2 text-sm font-bold text-text-secondary hover:text-primary transition-colors py-5 after:absolute after:bottom-0 after:left-0 after:w-full after:h-0.5 after:bg-primary after:rounded-t-full after:scale-x-0 after:transition-transform after:duration-300"
          >
            <span class="material-symbols-outlined text-[20px]">person</span>
            <span>Profil</span>
          </router-link>
        </nav>

        <!-- Actions -->
        <div class="flex items-center gap-4">
          <!-- Dark Mode Toggle -->
          <button
            @click="toggleTheme"
            class="flex items-center justify-center size-10 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 text-text-secondary transition-colors group"
          >
            <span
              class="material-symbols-outlined transition-transform duration-500 rotate-0 dark:-rotate-180 group-hover:text-primary"
            >
              {{ isDark ? "light_mode" : "dark_mode" }}
            </span>
          </button>

          <button
            @click="logout"
            class="hidden sm:flex items-center justify-center gap-2 h-9 px-4 rounded-full border border-border-light bg-transparent hover:bg-sidebar-light text-sm font-medium transition-colors text-text-secondary hover:text-primary"
          >
            <span class="material-symbols-outlined text-[18px]">logout</span>
            <span>Logout</span>
          </button>

          <!-- Mobile Menu Icon -->
          <button
            class="md:hidden p-2 text-text-secondary hover:text-primary transition-colors"
          >
            <span class="material-symbols-outlined">menu</span>
          </button>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main
      class="flex-1 w-full max-w-7xl mx-auto px-4 md:px-6 py-8 flex flex-col gap-6 animate-fade-in"
    >
      <router-view></router-view>
    </main>
    <ChatWidget ref="chatWidgetRef" />
  </div>
</template>

<script setup>
import { ref, onMounted, provide } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "../stores/auth";
import ChatWidget from "../components/ChatWidget.vue";

const router = useRouter();
const authStore = useAuthStore();
const isDark = ref(false);
const chatWidgetRef = ref(null);

provide("openChatWithAdmin", () => {
  chatWidgetRef.value?.openChatWithAdmin();
});

const toggleTheme = () => {
  if (isDark.value) {
    document.documentElement.classList.remove("dark");
    localStorage.setItem("theme", "light");
    isDark.value = false;
  } else {
    document.documentElement.classList.add("dark");
    localStorage.setItem("theme", "dark");
    isDark.value = true;
  }
};

const logout = async () => {
  try {
    await authStore.logout();
    router.replace("/login");
  } catch (error) {
    console.error("Logout failed:", error);
    router.replace("/login");
  }
};

onMounted(() => {
  const storedTheme = localStorage.getItem("theme");
  const systemDark = window.matchMedia("(prefers-color-scheme: dark)").matches;

  if (storedTheme === "dark" || (!storedTheme && systemDark)) {
    isDark.value = true;
    document.documentElement.classList.add("dark");
  } else {
    isDark.value = false;
    document.documentElement.classList.remove("dark");
  }
});
</script>
