<template>
  <div class="max-w-7xl mx-auto flex flex-col gap-8 animate-fade-in-up">
    <!-- Header -->
    <div class="flex flex-col gap-1">
      <div class="flex items-center gap-2 text-sm text-text-secondary">
        <router-link
          to="/admin/dashboard"
          class="hover:text-primary transition-colors"
          >Dashboard</router-link
        >
        <span>/</span>
        <span class="text-text-main font-medium">Data Skripsi</span>
      </div>
      <h1 class="text-text-main text-3xl font-bold leading-tight">
        Data Skripsi
      </h1>
      <p class="text-text-secondary text-sm font-normal">
        Kelola data pendaftaran dan status skripsi mahasiswa.
      </p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div
        class="flex flex-col gap-2 rounded-xl p-6 bg-surface-light border border-border-light shadow-sm hover:shadow-md transition-all hover:-translate-y-1 duration-300"
      >
        <div class="flex items-center justify-between">
          <p
            class="text-text-secondary text-xs font-bold uppercase tracking-wider"
          >
            Total Judul
          </p>
          <div class="bg-primary/10 p-2 rounded-lg text-primary">
            <span class="material-symbols-outlined">library_books</span>
          </div>
        </div>
        <div class="mt-2">
          <p class="text-text-main text-3xl font-bold leading-tight">1,240</p>
          <div class="flex items-center gap-1 mt-1">
            <span class="material-symbols-outlined text-green-600 text-[18px]"
              >trending_up</span
            >
            <p class="text-green-600 text-xs font-semibold">+5% bulan ini</p>
          </div>
        </div>
      </div>

      <div
        class="flex flex-col gap-2 rounded-xl p-6 bg-surface-light border border-border-light shadow-sm hover:shadow-md transition-all hover:-translate-y-1 duration-300"
      >
        <div class="flex items-center justify-between">
          <p
            class="text-text-secondary text-xs font-bold uppercase tracking-wider"
          >
            Perlu Verifikasi
          </p>
          <div class="bg-orange-100 p-2 rounded-lg text-orange-600">
            <span class="material-symbols-outlined">pending_actions</span>
          </div>
        </div>
        <div class="mt-2">
          <p class="text-text-main text-3xl font-bold leading-tight">12</p>
          <div class="flex items-center gap-1 mt-1">
            <span class="material-symbols-outlined text-orange-600 text-[18px]"
              >priority_high</span
            >
            <p class="text-orange-600 text-xs font-semibold">
              Menunggu tindakan
            </p>
          </div>
        </div>
      </div>

      <div
        class="flex flex-col gap-2 rounded-xl p-6 bg-surface-light border border-border-light shadow-sm hover:shadow-md transition-all hover:-translate-y-1 duration-300"
      >
        <div class="flex items-center justify-between">
          <p
            class="text-text-secondary text-xs font-bold uppercase tracking-wider"
          >
            Selesai
          </p>
          <div class="bg-green-100 p-2 rounded-lg text-green-600">
            <span class="material-symbols-outlined">check_circle</span>
          </div>
        </div>
        <div class="mt-2">
          <p class="text-text-main text-3xl font-bold leading-tight">856</p>
          <div class="flex items-center gap-1 mt-1">
            <span class="material-symbols-outlined text-green-600 text-[18px]"
              >trending_up</span
            >
            <p class="text-green-600 text-xs font-semibold">Total lulus</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Toolbar & Table -->
    <div
      class="flex flex-col bg-surface-light border border-border-light rounded-xl shadow-sm"
    >
      <!-- Toolbar -->
      <div
        class="p-5 border-b border-border-light flex flex-col md:flex-row gap-4 items-center justify-between"
      >
        <!-- Search -->
        <div class="relative w-full md:max-w-md">
          <div
            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
          >
            <span class="material-symbols-outlined text-text-secondary"
              >search</span
            >
          </div>
          <input
            class="block w-full pl-10 pr-3 py-2.5 border border-border-light rounded-lg leading-5 bg-background-light text-text-main placeholder-text-secondary focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary sm:text-sm transition-shadow dark:bg-background"
            placeholder="Cari Mahasiswa, NIM, atau Judul..."
            type="text"
          />
        </div>
        <!-- Actions -->
        <div class="flex gap-3 w-full md:w-auto">
          <button
            class="flex items-center justify-center gap-2 px-4 py-2.5 bg-surface-light border border-border-light rounded-lg text-text-secondary hover:bg-background-light hover:text-text-main dark:bg-sidebar-light dark:hover:bg-surface-light font-medium text-sm transition-colors w-full md:w-auto"
          >
            <span class="material-symbols-outlined text-[20px]"
              >filter_list</span
            >
            Filter
          </button>
          <button
            class="flex items-center justify-center gap-2 px-4 py-2.5 bg-primary hover:bg-blue-600 text-white rounded-lg font-medium text-sm shadow-sm shadow-blue-500/20 transition-all w-full md:w-auto whitespace-nowrap"
          >
            <span class="material-symbols-outlined text-[20px]">add</span>
            Tambah Data
          </button>
        </div>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
          <thead
            class="bg-sidebar-light/50 text-text-secondary font-medium border-b border-border-light"
          >
            <tr>
              <th class="px-6 py-4 w-12">
                <input
                  type="checkbox"
                  class="rounded border-border-light text-primary focus:ring-primary h-4 w-4"
                />
              </th>
              <th class="px-6 py-4">Mahasiswa</th>
              <th class="px-6 py-4">Judul Skripsi</th>
              <th class="px-6 py-4">Pembimbing</th>
              <th class="px-6 py-4">Status</th>
              <th class="px-6 py-4 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border-light">
            <tr class="group hover:bg-background-light/50 transition-colors">
              <td class="px-6 py-4">
                <input
                  type="checkbox"
                  class="rounded border-border-light text-primary focus:ring-primary h-4 w-4"
                />
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div
                    class="size-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold shrink-0"
                  >
                    BS
                  </div>
                  <div>
                    <p class="font-bold text-text-main text-sm">Budi Santoso</p>
                    <p class="text-xs text-text-secondary font-medium">
                      201910234
                    </p>
                  </div>
                </div>
              </td>
              <td
                class="px-6 py-4 text-text-main max-w-xs truncate"
                title="Sistem Pendukung Keputusan Pemilihan Jurusan"
              >
                Sistem Pendukung Keputusan Pemilihan Jurusan
              </td>
              <td class="px-6 py-4 text-text-secondary">Dr. Andi Wijaya</td>
              <td class="px-6 py-4">
                <span
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400"
                >
                  <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                  Sem. Proposal
                </span>
              </td>
              <td class="px-6 py-4 text-right">
                <div
                  class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity"
                >
                  <button
                    class="p-2 text-text-secondary hover:text-primary hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
                    title="Edit"
                  >
                    <span class="material-symbols-outlined text-[20px]"
                      >edit</span
                    >
                  </button>
                  <button
                    class="p-2 text-text-secondary hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                    title="Delete"
                  >
                    <span class="material-symbols-outlined text-[20px]"
                      >delete</span
                    >
                  </button>
                </div>
              </td>
            </tr>

            <!-- Static Row 2 -->
            <tr class="group hover:bg-background-light/50 transition-colors">
              <td class="px-6 py-4">
                <input
                  type="checkbox"
                  class="rounded border-border-light text-primary focus:ring-primary h-4 w-4"
                />
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div
                    class="size-10 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center text-xs font-bold shrink-0"
                  >
                    SA
                  </div>
                  <div>
                    <p class="font-bold text-text-main text-sm">Siti Aminah</p>
                    <p class="text-xs text-text-secondary font-medium">
                      201910255
                    </p>
                  </div>
                </div>
              </td>
              <td
                class="px-6 py-4 text-text-main max-w-xs truncate"
                title="Analisis Sentimen Pengguna Twitter"
              >
                Analisis Sentimen Pengguna Twitter
              </td>
              <td class="px-6 py-4 text-text-secondary">Rina Haryani, M.Kom</td>
              <td class="px-6 py-4">
                <span
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400"
                >
                  <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                  Bimbingan
                </span>
              </td>
              <td class="px-6 py-4 text-right">
                <div
                  class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity"
                >
                  <button
                    class="p-2 text-text-secondary hover:text-primary hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
                    title="Edit"
                  >
                    <span class="material-symbols-outlined text-[20px]"
                      >edit</span
                    >
                  </button>
                  <button
                    class="p-2 text-text-secondary hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                    title="Delete"
                  >
                    <span class="material-symbols-outlined text-[20px]"
                      >delete</span
                    >
                  </button>
                </div>
              </td>
            </tr>

            <!-- Static Row 3 -->
            <tr class="group hover:bg-background-light/50 transition-colors">
              <td class="px-6 py-4">
                <input
                  type="checkbox"
                  class="rounded border-border-light text-primary focus:ring-primary h-4 w-4"
                />
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div
                    class="size-10 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center text-xs font-bold shrink-0"
                  >
                    AR
                  </div>
                  <div>
                    <p class="font-bold text-text-main text-sm">Ahmad Rizky</p>
                    <p class="text-xs text-text-secondary font-medium">
                      201910301
                    </p>
                  </div>
                </div>
              </td>
              <td
                class="px-6 py-4 text-text-main max-w-xs truncate"
                title="Perancangan UI/UX Aplikasi E-Learning"
              >
                Perancangan UI/UX Aplikasi E-Learning
              </td>
              <td class="px-6 py-4 text-text-secondary">Budi Darmawan, MT</td>
              <td class="px-6 py-4">
                <span
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400"
                >
                  <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                  Daftar Ujian
                </span>
              </td>
              <td class="px-6 py-4 text-right">
                <div
                  class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity"
                >
                  <button
                    class="p-2 text-text-secondary hover:text-primary hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
                    title="Edit"
                  >
                    <span class="material-symbols-outlined text-[20px]"
                      >edit</span
                    >
                  </button>
                  <button
                    class="p-2 text-text-secondary hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                    title="Delete"
                  >
                    <span class="material-symbols-outlined text-[20px]"
                      >delete</span
                    >
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div
        class="flex items-center justify-between px-6 py-4 border-t border-border-light"
      >
        <p class="text-sm text-text-secondary">
          Menampilkan <span class="font-medium text-text-main">1</span> sampai
          <span class="font-medium text-text-main">3</span> dari
          <span class="font-medium text-text-main">128</span> data
        </p>
        <div class="flex gap-2">
          <button
            class="px-3 py-1.5 rounded-md border border-border-light text-text-secondary text-sm font-medium hover:bg-background-light disabled:opacity-50"
            disabled
          >
            Sebelumnya
          </button>
          <button
            class="px-3 py-1.5 rounded-md border border-border-light text-text-secondary text-sm font-medium hover:bg-background-light"
          >
            Selanjutnya
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
// Component logic here if needed
</script>
