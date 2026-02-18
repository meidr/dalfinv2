<template>
  <div class="max-w-7xl mx-auto flex flex-col gap-8 animate-fade-in-up">
    <div class="flex flex-col gap-1">
      <h1 class="text-text-main text-3xl font-bold leading-tight">
        Manajemen SK Tugas
      </h1>
      <p class="text-text-secondary text-sm font-normal">
        Daftar pengajuan SK Tugas Akhir mahasiswa dengan status Sudah Isi
        Dospem.
      </p>
    </div>

    <!-- Signer Configuration -->
    <div
      class="bg-surface-light border border-border-light rounded-xl shadow-sm p-5"
    >
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
          <span class="material-symbols-outlined text-primary"
            >edit_document</span
          >
          <h3 class="text-text-main text-lg font-bold">
            Konfigurasi Penanda Tangan
          </h3>
        </div>
        <button
          @click="saveConfig"
          :disabled="isSavingConfig"
          class="flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-hover transition-colors disabled:opacity-50 disabled:cursor-not-allowed text-sm"
        >
          <span
            v-if="isSavingConfig"
            class="material-symbols-outlined animate-spin text-[18px]"
            >progress_activity</span
          >
          <span v-else class="material-symbols-outlined text-[18px]">save</span>
          {{ isSavingConfig ? "Menyimpan..." : "Simpan Konfigurasi" }}
        </button>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div>
          <label class="block text-xs font-medium text-text-secondary mb-1"
            >Nama Penanda Tangan</label
          >
          <VueMultiselect
            v-model="selectedDosen"
            :options="dosenList"
            :searchable="true"
            :loading="isLoadingDosen"
            :internal-search="false"
            :clear-on-select="false"
            :close-on-select="true"
            :options-limit="50"
            placeholder="Cari Dosen..."
            label="full_name"
            track-by="nip"
            @search-change="debouncedSearchDosen"
          >
            <template #noResult> Data tidak ditemukan. </template>
          </VueMultiselect>
        </div>
        <div>
          <label class="block text-xs font-medium text-text-secondary mb-1"
            >NIP / NIY</label
          >
          <input
            v-model="signer.nip"
            class="w-full px-3 py-2 border border-border-light rounded-lg text-sm focus:ring-1 focus:ring-primary focus:border-primary bg-gray-50"
            placeholder="Otomatis terisi"
            readonly
          />
        </div>
        <div>
          <label class="block text-xs font-medium text-text-secondary mb-1"
            >Jabatan</label
          >
          <input
            v-model="signer.position"
            class="w-full px-3 py-2 border border-border-light rounded-lg text-sm focus:ring-1 focus:ring-primary focus:border-primary"
            placeholder="Contoh: Dekan"
          />
        </div>
        <div>
          <label class="block text-xs font-medium text-text-secondary mb-1"
            >Kota Penetapan</label
          >
          <input
            v-model="signer.city"
            class="w-full px-3 py-2 border border-border-light rounded-lg text-sm focus:ring-1 focus:ring-primary focus:border-primary"
            placeholder="Contoh: Pekanbaru"
          />
        </div>
      </div>

      <!-- Institution field -->
      <div class="mt-4">
        <label class="block text-xs font-medium text-text-secondary mb-1"
          >Nama Institusi / Lembaga</label
        >
        <input
          v-model="signer.institution"
          class="w-full px-3 py-2 border border-border-light rounded-lg text-sm focus:ring-1 focus:ring-primary focus:border-primary"
          placeholder="Contoh: Universitas Islam Internasional Darullughah Wadda'wah"
        />
      </div>

      <!-- Tanda Tangan Upload -->
      <div class="mt-4">
        <label class="block text-xs font-medium text-text-secondary mb-1"
          >Tanda Tangan (Gambar)</label
        >
        <div class="flex items-center gap-4">
          <input
            type="file"
            accept="image/*"
            @change="handleSignatureUpload"
            class="text-sm text-text-secondary file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20"
          />
          <div
            v-if="signer.signature"
            class="h-10 border border-border-light rounded p-1 bg-white"
          >
            <img
              :src="signer.signature"
              class="h-full w-auto object-contain"
              alt="Preview TTD"
            />
          </div>
          <button
            v-if="signer.signature"
            @click="signer.signature = null"
            class="text-xs text-red-500 hover:underline"
          >
            Hapus
          </button>
        </div>
        <p class="text-[10px] text-text-secondary mt-1">
          Format: PNG/JPG. Background transparan disarankan.
        </p>
        <p class="text-[10px] text-text-secondary">
          Kop surat & cap/stempel sudah otomatis dari server.
        </p>
      </div>

      <div
        v-if="!isSignerValid"
        class="mt-3 p-2 bg-yellow-50 text-yellow-700 text-xs rounded border border-yellow-200 flex items-center gap-2"
      >
        <span class="material-symbols-outlined text-[16px]">info</span>
        Lengkapi data penanda tangan & upload gambar TTD untuk mengaktifkan
        tombol Generate SK.
      </div>
    </div>
    <div v-if="loading" class="p-12 text-center">
      <div
        class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto"
      ></div>
      <p class="text-text-secondary text-sm mt-3">Memuat data...</p>
    </div>

    <div
      v-else
      class="flex flex-col bg-surface-light border border-border-light rounded-xl shadow-sm"
    >
      <div
        class="p-5 border-b border-border-light flex flex-col md:flex-row gap-4 items-center justify-between"
      >
        <div>
          <h3 class="text-text-main text-lg font-bold">Data SK Tugas</h3>
          <p class="text-text-secondary text-xs">
            Menampilkan semua mahasiswa yang sudah memiliki pembimbing.
          </p>
        </div>
        <div
          class="p-5 border-b border-border-light flex flex-col lg:flex-row lg:items-center justify-between gap-4"
        >
          <div class="flex p-1 bg-sidebar-light rounded-lg w-fit">
            <button
              @click="setFilter('')"
              :class="
                filterStatus === ''
                  ? 'bg-background-light text-primary shadow-sm border border-border-light/50 dark:bg-surface'
                  : 'text-text-secondary hover:text-text-main hover:bg-background-light/50'
              "
              class="px-4 py-1.5 text-xs font-bold rounded transition-all"
            >
              Semua
            </button>
            <button
              @click="setFilter('belum_ttd')"
              :class="
                filterStatus === 'belum_ttd'
                  ? 'bg-background-light text-primary shadow-sm border border-border-light/50 dark:bg-surface'
                  : 'text-text-secondary hover:text-text-main hover:bg-background-light/50'
              "
              class="px-4 py-1.5 text-xs font-medium rounded transition-all"
            >
              Belum TTD
            </button>
            <button
              @click="setFilter('sudah_ttd')"
              :class="
                filterStatus === 'sudah_ttd'
                  ? 'bg-background-light text-primary shadow-sm border border-border-light/50 dark:bg-surface'
                  : 'text-text-secondary hover:text-text-main hover:bg-white/50'
              "
              class="px-4 py-1.5 text-xs font-medium rounded transition-all"
            >
              Sudah TTD
            </button>
          </div>
          <div class="relative flex-1 md:flex-none">
            <input
              v-model="searchQuery"
              @input="debouncedSearch"
              class="block w-full pl-10 pr-3 py-2.5 border border-border-light rounded-lg leading-5 bg-background-light text-text-main placeholder-text-secondary focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary sm:text-sm transition-shadow dark:bg-background"
              placeholder="Cari mahasiswa..."
            />
            <div
              class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
            >
              <span class="material-symbols-outlined text-text-secondary"
                >search</span
              >
            </div>
          </div>
        </div>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
          <thead
            class="bg-sidebar-light/50 text-text-secondary font-medium border-b border-border-light"
          >
            <tr>
              <th class="px-6 py-4">Mahasiswa</th>
              <th class="px-6 py-4">Judul Skripsi</th>
              <th class="px-6 py-4">Pembimbing</th>
              <th class="px-6 py-4">Status</th>
              <th class="px-6 py-4 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border-light">
            <tr v-if="skTugasList.length === 0">
              <td
                colspan="5"
                class="px-6 py-12 text-center text-text-secondary"
              >
                Tidak ada data
              </td>
            </tr>
            <tr
              v-for="item in skTugasList"
              :key="item.id"
              class="group hover:bg-sidebar-light/30 transition-colors"
            >
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
              <td class="px-6 py-4 max-w-xs truncate" :title="item.judul">
                {{ item.judul || "-" }}
              </td>
              <td class="px-6 py-4">
                <div class="flex flex-col gap-1">
                  <div
                    v-for="(pembimbing, idx) in item.pembimbing || []"
                    :key="idx"
                    class="flex items-center gap-2"
                  >
                    <span
                      class="size-5 rounded-full flex items-center justify-center text-[10px] font-bold"
                      :class="
                        idx === 0
                          ? 'bg-blue-100 text-blue-700'
                          : 'bg-gray-100 text-gray-600'
                      "
                      >{{ idx + 1 }}</span
                    >
                    <span class="text-xs text-text-main">{{
                      getDosenName(pembimbing.dosen)
                    }}</span>
                  </div>
                  <div
                    v-if="!item.pembimbing?.length"
                    class="text-xs text-text-secondary"
                  >
                    Belum ada pembimbing
                  </div>
                </div>
              </td>
              <td class="px-6 py-4">
                <span
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium"
                  :class="getStatusClass(item.sk_status)"
                >
                  <span
                    class="w-1.5 h-1.5 rounded-full"
                    :class="getStatusDot(item.sk_status)"
                  ></span>
                  {{ getStatusLabel(item.sk_status) }}
                </span>
              </td>
              <td class="px-6 py-4 text-right">
                <div class="flex items-center justify-end gap-2">
                  <button
                    v-if="item.sk_dokumen"
                    @click="viewFile(item.sk_dokumen)"
                    class="p-2 text-text-secondary hover:text-primary hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
                    title="Lihat File"
                  >
                    <span class="material-symbols-outlined text-[20px]"
                      >visibility</span
                    >
                  </button>
                  <button
                    @click="generateSK(item)"
                    :disabled="generating === item.id || !isSignerValid"
                    class="p-2 text-text-secondary hover:text-primary hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors disabled:opacity-30 disabled:cursor-not-allowed"
                    :title="
                      !isSignerValid
                        ? 'Lengkapi data penanda tangan terlebih dahulu'
                        : 'Generate SK'
                    "
                  >
                    <span class="material-symbols-outlined text-[20px]"
                      >print</span
                    >
                  </button>
                  <button
                    @click="uploadSK(item)"
                    class="p-2 text-text-secondary hover:text-primary hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
                    title="Upload SK"
                  >
                    <span class="material-symbols-outlined text-[20px]"
                      >upload_file</span
                    >
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
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
          data
        </p>
        <div class="flex gap-1">
          <button
            @click="goToPage(pagination.current_page - 1)"
            :disabled="pagination.current_page <= 1"
            class="size-8 flex items-center justify-center rounded border border-border-light hover:bg-gray-50 text-text-secondary transition-colors disabled:opacity-50"
          >
            <span class="material-symbols-outlined text-[16px]"
              >chevron_left</span
            >
          </button>
          <button
            class="size-8 flex items-center justify-center rounded bg-primary text-white text-xs font-bold shadow-sm shadow-primary/20"
          >
            {{ pagination.current_page }}
          </button>
          <button
            @click="goToPage(pagination.current_page + 1)"
            :disabled="pagination.current_page >= pagination.last_page"
            class="size-8 flex items-center justify-center rounded border border-border-light hover:bg-gray-50 text-text-secondary transition-colors"
          >
            <span class="material-symbols-outlined text-[16px]"
              >chevron_right</span
            >
          </button>
        </div>
      </div>
    </div>

    <!-- Upload Modal -->
    <Transition name="modal-fade">
      <div
        v-if="showUploadModal"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
      >
        <div
          class="bg-white dark:bg-surface-light rounded-xl shadow-2xl w-full max-w-md"
        >
          <div class="p-6 border-b border-border-light">
            <h2 class="text-xl font-bold text-text-main">Upload SK Tugas</h2>
            <p class="text-sm text-text-secondary mt-1">
              {{ selectedItem?.skripsi?.mahasiswa?.nama }}
            </p>
          </div>
          <form @submit.prevent="submitUpload" class="p-6 space-y-4">
            <div>
              <label class="block text-sm font-medium text-text-main mb-1"
                >File SK (PDF)</label
              >
              <input
                type="file"
                @change="handleFileChange"
                accept=".pdf"
                class="w-full px-3 py-2 border border-border-light rounded-lg"
                required
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-text-main mb-1"
                >Nomor SK</label
              >
              <input
                v-model="uploadForm.nomor_sk"
                type="text"
                class="w-full px-3 py-2 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
                required
              />
            </div>
            <div>
              <input
                v-model="uploadForm.tanggal_sk"
                type="date"
                class="w-full px-3 py-2 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
                required
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-text-main mb-2"
                >Status SK</label
              >
              <div class="flex gap-4">
                <label class="flex items-center gap-2 cursor-pointer">
                  <input
                    type="radio"
                    v-model="uploadForm.status"
                    value="pending"
                    class="text-primary focus:ring-primary"
                  />
                  <span class="text-sm text-text-main">Belum TTD</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                  <input
                    type="radio"
                    v-model="uploadForm.status"
                    value="approved"
                    class="text-primary focus:ring-primary"
                  />
                  <span class="text-sm text-text-main">Sudah TTD</span>
                </label>
              </div>
            </div>
            <div class="flex gap-3 pt-4">
              <button
                type="button"
                @click="showUploadModal = false"
                class="flex-1 px-4 py-2.5 border border-border-light rounded-lg text-text-secondary hover:bg-background-light transition-colors"
              >
                Batal
              </button>
              <button
                type="submit"
                :disabled="uploading"
                class="flex-1 px-4 py-2.5 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors disabled:opacity-50"
              >
                {{ uploading ? "Mengupload..." : "Upload" }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive, watch, computed } from "vue";
import adminService from "../../../services/adminService";
import VueMultiselect from "vue-multiselect";

const loading = ref(true);
const generating = ref(null);
const uploading = ref(false);
const showUploadModal = ref(false);
const selectedItem = ref(null);
const skTugasList = ref([]);
const searchQuery = ref("");
const filterStatus = ref("");

const isSavingConfig = ref(false);
const signer = ref({
  name: "",
  nip: "",
  position: "Kepala Prodi",
  city: "Bangil",
  institution: "Universitas Islam Internasional Darullughah Wadda'wah",
  signature: null,
});

// Dosen Selection Logic
const dosenList = ref([]);
const isLoadingDosen = ref(false);
const selectedDosen = ref(null);
let searchDosenTimeout = null;

const searchDosen = async (query) => {
  isLoadingDosen.value = true;
  try {
    const response = await adminService.getDosen({
      search: query,
      per_page: 20,
    });
    if (response.success) {
      dosenList.value = response.data.data;
    }
  } catch (error) {
    console.error("Failed to search dosen:", error);
  } finally {
    isLoadingDosen.value = false;
  }
};

const debouncedSearchDosen = (query) => {
  clearTimeout(searchDosenTimeout);
  searchDosenTimeout = setTimeout(() => {
    searchDosen(query);
  }, 300);
};

// Sync selectedDosen with signer fields
watch(selectedDosen, (newVal) => {
  if (newVal) {
    signer.value.name = newVal.full_name;
    signer.value.nip = newVal.nip;
    signer.value.position =
      newVal.jabatan_fungsional || signer.value.position || "Dekan";
  } else {
    // Optional: Clear fields if selection is cleared?
    // User might want to keep custom text if they cleared the dropdown.
    // But "Konfigurasi Penanda Tangan" implies strict sync if using dropdown.
    // To be safe, let's NOT clear name if it's already set, unless we are sure.
    // But usually clearing dropdown means "I want to remove this person".
    // Let's clear it to be consistent with "dropdown as input".
    // signer.value.name = "";
    // signer.value.nip = "";
    // signer.value.position = "Dekan";
    // COMMENTED OUT: User might have edited manually.
    // If I clear, it overrides manual edits.
    // But since the dropdown IS the input for name now...
    // Wait, the input is gone (replaced by Multiselect).
    // So if I clear Multiselect, I MUST clear signer.name.
    signer.value.name = "";
    signer.value.nip = "";
    // signer.value.position = "Dekan"; // Keep position?
  }
});

const isSignerValid = computed(() => {
  return (
    signer.value.name &&
    signer.value.nip &&
    signer.value.position &&
    signer.value.city &&
    signer.value.signature
  );
});

const handleSignatureUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = (e) => {
      signer.value.signature = e.target.result;
    };
    reader.readAsDataURL(file);
  }
};

const pagination = reactive({
  current_page: 1,
  last_page: 1,
  total: 0,
  from: 0,
  to: 0,
});

const uploadForm = reactive({
  file: null,
  nomor_sk: "",
  tanggal_sk: "",
  status: "pending",
});

const setFilter = (status) => {
  filterStatus.value = status;
  pagination.current_page = 1;
  fetchSKTugas();
};

let searchTimeout = null;

const fetchSKTugas = async () => {
  try {
    loading.value = true;
    const params = {
      page: pagination.current_page,
      search: searchQuery.value,
      status: filterStatus.value,
    };
    const response = await adminService.getSKTugas(params);

    // Normalize response (handle both axios object and direct data)
    const body = response.data?.success ? response.data : response;

    if (body.success) {
      skTugasList.value = body.data.data || body.data;
      if (body.data.current_page) {
        Object.assign(pagination, {
          current_page: body.data.current_page,
          last_page: body.data.last_page,
          total: body.data.total,
          from: body.data.from,
          to: body.data.to,
        });
      }
    }
  } catch (error) {
    console.error("Failed to fetch SK Tugas:", error);
  } finally {
    loading.value = false;
  }
};

const debouncedSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    pagination.current_page = 1;
    fetchSKTugas();
  }, 300);
};

const goToPage = (page) => {
  if (page >= 1 && page <= pagination.last_page) {
    pagination.current_page = page;
    fetchSKTugas();
  }
};

const generateSK = async (item) => {
  try {
    if (!isSignerValid.value) {
      alert("Mohon lengkapi data penanda tangan terlebih dahulu.");
      return;
    }
    generating.value = item.id;
    const response = await adminService.getSkTugasPdf(item.id, {
      signer_name: signer.value.name,
      signer_nip: signer.value.nip,
      signer_position: signer.value.position,
      signer_city: signer.value.city,
      signer_institution: signer.value.institution,
      signer_signature: signer.value.signature,
    });
    const blob = new Blob([response.data], { type: "application/pdf" });
    const url = window.URL.createObjectURL(blob);
    window.open(url, "_blank");
  } catch (error) {
    console.error("Failed to generate SK:", error);
    alert("Gagal generate SK Tugas");
  } finally {
    generating.value = null;
  }
};

const saveConfig = async () => {
  if (!isSignerValid.value) {
    alert("Mohon lengkapi semua data penanda tangan terlebih dahulu.");
    return;
  }

  isSavingConfig.value = true;
  try {
    // console.log("Saving config:", signer.value);
    await adminService.saveSkTugasSignerConfig(signer.value);
    alert("Konfigurasi berhasil disimpan!");
  } catch (error) {
    console.error("Failed to save config:", error);
    alert("Gagal menyimpan konfigurasi.");
  } finally {
    isSavingConfig.value = false;
  }
};

const fetchConfig = async () => {
  try {
    const response = await adminService.getSkTugasSignerConfig();

    if (response.success && response.data) {
      signer.value.name = response.data.name || "";
      signer.value.nip = response.data.nip || "";
      signer.value.position = response.data.position || "";
      signer.value.city = response.data.city || "";
      signer.value.institution =
        response.data.institution ||
        "Universitas Islam Internasional Darullughah Wadda'wah";
      signer.value.signature = response.data.signature || null;

      // Init dropdown visual state
      if (signer.value.name) {
        selectedDosen.value = {
          full_name: signer.value.name,
          nip: signer.value.nip,
        };
      }
    }
  } catch (error) {
    console.error("Failed to fetch config:", error);
  }
};

const viewFile = async (dokumen) => {
  if (!dokumen) return;
  try {
    const response = await adminService.downloadDokumen(dokumen.id);
    const blob = new Blob([response.data], { type: "application/pdf" });
    const url = window.URL.createObjectURL(blob);
    window.open(url, "_blank");
  } catch (error) {
    console.error("Failed to view file:", error);
    alert("Gagal membuka file SK");
  }
};

const uploadSK = (item) => {
  selectedItem.value = item;
  uploadForm.file = null;
  uploadForm.nomor_sk = "";
  uploadForm.tanggal_sk = new Date().toISOString().split("T")[0];
  uploadForm.status = "pending";
  showUploadModal.value = true;
};

const handleFileChange = (e) => {
  uploadForm.file = e.target.files[0];
};

const submitUpload = async () => {
  try {
    uploading.value = true;
    const formData = new FormData();
    formData.append("file", uploadForm.file);
    formData.append("nomor_sk", uploadForm.nomor_sk);
    formData.append("tanggal_sk", uploadForm.tanggal_sk);
    formData.append("skripsi_id", selectedItem.value.id);
    formData.append("jenis", "sk_tugas");
    formData.append("status", uploadForm.status);

    await adminService.uploadDokumen(formData);
    showUploadModal.value = false;
    // Auto switch tab based on upload status
    filterStatus.value =
      uploadForm.status === "approved" ? "sudah_ttd" : "belum_ttd";
    pagination.current_page = 1;
    fetchSKTugas();
    alert("SK berhasil diupload!");
  } catch (error) {
    console.error("Failed to upload SK:", error);
    alert(
      "Gagal upload SK: " + (error.response?.data?.message || error.message),
    );
  } finally {
    uploading.value = false;
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

// Helper to get dosen full name
const getDosenName = (dosen) => {
  if (!dosen) return "-";
  const parts = [];
  if (dosen.gelar_depan) parts.push(dosen.gelar_depan);
  if (dosen.nama) parts.push(dosen.nama);
  if (dosen.gelar_belakang) parts.push(dosen.gelar_belakang);
  return parts.join(" ") || dosen.nama || "-";
};

const getStatusClass = (status) => {
  const classes = {
    belum_ada: "bg-blue-50 text-blue-600 border border-blue-100",
    pending: "bg-yellow-50 text-yellow-600 border border-yellow-100",
    approved: "bg-green-50 text-green-600 border border-green-100",
  };
  return classes[status] || "bg-gray-50 text-gray-600 border border-gray-100";
};

const getStatusDot = (status) => {
  const dots = {
    belum_ada: "bg-blue-600",
    pending: "bg-yellow-600",
    approved: "bg-green-600",
  };
  return dots[status] || "bg-gray-600";
};

const getStatusLabel = (status) => {
  const labels = {
    belum_ada: "Siap Proses",
    pending: "Belum TTD",
    approved: "Sudah TTD",
  };
  return labels[status] || status;
};

onMounted(() => {
  // console.log("Component Mounted");
  fetchSKTugas();
  fetchConfig();
});
</script>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>
