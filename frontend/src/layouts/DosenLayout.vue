<template>
  <div
    class="min-h-screen flex flex-col bg-background-light dark:bg-background-dark font-display text-text-main"
  >
    <!-- Navigation Bar -->
    <header
      class="sticky top-0 z-50 bg-surface-light dark:bg-sidebar-light border-b border-border-light shadow-sm transition-colors duration-300"
    >
      <div
        class="max-w-7xl mx-auto px-4 md:px-8 h-20 flex items-center justify-between"
      >
        <!-- Logo Section -->
        <div class="flex items-center gap-3">
          <img
            src="/DALFIN-LOGO.png"
            alt="DALFIN Logo"
            class="object-contain"
            style="width: 120px; height: 120px"
            :style="{
              width: windowWidth > 768 ? '160px' : '120px',
              height: windowWidth > 768 ? '160px' : '120px',
            }"
          />
          <div class="flex flex-col items-start">
            <h1
              class="text-sm md:text-lg font-black tracking-[0.2em] uppercase leading-none"
              style="
                background: linear-gradient(
                  135deg,
                  #0d2b3e 0%,
                  #1a4a5e 50%,
                  #00bcd4 100%
                );
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
              "
            >
              DOSEN
            </h1>
            <div class="hidden sm:flex items-center gap-2 mt-1.5">
              <span
                class="block w-6 h-0.5 rounded-full"
                style="background: linear-gradient(to right, #0d2b3e, #00bcd4)"
              ></span>
              <p
                class="text-[9px] font-semibold tracking-[0.3em] uppercase"
                style="color: #1a4a5e"
              >
                Panel Dosen
              </p>
            </div>
          </div>
        </div>

        <!-- Desktop Menu -->
        <nav class="hidden md:flex items-center gap-5 ml-8">
          <router-link
            to="/dosen/dashboard"
            active-class="text-primary after:scale-x-100"
            class="relative group flex items-center gap-1.5 text-xs font-semibold text-text-secondary hover:text-primary transition-colors py-5 after:absolute after:bottom-0 after:left-0 after:w-full after:h-0.5 after:bg-primary after:rounded-t-full after:scale-x-0 after:transition-transform after:duration-300"
          >
            <span class="material-symbols-outlined text-[17px]">dashboard</span>
            <span>Dashboard</span>
          </router-link>

          <router-link
            to="/dosen/bimbingan"
            active-class="text-primary after:scale-x-100"
            class="relative group flex items-center gap-1.5 text-xs font-semibold text-text-secondary hover:text-primary transition-colors py-5 after:absolute after:bottom-0 after:left-0 after:w-full after:h-0.5 after:bg-primary after:rounded-t-full after:scale-x-0 after:transition-transform after:duration-300"
          >
            <span class="material-symbols-outlined text-[17px]">groups</span>
            <span>Bimbingan</span>
          </router-link>

          <router-link
            to="/dosen/jadwal"
            active-class="text-primary after:scale-x-100"
            class="relative group flex items-center gap-1.5 text-xs font-semibold text-text-secondary hover:text-primary transition-colors py-5 after:absolute after:bottom-0 after:left-0 after:w-full after:h-0.5 after:bg-primary after:rounded-t-full after:scale-x-0 after:transition-transform after:duration-300"
          >
            <span class="material-symbols-outlined text-[20px]"
              >calendar_month</span
            >
            <span>Jadwal</span>
          </router-link>

          <router-link
            to="/dosen/informasi"
            active-class="text-primary after:scale-x-100"
            class="relative group flex items-center gap-1.5 text-xs font-semibold text-text-secondary hover:text-primary transition-colors py-5 after:absolute after:bottom-0 after:left-0 after:w-full after:h-0.5 after:bg-primary after:rounded-t-full after:scale-x-0 after:transition-transform after:duration-300"
          >
            <span class="material-symbols-outlined text-[17px]">info</span>
            <span>Informasi</span>
          </router-link>

          <router-link
            to="/dosen/profil"
            active-class="text-primary after:scale-x-100"
            class="relative group flex items-center gap-1.5 text-xs font-semibold text-text-secondary hover:text-primary transition-colors py-5 after:absolute after:bottom-0 after:left-0 after:w-full after:h-0.5 after:bg-primary after:rounded-t-full after:scale-x-0 after:transition-transform after:duration-300"
          >
            <span class="material-symbols-outlined text-[17px]">person</span>
            <span>Profil</span>
          </router-link>
        </nav>

        <!-- Actions -->
        <div class="flex items-center gap-4 md:gap-6 ml-auto">
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

          <div class="hidden sm:flex items-center gap-4">
            <!-- Dosen Avatar + Name -->
            <router-link
              to="/dosen/profil"
              class="flex items-center gap-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-full py-1 pl-1 pr-3 transition-colors group"
            >
              <div
                class="size-8 rounded-full flex items-center justify-center text-xs font-bold bg-primary/10 text-primary border border-primary/20"
              >
                {{ dosenInitials }}
              </div>
              <span
                class="text-sm font-medium text-text-main group-hover:text-primary transition-colors max-w-[140px] truncate"
              >
                {{ dosenName }}
              </span>
            </router-link>

            <!-- Divider -->
            <div class="h-5 w-px bg-border-light"></div>

            <!-- Logout Button -->
            <button
              @click="logout"
              class="flex items-center justify-center gap-2 h-9 px-4 rounded-full border border-border-light bg-transparent hover:bg-sidebar-light text-sm font-medium transition-colors text-text-secondary hover:text-primary"
            >
              <span class="material-symbols-outlined text-[18px]">logout</span>
              <span>Logout</span>
            </button>
          </div>

          <!-- Mobile Menu Icon -->
          <button
            @click="isMobileMenuOpen = !isMobileMenuOpen"
            class="md:hidden p-2 text-text-secondary hover:text-primary transition-colors"
          >
            <span class="material-symbols-outlined">{{
              isMobileMenuOpen ? "close" : "menu"
            }}</span>
          </button>
        </div>
      </div>

      <!-- Mobile Menu Dropdown -->
      <div
        v-if="isMobileMenuOpen"
        class="md:hidden border-t border-border-light bg-surface-light dark:bg-sidebar-light shadow-lg animate-fade-in"
      >
        <nav class="flex flex-col px-4 py-3 gap-1">
          <router-link
            to="/dosen/dashboard"
            @click="isMobileMenuOpen = false"
            active-class="bg-primary/10 text-primary"
            class="flex items-center gap-3 px-4 py-3 rounded-lg text-text-secondary hover:text-primary hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors text-sm font-medium"
          >
            <span class="material-symbols-outlined text-[20px]">dashboard</span>
            <span>Dashboard</span>
          </router-link>
          <router-link
            to="/dosen/bimbingan"
            @click="isMobileMenuOpen = false"
            active-class="bg-primary/10 text-primary"
            class="flex items-center gap-3 px-4 py-3 rounded-lg text-text-secondary hover:text-primary hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors text-sm font-medium"
          >
            <span class="material-symbols-outlined text-[20px]">groups</span>
            <span>Bimbingan</span>
          </router-link>
          <router-link
            to="/dosen/jadwal"
            @click="isMobileMenuOpen = false"
            active-class="bg-primary/10 text-primary"
            class="flex items-center gap-3 px-4 py-3 rounded-lg text-text-secondary hover:text-primary hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors text-sm font-medium"
          >
            <span class="material-symbols-outlined text-[20px]"
              >calendar_month</span
            >
            <span>Jadwal</span>
          </router-link>
          <router-link
            to="/dosen/informasi"
            @click="isMobileMenuOpen = false"
            active-class="bg-primary/10 text-primary"
            class="flex items-center gap-3 px-4 py-3 rounded-lg text-text-secondary hover:text-primary hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors text-sm font-medium"
          >
            <span class="material-symbols-outlined text-[20px]">info</span>
            <span>Informasi</span>
          </router-link>
          <router-link
            to="/dosen/profil"
            @click="isMobileMenuOpen = false"
            active-class="bg-primary/10 text-primary"
            class="flex items-center gap-3 px-4 py-3 rounded-lg text-text-secondary hover:text-primary hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors text-sm font-medium"
          >
            <span class="material-symbols-outlined text-[20px]">person</span>
            <span>Profil</span>
          </router-link>
          <div class="h-px bg-border-light my-2"></div>
          <button
            @click="logout"
            class="flex items-center gap-3 px-4 py-3 rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors text-sm font-medium w-full text-left"
          >
            <span class="material-symbols-outlined text-[20px]">logout</span>
            <span>Logout</span>
          </button>
        </nav>
      </div>
    </header>

    <!-- Main Content -->
    <main
      class="flex-1 w-full max-w-7xl mx-auto px-4 md:px-6 py-8 flex flex-col gap-6 animate-fade-in"
    >
      <router-view></router-view>
    </main>
    <ChatWidget />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "../stores/auth";
import ChatWidget from "../components/ChatWidget.vue";

const router = useRouter();
const authStore = useAuthStore();
const isDark = ref(false);
const isMobileMenuOpen = ref(false);
const windowWidth = ref(window.innerWidth);

const handleResize = () => {
  windowWidth.value = window.innerWidth;
};

const dosenName = computed(() => {
  return authStore.profile?.nama || authStore.user?.name || "Dosen";
});

const dosenInitials = computed(() => {
  const name = dosenName.value;
  return name
    .split(" ")
    .map((n) => n[0])
    .join("")
    .substring(0, 2)
    .toUpperCase();
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

  window.addEventListener("resize", handleResize);
});

onUnmounted(() => {
  window.removeEventListener("resize", handleResize);
});
</script>
