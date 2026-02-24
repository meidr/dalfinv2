<template>
  <div class="flex flex-col gap-8 animate-fade-in">
    <!-- Page Header -->
    <header>
      <h1 class="text-3xl font-bold tracking-tight text-text-main">
        Konfigurasi Sistem
      </h1>
      <p class="text-text-secondary text-base mt-2">
        Kelola pengaturan dan konfigurasi sistem skripsi.
      </p>
    </header>

    <!-- Syarat Bimbingan Ujian -->
    <section
      class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden"
    >
      <div class="p-5 border-b border-border-light flex items-center gap-3">
        <div class="p-2 bg-primary/10 rounded-lg text-primary">
          <span class="material-symbols-outlined">school</span>
        </div>
        <div>
          <h2 class="text-lg font-bold text-text-main">
            Syarat Bimbingan Ujian Skripsi
          </h2>
          <p class="text-sm text-text-secondary">
            Jumlah minimal bimbingan yang disetujui sebelum mahasiswa dapat
            mengajukan ujian
          </p>
        </div>
      </div>
      <div class="p-6">
        <div
          v-if="loadingConfig"
          class="flex items-center gap-3 text-text-secondary py-4"
        >
          <span
            class="animate-spin rounded-full h-5 w-5 border-b-2 border-primary"
          ></span>
          <span class="text-sm">Memuat konfigurasi...</span>
        </div>
        <div v-else class="flex flex-col gap-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Pembimbing 1 -->
            <div class="flex flex-col gap-2">
              <label class="text-sm font-bold text-text-main">
                Minimal Bimbingan Pembimbing 1
              </label>
              <p class="text-xs text-text-secondary mb-1">
                Jumlah sesi bimbingan yang harus disetujui oleh Pembimbing Utama
              </p>
              <input
                v-model.number="syaratForm.pembimbing_1"
                type="number"
                min="0"
                max="50"
                class="border border-border-light rounded-lg px-4 py-2.5 text-sm text-text-main bg-surface-light focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none w-full"
              />
            </div>
            <!-- Pembimbing 2 -->
            <div class="flex flex-col gap-2">
              <label class="text-sm font-bold text-text-main">
                Minimal Bimbingan Pembimbing 2
              </label>
              <p class="text-xs text-text-secondary mb-1">
                Jumlah sesi bimbingan yang harus disetujui oleh Pembimbing
                Pendamping
              </p>
              <input
                v-model.number="syaratForm.pembimbing_2"
                type="number"
                min="0"
                max="50"
                class="border border-border-light rounded-lg px-4 py-2.5 text-sm text-text-main bg-surface-light focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none w-full"
              />
            </div>
          </div>
          <div
            class="flex items-center justify-between pt-2 border-t border-border-light"
          >
            <p class="text-xs text-text-secondary">
              Perubahan akan berlaku untuk semua pengajuan ujian baru.
            </p>
            <button
              @click="saveSyarat"
              :disabled="savingConfig"
              class="inline-flex items-center gap-2 bg-primary hover:bg-blue-600 text-white font-bold px-6 py-2.5 rounded-lg transition-all shadow-sm text-sm disabled:opacity-50"
            >
              <span
                v-if="savingConfig"
                class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"
              ></span>
              <span v-else class="material-symbols-outlined text-[18px]"
                >save</span
              >
              {{ savingConfig ? "Menyimpan..." : "Simpan Perubahan" }}
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- Kuota Bimbingan Dosen -->
    <section
      class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden"
    >
      <div class="p-5 border-b border-border-light flex items-center gap-3">
        <div class="p-2 bg-emerald-500/10 rounded-lg text-emerald-600">
          <span class="material-symbols-outlined">group</span>
        </div>
        <div>
          <h2 class="text-lg font-bold text-text-main">
            Kuota Bimbingan Dosen
          </h2>
          <p class="text-sm text-text-secondary">
            Jumlah maksimal mahasiswa bimbingan per dosen (berlaku sebagai
            default untuk semua dosen)
          </p>
        </div>
      </div>
      <div class="p-6">
        <div
          v-if="loadingKuota"
          class="flex items-center gap-3 text-text-secondary py-4"
        >
          <span
            class="animate-spin rounded-full h-5 w-5 border-b-2 border-emerald-500"
          ></span>
          <span class="text-sm">Memuat konfigurasi...</span>
        </div>
        <div v-else class="flex flex-col gap-6">
          <div class="max-w-xs">
            <label class="text-sm font-bold text-text-main">
              Slot Kuota Default
            </label>
            <p class="text-xs text-text-secondary mb-2">
              Kuota ini berlaku untuk semua dosen yang tidak memiliki kuota
              individual. Kuota per dosen dapat diubah di Master Dosen.
            </p>
            <input
              v-model.number="kuotaForm.kuota"
              type="number"
              min="1"
              max="50"
              class="border border-border-light rounded-lg px-4 py-2.5 text-sm text-text-main bg-surface-light focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none w-full"
            />
          </div>
          <div
            class="flex items-center justify-between pt-2 border-t border-border-light"
          >
            <p class="text-xs text-text-secondary">
              Perubahan akan berlaku untuk semua dosen tanpa kuota individual.
            </p>
            <button
              @click="saveKuota"
              :disabled="savingKuota"
              class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-6 py-2.5 rounded-lg transition-all shadow-sm text-sm disabled:opacity-50"
            >
              <span
                v-if="savingKuota"
                class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"
              ></span>
              <span v-else class="material-symbols-outlined text-[18px]"
                >save</span
              >
              {{ savingKuota ? "Menyimpan..." : "Simpan Perubahan" }}
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- Toast -->
    <Transition name="toast-slide">
      <div
        v-if="toast.show"
        class="fixed bottom-6 right-6 z-50 flex items-center gap-3 px-5 py-3.5 rounded-xl shadow-lg text-white text-sm font-bold"
        :class="
          toast.type === 'success'
            ? 'bg-green-600 shadow-green-600/30'
            : 'bg-red-600 shadow-red-600/30'
        "
      >
        <span class="material-symbols-outlined text-[20px]">{{
          toast.type === "success" ? "check_circle" : "error"
        }}</span>
        {{ toast.message }}
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import adminService from "../../services/adminService";

const loadingConfig = ref(true);
const savingConfig = ref(false);
const syaratForm = ref({ pembimbing_1: 8, pembimbing_2: 4 });

const loadingKuota = ref(true);
const savingKuota = ref(false);
const kuotaForm = ref({ kuota: 10 });

const toast = ref({ show: false, message: "", type: "success" });

const fetchConfig = async () => {
  try {
    loadingConfig.value = true;
    const res = await adminService.getSyaratBimbingan();
    if (res.success && res.data) {
      syaratForm.value = {
        pembimbing_1: res.data.pembimbing_1 ?? 8,
        pembimbing_2: res.data.pembimbing_2 ?? 4,
      };
    }
  } catch (err) {
    console.error("Failed to fetch config:", err);
  } finally {
    loadingConfig.value = false;
  }
};

const saveSyarat = async () => {
  savingConfig.value = true;
  try {
    const res = await adminService.saveSyaratBimbingan(syaratForm.value);
    if (res.success) {
      showToast("Konfigurasi berhasil disimpan!", "success");
    }
  } catch (err) {
    console.error("Failed to save config:", err);
    showToast(
      err.response?.data?.message || "Gagal menyimpan konfigurasi.",
      "error",
    );
  } finally {
    savingConfig.value = false;
  }
};

const fetchKuota = async () => {
  try {
    loadingKuota.value = true;
    const res = await adminService.getKuotaBimbingan();
    if (res.success && res.data) {
      kuotaForm.value = {
        kuota: res.data.kuota ?? 10,
      };
    }
  } catch (err) {
    console.error("Failed to fetch kuota config:", err);
  } finally {
    loadingKuota.value = false;
  }
};

const saveKuota = async () => {
  savingKuota.value = true;
  try {
    const res = await adminService.saveKuotaBimbingan(kuotaForm.value);
    if (res.success) {
      showToast("Kuota bimbingan berhasil disimpan!", "success");
    }
  } catch (err) {
    console.error("Failed to save kuota:", err);
    showToast(
      err.response?.data?.message || "Gagal menyimpan kuota bimbingan.",
      "error",
    );
  } finally {
    savingKuota.value = false;
  }
};

const showToast = (message, type = "success") => {
  toast.value = { show: true, message, type };
  setTimeout(() => {
    toast.value.show = false;
  }, 3000);
};

onMounted(() => {
  fetchConfig();
  fetchKuota();
});
</script>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.3s ease-in-out;
}
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(5px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
.toast-slide-enter-active {
  transition: all 0.3s ease-out;
}
.toast-slide-leave-active {
  transition: all 0.2s ease-in;
}
.toast-slide-enter-from,
.toast-slide-leave-to {
  transform: translateY(20px);
  opacity: 0;
}
</style>
