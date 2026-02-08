<template>
  <div class="flex flex-col gap-6">
    <div
      class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4"
    >
      <div class="flex flex-col gap-1">
        <h1 class="text-text-main text-3xl font-bold leading-tight">
          Berita Acara Ujian
        </h1>
        <p class="text-text-secondary text-sm font-normal">
          Daftar mahasiswa yang telah menyelesaikan ujian dan siap untuk dicetak
          berita acara.
        </p>
      </div>
      <div class="flex items-center gap-3">
        <button
          class="hidden md:flex items-center gap-2 px-4 py-2 bg-white border border-border-light rounded-lg text-text-secondary text-sm font-medium hover:bg-gray-50 transition-colors shadow-sm dark:bg-sidebar-light dark:border-gray-600 dark:hover:bg-gray-700 dark:text-gray-200"
        >
          <span class="material-symbols-outlined text-[20px]"
            >calendar_today</span
          >
          Filter Periode
        </button>
        <button
          class="flex items-center gap-2 px-4 py-2 bg-white text-primary border border-primary/20 rounded-lg text-sm font-bold hover:bg-primary/5 transition-colors shadow-sm dark:bg-sidebar-light dark:border-gray-600 dark:hover:bg-gray-700"
        >
          <span class="material-symbols-outlined text-[20px]">download</span>
          Rekap Excel
        </button>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div
        class="flex items-center p-5 bg-surface-light border border-border-light rounded-xl shadow-sm hover:shadow-md transition-shadow dark:bg-surface-light"
      >
        <div
          class="p-3 bg-blue-50 text-blue-600 rounded-lg mr-4 dark:bg-blue-900/30 dark:text-blue-400"
        >
          <span class="material-symbols-outlined">pending_actions</span>
        </div>
        <div>
          <p
            class="text-text-secondary text-xs font-bold uppercase tracking-wider mb-1"
          >
            Siap Generate
          </p>
          <h3 class="text-2xl font-bold text-text-main">8</h3>
          <p class="text-xs text-text-secondary mt-1">Mahasiswa Lulus Ujian</p>
        </div>
      </div>
      <div
        class="flex items-center p-5 bg-surface-light border border-border-light rounded-xl shadow-sm hover:shadow-md transition-shadow dark:bg-surface-light"
      >
        <div
          class="p-3 bg-green-50 text-green-600 rounded-lg mr-4 dark:bg-green-900/30 dark:text-green-400"
        >
          <span class="material-symbols-outlined">print</span>
        </div>
        <div>
          <p
            class="text-text-secondary text-xs font-bold uppercase tracking-wider mb-1"
          >
            Sudah Dicetak
          </p>
          <h3 class="text-2xl font-bold text-text-main">142</h3>
          <p class="text-xs text-text-secondary mt-1">Dokumen Fisik Tersedia</p>
        </div>
      </div>
      <div
        class="flex items-center p-5 bg-surface-light border border-border-light rounded-xl shadow-sm hover:shadow-md transition-shadow dark:bg-surface-light"
      >
        <div
          class="p-3 bg-orange-50 text-orange-600 rounded-lg mr-4 dark:bg-orange-900/30 dark:text-orange-400"
        >
          <span class="material-symbols-outlined">history_edu</span>
        </div>
        <div>
          <p
            class="text-text-secondary text-xs font-bold uppercase tracking-wider mb-1"
          >
            Total Ujian
          </p>
          <h3 class="text-2xl font-bold text-text-main">150</h3>
          <p class="text-xs text-text-secondary mt-1">Periode Semester Ini</p>
        </div>
      </div>
    </div>

    <!-- Table Container -->
    <div
      class="flex flex-col rounded-xl border border-border-light bg-surface-light overflow-hidden shadow-sm dark:bg-surface-light"
    >
      <div
        class="p-5 border-b border-border-light flex flex-col md:flex-row justify-between items-center gap-4 bg-gray-50/50 dark:bg-sidebar-light/50"
      >
        <div class="relative w-full md:w-80">
          <span
            class="material-symbols-outlined absolute left-3 top-2.5 text-text-secondary text-[20px]"
            >search</span
          >
          <input
            class="w-full pl-10 pr-4 py-2 rounded-lg border border-border-light text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-text-main placeholder:text-text-secondary bg-white dark:bg-sidebar-light dark:border-gray-600"
            placeholder="Cari Mahasiswa atau NIM..."
            type="text"
          />
        </div>
        <div
          class="flex items-center gap-2 w-full md:w-auto overflow-x-auto pb-1 md:pb-0"
        >
          <span
            class="text-xs font-bold text-text-secondary uppercase tracking-wide mr-2 shrink-0"
            >Status:</span
          >
          <button
            class="px-4 py-1.5 rounded-full text-xs font-bold bg-primary text-white shadow-sm shadow-primary/20 shrink-0"
          >
            Semua
          </button>
          <button
            class="px-4 py-1.5 rounded-full text-xs font-bold bg-sidebar-light text-text-secondary hover:bg-gray-100 border border-border-light shrink-0 transition-colors dark:bg-sidebar-light dark:border-gray-600 dark:hover:bg-gray-700"
          >
            Belum Dicetak
          </button>
          <button
            class="px-4 py-1.5 rounded-full text-xs font-bold bg-sidebar-light text-text-secondary hover:bg-gray-100 border border-border-light shrink-0 transition-colors dark:bg-sidebar-light dark:border-gray-600 dark:hover:bg-gray-700"
          >
            Selesai
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
                class="p-4 pl-6 text-[10px] font-bold tracking-widest text-text-secondary uppercase"
              >
                Mahasiswa
              </th>
              <th
                class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase w-1/3"
              >
                Judul Skripsi
              </th>
              <th
                class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase whitespace-nowrap"
              >
                Tanggal Ujian
              </th>
              <th
                class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase"
              >
                Hasil
              </th>
              <th
                class="p-4 pr-6 text-[10px] font-bold tracking-widest text-text-secondary uppercase text-right"
              >
                Aksi
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border-light">
            <!-- Row 1 -->
            <tr class="group hover:bg-sidebar-light/30 transition-colors">
              <td class="p-4 pl-6">
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
                  </div>
                </div>
              </td>
              <td class="p-4">
                <p
                  class="text-sm text-text-main font-medium line-clamp-2 leading-snug"
                >
                  Implementasi Deep Learning untuk Deteksi Penyakit Tanaman Padi
                  Menggunakan CNN
                </p>
              </td>
              <td class="p-4 whitespace-nowrap">
                <div class="flex flex-col">
                  <span class="text-sm font-bold text-text-main"
                    >12 Okt 2023</span
                  >
                  <span class="text-xs text-text-secondary">09:00 - 11:00</span>
                </div>
              </td>
              <td class="p-4">
                <span
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-green-50 text-green-700 border border-green-100 dark:bg-green-500/20 dark:text-green-400 dark:border-green-500/30"
                >
                  Lulus
                </span>
              </td>
              <td class="p-4 pr-6 text-right">
                <button
                  class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-white bg-primary rounded-lg hover:bg-primary/90 transition-all shadow-md shadow-primary/20 hover:shadow-lg"
                >
                  <span class="material-symbols-outlined text-[16px]"
                    >description</span
                  >
                  Generate BA
                </button>
              </td>
            </tr>
            <!-- Row 2 -->
            <tr class="group hover:bg-sidebar-light/30 transition-colors">
              <td class="p-4 pl-6">
                <div class="flex items-center gap-3">
                  <div
                    class="bg-gray-200 size-10 rounded-full bg-cover bg-center border border-border-light shadow-sm"
                    style="
                      background-image: url(&quot;https://lh3.googleusercontent.com/aida-public/AB6AXuCzRZ4qfdCRtOwpchjjp-oRKQI4Rcgkv08vx0OHa00hvnRk3qNENQwuZjOW9rqhiXEpbfOrruMMP-phbXyM_xr_HR2g4235Zzj6x-4SX_gQKrcKkbn8hdhBjGL5DaxRhHD38VC6LViR0X3_HBw7iI2Hs_WqUkj7if0NslGJWsosBcW0Q_gowYR_8noaFgX7Yrn_g8-tgkOaZ5sWmQmPy4ZROvec6WewHl77MQfKlJpClmm5Kr5d39bNgT1TdhZY-Lua_zkjNOLKJUUp&quot;);
                    "
                  ></div>
                  <div>
                    <p class="font-bold text-text-main text-sm">Ahmad Rizky</p>
                    <p class="text-xs text-text-secondary font-medium">
                      201910301
                    </p>
                  </div>
                </div>
              </td>
              <td class="p-4">
                <p
                  class="text-sm text-text-main font-medium line-clamp-2 leading-snug"
                >
                  Rancang Bangun Sistem Informasi Geografis Pemetaan Daerah
                  Rawan Banjir
                </p>
              </td>
              <td class="p-4 whitespace-nowrap">
                <div class="flex flex-col">
                  <span class="text-sm font-bold text-text-main"
                    >11 Okt 2023</span
                  >
                  <span class="text-xs text-text-secondary">08:00 - 10:00</span>
                </div>
              </td>
              <td class="p-4">
                <span
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-yellow-50 text-yellow-700 border border-yellow-100 dark:bg-yellow-500/20 dark:text-yellow-400 dark:border-yellow-500/30"
                >
                  Revisi Mayor
                </span>
              </td>
              <td class="p-4 pr-6 text-right">
                <button
                  class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-text-secondary bg-gray-100 rounded-lg cursor-not-allowed opacity-70 dark:bg-gray-700"
                  disabled
                >
                  <span class="material-symbols-outlined text-[16px]"
                    >lock</span
                  >
                  Menunggu Revisi
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div
        class="p-4 border-t border-border-light flex justify-between items-center bg-gray-50/50 dark:bg-sidebar-light/50"
      >
        <p class="text-xs text-text-secondary font-medium">
          Menampilkan 1-4 dari 8 data
        </p>
        <div class="flex gap-2">
          <button
            class="p-1.5 rounded-lg border border-border-light bg-white hover:bg-gray-50 hover:shadow-sm disabled:opacity-50 text-text-secondary transition-all dark:bg-sidebar-light dark:border-gray-600 dark:hover:bg-gray-700"
            disabled
          >
            <span class="material-symbols-outlined text-[18px]"
              >chevron_left</span
            >
          </button>
          <button
            class="p-1.5 rounded-lg border border-border-light bg-white hover:bg-gray-50 hover:shadow-sm text-text-secondary transition-all dark:bg-sidebar-light dark:border-gray-600 dark:hover:bg-gray-700"
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
