<template>
  <div class="max-w-7xl mx-auto flex flex-col gap-8 animate-fade-in-up">
    <div
      class="flex flex-col md:flex-row md:items-center justify-between gap-3"
    >
      <div class="flex flex-col gap-1">
        <h1 class="text-3xl font-bold tracking-tight text-text-main">
          Manajemen User
        </h1>
        <p class="text-text-secondary text-sm font-normal">
          Kelola akun pengguna, hak akses, dan keamanan sistem skripsi.
        </p>
      </div>
      <!-- Add User Button (super_admin only) -->
      <button
        v-if="isSuperAdmin"
        @click="openAddModal"
        class="flex items-center gap-2 px-5 py-2.5 bg-primary text-white rounded-lg text-sm font-semibold hover:bg-blue-600 transition-colors shadow-sm shadow-primary/20"
      >
        <span class="material-symbols-outlined text-[18px]">person_add</span>
        Tambah User
      </button>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="p-12 text-center">
      <div
        class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto"
      ></div>
      <p class="text-text-secondary text-sm mt-3">Memuat data...</p>
    </div>

    <!-- Data Table Card -->
    <div
      v-else
      class="flex flex-col bg-surface-light border border-border-light rounded-xl shadow-sm"
    >
      <!-- Toolbar: Search & Filters -->
      <div
        class="p-5 border-b border-border-light flex flex-col md:flex-row gap-4 items-center justify-between"
      >
        <!-- Search Bar -->
        <div class="relative w-full md:max-w-md">
          <div
            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
          >
            <span class="material-symbols-outlined text-text-secondary"
              >search</span
            >
          </div>
          <input
            v-model="searchQuery"
            @input="debouncedSearch"
            class="block w-full pl-10 pr-3 py-2.5 border border-border-light rounded-lg leading-5 bg-background-light text-text-main placeholder-text-secondary focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary sm:text-sm transition-shadow dark:bg-background"
            placeholder="Cari username, nama, atau email..."
            type="text"
          />
        </div>
        <!-- Filters -->
        <div class="flex gap-3 w-full md:w-auto">
          <select
            v-model="filterRole"
            @change="fetchPengguna"
            class="px-4 py-2.5 bg-surface-light border border-border-light rounded-lg text-text-secondary text-sm focus:ring-1 focus:ring-primary dark:bg-surface"
          >
            <option value="">Semua Role</option>
            <option v-if="isSuperAdmin" value="super_admin">Super Admin</option>
            <option value="admin">Admin</option>
            <option value="staff">Staff</option>
            <option value="dosen">Dosen</option>
            <option value="mahasiswa">Mahasiswa</option>
          </select>
          <select
            v-model="filterStatus"
            @change="fetchPengguna"
            class="px-4 py-2.5 bg-surface-light border border-border-light rounded-lg text-text-secondary text-sm focus:ring-1 focus:ring-primary dark:bg-surface"
          >
            <option value="">Semua Status</option>
            <option value="active">Aktif</option>
            <option value="blocked">Diblokir</option>
          </select>
        </div>
      </div>
      <!-- Table Wrapper -->
      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
          <thead
            class="bg-sidebar-light/50 text-text-secondary font-medium border-b border-border-light"
          >
            <tr>
              <th class="px-6 py-4">User</th>
              <th class="px-6 py-4">Username</th>
              <th class="px-6 py-4">Email</th>
              <th class="px-6 py-4">Role</th>
              <th class="px-6 py-4">Status</th>
              <th class="px-6 py-4">Last Login</th>
              <th class="px-6 py-4 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border-light">
            <tr v-if="penggunaList.length === 0">
              <td colspan="7" class="p-12 text-center text-text-secondary">
                Tidak ada data pengguna
              </td>
            </tr>
            <tr
              v-for="user in penggunaList"
              :key="user.id"
              class="hover:bg-sidebar-light/30 transition-colors group"
            >
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div
                    class="size-8 rounded-full flex items-center justify-center text-xs font-bold shrink-0"
                    :class="getRoleColor(user.role)"
                  >
                    {{ getInitials(user.name) }}
                  </div>
                  <span class="font-bold text-text-main">{{ user.name }}</span>
                </div>
              </td>
              <td class="px-6 py-4 text-text-secondary">
                {{ user.username || "-" }}
              </td>
              <td class="px-6 py-4 text-text-secondary">{{ user.email }}</td>
              <td class="px-6 py-4">
                <span
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-bold"
                  :class="getRoleBadgeClass(user.role)"
                >
                  <span class="material-symbols-outlined text-[14px]">{{
                    getRoleIcon(user.role)
                  }}</span>
                  {{ getRoleLabel(user.role) }}
                </span>
              </td>
              <td class="px-6 py-4">
                <span
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium"
                  :class="
                    user.is_active
                      ? 'bg-green-50 text-green-700 border border-green-200'
                      : 'bg-red-50 text-red-700 border border-red-200'
                  "
                >
                  <span
                    class="w-1.5 h-1.5 rounded-full"
                    :class="user.is_active ? 'bg-green-600' : 'bg-red-500'"
                  ></span>
                  {{ user.is_active ? "Aktif" : "Diblokir" }}
                </span>
              </td>
              <td class="px-6 py-4 text-text-secondary text-xs">
                {{ formatLastLogin(user.last_login_at) }}
              </td>
              <td class="px-6 py-4 text-right">
                <div
                  class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity"
                >
                  <!-- Toggle Active/Block -->
                  <button
                    @click="toggleUserStatus(user)"
                    class="p-1.5 rounded-md transition-colors"
                    :class="
                      user.is_active
                        ? 'text-text-secondary hover:text-red-600 hover:bg-red-50'
                        : 'text-text-secondary hover:text-green-600 hover:bg-green-50'
                    "
                    :title="user.is_active ? 'Blokir User' : 'Aktifkan User'"
                  >
                    <span class="material-symbols-outlined text-[20px]">{{
                      user.is_active ? "block" : "check_circle"
                    }}</span>
                  </button>
                  <!-- Reset Password -->
                  <button
                    @click="resetPassword(user)"
                    class="p-1.5 text-text-secondary hover:text-amber-600 hover:bg-amber-50 rounded-md transition-colors"
                    title="Reset Password"
                  >
                    <span class="material-symbols-outlined text-[20px]"
                      >lock_reset</span
                    >
                  </button>
                  <!-- Edit Role -->
                  <button
                    v-if="canEditRole(user)"
                    @click="openEditModal(user)"
                    class="p-1.5 text-text-secondary hover:text-primary hover:bg-blue-50 rounded-md transition-colors"
                    title="Edit User"
                  >
                    <span class="material-symbols-outlined text-[20px]"
                      >edit</span
                    >
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <!-- Pagination -->
      <div
        class="flex items-center justify-between px-6 py-4 border-t border-border-light"
      >
        <p class="text-sm text-text-secondary">
          Menampilkan
          <span class="font-medium text-text-main">{{
            pagination.from || 0
          }}</span>
          sampai
          <span class="font-medium text-text-main">{{
            pagination.to || 0
          }}</span>
          dari
          <span class="font-medium text-text-main">{{ pagination.total }}</span>
          User
        </p>
        <div class="flex gap-2">
          <button
            @click="goToPage(pagination.current_page - 1)"
            :disabled="pagination.current_page <= 1"
            class="px-3 py-1.5 rounded-md border border-border-light text-text-secondary text-sm font-medium hover:bg-background-light disabled:opacity-50"
          >
            <span class="material-symbols-outlined text-[18px]"
              >chevron_left</span
            >
          </button>
          <button
            class="size-8 flex items-center justify-center rounded-lg border border-primary bg-primary text-white text-xs font-bold shadow-sm shadow-primary/20"
          >
            {{ pagination.current_page }}
          </button>
          <button
            @click="goToPage(pagination.current_page + 1)"
            :disabled="pagination.current_page >= pagination.last_page"
            class="px-3 py-1.5 rounded-md border border-border-light text-text-secondary text-sm font-medium hover:bg-background-light disabled:opacity-50"
          >
            <span class="material-symbols-outlined text-[18px]"
              >chevron_right</span
            >
          </button>
        </div>
      </div>
    </div>

    <!-- Add/Edit User Modal -->
    <Transition name="modal-fade">
      <div
        v-if="showUserModal"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
      >
        <div
          class="bg-white dark:bg-surface-light rounded-xl shadow-2xl w-full max-w-md"
        >
          <div class="p-6 border-b border-border-light">
            <h2 class="text-lg font-bold text-text-main">
              {{ isEditing ? "Edit User" : "Tambah User Baru" }}
            </h2>
            <p class="text-sm text-text-secondary mt-1">
              {{
                isEditing
                  ? `Edit akun ${selectedUser?.name}`
                  : "Buat akun admin, staff, atau super admin baru"
              }}
            </p>
          </div>
          <div class="p-6 space-y-4">
            <div>
              <label class="block text-sm font-medium text-text-main mb-1.5"
                >Nama</label
              >
              <input
                v-model="userForm.name"
                type="text"
                class="w-full px-3 py-2.5 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary bg-white dark:bg-surface-light text-sm"
                placeholder="Nama lengkap"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-text-main mb-1.5"
                >Email</label
              >
              <input
                v-model="userForm.email"
                type="email"
                :disabled="isEditing"
                class="w-full px-3 py-2.5 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary bg-white dark:bg-surface-light text-sm disabled:opacity-50 disabled:bg-gray-50"
                placeholder="email@example.com"
                autocomplete="off"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-text-main mb-1.5"
                >Password</label
              >
              <div class="relative">
                <input
                  v-model="userForm.password"
                  :type="showPassword ? 'text' : 'password'"
                  autocomplete="new-password"
                  class="w-full px-3 py-2.5 pr-10 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary bg-white dark:bg-surface-light text-sm"
                  :placeholder="
                    isEditing
                      ? 'Kosongkan jika tidak ingin diubah'
                      : 'Minimal 6 karakter'
                  "
                />
                <button
                  type="button"
                  @click="showPassword = !showPassword"
                  class="absolute inset-y-0 right-0 flex items-center pr-3 text-text-secondary hover:text-text-main"
                >
                  <span class="material-symbols-outlined text-[18px]">{{
                    showPassword ? "visibility_off" : "visibility"
                  }}</span>
                </button>
              </div>
              <p v-if="isEditing" class="text-xs text-text-secondary mt-1">
                Kosongkan jika tidak ingin mengubah password
              </p>
            </div>
            <div>
              <label class="block text-sm font-medium text-text-main mb-1.5"
                >No. Handphone</label
              >
              <input
                v-model="userForm.phone"
                type="text"
                class="w-full px-3 py-2.5 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary bg-white dark:bg-surface-light text-sm"
                placeholder="08xxxxxxxxxx"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-text-main mb-1.5"
                >Role</label
              >
              <select
                v-model="userForm.role"
                class="w-full px-3 py-2.5 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary bg-white dark:bg-surface-light text-sm"
              >
                <option value="admin">Admin</option>
                <option value="staff">Staff</option>
                <option v-if="isSuperAdmin" value="super_admin">
                  Super Admin
                </option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-text-main mb-1.5"
                >Jenis Kelamin</label
              >
              <select
                v-model="userForm.jenis_kelamin"
                class="w-full px-3 py-2.5 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary bg-white dark:bg-surface-light text-sm"
              >
                <option value="">-- Pilih --</option>
                <option value="L">Laki-laki</option>
                <option value="P">Perempuan</option>
              </select>
            </div>
            <div
              v-if="formError"
              class="p-3 bg-red-50 border border-red-200 rounded-lg"
            >
              <p class="text-sm text-red-600">{{ formError }}</p>
            </div>
            <div class="flex gap-3 pt-2">
              <button
                @click="closeUserModal"
                class="flex-1 px-4 py-2.5 border border-border-light rounded-lg text-text-secondary hover:bg-background-light transition-colors"
              >
                Batal
              </button>
              <button
                @click="saveUser"
                :disabled="saving"
                class="flex-1 px-4 py-2.5 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors disabled:opacity-50"
              >
                {{ saving ? "Menyimpan..." : "Simpan" }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Confirm Toggle Status Modal -->
    <Transition name="modal-fade">
      <div
        v-if="showToggleModal"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
      >
        <div
          class="bg-white dark:bg-surface-light rounded-xl shadow-2xl w-full max-w-sm"
        >
          <div class="p-6 text-center">
            <div
              class="size-16 mx-auto mb-4 rounded-full flex items-center justify-center"
              :class="selectedUser?.is_active ? 'bg-red-100' : 'bg-green-100'"
            >
              <span
                class="material-symbols-outlined text-3xl"
                :class="
                  selectedUser?.is_active ? 'text-red-600' : 'text-green-600'
                "
                >{{ selectedUser?.is_active ? "block" : "check_circle" }}</span
              >
            </div>
            <h3 class="text-lg font-bold text-text-main mb-2">
              {{ selectedUser?.is_active ? "Blokir User?" : "Aktifkan User?" }}
            </h3>
            <p class="text-sm text-text-secondary mb-6">
              {{
                selectedUser?.is_active
                  ? `User "${selectedUser?.name}" tidak akan bisa login setelah diblokir.`
                  : `User "${selectedUser?.name}" akan bisa login kembali setelah diaktifkan.`
              }}
            </p>
            <div class="flex gap-3">
              <button
                @click="showToggleModal = false"
                class="flex-1 px-4 py-2.5 border border-border-light rounded-lg text-text-secondary hover:bg-background-light transition-colors"
              >
                Batal
              </button>
              <button
                @click="confirmToggleStatus"
                :disabled="toggling"
                class="flex-1 px-4 py-2.5 text-white rounded-lg transition-colors disabled:opacity-50"
                :class="
                  selectedUser?.is_active
                    ? 'bg-red-600 hover:bg-red-700'
                    : 'bg-green-600 hover:bg-green-700'
                "
              >
                {{
                  toggling
                    ? "Memproses..."
                    : selectedUser?.is_active
                      ? "Blokir"
                      : "Aktifkan"
                }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive, computed } from "vue";
import { useAuthStore } from "../../../stores/auth";
import adminService from "../../../services/adminService";

const authStore = useAuthStore();
const isSuperAdmin = computed(() => authStore.isSuperAdmin);

const loading = ref(true);
const saving = ref(false);
const toggling = ref(false);
const showUserModal = ref(false);
const showToggleModal = ref(false);
const isEditing = ref(false);
const selectedUser = ref(null);
const formError = ref("");
const penggunaList = ref([]);
const searchQuery = ref("");
const filterRole = ref("");
const filterStatus = ref("");

const showPassword = ref(false);
const userForm = reactive({
  name: "",
  email: "",
  password: "",
  phone: "",
  role: "admin",
  jenis_kelamin: "",
});

const pagination = reactive({
  current_page: 1,
  last_page: 1,
  total: 0,
  from: 0,
  to: 0,
});

let searchTimeout = null;

const fetchPengguna = async () => {
  try {
    loading.value = true;
    const params = {
      page: pagination.current_page,
      search: searchQuery.value,
      role: filterRole.value,
    };
    if (filterStatus.value === "active") {
      params.is_active = 1;
    } else if (filterStatus.value === "blocked") {
      params.is_active = 0;
    }
    const response = await adminService.getPengguna(params);
    if (response.success) {
      penggunaList.value = response.data.data || response.data;
      if (response.data.current_page) {
        Object.assign(pagination, {
          current_page: response.data.current_page,
          last_page: response.data.last_page,
          total: response.data.total,
          from: response.data.from,
          to: response.data.to,
        });
      }
    }
  } catch (error) {
    console.error("Failed to fetch pengguna:", error);
  } finally {
    loading.value = false;
  }
};

const debouncedSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    pagination.current_page = 1;
    fetchPengguna();
  }, 300);
};

const goToPage = (page) => {
  if (page >= 1 && page <= pagination.last_page) {
    pagination.current_page = page;
    fetchPengguna();
  }
};

// Determine if current user can edit this user's role
const canEditRole = (user) => {
  if (isSuperAdmin.value) {
    // Super admin can edit admin, super_admin, and staff
    return (
      user.role === "admin" ||
      user.role === "super_admin" ||
      user.role === "staff"
    );
  }
  // Regular admin can edit admin and staff only
  return user.role === "admin" || user.role === "staff";
};

const openAddModal = () => {
  isEditing.value = false;
  selectedUser.value = null;
  formError.value = "";
  showPassword.value = false;
  Object.assign(userForm, {
    name: "",
    email: "",
    password: "",
    phone: "",
    role: "admin",
    jenis_kelamin: "",
  });
  showUserModal.value = true;
};

const openEditModal = (user) => {
  isEditing.value = true;
  selectedUser.value = user;
  formError.value = "";
  showPassword.value = false;
  Object.assign(userForm, {
    name: user.name,
    email: user.email,
    password: "",
    phone: user.phone || "",
    role: user.role,
    jenis_kelamin: user.jenis_kelamin || "",
  });
  showUserModal.value = true;
};

const closeUserModal = () => {
  showUserModal.value = false;
  formError.value = "";
};

const saveUser = async () => {
  formError.value = "";

  if (!userForm.name || !userForm.email) {
    formError.value = "Nama dan email wajib diisi";
    return;
  }

  if (!isEditing.value && !userForm.password) {
    formError.value = "Password wajib diisi";
    return;
  }

  if (!isEditing.value && userForm.password.length < 6) {
    formError.value = "Password minimal 6 karakter";
    return;
  }

  try {
    saving.value = true;

    if (isEditing.value) {
      // Update — only send changed fields
      const data = {
        name: userForm.name,
        role: userForm.role,
        phone: userForm.phone,
        jenis_kelamin: userForm.jenis_kelamin || null,
      };
      if (userForm.password) {
        data.password = userForm.password;
      }
      await adminService.updatePengguna(selectedUser.value.id, data);
    } else {
      // Create
      await adminService.createPengguna({
        name: userForm.name,
        email: userForm.email,
        password: userForm.password,
        role: userForm.role,
        jenis_kelamin: userForm.jenis_kelamin || null,
      });
    }

    showUserModal.value = false;
    fetchPengguna();
  } catch (error) {
    console.error("Failed to save user:", error);
    formError.value =
      error.response?.data?.message ||
      "Gagal menyimpan. Periksa kembali data yang diisi.";
  } finally {
    saving.value = false;
  }
};

const toggleUserStatus = (user) => {
  selectedUser.value = user;
  showToggleModal.value = true;
};

const confirmToggleStatus = async () => {
  try {
    toggling.value = true;
    await adminService.toggleUserStatus(selectedUser.value.id);
    showToggleModal.value = false;
    fetchPengguna();
  } catch (error) {
    console.error("Failed to toggle status:", error);
    alert(
      "Gagal mengubah status: " +
        (error.response?.data?.message || error.message),
    );
  } finally {
    toggling.value = false;
  }
};

const resetPassword = async (user) => {
  if (
    confirm(
      `Reset password untuk ${user.name}? Password akan direset ke "password".`,
    )
  ) {
    try {
      await adminService.resetPasswordPengguna(user.id);
      alert('Password berhasil direset ke "password"');
    } catch (error) {
      console.error("Failed to reset password:", error);
      alert(
        "Gagal reset password: " +
          (error.response?.data?.message || error.message),
      );
    }
  }
};

const getInitials = (name) => {
  if (!name) return "?";
  return name
    .split(" ")
    .map((n) => n[0])
    .join("")
    .substring(0, 2)
    .toUpperCase();
};

const getRoleColor = (role) => {
  const colors = {
    super_admin: "bg-red-100 text-red-600",
    admin: "bg-purple-100 text-purple-600",
    staff: "bg-blue-100 text-blue-600",
    dosen: "bg-indigo-100 text-indigo-600",
    mahasiswa: "bg-emerald-100 text-emerald-600",
  };
  return colors[role] || "bg-gray-100 text-gray-600";
};

const getRoleBadgeClass = (role) => {
  const classes = {
    super_admin: "bg-red-50 text-red-700 border border-red-100",
    admin: "bg-purple-50 text-purple-700 border border-purple-100",
    staff: "bg-blue-50 text-blue-700 border border-blue-100",
    dosen: "bg-indigo-50 text-indigo-700 border border-indigo-100",
    mahasiswa: "bg-emerald-50 text-emerald-700 border border-emerald-100",
  };
  return classes[role] || "bg-gray-50 text-gray-600 border border-gray-100";
};

const getRoleIcon = (role) => {
  const icons = {
    super_admin: "admin_panel_settings",
    admin: "security",
    staff: "badge",
    dosen: "school",
    mahasiswa: "backpack",
  };
  return icons[role] || "person";
};

const getRoleLabel = (role) => {
  const labels = {
    super_admin: "Super Admin",
    admin: "Admin",
    staff: "Staff",
    dosen: "Dosen",
    mahasiswa: "Mahasiswa",
  };
  return labels[role] || role;
};

const formatLastLogin = (date) => {
  if (!date) return "Belum pernah";
  const d = new Date(date);
  return (
    d.toLocaleDateString("id-ID", {
      day: "2-digit",
      month: "short",
      year: "numeric",
    }) +
    ", " +
    d.toLocaleTimeString("id-ID", {
      hour: "2-digit",
      minute: "2-digit",
    })
  );
};

onMounted(() => {
  fetchPengguna();
});
</script>
