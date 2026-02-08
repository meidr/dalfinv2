<template>
  <div class="flex flex-col gap-6">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
      <div class="flex flex-col gap-1">
        <h1 class="text-3xl font-bold tracking-tight text-text-main">
          Master Data Dosen
        </h1>
        <p class="text-text-secondary text-sm font-normal">
          Kelola data dosen pembimbing dan kuota bimbingan skripsi.
        </p>
      </div>
    </div>

    <!-- Filters & Actions -->
    <div
      class="bg-surface-light dark:bg-surface-light p-4 rounded-xl border border-border-light shadow-sm flex flex-col md:flex-row gap-4 justify-between items-center"
    >
      <div class="relative w-full md:w-96 group">
        <div
          class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
        >
          <span
            class="material-symbols-outlined text-text-secondary group-focus-within:text-primary transition-colors"
            >search</span
          >
        </div>
        <input
          class="block w-full pl-10 pr-3 py-2.5 border border-border-light rounded-lg bg-white dark:bg-sidebar-light dark:border-gray-600 text-text-main placeholder-text-secondary focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all text-sm outline-none"
          placeholder="Cari NIDN, Nama, atau Jabatan..."
          type="text"
        />
      </div>
      <div class="flex items-center gap-3 w-full md:w-auto">
        <div class="relative w-full md:w-auto">
          <select
            class="appearance-none bg-white dark:bg-sidebar-light border border-border-light dark:border-gray-600 text-text-main text-sm rounded-lg focus:ring-primary focus:border-primary block w-full pl-4 pr-10 py-2.5 cursor-pointer hover:border-gray-300 transition-colors outline-none"
          >
            <option>Semua Status</option>
            <option>Aktif</option>
            <option>Cuti</option>
            <option>Tugas Belajar</option>
          </select>
          <div
            class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-text-secondary"
          >
            <span class="material-symbols-outlined text-[20px]"
              >expand_more</span
            >
          </div>
        </div>
        <button
          class="flex items-center justify-center gap-2 bg-primary hover:bg-blue-600 text-white font-medium py-2.5 px-5 rounded-lg transition-all shadow-md shadow-primary/20 hover:shadow-lg active:scale-95 whitespace-nowrap w-full md:w-auto text-sm"
        >
          <span class="material-symbols-outlined text-[20px]">add</span>
          <span>Tambah Dosen</span>
        </button>
      </div>
    </div>

    <!-- Data Table -->
    <div
      class="bg-surface-light dark:bg-surface-light rounded-xl border border-border-light shadow-sm overflow-hidden"
    >
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead
            class="bg-sidebar-light/50 border-b border-border-light dark:bg-sidebar-light/50"
          >
            <tr>
              <th
                class="p-4 pl-6 text-[10px] font-bold tracking-widest text-text-secondary uppercase w-[140px]"
              >
                NIDN/NIP
              </th>
              <th
                class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase min-w-[200px]"
              >
                Nama Dosen
              </th>
              <th
                class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase w-[180px]"
              >
                Jabatan
              </th>
              <th
                class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase w-[220px]"
              >
                Kuota Bimbingan
              </th>
              <th
                class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase w-[120px]"
              >
                Status
              </th>
              <th
                class="p-4 pr-6 text-[10px] font-bold tracking-widest text-text-secondary uppercase text-right w-[100px]"
              >
                Aksi
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border-light">
            <!-- Row 1 -->
            <tr class="group hover:bg-sidebar-light/30 transition-colors">
              <td
                class="p-4 pl-6 font-mono text-xs font-medium text-text-secondary"
              >
                00123456
              </td>
              <td class="p-4">
                <div class="flex items-center gap-3">
                  <div
                    class="size-9 rounded-full bg-slate-100 flex items-center justify-center text-primary font-bold text-xs border border-slate-200 dark:bg-slate-700 dark:border-gray-600 dark:text-blue-400"
                  >
                    BS
                  </div>
                  <div class="font-bold text-sm text-text-main">
                    Dr. Budi Santoso
                  </div>
                </div>
              </td>
              <td class="p-4 text-sm text-text-secondary">Lektor Kepala</td>
              <td class="p-4">
                <div class="flex flex-col gap-1.5 w-full max-w-[160px]">
                  <div class="flex justify-between text-xs font-medium">
                    <span class="text-text-main">8 Mahasiswa</span>
                    <span class="text-text-secondary">8/10</span>
                  </div>
                  <div
                    class="w-full bg-slate-100 dark:bg-gray-700 rounded-full h-1.5 overflow-hidden"
                  >
                    <div
                      class="bg-yellow-500 h-1.5 rounded-full"
                      style="width: 80%"
                    ></div>
                  </div>
                </div>
              </td>
              <td class="p-4">
                <span
                  class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-green-50 text-green-700 border border-green-100 dark:bg-green-500/20 dark:text-green-400 dark:border-green-500/30"
                >
                  <span
                    class="size-1.5 rounded-full bg-green-600 dark:bg-green-400 mr-1.5"
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
                    class="p-1.5 text-text-secondary hover:text-primary hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-md transition-colors"
                    title="Detail"
                  >
                    <span class="material-symbols-outlined text-[20px]"
                      >visibility</span
                    >
                  </button>
                </div>
              </td>
            </tr>
            <!-- Row 2 -->
            <tr class="group hover:bg-sidebar-light/30 transition-colors">
              <td
                class="p-4 pl-6 font-mono text-xs font-medium text-text-secondary"
              >
                00987654
              </td>
              <td class="p-4">
                <div class="flex items-center gap-3">
                  <div
                    class="size-9 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 font-bold text-xs border border-indigo-100 dark:bg-indigo-900/30 dark:border-indigo-800 dark:text-indigo-400"
                  >
                    SA
                  </div>
                  <div class="font-bold text-sm text-text-main">
                    Siti Aminah, M.Kom
                  </div>
                </div>
              </td>
              <td class="p-4 text-sm text-text-secondary">Asisten Ahli</td>
              <td class="p-4">
                <div class="flex flex-col gap-1.5 w-full max-w-[160px]">
                  <div class="flex justify-between text-xs font-medium">
                    <span class="text-text-main">2 Mahasiswa</span>
                    <span class="text-text-secondary">2/8</span>
                  </div>
                  <div
                    class="w-full bg-slate-100 dark:bg-gray-700 rounded-full h-1.5 overflow-hidden"
                  >
                    <div
                      class="bg-primary h-1.5 rounded-full"
                      style="width: 25%"
                    ></div>
                  </div>
                </div>
              </td>
              <td class="p-4">
                <span
                  class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-green-50 text-green-700 border border-green-100 dark:bg-green-500/20 dark:text-green-400 dark:border-green-500/30"
                >
                  <span
                    class="size-1.5 rounded-full bg-green-600 dark:bg-green-400 mr-1.5"
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
                    class="p-1.5 text-text-secondary hover:text-primary hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-md transition-colors"
                    title="Detail"
                  >
                    <span class="material-symbols-outlined text-[20px]"
                      >visibility</span
                    >
                  </button>
                </div>
              </td>
            </tr>
            <!-- Row 3 -->
            <tr class="group hover:bg-sidebar-light/30 transition-colors">
              <td
                class="p-4 pl-6 font-mono text-xs font-medium text-text-secondary"
              >
                00555123
              </td>
              <td class="p-4">
                <div class="flex items-center gap-3">
                  <div
                    class="size-9 rounded-full bg-orange-50 flex items-center justify-center text-orange-600 font-bold text-xs border border-orange-100 dark:bg-orange-900/30 dark:border-orange-800 dark:text-orange-400"
                  >
                    AD
                  </div>
                  <div class="font-bold text-sm text-text-main">
                    Ahmad Dahlan, Ph.D
                  </div>
                </div>
              </td>
              <td class="p-4 text-sm text-text-secondary">Guru Besar</td>
              <td class="p-4">
                <div class="flex flex-col gap-1.5 w-full max-w-[160px]">
                  <div class="flex justify-between text-xs font-medium">
                    <span class="text-text-main">5 Mahasiswa</span>
                    <span class="text-text-secondary">5/5</span>
                  </div>
                  <div
                    class="w-full bg-slate-100 dark:bg-gray-700 rounded-full h-1.5 overflow-hidden"
                  >
                    <div
                      class="bg-red-500 h-1.5 rounded-full"
                      style="width: 100%"
                    ></div>
                  </div>
                </div>
              </td>
              <td class="p-4">
                <span
                  class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold bg-gray-100 text-gray-700 border border-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600"
                >
                  <span
                    class="size-1.5 rounded-full bg-gray-500 dark:bg-gray-400 mr-1.5"
                  ></span>
                  Cuti
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
                    class="p-1.5 text-text-secondary hover:text-primary hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-md transition-colors"
                    title="Detail"
                  >
                    <span class="material-symbols-outlined text-[20px]"
                      >visibility</span
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
        class="p-4 border-t border-border-light flex flex-col md:flex-row items-center justify-between gap-4 bg-gray-50/50 dark:bg-sidebar-light/50"
      >
        <p class="text-xs text-text-secondary font-medium">
          Menampilkan 1-3 dari 42 data
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
