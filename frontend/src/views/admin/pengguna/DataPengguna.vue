<template>
  <div class="flex flex-col gap-6">
    <div class="flex flex-col gap-1">
      <h1 class="text-3xl font-bold tracking-tight text-text-main">
        Manajemen User
      </h1>
      <p class="text-text-secondary text-sm font-normal">
        Kelola akun pengguna, hak akses, dan keamanan sistem skripsi.
      </p>
    </div>

    <!-- Toolbar: Search & Add Button -->
    <div
      class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-surface-light dark:bg-surface-light p-4 rounded-xl border border-border-light shadow-sm"
    >
      <!-- Search Bar -->
      <div class="w-full sm:max-w-md relative group">
        <div
          class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
        >
          <span
            class="material-symbols-outlined text-text-secondary group-focus-within:text-primary transition-colors"
            >search</span
          >
        </div>
        <input
          v-model="searchQuery"
          @input="debouncedSearch"
          class="block w-full pl-10 pr-3 py-2.5 border border-border-light rounded-lg bg-white dark:bg-sidebar-light dark:border-gray-600 text-text-main placeholder-text-secondary focus:ring-2 focus:ring-primary/50 text-sm transition-all outline-none"
          placeholder="Cari username, nama, atau email..."
          type="text"
        />
      </div>
      <!-- Filter & Add Button -->
      <div class="flex gap-3">
        <select
          v-model="filterRole"
          @change="fetchPengguna"
          class="px-4 py-2.5 border border-border-light rounded-lg bg-white text-sm focus:ring-1 focus:ring-primary"
        >
          <option value="">Semua Role</option>
          <option value="admin">Admin</option>
          <option value="dosen">Dosen</option>
          <option value="mahasiswa">Mahasiswa</option>
        </select>
        <button
          @click="openAddModal"
          class="flex shrink-0 items-center justify-center gap-2 bg-primary hover:bg-blue-600 text-white px-5 py-2.5 rounded-lg text-sm font-semibold shadow-md shadow-primary/20 hover:shadow-lg active:scale-95 transition-all"
        >
          <span class="material-symbols-outlined text-[20px]">add</span>
          <span>Tambah User</span>
        </button>
      </div>
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
      class="bg-surface-light dark:bg-surface-light rounded-xl border border-border-light shadow-sm flex flex-col overflow-hidden"
    >
      <!-- Table Wrapper -->
      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
          <thead
            class="bg-sidebar-light/50 text-text-secondary font-bold tracking-widest uppercase text-[10px] border-b border-border-light"
          >
            <tr>
              <th class="px-6 py-4">Username</th>
              <th class="px-6 py-4">Nama Lengkap</th>
              <th class="px-6 py-4">Email</th>
              <th class="px-6 py-4">Role</th>
              <th class="px-6 py-4">Last Login</th>
              <th class="px-6 py-4 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border-light">
            <tr v-if="penggunaList.length === 0">
              <td colspan="6" class="p-12 text-center text-text-secondary">
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
                  <span class="font-bold text-text-main">{{
                    user.username || user.email?.split("@")[0]
                  }}</span>
                </div>
              </td>
              <td class="px-6 py-4 text-text-main text-sm font-medium">
                {{ user.name }}
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
              <td class="px-6 py-4 text-text-secondary text-xs">
                {{ formatLastLogin(user.last_login_at) }}
              </td>
              <td class="px-6 py-4 text-right">
                <div
                  class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity"
                >
                  <button
                    @click="openEditModal(user)"
                    class="p-1.5 text-text-secondary hover:text-primary hover:bg-blue-50 rounded-md transition-colors"
                    title="Edit User"
                  >
                    <span class="material-symbols-outlined text-[20px]"
                      >edit</span
                    >
                  </button>
                  <button
                    @click="resetPassword(user)"
                    class="p-1.5 text-text-secondary hover:text-amber-600 hover:bg-amber-50 rounded-md transition-colors"
                    title="Reset Password"
                  >
                    <span class="material-symbols-outlined text-[20px]"
                      >lock_reset</span
                    >
                  </button>
                  <button
                    @click="confirmDelete(user)"
                    class="p-1.5 text-text-secondary hover:text-red-600 hover:bg-red-50 rounded-md transition-colors"
                    title="Hapus User"
                  >
                    <span class="material-symbols-outlined text-[20px]"
                      >delete</span
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
        class="p-4 border-t border-border-light flex flex-col md:flex-row items-center justify-between gap-4 bg-gray-50/50"
      >
        <p class="text-xs text-text-secondary font-medium">
          Menampilkan {{ pagination.from || 0 }}-{{ pagination.to || 0 }} dari
          {{ pagination.total }} User
        </p>
        <div class="flex gap-2">
          <button
            @click="goToPage(pagination.current_page - 1)"
            :disabled="pagination.current_page <= 1"
            class="p-1.5 rounded-lg border border-border-light bg-white hover:bg-gray-50 disabled:opacity-50 text-text-secondary transition-all"
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
            class="p-1.5 rounded-lg border border-border-light bg-white hover:bg-gray-50 text-text-secondary transition-all"
          >
            <span class="material-symbols-outlined text-[18px]"
              >chevron_right</span
            >
          </button>
        </div>
      </div>
    </div>

    <!-- Add/Edit Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
    >
      <div
        class="bg-white dark:bg-surface-light rounded-xl shadow-2xl w-full max-w-md"
      >
        <div class="p-6 border-b border-border-light">
          <h2 class="text-xl font-bold text-text-main">
            {{ isEditing ? "Edit User" : "Tambah User Baru" }}
          </h2>
        </div>
        <form @submit.prevent="saveUser" class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-medium text-text-main mb-1"
              >Nama Lengkap</label
            >
            <input
              v-model="userForm.name"
              type="text"
              class="w-full px-3 py-2 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
              required
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-text-main mb-1"
              >Email</label
            >
            <input
              v-model="userForm.email"
              type="email"
              class="w-full px-3 py-2 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
              required
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-text-main mb-1"
              >Role</label
            >
            <select
              v-model="userForm.role"
              class="w-full px-3 py-2 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
              required
            >
              <option value="admin">Admin</option>
              <option value="dosen">Dosen</option>
              <option value="mahasiswa">Mahasiswa</option>
            </select>
          </div>
          <div v-if="!isEditing">
            <label class="block text-sm font-medium text-text-main mb-1"
              >Password</label
            >
            <input
              v-model="userForm.password"
              type="password"
              class="w-full px-3 py-2 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
              :required="!isEditing"
            />
          </div>
          <div class="flex gap-3 pt-4">
            <button
              type="button"
              @click="showModal = false"
              class="flex-1 px-4 py-2.5 border border-border-light rounded-lg text-text-secondary hover:bg-background-light transition-colors"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="saving"
              class="flex-1 px-4 py-2.5 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors disabled:opacity-50"
            >
              {{ saving ? "Menyimpan..." : "Simpan" }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div
      v-if="showDeleteModal"
      class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
    >
      <div
        class="bg-white dark:bg-surface-light rounded-xl shadow-2xl w-full max-w-sm"
      >
        <div class="p-6 text-center">
          <div
            class="size-16 mx-auto mb-4 rounded-full bg-red-100 flex items-center justify-center"
          >
            <span class="material-symbols-outlined text-red-600 text-3xl"
              >warning</span
            >
          </div>
          <h3 class="text-lg font-bold text-text-main mb-2">Hapus User?</h3>
          <p class="text-sm text-text-secondary mb-6">
            Anda yakin ingin menghapus user
            <strong>{{ selectedUser?.name }}</strong
            >? Tindakan ini tidak dapat dibatalkan.
          </p>
          <div class="flex gap-3">
            <button
              @click="showDeleteModal = false"
              class="flex-1 px-4 py-2.5 border border-border-light rounded-lg text-text-secondary hover:bg-background-light transition-colors"
            >
              Batal
            </button>
            <button
              @click="deleteUser"
              :disabled="deleting"
              class="flex-1 px-4 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50"
            >
              {{ deleting ? "Menghapus..." : "Hapus" }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from "vue";
import adminService from "../../../services/adminService";

const loading = ref(true);
const saving = ref(false);
const deleting = ref(false);
const showModal = ref(false);
const showDeleteModal = ref(false);
const isEditing = ref(false);
const selectedUser = ref(null);
const penggunaList = ref([]);
const searchQuery = ref("");
const filterRole = ref("");

const pagination = reactive({
  current_page: 1,
  last_page: 1,
  total: 0,
  from: 0,
  to: 0,
});

const userForm = reactive({
  name: "",
  email: "",
  role: "mahasiswa",
  password: "",
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

const openAddModal = () => {
  isEditing.value = false;
  userForm.name = "";
  userForm.email = "";
  userForm.role = "mahasiswa";
  userForm.password = "";
  showModal.value = true;
};

const openEditModal = (user) => {
  isEditing.value = true;
  selectedUser.value = user;
  userForm.name = user.name;
  userForm.email = user.email;
  userForm.role = user.role;
  userForm.password = "";
  showModal.value = true;
};

const saveUser = async () => {
  try {
    saving.value = true;
    const data = {
      name: userForm.name,
      email: userForm.email,
      role: userForm.role,
    };
    if (!isEditing.value && userForm.password) {
      data.password = userForm.password;
    }

    if (isEditing.value) {
      await adminService.updatePengguna(selectedUser.value.id, data);
    } else {
      await adminService.createPengguna(data);
    }
    showModal.value = false;
    fetchPengguna();
  } catch (error) {
    console.error("Failed to save user:", error);
    alert(
      "Gagal menyimpan user: " +
        (error.response?.data?.message || error.message),
    );
  } finally {
    saving.value = false;
  }
};

const resetPassword = async (user) => {
  if (confirm(`Reset password untuk ${user.name}?`)) {
    try {
      await adminService.resetPasswordPengguna(user.id);
      alert('Password berhasil direset. Password baru: "password"');
    } catch (error) {
      console.error("Failed to reset password:", error);
      alert("Gagal reset password");
    }
  }
};

const confirmDelete = (user) => {
  selectedUser.value = user;
  showDeleteModal.value = true;
};

const deleteUser = async () => {
  try {
    deleting.value = true;
    await adminService.deletePengguna(selectedUser.value.id);
    showDeleteModal.value = false;
    fetchPengguna();
  } catch (error) {
    console.error("Failed to delete user:", error);
    alert(
      "Gagal menghapus user: " +
        (error.response?.data?.message || error.message),
    );
  } finally {
    deleting.value = false;
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
    admin: "bg-purple-100 text-purple-600",
    dosen: "bg-indigo-100 text-indigo-600",
    mahasiswa: "bg-emerald-100 text-emerald-600",
  };
  return colors[role] || "bg-gray-100 text-gray-600";
};

const getRoleBadgeClass = (role) => {
  const classes = {
    admin: "bg-purple-50 text-purple-700 border border-purple-100",
    dosen: "bg-indigo-50 text-indigo-700 border border-indigo-100",
    mahasiswa: "bg-emerald-50 text-emerald-700 border border-emerald-100",
  };
  return classes[role] || "bg-gray-50 text-gray-600 border border-gray-100";
};

const getRoleIcon = (role) => {
  const icons = { admin: "security", dosen: "school", mahasiswa: "backpack" };
  return icons[role] || "person";
};

const getRoleLabel = (role) => {
  const labels = { admin: "Admin", dosen: "Dosen", mahasiswa: "Mahasiswa" };
  return labels[role] || role;
};

const formatLastLogin = (date) => {
  if (!date) return "Belum pernah";
  const diff = Date.now() - new Date(date).getTime();
  const hours = Math.floor(diff / (1000 * 60 * 60));
  if (hours < 1) return "Online";
  if (hours < 24) return `${hours} jam lalu`;
  const days = Math.floor(hours / 24);
  if (days === 1) return "Kemarin";
  return `${days} hari lalu`;
};

onMounted(() => {
  fetchPengguna();
});
</script>
