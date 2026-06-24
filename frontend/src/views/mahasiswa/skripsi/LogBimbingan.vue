<template>
  <div class="flex flex-col gap-6 animate-fade-in">
    <!-- Header -->
    <div
      class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4"
    >
      <div>
        <h2 class="text-2xl font-bold text-text-main">Riwayat Log Bimbingan</h2>
        <p class="text-text-secondary text-sm mt-1">
          Pantau status bimbingan skripsi dan riwayat konsultasi Anda secara
          real-time.
        </p>
      </div>
      <button
        v-if="!isDosen && isActive"
        @click="showForm = true"
        class="flex items-center gap-2 bg-primary hover:bg-blue-600 text-white px-5 py-2.5 rounded-lg text-sm font-bold shadow-sm transition-all shrink-0"
      >
        <span class="material-symbols-outlined text-[18px]">add</span>
        Tambah Log
      </button>
    </div>

    <!-- Filter Status -->
    <div class="flex items-center gap-3 flex-wrap">
      <span class="text-sm font-bold text-text-secondary">Filter Status:</span>
      <div class="flex gap-2 flex-wrap">
        <button
          v-for="f in filterOptions"
          :key="f.value"
          @click="activeFilter = f.value"
          class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold transition-all border"
          :class="
            activeFilter === f.value
              ? 'bg-primary text-white border-primary shadow-sm'
              : 'bg-surface-light text-text-secondary border-border-light hover:border-gray-300 hover:bg-sidebar-light'
          "
        >
          <span v-if="f.dot" class="w-2 h-2 rounded-full" :class="f.dot"></span>
          {{ f.label }}
          <span
            v-if="f.value !== 'all'"
            class="ml-0.5 text-[10px] opacity-70"
            >{{ getFilterCount(f.value) }}</span
          >
        </button>
      </div>
    </div>

    <!-- Bulk Actions (Dosen only) -->
    <div
      v-if="isDosen && isActive && pendingCount > 0 && !loading"
      class="flex items-center gap-3 p-4 bg-surface-light rounded-xl border border-border-light shadow-sm"
    >
      <span class="material-symbols-outlined text-[20px] text-text-secondary"
        >checklist</span
      >
      <span class="text-sm font-medium text-text-secondary"
        >{{ pendingCount }} log menunggu persetujuan</span
      >
      <div class="flex-1"></div>
      <button
        @click="openBulkConfirm('approved')"
        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-bold bg-green-600 text-white hover:bg-green-700 transition-colors shadow-sm"
      >
        <span class="material-symbols-outlined text-[16px]">check_circle</span>
        Setujui Semua
      </button>
      <button
        @click="openBulkConfirm('rejected')"
        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-bold bg-red-600 text-white hover:bg-red-700 transition-colors shadow-sm"
      >
        <span class="material-symbols-outlined text-[16px]">cancel</span>
        Tolak Semua
      </button>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="animate-pulse flex flex-col gap-3">
      <div class="h-12 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
      <div class="h-16 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
      <div class="h-16 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
      <div class="h-16 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
    </div>

    <!-- Empty State -->
    <div
      v-else-if="!filteredList.length"
      class="bg-surface-light rounded-xl shadow-sm border border-border-light p-12 flex flex-col items-center justify-center gap-3 text-center"
    >
      <span
        class="material-symbols-outlined text-5xl text-text-secondary opacity-40"
        >history_edu</span
      >
      <h3 class="text-lg font-bold text-text-main">
        {{ activeFilter === "all" ? "Belum Ada Bimbingan" : "Tidak Ada Data" }}
      </h3>
      <p class="text-text-secondary text-sm max-w-sm">
        {{
          isDosen
            ? "Mahasiswa belum menambahkan log bimbingan."
            : activeFilter === "all"
              ? 'Belum ada riwayat bimbingan. Klik tombol "Tambah Log" untuk menambahkan.'
              : "Tidak ada log bimbingan dengan status ini."
        }}
      </p>
    </div>

    <!-- Table -->
    <div
      v-else
      class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden"
    >
      <DataTableScroll>
        <table class="w-full text-left text-sm">
          <thead class="bg-sidebar-light border-b border-border-light">
            <tr>
              <th
                class="px-6 py-4 font-bold text-text-secondary text-xs uppercase tracking-wider"
              >
                Tanggal
              </th>
              <th
                class="px-6 py-4 font-bold text-text-secondary text-xs uppercase tracking-wider"
              >
                Topik Bimbingan
              </th>
              <th
                class="px-6 py-4 font-bold text-text-secondary text-xs uppercase tracking-wider"
              >
                Pembimbing
              </th>
              <th
                class="px-6 py-4 font-bold text-text-secondary text-xs uppercase tracking-wider text-center"
              >
                Status
              </th>
              <th
                class="px-6 py-4 font-bold text-text-secondary text-xs uppercase tracking-wider text-center"
              >
                Aksi
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border-light">
            <tr
              v-for="item in paginatedList"
              :key="item.id"
              class="group hover:bg-sidebar-light/50 transition-colors"
            >
              <!-- Tanggal -->
              <td class="px-6 py-5 whitespace-nowrap">
                <span class="text-sm font-medium text-text-main">{{
                  formatDate(item.tanggal)
                }}</span>
              </td>

              <!-- Topik -->
              <td class="px-6 py-5 max-w-xs">
                <p class="font-bold text-text-main text-sm">
                  {{ item.topik || "-" }}
                </p>
                <p
                  v-if="item.deskripsi"
                  class="text-xs text-text-secondary mt-0.5 line-clamp-1"
                >
                  {{ item.deskripsi }}
                </p>
                <p
                  v-if="item.catatan_dosen"
                  class="text-xs text-orange-600 mt-1 italic flex items-center gap-1"
                >
                  <span class="material-symbols-outlined text-[12px]"
                    >chat</span
                  >
                  Catatan: {{ item.catatan_dosen }}
                </p>
              </td>

              <!-- Pembimbing -->
              <td class="px-6 py-5">
                <div class="flex items-center gap-3">
                  <div
                    class="size-9 rounded-full flex items-center justify-center text-xs font-bold shrink-0 bg-blue-100 dark:bg-blue-900/30 text-primary border border-blue-200 dark:border-blue-800"
                  >
                    {{ getInitials(item.dosen?.nama || item.dosen?.full_name) }}
                  </div>
                  <span class="font-bold text-text-main text-sm">
                    {{ item.dosen?.full_name || item.dosen?.nama || "Unknown" }}
                  </span>
                </div>
              </td>

              <!-- Status -->
              <td class="px-6 py-5 text-center">
                <span
                  v-if="item.status === 'approved'"
                  class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 border border-green-100 dark:border-green-800"
                >
                  Disetujui
                </span>
                <span
                  v-else-if="item.status === 'rejected'"
                  class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 border border-red-100 dark:border-red-800"
                >
                  Ditolak
                </span>
                <span
                  v-else-if="item.status === 'revision'"
                  class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 border border-orange-100 dark:border-orange-800"
                >
                  Revisi
                </span>
                <span
                  v-else
                  class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-800"
                >
                  Menunggu
                </span>
              </td>

              <!-- Aksi -->
              <td class="px-6 py-5 text-center">
                <div class="flex items-center justify-center gap-2">
                  <!-- View detail -->
                  <button
                    @click="openDetail(item)"
                    class="size-8 flex items-center justify-center rounded-lg border border-border-light text-text-secondary hover:text-primary hover:border-primary hover:bg-blue-50 transition-all"
                    title="Lihat Detail"
                  >
                    <span class="material-symbols-outlined text-[18px]"
                      >visibility</span
                    >
                  </button>
                  <!-- File bukti -->
                  <a
                    v-if="item.file_bukti"
                    :href="getFileUrl(item.file_bukti)"
                    target="_blank"
                    class="size-8 flex items-center justify-center rounded-lg border border-border-light text-text-secondary hover:text-green-600 hover:border-green-400 hover:bg-green-50 transition-all"
                    title="Lihat Bukti"
                  >
                    <span class="material-symbols-outlined text-[18px]"
                      >attach_file</span
                    >
                  </a>
                  <!-- Dosen Actions -->
                  <template
                    v-if="isDosen && isActive && item.status === 'pending'"
                  >
                    <button
                      @click="openDosenAction(item, 'approved')"
                      class="size-8 flex items-center justify-center rounded-lg bg-green-50 dark:bg-green-900/20 text-green-600 border border-green-100 dark:border-green-800 hover:bg-green-100 dark:hover:bg-green-900/40 transition-all"
                      title="Setujui"
                    >
                      <span class="material-symbols-outlined text-[18px]"
                        >check_circle</span
                      >
                    </button>
                    <button
                      @click="openDosenAction(item, 'revision')"
                      class="size-8 flex items-center justify-center rounded-lg bg-orange-50 dark:bg-orange-900/20 text-orange-600 border border-orange-100 dark:border-orange-800 hover:bg-orange-100 dark:hover:bg-orange-900/40 transition-all"
                      title="Revisi"
                    >
                      <span class="material-symbols-outlined text-[18px]"
                        >edit_note</span
                      >
                    </button>
                    <button
                      @click="openDosenAction(item, 'rejected')"
                      class="size-8 flex items-center justify-center rounded-lg bg-red-50 dark:bg-red-900/20 text-red-600 border border-red-100 dark:border-red-800 hover:bg-red-100 dark:hover:bg-red-900/40 transition-all"
                      title="Tolak"
                    >
                      <span class="material-symbols-outlined text-[18px]"
                        >cancel</span
                      >
                    </button>
                  </template>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </DataTableScroll>

      <!-- Pagination -->
      <TablePagination
        :pagination="localPagination"
        :per-page-options="[5, 10, 15, 20, 50]"
        item-label="log bimbingan"
        @page-change="goToPage"
        @per-page-change="changePerPage"
      />
    </div>

    <!-- ===== PENGAJUAN UJIAN SKRIPSI (Mahasiswa Only) ===== -->
    <section
      v-if="
        !isDosen &&
        !hidePengajuanSidang &&
        (skripsi?.status === 'bimbingan' ||
          skripsi?.status === 'pengajuan_sidang' ||
          skripsi?.status === 'pengajuan_sidang_tolak' ||
          skripsi?.status === 'pengajuan_sidang_acc')
      "
      class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden"
    >
      <div
        class="p-5 border-b border-border-light flex items-center justify-between"
      >
        <div>
          <h3 class="text-lg font-bold text-text-main">
            Pengajuan Ujian Skripsi
          </h3>
          <p class="text-sm text-text-secondary">
            Ajukan ujian skripsi setelah memenuhi syarat bimbingan
          </p>
        </div>
        <span
          v-if="skripsi?.status === 'pengajuan_sidang'"
          class="px-3 py-1 rounded-full text-xs font-bold bg-yellow-50 text-yellow-700 border border-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-300 dark:border-yellow-800"
        >
          Menunggu Persetujuan Dosen
        </span>
      </div>
      <div class="p-5">
        <!-- Already submitted -->
        <div
          v-if="skripsi?.status === 'pengajuan_sidang'"
          class="flex items-center gap-3 p-4 bg-yellow-50 dark:bg-yellow-900/10 rounded-lg border-l-4 border-yellow-400"
        >
          <span class="material-symbols-outlined text-yellow-600"
            >pending_actions</span
          >
          <p class="text-sm text-text-main">
            Pengajuan ujian skripsi Anda sedang menunggu persetujuan dosen
            pembimbing utama.
          </p>
        </div>

        <!-- Approved -->
        <div
          v-else-if="skripsi?.status === 'pengajuan_sidang_acc'"
          class="flex items-center gap-3 p-4 bg-green-50 dark:bg-green-900/10 rounded-lg border-l-4 border-green-400"
        >
          <span class="material-symbols-outlined text-green-600"
            >check_circle</span
          >
          <p class="text-sm text-text-main">
            Pengajuan ujian skripsi Anda telah <strong>disetujui</strong> oleh
            dosen pembimbing. Menunggu admin menjadwalkan ujian.
          </p>
        </div>

        <!-- Rejected -->
        <div
          v-else-if="skripsi?.status === 'pengajuan_sidang_tolak'"
          class="flex flex-col gap-4"
        >
          <div
            class="flex items-start gap-3 p-4 bg-red-50 dark:bg-red-900/10 rounded-lg border-l-4 border-red-400"
          >
            <span class="material-symbols-outlined text-red-600 mt-0.5"
              >error</span
            >
            <div>
              <p class="text-sm font-bold text-red-700 dark:text-red-400 mb-1">
                Pengajuan Ujian Ditolak
              </p>
              <p
                v-if="skripsi?.alasan_tolak_sidang"
                class="text-sm text-text-main"
              >
                <strong>Alasan:</strong> {{ skripsi.alasan_tolak_sidang }}
              </p>
              <p v-else class="text-sm text-text-secondary italic">
                Tidak ada alasan yang diberikan.
              </p>
            </div>
          </div>
          <div class="flex justify-end">
            <button
              @click="openUjianRequestModal"
              :disabled="submittingUjian"
              class="inline-flex items-center gap-2 bg-primary hover:bg-blue-600 text-white font-bold px-6 py-3 rounded-lg transition-all shadow-md shadow-primary/20 text-sm"
            >
              <span
                v-if="submittingUjian"
                class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"
              ></span>
              <span v-else class="material-symbols-outlined text-[20px]"
                >refresh</span
              >
              {{ submittingUjian ? "Mengirim..." : "Ajukan Ulang" }}
            </button>
          </div>
        </div>

        <!-- Eligibility Check -->
        <div v-else>
          <div
            v-if="ujianEligibility === null"
            class="flex items-center gap-3 text-text-secondary"
          >
            <span
              class="animate-spin rounded-full h-5 w-5 border-b-2 border-primary"
            ></span>
            <span class="text-sm">Memeriksa kelayakan...</span>
          </div>
          <div v-else class="flex flex-col gap-4">
            <!-- Checklist -->
            <div class="space-y-3">
              <div
                v-for="(item, idx) in ujianChecklist"
                :key="idx"
                class="flex items-center gap-3 p-3 rounded-lg border"
                :class="
                  item.met
                    ? 'bg-green-50 dark:bg-green-900/10 border-green-200 dark:border-green-800'
                    : 'bg-red-50 dark:bg-red-900/10 border-red-200 dark:border-red-800'
                "
              >
                <span
                  class="material-symbols-outlined text-[20px]"
                  :class="item.met ? 'text-green-600' : 'text-red-500'"
                  >{{ item.met ? "check_circle" : "cancel" }}</span
                >
                <div class="flex-1">
                  <p class="text-sm font-medium text-text-main">
                    {{ item.label }}
                  </p>
                  <p class="text-xs text-text-secondary">{{ item.detail }}</p>
                </div>
              </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end pt-2">
              <button
                v-if="ujianEligibility?.eligible"
                @click="openUjianRequestModal"
                class="inline-flex items-center gap-2 bg-primary hover:bg-blue-600 text-white font-bold px-6 py-3 rounded-lg transition-all shadow-md shadow-primary/20 text-sm"
              >
                <span class="material-symbols-outlined text-[20px]"
                  >school</span
                >
                Ajukan Ujian Skripsi
              </button>
              <p v-else class="text-sm text-red-500 font-medium">
                Anda belum memenuhi semua syarat untuk mengajukan ujian skripsi.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ===== MODAL: KONFIRMASI UJIAN ===== -->
    <div
      v-if="showUjianConfirmModal"
      class="fixed inset-0 z-50 flex items-center justify-center p-4"
    >
      <div
        class="absolute inset-0 bg-black/50 backdrop-blur-sm"
        @click="showUjianConfirmModal = false"
      ></div>
      <div
        class="relative bg-surface-light rounded-xl shadow-xl border border-border-light w-full max-w-sm max-h-[90vh] overflow-y-auto"
      >
        <div class="p-6 flex flex-col items-center gap-4 text-center">
          <div
            class="size-14 rounded-full flex items-center justify-center bg-blue-100 dark:bg-blue-900/30 text-primary"
          >
            <span class="material-symbols-outlined text-[28px]">school</span>
          </div>
          <div>
            <h3 class="text-lg font-bold text-text-main">
              Ajukan Ujian Skripsi?
            </h3>
            <p class="text-sm text-text-secondary mt-1">
              Pengajuan akan dikirim ke dosen pembimbing utama untuk disetujui.
            </p>
          </div>
          <div
            class="w-full flex items-start gap-2.5 p-3 rounded-lg bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-800 text-left"
          >
            <span class="material-symbols-outlined text-amber-600 text-[18px] mt-0.5"
              >info</span
            >
            <p class="text-xs text-amber-800 dark:text-amber-300">
              Belum mempunyai SK 6? Silakan mengurusnya ke staff prodi
              masing-masing sebelum mengajukan sidang.
            </p>
          </div>
          <div class="w-full text-left">
            <label class="block text-sm font-bold text-text-main mb-1.5">
              Lampiran SK 6 <span class="text-red-500">*</span>
            </label>
            <label
              class="flex items-center gap-3 px-3 py-3 rounded-lg border border-dashed border-border-light hover:border-primary hover:bg-primary/5 cursor-pointer transition-colors"
            >
              <span class="material-symbols-outlined text-primary"
                >upload_file</span
              >
              <span class="min-w-0 flex-1">
                <span class="block text-sm font-medium text-text-main truncate">
                  {{ sk6File?.name || "Pilih file SK 6" }}
                </span>
                <span class="block text-xs text-text-secondary">
                  PDF, DOC, atau DOCX, maksimal 20 MB
                </span>
              </span>
              <input
                type="file"
                class="sr-only"
                accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                @change="handleSk6File"
              />
            </label>
            <p v-if="sk6Error" class="text-xs text-red-600 mt-1.5">
              {{ sk6Error }}
            </p>
          </div>
          <div class="flex gap-3 w-full">
            <button
              @click="closeUjianRequestModal"
              class="flex-1 px-4 py-2.5 rounded-lg text-text-secondary font-bold hover:bg-sidebar-light transition-colors text-sm border border-border-light"
            >
              Batal
            </button>
            <button
              @click="submitUjianRequest"
              :disabled="submittingUjian"
              class="flex-1 px-4 py-2.5 rounded-lg bg-primary text-white font-bold hover:bg-blue-600 transition-colors shadow-sm text-sm flex items-center justify-center gap-2 disabled:opacity-50"
            >
              <span
                v-if="submittingUjian"
                class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"
              ></span>
              {{ submittingUjian ? "Mengirim..." : "Ya, Ajukan" }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Ujian Toast -->
    <Transition name="toast-slide">
      <div
        v-if="ujianToast.show"
        class="fixed bottom-6 right-6 z-50 flex items-center gap-3 px-5 py-3.5 rounded-xl shadow-lg text-white text-sm font-bold"
        :class="
          ujianToast.type === 'success'
            ? 'bg-green-600 shadow-green-600/30'
            : 'bg-red-600 shadow-red-600/30'
        "
      >
        <span class="material-symbols-outlined text-[20px]">{{
          ujianToast.type === "success" ? "check_circle" : "error"
        }}</span>
        {{ ujianToast.message }}
      </div>
    </Transition>

    <!-- ===== MODAL: DETAIL BIMBINGAN ===== -->
    <div
      v-if="detailItem"
      class="fixed inset-0 z-50 flex items-center justify-center p-4"
    >
      <div
        class="absolute inset-0 bg-black/50 backdrop-blur-sm"
        @click="detailItem = null"
      ></div>
      <div
        class="relative bg-surface-light rounded-xl shadow-xl border border-border-light w-full max-w-lg max-h-[90vh] overflow-y-auto"
      >
        <div
          class="p-6 border-b border-border-light flex justify-between items-center"
        >
          <div class="flex items-center gap-3">
            <div class="p-2 bg-primary/10 rounded-lg text-primary">
              <span class="material-symbols-outlined">info</span>
            </div>
            <h3 class="text-lg font-bold text-text-main">Detail Bimbingan</h3>
          </div>
          <button
            @click="detailItem = null"
            class="p-1 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors"
          >
            <span class="material-symbols-outlined text-text-secondary"
              >close</span
            >
          </button>
        </div>
        <div class="p-6 flex flex-col gap-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <p class="text-xs text-text-secondary font-medium">Tanggal</p>
              <p class="font-bold text-sm text-text-main">
                {{ formatDate(detailItem.tanggal) }}
              </p>
            </div>
            <div>
              <p class="text-xs text-text-secondary font-medium">Status</p>
              <span
                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-bold mt-0.5"
                :class="getDetailStatusClass(detailItem.status)"
              >
                {{ getDetailStatusLabel(detailItem.status) }}
              </span>
            </div>
          </div>
          <div>
            <p class="text-xs text-text-secondary font-medium">Pembimbing</p>
            <div class="flex items-center gap-2 mt-1">
              <div
                class="size-8 rounded-full flex items-center justify-center text-xs font-bold bg-blue-100 dark:bg-blue-900/30 text-primary border border-blue-200 dark:border-blue-800"
              >
                {{ getInitials(detailItem.dosen?.nama) }}
              </div>
              <span class="font-bold text-sm">{{
                detailItem.dosen?.full_name || detailItem.dosen?.nama || "-"
              }}</span>
            </div>
          </div>
          <div>
            <p class="text-xs text-text-secondary font-medium">Topik</p>
            <p class="font-bold text-sm text-text-main mt-0.5">
              {{ detailItem.topik || "-" }}
            </p>
          </div>
          <div v-if="detailItem.deskripsi">
            <p class="text-xs text-text-secondary font-medium">Deskripsi</p>
            <p class="text-sm text-text-main mt-0.5">
              {{ detailItem.deskripsi }}
            </p>
          </div>
          <div v-if="detailItem.catatan_dosen">
            <p class="text-xs text-text-secondary font-medium">Catatan Dosen</p>
            <p class="text-sm text-orange-600 mt-0.5 italic">
              {{ detailItem.catatan_dosen }}
            </p>
          </div>
          <div v-if="detailItem.file_bukti">
            <p class="text-xs text-text-secondary font-medium">File Bukti</p>
            <a
              :href="getFileUrl(detailItem.file_bukti)"
              target="_blank"
              class="inline-flex items-center gap-1 text-primary text-sm font-bold mt-0.5 hover:underline"
            >
              <span class="material-symbols-outlined text-[16px]"
                >attach_file</span
              >
              Lihat File
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- ===== MODAL: TAMBAH BIMBINGAN (Mahasiswa) ===== -->
    <div
      v-if="showForm"
      class="fixed inset-0 z-50 flex items-center justify-center p-4"
    >
      <div
        class="absolute inset-0 bg-black/50 backdrop-blur-sm"
        @click="closeForm"
      ></div>
      <div
        class="relative bg-surface-light rounded-xl shadow-xl border border-border-light w-full max-w-lg max-h-[90vh] overflow-y-auto"
      >
        <div
          class="p-6 border-b border-border-light flex justify-between items-center"
        >
          <div class="flex items-center gap-3">
            <div class="p-2 bg-primary/10 rounded-lg text-primary">
              <span class="material-symbols-outlined">edit_note</span>
            </div>
            <h3 class="text-lg font-bold text-text-main">
              Tambah Log Bimbingan
            </h3>
          </div>
          <button
            @click="closeForm"
            class="p-1 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors"
          >
            <span class="material-symbols-outlined text-text-secondary"
              >close</span
            >
          </button>
        </div>

        <form @submit.prevent="submitBimbingan" class="p-6 flex flex-col gap-5">
          <div
            v-if="formError"
            class="flex gap-2 bg-red-50 dark:bg-red-900/20 p-3 rounded-lg border border-red-100 dark:border-red-800 text-sm text-red-700 dark:text-red-300"
          >
            <span class="material-symbols-outlined text-red-500 text-[18px]"
              >error</span
            >
            {{ formError }}
          </div>

          <!-- Tanggal -->
          <div class="flex flex-col gap-2">
            <label class="text-sm font-bold text-text-main"
              >Tanggal Bimbingan</label
            >
            <input
              v-model="form.tanggal"
              type="date"
              required
              class="px-4 py-2.5 rounded-lg border border-border-light bg-background-light focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm"
            />
          </div>

          <!-- Pembimbing -->
          <div class="flex flex-col gap-2">
            <label class="text-sm font-bold text-text-main"
              >Dosen Pembimbing</label
            >
            <select
              v-model="form.dosen_id"
              required
              class="px-4 py-2.5 rounded-lg border border-border-light bg-background-light focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm"
            >
              <option value="" disabled>Pilih pembimbing</option>
              <option
                v-for="p in pembimbingOptions"
                :key="p.dosen_id || p.id"
                :value="p.dosen_id || p.dosen?.id"
              >
                {{ p.dosen?.nama || "Dosen" }} —
                {{ getPeranLabel(p.peran || p.jenis) }}
              </option>
            </select>
          </div>

          <!-- Topik -->
          <div class="flex flex-col gap-2">
            <label class="text-sm font-bold text-text-main"
              >Topik Bimbingan</label
            >
            <input
              v-model="form.topik"
              type="text"
              placeholder="Contoh: Revisi Bab 1 - Pendahuluan"
              required
              class="px-4 py-2.5 rounded-lg border border-border-light bg-background-light focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm"
            />
          </div>

          <!-- Deskripsi -->
          <div class="flex flex-col gap-2">
            <label class="text-sm font-bold text-text-main">
              Deskripsi
              <span class="font-normal text-text-secondary">(opsional)</span>
            </label>
            <textarea
              v-model="form.deskripsi"
              rows="3"
              placeholder="Jelaskan detail bimbingan..."
              class="px-4 py-2.5 rounded-lg border border-border-light bg-background-light focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm resize-none"
            ></textarea>
          </div>

          <!-- File Bukti -->
          <div class="flex flex-col gap-2">
            <label class="text-sm font-bold text-text-main">
              File Bukti
              <span class="font-normal text-text-secondary"
                >(opsional, maks 5MB)</span
              >
            </label>
            <input
              ref="fileInput"
              type="file"
              @change="onFileChange"
              accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
              class="text-sm file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer"
            />
            <p
              v-if="form.file_bukti"
              class="text-xs text-green-600 flex items-center gap-1"
            >
              <span class="material-symbols-outlined text-[14px]"
                >check_circle</span
              >
              {{ form.file_bukti.name }}
            </p>
          </div>

          <!-- Actions -->
          <div
            class="flex justify-end gap-3 mt-2 pt-4 border-t border-border-light"
          >
            <button
              type="button"
              @click="closeForm"
              class="px-5 py-2.5 rounded-lg text-text-secondary font-bold hover:bg-sidebar-light transition-colors text-sm"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="submitting"
              class="px-5 py-2.5 rounded-lg bg-primary text-white font-bold hover:bg-blue-600 transition-colors shadow-sm text-sm flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span
                v-if="submitting"
                class="material-symbols-outlined text-[18px] animate-spin"
                >progress_activity</span
              >
              <span v-else class="material-symbols-outlined text-[18px]"
                >save</span
              >
              {{ submitting ? "Menyimpan..." : "Simpan Log" }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- ===== MODAL: DOSEN ACTION ===== -->
    <div
      v-if="dosenActionModal"
      class="fixed inset-0 z-50 flex items-center justify-center p-4"
    >
      <div
        class="absolute inset-0 bg-black/50 backdrop-blur-sm"
        @click="closeDosenAction"
      ></div>
      <div
        class="relative bg-surface-light rounded-xl shadow-xl border border-border-light w-full max-w-md"
      >
        <div
          class="p-6 border-b border-border-light flex justify-between items-center"
        >
          <div class="flex items-center gap-3">
            <div class="p-2 rounded-lg" :class="dosenActionIconClass">
              <span class="material-symbols-outlined">{{
                dosenActionIcon
              }}</span>
            </div>
            <h3 class="text-lg font-bold text-text-main">
              {{ dosenActionTitle }}
            </h3>
          </div>
          <button
            @click="closeDosenAction"
            class="p-1 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors"
          >
            <span class="material-symbols-outlined text-text-secondary"
              >close</span
            >
          </button>
        </div>
        <div class="p-6 flex flex-col gap-4">
          <div class="bg-sidebar-light p-3 rounded-lg">
            <p class="text-xs text-text-secondary">Topik</p>
            <p class="font-bold text-sm">
              {{ dosenActionTarget?.topik || "-" }}
            </p>
            <p class="text-xs text-text-secondary mt-1">Tanggal</p>
            <p class="font-medium text-sm">
              {{ formatDate(dosenActionTarget?.tanggal) }}
            </p>
          </div>
          <div class="flex flex-col gap-2">
            <label class="text-sm font-bold text-text-main">
              Catatan Dosen
              <span class="font-normal text-text-secondary">(opsional)</span>
            </label>
            <textarea
              v-model="dosenCatatan"
              rows="3"
              placeholder="Berikan catatan untuk mahasiswa..."
              class="px-4 py-2.5 rounded-lg border border-border-light bg-background-light focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm resize-none"
            ></textarea>
          </div>
          <div
            v-if="dosenError"
            class="flex gap-2 bg-red-50 dark:bg-red-900/20 p-3 rounded-lg border border-red-100 dark:border-red-800 text-sm text-red-700 dark:text-red-300"
          >
            <span class="material-symbols-outlined text-red-500 text-[18px]"
              >error</span
            >
            {{ dosenError }}
          </div>
          <div class="flex justify-end gap-3 pt-4 border-t border-border-light">
            <button
              type="button"
              @click="closeDosenAction"
              class="px-5 py-2.5 rounded-lg text-text-secondary font-bold hover:bg-sidebar-light transition-colors text-sm"
            >
              Batal
            </button>
            <button
              @click="submitDosenAction"
              :disabled="dosenSubmitting"
              class="px-5 py-2.5 rounded-lg text-white font-bold transition-colors shadow-sm text-sm flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
              :class="dosenActionBtnClass"
            >
              <span
                v-if="dosenSubmitting"
                class="material-symbols-outlined text-[18px] animate-spin"
                >progress_activity</span
              >
              {{ dosenSubmitting ? "Memproses..." : dosenActionBtnLabel }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- ===== MODAL: BULK CONFIRM ===== -->
    <div
      v-if="bulkConfirmModal"
      class="fixed inset-0 z-50 flex items-center justify-center p-4"
    >
      <div
        class="absolute inset-0 bg-black/50 backdrop-blur-sm"
        @click="bulkConfirmModal = false"
      ></div>
      <div
        class="relative bg-surface-light rounded-xl shadow-xl border border-border-light w-full max-w-sm"
      >
        <div class="p-6 flex flex-col items-center gap-4 text-center">
          <div
            class="size-14 rounded-full flex items-center justify-center"
            :class="
              bulkConfirmType === 'approved'
                ? 'bg-green-100 dark:bg-green-900/30 text-green-600'
                : 'bg-red-100 dark:bg-red-900/30 text-red-600'
            "
          >
            <span class="material-symbols-outlined text-[28px]">{{
              bulkConfirmType === "approved" ? "check_circle" : "cancel"
            }}</span>
          </div>
          <div>
            <h3 class="text-lg font-bold text-text-main">
              {{
                bulkConfirmType === "approved"
                  ? "Setujui Semua?"
                  : "Tolak Semua?"
              }}
            </h3>
            <p class="text-sm text-text-secondary mt-1">
              {{ pendingCount }} log bimbingan pending akan
              {{ bulkConfirmType === "approved" ? "disetujui" : "ditolak" }}.
            </p>
          </div>
          <div
            v-if="bulkError"
            class="w-full flex gap-2 bg-red-50 dark:bg-red-900/20 p-3 rounded-lg border border-red-100 dark:border-red-800 text-sm text-red-700 dark:text-red-300"
          >
            <span class="material-symbols-outlined text-red-500 text-[18px]"
              >error</span
            >
            {{ bulkError }}
          </div>
          <div class="flex gap-3 w-full">
            <button
              @click="bulkConfirmModal = false"
              class="flex-1 px-4 py-2.5 rounded-lg text-text-secondary font-bold hover:bg-sidebar-light transition-colors text-sm border border-border-light"
            >
              Batal
            </button>
            <button
              @click="executeBulkAction"
              :disabled="bulkSubmitting"
              class="flex-1 px-4 py-2.5 rounded-lg text-white font-bold transition-colors shadow-sm text-sm flex items-center justify-center gap-2 disabled:opacity-50"
              :class="
                bulkConfirmType === 'approved'
                  ? 'bg-green-600 hover:bg-green-700'
                  : 'bg-red-600 hover:bg-red-700'
              "
            >
              <span
                v-if="bulkSubmitting"
                class="material-symbols-outlined text-[18px] animate-spin"
                >progress_activity</span
              >
              {{
                bulkSubmitting
                  ? "Memproses..."
                  : bulkConfirmType === "approved"
                    ? "Setujui"
                    : "Tolak"
              }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, inject, computed, onMounted, watch } from "vue";
import { useRoute } from "vue-router";
import { mahasiswaService } from "../../../services/mahasiswaService";
import dosenService from "../../../services/dosenService";

const props = defineProps({
  isDosen: { type: Boolean, default: false },
  hidePengajuanSidang: { type: Boolean, default: false },
});

const route = useRoute();
const skripsi = inject("skripsi");
const isActive = computed(() => skripsi.value?.is_active !== false);

const loading = ref(true);
const bimbinganList = ref([]);

// ---- Filter & Pagination ----
const activeFilter = ref("all");
const currentPage = ref(1);
const perPage = ref(5);

const filterOptions = [
  { value: "all", label: "Semua", dot: null },
  { value: "approved", label: "Disetujui", dot: "bg-green-500" },
  { value: "revision", label: "Revisi", dot: "bg-orange-500" },
  { value: "pending", label: "Menunggu", dot: "bg-blue-500" },
  { value: "rejected", label: "Ditolak", dot: "bg-red-500" },
];

const getFilterCount = (status) => {
  return bimbinganList.value.filter((b) => b.status === status).length;
};

const filteredList = computed(() => {
  if (activeFilter.value === "all") return bimbinganList.value;
  return bimbinganList.value.filter((b) => b.status === activeFilter.value);
});

const totalPages = computed(() =>
  Math.max(1, Math.ceil(filteredList.value.length / perPage.value)),
);

const paginatedList = computed(() => {
  const start = (currentPage.value - 1) * perPage.value;
  return filteredList.value.slice(start, start + perPage.value);
});

const paginationFrom = computed(() => {
  if (!filteredList.value.length) return 0;
  return (currentPage.value - 1) * perPage.value + 1;
});

const paginationTo = computed(() => {
  return Math.min(currentPage.value * perPage.value, filteredList.value.length);
});

const localPagination = computed(() => {
  return {
    current_page: currentPage.value,
    last_page: totalPages.value,
    per_page: perPage.value,
    total: filteredList.value.length,
    from: paginationFrom.value,
    to: paginationTo.value,
  };
});

// Reset to page 1 when filter changes
watch(activeFilter, () => {
  currentPage.value = 1;
});

watch(totalPages, (total) => {
  if (currentPage.value > total) {
    currentPage.value = total;
  }
});

const goToPage = (page) => {
  currentPage.value = Math.min(Math.max(page, 1), totalPages.value);
};

const changePerPage = (value) => {
  perPage.value = value;
  currentPage.value = 1;
};

// ---- Detail Modal ----
const detailItem = ref(null);
const openDetail = (item) => {
  detailItem.value = item;
};

// ---- Mahasiswa form state ----
const showForm = ref(false);
const submitting = ref(false);
const formError = ref("");
const fileInput = ref(null);
const form = ref({
  tanggal: new Date().toISOString().split("T")[0],
  dosen_id: "",
  topik: "",
  deskripsi: "",
  file_bukti: null,
});

const pembimbingOptions = computed(() => skripsi.value?.pembimbing || []);

// ---- Ujian eligibility state (mahasiswa) ----
const ujianEligibility = ref(null);
const showUjianConfirmModal = ref(false);
const submittingUjian = ref(false);
const ujianToast = ref({ show: false, message: "", type: "success" });
const sk6File = ref(null);
const sk6Error = ref("");

const ujianChecklist = computed(() => {
  if (!ujianEligibility.value) return [];
  const e = ujianEligibility.value;
  const items = [
    {
      label: "Bimbingan Pembimbing 1",
      detail: `${e.bimbingan?.pembimbing_1?.count ?? 0} / ${e.bimbingan?.pembimbing_1?.required ?? 8} sesi (disetujui)`,
      met: e.bimbingan?.pembimbing_1?.met ?? false,
    },
  ];
  if (e.has_pembimbing_2) {
    items.push({
      label: "Bimbingan Pembimbing 2",
      detail: `${e.bimbingan?.pembimbing_2?.count ?? 0} / ${e.bimbingan?.pembimbing_2?.required ?? 4} sesi (disetujui)`,
      met: e.bimbingan?.pembimbing_2?.met ?? false,
    });
  }
  items.push({
    label: "Naskah Final",
    detail: e.naskah_final?.uploaded ? "Sudah diunggah" : "Belum diunggah",
    met: e.naskah_final?.uploaded ?? false,
  });
  return items;
});

// ---- Dosen action state ----
const dosenActionModal = ref(false);
const dosenActionType = ref("");
const dosenActionTarget = ref(null);
const dosenCatatan = ref("");
const dosenSubmitting = ref(false);
const dosenError = ref("");

// ---- Bulk action state ----
const bulkConfirmModal = ref(false);
const bulkConfirmType = ref("");
const bulkSubmitting = ref(false);
const bulkError = ref("");

const pendingCount = computed(
  () => bimbinganList.value.filter((b) => b.status === "pending").length,
);

const dosenActionTitle = computed(() => {
  const map = {
    approved: "Setujui Bimbingan",
    revision: "Minta Revisi",
    rejected: "Tolak Bimbingan",
  };
  return map[dosenActionType.value] || "Aksi";
});
const dosenActionIcon = computed(() => {
  const map = {
    approved: "check_circle",
    revision: "edit_note",
    rejected: "cancel",
  };
  return map[dosenActionType.value] || "info";
});
const dosenActionIconClass = computed(() => {
  const map = {
    approved: "bg-green-100 dark:bg-green-900/30 text-green-600",
    revision: "bg-orange-100 dark:bg-orange-900/30 text-orange-600",
    rejected: "bg-red-100 dark:bg-red-900/30 text-red-600",
  };
  return (
    map[dosenActionType.value] || "bg-gray-100 dark:bg-gray-800 text-gray-600"
  );
});
const dosenActionBtnClass = computed(() => {
  const map = {
    approved: "bg-green-600 hover:bg-green-700",
    revision: "bg-orange-600 hover:bg-orange-700",
    rejected: "bg-red-600 hover:bg-red-700",
  };
  return map[dosenActionType.value] || "bg-primary hover:bg-blue-600";
});
const dosenActionBtnLabel = computed(() => {
  const map = {
    approved: "Setujui",
    revision: "Minta Revisi",
    rejected: "Tolak",
  };
  return map[dosenActionType.value] || "Konfirmasi";
});

// ---- Helpers ----
const getInitials = (name) => {
  if (!name) return "?";
  return name
    .split(" ")
    .map((n) => n[0])
    .join("")
    .substring(0, 2)
    .toUpperCase();
};

const getPeranLabel = (peran) => {
  const map = {
    pembimbing_1: "Pembimbing Utama",
    pembimbing_2: "Pembimbing Pendamping",
    utama: "Pembimbing Utama",
    pendamping: "Pembimbing Pendamping",
  };
  return map[peran] || peran || "Pembimbing";
};

const getFileUrl = (path) => {
  if (!path) return "#";
  if (path.startsWith("http")) return path;
  const baseUrl =
    import.meta.env.VITE_API_URL?.replace("/api", "") || "";
  return `${baseUrl}/api/file/${path}`;
};

const formatDate = (dateStr) => {
  if (!dateStr) return "-";
  return new Date(dateStr).toLocaleDateString("id-ID", {
    day: "numeric",
    month: "short",
    year: "numeric",
  });
};

const getDetailStatusClass = (status) => {
  const map = {
    approved:
      "bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 border border-green-100 dark:border-green-800",
    rejected:
      "bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 border border-red-100 dark:border-red-800",
    revision:
      "bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 border border-orange-100 dark:border-orange-800",
    pending:
      "bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-800",
  };
  return map[status] || map.pending;
};

const getDetailStatusLabel = (status) => {
  const map = {
    approved: "Disetujui",
    rejected: "Ditolak",
    revision: "Revisi",
    pending: "Menunggu",
  };
  return map[status] || "Menunggu";
};

// ---- Data fetching ----
const fetchBimbingan = async () => {
  loading.value = true;
  try {
    if (props.isDosen) {
      const skripsiId = route.params.id;
      const res = await dosenService.getBimbinganLogs(skripsiId);
      if (res.success) bimbinganList.value = res.data;
    } else {
      const res = await mahasiswaService.getBimbinganLogs();
      if (res.success) bimbinganList.value = res.data;
      // Also check ujian eligibility for mahasiswa
      await checkUjianEligibility();
    }
  } catch (err) {
    console.error("Failed to fetch bimbingan:", err);
    bimbinganList.value = skripsi.value?.bimbingan || [];
  } finally {
    loading.value = false;
  }
};

// ---- Ujian eligibility (mahasiswa) ----
const checkUjianEligibility = async () => {
  if (props.isDosen) return;
  if (
    ![
      "bimbingan",
      "pengajuan_sidang",
      "pengajuan_sidang_tolak",
      "pengajuan_sidang_acc",
    ].includes(skripsi.value?.status)
  )
    return;
  try {
    const res = await mahasiswaService.checkUjianEligibility();
    if (res.success) {
      ujianEligibility.value = res.data;
    }
  } catch (err) {
    console.error("Failed to check ujian eligibility:", err);
  }
};

const openUjianRequestModal = () => {
  sk6File.value = null;
  sk6Error.value = "";
  showUjianConfirmModal.value = true;
};

const closeUjianRequestModal = () => {
  showUjianConfirmModal.value = false;
  sk6File.value = null;
  sk6Error.value = "";
};

const handleSk6File = (event) => {
  const file = event.target.files?.[0] || null;
  sk6Error.value = "";

  if (!file) {
    sk6File.value = null;
    return;
  }

  const extension = file.name.split(".").pop()?.toLowerCase();
  if (!["pdf", "doc", "docx"].includes(extension)) {
    sk6File.value = null;
    sk6Error.value = "Format SK 6 harus PDF, DOC, atau DOCX.";
    event.target.value = "";
    return;
  }

  if (file.size > 20 * 1024 * 1024) {
    sk6File.value = null;
    sk6Error.value = "Ukuran file SK 6 maksimal 20 MB.";
    event.target.value = "";
    return;
  }

  sk6File.value = file;
};

const submitUjianRequest = async () => {
  if (!sk6File.value) {
    sk6Error.value = "File SK 6 wajib dilampirkan.";
    return;
  }

  submittingUjian.value = true;
  try {
    const formData = new FormData();
    formData.append("file_sk6", sk6File.value);
    const res = await mahasiswaService.requestUjian(formData);
    if (res.success) {
      closeUjianRequestModal();
      // Update skripsi status locally
      if (skripsi.value) {
        skripsi.value.status = "pengajuan_sidang";
        skripsi.value.progress_percentage = 60;
      }
      ujianToast.value = {
        show: true,
        message: "Pengajuan ujian skripsi berhasil dikirim!",
        type: "success",
      };
      setTimeout(() => {
        ujianToast.value.show = false;
      }, 3000);
    }
  } catch (err) {
    console.error("Failed to submit ujian request:", err);
    const msg =
      err.response?.data?.message || "Gagal mengajukan ujian skripsi.";
    ujianToast.value = { show: true, message: msg, type: "error" };
    setTimeout(() => {
      ujianToast.value.show = false;
    }, 4000);
  } finally {
    submittingUjian.value = false;
  }
};

// ---- Mahasiswa actions ----
const onFileChange = (e) => {
  form.value.file_bukti = e.target.files[0] || null;
};

const closeForm = () => {
  showForm.value = false;
  formError.value = "";
  form.value = {
    tanggal: new Date().toISOString().split("T")[0],
    dosen_id: "",
    topik: "",
    deskripsi: "",
    file_bukti: null,
  };
  if (fileInput.value) fileInput.value.value = "";
};

const submitBimbingan = async () => {
  formError.value = "";
  submitting.value = true;
  try {
    const res = await mahasiswaService.addBimbingan(form.value);
    if (res.success) {
      closeForm();
      await fetchBimbingan();
    } else {
      formError.value = res.message || "Gagal menyimpan log bimbingan.";
    }
  } catch (err) {
    const data = err.response?.data;
    if (data?.errors) {
      const firstErr = Object.values(data.errors)[0];
      formError.value = Array.isArray(firstErr) ? firstErr[0] : firstErr;
    } else {
      formError.value = data?.message || "Terjadi kesalahan saat menyimpan.";
    }
  } finally {
    submitting.value = false;
  }
};

// ---- Dosen actions ----
const openDosenAction = (item, type) => {
  dosenActionTarget.value = item;
  dosenActionType.value = type;
  dosenCatatan.value = "";
  dosenError.value = "";
  dosenActionModal.value = true;
};

const closeDosenAction = () => {
  dosenActionModal.value = false;
  dosenActionTarget.value = null;
  dosenActionType.value = "";
  dosenCatatan.value = "";
  dosenError.value = "";
};

const submitDosenAction = async () => {
  dosenSubmitting.value = true;
  dosenError.value = "";
  try {
    const res = await dosenService.updateBimbinganStatus(
      dosenActionTarget.value.id,
      dosenActionType.value,
      dosenCatatan.value || null,
    );
    if (res.success) {
      closeDosenAction();
      await fetchBimbingan();
    } else {
      dosenError.value = res.message || "Gagal memperbarui status.";
    }
  } catch (err) {
    dosenError.value =
      err.response?.data?.message || "Terjadi kesalahan saat memproses.";
  } finally {
    dosenSubmitting.value = false;
  }
};

// ---- Bulk actions ----
const openBulkConfirm = (type) => {
  bulkConfirmType.value = type;
  bulkError.value = "";
  bulkConfirmModal.value = true;
};

const executeBulkAction = async () => {
  bulkSubmitting.value = true;
  bulkError.value = "";
  try {
    const pendingIds = bimbinganList.value
      .filter((b) => b.status === "pending")
      .map((b) => b.id);
    if (!pendingIds.length) {
      bulkError.value = "Tidak ada log pending untuk diproses.";
      return;
    }
    const res = await dosenService.bulkUpdateBimbinganStatus(
      pendingIds,
      bulkConfirmType.value,
    );
    if (res.success) {
      bulkConfirmModal.value = false;
      await fetchBimbingan();
    } else {
      bulkError.value = res.message || "Gagal memproses bulk action.";
    }
  } catch (err) {
    bulkError.value =
      err.response?.data?.message || "Terjadi kesalahan saat memproses.";
  } finally {
    bulkSubmitting.value = false;
  }
};

onMounted(fetchBimbingan);
</script>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.3s ease-in-out;
}
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(5px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
.toast-slide-enter-active,
.toast-slide-leave-active {
  transition: all 0.3s ease;
}
.toast-slide-enter-from {
  opacity: 0;
  transform: translateY(20px);
}
.toast-slide-leave-to {
  opacity: 0;
  transform: translateX(20px);
}
</style>
