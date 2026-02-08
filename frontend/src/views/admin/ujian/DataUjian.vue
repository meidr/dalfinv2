<template>
  <div class="flex flex-col gap-6">
    <div class="flex flex-col gap-1">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-text-main text-3xl font-bold leading-tight">
            Ujian Skripsi
          </h1>
          <p class="text-text-secondary text-sm font-normal">
            Daftar mahasiswa yang siap melaksanakan ujian akhir skripsi.
          </p>
        </div>
        <div class="flex gap-3">
          <button
            class="flex items-center gap-2 px-4 py-2 bg-white border border-border-light rounded-lg text-text-secondary hover:text-primary hover:border-primary transition-all text-sm font-medium shadow-sm dark:bg-sidebar-light dark:border-gray-600 dark:hover:border-primary"
          >
            <span class="material-symbols-outlined text-[18px]"
              >filter_list</span
            >
            Filter
          </button>
          <button
            class="flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-600 transition-all text-sm font-bold shadow-md shadow-primary/20"
          >
            <span class="material-symbols-outlined text-[18px]">add</span>
            Jadwalkan Baru
          </button>
        </div>
      </div>
    </div>

    <!-- Table Container -->
    <div
      class="flex flex-col rounded-xl border border-border-light bg-surface-light dark:bg-surface-light overflow-hidden shadow-sm"
    >
      <div
        class="p-6 border-b border-border-light flex flex-wrap gap-4 justify-between items-center bg-gray-50/50 dark:bg-sidebar-light/50"
      >
        <div class="flex gap-4 items-center">
          <h3 class="text-text-main text-lg font-bold">Jadwal Ujian Aktif</h3>
          <div class="h-4 w-px bg-gray-300 dark:bg-gray-600"></div>
          <span
            class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-600 text-xs font-bold border border-blue-100 dark:bg-blue-500/20 dark:text-blue-400 dark:border-blue-500/30"
            >Semester Ganjil 2023/2024</span
          >
        </div>
        <div class="flex items-center gap-2">
          <span class="text-xs text-text-secondary"
            >Showing 4 of 12 students</span
          >
          <div class="flex gap-1">
            <button
              class="size-8 flex items-center justify-center rounded border border-border-light hover:bg-gray-50 dark:hover:bg-gray-700 text-text-secondary disabled:opacity-50"
            >
              <span class="material-symbols-outlined text-sm"
                >chevron_left</span
              >
            </button>
            <button
              class="size-8 flex items-center justify-center rounded border border-border-light hover:bg-gray-50 dark:hover:bg-gray-700 text-text-secondary"
            >
              <span class="material-symbols-outlined text-sm"
                >chevron_right</span
              >
            </button>
          </div>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr
              class="bg-sidebar-light/50 border-b border-border-light dark:bg-sidebar-light/50"
            >
              <th
                class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase w-[25%]"
              >
                Mahasiswa
              </th>
              <th
                class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase w-[25%]"
              >
                Judul Skripsi
              </th>
              <th
                class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase w-[20%]"
              >
                Jadwal &amp; Ruang
              </th>
              <th
                class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase w-[15%]"
              >
                Status Penilaian
              </th>
              <th
                class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase w-[15%] text-right"
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
              <td class="p-4 align-top">
                <div class="flex items-center gap-3">
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
                    <div
                      class="mt-1 flex items-center gap-1 text-[10px] text-text-secondary"
                    >
                      <span class="material-symbols-outlined text-[12px]"
                        >school</span
                      >
                      <span>Teknik Informatika</span>
                    </div>
                  </div>
                </div>
              </td>
              <td class="p-4 align-top">
                <p
                  class="text-sm font-medium text-text-main line-clamp-2 leading-snug"
                >
                  Implementasi Deep Learning untuk Deteksi Penyakit Tanaman Padi
                  Menggunakan CNN
                </p>
                <div class="mt-2 flex flex-col gap-1">
                  <p class="text-[10px] text-text-secondary">
                    <span class="font-bold">Pembimbing:</span> Dr. Rina Kusuma,
                    M.Kom
                  </p>
                  <p class="text-[10px] text-text-secondary">
                    <span class="font-bold">Ketua Penguji:</span> Prof. Bambang
                    S.
                  </p>
                </div>
              </td>
              <td class="p-4 align-top">
                <div class="flex flex-col gap-1">
                  <div
                    class="flex items-center gap-2 text-text-main text-xs font-bold"
                  >
                    <span
                      class="material-symbols-outlined text-[16px] text-primary"
                      >calendar_month</span
                    >
                    15 Okt 2023
                  </div>
                  <div
                    class="flex items-center gap-2 text-text-secondary text-xs"
                  >
                    <span class="material-symbols-outlined text-[16px]"
                      >schedule</span
                    >
                    08:00 - 10:00 WIB
                  </div>
                  <div
                    class="flex items-center gap-2 text-text-secondary text-xs mt-1"
                  >
                    <span class="material-symbols-outlined text-[16px]"
                      >meeting_room</span
                    >
                    R. Sidang 1 (Gedung A)
                  </div>
                </div>
              </td>
              <td class="p-4 align-top">
                <span
                  class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-orange-50 text-orange-600 border border-orange-100 dark:bg-orange-500/20 dark:text-orange-400 dark:border-orange-500/30"
                >
                  <span
                    class="size-1.5 rounded-full bg-orange-600 dark:bg-orange-400"
                  ></span>
                  Menunggu Nilai
                </span>
              </td>
              <td class="p-4 text-right align-middle">
                <button
                  class="inline-flex items-center justify-center px-4 py-2 text-xs font-bold text-white bg-primary rounded-lg hover:bg-primary/90 transition-all shadow-sm w-full md:w-auto"
                >
                  Input Nilai
                </button>
                <button
                  class="mt-2 text-[10px] text-text-secondary hover:text-primary font-medium block w-full text-right"
                >
                  Lihat Berkas
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Footer -->
      <div
        class="p-4 border-t border-border-light bg-gray-50 dark:bg-sidebar-light flex justify-center"
      >
        <button class="text-xs font-bold text-primary hover:underline">
          View All Schedule
        </button>
      </div>
    </div>
  </div>
</template>
