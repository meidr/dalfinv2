<template>
  <div class="flex flex-col gap-6">
    <div class="flex flex-col gap-1">
      <h1 class="text-text-main text-3xl font-bold leading-tight">
        Manajemen SK Tugas
      </h1>
      <p class="text-text-secondary text-sm font-normal">
        Daftar pengajuan SK Tugas Akhir mahasiswa dengan status Sudah Isi
        Dospem.
      </p>
    </div>

    <div
      class="w-full bg-surface-light border border-border-light rounded-xl shadow-sm flex flex-col overflow-hidden dark:bg-surface-light"
    >
      <div
        class="p-5 border-b border-border-light flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-gray-50/50 dark:bg-sidebar-light/50"
      >
        <div
          class="flex p-1 bg-sidebar-light rounded-lg w-fit dark:bg-sidebar-light/50"
        >
          <button
            class="px-4 py-1.5 bg-white text-primary text-xs font-bold rounded shadow-sm transition-all border border-border-light/50 dark:bg-surface-light dark:border-gray-600"
          >
            Sudah Isi Dospem
          </button>
          <button
            class="px-4 py-1.5 text-text-secondary hover:text-text-main hover:bg-white/50 text-xs font-medium rounded transition-all dark:hover:bg-gray-700"
          >
            Menunggu TTD
          </button>
          <button
            class="px-4 py-1.5 text-text-secondary hover:text-text-main hover:bg-white/50 text-xs font-medium rounded transition-all dark:hover:bg-gray-700"
          >
            Selesai
          </button>
        </div>
        <div class="flex items-center gap-3 w-full lg:w-auto">
          <button
            class="flex items-center justify-center gap-2 px-3 py-2 border border-border-light bg-white hover:bg-gray-50 text-text-main text-xs font-bold rounded-lg transition-colors dark:bg-sidebar-light dark:border-gray-600 dark:hover:bg-gray-700 dark:text-gray-200"
          >
            <span class="material-symbols-outlined text-[18px]"
              >filter_list</span
            >
            Filter
          </button>
          <button
            class="flex items-center justify-center gap-2 px-3 py-2 border border-border-light bg-white hover:bg-gray-50 text-text-main text-xs font-bold rounded-lg transition-colors dark:bg-sidebar-light dark:border-gray-600 dark:hover:bg-gray-700 dark:text-gray-200"
          >
            <span class="material-symbols-outlined text-[18px]">download</span>
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
                class="p-4 text-[11px] font-bold tracking-widest text-text-secondary uppercase w-1/4"
              >
                Mahasiswa
              </th>
              <th
                class="p-4 text-[11px] font-bold tracking-widest text-text-secondary uppercase w-1/4"
              >
                Judul Skripsi
              </th>
              <th
                class="p-4 text-[11px] font-bold tracking-widest text-text-secondary uppercase w-1/5"
              >
                Pembimbing Usulan
              </th>
              <th
                class="p-4 text-[11px] font-bold tracking-widest text-text-secondary uppercase text-right w-1/4"
              >
                Aksi
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border-light">
            <tr
              v-for="i in 3"
              :key="i"
              class="group hover:bg-blue-50/20 transition-colors dark:hover:bg-blue-900/10"
            >
              <td class="p-4 align-top">
                <div class="flex items-start gap-3">
                  <div
                    class="bg-gray-200 size-10 rounded-full bg-cover bg-center shadow-sm border border-border-light"
                    style="
                      background-image: url(&quot;https://lh3.googleusercontent.com/aida-public/AB6AXuCXvmzpNr4yS293bcSUOqs1k0_0T3xBHHwPvluP2og08U-Pmekxd-lNCbHkg4l_ebu9pQp84cfHsau6-I-Loo86_6lN4k7FAYpMXo2M8gLPbEmwMhxkImXktkSZuWnkqjuWbqooz7ty-6u73H6Ww1mo00MhCdgeUOEIo9ciWJAM13shkp7T-5f66DTAYns5Co-T2rBySywlsV7JMpyqqLNvqISxOJQ8Z_kdUpsLms_CGCqTmOhzaZq9rjsu2GBl23JEAP9HMC838l60&quot;);
                    "
                  ></div>
                  <div>
                    <p class="font-bold text-text-main text-sm">Budi Santoso</p>
                    <p class="text-xs text-text-secondary font-medium">
                      201910234
                    </p>
                    <span
                      class="inline-block mt-1 text-[10px] font-semibold text-text-secondary bg-gray-100 px-1.5 py-0.5 rounded border border-gray-200 dark:bg-gray-700 dark:border-gray-600"
                      >Teknik Informatika</span
                    >
                  </div>
                </div>
              </td>
              <td class="p-4 align-top">
                <p
                  class="text-xs font-medium text-text-main leading-relaxed line-clamp-2"
                >
                  Rancang Bangun Sistem Informasi Manajemen Skripsi Berbasis Web
                  pada Universitas XYZ
                </p>
              </td>
              <td class="p-4 align-top">
                <div class="flex flex-col gap-2">
                  <div class="flex items-center gap-2">
                    <span
                      class="size-5 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-[10px] font-bold dark:bg-blue-900 dark:text-blue-300"
                      >1</span
                    >
                    <span class="text-xs text-text-main"
                      >Dr. Hendra, M.Kom</span
                    >
                  </div>
                  <div class="flex items-center gap-2">
                    <span
                      class="size-5 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center text-[10px] font-bold dark:bg-gray-700 dark:text-gray-300"
                      >2</span
                    >
                    <span class="text-xs text-text-main">Siska Putri, M.T</span>
                  </div>
                </div>
              </td>
              <td class="p-4 align-top text-right">
                <div
                  class="flex flex-col sm:flex-row gap-2 justify-end items-center"
                >
                  <button
                    class="flex items-center justify-center gap-2 px-3 py-1.5 text-xs font-bold text-text-main bg-white border border-border-light rounded-lg hover:bg-gray-50 hover:border-gray-300 transition-all shadow-sm w-full sm:w-auto dark:bg-sidebar-light dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700"
                  >
                    <span class="material-symbols-outlined text-[16px]"
                      >print</span
                    >
                    Generate SK
                  </button>
                  <button
                    class="flex items-center justify-center gap-2 px-3 py-1.5 text-xs font-bold text-white bg-primary rounded-lg hover:bg-primary/90 transition-all shadow-sm shadow-primary/20 w-full sm:w-auto"
                  >
                    <span class="material-symbols-outlined text-[16px]"
                      >upload_file</span
                    >
                    Upload
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
          >Showing 1 to 4 of 12 entries</span
        >
        <div class="flex gap-1">
          <button
            class="size-8 flex items-center justify-center rounded border border-border-light hover:bg-gray-50 dark:hover:bg-gray-700 text-text-secondary transition-colors"
          >
            <span class="material-symbols-outlined text-[16px]"
              >chevron_left</span
            >
          </button>
          <button
            class="size-8 flex items-center justify-center rounded bg-primary text-white text-xs font-bold shadow-sm shadow-primary/20"
          >
            1
          </button>
          <button
            class="size-8 flex items-center justify-center rounded border border-border-light hover:bg-gray-50 dark:hover:bg-gray-700 text-text-secondary text-xs transition-colors"
          >
            2
          </button>
          <button
            class="size-8 flex items-center justify-center rounded border border-border-light hover:bg-gray-50 dark:hover:bg-gray-700 text-text-secondary text-xs transition-colors"
          >
            3
          </button>
          <button
            class="size-8 flex items-center justify-center rounded border border-border-light hover:bg-gray-50 dark:hover:bg-gray-700 text-text-secondary transition-colors"
          >
            <span class="material-symbols-outlined text-[16px]"
              >chevron_right</span
            >
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
