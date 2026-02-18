<template>
  <div class="max-w-7xl mx-auto flex flex-col gap-6 animate-fade-in-up">
    <!-- Header -->
    <div class="flex flex-col gap-1">
      <h1
        class="text-3xl font-bold tracking-tight text-text-main flex items-center gap-3"
      >
        <span class="material-symbols-outlined text-red-500 text-3xl"
          >admin_panel_settings</span
        >
        Kontrol Sistem
      </h1>
      <p class="text-text-secondary text-sm">
        Panel khusus super admin untuk kontrol dan monitoring sistem.
      </p>
    </div>

    <!-- Tabs -->
    <div
      class="flex gap-1 bg-surface-light border border-border-light rounded-xl p-1 overflow-x-auto"
    >
      <button
        v-for="tab in tabs"
        :key="tab.id"
        @click="activeTab = tab.id"
        :class="[
          'flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium transition-all whitespace-nowrap',
          activeTab === tab.id
            ? 'bg-primary text-white shadow-md shadow-primary/20'
            : 'text-text-secondary hover:text-primary hover:bg-blue-50',
        ]"
      >
        <span class="material-symbols-outlined text-[18px]">{{
          tab.icon
        }}</span>
        {{ tab.label }}
      </button>
    </div>

    <!-- Tab Content -->

    <!-- 1. Activity Logs -->
    <div v-if="activeTab === 'logs'" class="flex flex-col gap-4">
      <div
        class="bg-surface-light border border-border-light rounded-xl shadow-sm"
      >
        <!-- Toolbar -->
        <div class="p-5 border-b border-border-light flex flex-col gap-4">
          <div class="flex flex-col md:flex-row gap-4 items-center">
            <div class="relative w-full md:max-w-sm">
              <div
                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
              >
                <span
                  class="material-symbols-outlined text-text-secondary text-[20px]"
                  >search</span
                >
              </div>
              <input
                v-model="logSearch"
                @input="debouncedFetchLogs"
                class="block w-full pl-10 pr-3 py-2.5 border border-border-light rounded-lg bg-background-light text-text-main placeholder-text-secondary focus:ring-1 focus:ring-primary sm:text-sm"
                placeholder="Cari log..."
              />
            </div>
            <select
              v-model="logActionFilter"
              @change="fetchLogs"
              class="px-4 py-2.5 bg-surface-light border border-border-light rounded-lg text-text-secondary text-sm focus:ring-1 focus:ring-primary"
            >
              <option value="">Semua Aksi</option>
              <option value="create">Create</option>
              <option value="update">Update</option>
              <option value="delete">Delete</option>
              <option value="login">Login</option>
              <option value="impersonate">Impersonate</option>
              <option value="system_lock">System Lock</option>
              <option value="force_logout">Force Logout</option>
            </select>
          </div>
          <!-- Date range & Export -->
          <div
            class="flex flex-col md:flex-row gap-3 items-center justify-between"
          >
            <div class="flex items-center gap-2 flex-wrap">
              <label class="text-xs font-medium text-text-secondary"
                >Dari:</label
              >
              <input
                v-model="logDateFrom"
                @change="fetchLogs"
                type="date"
                class="px-3 py-2 border border-border-light rounded-lg bg-background-light text-text-main text-sm focus:ring-1 focus:ring-primary"
              />
              <label class="text-xs font-medium text-text-secondary"
                >Sampai:</label
              >
              <input
                v-model="logDateTo"
                @change="fetchLogs"
                type="date"
                class="px-3 py-2 border border-border-light rounded-lg bg-background-light text-text-main text-sm focus:ring-1 focus:ring-primary"
              />
            </div>
            <div class="flex items-center gap-2">
              <button
                @click="fetchLogs"
                :disabled="logsLoading"
                class="flex items-center gap-2 px-4 py-2.5 bg-surface-light border border-border-light text-text-secondary rounded-lg text-sm font-semibold hover:bg-sidebar-light hover:text-primary transition-colors shadow-sm disabled:opacity-50 whitespace-nowrap"
              >
                <span
                  class="material-symbols-outlined text-[18px]"
                  :class="logsLoading ? 'animate-spin' : ''"
                  >refresh</span
                >
                Refresh
              </button>
              <button
                @click="exportLogs"
                :disabled="exporting"
                class="flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white rounded-lg text-sm font-semibold hover:bg-green-700 transition-colors shadow-sm disabled:opacity-50 whitespace-nowrap"
              >
                <span class="material-symbols-outlined text-[18px]"
                  >download</span
                >
                {{ exporting ? "Mengunduh..." : "Export Excel" }}
              </button>
            </div>
          </div>
        </div>
        <!-- Table -->
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm whitespace-nowrap">
            <thead
              class="bg-sidebar-light/50 text-text-secondary font-medium border-b border-border-light"
            >
              <tr>
                <th class="px-6 py-3">User</th>
                <th class="px-6 py-3">Aksi</th>
                <th class="px-6 py-3">Deskripsi</th>
                <th class="px-6 py-3">Detail</th>
                <th class="px-6 py-3">IP</th>
                <th class="px-6 py-3">Waktu</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-border-light">
              <tr v-if="logsLoading">
                <td colspan="6" class="p-8 text-center text-text-secondary">
                  <div
                    class="animate-spin rounded-full h-6 w-6 border-b-2 border-primary mx-auto"
                  ></div>
                </td>
              </tr>
              <tr v-else-if="logs.length === 0">
                <td colspan="6" class="p-8 text-center text-text-secondary">
                  Belum ada log aktivitas
                </td>
              </tr>
              <tr
                v-for="log in logs"
                :key="log.id"
                class="hover:bg-sidebar-light/30 transition-colors"
              >
                <td class="px-6 py-3">
                  <div class="flex items-center gap-2">
                    <div
                      class="size-7 rounded-full flex items-center justify-center text-[10px] font-bold bg-blue-100 text-blue-600"
                    >
                      {{ log.user?.name?.charAt(0)?.toUpperCase() || "?" }}
                    </div>
                    <div>
                      <p class="font-medium text-text-main text-xs">
                        {{ log.user?.name || "System" }}
                      </p>
                      <p class="text-[10px] text-text-secondary">
                        {{ log.user?.email }}
                      </p>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-3">
                  <span
                    class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase"
                    :class="getActionBadge(log.action)"
                    >{{ log.action }}</span
                  >
                </td>
                <td
                  class="px-6 py-3 text-text-secondary text-xs max-w-xs truncate"
                >
                  {{ log.description }}
                </td>
                <td
                  class="px-6 py-3 text-text-main text-xs max-w-sm"
                  :title="log.detail"
                >
                  {{ log.detail || "-" }}
                </td>
                <td class="px-6 py-3 text-text-secondary text-xs font-mono">
                  {{ log.ip_address }}
                </td>
                <td class="px-6 py-3 text-text-secondary text-xs">
                  {{ formatTime(log.created_at) }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <!-- Pagination -->
        <div
          class="flex items-center justify-between px-6 py-4 border-t border-border-light"
        >
          <p class="text-xs text-text-secondary">
            {{ logPagination.from || 0 }}–{{ logPagination.to || 0 }} dari
            {{ logPagination.total }}
          </p>
          <div class="flex gap-2">
            <button
              @click="goToLogPage(logPagination.current_page - 1)"
              :disabled="logPagination.current_page <= 1"
              class="px-3 py-1.5 rounded-md border border-border-light text-text-secondary text-sm disabled:opacity-40"
            >
              <span class="material-symbols-outlined text-[16px]"
                >chevron_left</span
              >
            </button>
            <button
              @click="goToLogPage(logPagination.current_page + 1)"
              :disabled="logPagination.current_page >= logPagination.last_page"
              class="px-3 py-1.5 rounded-md border border-border-light text-text-secondary text-sm disabled:opacity-40"
            >
              <span class="material-symbols-outlined text-[16px]"
                >chevron_right</span
              >
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- 2. Impersonation -->
    <div v-if="activeTab === 'impersonate'" class="flex flex-col gap-4">
      <!-- Impersonation Banner -->
      <div
        v-if="isImpersonating"
        class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-center justify-between"
      >
        <div class="flex items-center gap-3">
          <span class="material-symbols-outlined text-amber-600">warning</span>
          <p class="text-sm font-medium text-amber-800">
            Sedang login sebagai <strong>{{ impersonatingUser }}</strong
            >. Semua aksi dicatat.
          </p>
        </div>
        <button
          @click="stopImpersonate"
          class="px-4 py-2 bg-amber-600 text-white rounded-lg text-sm font-medium hover:bg-amber-700 transition-colors"
        >
          Kembali ke Super Admin
        </button>
      </div>

      <div
        class="bg-surface-light border border-border-light rounded-xl shadow-sm"
      >
        <div
          class="p-5 border-b border-border-light flex flex-col md:flex-row gap-4 items-center"
        >
          <div class="relative w-full md:max-w-sm">
            <div
              class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
            >
              <span
                class="material-symbols-outlined text-text-secondary text-[20px]"
                >search</span
              >
            </div>
            <input
              v-model="userSearch"
              @input="debouncedFetchUsers"
              class="block w-full pl-10 pr-3 py-2.5 border border-border-light rounded-lg bg-background-light text-text-main placeholder-text-secondary focus:ring-1 focus:ring-primary sm:text-sm"
              placeholder="Cari user untuk impersonate..."
            />
          </div>
          <select
            v-model="userRoleFilter"
            @change="fetchUsers"
            class="px-4 py-2.5 bg-surface-light border border-border-light rounded-lg text-sm"
          >
            <option value="">Semua Role</option>
            <option value="admin">Admin</option>
            <option value="dosen">Dosen</option>
            <option value="mahasiswa">Mahasiswa</option>
          </select>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm whitespace-nowrap">
            <thead
              class="bg-sidebar-light/50 text-text-secondary font-medium border-b border-border-light"
            >
              <tr>
                <th class="px-6 py-3">User</th>
                <th class="px-6 py-3">Email</th>
                <th class="px-6 py-3">Role</th>
                <th class="px-6 py-3">Status</th>
                <th class="px-6 py-3 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-border-light">
              <tr v-if="usersLoading">
                <td colspan="5" class="p-8 text-center text-text-secondary">
                  <div
                    class="animate-spin rounded-full h-6 w-6 border-b-2 border-primary mx-auto"
                  ></div>
                </td>
              </tr>
              <tr
                v-for="u in users"
                :key="u.id"
                class="hover:bg-sidebar-light/30 transition-colors group"
              >
                <td class="px-6 py-3 font-medium text-text-main">
                  {{ u.name }}
                </td>
                <td class="px-6 py-3 text-text-secondary">{{ u.email }}</td>
                <td class="px-6 py-3">
                  <span
                    class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase"
                    :class="getRoleBadge(u.role)"
                    >{{ u.role }}</span
                  >
                </td>
                <td class="px-6 py-3">
                  <span
                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium"
                    :class="
                      u.is_active
                        ? 'bg-green-50 text-green-700'
                        : 'bg-red-50 text-red-700'
                    "
                  >
                    <span
                      class="w-1.5 h-1.5 rounded-full"
                      :class="u.is_active ? 'bg-green-500' : 'bg-red-500'"
                    ></span>
                    {{ u.is_active ? "Aktif" : "Nonaktif" }}
                  </span>
                </td>
                <td class="px-6 py-3 text-right">
                  <button
                    @click="impersonateUser(u)"
                    :disabled="impersonating"
                    class="opacity-0 group-hover:opacity-100 px-3 py-1.5 bg-primary text-white rounded-lg text-xs font-medium hover:bg-blue-600 transition-all disabled:opacity-50"
                  >
                    <span
                      class="material-symbols-outlined text-[14px] align-middle mr-1"
                      >login</span
                    >
                    Login Sebagai
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- 3. Security -->
    <div v-if="activeTab === 'security'" class="flex flex-col gap-4">
      <!-- System Status Card -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div
          class="bg-surface-light border border-border-light rounded-xl p-6 shadow-sm"
        >
          <p
            class="text-xs text-text-secondary font-bold uppercase tracking-wider mb-2"
          >
            Total User
          </p>
          <p class="text-3xl font-bold text-text-main">
            {{ systemStatus.total_users || 0 }}
          </p>
        </div>
        <div
          class="bg-surface-light border border-border-light rounded-xl p-6 shadow-sm"
        >
          <p
            class="text-xs text-text-secondary font-bold uppercase tracking-wider mb-2"
          >
            Sesi Aktif
          </p>
          <p class="text-3xl font-bold text-text-main">
            {{ systemStatus.active_sessions || 0 }}
          </p>
        </div>
        <div
          class="bg-surface-light border border-border-light rounded-xl p-6 shadow-sm"
        >
          <p
            class="text-xs text-text-secondary font-bold uppercase tracking-wider mb-2"
          >
            Status Sistem
          </p>
          <p
            class="text-xl font-bold"
            :class="systemStatus.is_locked ? 'text-red-600' : 'text-green-600'"
          >
            {{ systemStatus.is_locked ? "🔒 Terkunci" : "✅ Normal" }}
          </p>
        </div>
      </div>

      <!-- Actions -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Force Logout -->
        <div
          class="bg-surface-light border border-border-light rounded-xl p-6 shadow-sm"
        >
          <div class="flex items-center gap-3 mb-3">
            <div
              class="size-10 bg-red-100 rounded-lg flex items-center justify-center"
            >
              <span class="material-symbols-outlined text-red-600">logout</span>
            </div>
            <div>
              <h3 class="font-bold text-text-main">Force Logout</h3>
              <p class="text-xs text-text-secondary">
                Logout semua user kecuali Anda
              </p>
            </div>
          </div>
          <button
            @click="forceLogoutAll"
            :disabled="actionLoading"
            class="w-full px-4 py-2.5 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition-colors disabled:opacity-50"
          >
            {{ actionLoading ? "Memproses..." : "Force Logout Semua User" }}
          </button>
        </div>

        <!-- System Lock -->
        <div
          class="bg-surface-light border border-border-light rounded-xl p-6 shadow-sm"
        >
          <div class="flex items-center gap-3 mb-3">
            <div
              class="size-10 rounded-lg flex items-center justify-center"
              :class="systemStatus.is_locked ? 'bg-green-100' : 'bg-amber-100'"
            >
              <span
                class="material-symbols-outlined"
                :class="
                  systemStatus.is_locked ? 'text-green-600' : 'text-amber-600'
                "
              >
                {{ systemStatus.is_locked ? "lock_open" : "lock" }}
              </span>
            </div>
            <div>
              <h3 class="font-bold text-text-main">
                {{
                  systemStatus.is_locked ? "Buka Kunci Sistem" : "Kunci Sistem"
                }}
              </h3>
              <p class="text-xs text-text-secondary">
                {{
                  systemStatus.is_locked
                    ? "Izinkan user mengakses sistem kembali"
                    : "Blokir akses sistem sementara"
                }}
              </p>
            </div>
          </div>
          <input
            v-if="!systemStatus.is_locked"
            v-model="lockMessage"
            placeholder="Pesan pemeliharaan (opsional)"
            class="w-full px-3 py-2 border border-border-light rounded-lg text-sm mb-3 focus:ring-1 focus:ring-primary"
          />
          <button
            @click="toggleSystemLock"
            :disabled="actionLoading"
            class="w-full px-4 py-2.5 rounded-lg text-sm font-medium transition-colors disabled:opacity-50"
            :class="
              systemStatus.is_locked
                ? 'bg-green-600 hover:bg-green-700 text-white'
                : 'bg-amber-600 hover:bg-amber-700 text-white'
            "
          >
            {{
              actionLoading
                ? "Memproses..."
                : systemStatus.is_locked
                  ? "Buka Kunci Sistem"
                  : "Kunci Sistem"
            }}
          </button>
        </div>
      </div>
    </div>

    <!-- 4. Restore Data -->
    <div v-if="activeTab === 'restore'" class="flex flex-col gap-4">
      <div
        class="bg-surface-light border border-border-light rounded-xl p-12 shadow-sm text-center"
      >
        <div
          class="size-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4"
        >
          <span class="material-symbols-outlined text-gray-400 text-3xl"
            >restore</span
          >
        </div>
        <h3 class="text-lg font-bold text-text-main mb-2">Restore Data</h3>
        <p class="text-sm text-text-secondary max-w-md mx-auto">
          Fitur ini akan tersedia ketika model menggunakan soft delete. Data
          yang dihapus dapat dikembalikan dari panel ini.
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "../../../stores/auth";
import adminService from "../../../services/adminService";
import api from "../../../services/api";

const router = useRouter();
const authStore = useAuthStore();

const activeTab = ref("logs");
const tabs = [
  { id: "logs", label: "Log Aktivitas", icon: "history" },
  { id: "impersonate", label: "Impersonasi", icon: "switch_account" },
  { id: "security", label: "Keamanan", icon: "shield" },
  { id: "restore", label: "Restore Data", icon: "restore" },
];

// Logs
const logs = ref([]);
const logsLoading = ref(false);
const logSearch = ref("");
const logActionFilter = ref("");
const logDateFrom = ref("");
const logDateTo = ref("");
const exporting = ref(false);
const logPagination = reactive({
  current_page: 1,
  last_page: 1,
  total: 0,
  from: 0,
  to: 0,
});
let logSearchTimeout = null;

// Users
const users = ref([]);
const usersLoading = ref(false);
const userSearch = ref("");
const userRoleFilter = ref("");
const impersonating = ref(false);
const isImpersonating = ref(false);
const impersonatingUser = ref("");

// Security
const systemStatus = reactive({
  total_users: 0,
  active_sessions: 0,
  is_locked: false,
  lock_message: "",
});
const lockMessage = ref("");
const actionLoading = ref(false);

const fetchLogs = async () => {
  try {
    logsLoading.value = true;
    const params = {
      page: logPagination.current_page,
      search: logSearch.value,
      action: logActionFilter.value,
      date_from: logDateFrom.value,
      date_to: logDateTo.value,
    };
    const response = await adminService.getActivityLogs(params);
    if (response.success) {
      logs.value = response.data.data || [];
      Object.assign(logPagination, {
        current_page: response.data.current_page,
        last_page: response.data.last_page,
        total: response.data.total,
        from: response.data.from,
        to: response.data.to,
      });
    }
  } catch (e) {
    console.error("Failed to fetch logs:", e);
  } finally {
    logsLoading.value = false;
  }
};

const debouncedFetchLogs = () => {
  clearTimeout(logSearchTimeout);
  logSearchTimeout = setTimeout(() => {
    logPagination.current_page = 1;
    fetchLogs();
  }, 300);
};

const exportLogs = async () => {
  try {
    exporting.value = true;
    const params = new URLSearchParams();
    if (logSearch.value) params.append("search", logSearch.value);
    if (logActionFilter.value) params.append("action", logActionFilter.value);
    if (logDateFrom.value) params.append("date_from", logDateFrom.value);
    if (logDateTo.value) params.append("date_to", logDateTo.value);

    const response = await api.get(
      `/super-admin/activity-logs/export?${params.toString()}`,
      {
        responseType: "blob",
      },
    );

    // Create download link
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement("a");
    link.href = url;

    // Extract filename from header or generate one
    const disposition = response.headers["content-disposition"];
    let filename = "Log_Aktivitas.csv";
    if (disposition) {
      const match = disposition.match(/filename="?(.+?)"?$/);
      if (match) filename = match[1];
    }
    link.setAttribute("download", filename);
    document.body.appendChild(link);
    link.click();
    link.remove();
    window.URL.revokeObjectURL(url);
  } catch (e) {
    console.error("Failed to export logs:", e);
    alert("Gagal mengunduh file export. Pastikan ada data log yang tersedia.");
  } finally {
    exporting.value = false;
  }
};

const goToLogPage = (page) => {
  if (page >= 1 && page <= logPagination.last_page) {
    logPagination.current_page = page;
    fetchLogs();
  }
};

const fetchUsers = async () => {
  try {
    usersLoading.value = true;
    const params = { search: userSearch.value, role: userRoleFilter.value };
    const response = await adminService.getSuperAdminUsers(params);
    if (response.success) {
      users.value = response.data.data || response.data;
    }
  } catch (e) {
    console.error("Failed to fetch users:", e);
  } finally {
    usersLoading.value = false;
  }
};

let userSearchTimeout = null;
const debouncedFetchUsers = () => {
  clearTimeout(userSearchTimeout);
  userSearchTimeout = setTimeout(fetchUsers, 300);
};

const impersonateUser = async (user) => {
  if (
    !confirm(
      `Login sebagai ${user.name} (${user.email})? Semua aksi akan dicatat.`,
    )
  )
    return;
  try {
    impersonating.value = true;
    const response = await adminService.impersonateUser(user.id);
    if (response.success) {
      // Save current super_admin user data for restoration later
      const currentUser = JSON.parse(localStorage.getItem("user") || "{}");
      localStorage.setItem("original_super_admin", JSON.stringify(currentUser));

      // Store the impersonation token — api.js interceptor will use it
      localStorage.setItem("auth_token", response.data.token);

      // Update local state with impersonated user
      localStorage.setItem("user", JSON.stringify(response.data.user));
      localStorage.setItem("impersonating", "true");
      localStorage.setItem("impersonating_user", user.name);
      authStore.user = response.data.user;

      // Redirect based on role
      const role = response.data.user.role;
      if (role === "admin") router.push("/admin/dashboard");
      else if (role === "dosen") router.push("/dosen/dashboard");
      else if (role === "mahasiswa") router.push("/mahasiswa/dashboard");
    }
  } catch (e) {
    console.error("Impersonate failed:", e);
    alert("Gagal impersonate: " + (e.response?.data?.message || e.message));
  } finally {
    impersonating.value = false;
  }
};

const stopImpersonate = async () => {
  try {
    // Call backend to delete the impersonation token
    await adminService.stopImpersonate();
  } catch (e) {
    // Even if the API call fails, restore local state
    console.error("Stop impersonate API call failed:", e);
  }

  // Remove the impersonation token so requests go back to cookie-based auth
  localStorage.removeItem("auth_token");

  // Restore the original super_admin user data
  const originalUser = JSON.parse(
    localStorage.getItem("original_super_admin") || "{}",
  );
  localStorage.setItem("user", JSON.stringify(originalUser));
  localStorage.removeItem("original_super_admin");
  localStorage.removeItem("impersonating");
  localStorage.removeItem("impersonating_user");
  authStore.user = originalUser;
  isImpersonating.value = false;

  router.push("/admin/super-admin");
};

const fetchSystemStatus = async () => {
  try {
    const response = await adminService.getSystemStatus();
    if (response.success) {
      Object.assign(systemStatus, response.data);
    }
  } catch (e) {
    console.error("Failed to fetch system status:", e);
  }
};

const forceLogoutAll = async () => {
  if (!confirm("Yakin ingin logout SEMUA user? Mereka harus login ulang."))
    return;
  try {
    actionLoading.value = true;
    const response = await adminService.forceLogoutAll();
    if (response.success) {
      alert(response.message);
      fetchSystemStatus();
    }
  } catch (e) {
    alert("Gagal: " + (e.response?.data?.message || e.message));
  } finally {
    actionLoading.value = false;
  }
};

const toggleSystemLock = async () => {
  const action = systemStatus.is_locked ? "membuka kunci" : "mengunci";
  if (!confirm(`Yakin ingin ${action} sistem?`)) return;
  try {
    actionLoading.value = true;
    const response = await adminService.toggleSystemLock(lockMessage.value);
    if (response.success) {
      alert(response.message);
      lockMessage.value = "";
      fetchSystemStatus();
    }
  } catch (e) {
    alert("Gagal: " + (e.response?.data?.message || e.message));
  } finally {
    actionLoading.value = false;
  }
};

const getActionBadge = (action) => {
  const badges = {
    create: "bg-green-50 text-green-700 border border-green-200",
    update: "bg-blue-50 text-blue-700 border border-blue-200",
    delete: "bg-red-50 text-red-700 border border-red-200",
    login: "bg-purple-50 text-purple-700 border border-purple-200",
    impersonate: "bg-amber-50 text-amber-700 border border-amber-200",
    system_lock: "bg-red-50 text-red-700 border border-red-200",
    system_unlock: "bg-green-50 text-green-700 border border-green-200",
    force_logout: "bg-red-50 text-red-700 border border-red-200",
    stop_impersonate: "bg-amber-50 text-amber-700 border border-amber-200",
  };
  return badges[action] || "bg-gray-50 text-gray-700 border border-gray-200";
};

const getRoleBadge = (role) => {
  const badges = {
    admin: "bg-purple-50 text-purple-700",
    dosen: "bg-indigo-50 text-indigo-700",
    mahasiswa: "bg-emerald-50 text-emerald-700",
  };
  return badges[role] || "bg-gray-50 text-gray-600";
};

const formatTime = (date) => {
  if (!date) return "-";
  const d = new Date(date);
  return (
    d.toLocaleDateString("id-ID", {
      day: "2-digit",
      month: "short",
      year: "numeric",
    }) +
    " " +
    d.toLocaleTimeString("id-ID", {
      hour: "2-digit",
      minute: "2-digit",
      second: "2-digit",
    })
  );
};

onMounted(() => {
  isImpersonating.value = localStorage.getItem("impersonating") === "true";
  impersonatingUser.value = localStorage.getItem("impersonating_user") || "";

  fetchLogs();
  fetchUsers();
  fetchSystemStatus();
});
</script>
