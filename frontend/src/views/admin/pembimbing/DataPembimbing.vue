<template>
  <div class="flex flex-col gap-6">
    <div class="flex flex-col gap-1">
      <h1 class="text-text-main text-3xl font-bold leading-tight">
        Penetapan Pembimbing
      </h1>
      <p class="text-text-secondary text-sm font-normal">
        Kelola pengajuan judul skripsi dan penetapan dosen pembimbing mahasiswa.
      </p>
    </div>

    <!-- Filter/Action Bar -->
    <div
      class="flex flex-col rounded-xl border border-border-light bg-surface-light dark:bg-surface-light overflow-hidden shadow-sm"
    >
      <div
        class="p-6 border-b border-border-light flex flex-wrap gap-4 justify-between items-center bg-gray-50/50 dark:bg-sidebar-light/50"
      >
        <div>
          <h3 class="text-text-main text-lg font-bold">
            Mahasiswa Baru (Baru Isi Judul)
          </h3>
          <p class="text-text-secondary text-xs">
            Menampilkan daftar mahasiswa yang menunggu penetapan pembimbing.
          </p>
        </div>
        <div class="flex gap-2">
          <button
            class="flex items-center gap-2 px-4 py-2 bg-sidebar-light border border-border-light rounded-lg text-xs font-bold text-text-secondary hover:bg-white transition-colors dark:bg-sidebar-light dark:hover:bg-sidebar-light/80"
          >
            <span class="material-symbols-outlined text-[16px]"
              >filter_list</span
            >
            Filter
          </button>
          <button
            class="flex items-center gap-2 px-4 py-2 bg-primary/10 text-primary rounded-lg text-xs font-bold hover:bg-primary/20 transition-colors"
          >
            <span class="material-symbols-outlined text-[16px]">refresh</span>
            Refresh
          </button>
        </div>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr
              class="bg-sidebar-light/50 border-b border-border-light dark:bg-sidebar-light/50"
            >
              <th
                class="p-4 pl-6 text-[10px] font-bold tracking-widest text-text-secondary uppercase w-1/4"
              >
                Mahasiswa
              </th>
              <th
                class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase w-1/2"
              >
                Judul Skripsi
              </th>
              <th
                class="p-4 pr-6 text-[10px] font-bold tracking-widest text-text-secondary uppercase text-right"
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
              <td class="p-4 pl-6 align-top">
                <div class="flex items-start gap-3">
                  <div
                    class="bg-gray-200 size-10 rounded-full bg-cover bg-center border border-border-light shadow-sm shrink-0"
                    style="
                      background-image: url(&quot;https://lh3.googleusercontent.com/aida-public/AB6AXuCXvmzpNr4yS293bcSUOqs1k0_0T3xBHHwPvluP2og08U-Pmekxd-lNCbHkg4l_ebu9pQp84cfHsau6-I-Loo86_6lN4k7FAYpMXo2M8gLPbEmwMhxkImXktkSZuWnkqjuWbqooz7ty-6u73H6Ww1mo00MhCdgeUOEIo9ciWJAM13shkp7T-5f66DTAYns5Co-T2rBySywlsV7JMpyqqLNvqISxOJQ8Z_kdUpsLms_CGCqTmOhzaZq9rjsu2GBl23JEAP9HMC838l60&quot;);
                    "
                  ></div>
                  <div>
                    <p class="font-bold text-text-main text-sm">Budi Santoso</p>
                    <p class="text-xs text-text-secondary font-medium">
                      201910234
                    </p>
                    <p class="text-[10px] text-text-secondary mt-1">
                      Teknik Informatika
                    </p>
                  </div>
                </div>
              </td>
              <td class="p-4 align-top">
                <div class="flex flex-col gap-2">
                  <p class="text-sm font-medium text-text-main leading-relaxed">
                    Rancang Bangun Sistem Informasi Manajemen Aset Berbasis Web
                    Menggunakan Framework Laravel pada PT. Maju Jaya
                  </p>
                  <div class="flex gap-2">
                    <span
                      class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-blue-50 text-blue-600 border border-blue-100 dark:bg-blue-500/20 dark:text-blue-400 dark:border-blue-500/30"
                      >Baru Isi Judul</span
                    >
                    <span
                      class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-gray-50 text-gray-600 border border-gray-200 dark:bg-gray-700/50 dark:text-gray-400 dark:border-gray-600"
                      >KBK: Rekayasa Perangkat Lunak</span
                    >
                  </div>
                </div>
              </td>
              <td class="p-4 pr-6 align-top text-right">
                <button
                  class="inline-flex items-center justify-center px-4 py-2 text-xs font-bold text-white bg-primary rounded-lg hover:bg-primary/90 transition-all shadow-sm shadow-primary/20"
                >
                  Tetapkan
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
        <p class="text-xs text-text-secondary">
          Showing <span class="font-bold text-text-main">1-4</span> of
          <span class="font-bold text-text-main">18</span> mahasiswa
        </p>
        <div class="flex gap-2">
          <button
            class="size-8 flex items-center justify-center rounded-md border border-border-light bg-white dark:bg-sidebar-light text-text-secondary hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors disabled:opacity-50"
            disabled
          >
            <span class="material-symbols-outlined text-[16px]"
              >chevron_left</span
            >
          </button>
          <button
            class="size-8 flex items-center justify-center rounded-md border border-border-light bg-white dark:bg-sidebar-light text-text-secondary hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
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
