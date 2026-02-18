<template>
  <div class="flex flex-col gap-6">
    <!-- Breadcrumbs -->
    <div class="flex flex-wrap items-center gap-2 text-sm">
      <router-link
        to="/mahasiswa/dashboard"
        class="text-text-secondary hover:text-primary font-medium"
        >Home</router-link
      >
      <span class="material-symbols-outlined text-text-secondary text-sm"
        >chevron_right</span
      >
      <span class="text-text-main font-bold">Profil</span>
    </div>

    <div class="mb-4">
      <h1 class="text-3xl font-bold tracking-tight text-text-main">
        Profil Mahasiswa
      </h1>
      <p class="text-text-secondary text-sm">
        Kelola informasi akun dan keamanan Anda.
      </p>
    </div>

    <!-- Loading State -->
    <div
      v-if="loadingProfile"
      class="grid grid-cols-1 lg:grid-cols-12 gap-8 animate-pulse"
    >
      <div
        class="lg:col-span-4 h-96 bg-gray-200 dark:bg-gray-700 rounded-xl"
      ></div>
      <div
        class="lg:col-span-8 h-80 bg-gray-200 dark:bg-gray-700 rounded-xl"
      ></div>
    </div>

    <div v-else class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
      <!-- Left: Profile Card -->
      <section class="lg:col-span-4 w-full">
        <div
          class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden"
        >
          <!-- Banner -->
          <div
            class="h-24 w-full bg-gradient-to-r from-blue-500 to-primary"
          ></div>

          <div class="px-6 pb-8 -mt-12 flex flex-col items-center">
            <!-- Avatar -->
            <div class="relative">
              <div
                class="size-32 rounded-full border-4 border-surface-light shadow-md bg-primary/10 flex items-center justify-center text-primary text-4xl font-bold"
              >
                {{ initials }}
              </div>
            </div>

            <div class="text-center w-full mb-6 mt-4">
              <h3 class="text-xl font-bold text-text-main">
                {{ mahasiswa?.nama || "-" }}
              </h3>
              <p class="text-sm font-medium text-text-secondary">
                {{ mahasiswa?.prodi?.nama || "-" }}
              </p>
              <div class="mt-3">
                <span
                  v-if="mahasiswa?.is_active"
                  class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-400 text-xs font-bold border border-green-100 dark:border-green-800"
                >
                  <span
                    class="size-2 rounded-full bg-green-500 animate-pulse"
                  ></span>
                  Mahasiswa Aktif
                </span>
                <span
                  v-else
                  class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-gray-50 text-gray-700 dark:bg-gray-900/30 dark:text-gray-400 text-xs font-bold border border-gray-100 dark:border-gray-800"
                >
                  Tidak Aktif
                </span>
              </div>
            </div>

            <div class="w-full flex flex-col gap-3">
              <div class="flex justify-between p-3 bg-sidebar-light rounded-lg">
                <span class="text-sm text-text-secondary">NIM</span>
                <span class="text-sm font-bold text-text-main">{{
                  mahasiswa?.nim || "-"
                }}</span>
              </div>
              <div class="flex justify-between p-3 bg-sidebar-light rounded-lg">
                <span class="text-sm text-text-secondary">Angkatan</span>
                <span class="text-sm font-bold text-text-main">{{
                  mahasiswa?.angkatan || "-"
                }}</span>
              </div>
              <div
                v-if="mahasiswa?.semester"
                class="flex justify-between p-3 bg-sidebar-light rounded-lg"
              >
                <span class="text-sm text-text-secondary">Semester</span>
                <span class="text-sm font-bold text-text-main">{{
                  mahasiswa.semester
                }}</span>
              </div>
              <div
                v-if="mahasiswa?.jenis_kelamin"
                class="flex justify-between p-3 bg-sidebar-light rounded-lg"
              >
                <span class="text-sm text-text-secondary">Jenis Kelamin</span>
                <span class="text-sm font-bold text-text-main">{{
                  mahasiswa.jenis_kelamin === "L" ? "Laki-laki" : "Perempuan"
                }}</span>
              </div>
              <div
                v-if="mahasiswa?.no_hp"
                class="flex flex-col gap-1 p-3 bg-sidebar-light rounded-lg"
              >
                <span
                  class="text-xs font-bold text-text-secondary uppercase tracking-wider"
                  >No. HP</span
                >
                <span class="text-sm font-medium text-text-main">{{
                  mahasiswa.no_hp
                }}</span>
              </div>
              <div class="flex flex-col gap-1 p-3 bg-sidebar-light rounded-lg">
                <span
                  class="text-xs font-bold text-text-secondary uppercase tracking-wider"
                  >Email Institusi</span
                >
                <span class="text-sm font-medium text-text-main truncate">{{
                  user?.email || mahasiswa?.email || "-"
                }}</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Right: Password Form -->
      <section class="lg:col-span-8 w-full">
        <div
          class="bg-surface-light rounded-xl shadow-sm border border-border-light p-6 md:p-8"
        >
          <div
            class="flex items-center gap-3 mb-6 border-b border-border-light pb-4"
          >
            <div class="p-2 bg-primary/10 rounded-lg text-primary">
              <span class="material-symbols-outlined mb-0.5">lock_reset</span>
            </div>
            <div>
              <h2 class="text-lg font-bold text-text-main">Ganti Password</h2>
              <p class="text-sm text-text-secondary">
                Pastikan akun Anda tetap aman.
              </p>
            </div>
          </div>

          <!-- Success Message -->
          <div
            v-if="successMsg"
            class="flex gap-3 bg-green-50 dark:bg-green-900/20 p-4 rounded-lg border border-green-100 dark:border-green-800 text-sm text-green-800 dark:text-green-300 mb-6"
          >
            <span
              class="material-symbols-outlined text-green-600 dark:text-green-400"
              >check_circle</span
            >
            <p>{{ successMsg }}</p>
          </div>

          <!-- Error Message -->
          <div
            v-if="errorMsg"
            class="flex gap-3 bg-red-50 dark:bg-red-900/20 p-4 rounded-lg border border-red-100 dark:border-red-800 text-sm text-red-800 dark:text-red-300 mb-6"
          >
            <span
              class="material-symbols-outlined text-red-600 dark:text-red-400"
              >error</span
            >
            <p>{{ errorMsg }}</p>
          </div>

          <form
            @submit.prevent="handleChangePassword"
            class="flex flex-col gap-6 max-w-2xl"
          >
            <div
              class="flex gap-3 bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-100 dark:border-blue-800 text-sm text-blue-800 dark:text-blue-300"
            >
              <span
                class="material-symbols-outlined text-blue-600 dark:text-blue-400"
                >info</span
              >
              <p>Password baru harus terdiri dari minimal 8 karakter.</p>
            </div>

            <div class="flex flex-col gap-2">
              <label class="text-sm font-bold text-text-main"
                >Password Lama</label
              >
              <input
                v-model="form.currentPassword"
                type="password"
                placeholder="Masukkan password saat ini"
                class="px-4 py-2.5 rounded-lg border border-border-light bg-background-light focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm"
                required
              />
            </div>

            <div class="h-px bg-border-light w-full"></div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="flex flex-col gap-2">
                <label class="text-sm font-bold text-text-main"
                  >Password Baru</label
                >
                <input
                  v-model="form.newPassword"
                  type="password"
                  placeholder="Buat password baru"
                  class="px-4 py-2.5 rounded-lg border border-border-light bg-background-light focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm"
                  required
                  minlength="8"
                />
              </div>
              <div class="flex flex-col gap-2">
                <label class="text-sm font-bold text-text-main"
                  >Konfirmasi Password</label
                >
                <input
                  v-model="form.confirmPassword"
                  type="password"
                  placeholder="Ulangi password baru"
                  class="px-4 py-2.5 rounded-lg border border-border-light bg-background-light focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm"
                  required
                  minlength="8"
                />
              </div>
            </div>

            <div class="flex justify-end gap-3 mt-4">
              <button
                type="button"
                @click="resetForm"
                class="px-6 py-2.5 rounded-lg text-text-secondary font-bold hover:bg-sidebar-light transition-colors text-sm"
              >
                Batal
              </button>
              <button
                type="submit"
                :disabled="saving"
                class="px-6 py-2.5 rounded-lg bg-primary text-white font-bold hover:bg-blue-600 transition-colors shadow-sm text-sm flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <span
                  v-if="saving"
                  class="material-symbols-outlined text-[18px] animate-spin"
                  >progress_activity</span
                >
                <span v-else class="material-symbols-outlined text-[18px]"
                  >save</span
                >
                {{ saving ? "Menyimpan..." : "Update Password" }}
              </button>
            </div>
          </form>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useAuthStore } from "../../stores/auth";
import authService from "../../services/authService";

const authStore = useAuthStore();
const loadingProfile = ref(true);
const saving = ref(false);
const successMsg = ref("");
const errorMsg = ref("");

const user = computed(() => authStore.currentUser);
const mahasiswa = computed(() => authStore.profile);

const initials = computed(() => {
  const nama = mahasiswa.value?.nama || "";
  return nama
    .split(" ")
    .map((w) => w[0])
    .join("")
    .toUpperCase()
    .slice(0, 2);
});

const form = ref({
  currentPassword: "",
  newPassword: "",
  confirmPassword: "",
});

const resetForm = () => {
  form.value = { currentPassword: "", newPassword: "", confirmPassword: "" };
  errorMsg.value = "";
  successMsg.value = "";
};

const handleChangePassword = async () => {
  errorMsg.value = "";
  successMsg.value = "";

  if (form.value.newPassword !== form.value.confirmPassword) {
    errorMsg.value = "Password baru dan konfirmasi password tidak cocok.";
    return;
  }

  if (form.value.newPassword.length < 8) {
    errorMsg.value = "Password baru harus terdiri dari minimal 8 karakter.";
    return;
  }

  saving.value = true;
  try {
    const res = await authService.changePassword(
      form.value.currentPassword,
      form.value.newPassword,
      form.value.confirmPassword,
    );
    successMsg.value = res.message || "Password berhasil diperbarui!";
    resetForm();
    // Keep success message visible
    successMsg.value = res.message || "Password berhasil diperbarui!";
  } catch (err) {
    const data = err.response?.data;
    if (data?.errors) {
      const firstErr = Object.values(data.errors)[0];
      errorMsg.value = Array.isArray(firstErr) ? firstErr[0] : firstErr;
    } else {
      errorMsg.value =
        data?.message || "Gagal mengubah password. Periksa password lama Anda.";
    }
  } finally {
    saving.value = false;
  }
};

onMounted(async () => {
  try {
    // Refresh user data from server
    await authStore.fetchUser();
  } catch (err) {
    console.error("Failed to fetch user profile:", err);
  } finally {
    loadingProfile.value = false;
  }
});
</script>
