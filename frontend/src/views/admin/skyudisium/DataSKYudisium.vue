<template>
  <div class="max-w-7xl mx-auto flex flex-col gap-8 animate-fade-in-up">
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
        class="flex flex-col gap-2 rounded-xl p-6 bg-surface-light border border-border-light shadow-sm hover:shadow-md transition-shadow"
      >
        <div class="flex items-center justify-between">
          <p
            class="text-text-secondary text-xs font-bold uppercase tracking-wider"
          >
            Siap Yudisium
          </p>
          <div class="bg-green-100 p-2 rounded-lg text-green-600">
            <span class="material-symbols-outlined">how_to_reg</span>
          </div>
        </div>
        <div class="mt-2">
          <p class="text-text-main text-3xl font-bold leading-tight">
            {{ stats.siap_yudisium }}
          </p>
          <div class="flex items-center gap-1 mt-1">
            <span class="material-symbols-outlined text-green-600 text-[18px]"
              >check_circle</span
            >
            <p class="text-green-600 text-xs font-semibold">Ready to process</p>
          </div>
        </div>
      </div>
      <div
        class="flex flex-col gap-2 rounded-xl p-6 bg-surface-light border border-border-light shadow-sm hover:shadow-md transition-shadow"
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
          <p class="text-text-main text-3xl font-bold leading-tight">
            {{ stats.sk_terbit_bulan_ini }}
          </p>
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
        class="flex flex-col gap-2 rounded-xl p-6 bg-surface-light border border-border-light shadow-sm hover:shadow-md transition-shadow"
      >
        <div class="flex items-center justify-between">
          <p
            class="text-text-secondary text-xs font-bold uppercase tracking-wider"
          >
            Total Lulusan {{ currentYear }}
          </p>
          <div class="bg-purple-100 p-2 rounded-lg text-purple-600">
            <span class="material-symbols-outlined">school</span>
          </div>
        </div>
        <div class="mt-2">
          <p class="text-text-main text-3xl font-bold leading-tight">
            {{ stats.total_lulusan }}
          </p>
          <div class="flex items-center gap-1 mt-1">
            <span class="material-symbols-outlined text-purple-600 text-[18px]"
              >trending_up</span
            >
            <p class="text-purple-600 text-xs font-semibold">
              +{{ stats.persentase_kenaikan }}% increase
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Daftar SK Yudisium (Batch) Table -->
    <div
      class="flex flex-col bg-surface-light border border-border-light rounded-xl shadow-sm"
    >
      <!-- Toolbar -->
      <div class="p-5 border-b border-border-light space-y-3">
        <div
          class="flex flex-col md:flex-row gap-3 items-center justify-between"
        >
          <div>
            <h3 class="text-text-main text-lg font-bold">Daftar SK Yudisium</h3>
            <p class="text-text-secondary text-sm">
              Pengelompokan SK Yudisium per batch.
            </p>
          </div>
          <div class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
            <div class="relative w-full md:w-64">
              <input
                v-model="batchSearchQuery"
                @input="debouncedBatchSearch"
                class="w-full pl-10 pr-4 py-2 rounded-lg border border-border-light dark:border-white/10 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all bg-background-light dark:bg-surface-light text-text-main placeholder-text-secondary"
                placeholder="Cari nomor SK batch..."
              />
              <span
                class="material-symbols-outlined absolute left-3 top-2.5 text-[18px] text-text-secondary"
                >search</span
              >
            </div>
            <button
              @click="showBatchModal = true"
              class="flex items-center justify-center gap-2 text-white bg-primary hover:bg-primary/90 text-sm font-semibold px-4 py-2 rounded-lg transition-colors shadow-sm w-full md:w-auto"
            >
              <span class="material-symbols-outlined text-[18px]">add</span>
              Tambah SK Yudisium
            </button>
          </div>
        </div>
      </div>

      <!-- Batch Table -->
      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
          <thead
            class="bg-sidebar-light/50 text-text-secondary font-medium border-b border-border-light"
          >
            <tr>
              <th class="px-6 py-4">No</th>
              <th class="px-6 py-4">Tahun Akademik</th>
              <th class="px-6 py-4">Nomor SK</th>
              <th class="px-6 py-4">Tanggal Terbit</th>
              <th class="px-6 py-4">Tanggal Yudisium</th>
              <th class="px-6 py-4">Jumlah Mhs</th>
              <th class="px-6 py-4 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border-light">
            <tr v-if="batchLoading">
              <td colspan="7" class="p-8 text-center text-text-secondary">
                <div
                  class="animate-spin rounded-full h-6 w-6 border-b-2 border-primary mx-auto"
                ></div>
                <p class="mt-2 text-xs">Memuat data batch...</p>
              </td>
            </tr>
            <tr v-else-if="batchList.length === 0">
              <td colspan="7" class="p-8 text-center text-text-secondary">
                Belum ada SK Yudisium batch
              </td>
            </tr>
            <tr
              v-for="(batch, index) in batchList"
              :key="batch.nomor_sk_batch"
              class="group hover:bg-sidebar-light/30 transition-colors"
            >
              <td class="px-6 py-4 font-medium text-text-main">
                {{ (batchPagination.current_page - 1) * 10 + index + 1 }}
              </td>
              <td class="px-6 py-4 text-text-main">
                {{ batch.tahun_akademik_name }}
              </td>
              <td class="px-6 py-4">
                <span
                  class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-primary/10 text-primary"
                >
                  {{ batch.nomor_sk_batch }}
                </span>
              </td>
              <td class="px-6 py-4 text-xs font-medium text-text-secondary">
                {{ formatDate(batch.tanggal_terbit) }}
              </td>
              <td class="px-6 py-4 text-xs font-medium text-text-secondary">
                {{ formatDate(batch.tanggal_yudisium) }}
              </td>
              <td class="px-6 py-4">
                <span
                  class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-green-50 text-green-700 border border-green-200"
                >
                  <span class="material-symbols-outlined text-[14px]"
                    >group</span
                  >
                  {{ batch.jumlah_mahasiswa }}
                </span>
              </td>
              <td class="px-6 py-4 text-right">
                <div class="flex items-center justify-end gap-2">
                  <button
                    @click="generateBatchSK(batch)"
                    :disabled="generatingBatchId === batch.nomor_sk_batch"
                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-green-700 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition-all disabled:opacity-50"
                  >
                    <span class="material-symbols-outlined text-[14px]"
                      >picture_as_pdf</span
                    >
                    {{
                      generatingBatchId === batch.nomor_sk_batch
                        ? "Generating..."
                        : "Generate SK"
                    }}
                  </button>
                  <button
                    @click="goToBatchDetail(batch)"
                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-primary bg-primary/5 border border-primary/20 rounded-lg hover:bg-primary/10 transition-all"
                  >
                    <span class="material-symbols-outlined text-[14px]"
                      >visibility</span
                    >
                    Detail
                  </button>
                  <button
                    @click="deleteBatch(batch)"
                    :disabled="deletingBatchId === batch.nomor_sk_batch"
                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-red-700 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-all disabled:opacity-50"
                  >
                    <span class="material-symbols-outlined text-[14px]"
                      >delete</span
                    >
                    {{
                      deletingBatchId === batch.nomor_sk_batch
                        ? "Menghapus..."
                        : "Hapus"
                    }}
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Batch Pagination -->
      <div
        v-if="batchList.length > 0"
        class="flex items-center justify-between px-6 py-4 border-t border-border-light"
      >
        <p class="text-sm text-text-secondary">
          Halaman
          <span class="font-medium text-text-main">{{
            batchPagination.current_page
          }}</span>
          dari
          <span class="font-medium text-text-main">{{
            batchPagination.last_page
          }}</span>
        </p>
        <div class="flex gap-2">
          <button
            @click="goToBatchPage(batchPagination.current_page - 1)"
            :disabled="batchPagination.current_page <= 1"
            class="px-3 py-1.5 rounded-md border border-border-light text-text-secondary text-sm font-medium hover:bg-background-light disabled:opacity-50"
          >
            <span class="material-symbols-outlined text-sm">chevron_left</span>
          </button>
          <button
            class="px-3 py-1.5 rounded-md bg-primary text-white text-sm font-medium"
          >
            {{ batchPagination.current_page }}
          </button>
          <button
            @click="goToBatchPage(batchPagination.current_page + 1)"
            :disabled="
              batchPagination.current_page >= batchPagination.last_page
            "
            class="px-3 py-1.5 rounded-md border border-border-light text-text-secondary text-sm font-medium hover:bg-background-light disabled:opacity-50"
          >
            <span class="material-symbols-outlined text-sm">chevron_right</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Tambah SK Yudisium Batch Modal -->
    <Transition name="modal-fade">
      <div
        v-if="showBatchModal"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
      >
        <div
          class="bg-white dark:bg-surface-light rounded-xl shadow-2xl w-full max-w-md"
        >
          <div class="p-6 border-b border-border-light">
            <h2 class="text-xl font-bold text-text-main">Tambah SK Yudisium</h2>
            <p class="text-sm text-text-secondary mt-1">
              Buat batch baru untuk mengelompokkan SK Yudisium.
            </p>
          </div>
          <form @submit.prevent="submitBatch" class="p-6 space-y-4">
            <div>
              <label class="block text-sm font-medium text-text-main mb-1"
                >Nomor SK Yudisium</label
              >
              <input
                v-model="batchForm.nomor_sk_batch"
                type="text"
                class="w-full px-3 py-2 border border-border-light dark:border-white/10 rounded-lg bg-white dark:bg-surface-light text-text-main focus:ring-2 focus:ring-primary/20 focus:border-primary"
                placeholder="Contoh: 001/SK-YUD/2026"
                required
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-text-main mb-1"
                >Tahun Akademik</label
              >
              <select
                v-model="batchForm.th_akademik_id"
                class="w-full px-3 py-2 border border-border-light dark:border-white/10 rounded-lg bg-white dark:bg-surface-light text-text-main focus:ring-2 focus:ring-primary/20 focus:border-primary"
                required
              >
                <option value="">Pilih Tahun Akademik</option>
                <option v-for="t in tahunList" :key="t.id" :value="t.id">
                  {{ t.name }}
                </option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-text-main mb-1"
                >Program Studi</label
              >
              <select
                v-model="batchForm.prodi_id"
                class="w-full px-3 py-2 border border-border-light dark:border-white/10 rounded-lg bg-white dark:bg-surface-light text-text-main focus:ring-2 focus:ring-primary/20 focus:border-primary"
              >
                <option value="">Pilih Program Studi</option>
                <option v-for="p in prodiList" :key="p.id" :value="p.id">
                  {{ p.nama }}
                </option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-text-main mb-1"
                >Tanggal Terbit</label
              >
              <input
                v-model="batchForm.tanggal_terbit"
                type="date"
                class="w-full px-3 py-2 border border-border-light dark:border-white/10 rounded-lg bg-white dark:bg-surface-light text-text-main focus:ring-2 focus:ring-primary/20 focus:border-primary"
                required
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-text-main mb-1"
                >Tanggal Yudisium</label
              >
              <input
                v-model="batchForm.tanggal_yudisium"
                type="date"
                class="w-full px-3 py-2 border border-border-light dark:border-white/10 rounded-lg bg-white dark:bg-surface-light text-text-main focus:ring-2 focus:ring-primary/20 focus:border-primary"
                required
              />
            </div>
            <div class="flex gap-3 pt-4">
              <button
                type="button"
                @click="showBatchModal = false"
                class="flex-1 px-4 py-2.5 border border-border-light rounded-lg text-text-secondary hover:bg-background-light transition-colors"
              >
                Batal
              </button>
              <button
                type="submit"
                :disabled="savingBatch"
                class="flex-1 px-4 py-2.5 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors disabled:opacity-50"
              >
                {{ savingBatch ? "Menyimpan..." : "Simpan" }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Transition>

    <!-- Loading -->
    <div v-if="loading" class="p-12 text-center">
      <div
        class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto"
      ></div>
      <p class="text-text-secondary text-sm mt-3">Memuat data...</p>
    </div>

    <!-- Table Container -->
    <div
      v-else
      class="flex flex-col bg-surface-light border border-border-light rounded-xl shadow-sm"
    >
      <!-- Toolbar -->
      <div class="p-5 border-b border-border-light space-y-3">
        <!-- Row 1: Title + Search + Export -->
        <div
          class="flex flex-col md:flex-row gap-3 items-center justify-between"
        >
          <div>
            <h3 class="text-text-main text-lg font-bold">
              Daftar Mahasiswa Siap Yudisium
            </h3>
            <p class="text-text-secondary text-sm">
              Mahasiswa dengan status 'Sudah Terbit Berita Acara'.
            </p>
          </div>
          <div class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
            <div class="relative w-full md:w-64">
              <input
                v-model="searchQuery"
                @input="debouncedSearch"
                class="w-full pl-10 pr-4 py-2 rounded-lg border border-border-light dark:border-white/10 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all bg-background-light dark:bg-surface-light text-text-main placeholder-text-secondary"
                placeholder="Cari mahasiswa..."
              />
              <span
                class="material-symbols-outlined absolute left-3 top-2.5 text-[18px] text-text-secondary"
                >search</span
              >
            </div>
            <button
              @click="exportPdf"
              :disabled="exporting"
              class="flex items-center justify-center gap-2 text-white bg-green-600 hover:bg-green-700 text-sm font-semibold px-4 py-2 rounded-lg transition-colors shadow-sm w-full md:w-auto disabled:opacity-50"
            >
              <span class="material-symbols-outlined text-[18px]"
                >picture_as_pdf</span
              >
              {{ exporting ? "Mengunduh..." : "Export PDF" }}
            </button>
          </div>
        </div>
        <!-- Row 2: Filter bar -->
        <div class="flex flex-wrap gap-2 items-center">
          <select
            v-model="filterTahunAkademik"
            @change="applyFilters"
            class="px-3 py-2 bg-white dark:bg-surface-light border border-border-light dark:border-white/10 rounded-lg text-text-main text-xs focus:ring-1 focus:ring-primary transition-colors"
          >
            <option value="">Semua Tahun</option>
            <option v-for="t in tahunList" :key="t.id" :value="t.name">
              {{ t.name }}
            </option>
          </select>
          <select
            v-model="filterFakultas"
            @change="onFakultasChange"
            class="px-3 py-2 bg-white dark:bg-surface-light border border-border-light dark:border-white/10 rounded-lg text-text-main text-xs focus:ring-1 focus:ring-primary transition-colors"
          >
            <option value="">Semua Fakultas</option>
            <option v-for="f in fakultasList" :key="f.id" :value="f.id">
              {{ f.nama_fakultas }}
            </option>
          </select>
          <select
            v-model="filterProdi"
            @change="applyFilters"
            class="px-3 py-2 bg-white dark:bg-surface-light border border-border-light dark:border-white/10 rounded-lg text-text-main text-xs focus:ring-1 focus:ring-primary transition-colors"
          >
            <option value="">Semua Prodi</option>
            <option v-for="p in filteredProdiList" :key="p.id" :value="p.id">
              {{ p.nama }}
            </option>
          </select>
          <button
            v-if="hasActiveFilters"
            @click="clearAllFilters"
            class="flex items-center gap-1 px-3 py-2 text-xs text-red-400 hover:text-red-300 hover:bg-red-500/10 rounded-lg transition-colors"
          >
            <span class="material-symbols-outlined text-[14px]">close</span>
            Reset
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
              <th class="px-6 py-4">Mahasiswa</th>
              <th class="px-6 py-4">Judul Skripsi</th>
              <th class="px-6 py-4">Tanggal Ujian</th>
              <th class="px-6 py-4">Status</th>
              <th class="px-6 py-4 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border-light">
            <tr v-if="yudisiumList.length === 0">
              <td colspan="5" class="p-12 text-center text-text-secondary">
                Tidak ada data
              </td>
            </tr>
            <tr
              v-for="item in yudisiumList"
              :key="item.id"
              class="group hover:bg-sidebar-light/30 transition-colors"
            >
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div
                    class="size-10 rounded-full flex items-center justify-center text-xs font-bold border border-border-light shadow-sm shrink-0"
                    :class="getAvatarColor(item.skripsi?.mahasiswa?.nama)"
                  >
                    {{ getInitials(item.skripsi?.mahasiswa?.nama) }}
                  </div>
                  <div>
                    <p class="font-bold text-text-main text-sm">
                      {{ item.skripsi?.mahasiswa?.nama || "-" }}
                    </p>
                    <p class="text-xs text-text-secondary font-medium">
                      {{ item.skripsi?.mahasiswa?.nim || "-" }}
                    </p>
                  </div>
                </div>
              </td>
              <td
                class="px-6 py-4 max-w-xs truncate"
                :title="item.skripsi?.judul"
              >
                {{ item.skripsi?.judul || "-" }}
              </td>
              <td class="px-6 py-4 text-xs font-medium text-text-secondary">
                {{ formatDate(item.tanggal_ujian) }}
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
              <td class="px-6 py-4 text-right">
                <button
                  v-if="item.status === 'siap_yudisium'"
                  @click="prosesYudisium(item)"
                  :disabled="processing === item.id"
                  class="inline-flex items-center justify-center px-4 py-2 text-xs font-bold text-white bg-primary rounded-lg hover:bg-primary/90 transition-all shadow-sm disabled:opacity-50"
                >
                  {{
                    processing === item.id ? "Memproses..." : "Proses Yudisium"
                  }}
                </button>
                <div v-else class="flex items-center gap-2">
                  <button
                    v-if="item.sk_yudisium?.nomor_sk_batch"
                    @click="
                      generateBatchSK({
                        nomor_sk_batch: item.sk_yudisium.nomor_sk_batch,
                      })
                    "
                    :disabled="
                      generatingBatchId === item.sk_yudisium?.nomor_sk_batch
                    "
                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-primary bg-primary/5 border border-primary/20 rounded-lg hover:bg-primary/10 transition-all disabled:opacity-50"
                  >
                    <span class="material-symbols-outlined text-[14px]"
                      >description</span
                    >
                    {{
                      generatingBatchId === item.sk_yudisium?.nomor_sk_batch
                        ? "Mengunduh..."
                        : "Lihat File"
                    }}
                  </button>
                  <span v-else class="text-xs text-text-secondary italic"
                    >Belum ada file</span
                  >
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
          <span class="font-medium text-text-main">{{ pagination.total }}</span>
          mahasiswa
        </p>
        <div class="flex gap-2">
          <button
            @click="goToPage(pagination.current_page - 1)"
            :disabled="pagination.current_page <= 1"
            class="px-3 py-1.5 rounded-md border border-border-light text-text-secondary text-sm font-medium hover:bg-background-light disabled:opacity-50"
          >
            <span class="material-symbols-outlined text-sm">chevron_left</span>
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
            <span class="material-symbols-outlined text-sm">chevron_right</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Proses Yudisium Modal -->
    <Transition name="modal-fade">
      <div
        v-if="showModal"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
      >
        <div
          class="bg-white dark:bg-surface-light rounded-xl shadow-2xl w-full max-w-md"
        >
          <div class="p-6 border-b border-border-light">
            <h2 class="text-xl font-bold text-text-main">Proses SK Yudisium</h2>
            <p class="text-sm text-text-secondary mt-1">
              {{ selectedItem?.skripsi?.mahasiswa?.nama }}
            </p>
          </div>
          <form @submit.prevent="submitYudisium" class="p-6 space-y-4">
            <div>
              <label class="block text-sm font-medium text-text-main mb-1"
                >Nomor SK Yudisium</label
              >
              <input
                v-model="yudisiumForm.nomor_sk"
                type="text"
                class="w-full px-3 py-2 border border-border-light dark:border-white/10 rounded-lg bg-white dark:bg-surface-light text-text-main focus:ring-2 focus:ring-primary/20 focus:border-primary"
                required
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-text-main mb-1"
                >Tanggal Yudisium</label
              >
              <input
                v-model="yudisiumForm.tanggal"
                type="date"
                class="w-full px-3 py-2 border border-border-light dark:border-white/10 rounded-lg bg-white dark:bg-surface-light text-text-main focus:ring-2 focus:ring-primary/20 focus:border-primary"
                required
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-text-main mb-1"
                >IPK</label
              >
              <input
                v-model="yudisiumForm.ipk"
                type="number"
                step="0.01"
                min="0"
                max="4"
                class="w-full px-3 py-2 border border-border-light dark:border-white/10 rounded-lg bg-white dark:bg-surface-light text-text-main focus:ring-2 focus:ring-primary/20 focus:border-primary"
                required
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-text-main mb-1"
                >Predikat</label
              >
              <select
                v-model="yudisiumForm.predikat"
                class="w-full px-3 py-2 border border-border-light dark:border-white/10 rounded-lg bg-white dark:bg-surface-light text-text-main focus:ring-2 focus:ring-primary/20 focus:border-primary"
                required
              >
                <option value="memuaskan">Memuaskan</option>
                <option value="sangat_memuaskan">Sangat Memuaskan</option>
                <option value="cum_laude">Cum Laude</option>
              </select>
            </div>
            <div class="flex gap-3 pt-4">
              <button
                type="button"
                @click="showModal = false"
                class="flex-1 px-4 py-2.5 border border-border-light rounded-lg text-text-secondary hover:bg-background-light transition-colors"
              >
                Batal
              </button>
              <button
                type="submit"
                :disabled="saving"
                class="flex-1 px-4 py-2.5 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors disabled:opacity-50"
              >
                {{ saving ? "Menyimpan..." : "Proses Yudisium" }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive, computed } from "vue";
import { useRouter } from "vue-router";
import adminService from "../../../services/adminService";

const router = useRouter();

const loading = ref(true);
const processing = ref(null);
const saving = ref(false);
const exporting = ref(false);
const generatingId = ref(null);
const generatingBatchId = ref(null);
const deletingBatchId = ref(null);
const showModal = ref(false);
const selectedItem = ref(null);
const yudisiumList = ref([]);
const searchQuery = ref("");

// Filter state
const filterTahunAkademik = ref("");
const filterFakultas = ref("");
const filterProdi = ref("");
const tahunList = ref([]);
const fakultasList = ref([]);
const prodiList = ref([]);
let filterTimeout = null;
let batchFilterTimeout = null;

// Batch state
const batchLoading = ref(false);
const batchList = ref([]);
const batchSearchQuery = ref("");
const showBatchModal = ref(false);
const savingBatch = ref(false);
const batchPagination = reactive({
  current_page: 1,
  last_page: 1,
  total: 0,
});
const batchForm = reactive({
  nomor_sk_batch: "",
  th_akademik_id: "",
  prodi_id: "",
  tanggal_terbit: "",
  tanggal_yudisium: "",
});

const currentYear = computed(() => new Date().getFullYear());

const filteredProdiList = computed(() => {
  if (!filterFakultas.value) return prodiList.value;
  return prodiList.value.filter((p) => p.fakultas_id == filterFakultas.value);
});

const hasActiveFilters = computed(() => {
  return filterTahunAkademik.value || filterFakultas.value || filterProdi.value;
});

const stats = reactive({
  siap_yudisium: 0,
  sk_terbit_bulan_ini: 0,
  total_lulusan: 0,
  persentase_kenaikan: 0,
});

const pagination = reactive({
  current_page: 1,
  last_page: 1,
  total: 0,
  from: 0,
  to: 0,
});

const yudisiumForm = reactive({
  nomor_sk: "",
  tanggal: "",
  ipk: "",
  predikat: "memuaskan",
});

const fetchYudisium = async () => {
  try {
    loading.value = true;
    const params = {
      page: pagination.current_page,
      search: searchQuery.value,
    };
    if (filterTahunAkademik.value)
      params.tahun_akademik = filterTahunAkademik.value;
    if (filterFakultas.value) params.fakultas_id = filterFakultas.value;
    if (filterProdi.value) params.prodi_id = filterProdi.value;

    const response = await adminService.getSKYudisium(params);
    if (response.success) {
      yudisiumList.value = response.data.data || response.data;
      if (response.data.current_page) {
        Object.assign(pagination, {
          current_page: response.data.current_page,
          last_page: response.data.last_page,
          total: response.data.total,
          from: response.data.from,
          to: response.data.to,
        });
      }
      if (response.stats) {
        Object.assign(stats, response.stats);
      }
    }
  } catch (error) {
    console.error("Failed to fetch yudisium:", error);
  } finally {
    loading.value = false;
  }
};

const debouncedSearch = () => {
  clearTimeout(filterTimeout);
  filterTimeout = setTimeout(() => {
    pagination.current_page = 1;
    fetchYudisium();
  }, 300);
};

const applyFilters = () => {
  pagination.current_page = 1;
  fetchYudisium();
};

const onFakultasChange = () => {
  filterProdi.value = "";
  applyFilters();
};

const clearAllFilters = () => {
  filterTahunAkademik.value = "";
  filterFakultas.value = "";
  filterProdi.value = "";
  applyFilters();
};

const goToPage = (page) => {
  if (page >= 1 && page <= pagination.last_page) {
    pagination.current_page = page;
    fetchYudisium();
  }
};

const prosesYudisium = (item) => {
  selectedItem.value = item;
  yudisiumForm.nomor_sk = "";
  yudisiumForm.tanggal = new Date().toISOString().split("T")[0];
  yudisiumForm.ipk = item.skripsi?.mahasiswa?.ipk || "";
  yudisiumForm.predikat = "memuaskan";
  showModal.value = true;
};

const submitYudisium = async () => {
  try {
    saving.value = true;
    await adminService.createSKYudisium({
      skripsi_id: selectedItem.value.skripsi_id,
      nomor_sk: yudisiumForm.nomor_sk,
      tanggal: yudisiumForm.tanggal,
      ipk: yudisiumForm.ipk,
      predikat: yudisiumForm.predikat,
    });
    showModal.value = false;
    fetchYudisium();
    alert("SK Yudisium berhasil diproses!");
  } catch (error) {
    console.error("Failed to process yudisium:", error);
    alert(
      "Gagal memproses yudisium: " +
        (error.response?.data?.message || error.message),
    );
  } finally {
    saving.value = false;
  }
};

const exportPdf = async () => {
  try {
    exporting.value = true;
    const params = {};
    if (filterTahunAkademik.value)
      params.tahun_akademik = filterTahunAkademik.value;
    if (filterFakultas.value) params.fakultas_id = filterFakultas.value;
    if (filterProdi.value) params.prodi_id = filterProdi.value;

    const response = await adminService.exportRekapYudisiumPdf(params);
    const url = window.URL.createObjectURL(
      new Blob([response.data], { type: "application/pdf" }),
    );
    const link = document.createElement("a");
    link.href = url;
    link.setAttribute("download", "Rekap_SK_Yudisium.pdf");
    document.body.appendChild(link);
    link.click();
    link.remove();
    window.URL.revokeObjectURL(url);
  } catch (error) {
    console.error("Failed to export PDF:", error);
    alert("Gagal mengexport PDF");
  } finally {
    exporting.value = false;
  }
};

const generateBatchSK = async (batch) => {
  try {
    generatingBatchId.value = batch.nomor_sk_batch;
    const params = { nomor_sk_batch: batch.nomor_sk_batch };
    const response = await adminService.exportRekapYudisiumPdf(params);
    const url = window.URL.createObjectURL(
      new Blob([response.data], { type: "application/pdf" }),
    );
    const link = document.createElement("a");
    link.href = url;
    link.setAttribute("download", `SK_Yudisium_${batch.nomor_sk_batch}.pdf`);
    document.body.appendChild(link);
    link.click();
    link.remove();
    window.URL.revokeObjectURL(url);
  } catch (error) {
    console.error("Failed to generate batch SK:", error);
    alert(
      "Gagal generate SK: " + (error.response?.data?.message || error.message),
    );
  } finally {
    generatingBatchId.value = null;
  }
};

const deleteBatch = async (batch) => {
  if (
    !confirm(
      `Yakin ingin menghapus batch "${batch.nomor_sk_batch}" beserta ${batch.jumlah_mahasiswa} mahasiswa di dalamnya?\n\nStatus skripsi & mahasiswa akan dikembalikan.`,
    )
  )
    return;

  try {
    deletingBatchId.value = batch.nomor_sk_batch;
    const response = await adminService.destroySKYudisiumBatch(
      batch.nomor_sk_batch,
    );
    alert(response.message || "Batch berhasil dihapus");
    fetchBatch();
    fetchYudisium();
  } catch (error) {
    console.error("Failed to delete batch:", error);
    alert(
      "Gagal menghapus batch: " +
        (error.response?.data?.message || error.message),
    );
  } finally {
    deletingBatchId.value = null;
  }
};

const generateSK = async (item) => {
  try {
    generatingId.value = item.id;
    const skripsiId = item.skripsi_id || item.skripsi?.id;
    const response = await adminService.generateSKYudisiumPdf(skripsiId);
    const url = window.URL.createObjectURL(
      new Blob([response.data], { type: "application/pdf" }),
    );
    const link = document.createElement("a");
    link.href = url;
    const nim = item.skripsi?.mahasiswa?.nim || "unknown";
    link.setAttribute("download", `SK_Yudisium_${nim}.pdf`);
    document.body.appendChild(link);
    link.click();
    link.remove();
    window.URL.revokeObjectURL(url);
  } catch (error) {
    console.error("Failed to generate SK:", error);
    alert(
      "Gagal generate SK Yudisium: " +
        (error.response?.data?.message || error.message),
    );
  } finally {
    generatingId.value = null;
  }
};

const loadFilterData = async () => {
  try {
    const [tahunRes, fakultasRes, prodiRes] = await Promise.all([
      adminService.getTahun(),
      adminService.getFakultas(),
      adminService.getProdi(),
    ]);
    tahunList.value = tahunRes.data || [];
    fakultasList.value = fakultasRes.data || [];
    prodiList.value = prodiRes.data || [];
  } catch (error) {
    console.error("Failed to load filter data:", error);
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

const formatDate = (date) => {
  if (!date) return "-";
  return new Date(date).toLocaleDateString("id-ID", {
    day: "numeric",
    month: "short",
    year: "numeric",
  });
};

const getStatusClass = (status) => {
  const classes = {
    siap_yudisium: "bg-green-50 text-green-700 border border-green-200",
    sk_terbit: "bg-blue-50 text-blue-700 border border-blue-200",
  };
  return classes[status] || "bg-gray-50 text-gray-600 border border-gray-100";
};

const getStatusDot = (status) => {
  const dots = { siap_yudisium: "bg-green-600", sk_terbit: "bg-blue-600" };
  return dots[status] || "bg-gray-600";
};

const getStatusLabel = (status) => {
  const labels = { siap_yudisium: "Siap Yudisium", sk_terbit: "SK Terbit" };
  return labels[status] || status;
};

// Batch methods
const fetchBatch = async () => {
  try {
    batchLoading.value = true;
    const params = {
      page: batchPagination.current_page,
      search: batchSearchQuery.value,
    };
    const response = await adminService.getSKYudisiumBatch(params);
    if (response.success) {
      batchList.value = response.data.data || [];
      if (response.data.current_page) {
        Object.assign(batchPagination, {
          current_page: response.data.current_page,
          last_page: response.data.last_page,
          total: response.data.total,
        });
      }
    }
  } catch (error) {
    console.error("Failed to fetch batch:", error);
  } finally {
    batchLoading.value = false;
  }
};

const debouncedBatchSearch = () => {
  clearTimeout(batchFilterTimeout);
  batchFilterTimeout = setTimeout(() => {
    batchPagination.current_page = 1;
    fetchBatch();
  }, 300);
};

const goToBatchPage = (page) => {
  if (page >= 1 && page <= batchPagination.last_page) {
    batchPagination.current_page = page;
    fetchBatch();
  }
};

const submitBatch = async () => {
  try {
    savingBatch.value = true;
    await adminService.createSKYudisiumBatch({
      nomor_sk_batch: batchForm.nomor_sk_batch,
      th_akademik_id: batchForm.th_akademik_id,
      prodi_id: batchForm.prodi_id || null,
      tanggal_terbit: batchForm.tanggal_terbit,
      tanggal_yudisium: batchForm.tanggal_yudisium,
    });
    showBatchModal.value = false;
    // Navigate to the batch detail page so admin can assign mahasiswa
    router.push({
      name: "DetailSKYudisiumBatch",
      params: { nomor: encodeURIComponent(batchForm.nomor_sk_batch) },
      query: {
        th_akademik_id: batchForm.th_akademik_id,
        prodi_id: batchForm.prodi_id || null,
        tanggal_terbit: batchForm.tanggal_terbit,
        tanggal_yudisium: batchForm.tanggal_yudisium,
      },
    });
  } catch (error) {
    console.error("Failed to create batch:", error);
    alert(
      "Gagal membuat batch: " +
        (error.response?.data?.message || error.message),
    );
  } finally {
    savingBatch.value = false;
  }
};

const goToBatchDetail = (batch) => {
  router.push({
    name: "DetailSKYudisiumBatch",
    params: { nomor: encodeURIComponent(batch.nomor_sk_batch) },
  });
};

onMounted(() => {
  loadFilterData();
  fetchYudisium();
  fetchBatch();
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

/* Dark mode: native select/option styling */
:root.dark select,
.dark select {
  color-scheme: dark;
}
:root.dark select option,
.dark select option {
  background-color: #1e293b;
  color: #e2e8f0;
}
</style>
