<template>
  <div class="max-w-7xl mx-auto flex flex-col gap-6 animate-fade-in-up">
    <!-- Loading -->
    <div v-if="loading" class="p-12 text-center">
      <div
        class="animate-spin rounded-full h-10 w-10 border-b-2 border-primary mx-auto"
      ></div>
      <p class="text-text-secondary text-sm mt-3">Memuat data seminar...</p>
    </div>

    <template v-else-if="seminar">
      <!-- Breadcrumbs -->
      <div class="flex flex-wrap items-center gap-2 text-sm">
        <router-link
          to="/admin/seminar"
          class="text-text-secondary hover:text-primary font-medium"
        >
          Seminar Proposal
        </router-link>
        <span class="material-symbols-outlined text-text-secondary text-[18px]"
          >chevron_right</span
        >
        <span class="text-text-main font-bold">Detail Seminar</span>
      </div>

      <!-- Header -->
      <div
        class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4"
      >
        <div class="flex items-center gap-4">
          <div
            class="size-14 rounded-full flex items-center justify-center text-lg font-bold shrink-0"
            :class="getAvatarColor(seminar.skripsi?.mahasiswa?.nama)"
          >
            {{ getInitials(seminar.skripsi?.mahasiswa?.nama) }}
          </div>
          <div>
            <h1 class="text-2xl font-bold text-text-main">
              {{ seminar.skripsi?.mahasiswa?.nama || "-" }}
            </h1>
            <p class="text-text-secondary text-sm">
              {{ seminar.skripsi?.mahasiswa?.nim || "-" }} •
              {{ seminar.skripsi?.mahasiswa?.prodi?.nama || "-" }}
            </p>
          </div>
        </div>
        <div class="flex items-center gap-3">
          <span
            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold"
            :class="getSeminarStatusClass(seminar.status)"
          >
            <span
              class="w-2 h-2 rounded-full"
              :class="getSeminarStatusDot(seminar.status)"
            ></span>
            {{ getSeminarStatusLabel(seminar.status) }}
          </span>
          <button
            @click="deleteSeminar"
            class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-red-600 bg-red-50 dark:bg-red-900/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 border border-red-200 dark:border-red-800 transition-colors"
          >
            <span class="material-symbols-outlined text-[16px]">delete</span>
            Hapus
          </button>
        </div>
      </div>

      <!-- Info Cards Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Jadwal Seminar -->
        <div
          class="bg-surface-light border border-border-light rounded-xl p-5 shadow-sm"
        >
          <h3
            class="text-xs font-bold text-text-secondary uppercase tracking-wide mb-4"
          >
            Jadwal Seminar
          </h3>
          <div class="space-y-3">
            <div class="flex items-start gap-3">
              <span
                class="material-symbols-outlined text-primary text-[20px] mt-0.5"
                >calendar_today</span
              >
              <div>
                <p class="text-sm font-medium text-text-main">
                  {{ formatDate(seminar.tanggal) }}
                </p>
                <p class="text-xs text-text-secondary">Tanggal Seminar</p>
              </div>
            </div>
            <div class="flex items-start gap-3">
              <span
                class="material-symbols-outlined text-primary text-[20px] mt-0.5"
                >schedule</span
              >
              <div>
                <p class="text-sm font-medium text-text-main">
                  {{ seminar.waktu || "-" }}
                </p>
                <p class="text-xs text-text-secondary">Waktu</p>
              </div>
            </div>
            <div class="flex items-start gap-3">
              <span
                class="material-symbols-outlined text-primary text-[20px] mt-0.5"
                >location_on</span
              >
              <div>
                <p class="text-sm font-medium text-text-main">
                  {{ seminar.ruangan || "-" }}
                </p>
                <p class="text-xs text-text-secondary">Ruangan</p>
              </div>
            </div>
          </div>
          <!-- Edit Jadwal Button -->
          <button
            v-if="seminar.status === 'terjadwal'"
            @click="openEditJadwalModal"
            class="mt-4 w-full px-4 py-2 text-sm font-medium text-primary bg-primary/10 rounded-lg hover:bg-primary/20 transition-colors flex items-center justify-center gap-2"
          >
            <span class="material-symbols-outlined text-[18px]">edit</span>
            Edit Jadwal
          </button>
        </div>

        <!-- Judul Skripsi & Pembimbing -->
        <div
          class="bg-surface-light border border-border-light rounded-xl p-5 shadow-sm"
        >
          <h3
            class="text-xs font-bold text-text-secondary uppercase tracking-wide mb-4"
          >
            Informasi Skripsi
          </h3>
          <div class="space-y-4">
            <div>
              <p class="text-xs text-text-secondary mb-1">Judul Skripsi</p>
              <p class="text-sm font-medium text-text-main leading-relaxed">
                {{ seminar.skripsi?.judul || "-" }}
              </p>
            </div>
            <div>
              <p class="text-xs text-text-secondary mb-2">Dosen Pembimbing</p>
              <div
                v-if="
                  seminar.skripsi?.pembimbing &&
                  seminar.skripsi.pembimbing.length > 0
                "
                class="space-y-2"
              >
                <div
                  v-for="p in seminar.skripsi.pembimbing"
                  :key="p.id"
                  class="flex items-center gap-3 p-2 bg-gray-50 dark:bg-white/5 rounded-lg"
                >
                  <div
                    class="size-8 rounded-full flex items-center justify-center text-xs font-bold"
                    :class="getAvatarColor(p.dosen?.nama)"
                  >
                    {{ getInitials(p.dosen?.nama) }}
                  </div>
                  <div>
                    <p class="text-sm font-medium text-text-main">
                      {{ p.dosen?.nama_lengkap || p.dosen?.nama || "-" }}
                    </p>
                    <p class="text-xs text-text-secondary capitalize">
                      {{ formatPembimbingJenis(p.jenis) }}
                    </p>
                  </div>
                </div>
              </div>
              <p v-else class="text-sm text-text-secondary italic">
                Belum ada pembimbing
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Dokumen Proposal Section -->
      <div
        class="bg-surface-light border border-border-light rounded-xl shadow-sm"
      >
        <div
          class="p-5 border-b border-border-light flex items-center justify-between"
        >
          <div>
            <h3 class="text-lg font-bold text-text-main">Dokumen Proposal</h3>
            <p class="text-sm text-text-secondary">
              Upload atau download dokumen proposal skripsi
            </p>
          </div>
        </div>
        <div class="p-5">
          <!-- File exists -->
          <div
            v-if="proposalUrl"
            class="flex items-center justify-between p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl"
          >
            <div class="flex items-center gap-4">
              <div
                class="size-12 rounded-xl flex items-center justify-center bg-green-100 dark:bg-green-900/30 text-green-600"
              >
                <span class="material-symbols-outlined">description</span>
              </div>
              <div>
                <h4
                  class="text-sm font-bold text-green-800 dark:text-green-400"
                >
                  Dokumen Proposal
                </h4>
                <p class="text-xs text-green-600 dark:text-green-500">
                  File sudah diupload
                </p>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <a
                :href="proposalUrl"
                target="_blank"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-green-700 bg-green-100 rounded-lg hover:bg-green-200 transition-colors"
              >
                <span class="material-symbols-outlined text-[16px]"
                  >visibility</span
                >
                Lihat
              </a>
              <a
                :href="proposalUrl"
                download
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors shadow-sm"
              >
                <span class="material-symbols-outlined text-[16px]"
                  >download</span
                >
                Download
              </a>
              <button
                @click="triggerProposalUpload"
                :disabled="uploadingProposal"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200 transition-colors disabled:opacity-50"
              >
                <span class="material-symbols-outlined text-[16px]"
                  >upload</span
                >
                Upload Ulang
              </button>
            </div>
          </div>
          <!-- No file -->
          <div v-else>
            <div
              @click="triggerProposalUpload"
              class="border-2 border-dashed border-border-light rounded-xl p-8 text-center cursor-pointer hover:border-primary/50 hover:bg-blue-50/30 transition-all"
              :class="{ 'pointer-events-none opacity-60': uploadingProposal }"
            >
              <span
                v-if="!uploadingProposal"
                class="material-symbols-outlined text-4xl text-text-secondary/50 block mb-2"
                >cloud_upload</span
              >
              <div
                v-else
                class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto mb-2"
              ></div>
              <p class="text-sm font-medium text-text-secondary">
                {{
                  uploadingProposal
                    ? "Mengupload..."
                    : "Klik untuk upload dokumen proposal"
                }}
              </p>
              <p
                v-if="!uploadingProposal"
                class="text-xs text-text-secondary/70 mt-0.5"
              >
                PDF, DOC, DOCX (max 10MB)
              </p>
            </div>
          </div>
          <input
            ref="proposalFileInput"
            type="file"
            accept=".pdf,.doc,.docx"
            class="hidden"
            @change="uploadProposalFile"
          />
        </div>
      </div>

      <!-- Dosen Penguji Section -->
      <div
        class="bg-surface-light border border-border-light rounded-xl shadow-sm"
      >
        <div
          class="p-5 border-b border-border-light flex items-center justify-between"
        >
          <div>
            <h3 class="text-lg font-bold text-text-main">Dosen Penguji</h3>
            <p class="text-sm text-text-secondary">
              Daftar dosen penguji seminar proposal
            </p>
          </div>
          <button
            v-if="seminar.status === 'terjadwal' || !isLocked"
            @click="openAddPengujiModal"
            class="flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-blue-600 transition-colors shadow-sm"
          >
            <span class="material-symbols-outlined text-[18px]"
              >person_add</span
            >
            Tambah Penguji
          </button>
        </div>
        <div class="p-5">
          <div
            v-if="seminar.penguji && seminar.penguji.length > 0"
            class="space-y-3"
          >
            <div
              v-for="penguji in seminar.penguji"
              :key="penguji.id"
              class="p-4 bg-gray-50 dark:bg-white/5 rounded-xl border border-border-light"
            >
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                  <div
                    class="size-10 rounded-full flex items-center justify-center text-xs font-bold"
                    :class="getAvatarColor(penguji.dosen?.nama)"
                  >
                    {{ getInitials(penguji.dosen?.nama) }}
                  </div>
                  <div>
                    <p class="font-bold text-text-main text-sm">
                      {{
                        penguji.dosen?.nama_lengkap ||
                        penguji.dosen?.nama ||
                        "-"
                      }}
                    </p>
                    <div class="flex items-center gap-2 mt-0.5">
                      <span
                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                        :class="getPeranClass(penguji.peran)"
                      >
                        {{ getPeranLabel(penguji.peran) }}
                      </span>
                      <span
                        v-if="
                          penguji.nilai_mt !== null &&
                          penguji.nilai_mt !== undefined
                        "
                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400"
                      >
                        <span class="material-symbols-outlined text-[12px]"
                          >check_circle</span
                        >
                        Sudah dinilai dosen
                      </span>
                    </div>
                  </div>
                </div>
                <div class="flex items-center gap-3">
                  <div
                    v-if="penguji.nilai !== null && penguji.nilai !== undefined"
                    class="text-right"
                  >
                    <p class="text-lg font-bold text-primary">
                      {{ penguji.nilai }}
                    </p>
                    <p class="text-xs text-text-secondary">Rata-rata</p>
                  </div>
                  <span
                    v-else
                    class="text-xs text-text-secondary italic bg-gray-100 dark:bg-white/10 px-2 py-1 rounded"
                  >
                    Belum dinilai
                  </span>
                  <button
                    v-if="seminar.status === 'terjadwal' || !isLocked"
                    @click="removePenguji(penguji)"
                    class="p-1.5 text-text-secondary hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                    title="Hapus Penguji"
                  >
                    <span class="material-symbols-outlined text-[18px]"
                      >close</span
                    >
                  </button>
                </div>
              </div>
              <!-- 4-component scores display -->
              <div
                v-if="
                  penguji.nilai_mt !== null ||
                  penguji.nilai_ms !== null ||
                  penguji.nilai_pm !== null ||
                  penguji.nilai_pi !== null
                "
                class="mt-3 pt-3 border-t border-border-light"
              >
                <div class="grid grid-cols-4 gap-2">
                  <div
                    class="text-center p-2 bg-white dark:bg-white/5 rounded-lg border border-border-light"
                  >
                    <p class="text-xs text-text-secondary mb-1">Metodologi</p>
                    <p class="text-sm font-bold text-text-main">
                      {{ penguji.nilai_mt ?? "-" }}
                    </p>
                  </div>
                  <div
                    class="text-center p-2 bg-white dark:bg-white/5 rounded-lg border border-border-light"
                  >
                    <p class="text-xs text-text-secondary mb-1">Materi</p>
                    <p class="text-sm font-bold text-text-main">
                      {{ penguji.nilai_ms ?? "-" }}
                    </p>
                  </div>
                  <div
                    class="text-center p-2 bg-white dark:bg-white/5 rounded-lg border border-border-light"
                  >
                    <p class="text-xs text-text-secondary mb-1">Penyajian</p>
                    <p class="text-sm font-bold text-text-main">
                      {{ penguji.nilai_pm ?? "-" }}
                    </p>
                  </div>
                  <div
                    class="text-center p-2 bg-white dark:bg-white/5 rounded-lg border border-border-light"
                  >
                    <p class="text-xs text-text-secondary mb-1">Penguasaan</p>
                    <p class="text-sm font-bold text-text-main">
                      {{ penguji.nilai_pi ?? "-" }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div v-else class="text-center py-8 text-text-secondary">
            <span class="material-symbols-outlined text-4xl mb-2 block"
              >group_off</span
            >
            <p>Belum ada dosen penguji ditugaskan</p>
          </div>
        </div>
      </div>

      <!-- Input Nilai Section -->
      <div
        class="bg-surface-light border border-border-light rounded-xl shadow-sm"
      >
        <div
          class="p-5 border-b border-border-light flex items-center justify-between"
        >
          <div>
            <h3 class="text-lg font-bold text-text-main">
              Input Nilai Seminar
            </h3>
            <p class="text-sm text-text-secondary">
              Input nilai dari masing-masing penguji dan tentukan hasil seminar
            </p>
          </div>
        </div>
        <div class="p-5">
          <!-- Nilai per penguji -->
          <div
            v-if="seminar.penguji && seminar.penguji.length > 0"
            class="space-y-4"
          >
            <div
              v-for="(penguji, index) in nilaiForm.pengujiNilai"
              :key="penguji.penguji_id"
              class="p-4 bg-gray-50 dark:bg-white/5 rounded-xl border border-border-light"
            >
              <!-- Penguji header -->
              <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-3">
                  <div
                    class="size-9 rounded-full flex items-center justify-center text-xs font-bold shrink-0"
                    :class="getAvatarColor(penguji.nama)"
                  >
                    {{ getInitials(penguji.nama) }}
                  </div>
                  <div class="min-w-0">
                    <p class="font-medium text-text-main text-sm truncate">
                      {{ penguji.nama }}
                    </p>
                    <div class="flex items-center gap-2 mt-0.5">
                      <span
                        class="text-xs font-medium capitalize px-2 py-0.5 rounded"
                        :class="getPeranClass(penguji.peran)"
                      >
                        {{ getPeranLabel(penguji.peran) }}
                      </span>
                      <span
                        v-if="penguji.dosenScored"
                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400"
                      >
                        <span class="material-symbols-outlined text-[12px]"
                          >check_circle</span
                        >
                        Sudah dinilai dosen
                      </span>
                    </div>
                  </div>
                </div>
                <div class="text-right">
                  <p class="text-lg font-bold text-primary">
                    {{ getPengujiAverage(penguji) }}
                  </p>
                  <p class="text-xs text-text-secondary">Rata-rata</p>
                </div>
              </div>
              <!-- 4-component score inputs -->
              <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div>
                  <label class="block text-xs text-text-secondary mb-1"
                    >Metodologi (MT)</label
                  >
                  <input
                    v-model.number="penguji.nilai_mt"
                    type="number"
                    min="0"
                    max="100"
                    step="0.01"
                    placeholder="0-100"
                    class="w-full px-3 py-2 border border-border-light rounded-lg text-center text-sm font-bold bg-white dark:bg-white/5 text-text-main focus:ring-2 focus:ring-primary/20 focus:border-primary"
                    :disabled="isLocked"
                  />
                </div>
                <div>
                  <label class="block text-xs text-text-secondary mb-1"
                    >Materi (MS)</label
                  >
                  <input
                    v-model.number="penguji.nilai_ms"
                    type="number"
                    min="0"
                    max="100"
                    step="0.01"
                    placeholder="0-100"
                    class="w-full px-3 py-2 border border-border-light rounded-lg text-center text-sm font-bold bg-white dark:bg-white/5 text-text-main focus:ring-2 focus:ring-primary/20 focus:border-primary"
                    :disabled="isLocked"
                  />
                </div>
                <div>
                  <label class="block text-xs text-text-secondary mb-1"
                    >Penyajian (PM)</label
                  >
                  <input
                    v-model.number="penguji.nilai_pm"
                    type="number"
                    min="0"
                    max="100"
                    step="0.01"
                    placeholder="0-100"
                    class="w-full px-3 py-2 border border-border-light rounded-lg text-center text-sm font-bold bg-white dark:bg-white/5 text-text-main focus:ring-2 focus:ring-primary/20 focus:border-primary"
                    :disabled="isLocked"
                  />
                </div>
                <div>
                  <label class="block text-xs text-text-secondary mb-1"
                    >Penguasaan (PI)</label
                  >
                  <input
                    v-model.number="penguji.nilai_pi"
                    type="number"
                    min="0"
                    max="100"
                    step="0.01"
                    placeholder="0-100"
                    class="w-full px-3 py-2 border border-border-light rounded-lg text-center text-sm font-bold bg-white dark:bg-white/5 text-text-main focus:ring-2 focus:ring-primary/20 focus:border-primary"
                    :disabled="isLocked"
                  />
                </div>
              </div>
              <!-- Catatan -->
              <div class="mt-3">
                <input
                  v-model="penguji.catatan"
                  type="text"
                  placeholder="Catatan (opsional)"
                  class="w-full px-3 py-2 border border-border-light rounded-lg text-sm bg-white dark:bg-white/5 text-text-main focus:ring-2 focus:ring-primary/20 focus:border-primary"
                  :disabled="isLocked"
                />
              </div>
            </div>

            <!-- Rata-rata Nilai -->
            <div
              class="flex items-center justify-between p-4 bg-primary/5 rounded-xl border border-primary/20"
            >
              <div>
                <p class="text-sm font-bold text-text-main">
                  Rata-rata Nilai Seminar
                </p>
                <p class="text-xs text-text-secondary">
                  Dari
                  {{
                    nilaiForm.pengujiNilai.filter(
                      (p) => getPengujiAverage(p) !== "-",
                    ).length
                  }}
                  penguji yang sudah dinilai
                </p>
              </div>
              <p class="text-3xl font-bold text-primary">
                {{ averageNilai }}
              </p>
            </div>

            <!-- Hasil & Catatan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
              <div>
                <label class="block text-sm font-medium text-text-main mb-1"
                  >Hasil Seminar <span class="text-red-500">*</span></label
                >
                <select
                  v-model="nilaiForm.hasil"
                  class="w-full px-3 py-2 border border-border-light rounded-lg text-sm bg-white dark:bg-white/5 text-text-main focus:ring-2 focus:ring-primary/20 focus:border-primary"
                  :disabled="isLocked"
                >
                  <option class="bg-white text-gray-900" value="">
                    Pilih Hasil
                  </option>
                  <option class="bg-white text-gray-900" value="lulus">
                    Lulus
                  </option>
                  <option
                    class="bg-white text-gray-900"
                    value="lulus_bersyarat"
                  >
                    Lulus Bersyarat
                  </option>
                  <option class="bg-white text-gray-900" value="tidak_lulus">
                    Tidak Lulus
                  </option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-text-main mb-1"
                  >Catatan Seminar</label
                >
                <input
                  v-model="nilaiForm.catatan"
                  type="text"
                  placeholder="Catatan umum seminar..."
                  class="w-full px-3 py-2 border border-border-light rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary"
                  :disabled="isLocked"
                />
              </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end gap-3 pt-2">
              <button
                v-if="seminar.status === 'selesai' && isLocked"
                @click="toggleLock"
                class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-orange-600 bg-orange-50 dark:bg-orange-900/20 rounded-lg hover:bg-orange-100 dark:hover:bg-orange-900/30 border border-orange-200 dark:border-orange-800 transition-colors"
              >
                <span class="material-symbols-outlined text-[18px]"
                  >lock_open</span
                >
                Buka Kunci
              </button>
              <button
                v-if="!isLocked"
                @click="submitNilai"
                :disabled="savingNilai || !nilaiForm.hasil"
                class="px-6 py-2.5 bg-green-600 text-white rounded-lg font-medium text-sm hover:bg-green-700 transition-colors disabled:opacity-50 flex items-center gap-2 shadow-sm"
              >
                <span class="material-symbols-outlined text-[18px]"
                  >check_circle</span
                >
                {{
                  savingNilai
                    ? "Menyimpan..."
                    : "Simpan Nilai & Selesaikan Seminar"
                }}
              </button>
              <div
                v-if="isLocked && seminar.status === 'selesai'"
                class="flex items-center gap-2 px-4 py-2 bg-green-50 dark:bg-green-900/20 text-green-600 rounded-lg border border-green-200 dark:border-green-800"
              >
                <span class="material-symbols-outlined text-[18px]"
                  >verified</span
                >
                <span class="text-sm font-medium"
                  >Seminar telah selesai dan dinilai</span
                >
              </div>
            </div>
          </div>
          <div v-else class="text-center py-8 text-text-secondary">
            <span class="material-symbols-outlined text-4xl mb-2 block"
              >grading</span
            >
            <p>Tambahkan penguji terlebih dahulu untuk input nilai</p>
          </div>
        </div>
      </div>

      <!-- Berita Acara Section -->
      <div
        v-if="seminar.berita_acara"
        class="bg-surface-light border border-border-light rounded-xl shadow-sm"
      >
        <div class="p-5 border-b border-border-light">
          <h3 class="text-lg font-bold text-text-main">Berita Acara</h3>
        </div>
        <div class="p-5">
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
              <p class="text-xs text-text-secondary mb-1">Nomor BA</p>
              <p class="text-sm font-medium text-text-main">
                {{ seminar.berita_acara.nomor }}
              </p>
            </div>
            <div>
              <p class="text-xs text-text-secondary mb-1">Tanggal</p>
              <p class="text-sm font-medium text-text-main">
                {{ formatDate(seminar.berita_acara.tanggal) }}
              </p>
            </div>
            <div>
              <p class="text-xs text-text-secondary mb-1">Hasil</p>
              <span
                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold"
                :class="getHasilClass(seminar.berita_acara.hasil)"
              >
                {{ getHasilLabel(seminar.berita_acara.hasil) }}
              </span>
            </div>
            <div>
              <p class="text-xs text-text-secondary mb-1">Catatan</p>
              <p class="text-sm text-text-main">
                {{ seminar.berita_acara.catatan || "-" }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Revisi Proposal Section (for lulus_bersyarat) -->
      <div
        v-if="
          seminar.berita_acara?.hasil === 'lulus_bersyarat' ||
          seminar.hasil === 'lulus_bersyarat'
        "
        class="bg-surface-light border border-border-light rounded-xl shadow-sm"
      >
        <div
          class="p-5 border-b border-border-light flex items-center justify-between"
        >
          <div>
            <h3 class="text-lg font-bold text-text-main">Revisi Proposal</h3>
            <p class="text-sm text-text-secondary">
              Dokumen revisi proposal yang diunggah mahasiswa setelah lulus
              bersyarat
            </p>
          </div>
          <span
            class="px-3 py-1 rounded-full text-xs font-bold bg-yellow-50 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-400 border border-yellow-200 dark:border-yellow-800"
          >
            Lulus Bersyarat
          </span>
        </div>
        <div class="p-5">
          <div v-if="revisiProposalDocs.length > 0" class="space-y-3">
            <div
              v-for="doc in revisiProposalDocs"
              :key="doc.id"
              class="flex items-center justify-between p-4 rounded-xl border border-border-light hover:border-primary/30 transition-colors"
            >
              <div class="flex items-center gap-4 min-w-0">
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
                <!-- Status badge -->
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
                <!-- View button -->
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
                <!-- Approve/Reject buttons (only for pending) -->
                <template v-if="doc.status === 'pending'">
                  <button
                    @click="approveRevisiProposal(doc.id)"
                    :disabled="approvingRevisiProposal === doc.id"
                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50"
                    title="Setujui"
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
                    title="Tolak"
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
          <div v-else class="text-center py-8 text-text-secondary">
            <span class="material-symbols-outlined text-4xl mb-2 block"
              >upload_file</span
            >
            <p>Mahasiswa belum mengunggah dokumen revisi proposal</p>
          </div>
        </div>
      </div>

      <!-- Dokumen Resmi Section -->
      <div
        class="bg-surface-light border border-border-light rounded-xl shadow-sm"
      >
        <div class="p-5 border-b border-border-light">
          <h3 class="text-lg font-bold text-text-main">Dokumen Resmi</h3>
          <p class="text-sm text-text-secondary">
            Download atau lihat dokumen resmi terkait seminar proposal
          </p>
        </div>
        <div class="p-5 space-y-4">
          <!-- SK Penguji Sempro -->
          <div
            class="flex items-center justify-between p-4 rounded-xl border border-border-light"
            :class="
              seminar.penguji?.length
                ? 'bg-white dark:bg-white/5'
                : 'bg-gray-50 dark:bg-gray-900/20 opacity-60'
            "
          >
            <div class="flex items-center gap-4">
              <div
                class="size-12 rounded-xl flex items-center justify-center bg-red-50 dark:bg-red-900/20 text-red-500"
              >
                <span class="material-symbols-outlined">picture_as_pdf</span>
              </div>
              <div>
                <h4 class="text-sm font-bold text-text-main">
                  SK Penguji Seminar Proposal
                </h4>
                <p class="text-xs text-text-secondary">
                  {{
                    seminar.penguji?.length
                      ? "Dosen penguji sudah ditugaskan"
                      : "Belum ada dosen penguji"
                  }}
                </p>
              </div>
            </div>
            <div v-if="seminar.penguji?.length" class="flex items-center gap-2">
              <button
                @click="previewSkPenguji"
                :disabled="previewingPdf === 'sk-penguji'"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-primary bg-primary/10 rounded-lg hover:bg-primary/20 transition-colors disabled:opacity-50"
              >
                <span class="material-symbols-outlined text-[16px]">{{
                  previewingPdf === "sk-penguji"
                    ? "hourglass_top"
                    : "visibility"
                }}</span>
                {{ previewingPdf === "sk-penguji" ? "Memuat..." : "Lihat" }}
              </button>
              <button
                @click="downloadSkPenguji"
                :disabled="downloadingPdf === 'sk-penguji'"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors disabled:opacity-50 shadow-sm"
              >
                <span class="material-symbols-outlined text-[16px]">{{
                  downloadingPdf === "sk-penguji" ? "hourglass_top" : "download"
                }}</span>
                {{ downloadingPdf === "sk-penguji" ? "Mengunduh..." : "Unduh" }}
              </button>
            </div>
          </div>

          <!-- Berita Acara Sempro -->
          <div
            class="flex items-center justify-between p-4 rounded-xl border border-border-light"
            :class="
              seminar.berita_acara
                ? 'bg-white dark:bg-white/5'
                : 'bg-gray-50 dark:bg-gray-900/20 opacity-60'
            "
          >
            <div class="flex items-center gap-4">
              <div
                class="size-12 rounded-xl flex items-center justify-center bg-blue-50 dark:bg-blue-900/20 text-blue-500"
              >
                <span class="material-symbols-outlined">description</span>
              </div>
              <div>
                <h4 class="text-sm font-bold text-text-main">
                  Berita Acara Seminar Proposal
                </h4>
                <p class="text-xs text-text-secondary">
                  {{
                    seminar.berita_acara
                      ? "Seminar telah selesai"
                      : "Seminar belum selesai"
                  }}
                </p>
              </div>
            </div>
            <div v-if="seminar.berita_acara" class="flex items-center gap-2">
              <button
                @click="previewBeritaAcara"
                :disabled="previewingPdf === 'berita-acara'"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-primary bg-primary/10 rounded-lg hover:bg-primary/20 transition-colors disabled:opacity-50"
              >
                <span class="material-symbols-outlined text-[16px]">{{
                  previewingPdf === "berita-acara"
                    ? "hourglass_top"
                    : "visibility"
                }}</span>
                {{ previewingPdf === "berita-acara" ? "Memuat..." : "Lihat" }}
              </button>
              <button
                @click="downloadBeritaAcara"
                :disabled="downloadingPdf === 'berita-acara'"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors disabled:opacity-50 shadow-sm"
              >
                <span class="material-symbols-outlined text-[16px]">{{
                  downloadingPdf === "berita-acara"
                    ? "hourglass_top"
                    : "download"
                }}</span>
                {{
                  downloadingPdf === "berita-acara" ? "Mengunduh..." : "Unduh"
                }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </template>

    <!-- Not Found -->
    <div v-else class="text-center py-12">
      <span
        class="material-symbols-outlined text-5xl text-text-secondary mb-3 block"
        >search_off</span
      >
      <p class="text-text-main font-bold text-lg">
        Data seminar tidak ditemukan
      </p>
      <router-link
        to="/admin/seminar"
        class="text-primary hover:underline text-sm mt-2 inline-block"
      >
        ← Kembali ke daftar
      </router-link>
    </div>

    <!-- Edit Jadwal Modal -->
    <Transition name="modal-fade">
      <div
        v-if="showEditJadwalModal"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
      >
        <div
          class="bg-white dark:bg-surface-light rounded-xl shadow-2xl w-full max-w-lg"
        >
          <div class="p-6 border-b border-border-light">
            <h2 class="text-xl font-bold text-text-main">
              Edit Jadwal Seminar
            </h2>
          </div>
          <form @submit.prevent="saveEditJadwal" class="p-6 space-y-4">
            <div>
              <label class="block text-sm font-medium text-text-main mb-1"
                >Tanggal</label
              >
              <input
                v-model="editJadwalForm.tanggal"
                type="date"
                class="w-full px-3 py-2 border border-border-light rounded-lg bg-white dark:bg-white/5 text-text-main focus:ring-2 focus:ring-primary/20 focus:border-primary"
                required
              />
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-text-main mb-1"
                  >Jam Mulai</label
                >
                <input
                  v-model="editJadwalForm.waktu"
                  type="time"
                  class="w-full px-3 py-2 border border-border-light rounded-lg bg-white dark:bg-white/5 text-text-main focus:ring-2 focus:ring-primary/20 focus:border-primary"
                  required
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-text-main mb-1"
                  >Ruangan</label
                >
                <input
                  v-model="editJadwalForm.ruangan"
                  type="text"
                  class="w-full px-3 py-2 border border-border-light rounded-lg bg-white dark:bg-white/5 text-text-main focus:ring-2 focus:ring-primary/20 focus:border-primary"
                  required
                />
              </div>
            </div>
            <div class="flex gap-3 pt-4">
              <button
                type="button"
                @click="showEditJadwalModal = false"
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

    <!-- Add Penguji Modal -->
    <Transition name="modal-fade">
      <div
        v-if="showAddPengujiModal"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
      >
        <div
          class="bg-white dark:bg-surface-light rounded-xl shadow-2xl w-full max-w-lg"
        >
          <div class="p-6 border-b border-border-light">
            <h2 class="text-xl font-bold text-text-main">Tambah Penguji</h2>
          </div>
          <form @submit.prevent="saveAddPenguji" class="p-6 space-y-4">
            <div class="relative">
              <label class="block text-sm font-medium text-text-main mb-1"
                >Dosen <span class="text-red-500">*</span></label
              >
              <input
                v-model="dosenSearch"
                @input="filterDosen"
                @focus="showDosenDropdown = true"
                type="text"
                class="w-full px-3 py-2 border border-border-light rounded-lg bg-white dark:bg-white/5 text-text-main focus:ring-2 focus:ring-primary/20 focus:border-primary"
                placeholder="Ketik nama atau NIP dosen..."
                autocomplete="off"
              />
              <div
                v-if="showDosenDropdown && filteredDosenList.length > 0"
                class="absolute z-10 w-full mt-1 bg-white dark:bg-surface-light border border-border-light rounded-lg shadow-lg max-h-48 overflow-y-auto"
              >
                <div
                  v-for="dosen in filteredDosenList"
                  :key="dosen.id"
                  @mousedown.prevent="selectDosen(dosen)"
                  class="flex items-center gap-3 px-3 py-2.5 hover:bg-primary/5 cursor-pointer transition-colors border-b border-border-light last:border-b-0"
                >
                  <div
                    class="size-8 rounded-full flex items-center justify-center text-xs font-bold shrink-0"
                    :class="getAvatarColor(dosen.nama)"
                  >
                    {{ getInitials(dosen.nama) }}
                  </div>
                  <div class="min-w-0">
                    <p class="text-sm font-medium text-text-main truncate">
                      {{ dosen.nama_lengkap || dosen.full_name || dosen.nama }}
                    </p>
                    <p class="text-xs text-text-secondary">
                      NIP: {{ dosen.nip || "-" }}
                    </p>
                  </div>
                </div>
              </div>
              <div
                v-if="
                  showDosenDropdown &&
                  dosenSearch &&
                  filteredDosenList.length === 0
                "
                class="absolute z-10 w-full mt-1 bg-white dark:bg-surface-light border border-border-light rounded-lg shadow-lg p-3 text-sm text-text-secondary text-center"
              >
                Dosen tidak ditemukan
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-text-main mb-1"
                >Peran <span class="text-red-500">*</span></label
              >
              <select
                v-model="pengujiForm.peran"
                class="w-full px-3 py-2 border border-border-light rounded-lg bg-white dark:bg-white/5 text-text-main focus:ring-2 focus:ring-primary/20 focus:border-primary"
                required
              >
                <option class="bg-white text-gray-900" value="">
                  Pilih Peran
                </option>
                <option class="bg-white text-gray-900" value="ketua">
                  Ketua
                </option>
                <option class="bg-white text-gray-900" value="penguji_1">
                  Penguji 1
                </option>
                <option class="bg-white text-gray-900" value="penguji_2">
                  Penguji 2
                </option>
              </select>
            </div>
            <div class="flex gap-3 pt-4">
              <button
                type="button"
                @click="showAddPengujiModal = false"
                class="flex-1 px-4 py-2.5 border border-border-light rounded-lg text-text-secondary hover:bg-gray-50 dark:hover:bg-white/5 transition-colors"
              >
                Batal
              </button>
              <button
                type="submit"
                :disabled="saving"
                class="flex-1 px-4 py-2.5 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors disabled:opacity-50"
              >
                {{ saving ? "Menyimpan..." : "Tambah Penguji" }}
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
import { useRoute, useRouter } from "vue-router";
import adminService from "../../../services/adminService";

const route = useRoute();
const router = useRouter();

const loading = ref(true);
const saving = ref(false);
const savingNilai = ref(false);
const seminar = ref(null);
const dosenList = ref([]);
const isLocked = ref(true);
const dosenSearch = ref("");
const showDosenDropdown = ref(false);
const downloadingPdf = ref(null);
const previewingPdf = ref(null);
const filteredDosenList = ref([]);
const uploadingProposal = ref(false);
const proposalFileInput = ref(null);

// Computed: check both file_skripsi_url and dokumen table for proposal
const proposalUrl = computed(() => {
  // Primary: check file_skripsi_url on skripsi
  if (seminar.value?.skripsi?.file_skripsi_url) {
    return seminar.value.skripsi.file_skripsi_url;
  }
  // Fallback: check dokumen table for jenis='proposal'
  const docs = seminar.value?.skripsi?.dokumen;
  if (docs && Array.isArray(docs)) {
    const proposalDoc = docs.find((d) => d.jenis === "proposal");
    if (proposalDoc?.file_url) {
      return proposalDoc.file_url;
    }
  }
  return null;
});

const showEditJadwalModal = ref(false);
const showAddPengujiModal = ref(false);

const editJadwalForm = reactive({
  tanggal: "",
  waktu: "",
  ruangan: "",
});

const pengujiForm = reactive({
  dosen_id: "",
  peran: "",
});

const nilaiForm = reactive({
  pengujiNilai: [],
  hasil: "",
  catatan: "",
});

const getPengujiAverage = (penguji) => {
  const mt = penguji.nilai_mt;
  const ms = penguji.nilai_ms;
  const pm = penguji.nilai_pm;
  const pi = penguji.nilai_pi;
  if (
    mt !== null &&
    mt !== "" &&
    !isNaN(mt) &&
    ms !== null &&
    ms !== "" &&
    !isNaN(ms) &&
    pm !== null &&
    pm !== "" &&
    !isNaN(pm) &&
    pi !== null &&
    pi !== "" &&
    !isNaN(pi)
  ) {
    return ((Number(mt) + Number(ms) + Number(pm) + Number(pi)) / 4).toFixed(2);
  }
  return "-";
};

const averageNilai = computed(() => {
  const scored = nilaiForm.pengujiNilai.filter(
    (p) => getPengujiAverage(p) !== "-",
  );
  if (scored.length === 0) return "-";
  const sum = scored.reduce((acc, p) => acc + Number(getPengujiAverage(p)), 0);
  return (sum / scored.length).toFixed(2);
});

// ---- REVISI PROPOSAL (lulus bersyarat) ----
const revisiProposalDocs = computed(() => {
  const docs = seminar.value?.skripsi?.dokumen || [];
  return docs.filter((d) => d.jenis === "revisi_proposal");
});

const approvingRevisiProposal = ref(null);

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
    alert(
      "Dokumen revisi proposal disetujui. Status skripsi berubah menjadi Penentuan Dospem.",
    );
    fetchSeminarDetail();
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
  if (catatan === null) return; // cancelled
  try {
    approvingRevisiProposal.value = docId;
    await adminService.updateDokumen(docId, { status: "rejected", catatan });
    alert("Dokumen revisi proposal ditolak.");
    fetchSeminarDetail();
  } catch (error) {
    console.error("Failed to reject revisi proposal:", error);
    alert("Gagal menolak: " + (error.response?.data?.message || error.message));
  } finally {
    approvingRevisiProposal.value = null;
  }
};

const fetchSeminarDetail = async () => {
  try {
    loading.value = true;
    const response = await adminService.getSeminarDetail(route.params.id);
    if (response.success) {
      seminar.value = response.data;
      isLocked.value = seminar.value.status === "selesai";
      initNilaiForm();
    }
  } catch (error) {
    console.error("Failed to fetch seminar detail:", error);
  } finally {
    loading.value = false;
  }
};

const initNilaiForm = () => {
  if (seminar.value && seminar.value.penguji) {
    nilaiForm.pengujiNilai = seminar.value.penguji.map((p) => ({
      penguji_id: p.id,
      dosen_id: p.dosen_id,
      nama: p.dosen?.nama_lengkap || p.dosen?.nama || "-",
      peran: p.peran,
      nilai_mt: p.nilai_mt,
      nilai_ms: p.nilai_ms,
      nilai_pm: p.nilai_pm,
      nilai_pi: p.nilai_pi,
      catatan: p.catatan || "",
      dosenScored: p.nilai_mt !== null && p.nilai_mt !== undefined,
    }));
  }
  if (seminar.value?.berita_acara) {
    nilaiForm.hasil = seminar.value.berita_acara.hasil || "";
    nilaiForm.catatan = seminar.value.berita_acara.catatan || "";
  }
};

const toggleLock = () => {
  isLocked.value = !isLocked.value;
};

const deleteSeminar = async () => {
  if (!confirm("Apakah Anda yakin ingin menghapus seminar ini?")) return;
  try {
    await adminService.deleteSeminar(seminar.value.id);
    alert("Seminar berhasil dihapus.");
    router.push("/admin/seminar");
  } catch (error) {
    console.error("Failed to delete seminar:", error);
    alert(
      "Gagal menghapus seminar: " +
        (error.response?.data?.message || error.message),
    );
  }
};

const triggerProposalUpload = () => {
  proposalFileInput.value?.click();
};

const uploadProposalFile = async (e) => {
  const file = e.target.files[0];
  if (!file) return;
  e.target.value = "";
  try {
    uploadingProposal.value = true;
    const response = await adminService.uploadSeminarProposal(
      seminar.value.id,
      file,
    );
    if (response.success) {
      // Update the local skripsi data with new file URL
      if (seminar.value.skripsi) {
        seminar.value.skripsi.file_skripsi_url = response.data.file_skripsi_url;
      }
      alert("Dokumen proposal berhasil diupload");
    }
  } catch (error) {
    console.error("Failed to upload proposal:", error);
    alert(
      "Gagal mengupload dokumen: " +
        (error.response?.data?.message || error.message),
    );
  } finally {
    uploadingProposal.value = false;
  }
};

const fetchDosen = async () => {
  try {
    const response = await adminService.getDosen({ per_page: 100 });
    if (response.success) {
      dosenList.value = response.data.data || response.data;
    }
  } catch (error) {
    console.error("Failed to fetch dosen:", error);
  }
};

const openEditJadwalModal = () => {
  editJadwalForm.tanggal = seminar.value.tanggal
    ? seminar.value.tanggal.substring(0, 10)
    : "";
  editJadwalForm.waktu = seminar.value.waktu || "";
  editJadwalForm.ruangan = seminar.value.ruangan || "";
  showEditJadwalModal.value = true;
};

const saveEditJadwal = async () => {
  try {
    saving.value = true;
    await adminService.updateSeminar(seminar.value.id, {
      tanggal: editJadwalForm.tanggal,
      waktu: editJadwalForm.waktu,
      ruangan: editJadwalForm.ruangan,
    });
    showEditJadwalModal.value = false;
    fetchSeminarDetail();
  } catch (error) {
    console.error("Failed to update jadwal:", error);
    alert(
      "Gagal memperbarui jadwal: " +
        (error.response?.data?.message || error.message),
    );
  } finally {
    saving.value = false;
  }
};

const openAddPengujiModal = () => {
  pengujiForm.dosen_id = "";
  pengujiForm.peran = "";
  dosenSearch.value = "";
  showDosenDropdown.value = false;
  filteredDosenList.value = [];
  showAddPengujiModal.value = true;
  fetchDosen();
};

const filterDosen = () => {
  const query = dosenSearch.value.toLowerCase();
  if (!query) {
    filteredDosenList.value = dosenList.value;
  } else {
    filteredDosenList.value = dosenList.value.filter((d) => {
      const name = (
        d.nama_lengkap ||
        d.full_name ||
        d.nama ||
        ""
      ).toLowerCase();
      const nip = (d.nip || "").toLowerCase();
      return name.includes(query) || nip.includes(query);
    });
  }
  showDosenDropdown.value = true;
};

const selectDosen = (dosen) => {
  pengujiForm.dosen_id = dosen.id;
  dosenSearch.value =
    (dosen.nama_lengkap || dosen.full_name || dosen.nama) +
    (dosen.nip ? " - " + dosen.nip : "");
  showDosenDropdown.value = false;
};

const saveAddPenguji = async () => {
  try {
    saving.value = true;
    // Use the update endpoint to add a penguji
    // We need a custom endpoint, but since we have the store endpoint that can include penguji,
    // let's use a direct API call
    const api = (await import("../../../services/api")).default;
    await api.post(`/admin/seminar/${seminar.value.id}/penguji`, {
      dosen_id: pengujiForm.dosen_id,
      peran: pengujiForm.peran,
    });
    showAddPengujiModal.value = false;
    fetchSeminarDetail();
  } catch (error) {
    console.error("Failed to add penguji:", error);
    alert(
      "Gagal menambah penguji: " +
        (error.response?.data?.message || error.message),
    );
  } finally {
    saving.value = false;
  }
};

const removePenguji = async (penguji) => {
  if (
    !confirm(
      `Hapus ${penguji.dosen?.nama || "penguji ini"} dari daftar penguji?`,
    )
  )
    return;
  try {
    const api = (await import("../../../services/api")).default;
    await api.delete(
      `/admin/seminar/${seminar.value.id}/penguji/${penguji.id}`,
    );
    fetchSeminarDetail();
  } catch (error) {
    console.error("Failed to remove penguji:", error);
    alert(
      "Gagal menghapus penguji: " +
        (error.response?.data?.message || error.message),
    );
  }
};

const submitNilai = async () => {
  if (!nilaiForm.hasil) {
    alert("Pilih hasil seminar terlebih dahulu");
    return;
  }

  try {
    savingNilai.value = true;
    const api = (await import("../../../services/api")).default;

    // Update nilai per penguji (send all 4 components)
    for (const p of nilaiForm.pengujiNilai) {
      const hasAnyNilai = [p.nilai_mt, p.nilai_ms, p.nilai_pm, p.nilai_pi].some(
        (v) => v !== null && v !== "" && !isNaN(v),
      );
      if (hasAnyNilai) {
        await api.put(
          `/admin/seminar/${seminar.value.id}/penguji/${p.penguji_id}`,
          {
            nilai_mt: p.nilai_mt !== "" ? Number(p.nilai_mt) : null,
            nilai_ms: p.nilai_ms !== "" ? Number(p.nilai_ms) : null,
            nilai_pm: p.nilai_pm !== "" ? Number(p.nilai_pm) : null,
            nilai_pi: p.nilai_pi !== "" ? Number(p.nilai_pi) : null,
            catatan: p.catatan,
          },
        );
      }
    }

    // Update seminar nilai (average), status, and hasil
    // Backend will auto-update skripsi status to penentuan_dospem
    // when hasil is lulus/lulus_bersyarat — no separate verification needed
    await adminService.updateSeminar(seminar.value.id, {
      nilai: averageNilai.value !== "-" ? Number(averageNilai.value) : null,
      catatan: nilaiForm.catatan,
      status: "selesai",
      hasil: nilaiForm.hasil,
    });

    // Create berita acara
    try {
      const now = new Date();
      const nomorBA = `BA/SEMPRO/${now.getFullYear()}/${String(now.getMonth() + 1).padStart(2, "0")}/${seminar.value.id}`;
      await api.post(`/admin/seminar/${seminar.value.id}/berita-acara`, {
        nomor: nomorBA,
        hasil: nilaiForm.hasil,
        catatan: nilaiForm.catatan,
      });
    } catch (baError) {
      // If berita acara already exists, ignore
      console.warn("Berita acara might already exist:", baError);
    }

    isLocked.value = true;
    fetchSeminarDetail();
    alert("Nilai berhasil disimpan dan seminar telah diselesaikan.");
  } catch (error) {
    console.error("Failed to submit nilai:", error);
    alert(
      "Gagal menyimpan nilai: " +
        (error.response?.data?.message || error.message),
    );
  } finally {
    savingNilai.value = false;
  }
};

const viewDetail = (seminarItem) => {
  if (seminarItem && seminarItem.id) {
    router.push(`/admin/seminar/${seminarItem.id}`);
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
    weekday: "long",
    day: "numeric",
    month: "long",
    year: "numeric",
  });
};

const formatFileSize = (bytes) => {
  if (!bytes) return "";
  if (bytes < 1024) return bytes + " B";
  if (bytes < 1048576) return (bytes / 1024).toFixed(1) + " KB";
  return (bytes / 1048576).toFixed(1) + " MB";
};

const getFileUrl = (path) => {
  if (!path) return "#";
  if (path.startsWith("http")) return path;
  const baseUrl =
    import.meta.env.VITE_API_URL?.replace("/api", "") || "";
  return `${baseUrl}/api/file/${path}`;
};

const formatPembimbingJenis = (jenis) => {
  const labels = {
    pembimbing_1: "Pembimbing 1",
    pembimbing_2: "Pembimbing 2",
  };
  return labels[jenis] || jenis;
};

const getSeminarStatusClass = (status) => {
  const classes = {
    terjadwal: "bg-blue-50 text-blue-600 border border-blue-100",
    berlangsung: "bg-purple-50 text-purple-600 border border-purple-100",
    selesai: "bg-green-50 text-green-600 border border-green-100",
    batal: "bg-red-50 text-red-600 border border-red-100",
  };
  return classes[status] || "bg-gray-50 text-gray-600 border border-gray-100";
};

const getSeminarStatusDot = (status) => {
  const dots = {
    terjadwal: "bg-blue-600",
    berlangsung: "bg-purple-600",
    selesai: "bg-green-600",
    batal: "bg-red-600",
  };
  return dots[status] || "bg-gray-600";
};

const getSeminarStatusLabel = (status) => {
  const labels = {
    terjadwal: "Terjadwal",
    berlangsung: "Berlangsung",
    selesai: "Selesai",
    batal: "Dibatalkan",
  };
  return labels[status] || status;
};

const getPeranClass = (peran) => {
  const classes = {
    ketua: "bg-blue-100 text-blue-700",
    penguji_1: "bg-purple-100 text-purple-700",
    penguji_2: "bg-gray-100 text-gray-700",
  };
  return classes[peran] || "bg-gray-100 text-gray-700";
};

const getPeranLabel = (peran) => {
  const labels = {
    ketua: "Ketua",
    penguji_1: "Penguji 1",
    penguji_2: "Penguji 2",
  };
  return labels[peran] || peran;
};

const getHasilClass = (hasil) => {
  const classes = {
    lulus: "bg-green-100 text-green-700",
    lulus_bersyarat: "bg-yellow-100 text-yellow-700",
    tidak_lulus: "bg-red-100 text-red-700",
    mengulang: "bg-orange-100 text-orange-700",
  };
  return classes[hasil] || "bg-gray-100 text-gray-700";
};

const getHasilLabel = (hasil) => {
  const labels = {
    lulus: "Lulus",
    lulus_bersyarat: "Lulus Bersyarat",
    tidak_lulus: "Tidak Lulus",
    mengulang: "Mengulang",
  };
  return labels[hasil] || hasil;
};

// PDF Download & Preview
const downloadSkPenguji = async () => {
  downloadingPdf.value = "sk-penguji";
  try {
    const response = await adminService.getSkPengujiPdf(seminar.value.id);
    const blob = new Blob([response.data], { type: "application/pdf" });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = `SK_Penguji_Sempro_${seminar.value.skripsi?.mahasiswa?.nim || seminar.value.id}.pdf`;
    a.click();
    window.URL.revokeObjectURL(url);
  } catch (err) {
    console.error("Failed to download SK Penguji:", err);
    alert(
      "Gagal mengunduh SK Penguji: " +
        (err.response?.data?.message || err.message),
    );
  } finally {
    downloadingPdf.value = null;
  }
};

const previewSkPenguji = async () => {
  previewingPdf.value = "sk-penguji";
  try {
    const response = await adminService.getSkPengujiPdf(seminar.value.id);
    const blob = new Blob([response.data], { type: "application/pdf" });
    const url = window.URL.createObjectURL(blob);
    window.open(url, "_blank");
  } catch (err) {
    console.error("Failed to preview SK Penguji:", err);
    alert(
      "Gagal menampilkan SK Penguji: " +
        (err.response?.data?.message || err.message),
    );
  } finally {
    previewingPdf.value = null;
  }
};

const downloadBeritaAcara = async () => {
  downloadingPdf.value = "berita-acara";
  try {
    const response = await adminService.getBeritaAcaraPdf(seminar.value.id);
    const blob = new Blob([response.data], { type: "application/pdf" });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = `Berita_Acara_Sempro_${seminar.value.skripsi?.mahasiswa?.nim || seminar.value.id}.pdf`;
    a.click();
    window.URL.revokeObjectURL(url);
  } catch (err) {
    console.error("Failed to download Berita Acara:", err);
    alert(
      "Gagal mengunduh Berita Acara: " +
        (err.response?.data?.message || err.message),
    );
  } finally {
    downloadingPdf.value = null;
  }
};

const previewBeritaAcara = async () => {
  previewingPdf.value = "berita-acara";
  try {
    const response = await adminService.getBeritaAcaraPdf(seminar.value.id);
    const blob = new Blob([response.data], { type: "application/pdf" });
    const url = window.URL.createObjectURL(blob);
    window.open(url, "_blank");
  } catch (err) {
    console.error("Failed to preview Berita Acara:", err);
    alert(
      "Gagal menampilkan Berita Acara: " +
        (err.response?.data?.message || err.message),
    );
  } finally {
    previewingPdf.value = null;
  }
};

onMounted(() => {
  fetchSeminarDetail();
});
</script>
