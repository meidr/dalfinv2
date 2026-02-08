<template>
  <div class="flex flex-col gap-6">
    <div class="flex flex-col gap-1">
      <h1 class="text-text-main text-3xl font-bold leading-tight">
        Penerbitan Nota Bimbingan
      </h1>
      <p class="text-text-secondary text-sm font-normal">
        Daftar mahasiswa yang telah menyelesaikan bimbingan dan siap mengajukan
        ujian.
      </p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div
        class="flex flex-col gap-2 rounded-xl p-6 bg-surface-light border border-border-light shadow-sm hover:shadow-md transition-shadow dark:bg-surface-light"
      >
        <div class="flex items-center justify-between">
          <p
            class="text-text-secondary text-xs font-bold uppercase tracking-wider"
          >
            Siap Cetak
          </p>
          <div class="bg-primary/10 p-2 rounded-lg text-primary">
            <span class="material-symbols-outlined">print</span>
          </div>
        </div>
        <div class="mt-2">
          <p class="text-text-main text-3xl font-bold leading-tight">8</p>
          <div class="flex items-center gap-1 mt-1">
            <span class="material-symbols-outlined text-primary text-[18px]"
              >check_circle</span
            >
            <p class="text-primary text-xs font-semibold">Ready for issuance</p>
          </div>
        </div>
      </div>
      <div
        class="flex flex-col gap-2 rounded-xl p-6 bg-surface-light border border-border-light shadow-sm hover:shadow-md transition-shadow dark:bg-surface-light"
      >
        <div class="flex items-center justify-between">
          <p
            class="text-text-secondary text-xs font-bold uppercase tracking-wider"
          >
            Menunggu Upload
          </p>
          <div
            class="bg-orange-100 p-2 rounded-lg text-orange-600 dark:bg-orange-900/30 dark:text-orange-400"
          >
            <span class="material-symbols-outlined">upload_file</span>
          </div>
        </div>
        <div class="mt-2">
          <p class="text-text-main text-3xl font-bold leading-tight">12</p>
          <div class="flex items-center gap-1 mt-1">
            <span
              class="material-symbols-outlined text-orange-600 text-[18px] dark:text-orange-400"
              >pending</span
            >
            <p
              class="text-orange-600 text-xs font-semibold dark:text-orange-400"
            >
              Waiting for student action
            </p>
          </div>
        </div>
      </div>
      <div
        class="flex flex-col gap-2 rounded-xl p-6 bg-surface-light border border-border-light shadow-sm hover:shadow-md transition-shadow dark:bg-surface-light"
      >
        <div class="flex items-center justify-between">
          <p
            class="text-text-secondary text-xs font-bold uppercase tracking-wider"
          >
            Total Diterbitkan
          </p>
          <div
            class="bg-green-100 p-2 rounded-lg text-green-600 dark:bg-green-900/30 dark:text-green-400"
          >
            <span class="material-symbols-outlined">verified</span>
          </div>
        </div>
        <div class="mt-2">
          <p class="text-text-main text-3xl font-bold leading-tight">324</p>
          <div class="flex items-center gap-1 mt-1">
            <span
              class="material-symbols-outlined text-green-600 text-[18px] dark:text-green-400"
              >calendar_month</span
            >
            <p class="text-green-600 text-xs font-semibold dark:text-green-400">
              This semester
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Table Container -->
    <div
      class="flex flex-col rounded-xl border border-border-light bg-surface-light overflow-hidden shadow-sm dark:bg-surface-light"
    >
      <div
        class="p-6 border-b border-border-light flex flex-wrap gap-4 justify-between items-center bg-gray-50/50 dark:bg-sidebar-light/50"
      >
        <div>
          <h3 class="text-text-main text-lg font-bold">
            Mahasiswa Selesai Bimbingan
          </h3>
          <p class="text-text-secondary text-xs">
            Daftar mahasiswa yang telah mendapat persetujuan pembimbing untuk
            maju ujian.
          </p>
        </div>
        <div class="flex gap-2">
          <button
            class="flex items-center gap-2 text-text-secondary hover:text-primary text-sm font-bold bg-white hover:bg-sidebar-light px-4 py-2 rounded-lg transition-colors border border-border-light dark:bg-sidebar-light dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700"
          >
            <span class="material-symbols-outlined text-[18px]"
              >filter_list</span
            >
            Filter
          </button>
          <button
            class="flex items-center gap-2 text-white text-sm font-bold bg-primary hover:bg-primary/90 px-4 py-2 rounded-lg transition-colors shadow-sm shadow-primary/20"
          >
            <span class="material-symbols-outlined text-[18px]">download</span>
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
                class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase w-1/4"
              >
                Mahasiswa
              </th>
              <th
                class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase w-1/4"
              >
                Pembimbing
              </th>
              <th
                class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase"
              >
                Status Bimbingan
              </th>
              <th
                class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase"
              >
                Selesai Pada
              </th>
              <th
                class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase text-right"
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
              <td class="p-4">
                <div class="flex items-center gap-3">
                  <div
                    class="bg-gray-200 size-10 rounded-full bg-cover bg-center border border-border-light shadow-sm"
                    style="
                      background-image: url(&quot;https://lh3.googleusercontent.com/aida-public/AB6AXuCXvmzpNr4yS293bcSUOqs1k0_0T3xBHHwPvluP2og08U-Pmekxd-lNCbHkg4l_ebu9pQp84cfHsau6-I-Loo86_6lN4k7FAYpMXo2M8gLPbEmwMhxkImXktkSZuWnkqjuWbqooz7ty-6u73H6Ww1mo00MhCdgeUOEIo9ciWJAM13shkp7T-5f66DTAYns5Co-T2rBySywlsV7JMpyqqLNvqISxOJQ8Z_kdUpsLms_CGCqTmOhzaZq9rjsu2GBl23JEAP9HMC838l60&quot;);
                    "
                  ></div>
                  <div>
                    <p class="font-bold text-text-main text-sm">Budi Santoso</p>
                    <p class="text-xs text-text-secondary font-medium">
                      201910234
                    </p>
                    <p
                      class="text-[10px] text-text-secondary truncate max-w-[150px] mt-0.5"
                    >
                      Analisis Sistem Informasi Kepegawaian...
                    </p>
                  </div>
                </div>
              </td>
              <td class="p-4">
                <div class="flex flex-col">
                  <span class="text-sm font-medium text-text-main"
                    >Dr. Rina Wati, M.Kom</span
                  >
                  <span class="text-[10px] text-text-secondary"
                    >NIP. 198001012005012001</span
                  >
                </div>
              </td>
              <td class="p-4">
                <span
                  class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-green-50 text-green-600 border border-green-100 dark:bg-green-500/20 dark:text-green-400 dark:border-green-500/30"
                >
                  <span class="material-symbols-outlined text-[14px]"
                    >check_circle</span
                  >
                  Selesai
                </span>
              </td>
              <td class="p-4 text-xs font-medium text-text-secondary">
                12 Okt 2023
              </td>
              <td class="p-4 text-right">
                <div class="flex items-center justify-end gap-2">
                  <button
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 text-xs font-bold text-white bg-primary rounded-lg hover:bg-primary/90 transition-all shadow-sm shadow-primary/20"
                  >
                    <span class="material-symbols-outlined text-[16px]"
                      >print</span
                    >
                    Cetak Nota
                  </button>
                  <button
                    class="size-8 flex items-center justify-center rounded-lg border border-border-light text-text-secondary hover:text-primary hover:bg-white hover:border-primary transition-all dark:bg-sidebar-light dark:border-gray-600 dark:hover:bg-gray-700"
                    title="Lihat Detail"
                  >
                    <span class="material-symbols-outlined text-[18px]"
                      >visibility</span
                    >
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
