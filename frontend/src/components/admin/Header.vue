<template>
  <header
    class="flex items-center justify-between border-b border-border-light px-8 py-4 bg-surface-light dark:bg-sidebar-light z-30 shrink-0 sticky top-0"
  >
    <div class="flex items-center gap-6">
      <button
        class="text-text-main lg:hidden p-2 rounded-lg hover:bg-sidebar-light transition-colors"
        @click="$emit('toggle-sidebar')"
      >
        <span class="material-symbols-outlined">menu</span>
      </button>
      <div class="hidden md:flex flex-col min-w-80 h-10">
        <div
          class="flex w-full flex-1 items-stretch rounded-full h-full bg-sidebar-light border border-border-light group focus-within:border-primary focus-within:bg-white transition-all shadow-sm"
        >
          <div
            class="text-text-secondary flex border-none items-center justify-center pl-4 pr-2"
          >
            <span
              class="material-symbols-outlined text-[20px] group-focus-within:text-primary transition-colors"
              >search</span
            >
          </div>
          <input
            class="flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-full text-text-main bg-transparent border-none focus:ring-0 placeholder:text-text-secondary text-sm font-normal leading-normal focus:outline-none"
            placeholder="Search by name, NIM, or title..."
            value=""
          />
        </div>
      </div>
    </div>
    <div class="flex items-center gap-4">
      <!-- Theme Toggle -->
      <button
        @click="toggleTheme"
        class="flex items-center justify-center size-10 rounded-full hover:bg-sidebar-light text-text-secondary transition-colors group"
      >
        <span
          class="material-symbols-outlined transition-transform duration-500 rotate-0 dark:-rotate-180 group-hover:text-primary"
        >
          {{ isDark ? "light_mode" : "dark_mode" }}
        </span>
      </button>

      <!-- Panduan Staff -->
      <div class="relative" ref="panduanRef">
        <button
          @click="togglePanduan"
          class="flex items-center justify-center size-10 rounded-full hover:bg-sidebar-light text-text-secondary transition-colors group"
          title="Panduan Staff"
        >
          <span
            class="material-symbols-outlined transition-transform group-hover:text-primary"
          >
            menu_book
          </span>
        </button>

        <!-- Panduan Dropdown -->
        <Transition name="dropdown">
          <div
            v-if="showPanduan"
            class="absolute right-0 top-12 w-72 bg-white dark:bg-surface-light rounded-xl border border-border-light shadow-xl overflow-hidden"
          >
            <div class="p-4 border-b border-border-light">
              <h3 class="text-sm font-bold text-text-main">Panduan Staff</h3>
            </div>
            <div class="max-h-60 overflow-y-auto">
              <div v-if="staffPanduan.length === 0" class="p-6 text-center">
                <span
                  class="material-symbols-outlined text-3xl text-text-secondary mb-2 block"
                  >folder_open</span
                >
                <p class="text-sm text-text-secondary">
                  Belum ada panduan staff
                </p>
              </div>
              <button
                v-for="p in staffPanduan"
                :key="p.id"
                @click="downloadStaffPanduan(p)"
                class="flex items-center gap-3 px-4 py-3 hover:bg-sidebar-light/50 transition-colors w-full text-left border-b border-border-light last:border-b-0"
              >
                <div
                  class="size-8 rounded-lg flex items-center justify-center bg-red-50 text-red-500 shrink-0"
                >
                  <span class="material-symbols-outlined text-[16px]"
                    >description</span
                  >
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium text-text-main truncate">
                    {{ p.nama_file }}
                  </p>
                  <p class="text-xs text-text-secondary">
                    {{ formatPanduanSize(p.ukuran) }}
                  </p>
                </div>
                <span
                  class="material-symbols-outlined text-primary text-[16px] shrink-0"
                  >download</span
                >
              </button>
            </div>
          </div>
        </Transition>
      </div>

      <!-- Notification Bell -->
      <div class="relative" ref="notifRef">
        <button
          @click="toggleNotifications"
          class="flex items-center justify-center size-10 rounded-full hover:bg-sidebar-light text-text-secondary relative transition-colors group"
        >
          <span
            class="material-symbols-outlined group-hover:scale-110 transition-transform"
            >notifications</span
          >
          <span
            v-if="unreadCount > 0"
            class="absolute top-1.5 right-1.5 min-w-[18px] h-[18px] flex items-center justify-center bg-red-500 text-white text-[10px] font-bold rounded-full px-1 border-2 border-white"
          >
            {{ unreadCount > 9 ? "9+" : unreadCount }}
          </span>
        </button>

        <!-- Notification Dropdown -->
        <Transition name="dropdown">
          <div
            v-if="showNotifications"
            class="absolute right-0 top-12 w-80 bg-white dark:bg-surface-light rounded-xl border border-border-light shadow-xl overflow-hidden"
          >
            <div
              class="p-4 border-b border-border-light flex items-center justify-between"
            >
              <h3 class="text-sm font-bold text-text-main">Notifikasi</h3>
              <button
                v-if="unreadCount > 0"
                @click="clearNotifications"
                class="text-xs text-primary hover:underline font-medium"
              >
                Tandai semua dibaca
              </button>
            </div>
            <div class="max-h-72 overflow-y-auto">
              <div v-if="notifications.length === 0" class="p-6 text-center">
                <span
                  class="material-symbols-outlined text-3xl text-text-secondary mb-2 block"
                  >notifications_off</span
                >
                <p class="text-sm text-text-secondary">Belum ada notifikasi</p>
              </div>
              <div
                v-for="notif in notifications"
                :key="notif.id"
                @click="handleNotifClick(notif)"
                class="flex items-start gap-3 px-4 py-3 hover:bg-sidebar-light/50 cursor-pointer transition-colors border-b border-border-light last:border-b-0"
                :class="{ 'bg-blue-50/50 dark:bg-blue-900/10': !notif.is_read }"
              >
                <div
                  class="size-8 rounded-full flex items-center justify-center text-xs shrink-0"
                  :class="getNotifClass(notif.type)"
                >
                  <span class="material-symbols-outlined text-[16px]">{{
                    getNotifIcon(notif.type)
                  }}</span>
                </div>
                <div class="min-w-0 flex-1">
                  <p
                    class="text-sm leading-snug"
                    :class="
                      notif.is_read
                        ? 'text-text-secondary'
                        : 'text-text-main font-medium'
                    "
                  >
                    {{ notif.message }}
                  </p>
                  <p class="text-xs text-text-secondary mt-0.5">
                    {{ timeAgo(notif.created_at) }}
                  </p>
                </div>
                <div
                  v-if="!notif.is_read"
                  class="w-2 h-2 rounded-full bg-primary shrink-0 mt-1.5"
                ></div>
              </div>
            </div>
          </div>
        </Transition>
      </div>

      <div class="h-6 w-px bg-border-light"></div>

      <!-- User Menu -->
      <div class="relative" ref="userMenuRef">
        <button
          @click="toggleUserMenu"
          class="flex items-center gap-3 hover:bg-sidebar-light rounded-full p-1 pr-3 transition-colors border border-transparent hover:border-border-light group"
        >
          <div
            class="size-8 rounded-full flex items-center justify-center text-xs font-bold bg-primary/10 text-primary border border-primary/20 transition-transform group-hover:scale-105"
          >
            {{ userInitials }}
          </div>
          <div class="hidden md:flex flex-col items-start">
            <span
              class="text-xs font-bold text-text-main leading-tight group-hover:text-primary transition-colors"
              >{{ displayName }}</span
            >
            <span class="text-[10px] text-text-secondary leading-tight">{{
              displayRole
            }}</span>
          </div>
          <span
            class="material-symbols-outlined text-[18px] text-text-secondary hidden md:block transition-transform"
            :class="showUserMenu ? 'rotate-180' : ''"
            >expand_more</span
          >
        </button>

        <!-- User Dropdown -->
        <Transition name="dropdown">
          <div
            v-if="showUserMenu"
            class="absolute right-0 top-12 w-56 bg-white dark:bg-surface-light rounded-xl border border-border-light shadow-xl overflow-hidden"
          >
            <div class="p-3 border-b border-border-light">
              <p class="text-sm font-bold text-text-main">{{ displayName }}</p>
              <p class="text-xs text-text-secondary">
                {{ authStore.user?.email }}
              </p>
            </div>
            <div class="py-1">
              <router-link
                to="/admin/profil"
                @click="showUserMenu = false"
                class="flex items-center gap-3 px-4 py-2.5 text-sm text-text-main hover:bg-sidebar-light/70 transition-colors"
              >
                <span
                  class="material-symbols-outlined text-[18px] text-text-secondary"
                  >person</span
                >
                Profil Saya
              </router-link>
              <button
                @click="handleLogout"
                class="flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors w-full text-left"
              >
                <span class="material-symbols-outlined text-[18px]"
                  >logout</span
                >
                Keluar
              </button>
            </div>
          </div>
        </Transition>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "../../stores/auth";
import { adminService } from "../../services/adminService";

defineEmits(["toggle-sidebar"]);

const router = useRouter();
const authStore = useAuthStore();

const isDark = ref(false);
const showNotifications = ref(false);
const showUserMenu = ref(false);
const notifRef = ref(null);
const userMenuRef = ref(null);
const panduanRef = ref(null);

const notifications = ref([]);
const unreadCount = ref(0);
let pollInterval = null;

// Staff panduan state
const showPanduan = ref(false);
const staffPanduan = ref([]);

const displayName = computed(() => {
  const user = authStore.user;
  if (!user) return "User";
  if (user.role === "super_admin") return "Super Admin";
  if (user.role === "admin") return "Admin";
  return user.name || "User";
});

const displayRole = computed(() => {
  const role = authStore.user?.role;
  if (role === "super_admin") return "Super Administrator";
  if (role === "admin") return "Administrator";
  return role || "";
});

const userInitials = computed(() => {
  const name = displayName.value;
  return name
    .split(" ")
    .map((n) => n[0])
    .join("")
    .substring(0, 2)
    .toUpperCase();
});

const toggleTheme = () => {
  isDark.value = !isDark.value;
  if (isDark.value) {
    document.documentElement.classList.add("dark");
    localStorage.setItem("theme", "dark");
  } else {
    document.documentElement.classList.remove("dark");
    localStorage.setItem("theme", "light");
  }
};

// ---- Notification helpers ----
const typeConfig = {
  pengajuan_skripsi: {
    icon: "description",
    class: "bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-300",
  },
  upload_dokumen: {
    icon: "upload_file",
    class:
      "bg-green-100 text-green-600 dark:bg-green-900/40 dark:text-green-300",
  },
  tambah_bimbingan: {
    icon: "school",
    class:
      "bg-purple-100 text-purple-600 dark:bg-purple-900/40 dark:text-purple-300",
  },
};

const getNotifIcon = (type) => typeConfig[type]?.icon || "notifications";
const getNotifClass = (type) =>
  typeConfig[type]?.class ||
  "bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400";

const timeAgo = (dateStr) => {
  const now = new Date();
  const date = new Date(dateStr);
  const diff = Math.floor((now - date) / 1000);

  if (diff < 60) return "Baru saja";
  if (diff < 3600) return `${Math.floor(diff / 60)} menit lalu`;
  if (diff < 86400) return `${Math.floor(diff / 3600)} jam lalu`;
  if (diff < 604800) return `${Math.floor(diff / 86400)} hari lalu`;
  return date.toLocaleDateString("id-ID", {
    day: "numeric",
    month: "short",
    year: "numeric",
  });
};

const fetchUnreadCount = async () => {
  try {
    const res = await adminService.getUnreadNotificationCount();
    if (res.success) {
      unreadCount.value = res.count;
    }
  } catch (err) {
    // silently ignore
  }
};

const fetchNotifications = async () => {
  try {
    const res = await adminService.getNotifications();
    if (res.success) {
      notifications.value = res.data;
    }
  } catch (err) {
    console.error("Failed to load notifications:", err);
  }
};

const toggleNotifications = async () => {
  showNotifications.value = !showNotifications.value;
  showUserMenu.value = false;

  if (showNotifications.value) {
    await fetchNotifications();
  }
};

const toggleUserMenu = () => {
  showUserMenu.value = !showUserMenu.value;
  showNotifications.value = false;
};

// --- Panduan helpers ---
const togglePanduan = async () => {
  showPanduan.value = !showPanduan.value;
  showNotifications.value = false;
  showUserMenu.value = false;
  if (showPanduan.value && staffPanduan.value.length === 0) {
    try {
      const res = await adminService.getStaffPanduan();
      if (res.success) staffPanduan.value = res.data || [];
    } catch (err) {
      console.error("Failed to fetch staff panduan:", err);
    }
  }
};

const formatPanduanSize = (bytes) => {
  if (!bytes) return "0 B";
  const sizes = ["B", "KB", "MB", "GB"];
  const i = Math.floor(Math.log(bytes) / Math.log(1024));
  return (bytes / Math.pow(1024, i)).toFixed(1) + " " + sizes[i];
};

const downloadStaffPanduan = async (p) => {
  showPanduan.value = false;
  try {
    const response = await adminService.downloadStaffPanduan(p.id);
    const blob = new Blob([response.data]);
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = p.nama_file;
    a.click();
    window.URL.revokeObjectURL(url);
  } catch (err) {
    console.error("Failed to download staff panduan:", err);
  }
};

const handleNotifClick = async (notif) => {
  if (!notif.is_read) {
    try {
      await adminService.markNotificationRead(notif.id);
      notif.is_read = true;
      unreadCount.value = Math.max(0, unreadCount.value - 1);
    } catch (err) {
      // ignore
    }
  }

  // Navigate based on type
  if (notif.data?.skripsi_id) {
    showNotifications.value = false;
    router.push(`/admin/skripsi/${notif.data.skripsi_id}`);
  }
};

const clearNotifications = async () => {
  try {
    await adminService.markAllNotificationsRead();
    notifications.value.forEach((n) => (n.is_read = true));
    unreadCount.value = 0;
  } catch (err) {
    console.error("Failed to mark all as read:", err);
  }
};

const handleLogout = async () => {
  showUserMenu.value = false;
  try {
    await authStore.logout();
    router.push("/login");
  } catch (error) {
    console.error("Logout failed:", error);
    router.push("/login");
  }
};

// Close dropdowns when clicking outside
const handleClickOutside = (event) => {
  if (notifRef.value && !notifRef.value.contains(event.target)) {
    showNotifications.value = false;
  }
  if (userMenuRef.value && !userMenuRef.value.contains(event.target)) {
    showUserMenu.value = false;
  }
  if (panduanRef.value && !panduanRef.value.contains(event.target)) {
    showPanduan.value = false;
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

  document.addEventListener("click", handleClickOutside);

  // Initial fetch + polling every 30s
  fetchUnreadCount();
  pollInterval = setInterval(fetchUnreadCount, 30000);
});

onBeforeUnmount(() => {
  document.removeEventListener("click", handleClickOutside);
  if (pollInterval) clearInterval(pollInterval);
});
</script>

<style scoped>
.dropdown-enter-active,
.dropdown-leave-active {
  transition: all 0.2s ease;
}
.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}
</style>
