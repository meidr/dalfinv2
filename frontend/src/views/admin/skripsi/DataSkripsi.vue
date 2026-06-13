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
          <p class="text-text-main text-3xl font-bold leading-tight">
            {{ pagination.total }}
          </p>
          <div class="flex items-center gap-1 mt-1">
            <span class="material-symbols-outlined text-green-600 text-[18px]"
              >trending_up</span
            >
            <p class="text-green-600 text-xs font-semibold">Total terdaftar</p>
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
            Sedang Bimbingan
          </p>
          <div class="bg-orange-100 p-2 rounded-lg text-orange-600">
            <span class="material-symbols-outlined">pending_actions</span>
          </div>
        </div>
        <div class="mt-2">
          <p class="text-text-main text-3xl font-bold leading-tight">
            {{ statsCount.bimbingan }}
          </p>
          <div class="flex items-center gap-1 mt-1">
            <span class="material-symbols-outlined text-orange-600 text-[18px]"
              >groups</span
            >
            <p class="text-orange-600 text-xs font-semibold">Mahasiswa aktif</p>
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
          <p class="text-text-main text-3xl font-bold leading-tight">
            {{ statsCount.lulus }}
          </p>
          <div class="flex items-center gap-1 mt-1">
            <span class="material-symbols-outlined text-green-600 text-[18px]"
              >celebration</span
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
            v-model="searchQuery"
            @input="debouncedSearch"
            class="block w-full pl-10 pr-3 py-2.5 border border-border-light rounded-lg leading-5 bg-background-light text-text-main placeholder-text-secondary focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary sm:text-sm transition-shadow dark:bg-background"
            placeholder="Cari Mahasiswa, NIM, atau Judul..."
            type="text"
          />
        </div>
        <!-- Actions -->
        <div class="flex gap-3 w-full md:w-auto">
          <select
            v-model="filterStatus"
            @change="fetchSkripsi"
            class="px-4 py-2.5 bg-surface-light dark:bg-background border border-border-light rounded-lg text-text-secondary text-sm focus:ring-1 focus:ring-primary"
          >
            <option value="" class="dark:bg-background dark:text-text-main">
              Semua Status
            </option>
            <option
              value="proposal"
              class="dark:bg-background dark:text-text-main"
            >
              Proposal
            </option>
            <option
              value="sempro"
              class="dark:bg-background dark:text-text-main"
            >
              Seminar Proposal
            </option>
            <option
              value="penentuan_dospem"
              class="dark:bg-background dark:text-text-main"
            >
              Penentuan Dospem
            </option>
            <option
              value="bimbingan"
              class="dark:bg-background dark:text-text-main"
            >
              Bimbingan
            </option>
            <option
              v-if="authStore.semhasEnabled"
              value="semhas"
              class="dark:bg-background dark:text-text-main"
            >
              Seminar Hasil
            </option>
            <option
              value="sidang"
              class="dark:bg-background dark:text-text-main"
            >
              Sidang
            </option>
            <option
              value="revisi"
              class="dark:bg-background dark:text-text-main"
            >
              Revisi
            </option>
            <option
              value="lulus"
              class="dark:bg-background dark:text-text-main"
            >
              Lulus
            </option>
          </select>
          <Transition name="fade">
            <div v-if="selectedItems.length > 0" class="flex items-center gap-2">
              <span class="text-sm text-text-secondary font-medium whitespace-nowrap mr-2">
                {{ selectedItems.length }} dipilih
              </span>
              <button
                @click="bulkApproveStatus"
                class="inline-flex items-center gap-1.5 px-3 py-2.5 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors whitespace-nowrap shadow-sm shadow-green-500/20"
              >
                <span class="material-symbols-outlined text-[18px]">check_circle</span>
                Setujui Terpilih
              </button>
              <button
                @click="bulkRejectStatus"
                class="inline-flex items-center gap-1.5 px-3 py-2.5 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors whitespace-nowrap shadow-sm shadow-red-500/20"
              >
                <span class="material-symbols-outlined text-[18px]">cancel</span>
                Tolak Terpilih
              </button>
            </div>
          </Transition>
          <button
            @click="openAddModal"
            class="flex items-center justify-center gap-2 px-4 py-2.5 bg-primary hover:bg-blue-600 text-white rounded-lg font-medium text-sm shadow-sm shadow-blue-500/20 transition-all w-full md:w-auto whitespace-nowrap"
          >
            <span class="material-symbols-outlined text-[20px]">add</span>
            Tambah Data
          </button>
        </div>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="p-12 text-center">
        <div
          class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto"
        ></div>
        <p class="text-text-secondary text-sm mt-3">Memuat data...</p>
      </div>

      <!-- Table -->
      <DataTableScroll v-else>
        <table class="w-full text-left text-sm whitespace-nowrap">
          <thead
            class="bg-sidebar-light/50 text-text-secondary font-medium border-b border-border-light"
          >
            <tr>
              <th class="px-4 py-4 w-10">
                <input
                  v-if="hasPengajuanItems"
                  type="checkbox"
                  :checked="isAllSelected"
                  :indeterminate="isIndeterminate"
                  @change="toggleSelectAll"
                  class="size-4 rounded border-border-light text-primary focus:ring-primary cursor-pointer accent-primary"
                />
              </th>
              <th
                class="px-6 py-4 cursor-pointer hover:text-primary transition-colors select-none group"
                @click="handleSort('mahasiswa_nama')"
              >
                <div class="flex items-center gap-1">
                  Mahasiswa
                  <span
                    class="material-symbols-outlined text-[16px] text-text-secondary/50 group-hover:text-primary transition-colors"
                  >
                    {{ getSortIcon("mahasiswa_nama") }}
                  </span>
                </div>
              </th>
              <th
                class="px-6 py-4 cursor-pointer hover:text-primary transition-colors select-none group"
                @click="handleSort('judul')"
              >
                <div class="flex items-center gap-1">
                  Judul Skripsi
                  <span
                    class="material-symbols-outlined text-[16px] text-text-secondary/50 group-hover:text-primary transition-colors"
                  >
                    {{ getSortIcon("judul") }}
                  </span>
                </div>
              </th>
              <th class="px-6 py-4">Pembimbing</th>
              <th class="px-6 py-4">Tahun Akademik</th>
              <th class="px-6 py-4">Aktif</th>
              <th
                class="px-6 py-4 cursor-pointer hover:text-primary transition-colors select-none group"
                @click="handleSort('status')"
              >
                <div class="flex items-center gap-1">
                  Status
                  <span
                    class="material-symbols-outlined text-[16px] text-text-secondary/50 group-hover:text-primary transition-colors"
                  >
                    {{ getSortIcon("status") }}
                  </span>
                </div>
              </th>
              <th
                class="px-6 py-4 cursor-pointer hover:text-primary transition-colors select-none group text-right"
                @click="handleSort('created_at')"
              >
                <div class="flex items-center justify-end gap-1">
                  Tanggal
                  <span
                    class="material-symbols-outlined text-[16px] text-text-secondary/50 group-hover:text-primary transition-colors"
                  >
                    {{ getSortIcon("created_at") }}
                  </span>
                </div>
              </th>
              <th class="px-6 py-4 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border-light">
            <tr v-if="skripsiList.length === 0">
              <td
                colspan="9"
                class="px-6 py-12 text-center text-text-secondary"
              >
                Tidak ada data skripsi
              </td>
            </tr>
            <tr
              v-for="item in skripsiList"
              :key="item.id"
              class="group hover:bg-sidebar-light/30 transition-colors"
              :class="{ 'bg-primary/5': selectedItems.includes(item.id) }"
            >
              <td class="px-4 py-4">
                <input
                  v-if="item.status === 'pengajuan'"
                  type="checkbox"
                  :checked="selectedItems.includes(item.id)"
                  @change="toggleSelect(item.id)"
                  class="size-4 rounded border-border-light text-primary focus:ring-primary cursor-pointer accent-primary"
                />
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div
                    class="size-10 rounded-full flex items-center justify-center text-xs font-bold shrink-0"
                    :class="getAvatarColor(item.mahasiswa?.nama)"
                  >
                    {{ getInitials(item.mahasiswa?.nama) }}
                  </div>
                  <div>
                    <p class="font-bold text-text-main text-sm">
                      {{ item.mahasiswa?.nama || "-" }}
                    </p>
                    <p class="text-xs text-text-secondary font-medium">
                      {{ item.mahasiswa?.nim || "-" }}
                    </p>
                  </div>
                </div>
              </td>
              <td
                class="px-6 py-4 text-text-main max-w-xs truncate"
                :title="item.judul"
              >
                {{ item.judul || "-" }}
              </td>
              <td class="px-6 py-4 text-text-secondary">
                {{ getPembimbing(item.pembimbing) }}
              </td>
              <td class="px-6 py-4 text-text-secondary text-xs">
                {{ item.tahun_akademik?.name || "-" }}
              </td>
              <td class="px-6 py-4">
                <span
                  class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium"
                  :class="
                    item.is_active
                      ? 'bg-green-50 dark:bg-green-900/20 text-green-600 border border-green-100 dark:border-green-800'
                      : 'bg-red-50 dark:bg-red-900/20 text-red-600 border border-red-100 dark:border-red-800'
                  "
                >
                  <span
                    class="w-1.5 h-1.5 rounded-full"
                    :class="item.is_active ? 'bg-green-600' : 'bg-red-600'"
                  ></span>
                  {{ item.is_active ? "Aktif" : "Nonaktif" }}
                </span>
              </td>
              <td class="px-6 py-4">
                <span
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium"
                  :class="getStatusClass(item.status)"
                >
                  <span
                    class="w-1.5 h-1.5 rounded-full"
                    :class="getStatusDot(item.status)"
                  ></span>
                  {{ getStatusLabel(item.status) }}
                </span>
              </td>
              <td class="px-6 py-4 text-right text-text-secondary text-xs">
                {{ formatDate(item.created_at) }}
              </td>
              <td class="px-6 py-4 text-right">
                <div
                  class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity"
                >
                  <button
                    v-if="item.status === 'pengajuan'"
                    @click="updateStatusDirect(item, 'disetujui')"
                    class="p-2 text-green-600 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg transition-colors"
                    title="Setujui"
                  >
                    <span class="material-symbols-outlined text-[20px]">check_circle</span>
                  </button>
                  <button
                    v-if="item.status === 'pengajuan'"
                    @click="updateStatusDirect(item, 'ditolak')"
                    class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                    title="Tolak"
                  >
                    <span class="material-symbols-outlined text-[20px]">cancel</span>
                  </button>
                  <button
                    @click="viewDetail(item.id)"
                    class="p-2 text-text-secondary hover:text-primary hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
                    title="Lihat Detail"
                  >
                    <span class="material-symbols-outlined text-[20px]"
                      >visibility</span
                    >
                  </button>
                  <button
                    @click="openEditModal(item)"
                    class="p-2 text-text-secondary hover:text-primary hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
                    title="Edit"
                  >
                    <span class="material-symbols-outlined text-[20px]"
                      >edit</span
                    >
                  </button>
                  <button
                    @click="confirmDelete(item)"
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
      </DataTableScroll>

      <!-- Pagination -->
      <TablePagination
        :pagination="pagination"
        :disabled="loading"
        @page-change="goToPage"
        @per-page-change="changePerPage"
      />
    </div>

    <!-- Add/Edit Modal -->
    <Teleport to="body">
    <Transition name="modal-fade">
      <div
        v-if="showModal"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
      >
        <div
          class="bg-white dark:bg-surface-light rounded-xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto"
        >
          <div class="p-6 border-b border-border-light">
            <h2 class="text-xl font-bold text-text-main">
              {{ isEditing ? "Edit Skripsi" : "Tambah Skripsi" }}
            </h2>
          </div>
          <form @submit.prevent="saveSkripsi" class="p-6 space-y-4">
            <!-- Mahasiswa Selector (only for add) -->
            <div v-if="!isEditing" class="relative">
              <label class="block text-sm font-medium text-text-main mb-1"
                >Mahasiswa <span class="text-red-500">*</span></label
              >
              <div class="relative">
                <input
                  v-model="mahasiswaSearch"
                  @input="filterMahasiswa"
                  @focus="showMahasiswaDropdown = true"
                  type="text"
                  class="w-full px-3 py-2.5 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
                  placeholder="Cari nama atau NIM mahasiswa..."
                  :class="{ 'border-primary bg-primary/5': form.mahasiswa_id }"
                />
                <div
                  v-if="form.mahasiswa_id"
                  class="absolute right-3 top-1/2 -translate-y-1/2"
                >
                  <span
                    class="material-symbols-outlined text-primary text-[20px]"
                    >check_circle</span
                  >
                </div>
              </div>
              <!-- Selected Mahasiswa Display -->
              <div
                v-if="selectedMahasiswa"
                class="mt-2 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800"
              >
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-3">
                    <div
                      class="size-8 rounded-full bg-primary/20 text-primary flex items-center justify-center text-xs font-bold"
                    >
                      {{ getInitials(selectedMahasiswa.nama) }}
                    </div>
                    <div>
                      <p class="font-bold text-text-main text-sm">
                        {{ selectedMahasiswa.nama }}
                      </p>
                      <p class="text-xs text-text-secondary">
                        {{ selectedMahasiswa.nim }}
                      </p>
                    </div>
                  </div>
                  <button
                    type="button"
                    @click="clearMahasiswa"
                    class="text-text-secondary hover:text-red-500"
                  >
                    <span class="material-symbols-outlined text-[18px]"
                      >close</span
                    >
                  </button>
                </div>
              </div>
              <!-- Dropdown -->
              <div
                v-if="
                  showMahasiswaDropdown &&
                  filteredMahasiswa.length > 0 &&
                  !form.mahasiswa_id
                "
                class="absolute z-10 mt-1 w-full max-h-48 overflow-y-auto bg-white dark:bg-surface-light border border-border-light rounded-lg shadow-lg"
              >
                <button
                  v-for="mhs in filteredMahasiswa"
                  :key="mhs.id"
                  type="button"
                  @click="selectMahasiswa(mhs)"
                  class="w-full px-4 py-3 text-left hover:bg-primary/5 flex items-center gap-3 border-b border-border-light last:border-0"
                >
                  <div
                    class="size-8 rounded-full bg-gray-100 flex items-center justify-center text-xs font-bold text-text-secondary"
                  >
                    {{ getInitials(mhs.nama) }}
                  </div>
                  <div class="truncate max-w-[280px]">
                    <p class="font-medium text-text-main text-sm truncate">
                      {{ mhs.nama }}
                    </p>
                    <p class="text-xs text-text-secondary">
                      {{ mhs.nim }} • {{ mhs.prodi?.nama || "-" }}
                    </p>
                  </div>
                </button>
              </div>
              <p
                v-if="
                  showMahasiswaDropdown &&
                  filteredMahasiswa.length === 0 &&
                  mahasiswaSearch
                "
                class="mt-2 text-sm text-text-secondary italic"
              >
                Tidak ditemukan mahasiswa dengan kata kunci tersebut
              </p>
            </div>

            <div>
              <label class="block text-sm font-medium text-text-main mb-1"
                >Judul Skripsi <span class="text-red-500">*</span></label
              >
              <textarea
                v-model="form.judul"
                rows="3"
                class="w-full px-3 py-2 border border-border-light rounded-lg bg-white dark:bg-white/5 text-text-main focus:ring-2 focus:ring-primary/20 focus:border-primary"
                placeholder="Masukkan judul skripsi..."
                required
              ></textarea>
            </div>

            <div class="relative" ref="formTahunDropdownRef">
              <label class="block text-sm font-medium text-text-main mb-1"
                >Tahun Akademik</label
              >
              <button
                type="button"
                @click="formTahunDropdownOpen = !formTahunDropdownOpen"
                class="w-full px-3 py-2.5 border border-border-light rounded-lg bg-white dark:bg-white/5 text-text-main text-left flex items-center justify-between transition-colors text-sm"
              >
                <span>{{ getFormTahunLabel(form.th_akademik_id) }}</span>
                <span
                  class="material-symbols-outlined text-[18px] text-text-secondary transition-transform"
                  :class="{ 'rotate-180': formTahunDropdownOpen }"
                  >expand_more</span
                >
              </button>
              <Transition name="dropdown-fade">
                <div
                  v-if="formTahunDropdownOpen"
                  class="absolute top-full left-0 right-0 mt-1 bg-white dark:bg-sidebar-light border border-border-light rounded-lg shadow-xl z-20 py-1 max-h-60 overflow-y-auto"
                >
                  <button
                    type="button"
                    @click="
                      form.th_akademik_id = '';
                      formTahunDropdownOpen = false;
                    "
                    class="w-full px-3 py-2 text-left text-sm transition-colors flex items-center justify-between"
                    :class="
                      !form.th_akademik_id
                        ? 'bg-primary/10 text-primary font-bold'
                        : 'text-text-main hover:bg-gray-100 dark:hover:bg-white/10'
                    "
                  >
                    Pilih Tahun Akademik
                    <span
                      v-if="!form.th_akademik_id"
                      class="material-symbols-outlined text-[16px] text-primary"
                      >check</span
                    >
                  </button>
                  <button
                    v-for="t in tahunList"
                    :key="t.id"
                    type="button"
                    @click="
                      form.th_akademik_id = t.id;
                      formTahunDropdownOpen = false;
                    "
                    class="w-full px-3 py-2 text-left text-sm transition-colors flex items-center justify-between"
                    :class="
                      form.th_akademik_id === t.id
                        ? 'bg-primary/10 text-primary font-bold'
                        : 'text-text-main hover:bg-gray-100 dark:hover:bg-white/10'
                    "
                  >
                    <span class="flex flex-col">
                      <span>{{ t.name }}</span>
                      <span
                        v-if="t.semester"
                        class="text-xs font-normal text-text-secondary"
                        :class="
                          form.th_akademik_id === t.id ? 'text-primary/80' : ''
                        "
                      >
                        Semester {{ t.semester }}
                      </span>
                    </span>
                    <span
                      v-if="form.th_akademik_id === t.id"
                      class="material-symbols-outlined text-[16px] text-primary"
                      >check</span
                    >
                  </button>
                </div>
              </Transition>
            </div>

            <div v-if="isEditing">
              <label class="block text-sm font-medium text-text-main mb-1"
                >Alasan Perubahan <span class="text-red-500">*</span></label
              >
              <textarea
                v-model="form.alasan"
                rows="2"
                class="w-full px-3 py-2 border border-border-light rounded-lg bg-white dark:bg-white/5 text-text-main focus:ring-2 focus:ring-primary/20 focus:border-primary"
                placeholder="Jelaskan alasan perubahan..."
                required
              ></textarea>
            </div>

            <div class="relative" ref="formStatusDropdownRef">
              <label class="block text-sm font-medium text-text-main mb-1"
                >Status</label
              >
              <button
                type="button"
                @click="formStatusDropdownOpen = !formStatusDropdownOpen"
                class="w-full px-3 py-2.5 border border-border-light rounded-lg bg-white dark:bg-white/5 text-text-main text-left flex items-center justify-between transition-colors text-sm"
              >
                <span>{{ getFormStatusLabel(form.status) }}</span>
                <span
                  class="material-symbols-outlined text-[18px] text-text-secondary transition-transform"
                  :class="{ 'rotate-180': formStatusDropdownOpen }"
                  >expand_more</span
                >
              </button>
              <Transition name="dropdown-fade">
                <div
                  v-if="formStatusDropdownOpen"
                  class="absolute top-full left-0 right-0 mt-1 bg-white dark:bg-sidebar-light border border-border-light rounded-lg shadow-xl z-20 py-1 max-h-60 overflow-y-auto"
                >
                  <button
                    v-for="opt in formStatusOptions"
                    :key="opt.value"
                    type="button"
                    @click="
                      form.status = opt.value;
                      formStatusDropdownOpen = false;
                    "
                    class="w-full px-3 py-2 text-left text-sm transition-colors flex items-center justify-between"
                    :class="
                      form.status === opt.value
                        ? 'bg-primary/10 text-primary font-bold'
                        : 'text-text-main hover:bg-gray-100 dark:hover:bg-white/10'
                    "
                  >
                    {{ opt.label }}
                    <span
                      v-if="form.status === opt.value"
                      class="material-symbols-outlined text-[16px] text-primary"
                      >check</span
                    >
                  </button>
                </div>
              </Transition>
            </div>

            <div class="relative" ref="formActiveDropdownRef">
              <label class="block text-sm font-medium text-text-main mb-1"
                >Status Aktif</label
              >
              <button
                type="button"
                @click="formActiveDropdownOpen = !formActiveDropdownOpen"
                class="w-full px-3 py-2.5 border border-border-light rounded-lg bg-white dark:bg-white/5 text-text-main text-left flex items-center justify-between transition-colors text-sm"
              >
                <span>{{ form.is_active ? "Aktif" : "Nonaktif" }}</span>
                <span
                  class="material-symbols-outlined text-[18px] text-text-secondary transition-transform"
                  :class="{ 'rotate-180': formActiveDropdownOpen }"
                  >expand_more</span
                >
              </button>
              <Transition name="dropdown-fade">
                <div
                  v-if="formActiveDropdownOpen"
                  class="absolute top-full left-0 right-0 mt-1 bg-white dark:bg-sidebar-light border border-border-light rounded-lg shadow-xl z-20 py-1"
                >
                  <button
                    type="button"
                    @click="
                      form.is_active = true;
                      formActiveDropdownOpen = false;
                    "
                    class="w-full px-3 py-2 text-left text-sm transition-colors flex items-center justify-between"
                    :class="
                      form.is_active
                        ? 'bg-primary/10 text-primary font-bold'
                        : 'text-text-main hover:bg-gray-100 dark:hover:bg-white/10'
                    "
                  >
                    Aktif
                    <span
                      v-if="form.is_active"
                      class="material-symbols-outlined text-[16px] text-primary"
                      >check</span
                    >
                  </button>
                  <button
                    type="button"
                    @click="
                      form.is_active = false;
                      formActiveDropdownOpen = false;
                    "
                    class="w-full px-3 py-2 text-left text-sm transition-colors flex items-center justify-between"
                    :class="
                      !form.is_active
                        ? 'bg-primary/10 text-primary font-bold'
                        : 'text-text-main hover:bg-gray-100 dark:hover:bg-white/10'
                    "
                  >
                    Nonaktif
                    <span
                      v-if="!form.is_active"
                      class="material-symbols-outlined text-[16px] text-primary"
                      >check</span
                    >
                  </button>
                </div>
              </Transition>
              <p class="mt-1 text-xs text-text-secondary">
                Jika dipilih <strong>Aktif</strong>, skripsi lain milik
                mahasiswa ini akan otomatis menjadi <strong>Nonaktif</strong>.
              </p>
            </div>

            <div
              v-if="
                ['proposal', 'sempro', 'semhas', 'revisi'].includes(form.status)
              "
            >
              <label class="block text-sm font-medium text-text-main mb-1">
                File Skripsi (PDF/Word) <span class="text-red-500">*</span>
              </label>
              <input
                type="file"
                ref="fileInput"
                @change="handleFileChange"
                accept=".pdf,.doc,.docx"
                class="w-full px-3 py-2 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20"
              />
              <p
                v-if="isEditing && form.file_skripsi"
                class="mt-1 text-xs text-text-secondary"
              >
                File saat ini:
                <a
                  :href="form.file_url"
                  target="_blank"
                  class="text-primary hover:underline"
                  >Lihat File</a
                >
              </p>
            </div>
            <div class="flex gap-3 pt-4">
              <button
                type="button"
                @click="closeModal"
                class="flex-1 px-4 py-2.5 border border-border-light rounded-lg text-text-secondary hover:bg-gray-50 dark:hover:bg-white/5 transition-colors"
              >
                Batal
              </button>
              <button
                type="submit"
                :disabled="saving"
                class="flex-1 px-4 py-2.5 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors disabled:opacity-50"
              >
                {{ saving ? "Menyimpan..." : "Simpan" }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Transition>
    </Teleport>

    <!-- Delete Confirmation Modal -->
    <Teleport to="body">
    <Transition name="modal-fade">
      <div
        v-if="showDeleteModal"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
      >
        <div
          class="bg-white dark:bg-surface-light rounded-xl shadow-2xl w-full max-w-md"
        >
          <div class="p-6">
            <div class="flex items-center gap-4 mb-4">
              <div class="p-3 bg-red-100 text-red-600 rounded-full">
                <span class="material-symbols-outlined">warning</span>
              </div>
              <div>
                <h3 class="text-lg font-bold text-text-main">Hapus Skripsi?</h3>
                <p class="text-sm text-text-secondary">
                  Tindakan ini tidak dapat dibatalkan.
                </p>
              </div>
            </div>
            <p class="text-text-main mb-6">
              Apakah Anda yakin ingin menghapus skripsi
              <strong>"{{ deleteItem?.judul }}"</strong>?
            </p>
            <div class="flex gap-3">
              <button
                @click="showDeleteModal = false"
                class="flex-1 px-4 py-2.5 border border-border-light rounded-lg text-text-secondary hover:bg-background-light transition-colors"
              >
                Batal
              </button>
              <button
                @click="deleteSkripsi"
                :disabled="deleting"
                class="flex-1 px-4 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50"
              >
                {{ deleting ? "Menghapus..." : "Hapus" }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, reactive } from "vue";
import { useRouter } from "vue-router";
import adminService from "../../../services/adminService";
import { useAuthStore } from "../../../stores/auth";

const router = useRouter();
const authStore = useAuthStore();

const loading = ref(true);
const saving = ref(false);
const deleting = ref(false);
const skripsiList = ref([]);
const selectedItems = ref([]);

const hasPengajuanItems = computed(() => {
  return skripsiList.value.some(item => item.status === 'pengajuan');
});

const isAllSelected = computed(() => {
  const pengajuanItems = skripsiList.value.filter(item => item.status === 'pengajuan');
  return pengajuanItems.length > 0 && pengajuanItems.every(item => selectedItems.value.includes(item.id));
});

const isIndeterminate = computed(() => {
  return selectedItems.value.length > 0 && !isAllSelected.value;
});

const toggleSelectAll = () => {
  if (isAllSelected.value) {
    selectedItems.value = [];
  } else {
    selectedItems.value = skripsiList.value.filter(item => item.status === 'pengajuan').map(item => item.id);
  }
};

const toggleSelect = (id) => {
  const idx = selectedItems.value.indexOf(id);
  if (idx >= 0) {
    selectedItems.value.splice(idx, 1);
  } else {
    selectedItems.value.push(id);
  }
};

const updateStatusDirect = async (item, newStatus) => {
  if (newStatus === 'disetujui') {
    const hasActive = skripsiList.value.some(s => s.mahasiswa_id === item.mahasiswa_id && s.id !== item.id && s.is_active);
    let msg = `Apakah Anda yakin ingin menyetujui pengajuan ini?`;
    if (hasActive) {
      msg = `Peringatan: Mahasiswa ini sudah memiliki judul skripsi yang aktif.\nMenyetujui pengajuan ini akan otomatis menonaktifkan judul yang aktif tersebut.\nApakah Anda ingin melanjutkan?`;
    }
    if (!confirm(msg)) return;
  } else {
    if (!confirm(`Apakah Anda yakin ingin menolak pengajuan ini?`)) return;
  }

  try {
    loading.value = true;
    const formData = new FormData();
    formData.append("_method", "PUT");
    formData.append("status", newStatus);
    if (newStatus === 'disetujui') {
      formData.append("is_active", true);
    }
    formData.append("alasan", `Status diubah menjadi ${newStatus} secara langsung`);
    const response = await adminService.updateSkripsi(item.id, formData);
    if (response && response.message) {
      alert(response.message);
    }
    fetchSkripsi();
  } catch (error) {
    console.error("Failed to update status:", error);
    alert("Gagal mengupdate status: " + (error.response?.data?.message || error.message));
    loading.value = false;
  }
};

const bulkApproveStatus = async () => {
  const hasActive = selectedItems.value.some(id => {
    const item = skripsiList.value.find(s => s.id === id);
    if (!item) return false;
    return skripsiList.value.some(s => s.mahasiswa_id === item.mahasiswa_id && s.id !== item.id && s.is_active);
  });
  
  let msg = `Setujui ${selectedItems.value.length} pengajuan skripsi terpilih?`;
  if (hasActive) {
    msg = `Peringatan: Beberapa mahasiswa yang dipilih sudah memiliki judul skripsi aktif.\nMenyetujui pengajuan ini akan menonaktifkan judul mereka sebelumnya.\nLanjutkan menyetujui ${selectedItems.value.length} pengajuan?`;
  }
  
  if (!confirm(msg)) return;

  try {
    loading.value = true;
    for (const id of selectedItems.value) {
      const formData = new FormData();
      formData.append("_method", "PUT");
      formData.append("status", "disetujui");
      formData.append("is_active", true);
      formData.append("alasan", "Pengajuan disetujui secara massal");
      await adminService.updateSkripsi(id, formData);
    }
    selectedItems.value = [];
    fetchSkripsi();
  } catch (error) {
    console.error("Failed to bulk update status:", error);
    alert("Gagal menyetujui pengajuan secara massal.");
    loading.value = false;
  }
};

const bulkRejectStatus = async () => {
  if (!confirm(`Tolak ${selectedItems.value.length} pengajuan skripsi terpilih?`)) return;
  try {
    loading.value = true;
    for (const id of selectedItems.value) {
      const formData = new FormData();
      formData.append("_method", "PUT");
      formData.append("status", "ditolak");
      formData.append("alasan", "Pengajuan ditolak secara massal");
      await adminService.updateSkripsi(id, formData);
    }
    selectedItems.value = [];
    fetchSkripsi();
  } catch (error) {
    console.error("Failed to bulk update status:", error);
    alert("Gagal menolak pengajuan secara massal.");
    loading.value = false;
  }
};

const searchQuery = ref("");
const filterStatus = ref("");
const showModal = ref(false);
const showDeleteModal = ref(false);
const isEditing = ref(false);
const deleteItem = ref(null);
const formStatusDropdownOpen = ref(false);
const formActiveDropdownOpen = ref(false);
const formTahunDropdownOpen = ref(false);
const formStatusDropdownRef = ref(null);
const formActiveDropdownRef = ref(null);
const formTahunDropdownRef = ref(null);

const formStatusOptionsList = [
  { value: "pengajuan", label: "Pengajuan" },
  { value: "proposal", label: "Proposal" },
  { value: "sempro", label: "Seminar Proposal" },
  { value: "penentuan_dospem", label: "Penentuan Dospem" },
  { value: "bimbingan", label: "Bimbingan" },
  { value: "semhas", label: "Seminar Hasil" },
  { value: "sidang", label: "Sidang" },
  { value: "revisi", label: "Revisi" },
  { value: "lulus", label: "Lulus" },
];

const formStatusOptions = computed(() =>
  formStatusOptionsList.filter(
    (opt) => opt.value !== "semhas" || authStore.semhasEnabled,
  ),
);

const getFormStatusLabel = (value) => {
  const found = formStatusOptionsList.find((o) => o.value === value);
  return found ? found.label : value;
};

const getFormTahunLabel = (value) => {
  if (!value) return "Pilih Tahun Akademik";
  const found = tahunList.value.find((t) => t.id === value);
  if (!found) return value;
  return found.semester ? `${found.name} - Semester ${found.semester}` : found.name;
};

const handleFormDropdownClickOutside = (e) => {
  if (
    formStatusDropdownRef.value &&
    !formStatusDropdownRef.value.contains(e.target)
  ) {
    formStatusDropdownOpen.value = false;
  }
  if (
    formActiveDropdownRef.value &&
    !formActiveDropdownRef.value.contains(e.target)
  ) {
    formActiveDropdownOpen.value = false;
  }
  if (
    formTahunDropdownRef.value &&
    !formTahunDropdownRef.value.contains(e.target)
  ) {
    formTahunDropdownOpen.value = false;
  }
};

const sorting = reactive({
  by: "created_at",
  order: "desc",
});

const pagination = reactive({
  current_page: 1,
  last_page: 1,
  per_page: 15,
  total: 0,
  from: 0,
  to: 0,
});

const statsCount = reactive({
  bimbingan: 0,
  lulus: 0,
});

// Mahasiswa search
const mahasiswaList = ref([]);
const mahasiswaSearch = ref("");
const showMahasiswaDropdown = ref(false);
const filteredMahasiswa = ref([]);
const selectedMahasiswa = ref(null);

// Tahun akademik list
const tahunList = ref([]);

let searchTimeout = null;

const fetchSkripsi = async () => {
  try {
    loading.value = true;
    const params = {
      page: pagination.current_page,
      per_page: pagination.per_page,
      search: searchQuery.value,
      status: filterStatus.value,
      sort_by: sorting.by,
      sort_order: sorting.order,
    };
    const response = await adminService.getSkripsi(params);
    if (response.success) {
      skripsiList.value = response.data.data;
      Object.assign(pagination, {
        current_page: response.data.current_page,
        last_page: response.data.last_page,
        per_page: response.data.per_page,
        total: response.data.total,
        from: response.data.from,
        to: response.data.to,
      });
    }
  } catch (error) {
    console.error("Failed to fetch skripsi:", error);
  } finally {
    loading.value = false;
  }
};

const fetchStats = async () => {
  try {
    const response = await adminService.getDashboard();
    if (response.success) {
      const data = response.data;
      statsCount.bimbingan = data.skripsi_bimbingan || 0;
      statsCount.lulus = data.skripsi_lulus || 0;
    }
  } catch (error) {
    console.error("Failed to fetch stats:", error);
  }
};

const debouncedSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    pagination.current_page = 1;
    fetchSkripsi();
  }, 300);
};

const goToPage = (page) => {
  if (page >= 1 && page <= pagination.last_page) {
    pagination.current_page = page;
    fetchSkripsi();
  }
};

const changePerPage = (perPage) => {
  pagination.per_page = perPage;
  pagination.current_page = 1;
  fetchSkripsi();
};

const handleSort = (column) => {
  if (sorting.by === column) {
    sorting.order = sorting.order === "asc" ? "desc" : "asc";
  } else {
    sorting.by = column;
    sorting.order = "asc";
  }
  fetchSkripsi();
};

const getSortIcon = (column) => {
  if (sorting.by !== column) return "unfold_more";
  return sorting.order === "asc" ? "expand_less" : "expand_more";
};

const form = reactive({
  id: null,
  mahasiswa_id: null,
  judul: "",
  status: "pengajuan",
  is_active: true,
  alasan: "",
  th_akademik_id: "",
  file_skripsi: null,
  file_url: null,
});

const fetchMahasiswa = async (search = "") => {
  try {
    const response = await adminService.getMahasiswa({
      per_page: 30,
      search: search,
    });
    if (response.success) {
      mahasiswaList.value = response.data.data || response.data;
      filteredMahasiswa.value = search ? mahasiswaList.value : [];
    }
  } catch (error) {
    console.error("Failed to fetch mahasiswa:", error);
  }
};

let mahasiswaSearchTimeout = null;
const filterMahasiswa = () => {
  const search = mahasiswaSearch.value.trim();
  if (!search) {
    filteredMahasiswa.value = [];
    return;
  }
  clearTimeout(mahasiswaSearchTimeout);
  mahasiswaSearchTimeout = setTimeout(() => {
    fetchMahasiswa(search);
  }, 300);
};

const selectMahasiswa = (mhs) => {
  if (mhs.status === "lulus") {
    alert(
      "Mahasiswa " +
        mhs.nama +
        " sudah berstatus LULUS. Tidak dapat menambahkan data skripsi baru.",
    );
    return;
  }
  form.mahasiswa_id = mhs.id;
  selectedMahasiswa.value = mhs;
  mahasiswaSearch.value = mhs.nama;
  showMahasiswaDropdown.value = false;
};

const clearMahasiswa = () => {
  form.mahasiswa_id = null;
  selectedMahasiswa.value = null;
  mahasiswaSearch.value = "";
};

const handleFileChange = (e) => {
  const file = e.target.files[0];
  if (file) {
    if (file.size > 10 * 1024 * 1024) {
      alert("Ukuran file maksimal 10MB");
      e.target.value = "";
      form.file_skripsi = null;
      return;
    }
    form.file_skripsi = file;
  }
};

const openAddModal = () => {
  isEditing.value = false;
  form.id = null;
  form.mahasiswa_id = null;
  form.judul = "";
  form.status = "pengajuan";
  form.is_active = true;
  form.alasan = "";
  form.th_akademik_id = "";
  form.file_skripsi = null;
  form.file_url = null;
  selectedMahasiswa.value = null;
  mahasiswaSearch.value = "";
  showMahasiswaDropdown.value = false;
  showModal.value = true;
};

const openEditModal = (item) => {
  isEditing.value = true;
  form.id = item.id;
  form.judul = item.judul;
  form.status = item.status;
  form.is_active = item.is_active ?? true;
  form.alasan = "";
  form.th_akademik_id = item.th_akademik_id || "";
  form.file_skripsi = item.file_skripsi; // Store boolean/string presence
  form.file_url = item.file_url; // Virtual attribute from backend
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  form.file_skripsi = null;
};

const saveSkripsi = async () => {
  try {
    saving.value = true;

    // Validation for required file
    const requiredFileStatuses = ["proposal", "sempro", "semhas", "revisi"];
    if (requiredFileStatuses.includes(form.status)) {
      // If adding new, file is mandatory
      if (!isEditing.value && !form.file_skripsi) {
        alert("File Skripsi wajib diupload untuk status " + form.status);
        saving.value = false;
        return;
      }
      // If editing, file is mandatory only if not already present
      if (isEditing.value && !form.file_skripsi && !form.file_url) {
        alert("File Skripsi wajib diupload untuk status " + form.status);
        saving.value = false;
        return;
      }
    }

    const formData = new FormData();
    if (form.mahasiswa_id) formData.append("mahasiswa_id", form.mahasiswa_id);
    formData.append("is_active", form.is_active ? "1" : "0");
    formData.append("judul", form.judul);
    formData.append("status", form.status);
    if (form.file_skripsi instanceof File) {
      formData.append("file_skripsi", form.file_skripsi);
    }
    if (form.th_akademik_id) {
      formData.append("th_akademik_id", form.th_akademik_id);
    }

    if (isEditing.value) {
      if (!form.alasan) {
        alert("Mohon isi alasan perubahan.");
        saving.value = false;
        return;
      }
      formData.append("alasan", form.alasan);
    }

    // For update PUT via FormData in Laravel, we need _method: PUT
    let response;
    if (isEditing.value) {
      formData.append("_method", "PUT");
      response = await adminService.updateSkripsi(form.id, formData);
    } else {
      if (!form.mahasiswa_id) {
        alert("Pilih mahasiswa terlebih dahulu");
        saving.value = false;
        return;
      }
      response = await adminService.createSkripsi(formData);
    }

    closeModal();
    if (response && response.message) {
      alert(response.message);
    }
    fetchSkripsi();
    fetchStats();
  } catch (error) {
    console.error("Failed to save skripsi:", error);
    alert(
      "Gagal menyimpan data: " +
        (error.response?.data?.message || error.message),
    );
  } finally {
    saving.value = false;
  }
};

const confirmDelete = (item) => {
  deleteItem.value = item;
  showDeleteModal.value = true;
};

const deleteSkripsi = async () => {
  try {
    deleting.value = true;
    await adminService.deleteSkripsi(deleteItem.value.id);
    showDeleteModal.value = false;
    fetchSkripsi();
    fetchStats();
  } catch (error) {
    console.error("Failed to delete skripsi:", error);
    alert(
      "Gagal menghapus data: " +
        (error.response?.data?.message || error.message),
    );
  } finally {
    deleting.value = false;
  }
};

const viewDetail = (id) => {
  router.push(`/admin/skripsi/${id}`);
};

const getInitials = (name) => {
  if (!name) return "?";
  return name
    .split(" ")
    .map((n) => n[0])
    .join("")
    .substring(0, 2)
    .toUpperCase();
};

const getAvatarColor = (name) => {
  const colors = [
    "bg-blue-100 text-blue-600",
    "bg-purple-100 text-purple-600",
    "bg-orange-100 text-orange-600",
    "bg-green-100 text-green-600",
  ];
  if (!name) return colors[0];
  const index = name.charCodeAt(0) % colors.length;
  return colors[index];
};

const getPembimbing = (pembimbingList) => {
  if (!pembimbingList || pembimbingList.length === 0) return "-";
  const p1 = pembimbingList.find((p) => p.jenis === "pembimbing_1");
  const p2 = pembimbingList.find((p) => p.jenis === "pembimbing_2");
  const name1 = p1?.dosen?.full_name || p1?.dosen?.nama;
  const name2 = p2?.dosen?.full_name || p2?.dosen?.nama;
  if (name1 && name2) return `${name1}, ${name2}`;
  if (name1) return name1;
  if (name2) return name2;
  return "-";
};

const getStatusClass = (status) => {
  const classes = {
    pengajuan: "bg-gray-50 text-gray-600 border border-gray-100",
    disetujui: "bg-green-100 text-green-700 border border-green-200",
    ditolak: "bg-red-100 text-red-700 border border-red-200",
    proposal: "bg-yellow-50 text-yellow-600 border border-yellow-100",
    bimbingan: "bg-purple-50 text-purple-600 border border-purple-100",
    sempro: "bg-blue-50 text-blue-600 border border-blue-100",
    penentuan_dospem: "bg-indigo-50 text-indigo-600 border border-indigo-100",
    semhas: "bg-cyan-50 text-cyan-600 border border-cyan-100",
    sidang: "bg-orange-50 text-orange-600 border border-orange-100",
    revisi: "bg-amber-50 text-amber-600 border border-amber-100",
    lulus: "bg-green-50 text-green-600 border border-green-100",
  };
  return classes[status] || "bg-gray-50 text-gray-600 border border-gray-100";
};

const getStatusDot = (status) => {
  const dots = {
    pengajuan: "bg-gray-500",
    disetujui: "bg-green-600",
    ditolak: "bg-red-600",
    proposal: "bg-yellow-600",
    bimbingan: "bg-purple-600",
    sempro: "bg-blue-600",
    penentuan_dospem: "bg-indigo-600",
    semhas: "bg-cyan-600",
    sidang: "bg-orange-600",
    revisi: "bg-amber-600",
    lulus: "bg-green-600",
  };
  return dots[status] || "bg-gray-600";
};

const getStatusLabel = (status) => {
  const labels = {
    pengajuan: "Pengajuan",
    disetujui: "Disetujui",
    ditolak: "Ditolak",
    proposal: "Proposal",
    bimbingan: "Bimbingan",
    sempro: "Sem. Proposal",
    penentuan_dospem: "Dospem",
    semhas: "Sem. Hasil",
    sidang: "Sidang",
    revisi: "Revisi",
    lulus: "Lulus",
  };
  return labels[status] || status;
};

const formatDate = (date) => {
  if (!date) return "-";
  return new Date(date).toLocaleDateString("id-ID", {
    day: "numeric",
    month: "short",
    year: "numeric",
  });
};

onMounted(async () => {
  fetchSkripsi();
  fetchStats();
  document.addEventListener("click", handleFormDropdownClickOutside);
  try {
    const tahunRes = await adminService.getTahun();
    if (tahunRes.success) {
      tahunList.value = tahunRes.data || [];
    }
  } catch (e) {
    console.error("Failed to load tahun list:", e);
  }
});

onBeforeUnmount(() => {
  document.removeEventListener("click", handleFormDropdownClickOutside);
});
</script>

<style scoped>
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.2s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}

.dropdown-fade-enter-active,
.dropdown-fade-leave-active {
  transition: all 0.15s ease;
}

.dropdown-fade-enter-from,
.dropdown-fade-leave-to {
  opacity: 0;
  transform: translateY(-4px);
}

:global(.dark) select {
  color-scheme: dark;
}
</style>
