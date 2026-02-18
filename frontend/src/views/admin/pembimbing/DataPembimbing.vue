<template>
  <div class="max-w-7xl mx-auto flex flex-col gap-8 animate-fade-in-up">
    <div class="flex flex-col gap-1">
      <h1 class="text-text-main text-3xl font-bold leading-tight">
        Penetapan Pembimbing
      </h1>
      <p class="text-text-secondary text-sm font-normal">
        Kelola pengajuan judul skripsi dan penetapan dosen pembimbing mahasiswa.
      </p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div
        class="flex flex-col gap-2 rounded-xl p-6 bg-surface-light border border-border-light shadow-sm hover:shadow-md transition-all hover:-translate-y-1 duration-300"
      >
        <div class="flex items-center justify-between">
          <p
            class="text-text-secondary text-xs font-bold uppercase tracking-wider"
          >
            Total Mahasiswa
          </p>
          <div class="bg-blue-100 p-2 rounded-lg text-blue-600">
            <span class="material-symbols-outlined">people</span>
          </div>
        </div>
        <div class="mt-2">
          <p class="text-text-main text-3xl font-bold leading-tight">
            {{ statsCount.total }}
          </p>
          <div class="flex items-center gap-1 mt-1">
            <span class="material-symbols-outlined text-primary text-[18px]"
              >school</span
            >
            <p class="text-primary text-xs font-semibold">
              Mahasiswa berstatus bimbingan
            </p>
          </div>
        </div>
      </div>

      <div
        class="flex flex-col gap-2 rounded-xl p-6 bg-surface-light border border-border-light shadow-sm hover:shadow-md transition-all hover:-translate-y-1 duration-300"
      >
        <div class="flex items-center justify-between">
          <p
            class="text-text-secondary text-xs font-bold uppercase tracking-wider"
          >
            Sudah Ditetapkan
          </p>
          <div class="bg-green-100 p-2 rounded-lg text-green-600">
            <span class="material-symbols-outlined">how_to_reg</span>
          </div>
        </div>
        <div class="mt-2">
          <p class="text-text-main text-3xl font-bold leading-tight">
            {{ statsCount.sudah }}
          </p>
          <div class="flex items-center gap-1 mt-1">
            <span class="material-symbols-outlined text-green-600 text-[18px]"
              >check_circle</span
            >
            <p class="text-green-600 text-xs font-semibold">Ada pembimbing</p>
          </div>
        </div>
      </div>

      <div
        class="flex flex-col gap-2 rounded-xl p-6 bg-surface-light border border-border-light shadow-sm hover:shadow-md transition-all hover:-translate-y-1 duration-300"
      >
        <div class="flex items-center justify-between">
          <p
            class="text-text-secondary text-xs font-bold uppercase tracking-wider"
          >
            Belum Ditetapkan
          </p>
          <div class="bg-orange-100 p-2 rounded-lg text-orange-600">
            <span class="material-symbols-outlined">person_off</span>
          </div>
        </div>
        <div class="mt-2">
          <p class="text-text-main text-3xl font-bold leading-tight">
            {{ statsCount.belum }}
          </p>
          <div class="flex items-center gap-1 mt-1">
            <span class="material-symbols-outlined text-orange-600 text-[18px]"
              >pending</span
            >
            <p class="text-orange-600 text-xs font-semibold">
              Menunggu penetapan
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Filter/Action Bar -->
    <div
      class="flex flex-col bg-surface-light border border-border-light rounded-xl shadow-sm"
    >
      <div
        class="p-5 border-b border-border-light flex flex-col md:flex-row gap-4 items-center justify-between"
      >
        <div>
          <h3 class="text-text-main text-lg font-bold">
            Data Pembimbing Skripsi
          </h3>
          <p class="text-text-secondary text-xs">
            Menampilkan daftar mahasiswa dengan status bimbingan.
          </p>
        </div>
        <div class="flex gap-2 items-center flex-wrap">
          <!-- Search -->
          <div class="relative">
            <span
              class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-text-secondary text-[18px]"
              >search</span
            >
            <input
              v-model="searchQuery"
              @input="debounceFetch"
              type="text"
              placeholder="Cari nama, NIM, judul..."
              class="pl-10 pr-4 py-2 border border-border-light rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary w-56"
            />
          </div>
          <!-- Filter Pembimbing -->
          <select
            v-model="filterPembimbing"
            @change="fetchPembimbing"
            class="px-3 py-2 border border-border-light rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary"
          >
            <option value="">Semua</option>
            <option value="sudah">Sudah Ada Pembimbing</option>
            <option value="belum">Belum Ada Pembimbing</option>
          </select>
          <button
            @click="fetchPembimbing"
            class="flex items-center gap-2 px-4 py-2 bg-primary/10 text-primary rounded-lg text-xs font-bold hover:bg-primary/20 transition-colors"
          >
            <span class="material-symbols-outlined text-[16px]">refresh</span>
            Refresh
          </button>
        </div>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="p-12 text-center">
        <div
          class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto"
        ></div>
        <p class="text-text-secondary text-sm mt-3">Memuat data...</p>
      </div>

      <!-- Table -->
      <div v-else class="overflow-x-auto">
        <table class="w-full text-left text-sm table-fixed">
          <thead
            class="bg-sidebar-light/50 text-text-secondary font-medium border-b border-border-light"
          >
            <tr>
              <th class="px-6 py-4 whitespace-nowrap w-[22%]">Mahasiswa</th>
              <th class="px-6 py-4 w-[33%]">Judul Skripsi</th>
              <th class="px-6 py-4 w-[28%]">Pembimbing</th>
              <th class="px-6 py-4 text-right whitespace-nowrap w-[17%]">
                Aksi
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border-light">
            <tr v-if="skripsiList.length === 0">
              <td colspan="4" class="p-12 text-center text-text-secondary">
                Tidak ada mahasiswa ditemukan
              </td>
            </tr>
            <tr
              v-for="item in skripsiList"
              :key="item.id"
              class="group hover:bg-sidebar-light/30 transition-colors"
            >
              <td class="px-6 py-4 align-middle">
                <div class="flex items-center gap-3">
                  <div
                    class="size-10 rounded-full flex items-center justify-center text-xs font-bold shrink-0"
                    :class="getAvatarColor(item.mahasiswa?.nama)"
                  >
                    {{ getInitials(item.mahasiswa?.nama) }}
                  </div>
                  <div>
                    <p
                      class="font-bold text-text-main text-sm whitespace-nowrap"
                    >
                      {{ item.mahasiswa?.nama || "-" }}
                    </p>
                    <p class="text-xs text-text-secondary font-medium">
                      {{ item.mahasiswa?.nim || "-" }}
                    </p>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 align-middle">
                <p
                  class="text-sm font-medium text-text-main leading-relaxed break-words line-clamp-2"
                >
                  {{ item.judul || "-" }}
                </p>
              </td>
              <td class="px-6 py-4 align-middle">
                <div
                  v-if="item.pembimbing && item.pembimbing.length > 0"
                  class="space-y-1"
                >
                  <div
                    v-for="p in item.pembimbing"
                    :key="p.id"
                    class="flex items-center gap-2"
                  >
                    <div
                      class="size-6 rounded-full flex items-center justify-center text-[9px] font-bold shrink-0"
                      :class="getAvatarColor(p.dosen?.nama)"
                    >
                      {{ getInitials(p.dosen?.nama) }}
                    </div>
                    <div class="min-w-0">
                      <p class="text-xs font-medium text-text-main truncate">
                        {{ p.dosen?.nama_lengkap || p.dosen?.nama || "-" }}
                      </p>
                      <p class="text-[10px] text-text-secondary capitalize">
                        {{
                          p.jenis === "pembimbing_1"
                            ? "Pembimbing 1"
                            : "Pembimbing 2"
                        }}
                      </p>
                    </div>
                  </div>
                </div>
                <span
                  v-else
                  class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-medium bg-yellow-50 text-yellow-700 border border-yellow-200"
                >
                  <span class="material-symbols-outlined text-[12px]"
                    >hourglass_empty</span
                  >
                  Belum ditetapkan
                </span>
              </td>
              <td class="px-6 py-4 align-middle text-right">
                <div class="flex items-center justify-end gap-2 flex-wrap">
                  <button
                    v-if="item.pembimbing && item.pembimbing.length > 0"
                    @click="openAssignModal(item)"
                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-orange-600 bg-orange-50 rounded-lg hover:bg-orange-100 border border-orange-200 transition-all"
                  >
                    <span class="material-symbols-outlined text-[14px]"
                      >edit</span
                    >
                    Edit
                  </button>
                  <button
                    v-else
                    @click="openAssignModal(item)"
                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-white bg-primary rounded-lg hover:bg-primary/90 transition-all shadow-sm shadow-primary/20"
                  >
                    <span class="material-symbols-outlined text-[14px]"
                      >person_add</span
                    >
                    Tetapkan
                  </button>
                  <button
                    v-if="item.pembimbing && item.pembimbing.length > 0"
                    @click="deleteAllPembimbing(item)"
                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-red-600 bg-red-50 rounded-lg hover:bg-red-100 border border-red-200 transition-all"
                  >
                    <span class="material-symbols-outlined text-[14px]"
                      >delete</span
                    >
                    Hapus
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Footer -->
      <div
        class="flex items-center justify-between px-6 py-4 border-t border-border-light"
      >
        <p class="text-sm text-text-secondary">
          Showing
          <span class="font-medium text-text-main"
            >{{ pagination.from || 0 }}-{{ pagination.to || 0 }}</span
          >
          of
          <span class="font-medium text-text-main">{{ pagination.total }}</span>
          mahasiswa
        </p>
        <div class="flex gap-2">
          <button
            @click="goToPage(pagination.current_page - 1)"
            :disabled="pagination.current_page <= 1"
            class="size-8 flex items-center justify-center rounded-md border border-border-light bg-white text-text-secondary hover:bg-gray-50 transition-colors disabled:opacity-50"
          >
            <span class="material-symbols-outlined text-[16px]"
              >chevron_left</span
            >
          </button>
          <button
            @click="goToPage(pagination.current_page + 1)"
            :disabled="pagination.current_page >= pagination.last_page"
            class="size-8 flex items-center justify-center rounded-md border border-border-light bg-white text-text-secondary hover:bg-gray-50 transition-colors"
          >
            <span class="material-symbols-outlined text-[16px]"
              >chevron_right</span
            >
          </button>
        </div>
      </div>
    </div>

    <!-- Assign Pembimbing Modal -->
    <Transition name="modal-fade">
      <div
        v-if="showAssignModal"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
      >
        <div
          class="bg-white dark:bg-surface-light rounded-xl shadow-2xl w-full max-w-lg"
        >
          <div class="p-6 border-b border-border-light">
            <h2 class="text-xl font-bold text-text-main">
              {{ isEditMode ? "Edit Pembimbing" : "Tetapkan Pembimbing" }}
            </h2>
            <p class="text-sm text-text-secondary mt-1">
              {{ selectedSkripsi?.mahasiswa?.nama }} -
              {{ selectedSkripsi?.judul }}
            </p>
          </div>
          <form @submit.prevent="assignPembimbing" class="p-6 space-y-5">
            <!-- Bidang Keahlian Filter -->
            <div>
              <label class="block text-sm font-medium text-text-main mb-1"
                >Filter Bidang Keahlian</label
              >
              <select
                v-model="selectedBidang"
                @change="filterDosenByBidang"
                class="w-full px-3 py-2 border border-border-light rounded-lg bg-white text-sm"
                style="color: #000"
              >
                <option value="">Semua Bidang</option>
                <option
                  v-for="bidang in bidangList"
                  :key="bidang"
                  :value="bidang"
                  style="color: #000"
                >
                  {{ bidang }}
                </option>
              </select>
            </div>

            <!-- Pembimbing 1 -->
            <div>
              <label class="block text-sm font-medium text-text-main mb-1"
                >Pembimbing 1 <span class="text-red-500">*</span></label
              >
              <Multiselect
                v-model="selectedPembimbing1"
                :options="filteredDosenList"
                :custom-label="dosenLabel"
                track-by="id"
                placeholder="Cari dan pilih dosen..."
                :allow-empty="false"
                :show-labels="false"
                :searchable="true"
                :option-height="60"
              >
                <template #option="{ option }">
                  <div
                    class="dosen-option"
                    :class="{ 'dosen-disabled': !option.is_available }"
                  >
                    <div class="dosen-option-name">
                      {{ option.nama_lengkap }}
                    </div>
                    <div class="dosen-option-meta">
                      <span
                        v-if="option.bidang_keahlian"
                        class="dosen-bidang"
                        >{{ option.bidang_keahlian }}</span
                      >
                      <span
                        class="dosen-kuota"
                        :class="option.is_available ? 'kuota-ok' : 'kuota-full'"
                      >
                        {{ option.current_bimbingan }}/{{
                          option.kuota_bimbingan
                        }}
                        slot
                      </span>
                      <span v-if="!option.is_available" class="kuota-badge"
                        >Kuota Penuh</span
                      >
                    </div>
                  </div>
                </template>
                <template #singleLabel="{ option }">
                  <div class="selected-dosen">
                    <span class="selected-name">{{ option.nama_lengkap }}</span>
                    <span class="selected-slot"
                      >{{ option.current_bimbingan }}/{{
                        option.kuota_bimbingan
                      }}</span
                    >
                  </div>
                </template>
                <template #noResult>
                  <span>Dosen tidak ditemukan</span>
                </template>
              </Multiselect>
            </div>

            <!-- Pembimbing 2 -->
            <div>
              <label class="block text-sm font-medium text-text-main mb-1"
                >Pembimbing 2 (Opsional)</label
              >
              <Multiselect
                v-model="selectedPembimbing2"
                :options="filteredDosenList"
                :custom-label="dosenLabel"
                track-by="id"
                placeholder="Cari dan pilih dosen..."
                :allow-empty="true"
                :show-labels="false"
                :searchable="true"
                :option-height="60"
              >
                <template #option="{ option }">
                  <div
                    class="dosen-option"
                    :class="{ 'dosen-disabled': !option.is_available }"
                  >
                    <div class="dosen-option-name">
                      {{ option.nama_lengkap }}
                    </div>
                    <div class="dosen-option-meta">
                      <span
                        v-if="option.bidang_keahlian"
                        class="dosen-bidang"
                        >{{ option.bidang_keahlian }}</span
                      >
                      <span
                        class="dosen-kuota"
                        :class="option.is_available ? 'kuota-ok' : 'kuota-full'"
                      >
                        {{ option.current_bimbingan }}/{{
                          option.kuota_bimbingan
                        }}
                        slot
                      </span>
                      <span v-if="!option.is_available" class="kuota-badge"
                        >Kuota Penuh</span
                      >
                    </div>
                  </div>
                </template>
                <template #singleLabel="{ option }">
                  <div class="selected-dosen">
                    <span class="selected-name">{{ option.nama_lengkap }}</span>
                    <span class="selected-slot"
                      >{{ option.current_bimbingan }}/{{
                        option.kuota_bimbingan
                      }}</span
                    >
                  </div>
                </template>
                <template #noResult>
                  <span>Dosen tidak ditemukan</span>
                </template>
              </Multiselect>
            </div>

            <div class="flex gap-3 pt-4">
              <button
                type="button"
                @click="showAssignModal = false"
                class="flex-1 px-4 py-2.5 border border-border-light rounded-lg text-text-secondary hover:bg-background-light transition-colors"
              >
                Batal
              </button>
              <button
                type="submit"
                :disabled="saving || !selectedPembimbing1"
                class="flex-1 px-4 py-2.5 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors disabled:opacity-50"
              >
                {{
                  saving
                    ? "Menyimpan..."
                    : isEditMode
                      ? "Simpan Perubahan"
                      : "Tetapkan"
                }}
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
import Multiselect from "vue-multiselect";
import "vue-multiselect/dist/vue-multiselect.css";
import adminService from "../../../services/adminService";

const loading = ref(true);
const saving = ref(false);
const skripsiList = ref([]);
const dosenList = ref([]);
const bidangList = ref([]);
const showAssignModal = ref(false);
const selectedSkripsi = ref(null);
const selectedBidang = ref("");
const isEditMode = ref(false);
const selectedPembimbing1 = ref(null);
const selectedPembimbing2 = ref(null);

const pagination = reactive({
  current_page: 1,
  last_page: 1,
  total: 0,
  from: 0,
  to: 0,
});

const statsCount = reactive({
  total: 0,
  sudah: 0,
  belum: 0,
});

const fetchStats = async () => {
  try {
    const [allRes, sudahRes, belumRes] = await Promise.all([
      adminService.getPendingPembimbing({ per_page: 1 }),
      adminService.getPendingPembimbing({
        per_page: 1,
        pembimbing_status: "sudah",
      }),
      adminService.getPendingPembimbing({
        per_page: 1,
        pembimbing_status: "belum",
      }),
    ]);
    statsCount.total = allRes.data.total || 0;
    statsCount.sudah = sudahRes.data.total || 0;
    statsCount.belum = belumRes.data.total || 0;
  } catch (error) {
    console.error("Failed to fetch stats:", error);
  }
};

// Filter dosen by bidang keahlian and exclude full kuota from selectable
const filteredDosenList = computed(() => {
  let list = dosenList.value;
  if (selectedBidang.value) {
    list = list.filter(
      (d) =>
        d.bidang_keahlian &&
        d.bidang_keahlian
          .toLowerCase()
          .includes(selectedBidang.value.toLowerCase()),
    );
  }
  // Sort: available first, then full kuota
  return [...list].sort((a, b) => {
    if (a.is_available && !b.is_available) return -1;
    if (!a.is_available && b.is_available) return 1;
    return 0;
  });
});

const dosenLabel = (dosen) => {
  return dosen.nama_lengkap;
};

const searchQuery = ref("");
const filterPembimbing = ref("");
let debounceTimer = null;

const debounceFetch = () => {
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(() => {
    pagination.current_page = 1;
    fetchPembimbing();
  }, 400);
};

const fetchPembimbing = async () => {
  try {
    loading.value = true;
    const params = {
      page: pagination.current_page,
    };
    if (searchQuery.value) params.search = searchQuery.value;
    if (filterPembimbing.value)
      params.pembimbing_status = filterPembimbing.value;
    const response = await adminService.getPendingPembimbing(params);
    if (response.success) {
      const data = response.data.data || response.data;
      skripsiList.value = Array.isArray(data) ? data : [];
      if (response.data.current_page) {
        Object.assign(pagination, {
          current_page: response.data.current_page,
          last_page: response.data.last_page,
          total: response.data.total,
          from: response.data.from,
          to: response.data.to,
        });
      }
    }
  } catch (error) {
    console.error("Failed to fetch pembimbing data:", error);
  } finally {
    loading.value = false;
  }
};

const fetchAvailableDosen = async () => {
  try {
    const response = await adminService.getAvailableDosen();
    if (response.success) {
      dosenList.value = Array.isArray(response.data)
        ? response.data
        : Object.values(response.data);
      bidangList.value = response.bidang_list || [];
    }
  } catch (error) {
    console.error("Failed to fetch available dosen:", error);
  }
};

const filterDosenByBidang = () => {
  // Bidang filtering is handled reactively via computed property
  // Reset selected if they no longer match filter
  if (selectedPembimbing1.value && selectedBidang.value) {
    const match = filteredDosenList.value.find(
      (d) => d.id === selectedPembimbing1.value.id,
    );
    if (!match) selectedPembimbing1.value = null;
  }
  if (selectedPembimbing2.value && selectedBidang.value) {
    const match = filteredDosenList.value.find(
      (d) => d.id === selectedPembimbing2.value.id,
    );
    if (!match) selectedPembimbing2.value = null;
  }
};

const goToPage = (page) => {
  if (page >= 1 && page <= pagination.last_page) {
    pagination.current_page = page;
    fetchPembimbing();
  }
};

const openAssignModal = async (item) => {
  selectedSkripsi.value = item;
  selectedPembimbing1.value = null;
  selectedPembimbing2.value = null;
  selectedBidang.value = "";
  isEditMode.value = item.pembimbing && item.pembimbing.length > 0;
  showAssignModal.value = true;
  await fetchAvailableDosen();

  // Pre-fill selects with existing pembimbing when editing
  if (isEditMode.value && item.pembimbing) {
    const p1 = item.pembimbing.find((p) => p.jenis === "pembimbing_1");
    const p2 = item.pembimbing.find((p) => p.jenis === "pembimbing_2");
    if (p1) {
      selectedPembimbing1.value =
        dosenList.value.find((d) => d.id === p1.dosen_id) || null;
    }
    if (p2) {
      selectedPembimbing2.value =
        dosenList.value.find((d) => d.id === p2.dosen_id) || null;
    }
  }
};

const deleteAllPembimbing = async (item) => {
  if (!confirm(`Hapus semua pembimbing dari ${item.mahasiswa?.nama}?`)) return;
  try {
    for (const p of item.pembimbing) {
      const api = (await import("../../../services/api")).default;
      await api.delete(`/admin/pembimbing/${p.id}`);
    }
    fetchPembimbing();
    fetchStats();
  } catch (error) {
    console.error("Failed to delete pembimbing:", error);
    alert(
      "Gagal menghapus pembimbing: " +
        (error.response?.data?.message || error.message),
    );
  }
};

const assignPembimbing = async () => {
  if (!selectedPembimbing1.value) return;

  // Check kuota availability before submit
  if (!selectedPembimbing1.value.is_available) {
    alert("Dosen pembimbing 1 yang dipilih sudah memenuhi kuota bimbingan.");
    return;
  }
  if (selectedPembimbing2.value && !selectedPembimbing2.value.is_available) {
    alert("Dosen pembimbing 2 yang dipilih sudah memenuhi kuota bimbingan.");
    return;
  }
  if (
    selectedPembimbing2.value &&
    selectedPembimbing1.value.id === selectedPembimbing2.value.id
  ) {
    alert("Pembimbing 1 dan 2 tidak boleh sama.");
    return;
  }

  try {
    saving.value = true;
    await adminService.assignPembimbing(selectedSkripsi.value.id, {
      pembimbing_1_id: selectedPembimbing1.value.id,
      pembimbing_2_id: selectedPembimbing2.value?.id || null,
    });
    showAssignModal.value = false;
    fetchPembimbing();
    fetchStats();
  } catch (error) {
    console.error("Failed to assign pembimbing:", error);
    alert(
      "Gagal menetapkan pembimbing: " +
        (error.response?.data?.message || error.message),
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

const getStatusClass = (status) => {
  const classes = {
    proposal: "bg-yellow-50 text-yellow-600 border border-yellow-100",
    bimbingan: "bg-purple-50 text-purple-600 border border-purple-100",
    sempro: "bg-blue-50 text-blue-600 border border-blue-100",
  };
  return classes[status] || "bg-gray-50 text-gray-600 border border-gray-100";
};

const getStatusLabel = (status) => {
  const labels = {
    proposal: "Baru Isi Judul",
    bimbingan: "Bimbingan",
    sempro: "Seminar Proposal",
  };
  return labels[status] || status;
};

onMounted(() => {
  fetchPembimbing();
  fetchStats();
});
</script>

<style>
/* Vue Multiselect Custom Styles */
.multiselect {
  min-height: 42px;
  border: 1px solid var(--border) !important;
  border-radius: 0.5rem !important;
  font-size: 0.875rem;
  color: #000 !important;
}

.multiselect__tags {
  min-height: 42px;
  border: none !important;
  border-radius: 0.5rem !important;
  padding: 6px 40px 0 8px;
  background: #fff !important;
}

.multiselect__single {
  color: #000 !important;
  font-size: 0.875rem;
  margin-bottom: 0;
  padding: 2px 0;
}

.multiselect__placeholder {
  color: #9ca3af !important;
  font-size: 0.875rem;
  padding-top: 2px;
}

.multiselect__input {
  color: #000 !important;
  font-size: 0.875rem;
  background: transparent !important;
}

.multiselect__content-wrapper {
  border: 1px solid var(--border) !important;
  border-radius: 0 0 0.5rem 0.5rem !important;
  max-height: 250px !important;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
}

.multiselect__option {
  padding: 8px 12px !important;
  min-height: auto !important;
  font-size: 0.875rem;
}

.multiselect__option--highlight {
  background: #eff6ff !important;
  color: #000 !important;
}

.multiselect__option--selected {
  background: #dbeafe !important;
  color: #1d4ed8 !important;
  font-weight: 600;
}

.multiselect__option--selected.multiselect__option--highlight {
  background: #bfdbfe !important;
  color: #1d4ed8 !important;
}

.multiselect--active {
  box-shadow: 0 0 0 2px rgba(19, 127, 236, 0.2) !important;
  border-color: var(--primary) !important;
}

.multiselect__select {
  height: 40px;
}

/* Custom Dosen Option Styles */
.dosen-option {
  display: flex;
  flex-direction: column;
  gap: 2px;
  padding: 4px 0;
}

.dosen-option.dosen-disabled {
  opacity: 0.45;
  pointer-events: none;
  cursor: not-allowed;
}

.dosen-option-name {
  font-weight: 600;
  color: #111827;
  font-size: 0.875rem;
  line-height: 1.25;
}

.dosen-option-meta {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}

.dosen-bidang {
  font-size: 0.7rem;
  color: #6b7280;
  max-width: 200px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.dosen-kuota {
  font-size: 0.7rem;
  font-weight: 600;
  padding: 1px 6px;
  border-radius: 4px;
}

.kuota-ok {
  background: #ecfdf5;
  color: #059669;
}

.kuota-full {
  background: #fef2f2;
  color: #dc2626;
}

.kuota-badge {
  font-size: 0.65rem;
  font-weight: 700;
  color: #dc2626;
  background: #fef2f2;
  padding: 1px 6px;
  border-radius: 4px;
  border: 1px solid #fecaca;
}

/* Selected dosen display */
.selected-dosen {
  display: flex;
  align-items: center;
  gap: 8px;
}

.selected-name {
  color: #000;
  font-weight: 500;
  font-size: 0.875rem;
}

.selected-slot {
  font-size: 0.7rem;
  font-weight: 600;
  color: #059669;
  background: #ecfdf5;
  padding: 1px 6px;
  border-radius: 4px;
}
</style>
