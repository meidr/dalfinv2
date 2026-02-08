<template>
  <div class="flex flex-col gap-6">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
      <div class="flex flex-col gap-1">
        <h1 class="text-3xl font-bold tracking-tight text-text-main">
          Profil Saya
        </h1>
        <p class="text-text-secondary text-sm font-normal">
          Kelola informasi akun dan preferensi keamanan Anda.
        </p>
      </div>
      <button
        @click="saveProfile"
        :disabled="saving"
        class="flex items-center justify-center gap-2 bg-primary hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg transition-all shadow-md shadow-primary/20 hover:shadow-lg active:scale-95 text-sm disabled:opacity-50"
      >
        <span class="material-symbols-outlined text-[18px]">save</span>
        <span>{{ saving ? "Menyimpan..." : "Simpan Perubahan" }}</span>
      </button>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="p-12 text-center">
      <div
        class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto"
      ></div>
      <p class="text-text-secondary text-sm mt-3">Memuat profil...</p>
    </div>

    <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Profile Card -->
      <div class="flex flex-col gap-6 lg:col-span-1">
        <div
          class="bg-surface-light dark:bg-surface-light p-6 rounded-xl border border-border-light shadow-sm flex flex-col items-center text-center"
        >
          <div class="relative group">
            <div
              class="size-28 rounded-full flex items-center justify-center text-3xl font-bold border-4 border-white dark:border-gray-700 shadow-md mb-4"
              :class="getAvatarColor(user?.name)"
            >
              {{ getInitials(user?.name) }}
            </div>
          </div>
          <h2 class="text-xl font-bold text-text-main">
            {{ user?.name || "Admin" }}
          </h2>
          <p class="text-text-secondary text-sm mb-4">{{ user?.email }}</p>
          <div class="flex gap-2 w-full">
            <span
              class="inline-flex w-full items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300 border border-purple-100 dark:border-purple-800"
            >
              <span class="material-symbols-outlined text-[16px]"
                >security</span
              >
              {{ getRoleLabel(user?.role) }}
            </span>
          </div>
        </div>

        <div
          class="bg-surface-light dark:bg-surface-light p-6 rounded-xl border border-border-light shadow-sm flex flex-col gap-4"
        >
          <h3 class="text-sm font-bold text-text-main uppercase tracking-wider">
            Statistik Aktivitas
          </h3>
          <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between">
              <span class="text-sm text-text-secondary">Login Terakhir</span>
              <span class="text-sm font-medium text-text-main">{{
                formatLastLogin(user?.last_login_at)
              }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-text-secondary">Status Akun</span>
              <span
                class="text-sm font-medium"
                :class="user?.is_active ? 'text-green-600' : 'text-red-600'"
              >
                {{ user?.is_active ? "Aktif" : "Non-Aktif" }}
              </span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-text-secondary">Dibuat Pada</span>
              <span class="text-sm font-medium text-text-main">{{
                formatDate(user?.created_at)
              }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Forms -->
      <div class="flex flex-col gap-6 lg:col-span-2">
        <!-- General Info -->
        <div
          class="bg-surface-light dark:bg-surface-light p-6 rounded-xl border border-border-light shadow-sm"
        >
          <h3
            class="text-lg font-bold text-text-main mb-4 flex items-center gap-2"
          >
            <span class="material-symbols-outlined">person</span>
            Informasi Pribadi
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex flex-col gap-1.5 md:col-span-2">
              <label class="text-xs font-bold text-text-secondary uppercase"
                >Nama Lengkap</label
              >
              <input
                type="text"
                v-model="form.name"
                class="px-3 py-2 border border-border-light rounded-lg bg-white dark:bg-sidebar-light dark:border-gray-600 text-sm focus:ring-2 focus:ring-primary/50 outline-none transition-all"
              />
            </div>
            <div class="flex flex-col gap-1.5 md:col-span-2">
              <label class="text-xs font-bold text-text-secondary uppercase"
                >Email</label
              >
              <input
                type="email"
                v-model="form.email"
                class="px-3 py-2 border border-border-light rounded-lg bg-white dark:bg-sidebar-light dark:border-gray-600 text-sm focus:ring-2 focus:ring-primary/50 outline-none transition-all"
              />
            </div>
          </div>
        </div>

        <!-- Security -->
        <div
          class="bg-surface-light dark:bg-surface-light p-6 rounded-xl border border-border-light shadow-sm"
        >
          <h3
            class="text-lg font-bold text-text-main mb-4 flex items-center gap-2"
          >
            <span class="material-symbols-outlined">lock</span>
            Keamanan
          </h3>
          <div class="flex flex-col gap-4">
            <div
              class="flex items-center justify-between p-4 border border-border-light rounded-lg"
            >
              <div>
                <p class="font-bold text-text-main text-sm">Ganti Password</p>
                <p class="text-xs text-text-secondary">
                  Disarankan mengganti password secara berkala.
                </p>
              </div>
              <button
                @click="showPasswordModal = true"
                class="text-primary hover:text-blue-700 text-sm font-bold"
              >
                Update
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Password Modal -->
    <div
      v-if="showPasswordModal"
      class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
    >
      <div
        class="bg-white dark:bg-surface-light rounded-xl shadow-2xl w-full max-w-md"
      >
        <div class="p-6 border-b border-border-light">
          <h2 class="text-xl font-bold text-text-main">Ganti Password</h2>
        </div>
        <form @submit.prevent="changePassword" class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-medium text-text-main mb-1"
              >Password Lama</label
            >
            <input
              v-model="passwordForm.current_password"
              type="password"
              class="w-full px-3 py-2 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
              required
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-text-main mb-1"
              >Password Baru</label
            >
            <input
              v-model="passwordForm.new_password"
              type="password"
              class="w-full px-3 py-2 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
              required
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-text-main mb-1"
              >Konfirmasi Password Baru</label
            >
            <input
              v-model="passwordForm.new_password_confirmation"
              type="password"
              class="w-full px-3 py-2 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
              required
            />
          </div>
          <div class="flex gap-3 pt-4">
            <button
              type="button"
              @click="showPasswordModal = false"
              class="flex-1 px-4 py-2.5 border border-border-light rounded-lg text-text-secondary hover:bg-background-light transition-colors"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="savingPassword"
              class="flex-1 px-4 py-2.5 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors disabled:opacity-50"
            >
              {{ savingPassword ? "Menyimpan..." : "Simpan" }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from "vue";
import { useAuthStore } from "../../../stores/auth";
import authService from "../../../services/authService";

const authStore = useAuthStore();

const loading = ref(true);
const saving = ref(false);
const savingPassword = ref(false);
const showPasswordModal = ref(false);
const user = ref(null);

const form = reactive({
  name: "",
  email: "",
});

const passwordForm = reactive({
  current_password: "",
  new_password: "",
  new_password_confirmation: "",
});

const fetchProfile = async () => {
  try {
    loading.value = true;
    const response = await authService.getProfile();
    if (response.success) {
      user.value = response.data;
      form.name = user.value.name;
      form.email = user.value.email;
    }
  } catch (error) {
    console.error("Failed to fetch profile:", error);
  } finally {
    loading.value = false;
  }
};

const saveProfile = async () => {
  try {
    saving.value = true;
    await authService.updateProfile({
      name: form.name,
      email: form.email,
    });
    await fetchProfile();
    alert("Profil berhasil diperbarui!");
  } catch (error) {
    console.error("Failed to save profile:", error);
    alert(
      "Gagal menyimpan profil: " +
        (error.response?.data?.message || error.message),
    );
  } finally {
    saving.value = false;
  }
};

const changePassword = async () => {
  try {
    if (passwordForm.new_password !== passwordForm.new_password_confirmation) {
      alert("Konfirmasi password tidak cocok!");
      return;
    }
    savingPassword.value = true;
    await authService.changePassword({
      current_password: passwordForm.current_password,
      password: passwordForm.new_password,
      password_confirmation: passwordForm.new_password_confirmation,
    });
    showPasswordModal.value = false;
    passwordForm.current_password = "";
    passwordForm.new_password = "";
    passwordForm.new_password_confirmation = "";
    alert("Password berhasil diubah!");
  } catch (error) {
    console.error("Failed to change password:", error);
    alert(
      "Gagal mengubah password: " +
        (error.response?.data?.message || error.message),
    );
  } finally {
    savingPassword.value = false;
  }
};

const getInitials = (name) => {
  if (!name) return "A";
  return name
    .split(" ")
    .map((n) => n[0])
    .join("")
    .substring(0, 2)
    .toUpperCase();
};

const getAvatarColor = (name) => {
  const colors = [
    "bg-blue-100 text-blue-600",
    "bg-purple-100 text-purple-600",
    "bg-orange-100 text-orange-600",
    "bg-green-100 text-green-600",
  ];
  if (!name) return colors[0];
  const index = name.charCodeAt(0) % colors.length;
  return colors[index];
};

const getRoleLabel = (role) => {
  const labels = {
    admin: "Administrator",
    dosen: "Dosen",
    mahasiswa: "Mahasiswa",
  };
  return labels[role] || role;
};

const formatDate = (date) => {
  if (!date) return "-";
  return new Date(date).toLocaleDateString("id-ID", {
    day: "numeric",
    month: "long",
    year: "numeric",
  });
};

const formatLastLogin = (date) => {
  if (!date) return "Belum pernah";
  const diff = Date.now() - new Date(date).getTime();
  const hours = Math.floor(diff / (1000 * 60 * 60));
  if (hours < 1) return "Baru saja";
  if (hours < 24) return `${hours} jam yang lalu`;
  const days = Math.floor(hours / 24);
  if (days < 7) return `${days} hari yang lalu`;
  return formatDate(date);
};

onMounted(() => {
  fetchProfile();
});
</script>
