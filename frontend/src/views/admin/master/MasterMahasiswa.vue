<template>
  <div class="flex flex-col gap-6">
    <div
      class="flex flex-col md:flex-row md:items-center justify-between gap-4"
    >
      <div class="flex flex-col gap-1">
        <h1 class="text-text-main text-3xl font-bold leading-tight">
          Master Data Mahasiswa
        </h1>
        <p class="text-text-secondary text-sm font-normal">
          Manage all registered students for thesis submission.
        </p>
      </div>
      <div class="flex flex-col sm:flex-row items-center gap-3">
        <div class="relative w-full sm:w-64 group">
          <span
            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-text-secondary text-[20px] group-focus-within:text-primary transition-colors"
            >search</span
          >
          <input
            class="w-full pl-10 pr-4 py-2.5 bg-white border border-border-light rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none shadow-sm placeholder:text-gray-400 dark:bg-sidebar-light dark:border-gray-600 dark:text-gray-200"
            placeholder="Cari Nama, NIM..."
            type="text"
          />
        </div>
        <button
          class="flex items-center justify-center gap-2 bg-primary text-white px-5 py-2.5 rounded-lg text-sm font-semibold shadow-md shadow-primary/20 hover:bg-blue-600 hover:shadow-lg hover:shadow-primary/30 active:scale-95 transition-all w-full sm:w-auto"
        >
          <span class="material-symbols-outlined text-[20px]">add</span>
          <span>Tambah Mahasiswa</span>
        </button>
      </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div
        class="flex items-center gap-3 bg-white dark:bg-surface-light px-4 py-3 rounded-xl border border-border-light shadow-sm"
      >
        <div
          class="p-2 bg-green-50 text-green-600 rounded-full dark:bg-green-900/30 dark:text-green-400"
        >
          <span class="material-symbols-outlined text-xl">check_circle</span>
        </div>
        <div>
          <p
            class="text-xs text-text-secondary uppercase font-bold tracking-wider"
          >
            Mahasiswa Aktif
          </p>
          <p class="text-lg font-bold text-text-main">1,240</p>
        </div>
      </div>
      <div
        class="flex items-center gap-3 bg-white dark:bg-surface-light px-4 py-3 rounded-xl border border-border-light shadow-sm"
      >
        <div
          class="p-2 bg-orange-50 text-orange-600 rounded-full dark:bg-orange-900/30 dark:text-orange-400"
        >
          <span class="material-symbols-outlined text-xl">school</span>
        </div>
        <div>
          <p
            class="text-xs text-text-secondary uppercase font-bold tracking-wider"
          >
            Total Mahasiswa
          </p>
          <p class="text-lg font-bold text-text-main">1,452</p>
        </div>
      </div>
    </div>

    <!-- Table Container -->
    <div
      class="flex flex-col rounded-xl border border-border-light bg-surface-light overflow-hidden shadow-sm dark:bg-surface-light"
    >
      <div
        class="p-5 border-b border-border-light flex flex-col md:flex-row gap-4 items-center justify-between bg-gray-50/50 dark:bg-sidebar-light/50"
      >
        <div class="flex gap-3 w-full md:w-auto">
          <button
            class="flex items-center justify-center gap-2 px-4 py-2 bg-white border border-border-light rounded-lg text-text-secondary hover:bg-gray-50 hover:text-text-main dark:bg-sidebar-light dark:border-gray-600 dark:hover:bg-gray-700 dark:text-gray-300 transition-colors w-full md:w-auto text-sm font-medium shadow-sm"
          >
            <span class="material-symbols-outlined text-[18px]"
              >filter_list</span
            >
            Filter Angkatan
          </button>
        </div>
        <div class="flex gap-2">
          <!-- Pagination Controls (Top) can go here if needed -->
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead
            class="bg-sidebar-light/50 border-b border-border-light dark:bg-sidebar-light/50"
          >
            <tr>
              <th class="p-4 pl-6 w-12">
                <input
                  type="checkbox"
                  class="rounded border-border-light text-primary focus:ring-primary size-4"
                />
              </th>
              <th
                class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase"
              >
                NIM
              </th>
              <th
                class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase"
              >
                Mahasiswa
              </th>
              <th
                class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase"
              >
                Angkatan
              </th>
              <th
                class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase"
              >
                Program Studi
              </th>
              <th
                class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase"
              >
                Status
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
                <input
                  type="checkbox"
                  class="rounded border-border-light text-primary focus:ring-primary size-4"
                />
              </td>
              <td class="p-4 text-sm font-medium text-text-main font-mono">
                190102001
              </td>
              <td class="p-4">
                <div class="flex items-center gap-3">
                  <div
                    class="bg-blue-100 text-blue-600 size-9 rounded-full flex items-center justify-center font-bold text-xs shrink-0 dark:bg-blue-900/30 dark:text-blue-400"
                  >
                    BS
                  </div>
                  <span class="text-sm font-bold text-text-main"
                    >Budi Santoso</span
                  >
                </div>
              </td>
              <td class="p-4 text-sm text-text-secondary">2019</td>
              <td class="p-4 text-sm text-text-secondary">
                Teknik Informatika
              </td>
              <td class="p-4">
                <span
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-green-50 text-green-700 border border-green-100 dark:bg-green-500/20 dark:text-green-400 dark:border-green-500/30"
                >
                  <span
                    class="size-1.5 rounded-full bg-green-600 dark:bg-green-400"
                  ></span>
                  Aktif
                </span>
              </td>
              <td class="p-4 pr-6 text-right">
                <div
                  class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity"
                >
                  <button
                    class="p-1.5 text-text-secondary hover:text-primary hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-md transition-colors"
                    title="Edit"
                  >
                    <span class="material-symbols-outlined text-[20px]"
                      >edit</span
                    >
                  </button>
                  <button
                    class="p-1.5 text-text-secondary hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-md transition-colors"
                    title="Delete"
                  >
                    <span class="material-symbols-outlined text-[20px]"
                      >delete</span
                    >
                  </button>
                </div>
              </td>
            </tr>
            <!-- Row 2 -->
            <tr class="group hover:bg-sidebar-light/30 transition-colors">
              <td class="p-4 pl-6">
                <input
                  type="checkbox"
                  class="rounded border-border-light text-primary focus:ring-primary size-4"
                />
              </td>
              <td class="p-4 text-sm font-medium text-text-main font-mono">
                190102045
              </td>
              <td class="p-4">
                <div class="flex items-center gap-3">
                  <div
                    class="bg-purple-100 text-purple-600 size-9 rounded-full flex items-center justify-center font-bold text-xs shrink-0 dark:bg-purple-900/30 dark:text-purple-400"
                  >
                    SA
                  </div>
                  <span class="text-sm font-bold text-text-main"
                    >Siti Aminah</span
                  >
                </div>
              </td>
              <td class="p-4 text-sm text-text-secondary">2020</td>
              <td class="p-4 text-sm text-text-secondary">Sistem Informasi</td>
              <td class="p-4">
                <span
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-green-50 text-green-700 border border-green-100 dark:bg-green-500/20 dark:text-green-400 dark:border-green-500/30"
                >
                  <span
                    class="size-1.5 rounded-full bg-green-600 dark:bg-green-400"
                  ></span>
                  Aktif
                </span>
              </td>
              <td class="p-4 pr-6 text-right">
                <div
                  class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity"
                >
                  <button
                    class="p-1.5 text-text-secondary hover:text-primary hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-md transition-colors"
                    title="Edit"
                  >
                    <span class="material-symbols-outlined text-[20px]"
                      >edit</span
                    >
                  </button>
                  <button
                    class="p-1.5 text-text-secondary hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-md transition-colors"
                    title="Delete"
                  >
                    <span class="material-symbols-outlined text-[20px]"
                      >delete</span
                    >
                  </button>
                </div>
              </td>
            </tr>
            <!-- Row 3 -->
            <tr class="group hover:bg-sidebar-light/30 transition-colors">
              <td class="p-4 pl-6">
                <input
                  type="checkbox"
                  class="rounded border-border-light text-primary focus:ring-primary size-4"
                />
              </td>
              <td class="p-4 text-sm font-medium text-text-main font-mono">
                180101002
              </td>
              <td class="p-4">
                <div class="flex items-center gap-3">
                  <div
                    class="bg-orange-100 text-orange-600 size-9 rounded-full flex items-center justify-center font-bold text-xs shrink-0 dark:bg-orange-900/30 dark:text-orange-400"
                  >
                    AD
                  </div>
                  <span class="text-sm font-bold text-text-main"
                    >Ahmad Dahlan</span
                  >
                </div>
              </td>
              <td class="p-4 text-sm text-text-secondary">2018</td>
              <td class="p-4 text-sm text-text-secondary">
                Teknik Informatika
              </td>
              <td class="p-4">
                <span
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-gray-100 text-gray-700 border border-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600"
                >
                  <span
                    class="size-1.5 rounded-full bg-gray-500 dark:bg-gray-400"
                  ></span>
                  Non-Aktif
                </span>
              </td>
              <td class="p-4 pr-6 text-right">
                <div
                  class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity"
                >
                  <button
                    class="p-1.5 text-text-secondary hover:text-primary hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-md transition-colors"
                    title="Edit"
                  >
                    <span class="material-symbols-outlined text-[20px]"
                      >edit</span
                    >
                  </button>
                  <button
                    class="p-1.5 text-text-secondary hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-md transition-colors"
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

      <div
        class="p-4 border-t border-border-light flex flex-col md:flex-row items-center justify-between gap-4 bg-gray-50/50 dark:bg-sidebar-light/50"
      >
        <p class="text-xs text-text-secondary font-medium">
          Menampilkan 1-3 dari 128 data
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
            class="size-8 flex items-center justify-center rounded-lg border border-primary bg-primary text-white text-xs font-bold shadow-sm shadow-primary/20"
          >
            1
          </button>
          <button
            class="size-8 flex items-center justify-center rounded-lg border border-border-light text-text-secondary hover:bg-sidebar-light text-xs font-bold dark:border-gray-600 dark:hover:bg-gray-700"
          >
            2
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
