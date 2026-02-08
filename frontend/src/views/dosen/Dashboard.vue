<template>
  <div class="flex flex-col gap-8 animate-fade-in">
    <!-- Welcome Header -->
    <header
      class="flex flex-col md:flex-row md:items-end justify-between gap-4"
    >
      <div>
        <h1 class="text-3xl font-bold tracking-tight text-text-main">
          Selamat Datang, Bapak Budi
        </h1>
        <p class="text-text-secondary text-base mt-2">
          Berikut adalah ringkasan aktivitas bimbingan Anda hari ini.
        </p>
      </div>
      <div class="flex gap-3">
        <router-link
          to="/dosen/jadwal"
          class="flex items-center gap-2 bg-surface-light border border-border-light px-4 py-2 rounded-lg text-sm font-medium hover:bg-sidebar-light transition-colors shadow-sm text-text-main"
        >
          <span class="material-symbols-outlined text-[20px]"
            >calendar_month</span
          >
          Lihat Jadwal
        </router-link>
        <button
          class="flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors shadow-sm shadow-blue-200 dark:shadow-none"
        >
          <span class="material-symbols-outlined text-[20px]">add</span>
          Input Nilai
        </button>
      </div>
    </header>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <!-- Card 1: Total Advisees -->
      <router-link
        to="/dosen/bimbingan"
        class="bg-surface-light p-6 rounded-xl border border-border-light shadow-sm flex flex-col justify-between h-40 group hover:border-primary/50 transition-colors cursor-pointer"
      >
        <div class="flex justify-between items-start">
          <div>
            <p class="text-text-secondary text-sm font-medium mb-1">
              Mahasiswa Bimbingan
            </p>
            <p class="text-3xl font-bold text-text-main">12</p>
          </div>
          <div
            class="size-10 rounded-full bg-blue-50 dark:bg-blue-900/30 text-primary flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-colors"
          >
            <span class="material-symbols-outlined">groups</span>
          </div>
        </div>
        <div
          class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-1.5 mt-4 overflow-hidden"
        >
          <div class="bg-primary h-1.5 rounded-full" style="width: 75%"></div>
        </div>
        <p class="text-xs text-text-secondary mt-2">
          <span class="text-green-600 dark:text-green-400 font-medium"
            >8 Aktif</span
          >
          · 4 Cuti
        </p>
      </router-link>

      <!-- Card 2: Next Exam -->
      <div
        class="bg-surface-light p-6 rounded-xl border-l-4 border-l-primary border-y border-r border-border-light shadow-sm flex flex-col justify-between h-40"
      >
        <div class="flex justify-between items-start">
          <div>
            <p class="text-text-secondary text-sm font-medium mb-1">
              Ujian Terdekat
            </p>
            <p class="text-xl font-bold text-text-main leading-tight mt-1">
              Sidang Skripsi
            </p>
            <p class="text-sm text-text-main font-medium mt-1">
              Senin, 14:00 WIB
            </p>
          </div>
          <div
            class="size-10 rounded-full bg-orange-50 dark:bg-orange-900/30 text-orange-500 flex items-center justify-center"
          >
            <span class="material-symbols-outlined">event_available</span>
          </div>
        </div>
        <div
          class="flex items-center gap-2 mt-2 pt-2 border-t border-dashed border-gray-100 dark:border-gray-700"
        >
          <span class="material-symbols-outlined text-gray-400 text-sm"
            >person</span
          >
          <p class="text-sm text-text-secondary">
            Mahasiswa:
            <span class="font-medium text-text-main">Rina Wati</span>
          </p>
        </div>
      </div>

      <!-- Card 3: Pending Approvals -->
      <div
        class="bg-surface-light p-6 rounded-xl border border-border-light shadow-sm flex flex-col justify-between h-40 group hover:border-red-200 transition-colors cursor-pointer"
      >
        <div class="flex justify-between items-start">
          <div>
            <p class="text-text-secondary text-sm font-medium mb-1">
              Menunggu Persetujuan
            </p>
            <p class="text-3xl font-bold text-text-main">3</p>
          </div>
          <div
            class="size-10 rounded-full bg-red-50 dark:bg-red-900/30 text-red-500 flex items-center justify-center group-hover:bg-red-500 group-hover:text-white transition-colors"
          >
            <span class="material-symbols-outlined">pending_actions</span>
          </div>
        </div>
        <div class="flex gap-2 mt-4">
          <span
            class="bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 text-xs px-2 py-1 rounded font-medium border border-red-100 dark:border-red-800"
            >2 Proposal</span
          >
          <span
            class="bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 text-xs px-2 py-1 rounded font-medium border border-orange-100 dark:border-orange-800"
            >1 Revisi</span
          >
        </div>
      </div>
    </div>

    <!-- Content Split Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 h-full">
      <!-- Left: Recent Activity Table -->
      <div class="lg:col-span-2 flex flex-col gap-4">
        <div class="flex items-center justify-between">
          <h2 class="text-text-main text-[20px] font-bold leading-tight">
            Aktivitas Mahasiswa Terbaru
          </h2>
          <router-link
            to="/dosen/bimbingan"
            class="text-primary text-sm font-medium hover:underline"
          >
            Lihat Semua
          </router-link>
        </div>
        <div
          class="bg-surface-light border border-border-light rounded-xl overflow-hidden shadow-sm"
        >
          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr
                  class="bg-sidebar-light border-b border-border-light text-text-secondary"
                >
                  <th
                    class="px-6 py-4 text-xs font-semibold uppercase tracking-wider"
                  >
                    Mahasiswa
                  </th>
                  <th
                    class="px-6 py-4 text-xs font-semibold uppercase tracking-wider"
                  >
                    Aktivitas
                  </th>
                  <th
                    class="px-6 py-4 text-xs font-semibold uppercase tracking-wider"
                  >
                    Status
                  </th>
                  <th
                    class="px-6 py-4 text-xs font-semibold uppercase tracking-wider w-32"
                  >
                    Aksi
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-border-light">
                <tr class="group hover:bg-sidebar-light transition-colors">
                  <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                      <div
                        class="size-8 rounded-full bg-gray-200 dark:bg-gray-700 bg-center bg-cover"
                        style="
                          background-image: url(&quot;https://lh3.googleusercontent.com/aida-public/AB6AXuBYCuNlLBtqB5KfU2XCx2rhEuplT7TtbJUSVvOhYmIAfx2kppLQUH8zTYLXwTN9OvaxP5MYo9W9MFUrJCI_zdCG24bfYN8fwmdOq6q_ItllLLFI5du7S0eQqZ6jOjfWJ8xQYL-bKLQsn2QjWuqej244ODajAoJ7G-DHzNCOoOfCSnEmVnA1snb3cvEUAAyx7iLUvZxJldMICYLdeaP8GOEXVowgo3XeHvI38KfiEr6e5c8CGW2X1I8sxtImaSqIkjYGGQPz3kDOlsfi&quot;);
                        "
                      ></div>
                      <div>
                        <p class="text-sm font-medium text-text-main">
                          Andi Pratama
                        </p>
                        <p class="text-xs text-text-secondary">TI - 2020</p>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <p class="text-sm text-text-main font-medium">
                      Upload Bab 1
                    </p>
                    <p class="text-xs text-text-secondary mt-0.5">
                      Pendahuluan v2.pdf
                    </p>
                  </td>
                  <td class="px-6 py-4">
                    <span
                      class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-400 dark:border-yellow-800"
                    >
                      <span class="size-1.5 rounded-full bg-yellow-500"></span>
                      Menunggu Review
                    </span>
                  </td>
                  <td class="px-6 py-4">
                    <button
                      class="text-primary hover:text-blue-700 text-sm font-medium"
                    >
                      Review
                    </button>
                  </td>
                </tr>
                <tr class="group hover:bg-sidebar-light transition-colors">
                  <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                      <div
                        class="size-8 rounded-full bg-gray-200 dark:bg-gray-700 bg-center bg-cover"
                        style="
                          background-image: url(&quot;https://lh3.googleusercontent.com/aida-public/AB6AXuBQ5AcXY_f74bS71qnT_6JFBnENx2UbwCEUlRS5KC2A4ziE7a6DPOK5Q2r8xMzEPF2Fegffck89cyHmLIMJRdd8ZfJnoUS6JDiKnjpYCPPEgGb4C8_093aivJmu8IGkSNUpIcxQtXXMgsNBHjlAhHwWS2BAtxgQ_6LrlPHDRp1wAF9MndNWmPZFwKF-8zhRzLvGKFv8RFh3UWnsu8otMO-Y-ltvcsFweqkKmQJrVJEpqM-dFFdujq6i3JGZ2HsOyfXuh_RBhVSp3i7K&quot;);
                        "
                      ></div>
                      <div>
                        <p class="text-sm font-medium text-text-main">
                          Siti Aminah
                        </p>
                        <p class="text-xs text-text-secondary">SI - 2020</p>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <p class="text-sm text-text-main font-medium">
                      Pengajuan Judul
                    </p>
                    <p class="text-xs text-text-secondary mt-0.5">
                      11 Okt, 09:30
                    </p>
                  </td>
                  <td class="px-6 py-4">
                    <span
                      class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200 dark:bg-green-900/30 dark:text-green-400 dark:border-green-800"
                    >
                      <span class="size-1.5 rounded-full bg-green-500"></span>
                      Disetujui
                    </span>
                  </td>
                  <td class="px-6 py-4">
                    <button
                      class="text-text-secondary hover:text-text-main text-sm font-medium"
                    >
                      Detail
                    </button>
                  </td>
                </tr>
                <tr class="group hover:bg-sidebar-light transition-colors">
                  <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                      <div
                        class="size-8 rounded-full bg-gray-200 dark:bg-gray-700 bg-center bg-cover"
                        style="
                          background-image: url(&quot;https://lh3.googleusercontent.com/aida-public/AB6AXuDWMbbu5kAbqfETcaFdOpJKsupoRa96QzMJLIp4lqc9Xhnwwh6SaTlcs75ZM0FlSDwRadIzu-leSwNueJXVMo1ml3GfrGfrMa64SAlEkusK_35ezEPf9nql2QcIijeIX_o7OLH19amesPZgRFJTfQ8Fr9XwpP8PQjPtdn0hgTfv_EMwpkp40maPEMrG0IYaIlKU0dFjeHgXvGim8-MYkZEEusd4YShLCOGEbJppUw4PBIac1ZXpptcauMhIP0N2zuNR1INen1HOOG6M&quot;);
                        "
                      ></div>
                      <div>
                        <p class="text-sm font-medium text-text-main">
                          Budi Santoso
                        </p>
                        <p class="text-xs text-text-secondary">TI - 2019</p>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <p class="text-sm text-text-main font-medium">
                      Revisi Bab 3
                    </p>
                    <p class="text-xs text-text-secondary mt-0.5">
                      Perbaikan Metodologi.docx
                    </p>
                  </td>
                  <td class="px-6 py-4">
                    <span
                      class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800"
                    >
                      <span class="size-1.5 rounded-full bg-red-500"></span>
                      Perlu Revisi
                    </span>
                  </td>
                  <td class="px-6 py-4">
                    <button
                      class="text-primary hover:text-blue-700 text-sm font-medium"
                    >
                      Cek Ulang
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Right: Upcoming Schedule -->
      <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between">
          <h2 class="text-text-main text-[20px] font-bold leading-tight">
            Jadwal Minggu Ini
          </h2>
        </div>
        <div
          class="bg-surface-light border border-border-light rounded-xl p-5 shadow-sm h-full"
        >
          <div
            class="relative pl-4 border-l border-gray-200 dark:border-gray-700 space-y-8"
          >
            <!-- Timeline Item 1 -->
            <div class="relative">
              <span
                class="absolute -left-[21px] top-1 size-3 rounded-full border-2 border-white dark:border-surface-light bg-primary ring-2 ring-blue-100 dark:ring-blue-900"
              ></span>
              <div class="flex flex-col gap-1">
                <span
                  class="text-xs font-bold text-primary bg-blue-50 dark:bg-blue-900/30 w-fit px-2 py-0.5 rounded uppercase tracking-wider"
                  >Senin, 14 Okt</span
                >
                <p class="text-sm font-bold text-text-main mt-1">
                  Sidang Skripsi: Rina Wati
                </p>
                <div
                  class="flex items-center gap-1 text-text-secondary text-xs"
                >
                  <span class="material-symbols-outlined text-[16px]"
                    >schedule</span
                  >
                  14:00 - 15:30 WIB
                </div>
                <div
                  class="flex items-center gap-1 text-text-secondary text-xs"
                >
                  <span class="material-symbols-outlined text-[16px]"
                    >location_on</span
                  >
                  R. Sidang 2
                </div>
              </div>
            </div>
            <!-- Timeline Item 2 -->
            <div class="relative">
              <span
                class="absolute -left-[21px] top-1 size-3 rounded-full border-2 border-white dark:border-surface-light bg-gray-300 dark:bg-gray-600"
              ></span>
              <div class="flex flex-col gap-1">
                <span
                  class="text-xs font-bold text-gray-500 bg-gray-100 dark:bg-gray-700 w-fit px-2 py-0.5 rounded uppercase tracking-wider"
                  >Rabu, 16 Okt</span
                >
                <p class="text-sm font-bold text-text-main mt-1">
                  Seminar Proposal: Dimas A.
                </p>
                <div
                  class="flex items-center gap-1 text-text-secondary text-xs"
                >
                  <span class="material-symbols-outlined text-[16px]"
                    >schedule</span
                  >
                  09:00 - 10:00 WIB
                </div>
                <div
                  class="flex items-center gap-1 text-text-secondary text-xs"
                >
                  <span class="material-symbols-outlined text-[16px]"
                    >location_on</span
                  >
                  R. Seminar 1
                </div>
              </div>
            </div>
            <!-- Timeline Item 3 -->
            <div class="relative">
              <span
                class="absolute -left-[21px] top-1 size-3 rounded-full border-2 border-white dark:border-surface-light bg-gray-300 dark:bg-gray-600"
              ></span>
              <div class="flex flex-col gap-1">
                <span
                  class="text-xs font-bold text-gray-500 bg-gray-100 dark:bg-gray-700 w-fit px-2 py-0.5 rounded uppercase tracking-wider"
                  >Jumat, 18 Okt</span
                >
                <p class="text-sm font-bold text-text-main mt-1">
                  Bimbingan Rutin (Grup A)
                </p>
                <div
                  class="flex items-center gap-1 text-text-secondary text-xs"
                >
                  <span class="material-symbols-outlined text-[16px]"
                    >schedule</span
                  >
                  13:00 - 15:00 WIB
                </div>
                <div
                  class="flex items-center gap-1 text-text-secondary text-xs"
                >
                  <span class="material-symbols-outlined text-[16px]"
                    >location_on</span
                  >
                  Lab Komputer 3
                </div>
              </div>
            </div>
          </div>
          <div class="mt-8 pt-4 border-t border-border-light">
            <button
              class="w-full py-2 text-center text-primary text-sm font-medium hover:bg-blue-50 dark:hover:bg-blue-900/10 rounded-lg transition-colors"
            >
              Lihat Jadwal Lengkap
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
