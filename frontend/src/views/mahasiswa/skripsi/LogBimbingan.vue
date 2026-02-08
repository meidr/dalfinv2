<template>
  <div class="flex flex-col gap-6 animate-fade-in">
    <div class="flex justify-between items-center">
      <h3 class="font-bold text-lg">Riwayat Bimbingan</h3>
      <button
        v-if="!isDosen"
        class="flex items-center gap-2 bg-primary hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition-all"
      >
        <span class="material-symbols-outlined text-[18px]">add</span>
        Tambah Log
      </button>
    </div>

    <div
      class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden"
    >
      <table class="w-full text-left text-sm">
        <thead class="bg-sidebar-light border-b border-border-light">
          <tr>
            <th class="px-6 py-4 font-bold text-text-secondary">Tanggal</th>
            <th class="px-6 py-4 font-bold text-text-secondary">
              Topik Bimbingan
            </th>
            <th class="px-6 py-4 font-bold text-text-secondary">Pembimbing</th>
            <th class="px-6 py-4 font-bold text-text-secondary text-right">
              Status
            </th>
            <th
              v-if="isDosen"
              class="px-6 py-4 font-bold text-text-secondary text-right"
            >
              Aksi
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-border-light">
          <tr class="hover:bg-sidebar-light transition-colors">
            <td class="px-6 py-4 font-medium">12 Okt 2023</td>
            <td class="px-6 py-4">
              <p class="font-bold text-text-main dark:text-white">
                Bab 1: Latar Belakang & Perumusan Masalah
              </p>
              <p class="text-xs text-text-secondary mt-0.5">
                Penambahan literatur terbaru tahun 2023
              </p>
            </td>
            <td class="px-6 py-4">
              <span
                class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded text-xs font-bold dark:bg-blue-900/30 dark:text-blue-300"
                >Dr. Rina</span
              >
            </td>
            <td class="px-6 py-4 text-right">
              <span
                class="material-symbols-outlined text-green-500"
                title="Disetujui"
                >check_circle</span
              >
            </td>
            <td v-if="isDosen" class="px-6 py-4 text-right">
              <div class="flex justify-end gap-2">
                <button
                  class="bg-green-100 text-green-600 p-1 rounded hover:bg-green-200 transition-colors"
                  title="ACC"
                >
                  <span class="material-symbols-outlined text-[18px]"
                    >check</span
                  >
                </button>
                <button
                  class="bg-red-100 text-red-600 p-1 rounded hover:bg-red-200 transition-colors"
                  title="Tolak"
                >
                  <span class="material-symbols-outlined text-[18px]"
                    >close</span
                  >
                </button>
              </div>
            </td>
          </tr>
          <tr class="hover:bg-sidebar-light transition-colors">
            <td class="px-6 py-4 font-medium">05 Okt 2023</td>
            <td class="px-6 py-4">
              <p class="font-bold text-text-main dark:text-white">
                Diskusi Metodologi Penelitian
              </p>
              <p class="text-xs text-text-secondary mt-0.5">
                Perbaikan pada alur diagram penelitian
              </p>
            </td>
            <td class="px-6 py-4">
              <span
                class="bg-orange-50 text-orange-700 px-2 py-0.5 rounded text-xs font-bold dark:bg-orange-900/30 dark:text-orange-300"
                >Siti Aminah</span
              >
            </td>
            <td class="px-6 py-4 text-right">
              <span
                class="material-symbols-outlined text-yellow-500"
                title="Revisi"
                >edit_note</span
              >
            </td>
            <td v-if="isDosen" class="px-6 py-4 text-right">
              <div class="flex justify-end gap-2">
                <button
                  class="bg-green-100 text-green-600 p-1 rounded hover:bg-green-200 transition-colors"
                  title="ACC"
                >
                  <span class="material-symbols-outlined text-[18px]"
                    >check</span
                  >
                </button>
                <button
                  class="bg-red-100 text-red-600 p-1 rounded hover:bg-red-200 transition-colors"
                  title="Tolak"
                >
                  <span class="material-symbols-outlined text-[18px]"
                    >close</span
                  >
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
defineProps({
  isDosen: {
    type: Boolean,
    default: false,
  },
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
</style>
