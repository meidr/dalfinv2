<template>
  <div class="flex flex-col gap-8 animate-fade-in">
    <!-- Page Header -->
    <header>
      <h1 class="text-3xl font-bold tracking-tight text-text-main">
        Konfigurasi Sistem
      </h1>
      <p class="text-text-secondary text-base mt-2">
        Kelola pengaturan dan konfigurasi sistem skripsi.
      </p>
    </header>

    <!-- Syarat Bimbingan Ujian -->
    <section
      class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden"
    >
      <div class="p-5 border-b border-border-light flex items-center gap-3">
        <div class="p-2 bg-primary/10 rounded-lg text-primary">
          <span class="material-symbols-outlined">school</span>
        </div>
        <div>
          <h2 class="text-lg font-bold text-text-main">
            Syarat Bimbingan Ujian Skripsi
          </h2>
          <p class="text-sm text-text-secondary">
            Jumlah minimal bimbingan yang disetujui sebelum mahasiswa dapat
            mengajukan ujian
          </p>
        </div>
      </div>
      <div class="p-6">
        <div
          v-if="loadingConfig"
          class="flex items-center gap-3 text-text-secondary py-4"
        >
          <span
            class="animate-spin rounded-full h-5 w-5 border-b-2 border-primary"
          ></span>
          <span class="text-sm">Memuat konfigurasi...</span>
        </div>
        <div v-else class="flex flex-col gap-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Pembimbing 1 -->
            <div class="flex flex-col gap-2">
              <label class="text-sm font-bold text-text-main">
                Minimal Bimbingan Pembimbing 1
              </label>
              <p class="text-xs text-text-secondary mb-1">
                Jumlah sesi bimbingan yang harus disetujui oleh Pembimbing Utama
              </p>
              <input
                v-model.number="syaratForm.pembimbing_1"
                type="number"
                min="0"
                max="50"
                class="border border-border-light rounded-lg px-4 py-2.5 text-sm text-text-main bg-surface-light focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none w-full"
              />
            </div>
            <!-- Pembimbing 2 -->
            <div class="flex flex-col gap-2">
              <label class="text-sm font-bold text-text-main">
                Minimal Bimbingan Pembimbing 2
              </label>
              <p class="text-xs text-text-secondary mb-1">
                Jumlah sesi bimbingan yang harus disetujui oleh Pembimbing
                Pendamping
              </p>
              <input
                v-model.number="syaratForm.pembimbing_2"
                type="number"
                min="0"
                max="50"
                class="border border-border-light rounded-lg px-4 py-2.5 text-sm text-text-main bg-surface-light focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none w-full"
              />
            </div>
          </div>
          <div
            class="flex items-center justify-between pt-2 border-t border-border-light"
          >
            <p class="text-xs text-text-secondary">
              Perubahan akan berlaku untuk semua pengajuan ujian baru.
            </p>
            <button
              @click="saveSyarat"
              :disabled="savingConfig"
              class="inline-flex items-center gap-2 bg-primary hover:bg-blue-600 text-white font-bold px-6 py-2.5 rounded-lg transition-all shadow-sm text-sm disabled:opacity-50"
            >
              <span
                v-if="savingConfig"
                class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"
              ></span>
              <span v-else class="material-symbols-outlined text-[18px]"
                >save</span
              >
              {{ savingConfig ? "Menyimpan..." : "Simpan Perubahan" }}
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- Kuota Bimbingan Dosen -->
    <section
      class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden"
    >
      <div class="p-5 border-b border-border-light flex items-center gap-3">
        <div class="p-2 bg-emerald-500/10 rounded-lg text-emerald-600">
          <span class="material-symbols-outlined">group</span>
        </div>
        <div>
          <h2 class="text-lg font-bold text-text-main">
            Kuota Bimbingan Dosen
          </h2>
          <p class="text-sm text-text-secondary">
            Jumlah maksimal mahasiswa bimbingan per dosen (berlaku sebagai
            default untuk semua dosen)
          </p>
        </div>
      </div>
      <div class="p-6">
        <div
          v-if="loadingKuota"
          class="flex items-center gap-3 text-text-secondary py-4"
        >
          <span
            class="animate-spin rounded-full h-5 w-5 border-b-2 border-emerald-500"
          ></span>
          <span class="text-sm">Memuat konfigurasi...</span>
        </div>
        <div v-else class="flex flex-col gap-6">
          <div class="max-w-xs">
            <label class="text-sm font-bold text-text-main">
              Slot Kuota Default
            </label>
            <p class="text-xs text-text-secondary mb-2">
              Kuota ini berlaku untuk semua dosen yang tidak memiliki kuota
              individual. Kuota per dosen dapat diubah di Master Dosen.
            </p>
            <input
              v-model.number="kuotaForm.kuota"
              type="number"
              min="1"
              max="50"
              class="border border-border-light rounded-lg px-4 py-2.5 text-sm text-text-main bg-surface-light focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none w-full"
            />
          </div>
          <div
            class="flex items-center justify-between pt-2 border-t border-border-light"
          >
            <p class="text-xs text-text-secondary">
              Perubahan akan berlaku untuk semua dosen tanpa kuota individual.
            </p>
            <button
              @click="saveKuota"
              :disabled="savingKuota"
              class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-6 py-2.5 rounded-lg transition-all shadow-sm text-sm disabled:opacity-50"
            >
              <span
                v-if="savingKuota"
                class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"
              ></span>
              <span v-else class="material-symbols-outlined text-[18px]"
                >save</span
              >
              {{ savingKuota ? "Menyimpan..." : "Simpan Perubahan" }}
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- Tanggal Penting -->
    <section
      class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden"
    >
      <div class="p-5 border-b border-border-light flex items-center gap-3">
        <div class="p-2 bg-amber-500/10 rounded-lg text-amber-600">
          <span class="material-symbols-outlined">event</span>
        </div>
        <div>
          <h2 class="text-lg font-bold text-text-main">Tanggal Penting</h2>
          <p class="text-sm text-text-secondary">
            Atur jadwal penting yang akan ditampilkan di dashboard mahasiswa dan
            dosen
          </p>
        </div>
      </div>
      <div class="p-6">
        <div
          v-if="loadingDates"
          class="flex items-center gap-3 text-text-secondary py-4"
        >
          <span
            class="animate-spin rounded-full h-5 w-5 border-b-2 border-amber-500"
          ></span>
          <span class="text-sm">Memuat tanggal penting...</span>
        </div>
        <div v-else class="flex flex-col gap-4">
          <!-- Date entries -->
          <div
            v-for="(entry, idx) in dateEntries"
            :key="idx"
            class="flex items-end gap-3 p-3 rounded-lg border border-border-light bg-sidebar-light/30"
          >
            <div class="flex-1">
              <label
                class="text-xs font-bold text-text-secondary uppercase tracking-wider mb-1 block"
              >
                Keterangan
              </label>
              <input
                v-model="entry.label"
                type="text"
                placeholder="Contoh: Pendaftaran Seminar Proposal"
                class="border border-border-light rounded-lg px-3 py-2 text-sm text-text-main bg-surface-light focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none w-full"
              />
            </div>
            <div class="w-48">
              <label
                class="text-xs font-bold text-text-secondary uppercase tracking-wider mb-1 block"
              >
                Tanggal
              </label>
              <input
                v-model="entry.tanggal"
                type="date"
                class="border border-border-light rounded-lg px-3 py-2 text-sm text-text-main bg-surface-light focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none w-full"
              />
            </div>
            <button
              @click="removeDateEntry(idx)"
              class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors shrink-0"
              title="Hapus"
            >
              <span class="material-symbols-outlined text-[20px]">delete</span>
            </button>
          </div>

          <!-- Add button -->
          <button
            @click="addDateEntry"
            class="inline-flex items-center gap-2 text-amber-600 hover:text-amber-700 text-sm font-bold px-3 py-2 rounded-lg hover:bg-amber-50 transition-colors w-fit"
          >
            <span class="material-symbols-outlined text-[20px]">add</span>
            Tambah Tanggal Penting
          </button>

          <!-- Save -->
          <div
            class="flex items-center justify-between pt-2 border-t border-border-light"
          >
            <p class="text-xs text-text-secondary">
              Tanggal penting akan tampil di dashboard mahasiswa dan dosen.
            </p>
            <button
              @click="saveDates"
              :disabled="savingDates"
              class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-700 text-white font-bold px-6 py-2.5 rounded-lg transition-all shadow-sm text-sm disabled:opacity-50"
            >
              <span
                v-if="savingDates"
                class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"
              ></span>
              <span v-else class="material-symbols-outlined text-[18px]"
                >save</span
              >
              {{ savingDates ? "Menyimpan..." : "Simpan Perubahan" }}
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- Jenis Tanda Tangan Dokumen -->
    <section
      class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden"
    >
      <div class="p-5 border-b border-border-light flex items-center gap-3">
        <div class="p-2 bg-indigo-500/10 rounded-lg text-indigo-600">
          <span class="material-symbols-outlined">draw</span>
        </div>
        <div>
          <h2 class="text-lg font-bold text-text-main">
            Jenis Tanda Tangan Dokumen
          </h2>
          <p class="text-sm text-text-secondary">
            Pilih jenis tanda tangan yang digunakan pada dokumen yang dihasilkan
            sistem
          </p>
        </div>
      </div>
      <div class="p-6">
        <div
          v-if="loadingTtd"
          class="flex items-center gap-3 text-text-secondary py-4"
        >
          <span
            class="animate-spin rounded-full h-5 w-5 border-b-2 border-indigo-500"
          ></span>
          <span class="text-sm">Memuat konfigurasi...</span>
        </div>
        <div v-else class="flex flex-col gap-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- TTD Biasa -->
            <button
              @click="ttdForm.jenis = 'biasa'"
              class="relative flex flex-col items-center gap-3 p-6 rounded-xl border-2 transition-all text-center cursor-pointer"
              :class="
                ttdForm.jenis === 'biasa'
                  ? 'border-indigo-500 bg-indigo-50/50 dark:bg-indigo-900/10 shadow-md'
                  : 'border-border-light hover:border-indigo-300 hover:bg-indigo-50/20'
              "
            >
              <div
                v-if="ttdForm.jenis === 'biasa'"
                class="absolute top-3 right-3"
              >
                <span
                  class="material-symbols-outlined text-indigo-600 text-[20px]"
                  >check_circle</span
                >
              </div>
              <div
                class="size-16 rounded-xl flex items-center justify-center transition-colors"
                :class="
                  ttdForm.jenis === 'biasa'
                    ? 'bg-indigo-100 text-indigo-600'
                    : 'bg-gray-100 text-gray-500'
                "
              >
                <span class="material-symbols-outlined text-3xl">draw</span>
              </div>
              <div>
                <h3
                  class="font-bold text-sm"
                  :class="
                    ttdForm.jenis === 'biasa'
                      ? 'text-indigo-700'
                      : 'text-text-main'
                  "
                >
                  TTD Biasa + Stempel
                </h3>
                <p class="text-xs text-text-secondary mt-1">
                  Menggunakan gambar tanda tangan dan stempel/cap pada dokumen
                </p>
              </div>
            </button>

            <!-- QR Code -->
            <button
              @click="ttdForm.jenis = 'qr'"
              class="relative flex flex-col items-center gap-3 p-6 rounded-xl border-2 transition-all text-center cursor-pointer"
              :class="
                ttdForm.jenis === 'qr'
                  ? 'border-indigo-500 bg-indigo-50/50 dark:bg-indigo-900/10 shadow-md'
                  : 'border-border-light hover:border-indigo-300 hover:bg-indigo-50/20'
              "
            >
              <div v-if="ttdForm.jenis === 'qr'" class="absolute top-3 right-3">
                <span
                  class="material-symbols-outlined text-indigo-600 text-[20px]"
                  >check_circle</span
                >
              </div>
              <div
                class="size-16 rounded-xl flex items-center justify-center transition-colors"
                :class="
                  ttdForm.jenis === 'qr'
                    ? 'bg-indigo-100 text-indigo-600'
                    : 'bg-gray-100 text-gray-500'
                "
              >
                <span class="material-symbols-outlined text-3xl"
                  >qr_code_2</span
                >
              </div>
              <div>
                <h3
                  class="font-bold text-sm"
                  :class="
                    ttdForm.jenis === 'qr'
                      ? 'text-indigo-700'
                      : 'text-text-main'
                  "
                >
                  QR Code
                </h3>
                <p class="text-xs text-text-secondary mt-1">
                  Menggunakan QR code verifikasi digital pada dokumen
                </p>
              </div>
            </button>
          </div>

          <!-- Info -->
          <div
            class="flex items-start gap-3 p-3 rounded-lg bg-blue-50/50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-800"
          >
            <span
              class="material-symbols-outlined text-blue-500 text-[18px] mt-0.5"
              >info</span
            >
            <p class="text-xs text-blue-700 dark:text-blue-400">
              Pengaturan ini berlaku untuk semua dokumen yang dihasilkan sistem
              (SK Tugas, Berita Acara, SK Penguji, dll).
              <span v-if="ttdForm.jenis === 'qr'">
                QR code akan berisi link verifikasi yang dapat dipindai untuk
                memastikan keaslian dokumen.
              </span>
            </p>
          </div>

          <!-- Save -->
          <div
            class="flex items-center justify-between pt-2 border-t border-border-light"
          >
            <p class="text-xs text-text-secondary">
              Saat ini menggunakan:
              <strong class="text-text-main">{{
                ttdForm.jenis === "qr" ? "QR Code" : "TTD Biasa + Stempel"
              }}</strong>
            </p>
            <button
              @click="saveTtd"
              :disabled="savingTtd"
              class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-6 py-2.5 rounded-lg transition-all shadow-sm text-sm disabled:opacity-50"
            >
              <span
                v-if="savingTtd"
                class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"
              ></span>
              <span v-else class="material-symbols-outlined text-[18px]"
                >save</span
              >
              {{ savingTtd ? "Menyimpan..." : "Simpan Perubahan" }}
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- Panduan & Template -->
    <section
      class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden"
    >
      <div class="p-5 border-b border-border-light flex items-center gap-3">
        <div class="p-2 bg-violet-500/10 rounded-lg text-violet-600">
          <span class="material-symbols-outlined">menu_book</span>
        </div>
        <div>
          <h2 class="text-lg font-bold text-text-main">Panduan & Template</h2>
          <p class="text-sm text-text-secondary">
            Upload dokumen panduan dan template untuk mahasiswa, dosen, dan
            staff
          </p>
        </div>
      </div>
      <div class="p-6">
        <!-- Tabs -->
        <div class="flex gap-1 mb-6 bg-sidebar-light rounded-lg p-1">
          <button
            v-for="tab in panduanTabs"
            :key="tab.key"
            @click="activePanduanTab = tab.key"
            class="flex-1 py-2 px-4 rounded-md text-sm font-bold transition-all"
            :class="
              activePanduanTab === tab.key
                ? 'bg-white dark:bg-surface-light text-violet-600 shadow-sm'
                : 'text-text-secondary hover:text-text-main'
            "
          >
            {{ tab.label }}
          </button>
        </div>

        <!-- Loading -->
        <div
          v-if="loadingPanduan"
          class="flex items-center gap-3 text-text-secondary py-4"
        >
          <span
            class="animate-spin rounded-full h-5 w-5 border-b-2 border-violet-500"
          ></span>
          <span class="text-sm">Memuat panduan...</span>
        </div>

        <div v-else class="flex flex-col gap-4">
          <!-- Upload -->
          <div
            class="flex items-center gap-3 p-4 border-2 border-dashed border-border-light rounded-xl bg-sidebar-light/30"
          >
            <span class="material-symbols-outlined text-violet-500 text-2xl"
              >upload_file</span
            >
            <div class="flex-1">
              <input
                ref="panduanFileInput"
                type="file"
                accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx"
                class="text-sm text-text-main file:mr-3 file:px-4 file:py-2 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-violet-50 file:text-violet-600 hover:file:bg-violet-100 file:cursor-pointer file:transition-colors"
              />
            </div>
            <button
              @click="uploadPanduan"
              :disabled="uploadingPanduan"
              class="inline-flex items-center gap-2 bg-violet-600 hover:bg-violet-700 text-white font-bold px-5 py-2.5 rounded-lg transition-all text-sm disabled:opacity-50 shadow-sm"
            >
              <span
                v-if="uploadingPanduan"
                class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"
              ></span>
              <span v-else class="material-symbols-outlined text-[18px]"
                >cloud_upload</span
              >
              {{ uploadingPanduan ? "Mengunggah..." : "Unggah" }}
            </button>
          </div>

          <!-- File list -->
          <div v-if="panduanFiles.length === 0" class="text-center py-8">
            <span
              class="material-symbols-outlined text-4xl text-text-secondary opacity-40 mb-2 block"
              >folder_open</span
            >
            <p class="text-sm text-text-secondary">
              Belum ada file panduan untuk kategori ini
            </p>
          </div>
          <div v-else class="space-y-2">
            <div
              v-for="file in panduanFiles"
              :key="file.id"
              class="flex items-center gap-3 p-3 border border-border-light rounded-lg hover:bg-sidebar-light/30 transition-colors"
            >
              <div
                class="size-10 rounded-lg flex items-center justify-center bg-red-50 text-red-500 shrink-0"
              >
                <span class="material-symbols-outlined text-xl"
                  >description</span
                >
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-text-main truncate">
                  {{ file.nama_file }}
                </p>
                <p class="text-xs text-text-secondary">
                  {{ formatFileSize(file.ukuran) }} ·
                  {{ formatDate(file.created_at) }}
                </p>
              </div>
              <button
                @click="confirmDeletePanduan(file)"
                class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                title="Hapus"
              >
                <span class="material-symbols-outlined text-[18px]"
                  >delete</span
                >
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Delete Confirmation Modal -->
    <Transition name="modal-fade">
      <div
        v-if="deleteModal.show"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
      >
        <div
          class="absolute inset-0 bg-black/50 backdrop-blur-sm"
          @click="deleteModal.show = false"
        ></div>
        <div
          class="relative bg-surface-light rounded-xl shadow-xl border border-border-light w-full max-w-sm p-6 flex flex-col gap-4"
        >
          <div class="flex items-center gap-3">
            <div class="p-2 bg-red-50 rounded-lg text-red-500">
              <span class="material-symbols-outlined">warning</span>
            </div>
            <div>
              <h3 class="text-lg font-bold text-text-main">Hapus File</h3>
              <p class="text-xs text-text-secondary">
                {{ deleteModal.fileName }}
              </p>
            </div>
          </div>
          <p class="text-sm text-text-secondary">
            Apakah Anda yakin ingin menghapus file ini? Tindakan ini tidak dapat
            dibatalkan.
          </p>
          <div class="flex justify-end gap-3">
            <button
              @click="deleteModal.show = false"
              class="px-4 py-2 rounded-lg text-text-secondary font-bold hover:bg-sidebar-light transition-colors text-sm"
            >
              Batal
            </button>
            <button
              @click="executeDeletePanduan"
              :disabled="deletingPanduan"
              class="px-4 py-2 rounded-lg bg-red-600 text-white font-bold hover:bg-red-700 transition-colors text-sm disabled:opacity-50 flex items-center gap-2"
            >
              <span
                v-if="deletingPanduan"
                class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"
              ></span>
              Hapus
            </button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Toast -->
    <Transition name="toast-slide">
      <div
        v-if="toast.show"
        class="fixed bottom-6 right-6 z-50 flex items-center gap-3 px-5 py-3.5 rounded-xl shadow-lg text-white text-sm font-bold"
        :class="
          toast.type === 'success'
            ? 'bg-green-600 shadow-green-600/30'
            : 'bg-red-600 shadow-red-600/30'
        "
      >
        <span class="material-symbols-outlined text-[20px]">{{
          toast.type === "success" ? "check_circle" : "error"
        }}</span>
        {{ toast.message }}
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from "vue";
import adminService from "../../services/adminService";

// --- Syarat Bimbingan ---
const loadingConfig = ref(true);
const savingConfig = ref(false);
const syaratForm = ref({ pembimbing_1: 8, pembimbing_2: 4 });

// --- Kuota ---
const loadingKuota = ref(true);
const savingKuota = ref(false);
const kuotaForm = ref({ kuota: 10 });

// --- Tanggal Penting ---
const loadingDates = ref(true);
const savingDates = ref(false);
const dateEntries = ref([]);

// --- Jenis TTD ---
const loadingTtd = ref(true);
const savingTtd = ref(false);
const ttdForm = ref({ jenis: "biasa" });

// --- Panduan ---
const activePanduanTab = ref("mahasiswa");
const loadingPanduan = ref(true);
const uploadingPanduan = ref(false);
const panduanFiles = ref([]);
const panduanFileInput = ref(null);
const deletingPanduan = ref(false);
const deleteModal = ref({ show: false, id: null, fileName: "" });

const panduanTabs = [
  { key: "mahasiswa", label: "Mahasiswa" },
  { key: "dosen", label: "Dosen" },
  { key: "staff", label: "Staff" },
];

const toast = ref({ show: false, message: "", type: "success" });

// --- Fetch functions ---
const fetchConfig = async () => {
  try {
    loadingConfig.value = true;
    const res = await adminService.getSyaratBimbingan();
    if (res.success && res.data) {
      syaratForm.value = {
        pembimbing_1: res.data.pembimbing_1 ?? 8,
        pembimbing_2: res.data.pembimbing_2 ?? 4,
      };
    }
  } catch (err) {
    console.error("Failed to fetch config:", err);
  } finally {
    loadingConfig.value = false;
  }
};

const saveSyarat = async () => {
  savingConfig.value = true;
  try {
    const res = await adminService.saveSyaratBimbingan(syaratForm.value);
    if (res.success) {
      showToast("Konfigurasi berhasil disimpan!", "success");
    }
  } catch (err) {
    console.error("Failed to save config:", err);
    showToast(
      err.response?.data?.message || "Gagal menyimpan konfigurasi.",
      "error",
    );
  } finally {
    savingConfig.value = false;
  }
};

const fetchKuota = async () => {
  try {
    loadingKuota.value = true;
    const res = await adminService.getKuotaBimbingan();
    if (res.success && res.data) {
      kuotaForm.value = {
        kuota: res.data.kuota ?? 10,
      };
    }
  } catch (err) {
    console.error("Failed to fetch kuota config:", err);
  } finally {
    loadingKuota.value = false;
  }
};

const saveKuota = async () => {
  savingKuota.value = true;
  try {
    const res = await adminService.saveKuotaBimbingan(kuotaForm.value);
    if (res.success) {
      showToast("Kuota bimbingan berhasil disimpan!", "success");
    }
  } catch (err) {
    console.error("Failed to save kuota:", err);
    showToast(
      err.response?.data?.message || "Gagal menyimpan kuota bimbingan.",
      "error",
    );
  } finally {
    savingKuota.value = false;
  }
};

// --- Tanggal Penting ---
const fetchDates = async () => {
  try {
    loadingDates.value = true;
    const res = await adminService.getTanggalPenting();
    if (res.success && Array.isArray(res.data)) {
      dateEntries.value = res.data.map((d) => ({ ...d }));
    }
  } catch (err) {
    console.error("Failed to fetch dates:", err);
  } finally {
    loadingDates.value = false;
  }
};

const addDateEntry = () => {
  dateEntries.value.push({ label: "", tanggal: "" });
};

const removeDateEntry = (idx) => {
  dateEntries.value.splice(idx, 1);
};

const saveDates = async () => {
  // Validate
  const valid = dateEntries.value.every(
    (d) => d.label.trim() && d.tanggal.trim(),
  );
  if (!valid) {
    showToast("Semua baris harus memiliki keterangan dan tanggal.", "error");
    return;
  }
  savingDates.value = true;
  try {
    const res = await adminService.saveTanggalPenting({
      dates: dateEntries.value,
    });
    if (res.success) {
      showToast("Tanggal penting berhasil disimpan!", "success");
    }
  } catch (err) {
    console.error("Failed to save dates:", err);
    showToast(
      err.response?.data?.message || "Gagal menyimpan tanggal penting.",
      "error",
    );
  } finally {
    savingDates.value = false;
  }
};

// --- Jenis TTD ---
const fetchTtd = async () => {
  try {
    loadingTtd.value = true;
    const res = await adminService.getJenisTtd();
    if (res.success && res.data) {
      ttdForm.value = { jenis: res.data.jenis || "biasa" };
    }
  } catch (err) {
    console.error("Failed to fetch jenis TTD:", err);
  } finally {
    loadingTtd.value = false;
  }
};

const saveTtd = async () => {
  savingTtd.value = true;
  try {
    const res = await adminService.saveJenisTtd(ttdForm.value);
    if (res.success) {
      showToast("Jenis tanda tangan berhasil disimpan!", "success");
    }
  } catch (err) {
    console.error("Failed to save jenis TTD:", err);
    showToast(
      err.response?.data?.message || "Gagal menyimpan jenis tanda tangan.",
      "error",
    );
  } finally {
    savingTtd.value = false;
  }
};

// --- Panduan ---
const fetchPanduan = async () => {
  try {
    loadingPanduan.value = true;
    const res = await adminService.getPanduanList(activePanduanTab.value);
    if (res.success) {
      panduanFiles.value = res.data || [];
    }
  } catch (err) {
    console.error("Failed to fetch panduan:", err);
  } finally {
    loadingPanduan.value = false;
  }
};

watch(activePanduanTab, () => {
  fetchPanduan();
});

const uploadPanduan = async () => {
  const fileInput = panduanFileInput.value;
  if (!fileInput?.files?.length) {
    showToast("Pilih file terlebih dahulu.", "error");
    return;
  }
  uploadingPanduan.value = true;
  try {
    const formData = new FormData();
    formData.append("file", fileInput.files[0]);
    const res = await adminService.uploadPanduan(
      activePanduanTab.value,
      formData,
    );
    if (res.success) {
      showToast("File berhasil diunggah!", "success");
      fileInput.value = "";
      fetchPanduan();
    }
  } catch (err) {
    console.error("Failed to upload panduan:", err);
    showToast(err.response?.data?.message || "Gagal mengunggah file.", "error");
  } finally {
    uploadingPanduan.value = false;
  }
};

const confirmDeletePanduan = (file) => {
  deleteModal.value = { show: true, id: file.id, fileName: file.nama_file };
};

const executeDeletePanduan = async () => {
  deletingPanduan.value = true;
  try {
    const res = await adminService.deletePanduan(deleteModal.value.id);
    if (res.success) {
      showToast("File berhasil dihapus.", "success");
      deleteModal.value.show = false;
      fetchPanduan();
    }
  } catch (err) {
    console.error("Failed to delete panduan:", err);
    showToast(err.response?.data?.message || "Gagal menghapus file.", "error");
  } finally {
    deletingPanduan.value = false;
  }
};

// --- Helpers ---
const formatFileSize = (bytes) => {
  if (!bytes) return "0 B";
  const sizes = ["B", "KB", "MB", "GB"];
  const i = Math.floor(Math.log(bytes) / Math.log(1024));
  return (bytes / Math.pow(1024, i)).toFixed(1) + " " + sizes[i];
};

const formatDate = (dateStr) => {
  if (!dateStr) return "-";
  return new Date(dateStr).toLocaleDateString("id-ID", {
    day: "numeric",
    month: "long",
    year: "numeric",
  });
};

const showToast = (message, type = "success") => {
  toast.value = { show: true, message, type };
  setTimeout(() => {
    toast.value.show = false;
  }, 3000);
};

onMounted(() => {
  fetchConfig();
  fetchKuota();
  fetchDates();
  fetchTtd();
  fetchPanduan();
});
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
.modal-fade-enter-active {
  transition: all 0.2s ease-out;
}
.modal-fade-leave-active {
  transition: all 0.15s ease-in;
}
.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}
</style>
