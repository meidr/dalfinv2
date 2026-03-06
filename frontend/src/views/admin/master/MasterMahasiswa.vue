<template>
  <div class="max-w-7xl mx-auto flex flex-col gap-8 animate-fade-in-up">
    <div
      class="flex flex-col md:flex-row md:items-center justify-between gap-4"
    >
      <div class="flex flex-col gap-1">
        <h1 class="text-text-main text-3xl font-bold leading-tight">
          Master Data Mahasiswa
        </h1>
        <p class="text-text-secondary text-sm font-normal">
          Kelola data mahasiswa yang terdaftar untuk skripsi.
        </p>
      </div>
      <div class="flex flex-col sm:flex-row items-center gap-3">
        <div class="relative w-full sm:w-64 group">
          <span
            class="material-symbols-outlined absolute left-3 top-2.5 text-text-secondary text-[20px] group-focus-within:text-primary transition-colors"
            >search</span
          >
          <input
            v-model="searchQuery"
            @input="debouncedSearch"
            class="w-full pl-10 pr-4 py-2 bg-surface-light border border-border-light rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none"
            placeholder="Cari Nama, NIM..."
            type="text"
          />
        </div>
        <button
          @click="openSyncModal"
          class="flex items-center justify-center gap-2 bg-teal-600 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm shadow-teal-600/20 hover:bg-teal-700 transition-all w-full sm:w-auto"
        >
          <span class="material-symbols-outlined text-[20px]">sync</span>
          <span>Sinkronisasi</span>
        </button>
        <button
          @click="showImportModal = true"
          class="flex items-center justify-center gap-2 bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm shadow-green-600/20 hover:bg-green-700 transition-all w-full sm:w-auto"
        >
          <span class="material-symbols-outlined text-[20px]">upload_file</span>
          <span>Import</span>
        </button>
        <button
          @click="openAddModal"
          class="flex items-center justify-center gap-2 bg-primary text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm shadow-primary/20 hover:bg-primary/90 transition-all w-full sm:w-auto"
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
          <p class="text-lg font-bold text-text-main">
            {{ statsCount.active }}
          </p>
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
          <p class="text-lg font-bold text-text-main">{{ pagination.total }}</p>
        </div>
      </div>
    </div>

    <!-- Table Container -->
    <div
      class="flex flex-col rounded-xl border border-border-light bg-surface-light overflow-hidden shadow-sm"
    >
      <!-- Loading -->
      <div v-if="loading" class="p-12 text-center">
        <div
          class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto"
        ></div>
        <p class="text-text-secondary text-sm mt-3">Memuat data...</p>
      </div>

      <template v-else>
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm whitespace-nowrap">
            <thead
              class="bg-sidebar-light/50 text-text-secondary font-medium border-b border-border-light"
            >
              <tr>
                <th class="px-6 py-4 w-12">
                  <input
                    type="checkbox"
                    class="rounded border-border-light text-primary focus:ring-primary size-4"
                  />
                </th>
                <th class="px-6 py-4">NIM</th>
                <th class="px-6 py-4">Mahasiswa</th>
                <th class="px-6 py-4">Tahun</th>
                <th class="px-6 py-4">Program Studi</th>
                <th class="px-6 py-4">Status</th>
                <th class="px-6 py-4 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-border-light">
              <tr v-if="mahasiswaList.length === 0">
                <td colspan="7" class="p-12 text-center text-text-secondary">
                  Tidak ada data mahasiswa
                </td>
              </tr>
              <tr
                v-for="item in mahasiswaList"
                :key="item.id"
                class="group hover:bg-sidebar-light/30 transition-colors"
              >
                <td class="px-6 py-4">
                  <div class="flex items-center justify-center">
                    <input
                      type="checkbox"
                      class="rounded border-border-light text-primary focus:ring-primary size-4"
                    />
                  </div>
                </td>
                <td class="px-6 py-4 font-mono text-text-main">
                  {{ item.nim }}
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <div
                      class="size-8 rounded-full flex items-center justify-center font-bold text-xs shrink-0"
                      :class="getAvatarColor(item.nama)"
                    >
                      {{ getInitials(item.nama) }}
                    </div>
                    <span class="font-bold text-text-main">{{
                      item.nama
                    }}</span>
                  </div>
                </td>
                <td class="px-6 py-4 text-text-secondary">
                  {{ item.tahun?.name || "-" }}
                </td>
                <td class="px-6 py-4 text-text-secondary">
                  {{ item.prodi?.nama || "-" }}
                </td>
                <td class="px-6 py-4">
                  <span
                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium"
                    :class="
                      item.user?.is_active
                        ? 'bg-green-50 text-green-700 border border-green-200'
                        : 'bg-gray-100 text-gray-700 border border-gray-200'
                    "
                  >
                    <span
                      class="w-1.5 h-1.5 rounded-full"
                      :class="
                        item.user?.is_active ? 'bg-green-600' : 'bg-gray-500'
                      "
                    ></span>
                    {{ item.user?.is_active ? "Aktif" : "Non-Aktif" }}
                  </span>
                </td>
                <td class="px-6 py-4 text-right">
                  <div
                    class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity"
                  >
                    <button
                      @click="openEditModal(item)"
                      class="p-1.5 text-text-secondary hover:text-primary hover:bg-background-light rounded-md transition-colors"
                      title="Edit"
                    >
                      <span class="material-symbols-outlined text-[18px]"
                        >edit</span
                      >
                    </button>
                    <button
                      @click="confirmDelete(item)"
                      class="p-1.5 text-text-secondary hover:text-red-500 hover:bg-background-light rounded-md transition-colors"
                      title="Delete"
                    >
                      <span class="material-symbols-outlined text-[18px]"
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
            Menampilkan
            <span class="font-medium text-text-main">{{
              pagination.from || 0
            }}</span>
            sampai
            <span class="font-medium text-text-main">{{
              pagination.to || 0
            }}</span>
            dari
            <span class="font-medium text-text-main">{{
              pagination.total
            }}</span>
            data
          </p>
          <div class="flex gap-2">
            <button
              @click="goToPage(pagination.current_page - 1)"
              :disabled="pagination.current_page <= 1"
              class="px-3 py-1.5 rounded-md border border-border-light text-text-secondary text-sm font-medium hover:bg-background-light disabled:opacity-50"
            >
              <span class="material-symbols-outlined text-sm"
                >chevron_left</span
              >
            </button>
            <button
              class="px-3 py-1.5 rounded-md bg-primary text-white text-sm font-medium"
            >
              {{ pagination.current_page }}
            </button>
            <button
              @click="goToPage(pagination.current_page + 1)"
              :disabled="pagination.current_page >= pagination.last_page"
              class="px-3 py-1.5 rounded-md border border-border-light text-text-secondary text-sm font-medium hover:bg-background-light disabled:opacity-50"
            >
              <span class="material-symbols-outlined text-sm"
                >chevron_right</span
              >
            </button>
          </div>
        </div>
      </template>
    </div>

    <!-- Add/Edit Modal -->
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
              {{ isEditing ? "Edit Mahasiswa" : "Tambah Mahasiswa" }}
            </h2>
          </div>
          <form @submit.prevent="saveMahasiswa" class="p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-text-main mb-1"
                  >NIM</label
                >
                <input
                  v-model="form.nim"
                  type="text"
                  class="w-full px-3 py-2 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
                  required
                  :disabled="isEditing"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-text-main mb-1"
                  >Tahun</label
                >
                <select
                  v-model="form.tahun_id"
                  class="w-full px-3 py-2 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary bg-white dark:bg-surface-light"
                  required
                >
                  <option value="" disabled>Pilih Tahun</option>
                  <option v-for="t in tahunList" :key="t.id" :value="t.id">
                    {{ t.name }}
                  </option>
                </select>
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-text-main mb-1"
                >Nama Lengkap</label
              >
              <input
                v-model="form.nama"
                type="text"
                class="w-full px-3 py-2 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
                required
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-text-main mb-1"
                >Email</label
              >
              <input
                v-model="form.email"
                type="email"
                class="w-full px-3 py-2 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
                required
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-text-main mb-1"
                >Program Studi</label
              >
              <select
                v-model="form.prodi_id"
                class="w-full px-3 py-2 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary bg-white dark:bg-surface-light"
                required
              >
                <option value="" disabled>Pilih Program Studi</option>
                <option v-for="p in prodiList" :key="p.id" :value="p.id">
                  {{ p.nama }}
                </option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-text-main mb-1"
                >Jenis Kelamin</label
              >
              <select
                v-model="form.jenis_kelamin"
                class="w-full px-3 py-2 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary bg-white dark:bg-surface-light"
                required
              >
                <option value="" disabled>Pilih Jenis Kelamin</option>
                <option value="L">Laki-laki</option>
                <option value="P">Perempuan</option>
              </select>
            </div>
            <div v-if="!isEditing">
              <label class="block text-sm font-medium text-text-main mb-1"
                >Password</label
              >
              <input
                v-model="form.password"
                type="password"
                class="w-full px-3 py-2 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
                :required="!isEditing"
              />
            </div>
            <div class="flex gap-3 pt-4">
              <button
                type="button"
                @click="closeModal"
                class="flex-1 px-4 py-2.5 border border-border-light rounded-lg text-text-secondary hover:bg-background-light transition-colors"
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

    <!-- Delete Confirmation Modal -->
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
                <h3 class="text-lg font-bold text-text-main">
                  Hapus Mahasiswa?
                </h3>
                <p class="text-sm text-text-secondary">
                  Tindakan ini tidak dapat dibatalkan.
                </p>
              </div>
            </div>
            <p class="text-text-main mb-6">
              Apakah Anda yakin ingin menghapus mahasiswa
              <strong>"{{ deleteItem?.nama }}"</strong>?
            </p>
            <div class="flex gap-3">
              <button
                @click="showDeleteModal = false"
                class="flex-1 px-4 py-2.5 border border-border-light rounded-lg text-text-secondary hover:bg-background-light transition-colors"
              >
                Batal
              </button>
              <button
                @click="deleteMahasiswa"
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

    <!-- Import Modal -->
    <Transition name="modal-fade">
      <div
        v-if="showImportModal"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
      >
        <div
          class="bg-white dark:bg-surface-light rounded-xl shadow-2xl w-full max-w-lg"
        >
          <div class="p-6 border-b border-border-light">
            <h2 class="text-xl font-bold text-text-main">
              Import Data Mahasiswa
            </h2>
            <p class="text-sm text-text-secondary mt-1">
              Upload file CSV sesuai template yang disediakan.
            </p>
          </div>
          <div class="p-6 space-y-4">
            <!-- Template Download -->
            <div
              class="flex items-center gap-3 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800"
            >
              <span
                class="material-symbols-outlined text-blue-600 dark:text-blue-400"
                >description</span
              >
              <div class="flex-1">
                <p class="text-sm font-medium text-blue-800 dark:text-blue-300">
                  Download Template
                </p>
                <p class="text-xs text-blue-600 dark:text-blue-400">
                  Gunakan template ini untuk mengisi data mahasiswa
                </p>
              </div>
              <button
                @click="downloadTemplate"
                class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-xs font-bold hover:bg-blue-700 transition-colors"
              >
                Download
              </button>
            </div>

            <!-- File Input -->
            <div>
              <label class="block text-sm font-medium text-text-main mb-2"
                >File CSV</label
              >
              <div
                class="border-2 border-dashed border-border-light rounded-lg p-6 text-center hover:border-primary/50 transition-colors cursor-pointer"
                @click="$refs.importFile.click()"
                @dragover.prevent
                @drop.prevent="handleDrop"
              >
                <input
                  ref="importFile"
                  type="file"
                  accept=".csv"
                  class="hidden"
                  @change="handleFileSelect"
                />
                <span
                  class="material-symbols-outlined text-3xl text-text-secondary mb-2 block"
                  >cloud_upload</span
                >
                <p v-if="!importFile" class="text-sm text-text-secondary">
                  Klik atau drag & drop file CSV di sini
                </p>
                <p v-else class="text-sm text-primary font-medium">
                  {{ importFile.name }}
                </p>
              </div>
            </div>

            <!-- Import Result -->
            <div
              v-if="importResult"
              class="p-3 rounded-lg border"
              :class="
                importResult.success
                  ? 'bg-green-50 border-green-200 dark:bg-green-900/20 dark:border-green-800'
                  : 'bg-red-50 border-red-200 dark:bg-red-900/20 dark:border-red-800'
              "
            >
              <p
                class="text-sm font-medium"
                :class="
                  importResult.success
                    ? 'text-green-800 dark:text-green-300'
                    : 'text-red-800 dark:text-red-300'
                "
              >
                {{ importResult.message }}
              </p>
              <div
                v-if="importResult.data?.errors?.length"
                class="mt-2 space-y-1"
              >
                <p
                  v-for="(err, i) in importResult.data.errors"
                  :key="i"
                  class="text-xs text-red-600 dark:text-red-400"
                >
                  • {{ err }}
                </p>
              </div>
            </div>

            <div class="flex gap-3 pt-2">
              <button
                type="button"
                @click="closeImportModal"
                class="flex-1 px-4 py-2.5 border border-border-light rounded-lg text-text-secondary hover:bg-background-light transition-colors"
              >
                Tutup
              </button>
              <button
                @click="doImport"
                :disabled="!importFile || importing"
                class="flex-1 px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50"
              >
                {{ importing ? "Mengimport..." : "Import" }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Sync Modal -->
    <Transition name="modal-fade">
      <div
        v-if="showSyncModal"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
        @click.self="closeSyncModal"
      >
        <div
          class="bg-white dark:bg-surface-light rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col"
        >
          <!-- Header -->
          <div
            class="p-6 border-b border-border-light flex items-center justify-between shrink-0"
          >
            <div class="flex items-center gap-3">
              <div
                class="p-2 bg-teal-50 text-teal-600 rounded-lg dark:bg-teal-900/30 dark:text-teal-400"
              >
                <span class="material-symbols-outlined text-xl">sync</span>
              </div>
              <div>
                <h2 class="text-xl font-bold text-text-main">
                  Sinkronisasi Data Mahasiswa
                </h2>
                <p class="text-sm text-text-secondary mt-0.5">
                  {{ syncStepLabel }}
                </p>
              </div>
            </div>
            <button
              @click="closeSyncModal"
              class="p-1.5 text-text-secondary hover:text-text-main hover:bg-background-light rounded-md transition-colors"
            >
              <span class="material-symbols-outlined">close</span>
            </button>
          </div>

          <!-- Step Progress -->
          <div class="px-6 pt-4 shrink-0">
            <div class="flex items-center gap-2">
              <div
                v-for="(step, i) in syncSteps"
                :key="i"
                class="flex items-center gap-2"
                :class="{ 'flex-1': i < syncSteps.length - 1 }"
              >
                <div
                  class="size-7 rounded-full flex items-center justify-center text-xs font-bold shrink-0 transition-all"
                  :class="
                    syncStep > i
                      ? 'bg-teal-600 text-white'
                      : syncStep === i
                        ? 'bg-teal-100 text-teal-700 border-2 border-teal-600'
                        : 'bg-gray-100 text-gray-400 border border-gray-200'
                  "
                >
                  <span
                    v-if="syncStep > i"
                    class="material-symbols-outlined text-sm"
                    >check</span
                  >
                  <span v-else>{{ i + 1 }}</span>
                </div>
                <div
                  v-if="i < syncSteps.length - 1"
                  class="h-0.5 flex-1 rounded-full transition-all"
                  :class="syncStep > i ? 'bg-teal-600' : 'bg-gray-200'"
                ></div>
              </div>
            </div>
            <div class="flex justify-between mt-1.5 mb-3">
              <span
                v-for="(step, i) in syncSteps"
                :key="i"
                class="text-[10px] font-medium"
                :class="syncStep >= i ? 'text-teal-700' : 'text-gray-400'"
                >{{ step }}</span
              >
            </div>
          </div>

          <!-- Content -->
          <div class="flex-1 overflow-y-auto px-6 pb-6">
            <!-- Step 0: Fetching -->
            <div
              v-if="syncStep === 0"
              class="flex flex-col items-center justify-center py-16 gap-4"
            >
              <div class="relative w-12 h-12">
                <div
                  class="animate-spin rounded-full w-12 h-12 border-2 border-teal-200 border-t-teal-600"
                ></div>
                <div
                  class="absolute top-0 left-0 w-12 h-12 flex items-center justify-center"
                >
                  <span class="material-symbols-outlined text-teal-600 text-xl"
                    >cloud_download</span
                  >
                </div>
              </div>
              <div class="text-center">
                <p class="text-text-main font-semibold">
                  Mengambil data dari API...
                </p>
                <p class="text-text-secondary text-sm mt-1">
                  Mohon tunggu, sedang menghubungi server SIMKEU
                </p>
              </div>
            </div>

            <!-- Step 1: Preview -->
            <div v-if="syncStep === 1 && syncPreviewData" class="space-y-4">
              <!-- Summary Cards -->
              <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div
                  class="p-3 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800"
                >
                  <p
                    class="text-xs text-blue-600 dark:text-blue-400 font-medium uppercase tracking-wider"
                  >
                    Total API
                  </p>
                  <p
                    class="text-xl font-bold text-blue-800 dark:text-blue-300 mt-1"
                  >
                    {{ syncPreviewData.total_api }}
                  </p>
                </div>
                <div
                  class="p-3 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800"
                >
                  <p
                    class="text-xs text-green-600 dark:text-green-400 font-medium uppercase tracking-wider"
                  >
                    Baru
                  </p>
                  <p
                    class="text-xl font-bold text-green-800 dark:text-green-300 mt-1"
                  >
                    {{ syncPreviewData.new_count }}
                  </p>
                </div>
                <div
                  class="p-3 rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800"
                >
                  <p
                    class="text-xs text-amber-600 dark:text-amber-400 font-medium uppercase tracking-wider"
                  >
                    Update
                  </p>
                  <p
                    class="text-xl font-bold text-amber-800 dark:text-amber-300 mt-1"
                  >
                    {{ syncPreviewData.update_count }}
                  </p>
                </div>
                <div
                  class="p-3 rounded-lg bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700"
                >
                  <p
                    class="text-xs text-gray-500 dark:text-gray-400 font-medium uppercase tracking-wider"
                  >
                    Sama
                  </p>
                  <p
                    class="text-xl font-bold text-gray-800 dark:text-gray-200 mt-1"
                  >
                    {{ syncPreviewData.unchanged_count }}
                  </p>
                </div>
              </div>

              <!-- Auto-create warnings -->
              <div
                v-if="
                  syncPreviewData.missing_prodi.length > 0 ||
                  syncPreviewData.missing_tahun.length > 0
                "
                class="p-3 bg-orange-50 dark:bg-orange-900/20 rounded-lg border border-orange-200 dark:border-orange-800"
              >
                <div class="flex items-start gap-2">
                  <span
                    class="material-symbols-outlined text-orange-600 dark:text-orange-400 mt-0.5 text-lg"
                    >info</span
                  >
                  <div>
                    <p
                      class="text-sm font-semibold text-orange-800 dark:text-orange-300"
                    >
                      Data master baru akan dibuat otomatis
                    </p>
                    <div
                      v-if="syncPreviewData.missing_prodi.length"
                      class="mt-2"
                    >
                      <p
                        class="text-xs font-medium text-orange-700 dark:text-orange-400"
                      >
                        Program Studi:
                      </p>
                      <div class="flex flex-wrap gap-1.5 mt-1">
                        <span
                          v-for="p in syncPreviewData.missing_prodi"
                          :key="p.kode"
                          class="text-xs px-2 py-0.5 bg-orange-100 dark:bg-orange-800/30 text-orange-700 dark:text-orange-300 rounded-md"
                          >{{ p.kode }} — {{ p.nama }}</span
                        >
                      </div>
                    </div>
                    <div
                      v-if="syncPreviewData.missing_tahun.length"
                      class="mt-2"
                    >
                      <p
                        class="text-xs font-medium text-orange-700 dark:text-orange-400"
                      >
                        Tahun Akademik:
                      </p>
                      <div class="flex flex-wrap gap-1.5 mt-1">
                        <span
                          v-for="t in syncPreviewData.missing_tahun"
                          :key="t.kode"
                          class="text-xs px-2 py-0.5 bg-orange-100 dark:bg-orange-800/30 text-orange-700 dark:text-orange-300 rounded-md"
                          >{{ t.kode }} — {{ t.nama }}</span
                        >
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Tabs -->
              <div
                class="flex gap-1 bg-gray-100 dark:bg-gray-800 p-1 rounded-lg"
              >
                <button
                  v-for="tab in syncTabs"
                  :key="tab.key"
                  @click="syncActiveTab = tab.key"
                  class="flex-1 py-2 px-3 rounded-md text-xs font-semibold transition-all"
                  :class="
                    syncActiveTab === tab.key
                      ? 'bg-white dark:bg-surface-light text-text-main shadow-sm'
                      : 'text-text-secondary hover:text-text-main'
                  "
                >
                  {{ tab.label }}
                  <span
                    class="ml-1 px-1.5 py-0.5 rounded-full text-[10px]"
                    :class="
                      syncActiveTab === tab.key
                        ? 'bg-teal-100 text-teal-700'
                        : 'bg-gray-200 dark:bg-gray-700 text-gray-500'
                    "
                    >{{ tab.count }}</span
                  >
                </button>
              </div>

              <!-- Table -->
              <div
                class="border border-border-light rounded-lg overflow-hidden"
              >
                <div class="overflow-x-auto max-h-[40vh]">
                  <table class="w-full text-left text-sm">
                    <thead
                      class="bg-gray-50 dark:bg-gray-800 text-text-secondary sticky top-0 z-10"
                    >
                      <tr>
                        <th class="px-4 py-3 font-medium">NIM</th>
                        <th class="px-4 py-3 font-medium">Nama</th>
                        <th class="px-4 py-3 font-medium">Prodi</th>
                        <th class="px-4 py-3 font-medium">Tahun</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th
                          v-if="syncActiveTab === 'update'"
                          class="px-4 py-3 font-medium"
                        >
                          Perubahan
                        </th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-border-light">
                      <tr v-if="filteredSyncRecords.length === 0">
                        <td
                          :colspan="syncActiveTab === 'update' ? 6 : 5"
                          class="p-8 text-center text-text-secondary text-sm"
                        >
                          Tidak ada data pada kategori ini
                        </td>
                      </tr>
                      <tr
                        v-for="(record, i) in filteredSyncRecords"
                        :key="i"
                        class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30"
                      >
                        <td class="px-4 py-3 font-mono text-text-main text-xs">
                          {{ record.nim }}
                        </td>
                        <td class="px-4 py-3 text-text-main font-medium">
                          {{ record.nama }}
                        </td>
                        <td class="px-4 py-3 text-text-secondary text-xs">
                          {{ record.prodi_kode || "-" }}
                        </td>
                        <td class="px-4 py-3 text-text-secondary text-xs">
                          {{ record.tahun_kode || "-" }}
                        </td>
                        <td class="px-4 py-3">
                          <span
                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"
                            :class="
                              record._status === 'new'
                                ? 'bg-green-100 text-green-700 border border-green-200'
                                : record._status === 'update'
                                  ? 'bg-amber-100 text-amber-700 border border-amber-200'
                                  : 'bg-gray-100 text-gray-500 border border-gray-200'
                            "
                          >
                            <span class="material-symbols-outlined text-[10px]">
                              {{
                                record._status === "new"
                                  ? "add_circle"
                                  : record._status === "update"
                                    ? "edit"
                                    : "check_circle"
                              }}
                            </span>
                            {{
                              record._status === "new"
                                ? "Baru"
                                : record._status === "update"
                                  ? "Update"
                                  : "Sama"
                            }}
                          </span>
                        </td>
                        <td v-if="syncActiveTab === 'update'" class="px-4 py-3">
                          <div v-if="record.changes" class="space-y-1">
                            <div
                              v-for="(change, ci) in record.changes"
                              :key="ci"
                              class="text-[11px]"
                            >
                              <span class="font-semibold text-text-secondary"
                                >{{ change.field }}:</span
                              >
                              <span class="text-red-500 line-through ml-1">{{
                                change.old || "—"
                              }}</span>
                              <span class="text-text-secondary mx-1">→</span>
                              <span class="text-green-600 font-medium">{{
                                change.new
                              }}</span>
                            </div>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <!-- Actions -->
              <div class="flex gap-3 pt-2">
                <button
                  @click="closeSyncModal"
                  class="flex-1 px-4 py-2.5 border border-border-light rounded-lg text-text-secondary hover:bg-background-light transition-colors font-medium"
                >
                  Batal
                </button>
                <button
                  @click="executeSyncAll"
                  :disabled="
                    syncPreviewData.new_count + syncPreviewData.update_count ===
                    0
                  "
                  class="flex-1 px-4 py-2.5 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors disabled:opacity-50 font-bold flex items-center justify-center gap-2"
                >
                  <span class="material-symbols-outlined text-lg">sync</span>
                  Sinkronkan
                  {{ syncPreviewData.new_count + syncPreviewData.update_count }}
                  Data
                </button>
              </div>
            </div>

            <!-- Step 2: Executing -->
            <div
              v-if="syncStep === 2"
              class="flex flex-col items-center justify-center py-16 gap-4"
            >
              <div class="relative w-12 h-12">
                <div
                  class="animate-spin rounded-full w-12 h-12 border-2 border-teal-200 border-t-teal-600"
                ></div>
                <div
                  class="absolute top-0 left-0 w-12 h-12 flex items-center justify-center"
                >
                  <span class="material-symbols-outlined text-teal-600 text-xl"
                    >database</span
                  >
                </div>
              </div>
              <div class="text-center">
                <p class="text-text-main font-semibold">
                  Menyinkronkan data...
                </p>
                <p class="text-text-secondary text-sm mt-1">
                  Batch {{ syncProgress.currentBatch }} dari
                  {{ syncProgress.totalBatches }} —
                  {{ syncProgress.processed }}/{{ syncProgress.total }} data
                </p>
              </div>
              <!-- Progress Bar -->
              <div class="w-full max-w-xs">
                <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                  <div
                    class="h-full bg-teal-600 rounded-full transition-all duration-300"
                    :style="{ width: syncProgressPercent + '%' }"
                  ></div>
                </div>
                <p class="text-xs text-text-secondary text-center mt-1.5">
                  {{ syncProgressPercent }}%
                </p>
              </div>
            </div>

            <!-- Step 3: Result -->
            <div v-if="syncStep === 3 && syncResult" class="space-y-4">
              <div class="flex flex-col items-center py-8 gap-3">
                <div
                  class="size-16 rounded-full flex items-center justify-center"
                  :class="
                    syncResult.success
                      ? 'bg-green-100 text-green-600'
                      : 'bg-red-100 text-red-600'
                  "
                >
                  <span class="material-symbols-outlined text-3xl">
                    {{ syncResult.success ? "check_circle" : "error" }}
                  </span>
                </div>
                <h3 class="text-lg font-bold text-text-main">
                  {{
                    syncResult.success
                      ? "Sinkronisasi Berhasil"
                      : "Sinkronisasi Gagal"
                  }}
                </h3>
                <p class="text-sm text-text-secondary text-center max-w-md">
                  {{ syncResult.message }}
                </p>
              </div>

              <!-- Result Stats -->
              <div
                v-if="syncResult.data"
                class="grid grid-cols-2 gap-3 max-w-md mx-auto"
              >
                <div
                  class="p-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-center"
                >
                  <p
                    class="text-2xl font-bold text-green-700 dark:text-green-400"
                  >
                    {{ syncResult.data.success_count }}
                  </p>
                  <p class="text-xs text-green-600 font-medium mt-1">
                    Berhasil
                  </p>
                </div>
                <div
                  class="p-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-center"
                >
                  <p class="text-2xl font-bold text-red-700 dark:text-red-400">
                    {{ syncResult.data.failed_count }}
                  </p>
                  <p class="text-xs text-red-600 font-medium mt-1">Gagal</p>
                </div>
              </div>

              <!-- Errors -->
              <div
                v-if="syncResult.data?.errors?.length"
                class="mt-4 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800"
              >
                <p
                  class="text-sm font-semibold text-red-800 dark:text-red-300 mb-2"
                >
                  Detail Error:
                </p>
                <div class="space-y-1 max-h-32 overflow-y-auto">
                  <p
                    v-for="(err, i) in syncResult.data.errors"
                    :key="i"
                    class="text-xs text-red-600 dark:text-red-400"
                  >
                    • {{ err }}
                  </p>
                </div>
              </div>

              <div class="flex justify-center pt-2">
                <button
                  @click="closeSyncModal"
                  class="px-8 py-2.5 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors font-bold"
                >
                  Selesai
                </button>
              </div>
            </div>

            <!-- Error State -->
            <div
              v-if="syncError"
              class="flex flex-col items-center justify-center py-16 gap-4"
            >
              <div
                class="size-16 rounded-full bg-red-100 text-red-600 flex items-center justify-center"
              >
                <span class="material-symbols-outlined text-3xl">error</span>
              </div>
              <div class="text-center">
                <p class="text-text-main font-semibold">Terjadi Kesalahan</p>
                <p class="text-text-secondary text-sm mt-1 max-w-md">
                  {{ syncError }}
                </p>
              </div>
              <button
                @click="closeSyncModal"
                class="px-6 py-2 border border-border-light rounded-lg text-text-secondary hover:bg-background-light transition-colors font-medium"
              >
                Tutup
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive, computed, nextTick } from "vue";
import adminService from "../../../services/adminService";

const loading = ref(true);
const saving = ref(false);
const deleting = ref(false);
const mahasiswaList = ref([]);
const prodiList = ref([]);
const tahunList = ref([]);
const searchQuery = ref("");
const showModal = ref(false);
const showDeleteModal = ref(false);
const showImportModal = ref(false);
const isEditing = ref(false);
const deleteItem = ref(null);
const importFile = ref(null);
const importing = ref(false);
const importResult = ref(null);

// Sync state
const showSyncModal = ref(false);
const syncStep = ref(0); // 0=fetching, 1=preview, 2=executing, 3=result
const syncPreviewData = ref(null);
const syncResult = ref(null);
const syncError = ref(null);
const syncActiveTab = ref("all");
const syncProgress = reactive({
  currentBatch: 0,
  totalBatches: 0,
  processed: 0,
  total: 0,
});

const syncProgressPercent = computed(() => {
  if (!syncProgress.total) return 0;
  return Math.round((syncProgress.processed / syncProgress.total) * 100);
});

const syncSteps = ["Ambil Data", "Preview", "Sinkronisasi", "Selesai"];

const syncTabs = computed(() => {
  if (!syncPreviewData.value) return [];
  return [
    {
      key: "all",
      label: "Semua",
      count:
        syncPreviewData.value.new_count +
        syncPreviewData.value.update_count +
        syncPreviewData.value.unchanged_count,
    },
    { key: "new", label: "Baru", count: syncPreviewData.value.new_count },
    {
      key: "update",
      label: "Update",
      count: syncPreviewData.value.update_count,
    },
    {
      key: "unchanged",
      label: "Sama",
      count: syncPreviewData.value.unchanged_count,
    },
  ];
});

const syncStepLabel = computed(() => {
  switch (syncStep.value) {
    case 0:
      return "Mengambil data dari server SIMKEU...";
    case 1:
      return "Periksa data yang akan disinkronkan";
    case 2:
      return "Proses sinkronisasi sedang berjalan...";
    case 3:
      return "Proses sinkronisasi selesai";
    default:
      return "";
  }
});

const allSyncRecords = computed(() => {
  if (!syncPreviewData.value) return [];
  const newItems = (syncPreviewData.value.new || []).map((r) => ({
    ...r,
    _status: "new",
  }));
  const updateItems = (syncPreviewData.value.update || []).map((r) => ({
    ...r,
    _status: "update",
  }));
  const unchangedItems = (syncPreviewData.value.unchanged || []).map((r) => ({
    ...r,
    _status: "unchanged",
  }));
  return [...newItems, ...updateItems, ...unchangedItems];
});

const filteredSyncRecords = computed(() => {
  let records;
  if (syncActiveTab.value === "all") records = allSyncRecords.value;
  else
    records = allSyncRecords.value.filter(
      (r) => r._status === syncActiveTab.value,
    );
  return records;
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
  active: 0,
});

const form = reactive({
  id: null,
  nim: "",
  nama: "",
  email: "",
  password: "",
  tahun_id: "",
  prodi_id: "",
  jenis_kelamin: "",
});

let searchTimeout = null;

const fetchMahasiswa = async () => {
  try {
    loading.value = true;
    const params = {
      page: pagination.current_page,
      per_page: pagination.per_page,
      search: searchQuery.value,
    };
    const response = await adminService.getMahasiswa(params);
    if (response.success) {
      mahasiswaList.value = response.data.data;
      Object.assign(pagination, {
        current_page: response.data.current_page,
        last_page: response.data.last_page,
        per_page: response.data.per_page,
        total: response.data.total,
        from: response.data.from,
        to: response.data.to,
      });
      // Use total from pagination (all records, not just current page)
      statsCount.active = response.data.total || 0;
    }
  } catch (error) {
    console.error("Failed to fetch mahasiswa:", error);
  } finally {
    loading.value = false;
  }
};

const debouncedSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    pagination.current_page = 1;
    fetchMahasiswa();
  }, 300);
};

const goToPage = (page) => {
  if (page >= 1 && page <= pagination.last_page) {
    pagination.current_page = page;
    fetchMahasiswa();
  }
};

const openAddModal = () => {
  isEditing.value = false;
  form.id = null;
  form.nim = "";
  form.nama = "";
  form.email = "";
  form.password = "";
  form.tahun_id = "";
  form.prodi_id = "";
  form.jenis_kelamin = "";
  showModal.value = true;
};

const openEditModal = (item) => {
  isEditing.value = true;
  form.id = item.id;
  form.nim = item.nim;
  form.nama = item.nama;
  form.email = item.user?.email || "";
  form.password = "";
  form.tahun_id = item.tahun_id || "";
  form.prodi_id = item.prodi_id || "";
  form.jenis_kelamin = item.jenis_kelamin || "";
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
};

const saveMahasiswa = async () => {
  try {
    saving.value = true;
    const data = {
      nim: form.nim,
      nama: form.nama,
      email: form.email,
      tahun_id: form.tahun_id,
      prodi_id: form.prodi_id,
      jenis_kelamin: form.jenis_kelamin,
    };
    if (!isEditing.value && form.password) {
      data.password = form.password;
    }
    if (isEditing.value) {
      await adminService.updateMahasiswa(form.id, data);
    } else {
      await adminService.createMahasiswa(data);
    }
    closeModal();
    fetchMahasiswa();
  } catch (error) {
    console.error("Failed to save mahasiswa:", error);
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

const deleteMahasiswa = async () => {
  try {
    deleting.value = true;
    await adminService.deleteMahasiswa(deleteItem.value.id);
    showDeleteModal.value = false;
    fetchMahasiswa();
  } catch (error) {
    console.error("Failed to delete mahasiswa:", error);
    alert(
      "Gagal menghapus data: " +
        (error.response?.data?.message || error.message),
    );
  } finally {
    deleting.value = false;
  }
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

// --- Import ---
const handleFileSelect = (e) => {
  importFile.value = e.target.files[0] || null;
  importResult.value = null;
};

const handleDrop = (e) => {
  const file = e.dataTransfer.files[0];
  if (file && file.name.endsWith(".csv")) {
    importFile.value = file;
    importResult.value = null;
  }
};

const downloadTemplate = async () => {
  try {
    const response = await adminService.downloadMahasiswaTemplate();
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const a = document.createElement("a");
    a.href = url;
    a.download = "template_mahasiswa.csv";
    a.click();
    window.URL.revokeObjectURL(url);
  } catch (error) {
    console.error("Failed to download template:", error);
  }
};

const doImport = async () => {
  if (!importFile.value) return;
  importing.value = true;
  importResult.value = null;
  try {
    const result = await adminService.importMahasiswa(importFile.value);
    importResult.value = result;
    if (result.success) {
      fetchMahasiswa();
    }
  } catch (error) {
    importResult.value = {
      success: false,
      message:
        error.response?.data?.message ||
        "Import gagal. Pastikan format file benar.",
    };
  } finally {
    importing.value = false;
  }
};

const closeImportModal = () => {
  showImportModal.value = false;
  importFile.value = null;
  importResult.value = null;
};

// --- Sync ---
const openSyncModal = async () => {
  showSyncModal.value = true;
  syncStep.value = 0;
  syncPreviewData.value = null;
  syncResult.value = null;
  syncError.value = null;
  syncActiveTab.value = "all";

  try {
    const response = await adminService.syncMahasiswaPreview();
    if (response.success) {
      syncPreviewData.value = response.data;
      syncStep.value = 1;
    } else {
      syncError.value = response.message || "Gagal mengambil data preview.";
    }
  } catch (error) {
    syncError.value =
      error.response?.data?.message ||
      error.message ||
      "Gagal mengambil data dari API.";
  }
};

const executeSyncAll = async () => {
  if (!syncPreviewData.value) return;

  const items = [];

  // Add new records
  for (const record of syncPreviewData.value.new || []) {
    items.push({
      nim: record.nim,
      nama: record.nama,
      email: record.email || "",
      jenis_kelamin: record.jenis_kelamin || "",
      prodi_kode: record.prodi_kode || "",
      prodi_nama: record.prodi_nama || "",
      tahun_kode: record.tahun_kode || "",
      tahun_nama: record.tahun_nama || "",
      tahun_semester: record.tahun_semester || "",
      action: "create",
    });
  }

  // Add update records
  for (const record of syncPreviewData.value.update || []) {
    items.push({
      nim: record.nim,
      nama: record.nama,
      email: record.email || "",
      jenis_kelamin: record.jenis_kelamin || "",
      prodi_kode: record.prodi_kode || "",
      prodi_nama: record.prodi_nama || "",
      tahun_kode: record.tahun_kode || "",
      tahun_nama: record.tahun_nama || "",
      tahun_semester: record.tahun_semester || "",
      action: "update",
    });
  }

  if (items.length === 0) return;

  syncStep.value = 2;

  // Batch items into chunks of 500
  const BATCH_SIZE = 200;
  const batches = [];
  for (let i = 0; i < items.length; i += BATCH_SIZE) {
    batches.push(items.slice(i, i + BATCH_SIZE));
  }

  syncProgress.total = items.length;
  syncProgress.processed = 0;
  syncProgress.currentBatch = 0;
  syncProgress.totalBatches = batches.length;

  let totalSuccess = 0;
  let totalFailed = 0;
  let allErrors = [];

  try {
    for (let b = 0; b < batches.length; b++) {
      syncProgress.currentBatch = b + 1;

      const response = await adminService.syncMahasiswaExecute({
        items: batches[b],
      });

      if (response.data) {
        totalSuccess += response.data.success_count || 0;
        totalFailed += response.data.failed_count || 0;
        if (response.data.errors) {
          allErrors = allErrors.concat(response.data.errors);
        }
      }

      syncProgress.processed += batches[b].length;

      // Allow Vue to re-render progress
      await nextTick();
      await new Promise((r) => setTimeout(r, 100));
    }

    syncResult.value = {
      success: true,
      message: `Sinkronisasi selesai: ${totalSuccess} berhasil, ${totalFailed} gagal.`,
      data: {
        success_count: totalSuccess,
        failed_count: totalFailed,
        errors: allErrors.slice(0, 50),
      },
    };
    syncStep.value = 3;
    fetchMahasiswa();
  } catch (error) {
    syncResult.value = {
      success: false,
      message:
        error.response?.data?.message || error.message || "Sinkronisasi gagal.",
      data: {
        success_count: totalSuccess,
        failed_count:
          totalFailed + (syncProgress.total - syncProgress.processed),
        errors: allErrors,
      },
    };
    syncStep.value = 3;
  }
};

const closeSyncModal = () => {
  showSyncModal.value = false;
  syncStep.value = 0;
  syncPreviewData.value = null;
  syncResult.value = null;
  syncError.value = null;
};

const fetchProdi = async () => {
  try {
    const response = await adminService.getProdi({ active_only: true });
    if (response.success) {
      prodiList.value = response.data;
    }
  } catch (error) {
    console.error("Failed to fetch prodi:", error);
  }
};

const fetchTahun = async () => {
  try {
    const response = await adminService.getTahun({ active_only: 1 });
    if (response.success) {
      tahunList.value = response.data;
    }
  } catch (error) {
    console.error("Failed to fetch tahun:", error);
  }
};

onMounted(() => {
  fetchMahasiswa();
  fetchProdi();
  fetchTahun();
});
</script>
