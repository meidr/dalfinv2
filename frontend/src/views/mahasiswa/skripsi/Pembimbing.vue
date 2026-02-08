<template>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6 animate-fade-in">
    <div
      class="bg-surface-light rounded-xl p-6 shadow-sm border border-border-light flex items-start gap-4"
    >
      <div
        class="size-12 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-primary font-bold text-xl"
      >
        Dr
      </div>
      <div>
        <h4 class="font-bold text-lg">Dr. Rina Kurniawati, M.Kom</h4>
        <p class="text-sm text-text-secondary mb-2">Pembimbing Utama</p>
        <div class="flex gap-2">
          <span
            class="px-2 py-0.5 bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 text-xs rounded font-medium"
            >Disetujui</span
          >
        </div>
        <div class="mt-4 flex gap-2">
          <button
            class="px-3 py-1.5 text-xs font-bold text-primary border border-primary/20 rounded-lg hover:bg-primary/5 transition-colors"
          >
            Chat Dosen
          </button>
          <button
            class="px-3 py-1.5 text-xs font-bold text-text-secondary border border-border-light rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
          >
            Lihat Jadwal
          </button>
        </div>
      </div>
    </div>

    <div
      class="bg-surface-light rounded-xl p-6 shadow-sm border border-border-light flex items-start gap-4"
    >
      <div
        class="size-12 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center text-orange-600 font-bold text-xl"
      >
        SA
      </div>
      <div>
        <h4 class="font-bold text-lg">Siti Aminah, M.T.</h4>
        <p class="text-sm text-text-secondary mb-2">Pembimbing Pendamping</p>
        <div class="flex gap-2">
          <span
            class="px-2 py-0.5 bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 text-xs rounded font-medium"
            >Disetujui</span
          >
        </div>
        <div class="mt-4 flex gap-2">
          <button
            class="px-3 py-1.5 text-xs font-bold text-primary border border-primary/20 rounded-lg hover:bg-primary/5 transition-colors"
          >
            Chat Dosen
          </button>
          <button
            class="px-3 py-1.5 text-xs font-bold text-text-secondary border border-border-light rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
          >
            Lihat Jadwal
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

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
