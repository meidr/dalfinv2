<template>
  <div class="flex flex-col gap-6">
    <div class="flex flex-col gap-1">
      <h1 class="text-text-main text-3xl font-bold leading-tight">
        Seminar Proposal
      </h1>
      <p class="text-text-secondary text-sm font-normal">
        Daftar mahasiswa yang memenuhi syarat untuk melaksanakan seminar
        proposal.
      </p>
    </div>

    <!-- Table Container -->
    <div
      class="flex flex-col rounded-xl border border-border-light bg-surface-light dark:bg-surface-light overflow-hidden shadow-sm"
    >
      <div
        class="p-6 border-b border-border-light flex flex-wrap gap-4 justify-between items-center bg-gray-50/50 dark:bg-sidebar-light/50"
      >
        <div>
          <h3 class="text-text-main text-lg font-bold">Antrian Seminar</h3>
          <p class="text-text-secondary text-xs">
            Kelola jadwal dan input nilai.
          </p>
        </div>
        <div class="flex items-center gap-3">
          <div
            class="flex items-center gap-2 px-3 py-1.5 bg-white border border-border-light rounded-lg dark:bg-sidebar-light dark:border-gray-600"
          >
            <span
              class="material-symbols-outlined text-text-secondary text-[18px]"
              >filter_list</span
            >
            <span class="text-xs font-medium text-text-secondary"
              >Filter Status</span
            >
          </div>
          <button
            class="text-primary text-sm font-bold hover:bg-primary/5 px-4 py-2 rounded-lg transition-colors border border-primary/20 dark:hover:bg-primary/20"
          >
            Export Data
          </button>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr
              class="bg-sidebar-light/50 border-b border-border-light dark:bg-sidebar-light/50"
            >
              <th
                class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase w-1/2"
              >
                Mahasiswa
              </th>
              <th
                class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase w-1/3"
              >
                Jadwal
              </th>
              <th
                class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase text-right"
              >
                Action
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border-light">
            <tr
              v-for="i in 4"
              :key="i"
              class="group hover:bg-sidebar-light/30 transition-colors"
            >
              <td class="p-4 align-top">
                <div class="flex items-start gap-4">
                  <div
                    class="bg-gray-200 size-12 rounded-full bg-cover bg-center border border-border-light shadow-sm shrink-0"
                    style="
                      background-image: url(&quot;https://lh3.googleusercontent.com/aida-public/AB6AXuCXvmzpNr4yS293bcSUOqs1k0_0T3xBHHwPvluP2og08U-Pmekxd-lNCbHkg4l_ebu9pQp84cfHsau6-I-Loo86_6lN4k7FAYpMXo2M8gLPbEmwMhxkImXktkSZuWnkqjuWbqooz7ty-6u73H6Ww1mo00MhCdgeUOEIo9ciWJAM13shkp7T-5f66DTAYns5Co-T2rBySywlsV7JMpyqqLNvqISxOJQ8Z_kdUpsLms_CGCqTmOhzaZq9rjsu2GBl23JEAP9HMC838l60&quot;);
                    "
                  ></div>
                  <div class="flex flex-col gap-1">
                    <div>
                      <p class="font-bold text-text-main text-base">
                        Budi Santoso
                      </p>
                      <p class="text-xs text-text-secondary font-medium">
                        NIM: 201910234
                      </p>
                    </div>
                    <p
                      class="text-sm text-text-main line-clamp-2 leading-relaxed"
                    >
                      Implementasi Algoritma Genetic Pada Penjadwalan Mata
                      Kuliah
                    </p>
                    <div class="mt-1">
                      <span
                        class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-green-50 text-green-600 border border-green-100 uppercase tracking-wide dark:bg-green-500/20 dark:text-green-400 dark:border-green-500/30"
                      >
                        <span
                          class="size-1.5 rounded-full bg-green-600 dark:bg-green-400"
                        ></span>
                        Siap Sempro
                      </span>
                    </div>
                  </div>
                </div>
              </td>
              <td class="p-4 align-top">
                <div class="flex flex-col justify-center h-full pt-1">
                  <span
                    class="inline-flex items-center gap-2 text-text-secondary italic text-sm p-2 bg-gray-50 rounded-lg border border-gray-100 border-dashed w-fit dark:bg-sidebar-light/50 dark:border-gray-600"
                  >
                    <span class="material-symbols-outlined text-[18px]"
                      >calendar_today</span
                    >
                    Belum Dijadwalkan
                  </span>
                </div>
              </td>
              <td class="p-4 align-middle text-right">
                <button
                  class="inline-flex items-center justify-center px-4 py-2 text-xs font-bold text-white bg-primary rounded-lg hover:bg-primary/90 transition-all shadow-sm"
                >
                  Input Jadwal
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Footer -->
      <div
        class="p-4 border-t border-border-light flex items-center justify-between bg-gray-50/50 dark:bg-sidebar-light/50"
      >
        <span class="text-xs text-text-secondary"
          >Menampilkan 4 dari 24 mahasiswa</span
        >
        <div class="flex gap-1">
          <button
            class="size-8 flex items-center justify-center rounded border border-border-light bg-white dark:bg-sidebar-light text-text-secondary hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-50"
          >
            <span class="material-symbols-outlined text-sm">chevron_left</span>
          </button>
          <button
            class="size-8 flex items-center justify-center rounded border border-primary bg-primary text-white text-xs font-bold"
          >
            1
          </button>
          <button
            class="size-8 flex items-center justify-center rounded border border-border-light bg-white dark:bg-sidebar-light text-text-secondary hover:bg-gray-100 dark:hover:bg-gray-700 text-xs font-medium"
          >
            2
          </button>
          <button
            class="size-8 flex items-center justify-center rounded border border-border-light bg-white dark:bg-sidebar-light text-text-secondary hover:bg-gray-100 dark:hover:bg-gray-700 text-xs font-medium"
          >
            3
          </button>
          <button
            class="size-8 flex items-center justify-center rounded border border-border-light bg-white dark:bg-sidebar-light text-text-secondary hover:bg-gray-100 dark:hover:bg-gray-700"
          >
            <span class="material-symbols-outlined text-sm">chevron_right</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
