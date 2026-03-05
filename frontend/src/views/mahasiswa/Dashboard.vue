<template>
  <div class="flex flex-col gap-6">
    <!-- Loading State -->
    <div v-if="loading" class="flex flex-col gap-6 animate-pulse">
      <div class="h-10 w-80 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
      <div class="h-5 w-64 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
      <div class="h-56 bg-gray-200 dark:bg-gray-700 rounded-xl"></div>
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div
          class="lg:col-span-2 h-64 bg-gray-200 dark:bg-gray-700 rounded-xl"
        ></div>
        <div class="h-64 bg-gray-200 dark:bg-gray-700 rounded-xl"></div>
      </div>
    </div>

    <!-- No Skripsi State -->
    <template v-else-if="!skripsi">
      <section class="flex flex-col gap-1">
        <h2 class="text-3xl font-bold tracking-tight text-text-main">
          Selamat Datang, {{ mahasiswa?.nama || "Mahasiswa" }}
        </h2>
        <p class="text-text-secondary text-base">
          Pantau progres skripsi Anda dan selesaikan tahapan berikutnya.
        </p>
      </section>

      <!-- Ditolak setelah sempro: is_active false, tampilkan info -->
      <div
        v-if="rejectedSkripsi"
        class="bg-surface-light rounded-xl shadow-sm border border-red-200 dark:border-red-800 overflow-hidden"
      >
        <div
          class="p-6 flex flex-col items-center justify-center gap-4 text-center"
        >
          <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-full">
            <span class="material-symbols-outlined text-5xl text-red-500"
              >block</span
            >
          </div>
          <h3 class="text-xl font-bold text-red-700 dark:text-red-400">
            Skripsi Tidak Lulus Seminar Proposal
          </h3>
          <p class="text-text-secondary max-w-md">
            Maaf, judul skripsi "<strong>{{ rejectedSkripsi.judul }}</strong
            >" tidak lulus pada seminar proposal. Silakan ajukan judul baru
            untuk memulai kembali proses skripsi.
          </p>
          <router-link
            to="/mahasiswa/skripsi"
            class="mt-2 inline-flex items-center gap-2 bg-primary hover:bg-blue-600 text-white font-bold px-6 py-3 rounded-lg transition-all shadow-md shadow-primary/20"
          >
            <span class="material-symbols-outlined text-[20px]">add</span>
            Ajukan Judul Baru
          </router-link>
        </div>
      </div>

      <div
        v-else
        class="bg-surface-light rounded-xl shadow-sm border border-border-light p-12 flex flex-col items-center justify-center gap-4 text-center"
      >
        <span
          class="material-symbols-outlined text-6xl text-text-secondary opacity-40"
          >menu_book</span
        >
        <h3 class="text-xl font-bold text-text-main">
          Belum Ada Skripsi Aktif
        </h3>
        <p class="text-text-secondary max-w-md">
          Anda belum memiliki skripsi aktif. Silakan ajukan judul skripsi
          melalui halaman Skripsi Saya.
        </p>
        <router-link
          to="/mahasiswa/skripsi"
          class="mt-2 inline-flex items-center gap-2 bg-primary hover:bg-blue-600 text-white font-bold px-6 py-3 rounded-lg transition-all shadow-md shadow-primary/20"
        >
          <span class="material-symbols-outlined text-[20px]">add</span>
          Ajukan Skripsi
        </router-link>
      </div>
    </template>

    <!-- Main Dashboard Content -->
    <template v-else>
      <!-- Page Heading -->
      <section class="flex flex-col gap-1">
        <h2 class="text-3xl font-bold tracking-tight text-text-main">
          Selamat Datang, {{ mahasiswa?.nama || "Mahasiswa" }}
        </h2>
        <p class="text-text-secondary text-base">
          Pantau progres skripsi Anda dan selesaikan tahapan berikutnya.
        </p>
      </section>

      <!-- Thesis Summary Card -->
      <section
        class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden transition-all hover:shadow-md"
      >
        <div class="p-6 md:p-8 flex flex-col md:flex-row gap-8">
          <!-- Left: Info & Title -->
          <div class="flex-1 flex flex-col gap-4">
            <div class="flex items-center gap-3">
              <span
                class="px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-900/30 text-primary text-xs font-bold uppercase tracking-wide"
              >
                Skripsi Anda
              </span>
              <div class="flex items-center gap-1 text-text-secondary text-xs">
                <span class="material-symbols-outlined text-[16px]"
                  >schedule</span
                >
                <span>Terakhir update: {{ lastUpdated }}</span>
              </div>
            </div>
            <div>
              <h3 class="text-2xl font-bold leading-snug text-text-main mb-2">
                {{ skripsi.judul }}
              </h3>
              <p class="text-text-secondary text-sm" v-if="pembimbingNames">
                Pembimbing: {{ pembimbingNames }}
              </p>
            </div>
            <div class="mt-auto pt-4 flex items-center gap-2">
              <div
                :class="statusBadgeClass"
                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-semibold"
              >
                <span
                  class="material-symbols-outlined text-[18px] filled"
                  :class="statusIconClass"
                  >{{ statusIcon }}</span
                >
                Status: {{ statusLabel }}
              </div>
            </div>
          </div>

          <!-- Right: Visual Progress -->
          <div
            class="flex-1 md:max-w-[320px] bg-sidebar-light rounded-xl p-5 flex flex-col justify-center gap-4 border border-border-light"
          >
            <div class="flex justify-between items-end mb-1">
              <span class="text-sm font-semibold text-text-main"
                >Progres Keseluruhan</span
              >
              <span class="text-2xl font-bold text-primary"
                >{{ progress }}%</span
              >
            </div>
            <!-- Progress Track -->
            <div
              class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 mb-2 overflow-hidden"
            >
              <div
                class="bg-primary h-2.5 rounded-full transition-all duration-1000 ease-out"
                :style="{ width: progress + '%' }"
              ></div>
            </div>
            <!-- Milestones -->
            <div
              class="flex justify-between text-xs text-text-secondary font-medium"
            >
              <div
                v-for="milestone in milestones"
                :key="milestone.key"
                class="flex flex-col items-center gap-1"
                :class="{
                  'text-primary font-bold': milestone.isCurrent,
                  'opacity-50': !milestone.isCompleted && !milestone.isCurrent,
                }"
              >
                <div
                  class="w-2.5 h-2.5 rounded-full"
                  :class="
                    milestone.isCompleted || milestone.isCurrent
                      ? 'bg-primary ring-2 ring-blue-100 dark:ring-blue-900/30'
                      : 'bg-gray-300 dark:bg-gray-600'
                  "
                ></div>
                <span>{{ milestone.label }}</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Active Action / Next Steps -->
      <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Action Card -->
        <div
          class="lg:col-span-2 bg-surface-light rounded-xl shadow-sm border border-border-light p-6 md:p-8 flex flex-col justify-center relative overflow-hidden group hover:shadow-md transition-all"
        >
          <!-- Decorative gradient background -->
          <div
            class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-primary/5 to-transparent rounded-bl-full pointer-events-none"
          ></div>

          <div class="relative z-10 flex flex-col gap-6">
            <div class="flex items-start justify-between">
              <div class="flex flex-col gap-3">
                <div
                  class="flex items-center gap-2 text-primary font-bold text-sm uppercase tracking-wider"
                >
                  <span
                    class="material-symbols-outlined text-[20px] animate-pulse"
                    >priority_high</span
                  >
                  Perhatian Diperlukan
                </div>
                <h2 class="text-2xl font-bold text-text-main">
                  Informasi Tahap Aktif
                </h2>
              </div>
            </div>
            <div
              class="prose prose-blue max-w-none text-text-secondary leading-relaxed"
            >
              <p>
                {{ activeStageDescription }}
              </p>
              <!-- Upcoming schedule info -->
              <div
                v-if="upcomingSeminar"
                class="mt-4 flex items-center gap-3 p-4 bg-blue-50 dark:bg-blue-900/10 rounded-lg border-l-4 border-primary"
              >
                <span class="material-symbols-outlined text-primary"
                  >event</span
                >
                <p class="text-sm text-text-main">
                  <span class="font-bold">Jadwal Seminar:</span>
                  {{ formatDate(upcomingSeminar.tanggal) }}
                </p>
              </div>
              <div
                v-if="upcomingUjian"
                class="mt-4 flex items-center gap-3 p-4 bg-green-50 dark:bg-green-900/10 rounded-lg border-l-4 border-green-500"
              >
                <span class="material-symbols-outlined text-green-600"
                  >event</span
                >
                <p class="text-sm text-text-main">
                  <span class="font-bold">Jadwal Sidang:</span>
                  {{ formatDate(upcomingUjian.tanggal) }}
                </p>
              </div>
            </div>
            <div class="pt-2">
              <router-link
                to="/mahasiswa/skripsi/detail"
                class="inline-flex items-center justify-center h-12 px-6 rounded-lg bg-primary hover:bg-blue-600 text-white font-bold text-sm shadow-md shadow-primary/20 transition-all transform active:scale-95 group-hover:translate-x-1"
              >
                <span>Lihat Detail Skripsi</span>
                <span class="material-symbols-outlined ml-2 text-[20px]"
                  >arrow_forward</span
                >
              </router-link>
            </div>
          </div>
        </div>

        <!-- Side Widget: Quick Actions / Stats -->
        <div class="flex flex-col gap-4">
          <div
            class="bg-surface-light rounded-xl shadow-sm border border-border-light p-5 flex items-center gap-4 hover:shadow-md transition-all"
          >
            <div
              class="w-12 h-12 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-primary"
            >
              <span class="material-symbols-outlined">description</span>
            </div>
            <div>
              <p class="text-sm text-text-secondary font-medium">
                Total Dokumen
              </p>
              <p class="text-xl font-bold text-text-main">
                {{ stats.total_dokumen ?? 0 }} Berkas
              </p>
            </div>
          </div>
          <div
            class="bg-surface-light rounded-xl shadow-sm border border-border-light p-5 flex items-center gap-4 hover:shadow-md transition-all"
          >
            <div
              class="w-12 h-12 rounded-full bg-orange-50 dark:bg-orange-900/20 flex items-center justify-center text-orange-500"
            >
              <span class="material-symbols-outlined">forum</span>
            </div>
            <div>
              <p class="text-sm text-text-secondary font-medium">Bimbingan</p>
              <p class="text-xl font-bold text-text-main">
                {{ stats.total_bimbingan ?? 0 }} Sesi
              </p>
            </div>
          </div>
          <div
            class="flex-1 bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl shadow-sm p-6 text-white relative overflow-hidden flex flex-col justify-end group transition-all"
            data-alt="Abstract dark pattern background for aesthetic widget"
          >
            <div
              class="absolute top-0 left-0 w-full h-full opacity-10 group-hover:opacity-20 transition-opacity duration-700"
              style="
                background-image: radial-gradient(
                  circle at 2px 2px,
                  white 1px,
                  transparent 0
                );
                background-size: 20px 20px;
              "
            ></div>
            <div class="relative z-10">
              <p class="text-gray-300 text-sm mb-1 font-medium">
                Butuh Bantuan?
              </p>
              <p class="font-bold mb-4 text-lg">Hubungi Admin Prodi</p>
              <button
                @click="openChatWithAdmin"
                class="text-xs font-bold bg-white/10 hover:bg-white/20 backdrop-blur-sm py-2.5 px-4 rounded-lg text-white transition-all border border-white/10 hover:border-white/30 flex items-center gap-2 w-fit cursor-pointer"
              >
                <span class="material-symbols-outlined text-[16px]"
                  >support_agent</span
                >
                Kontak Admin
              </button>
            </div>
          </div>
        </div>
      </section>

      <!-- Upload Proposal Section (for disetujui status) -->
      <section
        v-if="skripsi?.status === 'disetujui'"
        class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden hover:shadow-md transition-all"
      >
        <div
          class="p-5 border-b border-border-light flex items-center justify-between"
        >
          <div>
            <h3 class="text-lg font-bold text-text-main">
              Upload Proposal Skripsi
            </h3>
            <p class="text-sm text-text-secondary">
              Unggah dokumen proposal setelah judul disetujui
            </p>
          </div>
          <span
            class="px-3 py-1 rounded-full text-xs font-bold bg-green-50 text-green-700 border border-green-200 dark:bg-green-900/30 dark:text-green-300 dark:border-green-800"
          >
            Judul Disetujui
          </span>
        </div>
        <div class="p-5 space-y-4">
          <!-- Info alert -->
          <div
            class="flex items-start gap-3 p-4 bg-blue-50 dark:bg-blue-900/10 rounded-lg border-l-4 border-blue-400"
          >
            <span class="material-symbols-outlined text-blue-600 mt-0.5"
              >info</span
            >
            <div>
              <p class="text-sm text-text-main">
                Selamat! Judul skripsi Anda telah <strong>disetujui</strong>.
                Langkah selanjutnya adalah mengupload dokumen proposal Anda.
                Setelah proposal diperiksa dan disetujui oleh staff/admin,
                status skripsi Anda akan berubah menjadi
                <strong>Tahap Proposal</strong>.
              </p>
            </div>
          </div>

          <!-- Upload form -->
          <div class="border border-dashed border-border-light rounded-xl p-4">
            <div class="flex items-center gap-3 mb-3">
              <span class="material-symbols-outlined text-primary text-xl"
                >upload_file</span
              >
              <p class="text-sm font-bold text-text-main">
                Unggah Dokumen Proposal
              </p>
            </div>
            <div class="flex gap-3">
              <input
                type="file"
                ref="proposalFileInput"
                accept=".pdf,.doc,.docx"
                class="flex-1 px-3 py-2 border border-border-light rounded-lg bg-background-light text-text-main text-sm dark:bg-background"
              />
              <button
                @click="uploadProposal"
                :disabled="uploadingProposal"
                class="px-5 py-2 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors text-sm font-bold disabled:opacity-50 flex items-center gap-2"
              >
                <span
                  v-if="uploadingProposal"
                  class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"
                ></span>
                <span v-else class="material-symbols-outlined text-[18px]"
                  >cloud_upload</span
                >
                {{ uploadingProposal ? "Mengunggah..." : "Unggah" }}
              </button>
            </div>
          </div>

          <!-- Uploaded docs list -->
          <div v-if="proposalDocs.length > 0" class="space-y-2">
            <p
              class="text-xs font-bold text-text-secondary uppercase tracking-wider"
            >
              Dokumen Proposal yang Diunggah
            </p>
            <div
              v-for="doc in proposalDocs"
              :key="doc.id"
              class="flex items-center justify-between p-3 border border-border-light rounded-lg"
            >
              <div class="flex items-center gap-3 min-w-0">
                <span
                  class="material-symbols-outlined text-xl"
                  :class="
                    doc.status === 'approved'
                      ? 'text-green-600'
                      : doc.status === 'rejected'
                        ? 'text-red-600'
                        : 'text-yellow-600'
                  "
                  >description</span
                >
                <div class="min-w-0">
                  <p class="text-sm font-medium text-text-main truncate">
                    {{ doc.nama_file }}
                  </p>
                  <p class="text-[10px] text-text-secondary">
                    {{
                      new Date(doc.created_at).toLocaleDateString("id-ID", {
                        day: "numeric",
                        month: "long",
                        year: "numeric",
                      })
                    }}
                  </p>
                </div>
              </div>
              <span
                class="px-2.5 py-1 rounded-full text-[10px] font-bold shrink-0"
                :class="
                  doc.status === 'approved'
                    ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300'
                    : doc.status === 'rejected'
                      ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300'
                      : 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300'
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
            </div>
          </div>
        </div>
      </section>

      <!-- Ujian Skripsi Request Section -->
      <section
        v-if="
          skripsi?.status === 'bimbingan' ||
          skripsi?.status === 'pengajuan_sidang' ||
          skripsi?.status === 'pengajuan_sidang_tolak' ||
          skripsi?.status === 'pengajuan_sidang_acc'
        "
        class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden hover:shadow-md transition-all"
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

          <!-- Approved: waiting for admin to schedule -->
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

          <!-- Rejected: show reason + re-submit -->
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
                <p
                  class="text-sm font-bold text-red-700 dark:text-red-400 mb-1"
                >
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
                @click="submitUjianRequest"
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
                  v-for="(item, idx) in eligibilityChecklist"
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
                  @click="showUjianConfirmModal = true"
                  class="inline-flex items-center gap-2 bg-primary hover:bg-blue-600 text-white font-bold px-6 py-3 rounded-lg transition-all shadow-md shadow-primary/20 text-sm"
                >
                  <span class="material-symbols-outlined text-[20px]"
                    >school</span
                  >
                  Ajukan Ujian Skripsi
                </button>
                <p v-else class="text-sm text-red-500 font-medium">
                  Anda belum memenuhi semua syarat untuk mengajukan ujian
                  skripsi.
                </p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Revisi Proposal Sempro Section (for lulus_bersyarat) -->
      <section
        v-if="isLulusBersyaratSempro"
        class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden hover:shadow-md transition-all"
      >
        <div
          class="p-5 border-b border-border-light flex items-center justify-between"
        >
          <div>
            <h3 class="text-lg font-bold text-text-main">
              Revisi Proposal Seminar
            </h3>
            <p class="text-sm text-text-secondary">
              Unggah dokumen revisi proposal sesuai catatan dosen penguji
            </p>
          </div>
          <span
            class="px-3 py-1 rounded-full text-xs font-bold bg-yellow-50 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 border border-yellow-200 dark:border-yellow-800"
          >
            Lulus Bersyarat
          </span>
        </div>
        <div class="p-5 space-y-4">
          <!-- Info alert -->
          <div
            class="flex items-start gap-3 p-4 bg-yellow-50 dark:bg-yellow-900/10 rounded-lg border-l-4 border-yellow-400"
          >
            <span class="material-symbols-outlined text-yellow-600 mt-0.5"
              >info</span
            >
            <div>
              <p class="text-sm text-text-main">
                Hasil seminar proposal Anda adalah
                <strong>Lulus Bersyarat</strong>. Silakan perbaiki proposal
                sesuai catatan dari dosen penguji, lalu unggah dokumen revisi
                proposal di bawah ini. Setelah disetujui oleh staff/admin,
                status Anda akan berubah menjadi
                <strong>Penentuan Dospem</strong>.
              </p>
            </div>
          </div>

          <!-- Upload form -->
          <div class="border border-dashed border-border-light rounded-xl p-4">
            <div class="flex items-center gap-3 mb-3">
              <span class="material-symbols-outlined text-primary text-xl"
                >upload_file</span
              >
              <p class="text-sm font-bold text-text-main">
                Unggah Revisi Proposal
              </p>
            </div>
            <div class="flex gap-3">
              <input
                type="file"
                ref="revisiProposalFileInput"
                accept=".pdf,.doc,.docx"
                class="flex-1 px-3 py-2 border border-border-light rounded-lg bg-background-light text-text-main text-sm dark:bg-background"
              />
              <button
                @click="uploadRevisiProposal"
                :disabled="uploadingRevisiProposal"
                class="px-5 py-2 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors text-sm font-bold disabled:opacity-50 flex items-center gap-2"
              >
                <span
                  v-if="uploadingRevisiProposal"
                  class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"
                ></span>
                <span v-else class="material-symbols-outlined text-[18px]"
                  >cloud_upload</span
                >
                {{ uploadingRevisiProposal ? "Mengunggah..." : "Unggah" }}
              </button>
            </div>
          </div>

          <!-- Uploaded docs list -->
          <div v-if="revisiProposalDocs.length > 0" class="space-y-2">
            <p
              class="text-xs font-bold text-text-secondary uppercase tracking-wider"
            >
              Dokumen yang Diunggah
            </p>
            <div
              v-for="doc in revisiProposalDocs"
              :key="doc.id"
              class="flex items-center justify-between p-3 border border-border-light rounded-lg"
            >
              <div class="flex items-center gap-3 min-w-0">
                <span
                  class="material-symbols-outlined text-xl"
                  :class="
                    doc.status === 'approved'
                      ? 'text-green-600'
                      : doc.status === 'rejected'
                        ? 'text-red-600'
                        : 'text-yellow-600'
                  "
                  >description</span
                >
                <div class="min-w-0">
                  <p class="text-sm font-medium text-text-main truncate">
                    {{ doc.nama_file }}
                  </p>
                  <p class="text-[10px] text-text-secondary">
                    {{
                      new Date(doc.created_at).toLocaleDateString("id-ID", {
                        day: "numeric",
                        month: "long",
                        year: "numeric",
                      })
                    }}
                  </p>
                </div>
              </div>
              <span
                class="px-2.5 py-1 rounded-full text-[10px] font-bold shrink-0"
                :class="
                  doc.status === 'approved'
                    ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300'
                    : doc.status === 'rejected'
                      ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300'
                      : 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300'
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
            </div>
          </div>
        </div>
      </section>

      <!-- Ujian Confirmation Modal -->
      <Transition name="modal-fade">
        <div
          v-if="showUjianConfirmModal"
          class="fixed inset-0 z-50 flex items-center justify-center p-4"
        >
          <div
            class="absolute inset-0 bg-black/50 backdrop-blur-sm"
            @click="showUjianConfirmModal = false"
          ></div>
          <div
            class="relative bg-surface-light rounded-xl shadow-xl border border-border-light w-full max-w-md p-6 flex flex-col gap-5"
          >
            <div class="flex items-center gap-3">
              <div class="p-2 bg-primary/10 rounded-lg text-primary">
                <span class="material-symbols-outlined">school</span>
              </div>
              <div>
                <h3 class="text-lg font-bold text-text-main">
                  Konfirmasi Pengajuan
                </h3>
                <p class="text-xs text-text-secondary">
                  Pengajuan ini akan dikirim ke dosen pembimbing utama
                </p>
              </div>
            </div>
            <p class="text-sm text-text-secondary">
              Apakah Anda yakin ingin mengajukan ujian skripsi? Pastikan semua
              persyaratan sudah terpenuhi dan naskah final sudah diunggah.
            </p>
            <div class="flex justify-end gap-3 pt-2">
              <button
                @click="showUjianConfirmModal = false"
                class="px-5 py-2.5 rounded-lg text-text-secondary font-bold hover:bg-sidebar-light transition-colors text-sm"
              >
                Batal
              </button>
              <button
                @click="submitUjianRequest"
                :disabled="submittingUjian"
                class="px-5 py-2.5 rounded-lg bg-primary text-white font-bold hover:bg-blue-600 transition-colors shadow-sm text-sm flex items-center gap-2 disabled:opacity-50"
              >
                <span
                  v-if="submittingUjian"
                  class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"
                ></span>
                <span v-else class="material-symbols-outlined text-[18px]"
                  >send</span
                >
                {{ submittingUjian ? "Mengirim..." : "Ajukan Ujian" }}
              </button>
            </div>
          </div>
        </div>
      </Transition>

      <!-- Revisi Pasca Sidang Section -->
      <section
        v-if="skripsi?.status === 'revisi'"
        class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden hover:shadow-md transition-all"
      >
        <div
          class="p-5 border-b border-border-light flex items-center justify-between"
        >
          <div>
            <h3 class="text-lg font-bold text-text-main">
              Revisi Pasca Sidang
            </h3>
            <p class="text-sm text-text-secondary">
              Unggah dokumen revisi sesuai catatan dosen penguji
            </p>
          </div>
          <span
            class="px-3 py-1 rounded-full text-xs font-bold bg-orange-50 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300 border border-orange-200 dark:border-orange-800"
          >
            Perlu Revisi
          </span>
        </div>
        <div class="p-5 space-y-4">
          <!-- Info alert -->
          <div
            class="flex items-start gap-3 p-4 bg-blue-50 dark:bg-blue-900/10 rounded-lg border-l-4 border-blue-400"
          >
            <span class="material-symbols-outlined text-blue-600 mt-0.5"
              >info</span
            >
            <div>
              <p class="text-sm text-text-main">
                Hasil sidang Anda adalah <strong>Lulus Bersyarat</strong>.
                Silakan perbaiki skripsi sesuai catatan dari dosen penguji, lalu
                unggah dokumen revisi di bawah ini. Setelah disetujui oleh
                staff, status Anda akan berubah menjadi <strong>Lulus</strong>.
              </p>
            </div>
          </div>

          <!-- Upload form -->
          <div class="border border-dashed border-border-light rounded-xl p-4">
            <div class="flex items-center gap-3 mb-3">
              <span class="material-symbols-outlined text-primary text-xl"
                >upload_file</span
              >
              <p class="text-sm font-bold text-text-main">
                Unggah Dokumen Revisi
              </p>
            </div>
            <div class="flex gap-3">
              <input
                type="file"
                ref="revisiFileInput"
                accept=".pdf,.doc,.docx"
                class="flex-1 px-3 py-2 border border-border-light rounded-lg bg-background-light text-text-main text-sm dark:bg-background"
              />
              <button
                @click="uploadRevisiDoc"
                :disabled="uploadingRevisi"
                class="px-5 py-2 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors text-sm font-bold disabled:opacity-50 flex items-center gap-2"
              >
                <span
                  v-if="uploadingRevisi"
                  class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"
                ></span>
                <span v-else class="material-symbols-outlined text-[18px]"
                  >cloud_upload</span
                >
                {{ uploadingRevisi ? "Mengunggah..." : "Unggah" }}
              </button>
            </div>
          </div>

          <!-- Uploaded docs list -->
          <div v-if="revisiDocsMhs.length > 0" class="space-y-2">
            <p
              class="text-xs font-bold text-text-secondary uppercase tracking-wider"
            >
              Dokumen yang Diunggah
            </p>
            <div
              v-for="doc in revisiDocsMhs"
              :key="doc.id"
              class="flex items-center justify-between p-3 border border-border-light rounded-lg"
            >
              <div class="flex items-center gap-3 min-w-0">
                <span
                  class="material-symbols-outlined text-xl"
                  :class="
                    doc.status === 'approved'
                      ? 'text-green-600'
                      : doc.status === 'rejected'
                        ? 'text-red-600'
                        : 'text-blue-600'
                  "
                  >description</span
                >
                <div class="min-w-0">
                  <p class="text-sm font-medium text-text-main truncate">
                    {{ doc.nama_file }}
                  </p>
                  <p class="text-[10px] text-text-secondary">
                    {{
                      new Date(doc.created_at).toLocaleDateString("id-ID", {
                        day: "numeric",
                        month: "long",
                        year: "numeric",
                      })
                    }}
                  </p>
                </div>
              </div>
              <span
                class="px-2.5 py-1 rounded-full text-[10px] font-bold shrink-0"
                :class="
                  doc.status === 'approved'
                    ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300'
                    : doc.status === 'rejected'
                      ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300'
                      : 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300'
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
            </div>
          </div>
        </div>
      </section>

      <!-- Dokumen Umum Skripsi Section (SK Tugas & Nota Bimbingan) -->
      <section
        v-if="skTugas || hasBimbingan"
        class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden hover:shadow-md transition-all"
      >
        <div class="p-5 border-b border-border-light">
          <h3 class="text-lg font-bold text-text-main">Dokumen Umum Skripsi</h3>
          <p class="text-sm text-text-secondary">
            Surat tugas pembimbing dan nota bimbingan
          </p>
        </div>
        <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
          <!-- SK Tugas Pembimbing -->
          <div
            v-if="skTugas"
            class="flex items-center gap-4 p-4 rounded-xl border border-border-light bg-white dark:bg-white/5"
          >
            <div
              class="size-12 rounded-xl flex items-center justify-center bg-purple-50 dark:bg-purple-900/20 text-purple-500 dark:text-purple-400 shrink-0"
            >
              <span class="material-symbols-outlined">assignment</span>
            </div>
            <div class="flex-1 min-w-0">
              <h4 class="text-sm font-bold text-text-main">
                SK Tugas Pembimbing
              </h4>
              <p class="text-xs text-text-secondary">
                Surat penugasan dosen pembimbing
              </p>
            </div>
            <div class="flex items-center gap-1.5 shrink-0">
              <button
                @click="previewPdf('sk-tugas')"
                :disabled="previewingPdf === 'sk-tugas'"
                class="p-2 text-primary bg-primary/10 rounded-lg hover:bg-primary/20 transition-colors disabled:opacity-50"
                title="Lihat"
              >
                <span class="material-symbols-outlined text-[18px]">{{
                  previewingPdf === "sk-tugas" ? "hourglass_top" : "visibility"
                }}</span>
              </button>
              <button
                @click="downloadPdf('sk-tugas')"
                :disabled="downloadingPdf === 'sk-tugas'"
                class="p-2 text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors disabled:opacity-50 shadow-sm"
                title="Unduh"
              >
                <span class="material-symbols-outlined text-[18px]">{{
                  downloadingPdf === "sk-tugas" ? "hourglass_top" : "download"
                }}</span>
              </button>
            </div>
          </div>

          <!-- Nota Bimbingan -->
          <div
            v-if="hasBimbingan"
            class="flex items-center gap-4 p-4 rounded-xl border border-border-light bg-white dark:bg-white/5"
          >
            <div
              class="size-12 rounded-xl flex items-center justify-center bg-amber-50 dark:bg-amber-900/20 text-amber-500 dark:text-amber-400 shrink-0"
            >
              <span class="material-symbols-outlined">menu_book</span>
            </div>
            <div class="flex-1 min-w-0">
              <h4 class="text-sm font-bold text-text-main">Nota Bimbingan</h4>
              <p class="text-xs text-text-secondary">
                Rekap bimbingan dengan dosen
              </p>
            </div>
            <div class="flex items-center gap-1.5 shrink-0">
              <button
                @click="previewPdf('nota-bimbingan')"
                :disabled="previewingPdf === 'nota-bimbingan'"
                class="p-2 text-primary bg-primary/10 rounded-lg hover:bg-primary/20 transition-colors disabled:opacity-50"
                title="Lihat"
              >
                <span class="material-symbols-outlined text-[18px]">{{
                  previewingPdf === "nota-bimbingan"
                    ? "hourglass_top"
                    : "visibility"
                }}</span>
              </button>
              <button
                @click="downloadPdf('nota-bimbingan')"
                :disabled="downloadingPdf === 'nota-bimbingan'"
                class="p-2 text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors disabled:opacity-50 shadow-sm"
                title="Unduh"
              >
                <span class="material-symbols-outlined text-[18px]">{{
                  downloadingPdf === "nota-bimbingan"
                    ? "hourglass_top"
                    : "download"
                }}</span>
              </button>
            </div>
          </div>
        </div>
      </section>

      <!-- Dokumen Sempro Section -->
      <section
        v-if="
          semproSeminar &&
          (semproSeminar.penguji?.length || semproSeminar.berita_acara)
        "
        class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden hover:shadow-md transition-all"
      >
        <div class="p-5 border-b border-border-light">
          <h3 class="text-lg font-bold text-text-main">
            Dokumen Seminar Proposal
          </h3>
          <p class="text-sm text-text-secondary">
            Dokumen resmi terkait seminar proposal Anda
          </p>
        </div>
        <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
          <!-- SK Penguji Sempro -->
          <div
            v-if="semproSeminar.penguji?.length"
            class="flex items-center gap-4 p-4 rounded-xl border border-border-light bg-white dark:bg-white/5"
          >
            <div
              class="size-12 rounded-xl flex items-center justify-center bg-red-50 dark:bg-red-900/20 text-red-500 dark:text-red-400 shrink-0"
            >
              <span class="material-symbols-outlined">picture_as_pdf</span>
            </div>
            <div class="flex-1 min-w-0">
              <h4 class="text-sm font-bold text-text-main">
                SK Penguji Sempro
              </h4>
              <p class="text-xs text-text-secondary">
                {{ semproSeminar.penguji.length }} penguji ditugaskan
              </p>
            </div>
            <div class="flex items-center gap-1.5 shrink-0">
              <button
                @click="previewPdf('sk-penguji-sempro')"
                :disabled="previewingPdf === 'sk-penguji-sempro'"
                class="p-2 text-primary bg-primary/10 rounded-lg hover:bg-primary/20 transition-colors disabled:opacity-50"
                title="Lihat"
              >
                <span class="material-symbols-outlined text-[18px]">{{
                  previewingPdf === "sk-penguji-sempro"
                    ? "hourglass_top"
                    : "visibility"
                }}</span>
              </button>
              <button
                @click="downloadPdf('sk-penguji-sempro')"
                :disabled="downloadingPdf === 'sk-penguji-sempro'"
                class="p-2 text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors disabled:opacity-50 shadow-sm"
                title="Unduh"
              >
                <span class="material-symbols-outlined text-[18px]">{{
                  downloadingPdf === "sk-penguji-sempro"
                    ? "hourglass_top"
                    : "download"
                }}</span>
              </button>
            </div>
          </div>

          <!-- Berita Acara Sempro -->
          <div
            v-if="semproSeminar.berita_acara"
            class="flex items-center gap-4 p-4 rounded-xl border border-border-light bg-white dark:bg-white/5"
          >
            <div
              class="size-12 rounded-xl flex items-center justify-center bg-blue-50 dark:bg-blue-900/20 text-blue-500 dark:text-blue-400 shrink-0"
            >
              <span class="material-symbols-outlined">description</span>
            </div>
            <div class="flex-1 min-w-0">
              <h4 class="text-sm font-bold text-text-main">
                Berita Acara Sempro
              </h4>
              <p class="text-xs text-text-secondary">Seminar selesai</p>
            </div>
            <div class="flex items-center gap-1.5 shrink-0">
              <button
                @click="previewPdf('berita-acara-sempro')"
                :disabled="previewingPdf === 'berita-acara-sempro'"
                class="p-2 text-primary bg-primary/10 rounded-lg hover:bg-primary/20 transition-colors disabled:opacity-50"
                title="Lihat"
              >
                <span class="material-symbols-outlined text-[18px]">{{
                  previewingPdf === "berita-acara-sempro"
                    ? "hourglass_top"
                    : "visibility"
                }}</span>
              </button>
              <button
                @click="downloadPdf('berita-acara-sempro')"
                :disabled="downloadingPdf === 'berita-acara-sempro'"
                class="p-2 text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors disabled:opacity-50 shadow-sm"
                title="Unduh"
              >
                <span class="material-symbols-outlined text-[18px]">{{
                  downloadingPdf === "berita-acara-sempro"
                    ? "hourglass_top"
                    : "download"
                }}</span>
              </button>
            </div>
          </div>
        </div>
      </section>

      <!-- Dokumen Semhas Section -->
      <section
        v-if="
          authStore.semhasEnabled &&
          semhasSeminar &&
          (semhasSeminar.penguji?.length || semhasSeminar.berita_acara)
        "
        class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden hover:shadow-md transition-all"
      >
        <div class="p-5 border-b border-border-light">
          <h3 class="text-lg font-bold text-text-main">
            Dokumen Seminar Hasil
          </h3>
          <p class="text-sm text-text-secondary">
            Dokumen resmi terkait seminar hasil Anda
          </p>
        </div>
        <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div
            v-if="semhasSeminar.penguji?.length"
            class="flex items-center gap-4 p-4 rounded-xl border border-border-light bg-white dark:bg-white/5"
          >
            <div
              class="size-12 rounded-xl flex items-center justify-center bg-orange-50 dark:bg-orange-900/20 text-orange-500 dark:text-orange-400 shrink-0"
            >
              <span class="material-symbols-outlined">picture_as_pdf</span>
            </div>
            <div class="flex-1 min-w-0">
              <h4 class="text-sm font-bold text-text-main">
                SK Penguji Semhas
              </h4>
              <p class="text-xs text-text-secondary">
                {{ semhasSeminar.penguji.length }} penguji ditugaskan
              </p>
            </div>
            <div class="flex items-center gap-1.5 shrink-0">
              <button
                @click="previewPdf('sk-penguji-semhas')"
                :disabled="previewingPdf === 'sk-penguji-semhas'"
                class="p-2 text-primary bg-primary/10 rounded-lg hover:bg-primary/20 transition-colors disabled:opacity-50"
                title="Lihat"
              >
                <span class="material-symbols-outlined text-[18px]">{{
                  previewingPdf === "sk-penguji-semhas"
                    ? "hourglass_top"
                    : "visibility"
                }}</span>
              </button>
              <button
                @click="downloadPdf('sk-penguji-semhas')"
                :disabled="downloadingPdf === 'sk-penguji-semhas'"
                class="p-2 text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors disabled:opacity-50 shadow-sm"
                title="Unduh"
              >
                <span class="material-symbols-outlined text-[18px]">{{
                  downloadingPdf === "sk-penguji-semhas"
                    ? "hourglass_top"
                    : "download"
                }}</span>
              </button>
            </div>
          </div>

          <div
            v-if="semhasSeminar.berita_acara"
            class="flex items-center gap-4 p-4 rounded-xl border border-border-light bg-white dark:bg-white/5"
          >
            <div
              class="size-12 rounded-xl flex items-center justify-center bg-teal-50 dark:bg-teal-900/20 text-teal-500 dark:text-teal-400 shrink-0"
            >
              <span class="material-symbols-outlined">description</span>
            </div>
            <div class="flex-1 min-w-0">
              <h4 class="text-sm font-bold text-text-main">
                Berita Acara Semhas
              </h4>
              <p class="text-xs text-text-secondary">Seminar selesai</p>
            </div>
            <div class="flex items-center gap-1.5 shrink-0">
              <button
                @click="previewPdf('berita-acara-semhas')"
                :disabled="previewingPdf === 'berita-acara-semhas'"
                class="p-2 text-primary bg-primary/10 rounded-lg hover:bg-primary/20 transition-colors disabled:opacity-50"
                title="Lihat"
              >
                <span class="material-symbols-outlined text-[18px]">{{
                  previewingPdf === "berita-acara-semhas"
                    ? "hourglass_top"
                    : "visibility"
                }}</span>
              </button>
              <button
                @click="downloadPdf('berita-acara-semhas')"
                :disabled="downloadingPdf === 'berita-acara-semhas'"
                class="p-2 text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors disabled:opacity-50 shadow-sm"
                title="Unduh"
              >
                <span class="material-symbols-outlined text-[18px]">{{
                  downloadingPdf === "berita-acara-semhas"
                    ? "hourglass_top"
                    : "download"
                }}</span>
              </button>
            </div>
          </div>
        </div>
      </section>

      <!-- Dokumen Sidang Skripsi Section -->
      <section
        v-if="
          sidangSeminar &&
          (sidangSeminar.penguji?.length || sidangSeminar.berita_acara)
        "
        class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden hover:shadow-md transition-all"
      >
        <div class="p-5 border-b border-border-light">
          <h3 class="text-lg font-bold text-text-main">
            Dokumen Sidang Skripsi
          </h3>
          <p class="text-sm text-text-secondary">
            Dokumen resmi terkait sidang skripsi Anda
          </p>
        </div>
        <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div
            v-if="sidangSeminar.penguji?.length"
            class="flex items-center gap-4 p-4 rounded-xl border border-border-light bg-white dark:bg-white/5"
          >
            <div
              class="size-12 rounded-xl flex items-center justify-center bg-indigo-50 dark:bg-indigo-900/20 text-indigo-500 dark:text-indigo-400 shrink-0"
            >
              <span class="material-symbols-outlined">picture_as_pdf</span>
            </div>
            <div class="flex-1 min-w-0">
              <h4 class="text-sm font-bold text-text-main">
                SK Penguji Sidang
              </h4>
              <p class="text-xs text-text-secondary">
                {{ sidangSeminar.penguji.length }} penguji ditugaskan
              </p>
            </div>
            <div class="flex items-center gap-1.5 shrink-0">
              <button
                @click="previewPdf('sk-penguji-sidang')"
                :disabled="previewingPdf === 'sk-penguji-sidang'"
                class="p-2 text-primary bg-primary/10 rounded-lg hover:bg-primary/20 transition-colors disabled:opacity-50"
                title="Lihat"
              >
                <span class="material-symbols-outlined text-[18px]">{{
                  previewingPdf === "sk-penguji-sidang"
                    ? "hourglass_top"
                    : "visibility"
                }}</span>
              </button>
              <button
                @click="downloadPdf('sk-penguji-sidang')"
                :disabled="downloadingPdf === 'sk-penguji-sidang'"
                class="p-2 text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors disabled:opacity-50 shadow-sm"
                title="Unduh"
              >
                <span class="material-symbols-outlined text-[18px]">{{
                  downloadingPdf === "sk-penguji-sidang"
                    ? "hourglass_top"
                    : "download"
                }}</span>
              </button>
            </div>
          </div>

          <div
            v-if="sidangSeminar.berita_acara"
            class="flex items-center gap-4 p-4 rounded-xl border border-border-light bg-white dark:bg-white/5"
          >
            <div
              class="size-12 rounded-xl flex items-center justify-center bg-rose-50 dark:bg-rose-900/20 text-rose-500 dark:text-rose-400 shrink-0"
            >
              <span class="material-symbols-outlined">description</span>
            </div>
            <div class="flex-1 min-w-0">
              <h4 class="text-sm font-bold text-text-main">
                Berita Acara Sidang
              </h4>
              <p class="text-xs text-text-secondary">Sidang selesai</p>
            </div>
            <div class="flex items-center gap-1.5 shrink-0">
              <button
                @click="previewPdf('berita-acara-sidang')"
                :disabled="previewingPdf === 'berita-acara-sidang'"
                class="p-2 text-primary bg-primary/10 rounded-lg hover:bg-primary/20 transition-colors disabled:opacity-50"
                title="Lihat"
              >
                <span class="material-symbols-outlined text-[18px]">{{
                  previewingPdf === "berita-acara-sidang"
                    ? "hourglass_top"
                    : "visibility"
                }}</span>
              </button>
              <button
                @click="downloadPdf('berita-acara-sidang')"
                :disabled="downloadingPdf === 'berita-acara-sidang'"
                class="p-2 text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors disabled:opacity-50 shadow-sm"
                title="Unduh"
              >
                <span class="material-symbols-outlined text-[18px]">{{
                  downloadingPdf === "berita-acara-sidang"
                    ? "hourglass_top"
                    : "download"
                }}</span>
              </button>
            </div>
          </div>
        </div>
      </section>

      <!-- SK Yudisium Section -->
      <section
        v-if="skYudisium"
        class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden hover:shadow-md transition-all"
      >
        <div class="p-5 border-b border-border-light">
          <h3 class="text-lg font-bold text-text-main">SK Yudisium</h3>
          <p class="text-sm text-text-secondary">
            Surat keputusan kelulusan Anda
          </p>
        </div>
        <div class="p-5">
          <div
            class="flex items-center gap-4 p-4 rounded-xl border border-border-light bg-white dark:bg-white/5"
          >
            <div
              class="size-12 rounded-xl flex items-center justify-center bg-emerald-50 dark:bg-emerald-900/20 text-emerald-500 dark:text-emerald-400 shrink-0"
            >
              <span class="material-symbols-outlined">school</span>
            </div>
            <div class="flex-1 min-w-0">
              <h4 class="text-sm font-bold text-text-main">SK Yudisium</h4>
              <p class="text-xs text-text-secondary">Dinyatakan lulus</p>
            </div>
            <div class="flex items-center gap-1.5 shrink-0">
              <button
                @click="previewPdf('sk-yudisium')"
                :disabled="previewingPdf === 'sk-yudisium'"
                class="p-2 text-primary bg-primary/10 rounded-lg hover:bg-primary/20 transition-colors disabled:opacity-50"
                title="Lihat"
              >
                <span class="material-symbols-outlined text-[18px]">{{
                  previewingPdf === "sk-yudisium"
                    ? "hourglass_top"
                    : "visibility"
                }}</span>
              </button>
              <button
                @click="downloadPdf('sk-yudisium')"
                :disabled="downloadingPdf === 'sk-yudisium'"
                class="p-2 text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors disabled:opacity-50 shadow-sm"
                title="Unduh"
              >
                <span class="material-symbols-outlined text-[18px]">{{
                  downloadingPdf === "sk-yudisium"
                    ? "hourglass_top"
                    : "download"
                }}</span>
              </button>
            </div>
          </div>
        </div>
      </section>
    </template>

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
  </div>
</template>

<script setup>
import { ref, computed, onMounted, inject } from "vue";
import { mahasiswaService } from "../../services/mahasiswaService";
import { useAuthStore } from "../../stores/auth";

const authStore = useAuthStore();
const openChatWithAdmin = inject("openChatWithAdmin", () => {});
const loading = ref(true);
const mahasiswa = ref(null);
const skripsi = ref(null);
const stats = ref({});
const upcomingSeminar = ref(null);
const upcomingUjian = ref(null);
const semproSeminar = ref(null);
const semhasSeminar = ref(null);
const sidangSeminar = ref(null);
const skYudisium = ref(null);
const skTugas = ref(null);
const hasBimbingan = ref(false);
const downloadingPdf = ref(null);
const previewingPdf = ref(null);

// Ujian request state
const ujianEligibility = ref(null);
const showUjianConfirmModal = ref(false);
const submittingUjian = ref(false);
const ujianToast = ref({ show: false, message: "", type: "success" });

// Revisi state
const revisiDocsMhs = ref([]);
const uploadingRevisi = ref(false);
const revisiFileInput = ref(null);

// Revisi Proposal (Sempro lulus bersyarat) state
const revisiProposalDocs = ref([]);
const uploadingRevisiProposal = ref(false);
const revisiProposalFileInput = ref(null);
const rejectedSkripsi = ref(null);

// Proposal upload state (for disetujui status)
const proposalDocs = ref([]);
const uploadingProposal = ref(false);
const proposalFileInput = ref(null);

const eligibilityChecklist = computed(() => {
  if (!ujianEligibility.value) return [];
  const e = ujianEligibility.value;
  const items = [
    {
      label: `Bimbingan Pembimbing 1`,
      detail: `${e.bimbingan?.pembimbing_1?.count ?? 0} / ${e.bimbingan?.pembimbing_1?.required ?? 8} sesi (disetujui)`,
      met: e.bimbingan?.pembimbing_1?.met ?? false,
    },
  ];
  // Only show pembimbing 2 requirement if student has 2 advisors
  if (e.has_pembimbing_2) {
    items.push({
      label: `Bimbingan Pembimbing 2`,
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

const checkUjianEligibility = async () => {
  try {
    const res = await mahasiswaService.checkUjianEligibility();
    if (res.success) {
      ujianEligibility.value = res.data;
    }
  } catch (err) {
    console.error("Failed to check ujian eligibility:", err);
  }
};

const submitUjianRequest = async () => {
  submittingUjian.value = true;
  try {
    const res = await mahasiswaService.requestUjian();
    if (res.success) {
      showUjianConfirmModal.value = false;
      // Update skripsi status locally
      if (skripsi.value) {
        skripsi.value.status = "pengajuan_sidang";
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

// ---- REVISI DOCS (MAHASISWA) ----
const fetchRevisiDocs = async () => {
  if (!skripsi.value) return;
  try {
    const response = await mahasiswaService.getDokumen({ jenis: "revisi" });
    if (response.success) {
      revisiDocsMhs.value = response.data || [];
    }
  } catch (error) {
    console.error("Failed to fetch revisi docs:", error);
  }
};

const uploadRevisiDoc = async () => {
  const fileInput = revisiFileInput.value;
  if (!fileInput?.files?.length) {
    alert("Pilih file terlebih dahulu");
    return;
  }
  try {
    uploadingRevisi.value = true;
    await mahasiswaService.uploadDokumen({
      jenis: "revisi",
      file: fileInput.files[0],
    });
    fileInput.value = "";
    await fetchRevisiDocs();
    alert("Dokumen revisi berhasil diunggah. Menunggu persetujuan staff.");
  } catch (error) {
    console.error("Failed to upload revisi:", error);
    alert(
      "Gagal mengunggah: " + (error.response?.data?.message || error.message),
    );
  } finally {
    uploadingRevisi.value = false;
  }
};

// ---- REVISI PROPOSAL DOCS (SEMPRO LULUS BERSYARAT) ----
const isLulusBersyaratSempro = computed(() => {
  if (!skripsi.value || skripsi.value.status !== "sempro") return false;
  // Check if sempro seminar has hasil lulus_bersyarat
  if (semproSeminar.value?.berita_acara?.hasil === "lulus_bersyarat")
    return true;
  if (semproSeminar.value?.hasil === "lulus_bersyarat") return true;
  return false;
});

const fetchRevisiProposalDocs = async () => {
  if (!skripsi.value) return;
  try {
    const response = await mahasiswaService.getDokumen({
      jenis: "revisi_proposal",
    });
    if (response.success) {
      revisiProposalDocs.value = response.data || [];
    }
  } catch (error) {
    console.error("Failed to fetch revisi proposal docs:", error);
  }
};

const uploadRevisiProposal = async () => {
  const fileInput = revisiProposalFileInput.value;
  if (!fileInput?.files?.length) {
    alert("Pilih file terlebih dahulu");
    return;
  }
  try {
    uploadingRevisiProposal.value = true;
    await mahasiswaService.uploadDokumen({
      jenis: "revisi_proposal",
      file: fileInput.files[0],
    });
    fileInput.value = "";
    await fetchRevisiProposalDocs();
    alert(
      "Dokumen revisi proposal berhasil diunggah. Menunggu persetujuan staff/admin.",
    );
  } catch (error) {
    console.error("Failed to upload revisi proposal:", error);
    alert(
      "Gagal mengunggah: " + (error.response?.data?.message || error.message),
    );
  } finally {
    uploadingRevisiProposal.value = false;
  }
};

// ---- PROPOSAL DOCS (DISETUJUI STATUS) ----
const fetchProposalDocs = async () => {
  if (!skripsi.value) return;
  try {
    const response = await mahasiswaService.getDokumen({ jenis: "proposal" });
    if (response.success) {
      proposalDocs.value = response.data || [];
    }
  } catch (error) {
    console.error("Failed to fetch proposal docs:", error);
  }
};

const uploadProposal = async () => {
  const fileInput = proposalFileInput.value;
  if (!fileInput?.files?.length) {
    alert("Pilih file terlebih dahulu");
    return;
  }
  try {
    uploadingProposal.value = true;
    await mahasiswaService.uploadDokumen({
      jenis: "proposal",
      file: fileInput.files[0],
    });
    fileInput.value = "";
    await fetchProposalDocs();
    alert(
      "Dokumen proposal berhasil diunggah. Menunggu persetujuan staff/admin.",
    );
  } catch (error) {
    console.error("Failed to upload proposal:", error);
    alert(
      "Gagal mengunggah: " + (error.response?.data?.message || error.message),
    );
  } finally {
    uploadingProposal.value = false;
  }
};

// Status mapping
const statusMap = {
  draft: { label: "Draft", icon: "edit_note", color: "gray" },
  pengajuan: { label: "Pengajuan", icon: "hourglass_top", color: "yellow" },
  disetujui: { label: "Disetujui", icon: "check_circle", color: "green" },
  ditolak: { label: "Ditolak", icon: "cancel", color: "red" },
  proposal: { label: "Tahap Proposal", icon: "description", color: "blue" },
  sempro: { label: "Sudah Sempro", icon: "check_circle", color: "green" },
  penentuan_dospem: {
    label: "Penentuan Dospem",
    icon: "supervisor_account",
    color: "blue",
  },
  dospem: {
    label: "Dospem Ditentukan",
    icon: "supervisor_account",
    color: "indigo",
  },
  bimbingan: {
    label: "Proses Bimbingan",
    icon: "auto_stories",
    color: "blue",
  },
  pengajuan_sidang: {
    label: "Pengajuan Sidang",
    icon: "pending_actions",
    color: "yellow",
  },
  pengajuan_sidang_acc: {
    label: "Sidang Disetujui",
    icon: "check_circle",
    color: "green",
  },
  pengajuan_sidang_tolak: {
    label: "Sidang Ditolak",
    icon: "cancel",
    color: "red",
  },
  semhas: { label: "Seminar Hasil", icon: "record_voice_over", color: "blue" },
  sidang: { label: "Sidang", icon: "school", color: "blue" },
  revisi: { label: "Revisi", icon: "edit", color: "orange" },
  lulus: { label: "Lulus", icon: "celebration", color: "green" },
};

const statusLabel = computed(() => {
  if (!skripsi.value) return "";
  return statusMap[skripsi.value.status]?.label || skripsi.value.status;
});

const statusIcon = computed(() => {
  if (!skripsi.value) return "info";
  return statusMap[skripsi.value.status]?.icon || "info";
});

const statusBadgeClass = computed(() => {
  if (!skripsi.value) return "";
  const color = statusMap[skripsi.value.status]?.color || "gray";
  const classes = {
    green:
      "bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-800 text-green-700 dark:text-green-400",
    blue: "bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 text-blue-700 dark:text-blue-400",
    indigo:
      "bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800 text-indigo-700 dark:text-indigo-400",
    yellow:
      "bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-100 dark:border-yellow-800 text-yellow-700 dark:text-yellow-400",
    red: "bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800 text-red-700 dark:text-red-400",
    orange:
      "bg-orange-50 dark:bg-orange-900/20 border border-orange-100 dark:border-orange-800 text-orange-700 dark:text-orange-400",
    gray: "bg-gray-50 dark:bg-gray-900/20 border border-gray-100 dark:border-gray-800 text-gray-700 dark:text-gray-400",
  };
  return classes[color] || classes.gray;
});

const statusIconClass = computed(() => {
  if (!skripsi.value) return "";
  const color = statusMap[skripsi.value.status]?.color || "gray";
  const classes = {
    green: "text-green-600 dark:text-green-400",
    blue: "text-blue-600 dark:text-blue-400",
    indigo: "text-indigo-600 dark:text-indigo-400",
    yellow: "text-yellow-600 dark:text-yellow-400",
    red: "text-red-600 dark:text-red-400",
    orange: "text-orange-600 dark:text-orange-400",
    gray: "text-gray-600 dark:text-gray-400",
  };
  return classes[color] || classes.gray;
});

const progress = computed(() => stats.value.progress ?? 0);

// Pembimbing names
const pembimbingNames = computed(() => {
  if (!skripsi.value?.pembimbing?.length) return "";
  return skripsi.value.pembimbing
    .map((p) => p.dosen?.nama || "Unknown")
    .join(", ");
});

// Last updated relative time
const lastUpdated = computed(() => {
  if (!skripsi.value?.updated_at) return "-";
  const now = new Date();
  const updated = new Date(skripsi.value.updated_at);
  const diffMs = now - updated;
  const diffMinutes = Math.floor(diffMs / 60000);
  const diffHours = Math.floor(diffMinutes / 60);
  const diffDays = Math.floor(diffHours / 24);

  if (diffMinutes < 1) return "Baru saja";
  if (diffMinutes < 60) return `${diffMinutes} menit yang lalu`;
  if (diffHours < 24) return `${diffHours} jam yang lalu`;
  if (diffDays < 30) return `${diffDays} hari yang lalu`;
  return formatDate(skripsi.value.updated_at);
});

// Milestones for progress bar
const milestoneSteps = computed(() => {
  const allSteps = [
    {
      key: "proposal",
      label: "Proposal",
      statuses: ["proposal", "pengajuan", "disetujui"],
    },
    { key: "sempro", label: "Sempro", statuses: ["sempro"] },
    {
      key: "dospem",
      label: "Dospem",
      statuses: ["penentuan_dospem", "dospem"],
    },
    {
      key: "bimbingan",
      label: "Bimbingan",
      statuses: [
        "bimbingan",
        "pengajuan_sidang",
        "pengajuan_sidang_acc",
        "pengajuan_sidang_tolak",
      ],
    },
    { key: "semhas", label: "Semhas", statuses: ["semhas"] },
    { key: "sidang", label: "Sidang", statuses: ["sidang", "revisi", "lulus"] },
  ];
  if (!authStore.semhasEnabled) {
    return allSteps.filter((s) => s.key !== "semhas");
  }
  return allSteps;
});

const statusOrder = [
  "draft",
  "pengajuan",
  "disetujui",
  "proposal",
  "sempro",
  "penentuan_dospem",
  "dospem",
  "bimbingan",
  "pengajuan_sidang",
  "pengajuan_sidang_tolak",
  "pengajuan_sidang_acc",
  "semhas",
  "sidang",
  "revisi",
  "lulus",
];

const milestones = computed(() => {
  if (!skripsi.value) return milestoneSteps.value;
  const currentIdx = statusOrder.indexOf(skripsi.value.status);
  return milestoneSteps.value.map((m) => {
    const mStatuses = m.statuses;
    const mMaxIdx = Math.max(...mStatuses.map((s) => statusOrder.indexOf(s)));
    const mMinIdx = Math.min(...mStatuses.map((s) => statusOrder.indexOf(s)));
    return {
      ...m,
      isCompleted: currentIdx > mMaxIdx,
      isCurrent: currentIdx >= mMinIdx && currentIdx <= mMaxIdx,
    };
  });
});

// Active stage description
const activeStageDescription = computed(() => {
  if (!skripsi.value) return "";
  const descriptions = {
    draft:
      "Skripsi Anda masih dalam status draft. Silakan lengkapi dan ajukan judul skripsi Anda.",
    pengajuan:
      "Judul skripsi Anda sedang dalam proses review oleh admin. Mohon tunggu konfirmasi persetujuan.",
    disetujui:
      "Selamat! Judul skripsi Anda telah disetujui. Langkah selanjutnya adalah mengupload dokumen proposal Anda untuk diperiksa oleh staff/admin.",
    proposal:
      "Anda sedang dalam tahap proposal. Persiapkan materi proposal Anda untuk seminar proposal.",
    sempro: isLulusBersyaratSempro.value
      ? "Hasil seminar proposal Anda adalah Lulus Bersyarat. Silakan upload revisi proposal sesuai catatan dosen penguji. Setelah disetujui, status Anda akan dilanjutkan ke tahap Penentuan Dospem."
      : "Selamat! Anda telah menyelesaikan Seminar Proposal. Menunggu proses selanjutnya.",
    penentuan_dospem:
      "Pembimbing skripsi Anda sedang ditentukan oleh admin. Mohon tunggu hingga SK Tugas Pembimbing diterbitkan.",
    dospem:
      "Dosen pembimbing Anda telah ditentukan. Menunggu SK Tugas Pembimbing disetujui oleh admin sebelum tahap bimbingan dimulai.",
    bimbingan:
      "Anda sedang dalam tahap bimbingan. Lakukan bimbingan secara rutin dengan dosen pembimbing untuk menyelesaikan skripsi.",
    pengajuan_sidang:
      "Pengajuan ujian skripsi Anda sedang menunggu persetujuan dosen pembimbing utama. Mohon tunggu konfirmasi.",
    pengajuan_sidang_acc:
      "Pengajuan ujian skripsi Anda telah disetujui oleh dosen pembimbing. Menunggu admin menjadwalkan ujian sidang.",
    pengajuan_sidang_tolak:
      "Pengajuan ujian skripsi Anda ditolak oleh dosen pembimbing. Silakan perbaiki sesuai catatan dan ajukan kembali.",
    semhas:
      "Anda sudah memasuki tahap Seminar Hasil. Persiapkan presentasi dan dokumen yang diperlukan.",
    sidang:
      "Anda sudah memasuki tahap Sidang Skripsi. Persiapkan diri dengan baik untuk ujian sidang.",
    revisi:
      "Anda sedang dalam tahap revisi pasca sidang. Selesaikan revisi sesuai catatan dari dosen penguji.",
    lulus:
      "Selamat! Anda telah dinyatakan lulus. Pastikan semua dokumen akhir sudah diselesaikan.",
    ditolak:
      "Maaf, judul skripsi Anda ditolak. Silakan perbaiki dan ajukan kembali judul baru.",
  };
  return (
    descriptions[skripsi.value.status] ||
    "Pantau terus progres skripsi Anda di halaman ini."
  );
});

const formatDate = (dateStr) => {
  if (!dateStr) return "-";
  return new Date(dateStr).toLocaleDateString("id-ID", {
    day: "numeric",
    month: "long",
    year: "numeric",
  });
};

const pdfFileNames = {
  "sk-tugas": "SK_Tugas_Pembimbing.pdf",
  "nota-bimbingan": "Nota_Bimbingan.pdf",
  "sk-penguji-sempro": "SK_Penguji_Sempro.pdf",
  "berita-acara-sempro": "Berita_Acara_Sempro.pdf",
  "sk-penguji-semhas": "SK_Penguji_Semhas.pdf",
  "berita-acara-semhas": "Berita_Acara_Semhas.pdf",
  "sk-penguji-sidang": "SK_Penguji_Sidang.pdf",
  "berita-acara-sidang": "Berita_Acara_Sidang.pdf",
  "sk-yudisium": "SK_Yudisium.pdf",
};

const downloadPdf = async (type) => {
  downloadingPdf.value = type;
  try {
    const response = await mahasiswaService.downloadOfficialPdf(type);
    const blob = new Blob([response.data], { type: "application/pdf" });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = pdfFileNames[type] || `${type}.pdf`;
    a.click();
    window.URL.revokeObjectURL(url);
  } catch (err) {
    console.error("Failed to download PDF:", err);
    alert(
      "Gagal mengunduh dokumen: " +
        (err.response?.data?.message || err.message),
    );
  } finally {
    downloadingPdf.value = null;
  }
};

const previewPdf = async (type) => {
  previewingPdf.value = type;
  try {
    const response = await mahasiswaService.downloadOfficialPdf(type);
    const blob = new Blob([response.data], { type: "application/pdf" });
    const url = window.URL.createObjectURL(blob);
    window.open(url, "_blank");
  } catch (err) {
    console.error("Failed to preview PDF:", err);
    alert(
      "Gagal menampilkan dokumen: " +
        (err.response?.data?.message || err.message),
    );
  } finally {
    previewingPdf.value = null;
  }
};

onMounted(async () => {
  try {
    const res = await mahasiswaService.getDashboard();
    if (res.success) {
      mahasiswa.value = res.data.mahasiswa;
      skripsi.value = res.data.skripsi;
      stats.value = res.data.stats || {};
      upcomingSeminar.value = res.data.upcoming_seminar;
      upcomingUjian.value = res.data.upcoming_ujian;
      semproSeminar.value = res.data.sempro_seminar;
      semhasSeminar.value = res.data.semhas_seminar;
      sidangSeminar.value = res.data.sidang_seminar;
      skYudisium.value = res.data.sk_yudisium;
      skTugas.value = res.data.sk_tugas;
      hasBimbingan.value = res.data.has_bimbingan ?? false;

      // Check ujian eligibility if in bimbingan status
      if (res.data.skripsi?.status === "bimbingan") {
        checkUjianEligibility();
      }

      // Fetch revisi docs if in revisi status
      if (res.data.skripsi?.status === "revisi") {
        fetchRevisiDocs();
      }

      // Fetch revisi proposal docs if sempro lulus bersyarat
      if (res.data.skripsi?.status === "sempro") {
        fetchRevisiProposalDocs();
      }

      // Fetch proposal docs if status disetujui
      if (res.data.skripsi?.status === "disetujui") {
        fetchProposalDocs();
      }
    } else {
      // No active skripsi — check for rejected (ditolak, is_active=false) skripsi
      try {
        const skripsiRes = await mahasiswaService.getSkripsiList();
        if (skripsiRes.success && skripsiRes.data) {
          const rejected = skripsiRes.data.find(
            (s) => s.status === "ditolak" && !s.is_active,
          );
          if (rejected) {
            rejectedSkripsi.value = rejected;
          }
        }
      } catch (rejErr) {
        console.error("Failed to check rejected skripsi:", rejErr);
      }
    }
  } catch (err) {
    console.error("Failed to load dashboard:", err);
  } finally {
    loading.value = false;
  }
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
.toast-slide-enter-active {
  transition: all 0.3s ease-out;
}
.toast-slide-leave-active {
  transition: all 0.2s ease-in;
}
.toast-slide-enter-from,
.toast-slide-leave-to {
  transform: translateY(20px);
  opacity: 0;
}
</style>
