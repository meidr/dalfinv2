<template>
  <div class="flex flex-col gap-6">
    <div class="flex flex-col gap-1">
      <h1 class="text-3xl font-bold tracking-tight text-text-main">
        Manajemen User
      </h1>
      <p class="text-text-secondary text-sm font-normal">
        Kelola akun pengguna, hak akses, dan keamanan sistem skripsi.
      </p>
    </div>

    <!-- Toolbar: Search & Add Button -->
    <div
      class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-surface-light dark:bg-surface-light p-4 rounded-xl border border-border-light shadow-sm"
    >
      <!-- Search Bar -->
      <div class="w-full sm:max-w-md relative group">
        <div
          class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
        >
          <span
            class="material-symbols-outlined text-text-secondary group-focus-within:text-primary transition-colors"
            >search</span
          >
        </div>
        <input
          class="block w-full pl-10 pr-3 py-2.5 border border-border-light rounded-lg bg-white dark:bg-sidebar-light dark:border-gray-600 text-text-main placeholder-text-secondary focus:ring-2 focus:ring-primary/50 text-sm transition-all outline-none"
          placeholder="Cari username, nama, atau email..."
          type="text"
        />
      </div>
      <!-- Add Button -->
      <button
        class="flex shrink-0 items-center justify-center gap-2 bg-primary hover:bg-blue-600 text-white px-5 py-2.5 rounded-lg text-sm font-semibold shadow-md shadow-primary/20 hover:shadow-lg active:scale-95 transition-all w-full sm:w-auto"
      >
        <span class="material-symbols-outlined text-[20px]">add</span>
        <span>Tambah User</span>
      </button>
    </div>

    <!-- Data Table Card -->
    <div
      class="bg-surface-light dark:bg-surface-light rounded-xl border border-border-light shadow-sm flex flex-col overflow-hidden"
    >
      <!-- Table Wrapper -->
      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
          <thead
            class="bg-sidebar-light/50 text-text-secondary font-bold tracking-widest uppercase text-[10px] border-b border-border-light dark:bg-sidebar-light/50"
          >
            <tr>
              <th class="px-6 py-4">Username</th>
              <th class="px-6 py-4">Nama Lengkap</th>
              <th class="px-6 py-4">Email</th>
              <th class="px-6 py-4">Role</th>
              <th class="px-6 py-4">Last Login</th>
              <th class="px-6 py-4 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border-light">
            <!-- Row 1: Dosen -->
            <tr class="hover:bg-sidebar-light/30 transition-colors group">
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div
                    class="size-8 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xs font-bold shrink-0"
                  >
                    DS
                  </div>
                  <span class="font-bold text-text-main">dian.s</span>
                </div>
              </td>
              <td class="px-6 py-4 text-text-main text-sm font-medium">
                Dian Sastro
              </td>
              <td class="px-6 py-4 text-text-secondary">dian@univ.ac.id</td>
              <td class="px-6 py-4">
                <span
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-bold bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300 border border-indigo-100 dark:border-indigo-800"
                >
                  <span class="material-symbols-outlined text-[14px]"
                    >school</span
                  >
                  Dosen
                </span>
              </td>
              <td class="px-6 py-4 text-text-secondary text-xs">2 hours ago</td>
              <td class="px-6 py-4 text-right">
                <div
                  class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity"
                >
                  <button
                    class="p-1.5 text-text-secondary hover:text-primary hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-md transition-colors"
                    title="Edit User"
                  >
                    <span class="material-symbols-outlined text-[20px]"
                      >edit</span
                    >
                  </button>
                  <button
                    class="p-1.5 text-text-secondary hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-md transition-colors"
                    title="Reset Password"
                  >
                    <span class="material-symbols-outlined text-[20px]"
                      >lock_reset</span
                    >
                  </button>
                </div>
              </td>
            </tr>
            <!-- Row 2: Mahasiswa -->
            <tr class="hover:bg-sidebar-light/30 transition-colors group">
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div
                    class="size-8 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs font-bold shrink-0"
                  >
                    BS
                  </div>
                  <span class="font-bold text-text-main">budi01</span>
                </div>
              </td>
              <td class="px-6 py-4 text-text-main text-sm font-medium">
                Budi Santoso
              </td>
              <td class="px-6 py-4 text-text-secondary">budi.mhs@univ.ac.id</td>
              <td class="px-6 py-4">
                <span
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300 border border-emerald-100 dark:border-emerald-800"
                >
                  <span class="material-symbols-outlined text-[14px]"
                    >backpack</span
                  >
                  Mahasiswa
                </span>
              </td>
              <td class="px-6 py-4 text-text-secondary text-xs">Yesterday</td>
              <td class="px-6 py-4 text-right">
                <div
                  class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity"
                >
                  <button
                    class="p-1.5 text-text-secondary hover:text-primary hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-md transition-colors"
                    title="Edit User"
                  >
                    <span class="material-symbols-outlined text-[20px]"
                      >edit</span
                    >
                  </button>
                  <button
                    class="p-1.5 text-text-secondary hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-md transition-colors"
                    title="Reset Password"
                  >
                    <span class="material-symbols-outlined text-[20px]"
                      >lock_reset</span
                    >
                  </button>
                </div>
              </td>
            </tr>
            <!-- Row 3: Admin -->
            <tr class="hover:bg-sidebar-light/30 transition-colors group">
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div
                    class="size-8 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xs font-bold shrink-0"
                  >
                    AD
                  </div>
                  <span class="font-bold text-text-main">admin_sys</span>
                </div>
              </td>
              <td class="px-6 py-4 text-text-main text-sm font-medium">
                System Administrator
              </td>
              <td class="px-6 py-4 text-text-secondary">admin@univ.ac.id</td>
              <td class="px-6 py-4">
                <span
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-bold bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300 border border-purple-100 dark:border-purple-800"
                >
                  <span class="material-symbols-outlined text-[14px]"
                    >security</span
                  >
                  Admin
                </span>
              </td>
              <td class="px-6 py-4">
                <span
                  class="inline-flex items-center gap-1.5 text-xs text-emerald-600 dark:text-emerald-400 font-bold bg-emerald-50 dark:bg-emerald-900/20 px-2 py-0.5 rounded-full"
                >
                  <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                  Online
                </span>
              </td>
              <td class="px-6 py-4 text-right">
                <div
                  class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity"
                >
                  <button
                    class="p-1.5 text-text-secondary hover:text-primary hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-md transition-colors"
                    title="Edit User"
                  >
                    <span class="material-symbols-outlined text-[20px]"
                      >edit</span
                    >
                  </button>
                  <button
                    class="p-1.5 text-text-secondary hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-md transition-colors"
                    title="Reset Password"
                  >
                    <span class="material-symbols-outlined text-[20px]"
                      >lock_reset</span
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
          Menampilkan 1-3 dari 24 User
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
