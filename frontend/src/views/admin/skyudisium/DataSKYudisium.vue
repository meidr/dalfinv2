<template>
  <div class="flex flex-col gap-6">
    <div class="flex flex-col gap-1">
      <h1 class="text-text-main text-3xl font-bold leading-tight">
        Manajemen SK Yudisium
      </h1>
      <p class="text-text-secondary text-sm font-normal">
        Verifikasi dan proses mahasiswa yang telah menyelesaikan seluruh tahapan
        skripsi.
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
            Siap Yudisium
          </p>
          <div
            class="bg-green-100 p-2 rounded-lg text-green-600 dark:bg-green-900/30 dark:text-green-400"
          >
            <span class="material-symbols-outlined">how_to_reg</span>
          </div>
        </div>
        <div class="mt-2">
          <p class="text-text-main text-3xl font-bold leading-tight">18</p>
          <div class="flex items-center gap-1 mt-1">
            <span
              class="material-symbols-outlined text-green-600 text-[18px] dark:text-green-400"
              >check_circle</span
            >
            <p class="text-green-600 text-xs font-semibold dark:text-green-400">
              Ready to process
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
            SK Terbit (Bulan Ini)
          </p>
          <div class="bg-primary/10 p-2 rounded-lg text-primary">
            <span class="material-symbols-outlined">print</span>
          </div>
        </div>
        <div class="mt-2">
          <p class="text-text-main text-3xl font-bold leading-tight">42</p>
          <div class="flex items-center gap-1 mt-1">
            <span
              class="material-symbols-outlined text-text-secondary text-[18px]"
              >calendar_month</span
            >
            <p class="text-text-secondary text-xs font-semibold">
              Last 30 days
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
            Total Lulusan 2023
          </p>
          <div
            class="bg-purple-100 p-2 rounded-lg text-purple-600 dark:bg-purple-900/30 dark:text-purple-400"
          >
            <span class="material-symbols-outlined">school</span>
          </div>
        </div>
        <div class="mt-2">
          <p class="text-text-main text-3xl font-bold leading-tight">564</p>
          <div class="flex items-center gap-1 mt-1">
            <span
              class="material-symbols-outlined text-purple-600 text-[18px] dark:text-purple-400"
              >trending_up</span
            >
            <p
              class="text-purple-600 text-xs font-semibold dark:text-purple-400"
            >
              +8% increase
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
            Daftar Mahasiswa Siap Yudisium
          </h3>
          <p class="text-text-secondary text-xs">
            Mahasiswa dengan status 'Sudah Terbit Berita Acara'.
          </p>
        </div>
        <div class="flex gap-2">
          <button
            class="flex items-center gap-2 text-text-secondary hover:text-primary text-sm font-semibold px-4 py-2 rounded-lg transition-colors border border-border-light hover:border-primary/50 hover:bg-white dark:bg-sidebar-light dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700"
          >
            <span class="material-symbols-outlined text-[18px]"
              >filter_list</span
            >
            Filter
          </button>
          <button
            class="flex items-center gap-2 text-white bg-primary hover:bg-primary/90 text-sm font-semibold px-4 py-2 rounded-lg transition-colors shadow-sm shadow-primary/20"
          >
            <span class="material-symbols-outlined text-[18px]"
              >file_download</span
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
                class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase w-1/4"
              >
                Mahasiswa
              </th>
              <th
                class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase w-1/3"
              >
                Judul Skripsi
              </th>
              <th
                class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase"
              >
                Tanggal Ujian
              </th>
              <th
                class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase"
              >
                Status
              </th>
              <th
                class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase text-right"
              >
                Aksi
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border-light">
            <tr class="group hover:bg-sidebar-light/30 transition-colors">
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
                  </div>
                </div>
              </td>
              <td class="p-4">
                <p
                  class="text-sm text-text-main font-medium line-clamp-2"
                  title="Analisis Sentimen Pengguna Aplikasi E-Commerce Menggunakan Metode Naive Bayes"
                >
                  Analisis Sentimen Pengguna Aplikasi E-Commerce Menggunakan
                  Metode Naive Bayes
                </p>
              </td>
              <td class="p-4 text-xs font-medium text-text-secondary">
                15 Okt 2023
              </td>
              <td class="p-4">
                <span
                  class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-green-50 text-green-700 border border-green-200 dark:bg-green-500/20 dark:text-green-400 dark:border-green-500/30"
                >
                  <span
                    class="size-1.5 rounded-full bg-green-600 dark:bg-green-400"
                  ></span>
                  Sudah Terbit Berita Acara
                </span>
              </td>
              <td class="p-4 text-right">
                <button
                  class="inline-flex items-center justify-center px-4 py-2 text-xs font-bold text-white bg-primary rounded-lg hover:bg-primary/90 transition-all shadow-sm"
                >
                  Proses Yudisium
                </button>
              </td>
            </tr>
            <tr class="group hover:bg-sidebar-light/30 transition-colors">
              <td class="p-4">
                <div class="flex items-center gap-3">
                  <div
                    class="bg-gray-200 size-10 rounded-full bg-cover bg-center border border-border-light shadow-sm"
                    style="
                      background-image: url(&quot;https://lh3.googleusercontent.com/aida-public/AB6AXuDGtEzfJbqFw60krz6Rx-r9nY2eGR03hTU-JrE4a9UUCRSVRSIoqGGg9ZXSb-StEeDNGhLxU8KtR4mweFY8Q7hjyqvioiT8DQcg04eUjXkVjEOSS1iUMSWjTEXfnzoRTlGLItklfOGPbOxxJYCc0ue2fH_Ei6Cv0K8-9MpF6rk9zWPzsOxHZAhMlIkwjyYDzLFklK6THvb9PX22L8Cs5hq5TcdL-NVDoJNaqCX4xEmnOJJKx4GrNWlmXNXFGrwyr_shcZuEQtxq7b94&quot;);
                    "
                  ></div>
                  <div>
                    <p class="font-bold text-text-main text-sm">Siti Aminah</p>
                    <p class="text-xs text-text-secondary font-medium">
                      201910255
                    </p>
                  </div>
                </div>
              </td>
              <td class="p-4">
                <p
                  class="text-sm text-text-main font-medium line-clamp-2"
                  title="Rancang Bangun Sistem Informasi Manajemen Aset Desa Berbasis Web"
                >
                  Rancang Bangun Sistem Informasi Manajemen Aset Desa Berbasis
                  Web
                </p>
              </td>
              <td class="p-4 text-xs font-medium text-text-secondary">
                14 Okt 2023
              </td>
              <td class="p-4">
                <span
                  class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-green-50 text-green-700 border border-green-200 dark:bg-green-500/20 dark:text-green-400 dark:border-green-500/30"
                >
                  <span
                    class="size-1.5 rounded-full bg-green-600 dark:bg-green-400"
                  ></span>
                  Sudah Terbit Berita Acara
                </span>
              </td>
              <td class="p-4 text-right">
                <button
                  class="inline-flex items-center justify-center px-4 py-2 text-xs font-bold text-white bg-primary rounded-lg hover:bg-primary/90 transition-all shadow-sm"
                >
                  Proses Yudisium
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div
        class="p-4 border-t border-border-light flex items-center justify-between bg-gray-50/50 dark:bg-sidebar-light/50"
      >
        <p class="text-xs text-text-secondary">
          Menampilkan 5 dari 18 mahasiswa
        </p>
        <div class="flex gap-2">
          <button
            class="size-8 flex items-center justify-center rounded-lg border border-border-light text-text-secondary hover:bg-sidebar-light disabled:opacity-50 dark:border-gray-600 dark:hover:bg-gray-700"
          >
            <span class="material-symbols-outlined text-sm">chevron_left</span>
          </button>
          <button
            class="size-8 flex items-center justify-center rounded-lg border border-primary bg-primary text-white text-xs font-bold"
          >
            1
          </button>
          <button
            class="size-8 flex items-center justify-center rounded-lg border border-border-light text-text-secondary hover:bg-sidebar-light text-xs font-bold dark:border-gray-600 dark:hover:bg-gray-700"
          >
            2
          </button>
          <button
            class="size-8 flex items-center justify-center rounded-lg border border-border-light text-text-secondary hover:bg-sidebar-light text-xs font-bold dark:border-gray-600 dark:hover:bg-gray-700"
          >
            3
          </button>
          <button
            class="size-8 flex items-center justify-center rounded-lg border border-border-light text-text-secondary hover:bg-sidebar-light disabled:opacity-50 dark:border-gray-600 dark:hover:bg-gray-700"
          >
            <span class="material-symbols-outlined text-sm">chevron_right</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
