<template>
  <div class="flex flex-col gap-6">
    <div class="flex flex-col gap-1">
      <h1 class="text-text-main text-3xl font-bold leading-tight">
        Manajemen SK Tugas
      </h1>
      <p class="text-text-secondary text-sm font-normal">
        Daftar pengajuan SK Tugas Akhir mahasiswa dengan status Sudah Isi
        Dospem.
      </p>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="p-12 text-center">
      <div
        class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto"
      ></div>
      <p class="text-text-secondary text-sm mt-3">Memuat data...</p>
    </div>

    <div
      v-else
      class="w-full bg-surface-light border border-border-light rounded-xl shadow-sm flex flex-col overflow-hidden"
    >
      <div
        class="p-5 border-b border-border-light flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-gray-50/50"
      >
        <div class="flex p-1 bg-sidebar-light rounded-lg w-fit">
          <button
            @click="setFilter('sudah_isi')"
            :class="
              filterStatus === 'sudah_isi'
                ? 'bg-white text-primary shadow-sm border border-border-light/50'
                : 'text-text-secondary hover:text-text-main hover:bg-white/50'
            "
            class="px-4 py-1.5 text-xs font-bold rounded transition-all"
          >
            Sudah Isi Dospem
          </button>
          <button
            @click="setFilter('menunggu_ttd')"
            :class="
              filterStatus === 'menunggu_ttd'
                ? 'bg-white text-primary shadow-sm border border-border-light/50'
                : 'text-text-secondary hover:text-text-main hover:bg-white/50'
            "
            class="px-4 py-1.5 text-xs font-medium rounded transition-all"
          >
            Menunggu TTD
          </button>
          <button
            @click="setFilter('selesai')"
            :class="
              filterStatus === 'selesai'
                ? 'bg-white text-primary shadow-sm border border-border-light/50'
                : 'text-text-secondary hover:text-text-main hover:bg-white/50'
            "
            class="px-4 py-1.5 text-xs font-medium rounded transition-all"
          >
            Selesai
          </button>
        </div>
        <div class="flex items-center gap-3 w-full lg:w-auto">
          <div class="relative flex-1 lg:flex-none">
            <input
              v-model="searchQuery"
              @input="debouncedSearch"
              class="pl-9 pr-4 py-2 rounded-lg border border-border-light bg-white text-xs w-full lg:w-48 focus:ring-1 focus:ring-primary"
              placeholder="Cari mahasiswa..."
            />
            <span
              class="material-symbols-outlined absolute left-2 top-2 text-[16px] text-text-secondary"
              >search</span
            >
          </div>
        </div>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-sidebar-light/50 border-b border-border-light">
              <th
                class="p-4 text-[11px] font-bold tracking-widest text-text-secondary uppercase w-1/4"
              >
                Mahasiswa
              </th>
              <th
                class="p-4 text-[11px] font-bold tracking-widest text-text-secondary uppercase w-1/4"
              >
                Judul Skripsi
              </th>
              <th
                class="p-4 text-[11px] font-bold tracking-widest text-text-secondary uppercase w-1/5"
              >
                Pembimbing
              </th>
              <th
                class="p-4 text-[11px] font-bold tracking-widest text-text-secondary uppercase text-right w-1/4"
              >
                Aksi
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border-light">
            <tr v-if="skTugasList.length === 0">
              <td colspan="4" class="p-12 text-center text-text-secondary">
                Tidak ada data
              </td>
            </tr>
            <tr
              v-for="item in skTugasList"
              :key="item.id"
              class="group hover:bg-blue-50/20 transition-colors"
            >
              <td class="p-4 align-top">
                <div class="flex items-start gap-3">
                  <div
                    class="size-10 rounded-full flex items-center justify-center text-xs font-bold shadow-sm border border-border-light"
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
                    <span
                      class="inline-block mt-1 text-[10px] font-semibold text-text-secondary bg-gray-100 px-1.5 py-0.5 rounded border border-gray-200"
                      >{{
                        item.mahasiswa?.program_studi || "Teknik Informatika"
                      }}</span
                    >
                  </div>
                </div>
              </td>
              <td class="p-4 align-top">
                <p
                  class="text-xs font-medium text-text-main leading-relaxed line-clamp-2"
                >
                  {{ item.judul || "-" }}
                </p>
              </td>
              <td class="p-4 align-top">
                <div class="flex flex-col gap-2">
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
              <td class="p-4 align-top text-right">
                <div
                  class="flex flex-col sm:flex-row gap-2 justify-end items-center"
                >
                  <button
                    @click="generateSK(item)"
                    :disabled="generating === item.id"
                    class="flex items-center justify-center gap-2 px-3 py-1.5 text-xs font-bold text-text-main bg-white border border-border-light rounded-lg hover:bg-gray-50 hover:border-gray-300 transition-all shadow-sm w-full sm:w-auto disabled:opacity-50"
                  >
                    <span class="material-symbols-outlined text-[16px]"
                      >print</span
                    >
                    {{ generating === item.id ? "Loading..." : "Generate SK" }}
                  </button>
                  <button
                    @click="uploadSK(item)"
                    class="flex items-center justify-center gap-2 px-3 py-1.5 text-xs font-bold text-white bg-primary rounded-lg hover:bg-primary/90 transition-all shadow-sm shadow-primary/20 w-full sm:w-auto"
                  >
                    <span class="material-symbols-outlined text-[16px]"
                      >upload_file</span
                    >
                    Upload
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div
        class="p-4 border-t border-border-light flex items-center justify-between bg-gray-50/50"
      >
        <span class="text-xs text-text-secondary">
          Menampilkan {{ pagination.from || 0 }}-{{ pagination.to || 0 }} dari
          {{ pagination.total }} data
        </span>
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
            <label class="block text-sm font-medium text-text-main mb-1"
              >Tanggal SK</label
            >
            <input
              v-model="uploadForm.tanggal_sk"
              type="date"
              class="w-full px-3 py-2 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
              required
            />
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
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from "vue";
import adminService from "../../../services/adminService";

const loading = ref(true);
const generating = ref(null);
const uploading = ref(false);
const showUploadModal = ref(false);
const selectedItem = ref(null);
const skTugasList = ref([]);
const searchQuery = ref("");
const filterStatus = ref("sudah_isi");

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
});

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

const setFilter = (status) => {
  filterStatus.value = status;
  pagination.current_page = 1;
  fetchSKTugas();
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
    generating.value = item.id;
    const response = await adminService.getSkTugasPdf(item.id);
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

const uploadSK = (item) => {
  selectedItem.value = item;
  uploadForm.file = null;
  uploadForm.nomor_sk = "";
  uploadForm.tanggal_sk = new Date().toISOString().split("T")[0];
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
    formData.append("tipe", "sk_tugas");

    await adminService.uploadDokumen(formData);
    showUploadModal.value = false;
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

onMounted(() => {
  fetchSKTugas();
});
</script>
