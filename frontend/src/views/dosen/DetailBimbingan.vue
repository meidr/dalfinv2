<template>
  <div class="flex flex-col gap-6">
    <!-- Breadcrumbs -->
    <div class="flex flex-wrap items-center gap-2 text-sm">
      <router-link
        to="/dosen/bimbingan"
        class="text-text-secondary hover:text-primary font-medium"
        >Mahasiswa Bimbingan</router-link
      >
      <span class="material-symbols-outlined text-text-secondary text-sm"
        >chevron_right</span
      >
      <span class="text-text-main font-bold">Detail Skripsi</span>
    </div>

    <!-- Page Heading -->
    <div
      class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4"
    >
      <div class="flex flex-col gap-1">
        <h1 class="text-3xl font-bold tracking-tight text-text-main">
          Detail Skripsi Mahasiswa
        </h1>
        <p class="text-text-secondary text-base">
          Tinjau progres, dokumen, dan riwayat bimbingan mahasiswa.
        </p>
      </div>
      <span
        class="px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-full text-sm font-bold border border-green-200 dark:border-green-800"
      >
        Status: Aktif
      </span>
    </div>

    <!-- Tabs Navigation -->
    <div class="border-b border-border-light">
      <div class="flex gap-8 overflow-x-auto no-scrollbar">
        <router-link
          v-for="tab in tabs"
          :key="tab.id"
          :to="{ name: tab.routeName }"
          class="pb-3 pt-2 min-w-fit text-sm font-bold transition-all border-b-[3px]"
          active-class="border-primary text-primary"
          exact-active-class="border-primary text-primary"
          :class="[
            $route.name === tab.routeName
              ? 'border-primary text-primary'
              : 'border-transparent text-text-secondary hover:text-text-main hover:border-gray-300',
          ]"
        >
          {{ tab.label }}
        </router-link>
      </div>
    </div>

    <!-- Tab Content -->
    <div class="mt-2 text-text-main">
      <router-view v-slot="{ Component }">
        <transition name="fade" mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>
    </div>
  </div>
</template>

<script setup>
const tabs = [
  { id: "progress", label: "Progress", routeName: "DosenBimbinganProgress" },
  { id: "umum", label: "Profil & Judul", routeName: "DosenBimbinganProfil" },
  {
    id: "pembimbing",
    label: "Pembimbing",
    routeName: "DosenBimbinganPembimbing",
  },
  { id: "log", label: "Log Bimbingan", routeName: "DosenBimbinganLog" },
  { id: "jadwal", label: "Jadwal", routeName: "DosenBimbinganJadwal" },
  { id: "nilai", label: "Nilai", routeName: "DosenBimbinganNilai" },
  { id: "dokumen", label: "Dokumen", routeName: "DosenBimbinganDokumen" },
];
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
