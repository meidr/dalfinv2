<template>
  <div class="flex flex-col gap-6">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
      <div class="flex flex-col gap-1">
        <div class="flex items-center gap-3">
          <span class="bg-primary/10 p-2 rounded-lg text-primary">
            <span class="material-symbols-outlined">cast_for_education</span>
          </span>
          <h1 class="text-text-main text-3xl font-bold leading-tight">
            Log Bimbingan
          </h1>
        </div>
        <p class="text-text-secondary text-sm font-normal pl-[52px]">
          Kelola data dan riwayat bimbingan skripsi mahasiswa aktif.
        </p>
      </div>
      <button
        class="flex items-center gap-2 bg-primary text-white px-5 py-2.5 rounded-lg hover:bg-blue-600 transition-all shadow-md shadow-primary/20 font-medium text-sm"
      >
        <span class="material-symbols-outlined text-[20px]">add</span>
        Tambah Log
      </button>
    </div>

    <div
      class="flex flex-col rounded-xl border border-border-light bg-surface-light overflow-hidden shadow-sm flex-1 mb-8 dark:bg-surface-light"
    >
      <div
        class="p-4 md:p-6 border-b border-border-light flex justify-between items-center bg-gray-50/50 dark:bg-sidebar-light/50"
      >
        <div class="flex items-center gap-2">
          <span class="text-sm font-bold text-text-main"
            >Daftar Mahasiswa Bimbingan</span
          >
          <span
            class="bg-primary/10 text-primary text-xs font-bold px-2 py-0.5 rounded-full"
            >4 Aktif</span
          >
        </div>
        <div class="flex gap-2">
          <button
            class="flex items-center gap-1 px-3 py-1.5 rounded-md border border-border-light bg-white text-xs font-medium text-text-secondary hover:text-primary hover:border-primary transition-colors dark:bg-sidebar-light dark:border-gray-600 dark:hover:bg-gray-700"
          >
            <span class="material-symbols-outlined text-[16px]"
              >filter_list</span
            >
            Filter
          </button>
          <button
            class="flex items-center gap-1 px-3 py-1.5 rounded-md border border-border-light bg-white text-xs font-medium text-text-secondary hover:text-primary hover:border-primary transition-colors dark:bg-sidebar-light dark:border-gray-600 dark:hover:bg-gray-700"
          >
            <span class="material-symbols-outlined text-[16px]">download</span>
            Export
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
                class="p-4 md:pl-6 text-[11px] font-bold tracking-widest text-text-secondary uppercase w-1/3"
              >
                Mahasiswa
              </th>
              <th
                class="p-4 text-[11px] font-bold tracking-widest text-text-secondary uppercase w-1/3"
              >
                Pembimbing
              </th>
              <th
                class="p-4 text-[11px] font-bold tracking-widest text-text-secondary uppercase w-1/6 text-center"
              >
                Total Bimbingan
              </th>
              <th
                class="p-4 md:pr-6 text-[11px] font-bold tracking-widest text-text-secondary uppercase w-1/6 text-right"
              >
                Aksi
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border-light">
            <tr
              v-for="i in 4"
              :key="i"
              class="group hover:bg-sidebar-light/30 transition-colors"
            >
              <td class="p-4 md:pl-6">
                <div class="flex items-center gap-4">
                  <div
                    class="bg-gray-200 size-10 rounded-full bg-cover bg-center border border-border-light shadow-sm shrink-0"
                    style="
                      background-image: url(&quot;https://lh3.googleusercontent.com/aida-public/AB6AXuCXvmzpNr4yS293bcSUOqs1k0_0T3xBHHwPvluP2og08U-Pmekxd-lNCbHkg4l_ebu9pQp84cfHsau6-I-Loo86_6lN4k7FAYpMXo2M8gLPbEmwMhxkImXktkSZuWnkqjuWbqooz7ty-6u73H6Ww1mo00MhCdgeUOEIo9ciWJAM13shkp7T-5f66DTAYns5Co-T2rBySywlsV7JMpyqqLNvqISxOJQ8Z_kdUpsLms_CGCqTmOhzaZq9rjsu2GBl23JEAP9HMC838l60&quot;);
                    "
                  ></div>
                  <div>
                    <p
                      class="font-bold text-text-main text-sm group-hover:text-primary transition-colors"
                    >
                      Budi Santoso
                    </p>
                    <p
                      class="text-xs text-text-secondary font-medium font-mono mt-0.5"
                    >
                      201910234
                    </p>
                    <p class="text-[10px] text-text-secondary mt-1">
                      Teknik Informatika
                    </p>
                  </div>
                </div>
              </td>
              <td class="p-4">
                <div class="flex items-center gap-3">
                  <div
                    class="bg-blue-100 flex items-center justify-center size-9 rounded-full text-primary border border-blue-200 shrink-0 dark:bg-blue-900/30 dark:border-blue-800 dark:text-blue-300"
                  >
                    <span class="material-symbols-outlined text-[18px]"
                      >person</span
                    >
                  </div>
                  <div>
                    <p class="font-semibold text-text-main text-sm">
                      Dr. Bambang Widjoyo
                    </p>
                    <div class="flex items-center gap-2 mt-0.5">
                      <span
                        class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-600 border border-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600"
                      >
                        Pembimbing 1
                      </span>
                    </div>
                  </div>
                </div>
              </td>
              <td class="p-4 text-center">
                <div class="inline-flex flex-col items-center justify-center">
                  <span class="text-xl font-bold text-text-main">8</span>
                  <span
                    class="text-[10px] text-text-secondary uppercase tracking-wider font-semibold"
                    >Kali</span
                  >
                </div>
              </td>
              <td class="p-4 md:pr-6 text-right">
                <div class="flex justify-end gap-1">
                  <button
                    class="inline-flex items-center justify-center p-2 text-text-secondary hover:text-primary hover:bg-primary/5 rounded-lg transition-all"
                    title="Upload Scan Offline"
                  >
                    <span class="material-symbols-outlined text-[20px]"
                      >cloud_upload</span
                    >
                  </button>
                  <button
                    class="inline-flex items-center justify-center p-2 text-text-secondary hover:text-primary hover:bg-primary/5 rounded-lg transition-all"
                    title="Lihat Detail"
                  >
                    <span class="material-symbols-outlined text-[20px]"
                      >visibility</span
                    >
                  </button>
                  <button
                    class="inline-flex items-center justify-center p-2 text-text-secondary hover:text-primary hover:bg-primary/5 rounded-lg transition-all"
                    title="Edit Log"
                  >
                    <span class="material-symbols-outlined text-[20px]"
                      >edit_note</span
                    >
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div
        class="p-4 border-t border-border-light flex items-center justify-between bg-gray-50/50 dark:bg-sidebar-light/50"
      >
        <span class="text-xs text-text-secondary"
          >Menampilkan 5 dari 24 mahasiswa</span
        >
        <div class="flex gap-1">
          <button
            class="size-8 flex items-center justify-center rounded-lg border border-border-light text-text-secondary hover:bg-sidebar-light transition-colors disabled:opacity-50"
            disabled
          >
            <span class="material-symbols-outlined text-[18px]"
              >chevron_left</span
            >
          </button>
          <button
            class="size-8 flex items-center justify-center rounded-lg bg-primary text-white font-bold text-xs shadow-sm shadow-primary/20"
          >
            1
          </button>
          <button
            class="size-8 flex items-center justify-center rounded-lg border border-border-light text-text-secondary hover:bg-sidebar-light transition-colors text-xs dark:hover:bg-gray-700"
          >
            2
          </button>
          <button
            class="size-8 flex items-center justify-center rounded-lg border border-border-light text-text-secondary hover:bg-sidebar-light transition-colors text-xs dark:hover:bg-gray-700"
          >
            3
          </button>
          <button
            class="size-8 flex items-center justify-center rounded-lg border border-border-light text-text-secondary hover:bg-sidebar-light transition-colors dark:hover:bg-gray-700"
          >
            <span class="material-symbols-outlined text-[18px]"
              >chevron_right</span
            >
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
