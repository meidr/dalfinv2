<template>
  <div class="max-w-7xl mx-auto flex flex-col gap-6 animate-fade-in-up">
    <!-- Loading -->
    <div v-if="loading" class="p-12 text-center">
      <div
        class="animate-spin rounded-full h-10 w-10 border-b-2 border-primary mx-auto"
      ></div>
      <p class="text-text-secondary text-sm mt-3">Memuat data skripsi...</p>
    </div>

    <template v-else-if="skripsi">
      <!-- Breadcrumbs -->
      <div class="flex flex-wrap items-center gap-2 text-sm">
        <router-link
          to="/admin/skripsi"
          class="text-text-secondary hover:text-primary font-medium"
        >
          Data Skripsi
        </router-link>
        <span class="material-symbols-outlined text-text-secondary text-sm"
          >chevron_right</span
        >
        <span class="text-text-main font-bold">Detail Skripsi</span>
      </div>

      <!-- Header -->
      <div
        class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4"
      >
        <div class="flex items-center gap-4">
          <div
            class="size-14 rounded-full flex items-center justify-center text-lg font-bold shrink-0"
            :class="getAvatarColor(skripsi.mahasiswa?.nama)"
          >
            {{ getInitials(skripsi.mahasiswa?.nama) }}
          </div>
          <div>
            <h1 class="text-2xl font-bold text-text-main">
              {{ skripsi.mahasiswa?.nama || "-" }}
            </h1>
            <p class="text-text-secondary text-sm">
              {{ skripsi.mahasiswa?.nim || "-" }} •
              {{ skripsi.mahasiswa?.prodi?.nama || "-" }}
            </p>
          </div>
        </div>
        <div class="flex items-center gap-3">
          <span
            class="px-3 py-1.5 rounded-full text-xs font-bold border"
            :class="getStatusClass(skripsi.status)"
          >
            {{ getStatusLabel(skripsi.status) }}
          </span>
          <button
            @click="showEditModal = true"
            class="flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-600 transition-all text-sm font-bold"
          >
            <span class="material-symbols-outlined text-[18px]">edit</span>
            Edit Status
          </button>
        </div>
      </div>

      <!-- Progress Card -->
      <div
        class="bg-surface-light border border-border-light rounded-xl p-5 shadow-sm"
      >
        <div class="flex items-center justify-between mb-3">
          <h3 class="text-sm font-bold text-text-main">Progress Skripsi</h3>
          <span class="text-2xl font-bold text-primary"
            >{{ skripsi.progress_percentage || 0 }}%</span
          >
        </div>
        <div
          class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-3 overflow-hidden"
        >
          <div
            class="h-full bg-linear-to-r from-primary to-blue-400 rounded-full transition-all duration-500"
            :style="{ width: `${skripsi.progress_percentage || 0}%` }"
          ></div>
        </div>
      </div>

      <!-- Info Cards Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Judul Skripsi -->
        <div
          class="bg-surface-light border border-border-light rounded-xl p-5 shadow-sm"
        >
          <h3
            class="text-xs font-bold text-text-secondary uppercase tracking-wide mb-3"
          >
            Judul Skripsi
          </h3>
          <p class="text-text-main font-medium leading-relaxed">
            {{ skripsi.judul || "Belum ada judul" }}
          </p>
          <div class="mt-4 flex flex-wrap gap-4 text-sm text-text-secondary">
            <div class="flex items-center gap-2">
              <span class="material-symbols-outlined text-[18px]"
                >calendar_today</span
              >
              Terdaftar: {{ formatDate(skripsi.tanggal_daftar) }}
            </div>
            <div class="flex items-center gap-2">
              <span class="material-symbols-outlined text-[18px]">school</span>
              {{ skripsi.semester_daftar || "-" }}
            </div>
            <div v-if="skripsi.tahun_akademik" class="flex items-center gap-2">
              <span class="material-symbols-outlined text-[18px]"
                >date_range</span
              >
              {{ skripsi.tahun_akademik?.name || "-" }}
            </div>
          </div>
        </div>

        <!-- Pembimbing -->
        <div
          class="bg-surface-light border border-border-light rounded-xl p-5 shadow-sm"
        >
          <h3
            class="text-xs font-bold text-text-secondary uppercase tracking-wide mb-3"
          >
            Dosen Pembimbing
          </h3>
          <div v-if="pembimbing.length > 0" class="space-y-3">
            <div
              v-for="(p, index) in pembimbing"
              :key="p.id"
              class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-white/5 rounded-lg"
            >
              <div
                class="size-10 rounded-full bg-primary/10 text-primary flex items-center justify-center text-sm font-bold"
              >
                {{ index + 1 }}
              </div>
              <div>
                <p class="font-bold text-text-main text-sm">
                  {{ p.dosen?.nama || "-" }}
                </p>
                <p class="text-xs text-text-secondary">
                  {{ p.dosen?.nidn || "-" }} • Pembimbing {{ index + 1 }}
                </p>
              </div>
            </div>
          </div>
          <div v-else class="text-text-secondary text-sm italic">
            Belum ada pembimbing ditugaskan
          </div>
        </div>
      </div>

      <!-- Tabs Section -->
      <div
        class="bg-surface-light border border-border-light rounded-xl shadow-sm overflow-hidden"
      >
        <div class="border-b border-border-light px-5">
          <div class="flex gap-6 overflow-x-auto no-scrollbar">
            <button
              v-for="tab in tabs"
              :key="tab.id"
              @click="activeTab = tab.id"
              class="py-4 text-sm font-bold transition-all border-b-[3px] whitespace-nowrap"
              :class="
                activeTab === tab.id
                  ? 'border-primary text-primary'
                  : 'border-transparent text-text-secondary hover:text-text-main'
              "
            >
              {{ tab.label }}
            </button>
          </div>
        </div>

        <div class="p-5">
          <!-- Bimbingan Tab -->
          <div v-if="activeTab === 'bimbingan'">
            <div class="flex items-center justify-between mb-4">
              <h4 class="font-bold text-text-main">Riwayat Bimbingan</h4>
              <span
                class="text-xs px-3 py-1 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-full font-bold"
              >
                {{ skripsi.bimbingan?.length || 0 }} sesi
              </span>
            </div>
            <div
              v-if="skripsi.bimbingan?.length > 0"
              class="space-y-3 max-h-96 overflow-y-auto"
            >
              <div
                v-for="bimbingan in skripsi.bimbingan"
                :key="bimbingan.id"
                class="p-4 bg-gray-50 dark:bg-white/5 rounded-lg border border-border-light"
              >
                <div class="flex justify-between items-start mb-2">
                  <div>
                    <p class="font-bold text-text-main text-sm">
                      {{
                        bimbingan.dosen?.full_name ||
                        bimbingan.dosen?.nama ||
                        "-"
                      }}
                    </p>
                    <p
                      v-if="bimbingan.topik"
                      class="text-xs text-primary font-medium mt-0.5"
                    >
                      {{ bimbingan.topik }}
                    </p>
                  </div>
                  <div class="flex items-center gap-2 shrink-0">
                    <span
                      v-if="bimbingan.status"
                      class="px-2 py-0.5 rounded-full text-[11px] font-bold"
                      :class="{
                        'bg-green-50 dark:bg-green-900/20 text-green-600 border border-green-200 dark:border-green-800':
                          bimbingan.status === 'approved',
                        'bg-yellow-50 dark:bg-yellow-900/20 text-yellow-600 border border-yellow-200 dark:border-yellow-800':
                          bimbingan.status === 'pending',
                        'bg-orange-50 dark:bg-orange-900/20 text-orange-600 border border-orange-200 dark:border-orange-800':
                          bimbingan.status === 'revision',
                        'bg-red-50 dark:bg-red-900/20 text-red-600 border border-red-200 dark:border-red-800':
                          bimbingan.status === 'rejected',
                      }"
                    >
                      {{
                        bimbingan.status === "approved"
                          ? "Disetujui"
                          : bimbingan.status === "pending"
                            ? "Menunggu"
                            : bimbingan.status === "revision"
                              ? "Revisi"
                              : "Ditolak"
                      }}
                    </span>
                    <span class="text-xs text-text-secondary">{{
                      formatDate(bimbingan.tanggal)
                    }}</span>
                  </div>
                </div>
                <p
                  v-if="bimbingan.deskripsi"
                  class="text-sm text-text-main mt-1"
                >
                  {{ bimbingan.deskripsi }}
                </p>
                <div
                  v-if="bimbingan.catatan_dosen"
                  class="mt-2 px-3 py-2 bg-blue-50 dark:bg-blue-900/20 rounded-md border-l-3 border-blue-400"
                >
                  <p class="text-xs font-bold text-blue-600 mb-0.5">
                    Catatan Dosen:
                  </p>
                  <p class="text-sm text-blue-800 dark:text-blue-300">
                    {{ bimbingan.catatan_dosen }}
                  </p>
                </div>
              </div>
            </div>
            <div v-else class="text-center py-8 text-text-secondary">
              <span class="material-symbols-outlined text-4xl mb-2 block"
                >history_edu</span
              >
              <p>Belum ada riwayat bimbingan</p>
            </div>
          </div>

          <!-- Seminar Tab -->
          <div v-else-if="activeTab === 'seminar'">
            <div class="space-y-4">
              <div
                v-for="seminar in filteredSeminars"
                :key="seminar.id"
                class="p-4 bg-gray-50 dark:bg-white/5 rounded-lg border border-border-light"
              >
                <div class="flex items-center justify-between mb-3">
                  <span
                    class="px-2 py-1 rounded text-xs font-bold uppercase"
                    :class="
                      seminar.jenis === 'sempro'
                        ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-600'
                        : 'bg-orange-100 dark:bg-orange-900/30 text-orange-600'
                    "
                  >
                    {{
                      seminar.jenis === "sempro"
                        ? "Sempro"
                        : seminar.jenis === "semhas"
                          ? "Semhas"
                          : seminar.jenis
                    }}
                  </span>
                  <span
                    class="px-2 py-1 rounded text-xs font-bold"
                    :class="getStatusClass(seminar.status)"
                  >
                    {{ seminar.status }}
                  </span>
                </div>
                <div class="grid grid-cols-2 gap-4 text-sm">
                  <div>
                    <p class="text-text-secondary text-xs">Tanggal</p>
                    <p class="font-medium text-text-main">
                      {{ formatDate(seminar.tanggal) }}
                    </p>
                  </div>
                  <div>
                    <p class="text-text-secondary text-xs">Waktu</p>
                    <p class="font-medium text-text-main">
                      {{ seminar.waktu || "-" }}
                    </p>
                  </div>
                  <div>
                    <p class="text-text-secondary text-xs">Ruangan</p>
                    <p class="font-medium text-text-main">
                      {{ seminar.ruangan || "-" }}
                    </p>
                  </div>
                  <div>
                    <p class="text-text-secondary text-xs">Nilai</p>
                    <p class="font-medium text-text-main">
                      {{ seminar.nilai ?? "-" }}
                    </p>
                  </div>
                </div>
              </div>
              <div
                v-if="!filteredSeminars?.length"
                class="text-center py-8 text-text-secondary"
              >
                <span class="material-symbols-outlined text-4xl mb-2 block"
                  >groups</span
                >
                <p>Belum ada jadwal seminar</p>
              </div>
            </div>
          </div>

          <!-- Sidang Tab -->
          <div v-else-if="activeTab === 'sidang'">
            <div class="space-y-4">
              <div
                v-for="item in sidangItems"
                :key="item._key"
                class="p-4 bg-gray-50 dark:bg-white/5 rounded-lg border border-border-light"
              >
                <div class="flex items-center justify-between mb-3">
                  <span
                    class="px-2 py-1 rounded text-xs font-bold uppercase bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600"
                  >
                    Ujian Sidang
                  </span>
                  <span
                    class="px-2 py-1 rounded text-xs font-bold"
                    :class="getStatusClass(item.status)"
                  >
                    {{ item.status }}
                  </span>
                </div>
                <div class="grid grid-cols-2 gap-4 text-sm">
                  <div>
                    <p class="text-text-secondary text-xs">Tanggal</p>
                    <p class="font-medium text-text-main">
                      {{ formatDate(item.tanggal) }}
                    </p>
                  </div>
                  <div>
                    <p class="text-text-secondary text-xs">Waktu</p>
                    <p class="font-medium text-text-main">
                      {{ item.waktu || "-" }}
                    </p>
                  </div>
                  <div>
                    <p class="text-text-secondary text-xs">Ruangan</p>
                    <p class="font-medium text-text-main">
                      {{ item.ruangan || "-" }}
                    </p>
                  </div>
                  <div>
                    <p class="text-text-secondary text-xs">Nilai</p>
                    <p class="font-medium text-text-main">
                      {{ item.nilai ?? "-" }}
                    </p>
                  </div>
                </div>
                <!-- Penguji -->
                <div
                  v-if="item.penguji?.length > 0"
                  class="mt-4 pt-3 border-t border-border-light"
                >
                  <p
                    class="text-xs font-bold text-text-secondary uppercase tracking-wide mb-2"
                  >
                    Dosen Penguji
                  </p>
                  <div class="space-y-2">
                    <div
                      v-for="(p, idx) in item.penguji"
                      :key="p.id"
                      class="flex items-center gap-2 text-sm"
                    >
                      <div
                        class="size-7 rounded-full bg-indigo-100 dark:bg-indigo-900/20 text-indigo-600 flex items-center justify-center text-xs font-bold"
                      >
                        {{ idx + 1 }}
                      </div>
                      <span class="text-text-main font-medium">{{
                        p.dosen?.nama || p.dosen?.full_name || "-"
                      }}</span>
                    </div>
                  </div>
                </div>
              </div>
              <div
                v-if="sidangItems.length === 0"
                class="text-center py-8 text-text-secondary"
              >
                <span class="material-symbols-outlined text-4xl mb-2 block"
                  >gavel</span
                >
                <p>Belum ada jadwal ujian sidang</p>
              </div>
            </div>
          </div>

          <!-- Dokumen Tab -->
          <div v-else-if="activeTab === 'dokumen'">
            <!-- File Skripsi -->
            <div v-if="skripsi.file_skripsi" class="mb-6">
              <h4 class="font-bold text-text-main mb-3">File Skripsi</h4>
              <div
                class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-white/5 rounded-lg border border-border-light"
              >
                <div
                  class="size-12 rounded-lg bg-red-100 dark:bg-red-900/20 text-red-500 flex items-center justify-center"
                >
                  <span class="material-symbols-outlined text-[28px]"
                    >description</span
                  >
                </div>
                <div class="flex-1 min-w-0">
                  <p class="font-bold text-text-main text-sm truncate">
                    {{ getFileName(skripsi.file_skripsi) }}
                  </p>
                  <p class="text-xs text-text-secondary mt-0.5">
                    Diupload pada {{ formatDateTime(skripsi.updated_at) }}
                  </p>
                </div>
                <div class="flex items-center gap-2">
                  <a
                    :href="skripsi.file_skripsi_url"
                    target="_blank"
                    class="flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-primary bg-primary/10 hover:bg-primary/20 rounded-lg transition-colors"
                    title="Lihat File"
                  >
                    <span class="material-symbols-outlined text-[18px]"
                      >visibility</span
                    >
                    Lihat
                  </a>
                  <a
                    :href="skripsi.file_skripsi_url"
                    :download="getFileName(skripsi.file_skripsi)"
                    class="flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-green-600 bg-green-50 hover:bg-green-100 rounded-lg transition-colors"
                    title="Download File"
                  >
                    <span class="material-symbols-outlined text-[18px]"
                      >download</span
                    >
                    Download
                  </a>
                </div>
              </div>
            </div>

            <!-- Other Dokumen -->
            <div v-if="skripsi.dokumen?.length > 0">
              <h4 class="font-bold text-text-main mb-3">Dokumen Lainnya</h4>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div
                  v-for="doc in skripsi.dokumen.filter(
                    (d) => d.jenis !== 'revisi_proposal',
                  )"
                  :key="doc.id"
                  class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-white/5 rounded-lg border border-border-light hover:border-primary/30 transition-colors"
                >
                  <div
                    class="size-10 rounded-lg bg-red-100 dark:bg-red-900/20 text-red-500 flex items-center justify-center shrink-0"
                  >
                    <span class="material-symbols-outlined"
                      >picture_as_pdf</span
                    >
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="font-bold text-text-main text-sm truncate">
                      {{ doc.nama_file || doc.jenis }}
                    </p>
                    <p class="text-xs text-text-secondary">
                      {{ formatDate(doc.created_at) }}
                      <span v-if="doc.ukuran">
                        • {{ formatFileSize(doc.ukuran) }}</span
                      >
                    </p>
                  </div>
                  <div class="flex items-center gap-1.5 shrink-0">
                    <a
                      :href="getFileUrl(doc.path)"
                      target="_blank"
                      class="size-8 flex items-center justify-center rounded-lg border border-border-light text-text-secondary hover:text-primary hover:border-primary transition-all"
                      title="Lihat"
                    >
                      <span class="material-symbols-outlined text-[16px]"
                        >visibility</span
                      >
                    </a>
                    <a
                      :href="getFileUrl(doc.path)"
                      :download="doc.nama_file || doc.jenis"
                      class="size-8 flex items-center justify-center rounded-lg border border-border-light text-text-secondary hover:text-green-600 hover:border-green-400 transition-all"
                      title="Unduh"
                    >
                      <span class="material-symbols-outlined text-[16px]"
                        >download</span
                      >
                    </a>
                  </div>
                </div>
              </div>
            </div>

            <!-- Revisi Proposal Dokumen -->
            <div v-if="revisiProposalDocs.length > 0">
              <h4 class="font-bold text-text-main mb-3 flex items-center gap-2">
                <span
                  class="material-symbols-outlined text-yellow-600 text-[20px]"
                  >rate_review</span
                >
                Revisi Proposal
                <span
                  class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-yellow-50 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-400 border border-yellow-200 dark:border-yellow-800"
                >
                  {{ revisiProposalDocs.length }} berkas
                </span>
              </h4>
              <div class="space-y-3">
                <div
                  v-for="doc in revisiProposalDocs"
                  :key="doc.id"
                  class="flex items-center justify-between p-4 rounded-xl border transition-colors"
                  :class="
                    doc.status === 'pending'
                      ? 'border-yellow-300 dark:border-yellow-800 bg-yellow-50/50 dark:bg-yellow-900/10'
                      : 'border-border-light bg-gray-50 dark:bg-white/5'
                  "
                >
                  <div class="flex items-center gap-3 min-w-0">
                    <div
                      class="size-10 rounded-lg bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 flex items-center justify-center shrink-0"
                    >
                      <span class="material-symbols-outlined">description</span>
                    </div>
                    <div class="min-w-0">
                      <p class="font-bold text-text-main text-sm truncate">
                        {{ doc.nama_file || "Revisi Proposal" }}
                      </p>
                      <p class="text-xs text-text-secondary">
                        {{ formatDate(doc.created_at) }}
                        <span v-if="doc.ukuran">
                          • {{ formatFileSize(doc.ukuran) }}</span
                        >
                      </p>
                    </div>
                  </div>
                  <div class="flex items-center gap-2 shrink-0">
                    <span
                      class="px-2.5 py-1 rounded-full text-[10px] font-bold"
                      :class="
                        doc.status === 'approved'
                          ? 'bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400'
                          : doc.status === 'rejected'
                            ? 'bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-400'
                            : 'bg-yellow-100 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-400'
                      "
                    >
                      {{
                        doc.status === "approved"
                          ? "Disetujui"
                          : doc.status === "rejected"
                            ? "Ditolak"
                            : "Menunggu"
                      }}
                    </span>
                    <a
                      :href="getFileUrl(doc.path)"
                      target="_blank"
                      class="size-8 flex items-center justify-center rounded-lg border border-border-light text-text-secondary hover:text-primary hover:border-primary transition-all"
                      title="Lihat"
                    >
                      <span class="material-symbols-outlined text-[16px]"
                        >visibility</span
                      >
                    </a>
                    <template v-if="doc.status === 'pending'">
                      <button
                        @click="approveRevisiProposal(doc.id)"
                        :disabled="approvingRevisiProposal === doc.id"
                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50"
                      >
                        <span class="material-symbols-outlined text-[14px]"
                          >check</span
                        >
                        {{
                          approvingRevisiProposal === doc.id
                            ? "Proses..."
                            : "Setujui"
                        }}
                      </button>
                      <button
                        @click="rejectRevisiProposal(doc.id)"
                        :disabled="approvingRevisiProposal === doc.id"
                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50"
                      >
                        <span class="material-symbols-outlined text-[14px]"
                          >close</span
                        >
                        Tolak
                      </button>
                    </template>
                  </div>
                </div>
              </div>
            </div>

            <div
              v-if="!skripsi.file_skripsi && !skripsi.dokumen?.length"
              class="text-center py-8 text-text-secondary"
            >
              <span class="material-symbols-outlined text-4xl mb-2 block"
                >folder_open</span
              >
              <p>Belum ada dokumen diunggah</p>
            </div>
          </div>

          <!-- History Tab -->
          <div v-else-if="activeTab === 'history'">
            <div
              v-if="skripsi.history?.length > 0"
              class="space-y-3 max-h-96 overflow-y-auto"
            >
              <div
                v-for="history in skripsi.history"
                :key="history.id"
                class="flex gap-4 p-4 bg-gray-50 dark:bg-white/5 rounded-lg border border-border-light"
              >
                <div
                  class="size-8 rounded-full bg-primary/10 text-primary flex items-center justify-center shrink-0"
                >
                  <span class="material-symbols-outlined text-[18px]"
                    >history</span
                  >
                </div>
                <div class="flex-1">
                  <div class="flex justify-between items-start">
                    <p class="font-bold text-text-main text-sm">
                      {{ history.keterangan || "Perubahan data" }}
                    </p>
                    <span class="text-xs text-text-secondary">{{
                      formatDateTime(history.created_at)
                    }}</span>
                  </div>
                  <p class="text-xs text-text-secondary mt-1">
                    <span v-if="history.status_lama"
                      >Status: {{ history.status_lama }} →
                      {{ history.status_baru }}</span
                    >
                    <span v-if="history.updated_by">
                      • oleh {{ history.updated_by?.name }}</span
                    >
                  </p>
                </div>
              </div>
            </div>
            <div v-else class="text-center py-8 text-text-secondary">
              <span class="material-symbols-outlined text-4xl mb-2 block"
                >history</span
              >
              <p>Belum ada riwayat perubahan</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Catatan Admin -->
      <div
        v-if="skripsi.catatan_admin"
        class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl p-5"
      >
        <h3 class="text-sm font-bold text-yellow-700 dark:text-yellow-400 mb-2">
          <span class="material-symbols-outlined text-[18px] align-middle mr-1"
            >sticky_note_2</span
          >
          Catatan Admin
        </h3>
        <p class="text-yellow-800 dark:text-yellow-300 text-sm">
          {{ skripsi.catatan_admin }}
        </p>
      </div>
    </template>

    <!-- Not Found -->
    <div v-else class="text-center py-12 text-text-secondary">
      <span class="material-symbols-outlined text-5xl mb-3 block"
        >search_off</span
      >
      <p class="text-lg font-medium">Data skripsi tidak ditemukan</p>
      <router-link
        to="/admin/skripsi"
        class="text-primary hover:underline mt-2 inline-block"
        >Kembali ke daftar</router-link
      >
    </div>

    <!-- Edit Status Modal -->
    <Transition name="modal-fade">
      <div
        v-if="showEditModal"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
      >
        <div
          class="bg-white dark:bg-surface-light rounded-xl shadow-2xl w-full max-w-md"
        >
          <div class="p-6 border-b border-border-light">
            <h2 class="text-xl font-bold text-text-main">
              Update Status Skripsi
            </h2>
            <p class="text-sm text-text-secondary mt-1">
              {{ skripsi.mahasiswa?.nama }}
            </p>
          </div>
          <form @submit.prevent="saveStatus" class="p-6 space-y-4">
            <div class="relative" ref="statusDropdownRef">
              <label class="block text-sm font-medium text-text-main mb-1"
                >Status</label
              >
              <button
                type="button"
                @click="statusDropdownOpen = !statusDropdownOpen"
                class="w-full px-3 py-2.5 border border-border-light rounded-lg bg-white dark:bg-white/5 text-text-main focus:ring-2 focus:ring-primary/20 focus:border-primary text-left flex items-center justify-between transition-colors"
              >
                <span>{{ getStatusLabel(editForm.status) }}</span>
                <span
                  class="material-symbols-outlined text-[18px] text-text-secondary transition-transform"
                  :class="{ 'rotate-180': statusDropdownOpen }"
                  >expand_more</span
                >
              </button>
              <Transition name="dropdown-fade">
                <div
                  v-if="statusDropdownOpen"
                  class="absolute top-full left-0 right-0 mt-1 bg-white dark:bg-sidebar-light border border-border-light rounded-lg shadow-xl z-20 py-1 max-h-60 overflow-y-auto"
                >
                  <button
                    v-for="opt in statusOptionsFiltered"
                    :key="opt.value"
                    type="button"
                    @click="
                      editForm.status = opt.value;
                      statusDropdownOpen = false;
                    "
                    class="w-full px-3 py-2 text-left text-sm transition-colors flex items-center justify-between"
                    :class="
                      editForm.status === opt.value
                        ? 'bg-primary/10 text-primary font-bold'
                        : 'text-text-main hover:bg-gray-100 dark:hover:bg-white/10'
                    "
                  >
                    {{ opt.label }}
                    <span
                      v-if="editForm.status === opt.value"
                      class="material-symbols-outlined text-[16px] text-primary"
                      >check</span
                    >
                  </button>
                </div>
              </Transition>
            </div>
            <div>
              <label class="block text-sm font-medium text-text-main mb-1"
                >Catatan Admin</label
              >
              <textarea
                v-model="editForm.catatan_admin"
                rows="3"
                class="w-full px-3 py-2.5 border border-border-light rounded-lg bg-white dark:bg-white/5 text-text-main focus:ring-2 focus:ring-primary/20 focus:border-primary"
                placeholder="Tambahkan catatan..."
              ></textarea>
            </div>
            <div class="flex gap-3 pt-4">
              <button
                type="button"
                @click="showEditModal = false"
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
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, reactive } from "vue";
import { useRoute, useRouter } from "vue-router";
import adminService from "../../../services/adminService";

const route = useRoute();
const router = useRouter();

import { useAuthStore } from "../../../stores/auth";
const authStore = useAuthStore();

const loading = ref(true);
const saving = ref(false);
const skripsi = ref(null);
const showEditModal = ref(false);
const activeTab = ref("bimbingan");
const approvingRevisiProposal = ref(null);
const statusDropdownOpen = ref(false);
const statusDropdownRef = ref(null);

const statusOptions = [
  { value: "pengajuan", label: "Pengajuan" },
  { value: "disetujui", label: "Disetujui" },
  { value: "ditolak", label: "Ditolak" },
  { value: "proposal", label: "Proposal" },
  { value: "sempro", label: "Sempro" },
  { value: "bimbingan", label: "Bimbingan" },
  { value: "semhas", label: "Semhas" },
  { value: "sidang", label: "Sidang" },
  { value: "revisi", label: "Revisi" },
  { value: "lulus", label: "Lulus" },
];

const statusOptionsFiltered = computed(() =>
  statusOptions.filter(
    (opt) => opt.value !== "semhas" || authStore.semhasEnabled,
  ),
);

const handleClickOutside = (e) => {
  if (statusDropdownRef.value && !statusDropdownRef.value.contains(e.target)) {
    statusDropdownOpen.value = false;
  }
};

const editForm = reactive({
  status: "",
  catatan_admin: "",
});

const tabs = [
  { id: "bimbingan", label: "Bimbingan" },
  { id: "seminar", label: "Seminar" },
  { id: "sidang", label: "Sidang" },
  { id: "dokumen", label: "Dokumen" },
  { id: "history", label: "Riwayat" },
];

const pembimbing = computed(() => {
  return skripsi.value?.pembimbing || [];
});

const filteredSeminars = computed(() => {
  const seminars = skripsi.value?.seminar || [];
  // Only include sempro and semhas (actual seminars) — everything else goes to Sidang tab
  return seminars.filter((s) => s.jenis === "sempro" || s.jenis === "semhas");
});

// Combine non-seminar entries from seminar table + ujian entries for Sidang tab
const sidangItems = computed(() => {
  const items = [];
  // Seminar entries that are NOT sempro or semhas (i.e. ujian/sidang data stored in seminar table)
  const sidangFromSeminar = (skripsi.value?.seminar || []).filter(
    (s) => s.jenis !== "sempro" && s.jenis !== "semhas",
  );
  sidangFromSeminar.forEach((s) =>
    items.push({ ...s, _key: `seminar-${s.id}` }),
  );
  // Ujian entries from ujian table
  const ujians = skripsi.value?.ujian || [];
  ujians.forEach((u) => items.push({ ...u, _key: `ujian-${u.id}` }));
  return items;
});

const revisiProposalDocs = computed(() => {
  const docs = skripsi.value?.dokumen || [];
  return docs.filter((d) => d.jenis === "revisi_proposal");
});

const approveRevisiProposal = async (docId) => {
  if (
    !confirm(
      "Apakah Anda yakin ingin menyetujui dokumen revisi proposal ini? Status skripsi akan berubah menjadi Penentuan Dospem.",
    )
  )
    return;
  try {
    approvingRevisiProposal.value = docId;
    await adminService.updateDokumen(docId, { status: "approved" });
    alert("Dokumen revisi proposal disetujui.");
    fetchSkripsi();
  } catch (error) {
    console.error("Failed to approve revisi proposal:", error);
    alert(
      "Gagal menyetujui: " + (error.response?.data?.message || error.message),
    );
  } finally {
    approvingRevisiProposal.value = null;
  }
};

const rejectRevisiProposal = async (docId) => {
  const catatan = prompt("Alasan penolakan (opsional):");
  if (catatan === null) return;
  try {
    approvingRevisiProposal.value = docId;
    await adminService.updateDokumen(docId, { status: "rejected", catatan });
    alert("Dokumen revisi proposal ditolak.");
    fetchSkripsi();
  } catch (error) {
    console.error("Failed to reject revisi proposal:", error);
    alert("Gagal menolak: " + (error.response?.data?.message || error.message));
  } finally {
    approvingRevisiProposal.value = null;
  }
};

const fetchSkripsi = async () => {
  try {
    loading.value = true;
    const id = route.params.id;
    const response = await adminService.getSkripsiDetail(id);
    if (response.success) {
      skripsi.value = response.data;
      editForm.status = skripsi.value.status;
      editForm.catatan_admin = skripsi.value.catatan_admin || "";
    }
  } catch (error) {
    console.error("Failed to fetch skripsi:", error);
  } finally {
    loading.value = false;
  }
};

const saveStatus = async () => {
  try {
    saving.value = true;
    await adminService.updateSkripsi(skripsi.value.id, editForm);
    showEditModal.value = false;
    fetchSkripsi();
  } catch (error) {
    console.error("Failed to update:", error);
    alert(
      "Gagal menyimpan: " + (error.response?.data?.message || error.message),
    );
  } finally {
    saving.value = false;
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

const formatDateTime = (date) => {
  if (!date) return "-";
  const d = new Date(date);
  const datePart = d.toLocaleDateString("id-ID", {
    day: "numeric",
    month: "short",
    year: "numeric",
  });
  const timePart = d.toLocaleTimeString("id-ID", {
    hour: "2-digit",
    minute: "2-digit",
    second: "2-digit",
    hour12: false,
  });
  return `${datePart}, ${timePart}`;
};

const getFileName = (filePath) => {
  if (!filePath) return "-";
  const parts = filePath.split("/");
  const fileName = parts[parts.length - 1];
  // Remove timestamp prefix (e.g., "1770930493_")
  return fileName.replace(/^\d+_/, "");
};

const getStatusClass = (status) => {
  const classes = {
    pengajuan: "bg-yellow-50 text-yellow-600 border-yellow-200",
    disetujui: "bg-green-50 text-green-600 border-green-200",
    ditolak: "bg-red-50 text-red-600 border-red-200",
    proposal: "bg-blue-50 text-blue-600 border-blue-200",
    sempro: "bg-purple-50 text-purple-600 border-purple-200",
    bimbingan: "bg-cyan-50 text-cyan-600 border-cyan-200",
    semhas: "bg-orange-50 text-orange-600 border-orange-200",
    sidang: "bg-indigo-50 text-indigo-600 border-indigo-200",
    revisi: "bg-pink-50 text-pink-600 border-pink-200",
    lulus: "bg-emerald-50 text-emerald-600 border-emerald-200",
    terjadwal: "bg-blue-50 text-blue-600 border-blue-200",
    selesai: "bg-green-50 text-green-600 border-green-200",
    pending: "bg-yellow-50 text-yellow-600 border-yellow-200",
  };
  return classes[status] || "bg-gray-50 text-gray-600 border-gray-200";
};

const getStatusLabel = (status) => {
  const labels = {
    pengajuan: "Pengajuan",
    disetujui: "Disetujui",
    ditolak: "Ditolak",
    proposal: "Proposal",
    sempro: "Sempro",
    bimbingan: "Bimbingan",
    semhas: "Seminar Hasil",
    sidang: "Sidang",
    revisi: "Revisi",
    lulus: "Lulus",
  };
  return labels[status] || status;
};

const getFileUrl = (path) => {
  if (!path) return "#";
  if (path.startsWith("http")) return path;
  const baseUrl =
    import.meta.env.VITE_API_URL?.replace("/api", "") || "";
  return `${baseUrl}/api/file/${path}`;
};

const formatFileSize = (bytes) => {
  if (!bytes) return "";
  if (bytes < 1024) return bytes + " B";
  if (bytes < 1048576) return (bytes / 1024).toFixed(1) + " KB";
  return (bytes / 1048576).toFixed(1) + " MB";
};

onMounted(() => {
  fetchSkripsi();
  document.addEventListener("click", handleClickOutside);
});

onBeforeUnmount(() => {
  document.removeEventListener("click", handleClickOutside);
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
</style>
