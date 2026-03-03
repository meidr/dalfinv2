<template>
  <div class="max-w-7xl mx-auto flex flex-col gap-8 animate-fade-in-up">
    <!-- Header -->
    <div class="flex items-center gap-4">
      <button
        @click="router.push({ name: 'DataSKYudisium' })"
        class="flex items-center justify-center size-10 rounded-lg border border-border-light hover:bg-background-light transition-colors"
      >
        <span class="material-symbols-outlined text-text-secondary"
          >arrow_back</span
        >
      </button>
      <div class="flex-1">
        <h1 class="text-text-main text-3xl font-bold leading-tight">
          Detail SK Yudisium
        </h1>
        <p class="text-text-secondary text-sm font-normal">
          Batch:
          {{
            batchInfo?.nomor_sk_batch || decodeURIComponent(route.params.nomor)
          }}
        </p>
      </div>
    </div>

    <!-- Batch Info Card -->
    <div
      class="bg-surface-light border border-border-light rounded-xl shadow-sm"
    >
      <div
        class="flex items-center justify-between p-4 border-b border-border-light"
      >
        <h3 class="text-text-main font-bold">Informasi Batch</h3>
        <button
          @click="openEditModal"
          class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-primary bg-primary/5 border border-primary/20 rounded-lg hover:bg-primary/10 transition-all"
        >
          <span class="material-symbols-outlined text-[14px]">edit</span>
          Edit
        </button>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-5 gap-4 p-6">
        <div>
          <p
            class="text-text-secondary text-xs font-bold uppercase tracking-wider mb-1"
          >
            Nomor SK
          </p>
          <p class="text-text-main font-semibold">
            {{
              batchInfo?.nomor_sk_batch ||
              decodeURIComponent(route.params.nomor)
            }}
          </p>
        </div>
        <div>
          <p
            class="text-text-secondary text-xs font-bold uppercase tracking-wider mb-1"
          >
            Tahun Akademik
          </p>
          <p class="text-text-main font-semibold">
            {{ resolvedTahunName }}
          </p>
        </div>
        <div>
          <p
            class="text-text-secondary text-xs font-bold uppercase tracking-wider mb-1"
          >
            Program Studi
          </p>
          <p class="text-text-main font-semibold">
            {{ resolvedProdiName }}
          </p>
        </div>
        <div>
          <p
            class="text-text-secondary text-xs font-bold uppercase tracking-wider mb-1"
          >
            Tanggal Terbit
          </p>
          <p class="text-text-main font-semibold">
            {{
              formatDate(
                batchInfo?.tanggal_terbit || route.query.tanggal_terbit,
              )
            }}
          </p>
        </div>
        <div>
          <p
            class="text-text-secondary text-xs font-bold uppercase tracking-wider mb-1"
          >
            Tanggal Yudisium
          </p>
          <p class="text-text-main font-semibold">
            {{
              formatDate(
                batchInfo?.tanggal_yudisium || route.query.tanggal_yudisium,
              )
            }}
          </p>
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="p-12 text-center">
      <div
        class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto"
      ></div>
      <p class="text-text-secondary text-sm mt-3">Memuat data...</p>
    </div>

    <template v-else>
      <!-- Table 1: Mahasiswa Sudah Ter-assign -->
      <div
        class="flex flex-col bg-surface-light border border-border-light rounded-xl shadow-sm"
      >
        <div class="p-5 border-b border-border-light">
          <h3 class="text-text-main text-lg font-bold flex items-center gap-2">
            <span class="material-symbols-outlined text-green-600"
              >check_circle</span
            >
            Mahasiswa Ter-assign
            <span
              class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-green-50 text-green-700 border border-green-200"
            >
              {{ assignedList.length }}
            </span>
          </h3>
          <p class="text-text-secondary text-sm">
            Mahasiswa yang sudah masuk dalam batch SK ini.
          </p>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm whitespace-nowrap">
            <thead
              class="bg-sidebar-light/50 text-text-secondary font-medium border-b border-border-light"
            >
              <tr>
                <th class="px-6 py-4">No</th>
                <th class="px-6 py-4">Mahasiswa</th>
                <th class="px-6 py-4">Program Studi</th>
                <th class="px-6 py-4">Nomor SK</th>
                <th class="px-6 py-4 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-border-light">
              <tr v-if="assignedList.length === 0">
                <td colspan="5" class="p-8 text-center text-text-secondary">
                  Belum ada mahasiswa yang ter-assign
                </td>
              </tr>
              <tr
                v-for="(item, index) in assignedList"
                :key="item.id"
                class="group hover:bg-sidebar-light/30 transition-colors"
              >
                <td class="px-6 py-4 font-medium text-text-main">
                  {{ index + 1 }}
                </td>
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
                <td class="px-6 py-4 text-text-secondary text-xs">
                  {{ item.skripsi?.mahasiswa?.prodi?.nama || "-" }}
                </td>
                <td class="px-6 py-4">
                  <span
                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-blue-50 text-blue-700"
                  >
                    {{ item.nomor_sk }}
                  </span>
                </td>
                <td class="px-6 py-4 text-right">
                  <button
                    @click="removeFromBatch(item)"
                    :disabled="removingId === item.id"
                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-all disabled:opacity-50"
                  >
                    <span class="material-symbols-outlined text-[14px]"
                      >delete</span
                    >
                    {{ removingId === item.id ? "Menghapus..." : "Hapus" }}
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Table 2: Mahasiswa Siap Yudisium (Belum Di-assign) -->
      <div
        class="flex flex-col bg-surface-light border border-border-light rounded-xl shadow-sm"
      >
        <div class="p-5 border-b border-border-light space-y-3">
          <div
            class="flex flex-col md:flex-row gap-3 items-center justify-between"
          >
            <div>
              <h3
                class="text-text-main text-lg font-bold flex items-center gap-2"
              >
                <span class="material-symbols-outlined text-orange-500"
                  >pending</span
                >
                Mahasiswa Siap Yudisium
                <span
                  class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-orange-50 text-orange-700 border border-orange-200"
                >
                  {{ unassignedList.length }}
                </span>
              </h3>
              <p class="text-text-secondary text-sm">
                Mahasiswa yang sudah lulus sidang dan belum memiliki SK
                Yudisium.
              </p>
            </div>
            <div class="flex gap-3 w-full md:w-auto">
              <div class="relative w-full md:w-64">
                <input
                  v-model="unassignedSearch"
                  @input="fetchData"
                  class="w-full pl-10 pr-4 py-2 rounded-lg border border-border-light dark:border-white/10 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all bg-background-light dark:bg-surface-light text-text-main placeholder-text-secondary"
                  placeholder="Cari mahasiswa..."
                />
                <span
                  class="material-symbols-outlined absolute left-3 top-2.5 text-[18px] text-text-secondary"
                  >search</span
                >
              </div>
            </div>
          </div>

          <!-- Filter bar -->
          <div class="flex flex-wrap gap-2 items-center">
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
            <select
              v-model="filterJenisKelamin"
              @change="applyFilters"
              class="px-3 py-2 bg-white dark:bg-surface-light border border-border-light dark:border-white/10 rounded-lg text-text-main text-xs focus:ring-1 focus:ring-primary transition-colors"
            >
              <option value="">Semua Jenis Kelamin</option>
              <option value="L">Laki-laki</option>
              <option value="P">Perempuan</option>
            </select>
            <button
              v-if="hasActiveFilters"
              @click="clearFilters"
              class="flex items-center gap-1 px-3 py-2 text-xs text-red-400 hover:text-red-300 hover:bg-red-500/10 rounded-lg transition-colors"
            >
              <span class="material-symbols-outlined text-[14px]">close</span>
              Reset
            </button>
          </div>

          <!-- Selection status bar -->
          <div
            v-if="selectedIds.length > 0"
            class="flex items-center justify-between bg-primary/5 border border-primary/20 rounded-lg px-4 py-3"
          >
            <p class="text-sm font-semibold text-primary">
              {{ selectedIds.length }} mahasiswa dipilih
            </p>
            <button
              @click="submitAssign"
              :disabled="assigning"
              class="inline-flex items-center gap-1 px-4 py-2 text-xs font-bold text-white bg-primary rounded-lg hover:bg-primary/90 transition-all shadow-sm disabled:opacity-50"
            >
              <span class="material-symbols-outlined text-[16px]"
                >assignment_turned_in</span
              >
              {{
                assigning
                  ? "Menyimpan..."
                  : `Assign ${selectedIds.length} Mahasiswa`
              }}
            </button>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm whitespace-nowrap">
            <thead
              class="bg-sidebar-light/50 text-text-secondary font-medium border-b border-border-light"
            >
              <tr>
                <th class="px-6 py-4">
                  <label class="flex items-center gap-2 cursor-pointer">
                    <input
                      type="checkbox"
                      :checked="isAllSelected"
                      :indeterminate="isIndeterminate"
                      @change="toggleSelectAll"
                      class="rounded border-border-light text-primary focus:ring-primary/30 cursor-pointer h-4 w-4"
                    />
                    <span class="text-xs">Pilih Semua</span>
                  </label>
                </th>
                <th class="px-6 py-4">Mahasiswa</th>
                <th class="px-6 py-4">Program Studi</th>
                <th class="px-6 py-4">Judul Skripsi</th>
                <th class="px-6 py-4">Tanggal Ujian</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-border-light">
              <tr v-if="unassignedList.length === 0">
                <td colspan="5" class="p-8 text-center text-text-secondary">
                  Tidak ada mahasiswa yang siap yudisium
                </td>
              </tr>
              <tr
                v-for="item in unassignedList"
                :key="item.skripsi?.id"
                class="group hover:bg-sidebar-light/30 transition-colors"
                :class="{
                  'bg-primary/5': selectedIds.includes(item.skripsi?.id),
                }"
              >
                <td class="px-6 py-4">
                  <input
                    type="checkbox"
                    :checked="selectedIds.includes(item.skripsi?.id)"
                    @change="toggleSelect(item.skripsi?.id)"
                    class="rounded border-border-light text-primary focus:ring-primary/30 cursor-pointer h-4 w-4"
                  />
                </td>
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
                <td class="px-6 py-4 text-text-secondary text-xs">
                  {{ item.skripsi?.mahasiswa?.prodi?.nama || "-" }}
                </td>
                <td
                  class="px-6 py-4 max-w-xs truncate text-text-main text-xs"
                  :title="item.skripsi?.judul"
                >
                  {{ item.skripsi?.judul || "-" }}
                </td>
                <td class="px-6 py-4 text-xs font-medium text-text-secondary">
                  {{ formatDate(item.tanggal) }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </template>

    <!-- ===== MODAL: Edit Batch ===== -->
    <div
      v-if="showEditModal"
      class="fixed inset-0 z-50 flex items-center justify-center p-4"
    >
      <div
        class="absolute inset-0 bg-black/50 backdrop-blur-sm"
        @click="showEditModal = false"
      ></div>
      <div
        class="relative bg-surface-light rounded-xl shadow-xl border border-border-light w-full max-w-md"
      >
        <div class="p-6 border-b border-border-light">
          <h2 class="text-xl font-bold text-text-main">Edit Batch</h2>
          <p class="text-sm text-text-secondary mt-1">
            Edit informasi batch SK Yudisium.
          </p>
        </div>
        <form @submit.prevent="submitEdit" class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-medium text-text-main mb-1"
              >Nomor SK</label
            >
            <input
              :value="
                batchInfo?.nomor_sk_batch ||
                decodeURIComponent(route.params.nomor)
              "
              disabled
              class="w-full px-3 py-2 border border-border-light rounded-lg bg-gray-100 dark:bg-gray-800 text-text-secondary cursor-not-allowed"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-text-main mb-1"
              >Tahun Akademik</label
            >
            <select
              v-model="editForm.th_akademik_id"
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
              v-model="editForm.prodi_id"
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
              v-model="editForm.tanggal_terbit"
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
              v-model="editForm.tanggal_yudisium"
              type="date"
              class="w-full px-3 py-2 border border-border-light dark:border-white/10 rounded-lg bg-white dark:bg-surface-light text-text-main focus:ring-2 focus:ring-primary/20 focus:border-primary"
              required
            />
          </div>
          <div class="flex gap-3 pt-4">
            <button
              type="button"
              @click="showEditModal = false"
              class="flex-1 px-4 py-2.5 border border-border-light rounded-lg text-text-secondary hover:bg-background-light transition-colors"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="savingEdit"
              class="flex-1 px-4 py-2.5 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors disabled:opacity-50"
            >
              {{ savingEdit ? "Menyimpan..." : "Simpan" }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import adminService from "../../../services/adminService";

const route = useRoute();
const router = useRouter();

const loading = ref(true);
const assigning = ref(false);
const removingId = ref(null);
const unassignedSearch = ref("");

// Filter state
const filterFakultas = ref("");
const filterProdi = ref("");
const filterJenisKelamin = ref("");
const fakultasList = ref([]);
const prodiList = ref([]);

const batchInfo = ref(null);
const assignedList = ref([]);
const unassignedList = ref([]);
const selectedIds = ref([]);
const tahunList = ref([]);

// Edit modal state
const showEditModal = ref(false);
const savingEdit = ref(false);
const editForm = reactive({
  th_akademik_id: "",
  prodi_id: "",
  tanggal_terbit: "",
  tanggal_yudisium: "",
});

const filteredProdiList = computed(() => {
  if (!filterFakultas.value) return prodiList.value;
  return prodiList.value.filter((p) => p.fakultas_id == filterFakultas.value);
});

const resolvedTahunName = computed(() => {
  if (batchInfo.value?.tahun_akademik_name)
    return batchInfo.value.tahun_akademik_name;
  const id = route.query.th_akademik_id;
  if (!id) return "-";
  const found = tahunList.value.find((t) => t.id == id);
  return found ? found.name : "-";
});

const resolvedProdiName = computed(() => {
  if (batchInfo.value?.prodi_name) return batchInfo.value.prodi_name;
  const id = route.query.prodi_id;
  if (!id) return "-";
  const found = prodiList.value.find((p) => p.id == id);
  return found ? found.nama : "-";
});

const hasActiveFilters = computed(() => {
  return filterFakultas.value || filterProdi.value || filterJenisKelamin.value;
});

const isAllSelected = computed(() => {
  if (unassignedList.value.length === 0) return false;
  return unassignedList.value.every((item) =>
    selectedIds.value.includes(item.skripsi?.id),
  );
});

const isIndeterminate = computed(() => {
  return (
    selectedIds.value.length > 0 &&
    selectedIds.value.length < unassignedList.value.length
  );
});

const toggleSelectAll = () => {
  if (isAllSelected.value) {
    selectedIds.value = [];
  } else {
    selectedIds.value = unassignedList.value
      .map((item) => item.skripsi?.id)
      .filter(Boolean);
  }
};

const toggleSelect = (id) => {
  const idx = selectedIds.value.indexOf(id);
  if (idx > -1) {
    selectedIds.value.splice(idx, 1);
  } else {
    selectedIds.value.push(id);
  }
};

const fetchData = async () => {
  try {
    loading.value = true;
    const nomor = decodeURIComponent(route.params.nomor);
    const params = {};
    if (unassignedSearch.value) params.search = unassignedSearch.value;
    if (filterFakultas.value) params.fakultas_id = filterFakultas.value;
    if (filterProdi.value) params.prodi_id = filterProdi.value;
    if (filterJenisKelamin.value)
      params.jenis_kelamin = filterJenisKelamin.value;

    const response = await adminService.getSKYudisiumBatchDetail(nomor, params);
    if (response.success) {
      batchInfo.value = response.batch_info;
      assignedList.value = response.assigned || [];
      unassignedList.value = response.unassigned || [];
    }
  } catch (error) {
    console.error("Failed to fetch batch detail:", error);
    alert(
      "Gagal memuat data batch: " +
        (error.response?.data?.message || error.message),
    );
  } finally {
    loading.value = false;
  }
};

const applyFilters = () => {
  fetchData();
};

const onFakultasChange = () => {
  filterProdi.value = "";
  applyFilters();
};

const clearFilters = () => {
  filterFakultas.value = "";
  filterProdi.value = "";
  filterJenisKelamin.value = "";
  applyFilters();
};

const loadFilterData = async () => {
  try {
    const [fakultasRes, prodiRes, tahunRes] = await Promise.all([
      adminService.getFakultas(),
      adminService.getProdi(),
      adminService.getTahun(),
    ]);
    fakultasList.value = fakultasRes.data || [];
    prodiList.value = prodiRes.data || [];
    tahunList.value = tahunRes.data || [];
  } catch (error) {
    console.error("Failed to load filter data:", error);
  }
};

const removeFromBatch = async (item) => {
  if (
    !confirm(
      `Yakin ingin mengeluarkan ${item.skripsi?.mahasiswa?.nama || "mahasiswa ini"} dari batch?`,
    )
  ) {
    return;
  }
  try {
    removingId.value = item.id;
    await adminService.removeSKYudisiumBatch(item.id);
    await fetchData();
  } catch (error) {
    console.error("Failed to remove from batch:", error);
    alert(
      "Gagal menghapus: " + (error.response?.data?.message || error.message),
    );
  } finally {
    removingId.value = null;
  }
};

const submitAssign = async () => {
  try {
    assigning.value = true;
    const nomor = decodeURIComponent(route.params.nomor);

    const payload = {
      nomor_sk_batch: nomor,
      th_akademik_id:
        batchInfo.value?.th_akademik_id || route.query.th_akademik_id,
      prodi_id: batchInfo.value?.prodi_id || route.query.prodi_id || null,
      tanggal_terbit:
        batchInfo.value?.tanggal_terbit || route.query.tanggal_terbit,
      tanggal_yudisium:
        batchInfo.value?.tanggal_yudisium || route.query.tanggal_yudisium,
      skripsi_ids: selectedIds.value,
    };

    const response = await adminService.assignSKYudisiumBatch(payload);
    alert(response.message || "Berhasil assign mahasiswa ke batch");

    // Reset state
    selectedIds.value = [];

    // Reload data
    await fetchData();
  } catch (error) {
    console.error("Failed to assign batch:", error);
    alert("Gagal assign: " + (error.response?.data?.message || error.message));
  } finally {
    assigning.value = false;
  }
};

const openEditModal = () => {
  // Pre-fill form with current batch info
  const info = batchInfo.value;
  editForm.th_akademik_id =
    info?.th_akademik_id || route.query.th_akademik_id || "";
  editForm.prodi_id = info?.prodi_id || route.query.prodi_id || "";
  editForm.tanggal_terbit = info?.tanggal_terbit
    ? info.tanggal_terbit.substring(0, 10)
    : route.query.tanggal_terbit || "";
  editForm.tanggal_yudisium = info?.tanggal_yudisium
    ? info.tanggal_yudisium.substring(0, 10)
    : route.query.tanggal_yudisium || "";
  showEditModal.value = true;
};

const submitEdit = async () => {
  try {
    savingEdit.value = true;
    const nomor = decodeURIComponent(route.params.nomor);
    await adminService.updateSKYudisiumBatch(nomor, {
      th_akademik_id: editForm.th_akademik_id,
      prodi_id: editForm.prodi_id || null,
      tanggal_terbit: editForm.tanggal_terbit,
      tanggal_yudisium: editForm.tanggal_yudisium,
    });
    showEditModal.value = false;
    alert("Batch berhasil diperbarui");
    await fetchData();
  } catch (error) {
    console.error("Failed to update batch:", error);
    alert(
      "Gagal memperbarui batch: " +
        (error.response?.data?.message || error.message),
    );
  } finally {
    savingEdit.value = false;
  }
};

const formatDate = (date) => {
  if (!date) return "-";
  return new Date(date).toLocaleDateString("id-ID", {
    day: "numeric",
    month: "short",
    year: "numeric",
  });
};

const formatPredikat = (predikat) => {
  if (!predikat) return "-";
  return predikat
    .split("_")
    .map((w) => w.charAt(0).toUpperCase() + w.slice(1))
    .join(" ");
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

onMounted(() => {
  loadFilterData();
  fetchData();
});
</script>

<style scoped>
.slide-fade-enter-active {
  transition: all 0.3s ease-out;
}
.slide-fade-leave-active {
  transition: all 0.2s ease-in;
}
.slide-fade-enter-from {
  transform: translateY(-10px);
  opacity: 0;
}
.slide-fade-leave-to {
  transform: translateY(-10px);
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
