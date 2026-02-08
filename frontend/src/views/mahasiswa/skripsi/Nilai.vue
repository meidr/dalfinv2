<template>
  <div class="flex flex-col gap-6 animate-fade-in">
    <!-- Card 1: Nilai Seminar Proposal -->
    <div
      class="bg-surface-light rounded-xl p-6 shadow-sm border border-border-light"
    >
      <div
        class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4"
      >
        <div class="flex items-center gap-3">
          <div class="bg-primary/10 p-2 rounded-lg text-primary">
            <span class="material-symbols-outlined">description</span>
          </div>
          <h3 class="text-xl font-bold text-text-main">
            Nilai Seminar Proposal
          </h3>
        </div>
        <span
          class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 w-fit"
        >
          <span class="material-symbols-outlined text-base">check_circle</span>
          Lulus (Passed)
        </span>
      </div>
      <div
        class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-border-light"
      >
        <div class="flex flex-col gap-1">
          <span
            class="text-xs font-semibold uppercase tracking-wider text-text-secondary"
            >Tanggal Sidang</span
          >
          <div class="flex items-center gap-2 text-text-main font-medium">
            <span class="material-symbols-outlined text-text-secondary"
              >event</span
            >
            12 October 2023
          </div>
        </div>
        <div class="flex flex-col gap-1">
          <span
            class="text-xs font-semibold uppercase tracking-wider text-text-secondary"
            >Nilai Akhir</span
          >
          <div class="flex items-center gap-2 text-text-main font-medium">
            <span class="material-symbols-outlined text-text-secondary"
              >analytics</span
            >
            85.5
          </div>
        </div>
      </div>
    </div>

    <!-- Card 2: Nilai Ujian Skripsi -->
    <div
      class="bg-surface-light rounded-xl p-6 shadow-sm border border-border-light"
    >
      <div class="flex items-center gap-3 mb-6">
        <div class="bg-primary/10 p-2 rounded-lg text-primary">
          <span class="material-symbols-outlined">school</span>
        </div>
        <h3 class="text-xl font-bold text-text-main">Nilai Ujian Skripsi</h3>
      </div>
      <div class="flex flex-col lg:flex-row gap-8">
        <!-- Score Highlight -->
        <div class="flex-1 lg:max-w-xs">
          <div
            class="bg-primary/5 dark:bg-primary/10 rounded-xl p-6 flex flex-col items-center justify-center h-full border border-primary/10 text-center"
          >
            <span class="text-sm font-medium text-text-secondary mb-2"
              >Final Grade</span
            >
            <div class="text-6xl font-black text-primary mb-1">A</div>
            <div class="text-2xl font-bold text-text-main">88.0</div>
            <span
              class="inline-flex mt-4 items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400"
            >
              <span class="material-symbols-outlined text-sm">verified</span>
              Excellent
            </span>
          </div>
        </div>
        <!-- Score Breakdown Table -->
        <div class="flex-[2] flex flex-col gap-4">
          <h4 class="font-bold text-text-main">Score Breakdown</h4>
          <div
            class="w-full overflow-hidden rounded-lg border border-border-light"
          >
            <table class="w-full text-left text-sm">
              <thead class="bg-sidebar-light">
                <tr>
                  <th class="px-4 py-3 font-semibold text-text-secondary">
                    Code
                  </th>
                  <th class="px-4 py-3 font-semibold text-text-secondary">
                    Component Name
                  </th>
                  <th
                    class="px-4 py-3 font-semibold text-text-secondary text-right"
                  >
                    Score
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-border-light">
                <tr class="hover:bg-sidebar-light transition-colors">
                  <td class="px-4 py-3 font-medium text-text-secondary">MT</td>
                  <td class="px-4 py-3 text-text-main">
                    Materi Tulis (Writing Material)
                  </td>
                  <td class="px-4 py-3 text-text-main font-bold text-right">
                    85
                  </td>
                </tr>
                <tr class="hover:bg-sidebar-light transition-colors">
                  <td class="px-4 py-3 font-medium text-text-secondary">MS</td>
                  <td class="px-4 py-3 text-text-main">
                    Materi Seminar (Seminar Material)
                  </td>
                  <td class="px-4 py-3 text-text-main font-bold text-right">
                    90
                  </td>
                </tr>
                <tr class="hover:bg-sidebar-light transition-colors">
                  <td class="px-4 py-3 font-medium text-text-secondary">PM</td>
                  <td class="px-4 py-3 text-text-main">
                    Penguasaan Materi (Mastery)
                  </td>
                  <td class="px-4 py-3 text-text-main font-bold text-right">
                    88
                  </td>
                </tr>
                <tr class="hover:bg-sidebar-light transition-colors">
                  <td class="px-4 py-3 font-medium text-text-secondary">PI</td>
                  <td class="px-4 py-3 text-text-main">
                    Perilaku Ilmiah (Scientific Conduct)
                  </td>
                  <td class="px-4 py-3 text-text-main font-bold text-right">
                    92
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
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
