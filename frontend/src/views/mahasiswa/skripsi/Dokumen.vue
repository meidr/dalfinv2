<template>
  <div class="flex flex-col gap-8 animate-fade-in">
    <!-- ===== SECTION 1: Berkas Skripsi (Chapter Uploads) ===== -->
    <div
      class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden"
    >
      <div
        class="px-6 py-4 border-b border-border-light bg-sidebar-light flex flex-col md:flex-row justify-between items-start md:items-center gap-3"
      >
        <div>
          <h3 class="font-bold text-lg text-text-main">Berkas Skripsi</h3>
          <p class="text-xs text-text-secondary">
            Upload berkas bab skripsi Anda. Format: PDF, DOC, DOCX (maks 10MB).
          </p>
        </div>
        <div class="flex items-center gap-2">
          <select
            v-model="berkasFilter"
            @change="berkasPage = 1"
            class="px-3 py-2 rounded-lg border border-border-light bg-background-light text-xs font-medium focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none"
          >
            <option value="">Semua Jenis</option>
            <option value="proposal">Proposal</option>
            <option value="bab1">Bab 1</option>
            <option value="bab2">Bab 2</option>
            <option value="bab3">Bab 3</option>
            <option value="bab4">Bab 4</option>
            <option value="bab5">Bab 5</option>
            <option value="full_draft">Draft Lengkap</option>
            <option value="final">Naskah Final</option>
            <option value="revisi">Revisi</option>
            <option value="revisi_proposal">Revisi Proposal</option>
            <option value="lainnya">Lainnya</option>
          </select>
          <button
            v-if="!isDosen && isActive"
            @click="showUploadModal = true"
            class="flex items-center gap-2 bg-primary hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition-all shrink-0"
          >
            <span class="material-symbols-outlined text-[18px]"
              >upload_file</span
            >
            Upload Berkas
          </button>
        </div>
      </div>

      <!-- Chapter Grid -->
      <div class="p-6">
        <div v-if="filteredDokumen.length">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div
              v-for="doc in paginatedDokumen"
              :key="doc.id"
              class="flex items-start gap-4 p-4 border border-border-light rounded-xl hover:border-primary/30 transition-colors group bg-white dark:bg-gray-900"
            >
              <div
                class="size-11 rounded-lg flex items-center justify-center shrink-0"
                :class="getDocIconClass(doc.jenis)"
              >
                <span class="material-symbols-outlined text-[20px]">{{
                  getDocIcon(doc.jenis)
                }}</span>
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-2">
                  <div class="min-w-0">
                    <h4 class="font-bold text-sm text-primary truncate">
                      {{ getJenisLabel(doc.jenis) }}
                    </h4>
                    <p class="text-[11px] text-text-secondary">
                      Versi {{ doc.versi || 1 }} •
                      {{ formatFileSize(doc.ukuran) }} •
                      {{ formatDate(doc.created_at) }}
                    </p>
                    <p
                      v-if="doc.catatan"
                      class="text-[11px] text-text-secondary mt-0.5 truncate"
                    >
                      {{ doc.catatan }}
                    </p>
                  </div>
                  <span
                    class="px-2 py-0.5 rounded-full text-[10px] font-bold shrink-0"
                    :class="getStatusClass(doc.status)"
                  >
                    {{ getStatusLabel(doc.status) }}
                  </span>
                </div>
                <div class="flex items-center gap-2 mt-2">
                  <a
                    :href="getFileUrl(doc.path)"
                    target="_blank"
                    class="inline-flex items-center gap-1 text-xs font-bold text-primary hover:text-blue-600 transition-colors"
                  >
                    <span class="material-symbols-outlined text-[14px]"
                      >visibility</span
                    >
                    Lihat
                  </a>
                  <a
                    :href="getFileUrl(doc.path)"
                    :download="doc.nama_file"
                    class="inline-flex items-center gap-1 text-xs font-bold text-text-secondary hover:text-primary transition-colors"
                  >
                    <span class="material-symbols-outlined text-[14px]"
                      >download</span
                    >
                    Unduh
                  </a>
                  <button
                    v-if="doc.status === 'pending' && !isDosen && isActive"
                    @click="confirmDelete(doc)"
                    class="inline-flex items-center gap-1 text-xs font-bold text-red-500 hover:text-red-600 transition-colors ml-auto"
                  >
                    <span class="material-symbols-outlined text-[14px]"
                      >delete</span
                    >
                    Hapus
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Berkas Pagination -->
          <div
            v-if="berkasTotalPages > 1"
            class="flex flex-col sm:flex-row items-center justify-between gap-3 mt-4 pt-3 border-t border-border-light"
          >
            <span class="text-xs text-text-secondary">
              Menampilkan
              <span class="font-bold text-text-main">{{
                berkasStartIdx + 1
              }}</span>
              –
              <span class="font-bold text-text-main">{{ berkasEndIdx }}</span>
              dari
              <span class="font-bold text-text-main">{{
                filteredDokumen.length
              }}</span>
              berkas
            </span>
            <div class="flex items-center gap-1">
              <button
                @click="berkasPage--"
                :disabled="berkasPage === 1"
                class="size-8 rounded-lg flex items-center justify-center text-sm transition-colors border border-border-light hover:bg-sidebar-light disabled:opacity-30 disabled:cursor-not-allowed"
              >
                <span class="material-symbols-outlined text-[16px]"
                  >chevron_left</span
                >
              </button>
              <button
                v-for="page in berkasVisiblePages"
                :key="page"
                @click="berkasPage = page"
                class="size-8 rounded-lg flex items-center justify-center text-xs font-bold transition-colors"
                :class="
                  page === berkasPage
                    ? 'bg-primary text-white shadow-sm'
                    : 'border border-border-light hover:bg-sidebar-light text-text-main'
                "
              >
                {{ page }}
              </button>
              <button
                @click="berkasPage++"
                :disabled="berkasPage === berkasTotalPages"
                class="size-8 rounded-lg flex items-center justify-center text-sm transition-colors border border-border-light hover:bg-sidebar-light disabled:opacity-30 disabled:cursor-not-allowed"
              >
                <span class="material-symbols-outlined text-[16px]"
                  >chevron_right</span
                >
              </button>
            </div>
          </div>
        </div>

        <!-- Empty state -->
        <div
          v-else
          class="py-10 flex flex-col items-center justify-center gap-3 text-center"
        >
          <span
            class="material-symbols-outlined text-5xl text-text-secondary opacity-40"
            >folder_open</span
          >
          <h3 class="text-lg font-bold text-text-main">
            {{
              berkasFilter
                ? "Tidak Ada Berkas " + getJenisLabel(berkasFilter)
                : "Belum Ada Berkas Skripsi"
            }}
          </h3>
          <p class="text-text-secondary text-sm max-w-md">
            {{
              berkasFilter
                ? "Coba ubah filter jenis berkas."
                : "Upload berkas bab skripsi Anda untuk melacak progres penulisan."
            }}
          </p>
        </div>
      </div>
    </div>

    <!-- ===== SECTION 2: Bukti Bimbingan (From bimbingan logs) ===== -->
    <div
      class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden"
    >
      <div class="px-6 py-4 border-b border-border-light bg-sidebar-light">
        <h3 class="font-bold text-lg text-text-main">Bukti Bimbingan</h3>
        <p class="text-xs text-text-secondary">
          File bukti yang diunggah dari log bimbingan.
        </p>
      </div>

      <div v-if="bimbinganFiles.length" class="p-6 flex flex-col gap-3">
        <div
          v-for="b in bimbinganFiles"
          :key="b.id"
          class="flex items-center gap-4 p-3 border border-border-light rounded-lg hover:border-primary/30 transition-colors bg-white dark:bg-gray-900"
        >
          <div
            class="size-10 rounded-lg bg-green-50 dark:bg-green-900/20 text-green-600 flex items-center justify-center shrink-0"
          >
            <span class="material-symbols-outlined text-[18px]"
              >attach_file</span
            >
          </div>
          <div class="flex-1 min-w-0">
            <h4 class="font-bold text-sm text-primary truncate">
              {{ b.topik }}
            </h4>
            <p class="text-[11px] text-text-secondary">
              {{ formatDate(b.tanggal) }} •
              {{ b.dosen?.full_name || b.dosen?.nama || "Dosen" }}
            </p>
          </div>
          <div class="flex items-center gap-2 shrink-0">
            <span
              class="px-2 py-0.5 rounded-full text-[10px] font-bold"
              :class="getBimbinganStatusClass(b.status)"
            >
              {{ getBimbinganStatusLabel(b.status) }}
            </span>
            <a
              :href="getFileUrl(b.file_bukti)"
              target="_blank"
              class="size-8 flex items-center justify-center rounded-lg border border-border-light text-text-secondary hover:text-primary hover:border-primary transition-all"
              title="Lihat File"
            >
              <span class="material-symbols-outlined text-[16px]"
                >visibility</span
              >
            </a>
            <a
              :href="getFileUrl(b.file_bukti)"
              :download="true"
              class="size-8 flex items-center justify-center rounded-lg border border-border-light text-text-secondary hover:text-green-600 hover:border-green-400 transition-all"
              title="Unduh File"
            >
              <span class="material-symbols-outlined text-[16px]"
                >download</span
              >
            </a>
          </div>
        </div>
      </div>

      <div
        v-else
        class="p-10 flex flex-col items-center justify-center gap-2 text-center"
      >
        <span
          class="material-symbols-outlined text-4xl text-text-secondary opacity-40"
          >cloud_off</span
        >
        <p class="text-sm text-text-secondary">
          Belum ada file bukti bimbingan yang diunggah.
        </p>
      </div>
    </div>

    <!-- ===== SECTION 3: Dokumen Resmi (Official PDFs) ===== -->
    <div
      class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden"
    >
      <div class="px-6 py-4 border-b border-border-light bg-sidebar-light">
        <h3 class="font-bold text-lg text-text-main">Dokumen Resmi</h3>
        <p class="text-xs text-text-secondary">
          Surat keputusan dan dokumen resmi. Klik unduh untuk mendapatkan PDF
          terbaru.
        </p>
      </div>
      <div class="p-3 flex flex-col gap-1">
        <div
          v-for="doc in officialDocuments"
          :key="doc.type"
          class="flex items-center justify-between p-4 hover:bg-sidebar-light rounded-lg transition-colors group"
        >
          <div class="flex items-center gap-4">
            <div
              class="size-10 rounded-lg flex items-center justify-center"
              :class="doc.iconClass"
            >
              <span class="material-symbols-outlined">{{ doc.icon }}</span>
            </div>
            <div>
              <h4
                class="font-bold text-sm text-text-main group-hover:text-primary transition-colors"
              >
                {{ doc.label }}
              </h4>
              <p class="text-xs text-text-secondary">
                {{ doc.subtitle }}
              </p>
            </div>
          </div>
          <div v-if="doc.available" class="flex items-center gap-2">
            <button
              @click="previewPdf(doc.type)"
              :disabled="previewing === doc.type"
              class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-bold transition-all border border-border-light text-text-secondary hover:text-primary hover:border-primary disabled:opacity-50"
            >
              <span
                v-if="previewing === doc.type"
                class="animate-spin rounded-full h-3.5 w-3.5 border-b-2 border-current"
              ></span>
              <span v-else class="material-symbols-outlined text-[16px]"
                >visibility</span
              >
              {{ previewing === doc.type ? "Memuat..." : "Lihat" }}
            </button>
            <button
              @click="downloadPdf(doc.type)"
              :disabled="downloading === doc.type"
              class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-bold transition-all shadow-sm border border-primary text-primary hover:bg-primary hover:text-white disabled:opacity-50"
            >
              <span
                v-if="downloading === doc.type"
                class="animate-spin rounded-full h-3.5 w-3.5 border-b-2 border-current"
              ></span>
              <span v-else class="material-symbols-outlined text-[16px]"
                >download</span
              >
              {{ downloading === doc.type ? "Mengunduh..." : "Unduh" }}
            </button>
          </div>
          <span v-else class="text-xs text-text-secondary italic"
            >Belum tersedia</span
          >
        </div>

        <!-- Empty state -->
        <div
          v-if="officialDocuments.length === 0"
          class="p-6 text-center text-text-secondary"
        >
          <span class="material-symbols-outlined text-4xl mb-2 block opacity-40"
            >description</span
          >
          <p class="text-sm">Belum ada dokumen resmi yang tersedia.</p>
        </div>
      </div>
    </div>

    <!-- ===== MODAL: Upload Berkas ===== -->
    <div
      v-if="showUploadModal"
      class="fixed inset-0 z-50 flex items-center justify-center p-4"
    >
      <div
        class="absolute inset-0 bg-black/50 backdrop-blur-sm"
        @click="closeUpload"
      ></div>
      <div
        class="relative bg-surface-light rounded-xl shadow-xl border border-border-light w-full max-w-md"
      >
        <div
          class="p-6 border-b border-border-light flex justify-between items-center"
        >
          <div class="flex items-center gap-3">
            <div class="p-2 bg-primary/10 rounded-lg text-primary">
              <span class="material-symbols-outlined">upload_file</span>
            </div>
            <h3 class="text-lg font-bold text-text-main">
              Upload Berkas Skripsi
            </h3>
          </div>
          <button
            @click="closeUpload"
            class="p-1 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors"
          >
            <span class="material-symbols-outlined text-text-secondary"
              >close</span
            >
          </button>
        </div>
        <form @submit.prevent="submitUpload" class="p-6 flex flex-col gap-5">
          <div
            v-if="uploadError"
            class="flex gap-2 bg-red-50 dark:bg-red-900/20 p-3 rounded-lg border border-red-100 dark:border-red-800 text-sm text-red-700 dark:text-red-300"
          >
            <span class="material-symbols-outlined text-red-500 text-[18px]"
              >error</span
            >
            {{ uploadError }}
          </div>

          <!-- Jenis Berkas -->
          <div class="flex flex-col gap-2">
            <label class="text-sm font-bold text-text-main">Jenis Berkas</label>
            <select
              v-model="uploadForm.jenis"
              required
              class="px-4 py-2.5 rounded-lg border border-border-light bg-background-light focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm"
            >
              <option value="" disabled>Pilih jenis berkas</option>
              <option
                v-for="opt in jenisOptions"
                :key="opt.value"
                :value="opt.value"
              >
                {{ opt.label }}
              </option>
            </select>
          </div>

          <!-- File -->
          <div class="flex flex-col gap-2">
            <label class="text-sm font-bold text-text-main"
              >File (PDF, DOC, DOCX — maks 10MB)</label
            >
            <input
              ref="fileInputRef"
              type="file"
              @change="onUploadFile"
              accept=".pdf,.doc,.docx"
              required
              class="text-sm file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer"
            />
            <p
              v-if="uploadForm.file"
              class="text-xs text-green-600 flex items-center gap-1"
            >
              <span class="material-symbols-outlined text-[14px]"
                >check_circle</span
              >
              {{ uploadForm.file.name }} ({{
                formatFileSize(uploadForm.file.size)
              }})
            </p>
          </div>

          <!-- Catatan -->
          <div class="flex flex-col gap-2">
            <label class="text-sm font-bold text-text-main"
              >Catatan
              <span class="font-normal text-text-secondary"
                >(opsional)</span
              ></label
            >
            <input
              v-model="uploadForm.catatan"
              type="text"
              placeholder="Contoh: Revisi sesuai masukan dosen"
              class="px-4 py-2.5 rounded-lg border border-border-light bg-background-light focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm"
            />
          </div>

          <div class="flex justify-end gap-3 pt-4 border-t border-border-light">
            <button
              type="button"
              @click="closeUpload"
              class="px-5 py-2.5 rounded-lg text-text-secondary font-bold hover:bg-sidebar-light transition-colors text-sm"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="uploading"
              class="px-5 py-2.5 rounded-lg bg-primary text-white font-bold hover:bg-blue-600 transition-colors shadow-sm text-sm flex items-center gap-2 disabled:opacity-50"
            >
              <span
                v-if="uploading"
                class="material-symbols-outlined text-[18px] animate-spin"
                >progress_activity</span
              >
              <span v-else class="material-symbols-outlined text-[18px]"
                >cloud_upload</span
              >
              {{ uploading ? "Mengunggah..." : "Upload" }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- ===== MODAL: Confirm Delete ===== -->
    <div
      v-if="deleteTarget"
      class="fixed inset-0 z-50 flex items-center justify-center p-4"
    >
      <div
        class="absolute inset-0 bg-black/50 backdrop-blur-sm"
        @click="deleteTarget = null"
      ></div>
      <div
        class="relative bg-surface-light rounded-xl shadow-xl border border-border-light w-full max-w-sm p-6 flex flex-col gap-4"
      >
        <div class="flex items-center gap-3">
          <div
            class="p-2 bg-red-100 dark:bg-red-900/30 rounded-lg text-red-600"
          >
            <span class="material-symbols-outlined">delete</span>
          </div>
          <h3 class="text-lg font-bold text-text-main">Hapus Berkas?</h3>
        </div>
        <p class="text-sm text-text-secondary">
          Apakah Anda yakin ingin menghapus berkas
          <strong>{{ getJenisLabel(deleteTarget.jenis) }}</strong
          >? Tindakan ini tidak dapat dibatalkan.
        </p>
        <div class="flex justify-end gap-3 pt-2 border-t border-border-light">
          <button
            @click="deleteTarget = null"
            class="px-4 py-2 rounded-lg text-text-secondary font-bold hover:bg-sidebar-light transition-colors text-sm"
          >
            Batal
          </button>
          <button
            v-if="!isDosen"
            @click="doDelete"
            :disabled="deleting"
            class="px-4 py-2 rounded-lg bg-red-600 text-white font-bold hover:bg-red-700 transition-colors text-sm flex items-center gap-2 disabled:opacity-50"
          >
            <span
              v-if="deleting"
              class="material-symbols-outlined text-[16px] animate-spin"
              >progress_activity</span
            >
            {{ deleting ? "Menghapus..." : "Ya, Hapus" }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { inject, computed, ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import mahasiswaService from "../../../services/mahasiswaService";
import dosenService from "../../../services/dosenService";

const route = useRoute();
const skripsi = inject("skripsi");

// Auth store for module settings
import { useAuthStore } from "../../../stores/auth";
const authStore = useAuthStore();

// Detect context: dosen or mahasiswa
const isDosen = computed(() => route.path.startsWith("/dosen"));
const isActive = computed(() => skripsi.value?.is_active !== false);
const skripsiId = computed(() => route.params.id || skripsi.value?.id);

// ===== Computed data =====
const dokumenList = computed(() => {
  return (skripsi.value?.dokumen || []).slice().sort((a, b) => {
    const order = [
      "proposal",
      "bab1",
      "bab2",
      "bab3",
      "bab4",
      "bab5",
      "full_draft",
      "final",
      "revisi",
      "revisi_proposal",
      "lainnya",
    ];
    return order.indexOf(a.jenis) - order.indexOf(b.jenis);
  });
});

// ===== Berkas Filter & Pagination =====
const berkasFilter = ref("");
const berkasPage = ref(1);
const berkasPerPage = 4;

const filteredDokumen = computed(() => {
  if (!berkasFilter.value) return dokumenList.value;
  return dokumenList.value.filter((d) => d.jenis === berkasFilter.value);
});

const berkasTotalPages = computed(() =>
  Math.ceil(filteredDokumen.value.length / berkasPerPage),
);
const berkasStartIdx = computed(() => (berkasPage.value - 1) * berkasPerPage);
const berkasEndIdx = computed(() =>
  Math.min(berkasStartIdx.value + berkasPerPage, filteredDokumen.value.length),
);
const paginatedDokumen = computed(() =>
  filteredDokumen.value.slice(berkasStartIdx.value, berkasEndIdx.value),
);
const berkasVisiblePages = computed(() => {
  const pages = [];
  const total = berkasTotalPages.value;
  const cur = berkasPage.value;
  let start = Math.max(1, cur - 2);
  let end = Math.min(total, start + 4);
  if (end - start < 4) start = Math.max(1, end - 4);
  for (let i = start; i <= end; i++) pages.push(i);
  return pages;
});

const bimbinganFiles = computed(() => {
  return (skripsi.value?.bimbingan || [])
    .filter((b) => b.file_bukti)
    .sort((a, b) => new Date(b.tanggal) - new Date(a.tanggal));
});

// ===== Official Documents (Dynamic) =====
const officialDocuments = computed(() => {
  const docs = [];
  const s = skripsi.value;
  if (!s) return docs;

  // 1. SK Pembimbing (SK Tugas)
  const latestSkTugas = dokumenList.value
    .filter((d) => d.jenis === "sk_tugas")
    .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))[0];
  const skTugasAvailable = !!s.sk_tugas || !!latestSkTugas;
  let skTugasSubtitle = "Belum diterbitkan";
  if (latestSkTugas) {
    skTugasSubtitle =
      "Versi " +
      (latestSkTugas.versi || 1) +
      " • " +
      formatFileSize(latestSkTugas.ukuran) +
      " • " +
      formatDate(latestSkTugas.created_at);
  } else if (s.sk_tugas) {
    skTugasSubtitle =
      "Diterbitkan " +
      formatDateTime(s.sk_tugas.tanggal_terbit || s.sk_tugas.created_at);
  }
  docs.push({
    type: "sk-tugas",
    label: "SK Pembimbing Skripsi",
    icon: "picture_as_pdf",
    iconClass: "bg-red-50 dark:bg-red-900/20 text-red-500",
    available: skTugasAvailable,
    subtitle: skTugasSubtitle,
  });

  // 2. Nota / Kartu Bimbingan
  docs.push({
    type: "nota-bimbingan",
    label: "Nota / Kartu Bimbingan",
    icon: "picture_as_pdf",
    iconClass: "bg-blue-50 dark:bg-blue-900/20 text-blue-500",
    available: true,
    subtitle: s.nota_bimbingan
      ? "Diterbitkan " +
        formatDateTime(
          s.nota_bimbingan.tanggal_terbit || s.nota_bimbingan.created_at,
        )
      : "Otomatis diperbarui",
  });

  // 3. Seminar Proposal documents
  const sempro = (s.seminar || []).find((sem) => sem.jenis === "sempro");
  if (sempro) {
    const hasSpPenguji = sempro.penguji && sempro.penguji.length > 0;
    docs.push({
      type: "sk-penguji-sempro",
      label: "SK Penguji Sempro",
      icon: "gavel",
      iconClass: "bg-purple-50 dark:bg-purple-900/20 text-purple-500",
      available: hasSpPenguji,
      subtitle: hasSpPenguji
        ? `${sempro.penguji.length} penguji ditugaskan`
        : "Belum ada penguji",
    });

    const hasBaSempro = !!sempro.berita_acara;
    docs.push({
      type: "berita-acara-sempro",
      label: "Berita Acara Sempro",
      icon: "article",
      iconClass: "bg-amber-50 dark:bg-amber-900/20 text-amber-600",
      available: hasBaSempro,
      subtitle: hasBaSempro
        ? "Diterbitkan " + formatDateTime(sempro.berita_acara.created_at)
        : "Belum dibuat",
    });
  }

  // 4. Seminar Hasil documents (only if module enabled)
  const semhas = authStore.semhasEnabled
    ? (s.seminar || []).find((sem) => sem.jenis === "semhas")
    : null;
  if (semhas) {
    const hasShPenguji = semhas.penguji && semhas.penguji.length > 0;
    docs.push({
      type: "sk-penguji-semhas",
      label: "SK Penguji Semhas",
      icon: "gavel",
      iconClass: "bg-orange-50 dark:bg-orange-900/20 text-orange-500",
      available: hasShPenguji,
      subtitle: hasShPenguji
        ? `${semhas.penguji.length} penguji ditugaskan`
        : "Belum ada penguji",
    });

    const hasBaSemhas = !!semhas.berita_acara;
    docs.push({
      type: "berita-acara-semhas",
      label: "Berita Acara Semhas",
      icon: "article",
      iconClass: "bg-teal-50 dark:bg-teal-900/20 text-teal-600",
      available: hasBaSemhas,
      subtitle: hasBaSemhas
        ? "Diterbitkan " + formatDateTime(semhas.berita_acara.created_at)
        : "Belum dibuat",
    });
  }

  // 5. Sidang Skripsi documents (stored in seminar table with jenis='sidang')
  const sidang = (s.seminar || []).find((sem) => sem.jenis === "sidang");
  if (sidang) {
    const hasSdPenguji = sidang.penguji && sidang.penguji.length > 0;
    docs.push({
      type: "sk-penguji-sidang",
      label: "SK Penguji Sidang",
      icon: "gavel",
      iconClass: "bg-indigo-50 dark:bg-indigo-900/20 text-indigo-500",
      available: hasSdPenguji,
      subtitle: hasSdPenguji
        ? `${sidang.penguji.length} penguji ditugaskan`
        : "Belum ada penguji",
    });

    const hasBaSidang = !!sidang.berita_acara;
    docs.push({
      type: "berita-acara-sidang",
      label: "Berita Acara Sidang",
      icon: "article",
      iconClass: "bg-rose-50 dark:bg-rose-900/20 text-rose-600",
      available: hasBaSidang,
      subtitle: hasBaSidang
        ? "Diterbitkan " + formatDateTime(sidang.berita_acara.created_at)
        : "Belum dibuat",
    });
  }

  // 6. SK Yudisium
  if (s.sk_yudisium) {
    docs.push({
      type: "sk-yudisium",
      label: "SK Yudisium",
      icon: "school",
      iconClass: "bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600",
      available: true,
      subtitle:
        "Diterbitkan " +
        formatDateTime(
          s.sk_yudisium.tanggal_terbit || s.sk_yudisium.created_at,
        ),
    });
  }

  return docs;
});

// ===== Upload Modal =====
const showUploadModal = ref(false);
const uploading = ref(false);
const uploadError = ref("");
const fileInputRef = ref(null);
const uploadForm = ref({ jenis: "", file: null, catatan: "" });

const jenisOptions = [
  { value: "proposal", label: "Proposal" },
  { value: "bab1", label: "Bab 1 - Pendahuluan" },
  { value: "bab2", label: "Bab 2 - Tinjauan Pustaka" },
  { value: "bab3", label: "Bab 3 - Metodologi Penelitian" },
  { value: "bab4", label: "Bab 4 - Hasil dan Pembahasan" },
  { value: "bab5", label: "Bab 5 - Kesimpulan dan Saran" },
  { value: "full_draft", label: "Draft Lengkap" },
  { value: "final", label: "Naskah Final" },
  { value: "revisi", label: "Revisi" },
  { value: "revisi_proposal", label: "Revisi Proposal" },
  { value: "lainnya", label: "Dokumen Lainnya" },
];

const onUploadFile = (e) => {
  uploadForm.value.file = e.target.files[0] || null;
};

const closeUpload = () => {
  showUploadModal.value = false;
  uploadError.value = "";
  uploadForm.value = { jenis: "", file: null, catatan: "" };
  if (fileInputRef.value) fileInputRef.value.value = "";
};

const submitUpload = async () => {
  uploadError.value = "";
  uploading.value = true;
  try {
    const res = await mahasiswaService.uploadDokumen(uploadForm.value);
    if (res.success) {
      closeUpload();
      await refreshSkripsi();
    } else {
      uploadError.value = res.message || "Gagal mengunggah berkas.";
    }
  } catch (err) {
    const data = err.response?.data;
    if (data?.errors) {
      const firstErr = Object.values(data.errors)[0];
      uploadError.value = Array.isArray(firstErr) ? firstErr[0] : firstErr;
    } else {
      uploadError.value = data?.message || "Terjadi kesalahan saat mengunggah.";
    }
  } finally {
    uploading.value = false;
  }
};

// ===== Delete =====
const deleteTarget = ref(null);
const deleting = ref(false);

const confirmDelete = (doc) => {
  deleteTarget.value = doc;
};

const doDelete = async () => {
  deleting.value = true;
  try {
    const res = await mahasiswaService.deleteDokumen(deleteTarget.value.id);
    if (res.success) {
      deleteTarget.value = null;
      await refreshSkripsi();
    }
  } catch (err) {
    alert(err.response?.data?.message || "Gagal menghapus berkas.");
  } finally {
    deleting.value = false;
  }
};

// ===== Official PDF Download & Preview =====
const downloading = ref(null);
const previewing = ref(null);

const getOfficialPdfBlob = async (type) => {
  const response = isDosen.value
    ? await dosenService.downloadOfficialPdf(skripsiId.value, type)
    : await mahasiswaService.downloadOfficialPdf(type);
  return new Blob([response.data], { type: "application/pdf" });
};

// Helper: find latest uploaded file for a given jenis
const getLatestUploadedFile = (jenis) => {
  return dokumenList.value
    .filter((d) => d.jenis === jenis)
    .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))[0];
};

const downloadPdf = async (type) => {
  downloading.value = type;
  try {
    // For sk-tugas, use uploaded file if available
    if (type === "sk-tugas") {
      const uploaded = getLatestUploadedFile("sk_tugas");
      if (uploaded && uploaded.path) {
        const link = document.createElement("a");
        link.href = getFileUrl(uploaded.path);
        link.download = uploaded.nama_file || "SK_Pembimbing.pdf";
        link.target = "_blank";
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        downloading.value = null;
        return;
      }
    }
    const blob = await getOfficialPdfBlob(type);
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement("a");
    link.href = url;
    const fileNames = {
      "sk-tugas": "SK_Pembimbing.pdf",
      "nota-bimbingan": "Nota_Bimbingan.pdf",
      "sk-penguji-sempro": "SK_Penguji_Sempro.pdf",
      "sk-penguji-semhas": "SK_Penguji_Semhas.pdf",
      "sk-penguji-sidang": "SK_Penguji_Sidang.pdf",
      "berita-acara-sempro": "Berita_Acara_Sempro.pdf",
      "berita-acara-semhas": "Berita_Acara_Semhas.pdf",
      "berita-acara-sidang": "Berita_Acara_Sidang.pdf",
      "sk-yudisium": "SK_Yudisium.pdf",
    };
    link.download = fileNames[type] || "Dokumen.pdf";
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    window.URL.revokeObjectURL(url);
  } catch (err) {
    const msg = err.response?.data?.message || "Gagal mengunduh dokumen.";
    alert(msg);
  } finally {
    downloading.value = null;
  }
};

const previewPdf = async (type) => {
  previewing.value = type;
  try {
    // For sk-tugas, use uploaded file if available
    if (type === "sk-tugas") {
      const uploaded = getLatestUploadedFile("sk_tugas");
      if (uploaded && uploaded.path) {
        window.open(getFileUrl(uploaded.path), "_blank");
        previewing.value = null;
        return;
      }
    }
    const blob = await getOfficialPdfBlob(type);
    const url = window.URL.createObjectURL(blob);
    window.open(url, "_blank");
  } catch (err) {
    const msg = err.response?.data?.message || "Gagal memuat dokumen.";
    alert(msg);
  } finally {
    previewing.value = null;
  }
};

// ===== Refresh =====
const refreshSkripsi = async () => {
  try {
    const res = isDosen.value
      ? await dosenService.getBimbinganDetail(skripsiId.value)
      : await mahasiswaService.getSkripsiDetail();
    if (res.success && skripsi.value) {
      Object.assign(skripsi.value, res.data);
    }
  } catch (err) {
    console.error("Failed to refresh skripsi:", err);
  }
};

// ===== Helpers =====
const getJenisLabel = (jenis) => {
  const map = {
    proposal: "Proposal",
    bab1: "Bab 1 - Pendahuluan",
    bab2: "Bab 2 - Tinjauan Pustaka",
    bab3: "Bab 3 - Metodologi",
    bab4: "Bab 4 - Hasil & Pembahasan",
    bab5: "Bab 5 - Kesimpulan",
    full_draft: "Draft Lengkap",
    final: "Naskah Final",
    revisi: "Revisi",
    revisi_proposal: "Revisi Proposal",
    lainnya: "Lainnya",
    sk_tugas: "SK Pembimbing Skripsi",
  };
  return map[jenis] || jenis || "Dokumen";
};

const getDocIcon = (jenis) => {
  const map = {
    proposal: "description",
    bab1: "looks_one",
    bab2: "looks_two",
    bab3: "looks_3",
    bab4: "looks_4",
    bab5: "looks_5",
    full_draft: "menu_book",
    final: "task",
    revisi: "rate_review",
    revisi_proposal: "rate_review",
    lainnya: "article",
  };
  return map[jenis] || "description";
};

const getDocIconClass = (jenis) => {
  const map = {
    proposal: "bg-blue-100 dark:bg-blue-900/30 text-blue-600",
    bab1: "bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600",
    bab2: "bg-purple-100 dark:bg-purple-900/30 text-purple-600",
    bab3: "bg-violet-100 dark:bg-violet-900/30 text-violet-600",
    bab4: "bg-fuchsia-100 dark:bg-fuchsia-900/30 text-fuchsia-600",
    bab5: "bg-pink-100 dark:bg-pink-900/30 text-pink-600",
    full_draft: "bg-amber-100 dark:bg-amber-900/30 text-amber-600",
    final: "bg-green-100 dark:bg-green-900/30 text-green-600",
    revisi: "bg-orange-100 dark:bg-orange-900/30 text-orange-600",
    revisi_proposal: "bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600",
    lainnya: "bg-gray-100 dark:bg-gray-800 text-gray-600",
  };
  return map[jenis] || "bg-gray-100 dark:bg-gray-800 text-gray-600";
};

const getStatusLabel = (status) => {
  const map = {
    pending: "Terupload",
    approved: "Disetujui",
    rejected: "Ditolak",
  };
  return map[status] || status || "Terupload";
};

const getStatusClass = (status) => {
  const map = {
    pending:
      "bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-800",
    approved:
      "bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-800",
    rejected:
      "bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-800",
  };
  return (
    map[status] ||
    "bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-700"
  );
};

const getBimbinganStatusLabel = (status) => {
  const map = {
    approved: "Disetujui",
    pending: "Menunggu",
    revision: "Revisi",
    rejected: "Ditolak",
  };
  return map[status] || status || "Menunggu";
};

const getBimbinganStatusClass = (status) => {
  const map = {
    approved:
      "bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 border border-green-100 dark:border-green-800",
    pending:
      "bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-800",
    revision:
      "bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 border border-orange-100 dark:border-orange-800",
    rejected:
      "bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 border border-red-100 dark:border-red-800",
  };
  return (
    map[status] ||
    "bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-gray-100 dark:border-gray-700"
  );
};

const getFileUrl = (path) => {
  if (!path) return "#";
  if (path.startsWith("http")) return path;
  const baseUrl =
    import.meta.env.VITE_API_URL?.replace("/api", "") || "";
  return `${baseUrl}/api/file/${path}`;
};

const formatDate = (dateStr) => {
  if (!dateStr) return "";
  return new Date(dateStr).toLocaleDateString("id-ID", {
    day: "numeric",
    month: "long",
    year: "numeric",
  });
};

const formatDateTime = (dateStr) => {
  if (!dateStr) return "";
  const d = new Date(dateStr);
  const date = d.toLocaleDateString("id-ID", {
    day: "numeric",
    month: "long",
    year: "numeric",
  });
  const time = d.toLocaleTimeString("id-ID", {
    hour: "2-digit",
    minute: "2-digit",
  });
  return `${date}, ${time}`;
};

const formatFileSize = (bytes) => {
  if (!bytes) return "-";
  if (bytes < 1024) return bytes + " B";
  if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + " KB";
  return (bytes / (1024 * 1024)).toFixed(1) + " MB";
};
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
</style>
