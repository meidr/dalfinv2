<template>
  <div class="flex flex-col gap-6 animate-fade-in">
    <!-- Info Banner -->
    <div
      class="flex items-start gap-3 p-4 bg-orange-50 dark:bg-orange-900/10 rounded-xl border border-orange-200 dark:border-orange-800"
    >
      <span class="material-symbols-outlined text-orange-500 mt-0.5"
        >info</span
      >
      <div>
        <p class="text-sm font-bold text-orange-700 dark:text-orange-300">
          Revisi Skripsi
        </p>
        <p class="text-xs text-orange-600/80 dark:text-orange-400/80 mt-0.5">
          Setelah sidang, penguji dapat memberikan catatan revisi yang harus
          Anda perbaiki. Upload berkas revisi Anda di halaman ini. Pastikan
          semua perbaikan selesai sesuai catatan penguji.
        </p>
      </div>
    </div>

    <!-- Not in revisi phase -->
    <div
      v-if="!hasRevisiData && !isRevisiPhase"
      class="bg-surface-light rounded-xl shadow-sm border border-border-light p-12 flex flex-col items-center justify-center gap-3 text-center"
    >
      <span
        class="material-symbols-outlined text-5xl text-text-secondary opacity-40"
        >edit_document</span
      >
      <h3 class="text-lg font-bold text-text-main">Belum Ada Revisi</h3>
      <p class="text-text-secondary text-sm max-w-md">
        Catatan revisi dari penguji akan muncul di sini setelah ujian sidang
        skripsi Anda selesai. Jika penguji memberikan catatan perbaikan, Anda
        dapat melihat detailnya dan mengupload berkas revisi.
      </p>
    </div>

    <template v-else>
      <!-- ===== CATATAN REVISI SEMPRO ===== -->
      <div v-if="semproPengujiWithCatatan.length">
        <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
          <span class="material-symbols-outlined text-blue-500"
            >rate_review</span
          >
          Catatan Revisi Seminar Proposal
        </h3>
        <div class="space-y-3">
          <div
            v-for="p in semproPengujiWithCatatan"
            :key="'sp-' + p.id"
            class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden"
          >
            <div
              class="px-5 py-3 border-b border-border-light bg-blue-50/50 dark:bg-blue-900/10 flex items-center gap-3"
            >
              <div
                class="size-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 font-bold text-xs"
              >
                {{ getInitials(p.dosen?.nama) }}
              </div>
              <div>
                <p class="text-sm font-bold text-text-main">
                  {{ p.dosen?.nama || "Penguji" }}
                </p>
                <p class="text-[10px] text-text-secondary">
                  {{ getPeranLabel(p.peran) }}
                </p>
              </div>
            </div>
            <div class="px-5 py-4">
              <p class="text-sm text-text-main whitespace-pre-wrap leading-relaxed">{{ p.catatan }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- ===== CATATAN REVISI SIDANG ===== -->
      <div v-if="sidangPengujiWithCatatan.length">
        <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
          <span class="material-symbols-outlined text-purple-500"
            >rate_review</span
          >
          Catatan Revisi Sidang
        </h3>
        <div class="space-y-3">
          <div
            v-for="p in sidangPengujiWithCatatan"
            :key="'sd-' + p.id"
            class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden"
          >
            <div
              class="px-5 py-3 border-b border-border-light bg-purple-50/50 dark:bg-purple-900/10 flex items-center gap-3"
            >
              <div
                class="size-8 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 font-bold text-xs"
              >
                {{ getInitials(p.dosen?.nama) }}
              </div>
              <div>
                <p class="text-sm font-bold text-text-main">
                  {{ p.dosen?.nama || "Penguji" }}
                </p>
                <p class="text-[10px] text-text-secondary">
                  {{ getPeranLabel(p.peran) }}
                </p>
              </div>
            </div>
            <div class="px-5 py-4">
              <p class="text-sm text-text-main whitespace-pre-wrap leading-relaxed">{{ p.catatan }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- ===== CATATAN REVISI SEMHAS ===== -->
      <div v-if="semhasPengujiWithCatatan.length">
        <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
          <span class="material-symbols-outlined text-orange-500"
            >rate_review</span
          >
          Catatan Revisi Seminar Hasil
        </h3>
        <div class="space-y-3">
          <div
            v-for="p in semhasPengujiWithCatatan"
            :key="'sh-' + p.id"
            class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden"
          >
            <div
              class="px-5 py-3 border-b border-border-light bg-orange-50/50 dark:bg-orange-900/10 flex items-center gap-3"
            >
              <div
                class="size-8 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center text-orange-600 font-bold text-xs"
              >
                {{ getInitials(p.dosen?.nama) }}
              </div>
              <div>
                <p class="text-sm font-bold text-text-main">
                  {{ p.dosen?.nama || "Penguji" }}
                </p>
                <p class="text-[10px] text-text-secondary">
                  {{ getPeranLabel(p.peran) }}
                </p>
              </div>
            </div>
            <div class="px-5 py-4">
              <p class="text-sm text-text-main whitespace-pre-wrap leading-relaxed">{{ p.catatan }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- ===== UPLOAD BERKAS REVISI ===== -->
      <div>
        <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
          <span class="material-symbols-outlined text-primary"
            >upload_file</span
          >
          Upload Berkas Revisi
        </h3>

        <div
          class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden"
        >
          <div class="p-5 border-b border-border-light">
            <p class="text-sm text-text-secondary">
              Upload berkas revisi skripsi Anda setelah melakukan perbaikan
              sesuai catatan penguji. File yang diupload akan masuk ke
              tab Dokumen.
            </p>
          </div>

          <!-- Existing revisi docs -->
          <div v-if="revisiDocs.length" class="p-5 border-b border-border-light">
            <p
              class="text-xs font-bold uppercase tracking-wider text-text-secondary mb-3"
            >
              Berkas Revisi yang Sudah Diupload
            </p>
            <div class="space-y-2">
              <div
                v-for="doc in revisiDocs"
                :key="doc.id"
                class="flex items-center justify-between bg-gray-50 dark:bg-white/5 rounded-lg px-4 py-3 border border-border-light"
              >
                <div class="flex items-center gap-3 min-w-0">
                  <div
                    class="p-1.5 rounded-lg bg-orange-100 dark:bg-orange-900/30 text-orange-600"
                  >
                    <span class="material-symbols-outlined text-[18px]"
                      >description</span
                    >
                  </div>
                  <div class="min-w-0">
                    <p class="text-sm font-medium text-text-main truncate">
                      {{ doc.nama_file || getJenisLabel(doc.jenis) }}
                    </p>
                    <p class="text-xs text-text-secondary">
                      {{ formatFileSize(doc.ukuran) }} •
                      {{ formatDate(doc.created_at) }}
                    </p>
                  </div>
                </div>
                <a
                  :href="getFileUrl(doc.path)"
                  target="_blank"
                  class="text-primary hover:text-blue-700 transition-colors"
                >
                  <span class="material-symbols-outlined text-[20px]"
                    >download</span
                  >
                </a>
              </div>
            </div>
          </div>

          <!-- Upload form -->
          <div class="p-5">
            <form @submit.prevent="submitUpload" class="flex flex-col gap-4">
              <div
                v-if="uploadError"
                class="flex gap-2 bg-red-50 dark:bg-red-900/20 p-3 rounded-lg border border-red-100 dark:border-red-800 text-sm text-red-700 dark:text-red-300"
              >
                <span
                  class="material-symbols-outlined text-red-500 text-[18px]"
                  >error</span
                >
                {{ uploadError }}
              </div>

              <!-- Jenis dropdown -->
              <div class="flex flex-col gap-2">
                <label class="text-sm font-bold text-text-main"
                  >Jenis Berkas</label
                >
                <select
                  v-model="uploadForm.jenis"
                  required
                  class="px-4 py-2.5 rounded-lg border border-border-light bg-background-light focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm"
                >
                  <option value="" disabled>Pilih jenis berkas</option>
                  <option value="revisi">Revisi Skripsi</option>
                  <option value="revisi_proposal">Revisi Proposal</option>
                </select>
              </div>

              <!-- File input -->
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
                  placeholder="Contoh: Revisi sesuai masukan penguji"
                  class="px-4 py-2.5 rounded-lg border border-border-light bg-background-light focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm"
                />
              </div>

              <div class="flex justify-end gap-3 pt-2">
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
                  <span
                    v-else
                    class="material-symbols-outlined text-[18px]"
                    >cloud_upload</span
                  >
                  {{ uploading ? "Mengunggah..." : "Upload Revisi" }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </template>

    <!-- Success Toast -->
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
import { ref, inject, computed } from "vue";
import { mahasiswaService } from "../../../services/mahasiswaService";

const skripsi = inject("skripsi");

const isRevisiPhase = computed(() =>
  ["revisi", "lulus"].includes(skripsi.value?.status),
);

// Extract catatan from penguji
const sempro = computed(() =>
  (skripsi.value?.seminar || []).find((s) => s.jenis === "sempro"),
);
const semhas = computed(() =>
  (skripsi.value?.seminar || []).find((s) => s.jenis === "semhas"),
);
const sidang = computed(() =>
  (skripsi.value?.seminar || []).find((s) => s.jenis === "sidang"),
);

const semproPengujiWithCatatan = computed(() =>
  (sempro.value?.penguji || []).filter((p) => p.catatan),
);
const semhasPengujiWithCatatan = computed(() =>
  (semhas.value?.penguji || []).filter((p) => p.catatan),
);
const sidangPengujiWithCatatan = computed(() => {
  const fromSeminar = (sidang.value?.penguji || []).filter((p) => p.catatan);
  // Also check ujian table
  const ujian = skripsi.value?.ujian || [];
  const fromUjian = ujian.flatMap((u) =>
    (u.penguji || []).filter((p) => p.catatan),
  );
  return [...fromSeminar, ...fromUjian];
});

const hasRevisiData = computed(
  () =>
    semproPengujiWithCatatan.value.length > 0 ||
    semhasPengujiWithCatatan.value.length > 0 ||
    sidangPengujiWithCatatan.value.length > 0 ||
    revisiDocs.value.length > 0,
);

// Existing revisi documents
const revisiDocs = computed(() =>
  (skripsi.value?.dokumen || [])
    .filter((d) => d.jenis === "revisi" || d.jenis === "revisi_proposal")
    .sort((a, b) => new Date(b.created_at) - new Date(a.created_at)),
);

// Upload form
const uploading = ref(false);
const uploadError = ref("");
const fileInputRef = ref(null);
const uploadForm = ref({ jenis: "revisi", file: null, catatan: "" });
const toast = ref({ show: false, message: "", type: "success" });

const onUploadFile = (e) => {
  uploadForm.value.file = e.target.files[0] || null;
};

const submitUpload = async () => {
  uploadError.value = "";
  uploading.value = true;
  try {
    const res = await mahasiswaService.uploadDokumen(uploadForm.value);
    if (res.success) {
      uploadForm.value = { jenis: "revisi", file: null, catatan: "" };
      if (fileInputRef.value) fileInputRef.value.value = "";
      toast.value = {
        show: true,
        message: "Berkas revisi berhasil diupload!",
        type: "success",
      };
      setTimeout(() => {
        toast.value.show = false;
      }, 3000);
      // Refresh skripsi data
      try {
        const refreshRes = await mahasiswaService.getSkripsiDetail();
        if (refreshRes.success && skripsi.value) {
          Object.assign(skripsi.value, refreshRes.data);
        }
      } catch (e) {
        // silent
      }
    } else {
      uploadError.value = res.message || "Gagal mengunggah berkas.";
    }
  } catch (err) {
    const data = err.response?.data;
    if (data?.errors) {
      const firstErr = Object.values(data.errors)[0];
      uploadError.value = Array.isArray(firstErr) ? firstErr[0] : firstErr;
    } else {
      uploadError.value =
        data?.message || "Terjadi kesalahan saat mengunggah.";
    }
  } finally {
    uploading.value = false;
  }
};

// Helpers
const getInitials = (nama) => {
  if (!nama) return "??";
  return nama.split(" ").map((w) => w[0]).join("").toUpperCase().slice(0, 2);
};

const getPeranLabel = (peran) => {
  const map = {
    ketua: "Ketua Penguji", sekretaris: "Sekretaris", penguji: "Penguji",
    penguji_1: "Penguji 1", penguji_2: "Penguji 2",
    pembimbing_1: "Pembimbing 1", pembimbing_2: "Pembimbing 2",
  };
  return map[peran] || peran || "Penguji";
};

const getJenisLabel = (jenis) => {
  const map = { revisi: "Revisi Skripsi", revisi_proposal: "Revisi Proposal" };
  return map[jenis] || jenis || "Dokumen";
};

const formatFileSize = (bytes) => {
  if (!bytes) return "-";
  if (bytes < 1024) return bytes + " B";
  if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + " KB";
  return (bytes / (1024 * 1024)).toFixed(1) + " MB";
};

const formatDate = (dateStr) => {
  if (!dateStr) return "-";
  return new Date(dateStr).toLocaleDateString("id-ID", {
    day: "numeric",
    month: "long",
    year: "numeric",
  });
};

const getFileUrl = (path) => {
  if (!path) return "#";
  if (path.startsWith("http")) return path;
  const baseUrl = import.meta.env.VITE_API_URL?.replace("/api", "") || "";
  return `${baseUrl}/api/file/${path}`;
};
</script>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.3s ease-in-out;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(5px); }
  to { opacity: 1; transform: translateY(0); }
}
.toast-slide-enter-active { transition: all 0.3s ease; }
.toast-slide-leave-active { transition: all 0.3s ease; }
.toast-slide-enter-from, .toast-slide-leave-to { opacity: 0; transform: translateY(20px); }
</style>
