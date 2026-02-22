<template>
  <div class="max-w-4xl mx-auto flex flex-col gap-6 animate-fade-in-up">
    <!-- Header -->
    <div class="flex flex-col gap-1">
      <h1
        class="text-3xl font-bold tracking-tight text-text-main flex items-center gap-3"
      >
        <span class="material-symbols-outlined text-red-500 text-3xl"
          >tune</span
        >
        Pengaturan Modul
      </h1>
      <p class="text-text-secondary text-sm">
        Aktifkan atau nonaktifkan modul sistem. Perubahan berlaku untuk semua
        pengguna.
      </p>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-12">
      <div
        class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"
      ></div>
    </div>

    <!-- Module Cards -->
    <div v-else class="flex flex-col gap-4">
      <!-- Seminar Hasil Module -->
      <div
        class="bg-surface-light border border-border-light rounded-xl shadow-sm overflow-hidden"
      >
        <div class="p-6">
          <div class="flex items-start justify-between gap-4">
            <div class="flex items-start gap-4">
              <div
                class="size-12 rounded-xl flex items-center justify-center shrink-0"
                :class="
                  authStore.semhasEnabled
                    ? 'bg-blue-100 text-blue-600'
                    : 'bg-gray-100 text-gray-400'
                "
              >
                <span class="material-symbols-outlined text-2xl"
                  >co_present</span
                >
              </div>
              <div>
                <h3 class="text-lg font-bold text-text-main">Seminar Hasil</h3>
                <p class="text-sm text-text-secondary mt-1 max-w-lg">
                  Modul penjadwalan, penilaian, dan pengelolaan seminar hasil
                  skripsi. Ketika dinonaktifkan, menu seminar hasil akan
                  disembunyikan dari sidebar, progress mahasiswa tidak
                  menampilkan tahap semhas, dan dokumen terkait semhas tidak
                  ditampilkan.
                </p>
              </div>
            </div>
            <div class="flex flex-col items-end gap-2 shrink-0">
              <span
                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold"
                :class="
                  authStore.semhasEnabled
                    ? 'bg-green-50 text-green-700 border border-green-200'
                    : 'bg-red-50 text-red-700 border border-red-200'
                "
              >
                <span
                  class="w-2 h-2 rounded-full"
                  :class="
                    authStore.semhasEnabled ? 'bg-green-500' : 'bg-red-500'
                  "
                ></span>
                {{ authStore.semhasEnabled ? "Aktif" : "Nonaktif" }}
              </span>
            </div>
          </div>
        </div>
        <div class="px-6 py-4 bg-sidebar-light/30 border-t border-border-light">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 text-xs text-text-secondary">
              <span class="material-symbols-outlined text-[16px]">info</span>
              Perubahan akan langsung berlaku untuk semua pengguna.
            </div>
            <button
              @click="toggleSemhas"
              :disabled="toggling"
              class="px-5 py-2.5 rounded-lg text-sm font-semibold transition-all disabled:opacity-50 shadow-sm"
              :class="
                authStore.semhasEnabled
                  ? 'bg-red-600 hover:bg-red-700 text-white'
                  : 'bg-green-600 hover:bg-green-700 text-white'
              "
            >
              <span v-if="toggling" class="flex items-center gap-2">
                <span
                  class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"
                ></span>
                Memproses...
              </span>
              <span v-else class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">{{
                  authStore.semhasEnabled ? "toggle_off" : "toggle_on"
                }}</span>
                {{
                  authStore.semhasEnabled
                    ? "Nonaktifkan Modul"
                    : "Aktifkan Modul"
                }}
              </span>
            </button>
          </div>
        </div>
      </div>

      <!-- Info Box -->
      <div
        class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-start gap-3"
      >
        <span class="material-symbols-outlined text-amber-600 mt-0.5"
          >warning</span
        >
        <div>
          <p class="text-sm font-medium text-amber-800">Perhatian</p>
          <p class="text-xs text-amber-700 mt-1">
            Menonaktifkan modul hanya menyembunyikan tampilan dari UI. Data yang
            sudah ada (jadwal seminar, nilai, berita acara) tetap tersimpan di
            database dan akan kembali terlihat saat modul diaktifkan kembali.
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useAuthStore } from "../../../stores/auth";
import adminService from "../../../services/adminService";

const authStore = useAuthStore();
const loading = ref(true);
const toggling = ref(false);

const fetchSettings = async () => {
  try {
    loading.value = true;
    await authStore.fetchModuleSettings();
  } finally {
    loading.value = false;
  }
};

const toggleSemhas = async () => {
  const action = authStore.semhasEnabled ? "menonaktifkan" : "mengaktifkan";
  if (!confirm(`Yakin ingin ${action} modul Seminar Hasil?`)) return;

  try {
    toggling.value = true;
    const response = await adminService.toggleSemhas();
    if (response.success) {
      authStore.semhasEnabled = response.data.semhas_enabled;
      localStorage.setItem(
        "semhas_enabled",
        JSON.stringify(response.data.semhas_enabled),
      );
      alert(response.message);
    }
  } catch (e) {
    alert("Gagal: " + (e.response?.data?.message || e.message));
  } finally {
    toggling.value = false;
  }
};

onMounted(fetchSettings);
</script>
