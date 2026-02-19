<template>
  <div class="flex flex-col gap-6 animate-fade-in">
    <div
      class="bg-surface-light rounded-xl p-6 shadow-sm border border-border-light"
    >
      <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
        <span class="material-symbols-outlined text-primary">person</span>
        Identitas Mahasiswa
      </h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-8">
        <div class="flex flex-col">
          <span class="text-xs text-text-secondary uppercase font-semibold"
            >Nama Lengkap</span
          >
          <span class="font-medium">{{
            skripsi?.mahasiswa?.nama || authStore.profile?.nama || "-"
          }}</span>
        </div>
        <div class="flex flex-col">
          <span class="text-xs text-text-secondary uppercase font-semibold"
            >NIM</span
          >
          <span class="font-medium">{{
            skripsi?.mahasiswa?.nim || authStore.profile?.nim || "-"
          }}</span>
        </div>
        <div class="flex flex-col">
          <span class="text-xs text-text-secondary uppercase font-semibold"
            >Program Studi</span
          >
          <span class="font-medium">{{
            skripsi?.mahasiswa?.prodi?.nama ||
            authStore.profile?.prodi?.nama ||
            "-"
          }}</span>
        </div>
        <div class="flex flex-col">
          <span class="text-xs text-text-secondary uppercase font-semibold"
            >Angkatan</span
          >
          <span class="font-medium">{{
            skripsi?.mahasiswa?.tahun?.name ||
            authStore.profile?.tahun?.name ||
            "-"
          }}</span>
        </div>
      </div>
    </div>

    <div
      class="bg-surface-light rounded-xl p-6 shadow-sm border border-border-light"
    >
      <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
        <span class="material-symbols-outlined text-primary">book</span>
        Data Skripsi
      </h3>
      <div class="flex flex-col gap-4">
        <div class="flex flex-col gap-1">
          <span class="text-xs text-text-secondary uppercase font-semibold"
            >Judul Skripsi</span
          >
          <h2 class="text-xl font-bold leading-snug">
            {{ skripsi?.judul || "-" }}
          </h2>
        </div>
        <div class="flex flex-col gap-1" v-if="skripsi?.abstrak">
          <span class="text-xs text-text-secondary uppercase font-semibold"
            >Abstrak</span
          >
          <p class="text-sm text-text-secondary leading-relaxed text-justify">
            {{ skripsi.abstrak }}
          </p>
        </div>
        <div
          class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-border-light"
          v-if="skripsi?.tanggal_daftar || skripsi?.semester_daftar"
        >
          <div class="flex flex-col gap-1" v-if="skripsi.tanggal_daftar">
            <span class="text-xs text-text-secondary uppercase font-semibold"
              >Tanggal Daftar</span
            >
            <span class="font-medium">{{
              formatDate(skripsi.tanggal_daftar)
            }}</span>
          </div>
          <div class="flex flex-col gap-1" v-if="skripsi.semester_daftar">
            <span class="text-xs text-text-secondary uppercase font-semibold"
              >Semester Daftar</span
            >
            <span class="font-medium">{{ skripsi.semester_daftar }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { inject } from "vue";
import { useAuthStore } from "../../../stores/auth";

const skripsi = inject("skripsi");
const authStore = useAuthStore();

const formatDate = (dateStr) => {
  if (!dateStr) return "-";
  return new Date(dateStr).toLocaleDateString("id-ID", {
    day: "numeric",
    month: "long",
    year: "numeric",
  });
};
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
</style>
