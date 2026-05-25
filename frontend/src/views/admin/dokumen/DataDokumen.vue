<template>
  <div class="max-w-7xl mx-auto flex flex-col gap-6 animate-fade-in-up">
    <div class="flex flex-col gap-1">
      <div class="flex items-center gap-2 text-sm text-text-secondary">
        <router-link to="/admin/dashboard" class="hover:text-primary transition-colors">
          Dashboard
        </router-link>
        <span>/</span>
        <span class="text-text-main font-medium">Dokumen</span>
      </div>
      <h1 class="text-text-main text-3xl font-bold leading-tight">Dokumen</h1>
      <p class="text-text-secondary text-sm">
        Rekap semua dokumen resmi dan jadwal ujian mahasiswa untuk kebutuhan unduh cepat.
      </p>
    </div>

    <TransitionGroup
      v-if="documentStatCards.length"
      name="stat-card"
      tag="div"
      class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
    >
      <button
        v-for="stat in documentStatCards"
        :key="stat.key"
        type="button"
        @click="setType(stat.key)"
        class="group flex min-h-[132px] flex-col justify-between rounded-xl border bg-surface-light p-4 text-left shadow-sm outline-none transition-all duration-300 ease-out hover:-translate-y-1 hover:shadow-lg focus-visible:ring-2 focus-visible:ring-primary/30 active:scale-[0.99]"
        :class="
          activeType === stat.key
            ? 'border-primary/50 bg-blue-50/50 ring-1 ring-primary/20 dark:bg-blue-900/10'
            : 'border-border-light hover:border-primary/40'
        "
      >
        <div class="flex items-start justify-between gap-3">
          <div class="min-w-0">
            <p class="text-xs font-bold uppercase tracking-wider text-text-secondary">
              Jenis Dokumen
            </p>
            <h2 class="mt-1 text-sm font-bold leading-5 text-text-main">
              {{ stat.label }}
            </h2>
          </div>
          <div
            class="flex size-10 shrink-0 items-center justify-center rounded-lg"
            :class="getDocumentTypeTone(stat.key)"
          >
            <span class="material-symbols-outlined text-[22px]">
              {{ getDocumentTypeIcon(stat.key) }}
            </span>
          </div>
        </div>
        <div class="mt-4 flex items-end justify-between gap-3">
          <p class="text-3xl font-bold leading-none text-text-main">
            {{ stat.count ?? 0 }}
          </p>
          <span
            class="inline-flex items-center gap-1 rounded-full px-2 py-1 text-xs font-semibold"
            :class="
              activeType === stat.key
                ? 'bg-primary/10 text-primary'
                : 'bg-sidebar-light text-text-secondary'
            "
          >
            <span class="material-symbols-outlined text-[16px]">inventory_2</span>
            Dokumen
          </span>
        </div>
      </button>
    </TransitionGroup>

    <div class="bg-surface-light border border-border-light rounded-xl shadow-sm overflow-hidden">
      <div class="p-5 border-b border-border-light space-y-4">
        <Transition name="toolbar-slide" mode="out-in">
          <div
            v-if="!isJadwalTab"
            class="flex flex-col lg:flex-row lg:items-center gap-4 justify-between"
          >
            <div class="relative flex-1 max-w-xl">
              <input
                v-model="search"
                @input="debouncedFetch"
                class="block w-full pl-10 pr-3 py-2.5 border border-border-light rounded-lg bg-background-light text-text-main placeholder-text-secondary focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary sm:text-sm transition-shadow dark:bg-background"
                placeholder="Cari nama, NIM, atau judul..."
              />
              <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-text-secondary">
                search
              </span>
            </div>
            <div class="flex flex-wrap items-center gap-2">
              <button
                @click="downloadBatch"
                :disabled="batchDownloading || selectedCount === 0"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-bold text-white shadow-sm shadow-blue-500/20 transition-all duration-300 ease-out hover:-translate-y-0.5 hover:bg-blue-600 hover:shadow-md disabled:translate-y-0 disabled:cursor-not-allowed disabled:opacity-50 disabled:shadow-none active:scale-[0.98]"
              >
                <span
                  v-if="batchDownloading"
                  class="animate-spin rounded-full h-4 w-4 border-b-2 border-current"
                ></span>
                <span v-else class="material-symbols-outlined text-[20px]">folder_zip</span>
                Download Batch
                <Transition name="count-pop">
                  <span
                    v-if="selectedCount"
                    class="rounded-full bg-white/20 px-1.5 py-0.5 text-[10px]"
                  >
                    {{ selectedCount }}
                  </span>
                </Transition>
              </button>
              <button
                @click="fetchData(1)"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 border border-border-light rounded-lg text-text-secondary hover:text-primary hover:bg-background-light transition-all duration-200 hover:-translate-y-0.5"
              >
                <span class="material-symbols-outlined text-[20px]">refresh</span>
                Muat Ulang
              </button>
            </div>
          </div>
        </Transition>

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:flex lg:flex-wrap lg:items-end">
          <label class="flex min-w-0 flex-col gap-1 lg:w-40">
            <span class="text-xs font-semibold text-text-secondary">Tahun Akademik</span>
            <select
              v-model="filterTahunAkademik"
              @change="applyFilters"
              class="h-10 w-full min-w-0 rounded-lg border border-border-light bg-background-light px-3 text-sm text-text-main outline-none transition-colors focus:border-primary focus:ring-1 focus:ring-primary dark:bg-background"
            >
              <option value="">Semua Tahun</option>
              <option v-for="tahun in tahunList" :key="tahun.id || tahun.name" :value="tahun.name">
                {{ tahun.name }}
              </option>
            </select>
          </label>
          <label class="flex min-w-0 flex-col gap-1 lg:w-48">
            <span class="text-xs font-semibold text-text-secondary">Fakultas</span>
            <select
              v-model="filterFakultas"
              @change="onFakultasChange"
              class="h-10 w-full min-w-0 rounded-lg border border-border-light bg-background-light px-3 text-sm text-text-main outline-none transition-colors focus:border-primary focus:ring-1 focus:ring-primary dark:bg-background"
            >
              <option value="">Semua Fakultas</option>
              <option v-for="fakultas in fakultasList" :key="fakultas.id" :value="fakultas.id">
                {{ fakultas.nama_fakultas }}
              </option>
            </select>
          </label>
          <label class="flex min-w-0 flex-col gap-1 lg:w-48">
            <span class="text-xs font-semibold text-text-secondary">Prodi</span>
            <select
              v-model="filterProdi"
              @change="applyFilters"
              class="h-10 w-full min-w-0 rounded-lg border border-border-light bg-background-light px-3 text-sm text-text-main outline-none transition-colors focus:border-primary focus:ring-1 focus:ring-primary dark:bg-background"
            >
              <option value="">Semua Prodi</option>
              <option v-for="prodi in filteredProdiList" :key="prodi.id" :value="prodi.id">
                {{ prodi.nama }}
              </option>
            </select>
          </label>
          <label v-if="!useDateRange" class="flex min-w-0 flex-col gap-1 lg:w-44">
            <span class="text-xs font-semibold text-text-secondary">Tanggal</span>
            <input
              v-model="filterTanggal"
              @change="applyFilters"
              type="date"
              class="h-10 w-full min-w-0 rounded-lg border border-border-light bg-background-light px-3 text-sm text-text-main outline-none transition-colors focus:border-primary focus:ring-1 focus:ring-primary dark:bg-background"
            />
          </label>
          <template v-else>
            <label class="flex min-w-0 flex-col gap-1 lg:w-44">
              <span class="text-xs font-semibold text-text-secondary">Dari Tanggal</span>
              <input
                v-model="filterTanggalMulai"
                @change="applyFilters"
                type="date"
                class="h-10 w-full min-w-0 rounded-lg border border-border-light bg-background-light px-3 text-sm text-text-main outline-none transition-colors focus:border-primary focus:ring-1 focus:ring-primary dark:bg-background"
              />
            </label>
            <label class="flex min-w-0 flex-col gap-1 lg:w-44">
              <span class="text-xs font-semibold text-text-secondary">Sampai Tanggal</span>
              <input
                v-model="filterTanggalSelesai"
                @change="applyFilters"
                type="date"
                class="h-10 w-full min-w-0 rounded-lg border border-border-light bg-background-light px-3 text-sm text-text-main outline-none transition-colors focus:border-primary focus:ring-1 focus:ring-primary dark:bg-background"
              />
            </label>
          </template>
          <label class="inline-flex h-10 min-w-0 items-center gap-2 rounded-lg border border-border-light px-3 text-sm font-medium text-text-secondary lg:w-auto">
            <input
              v-model="useDateRange"
              @change="onDateRangeToggle"
              type="checkbox"
              class="size-4 rounded border-border-light text-primary focus:ring-primary"
            />
            Pakai range tanggal
          </label>
          <Transition name="filter-chip">
            <button
              v-if="hasActiveFilters"
              @click="clearFilters"
              class="inline-flex h-10 items-center gap-1.5 rounded-lg px-3 text-sm font-medium text-red-500 transition-all duration-200 hover:-translate-y-0.5 hover:bg-red-50 dark:hover:bg-red-900/20"
            >
              <span class="material-symbols-outlined text-[16px]">close</span>
              Reset
            </button>
          </Transition>
        </div>
      </div>

      <div class="border-b border-border-light overflow-hidden">
        <SimpleBar
          class="document-tabs-scroll"
          data-simplebar-auto-hide="false"
        >
          <div class="flex gap-2 p-3 pb-4 min-w-max">
            <button
              v-for="tab in visibleTypes"
              :key="tab.key"
              @click="setType(tab.key)"
              class="px-3 py-2 rounded-lg text-xs font-semibold transition-colors flex items-center gap-2"
              :class="
                activeType === tab.key
                  ? 'bg-primary text-white shadow-sm'
                  : 'text-text-secondary hover:text-primary hover:bg-background-light'
              "
            >
              <span>{{ tab.label }}</span>
              <span
                class="px-1.5 py-0.5 rounded-full text-[10px]"
                :class="activeType === tab.key ? 'bg-white/20 text-white' : 'bg-sidebar-light text-text-secondary'"
              >
                {{ tab.count ?? 0 }}
              </span>
            </button>
          </div>
        </SimpleBar>
      </div>

      <Transition name="panel-switch" mode="out-in">
        <div v-if="loading && !isJadwalTab" key="loading" class="p-12 text-center">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto"></div>
          <p class="text-text-secondary text-sm mt-3">Memuat dokumen...</p>
        </div>

        <div v-else-if="isJadwalTab" key="jadwal" class="p-8 sm:p-10">
          <div class="mx-auto max-w-2xl rounded-xl border border-border-light bg-background-light p-6 text-center transition-all duration-300 hover:-translate-y-1 hover:shadow-md dark:bg-background">
            <div class="mx-auto mb-4 flex size-12 items-center justify-center rounded-xl bg-green-50 text-green-600 transition-transform duration-300 hover:scale-105 dark:bg-green-900/20 dark:text-green-400">
              <span class="material-symbols-outlined text-[28px]">event_note</span>
            </div>
            <h2 class="text-lg font-bold text-text-main">Cetak Jadwal Ujian</h2>
            <p class="mt-2 text-sm leading-6 text-text-secondary">
              Isi tanggal ujian atau aktifkan range tanggal, lalu klik tombol cetak jadwal.
            </p>
            <button
              @click="downloadJadwalPdf"
              :disabled="exportingJadwal || !canPrintJadwal"
              class="mt-5 inline-flex items-center justify-center gap-2 rounded-lg bg-green-600 px-5 py-2.5 text-sm font-bold text-white transition-all duration-300 hover:-translate-y-0.5 hover:bg-green-700 hover:shadow-md disabled:translate-y-0 disabled:cursor-not-allowed disabled:opacity-50 disabled:shadow-none active:scale-[0.98]"
            >
              <span
                v-if="exportingJadwal"
                class="animate-spin rounded-full h-4 w-4 border-b-2 border-current"
              ></span>
              <span v-else class="material-symbols-outlined text-[20px]">print</span>
              {{ exportingJadwal ? "Mencetak..." : "Cetak Jadwal" }}
            </button>
          </div>
        </div>

        <DataTableScroll v-else key="table">
          <table class="w-full text-left text-sm">
            <thead class="bg-sidebar-light/50 text-text-secondary border-b border-border-light">
              <tr>
                <th class="w-12 px-6 py-4">
                  <input
                    type="checkbox"
                    class="size-4 rounded border-border-light text-primary transition-all duration-200 hover:scale-110 focus:ring-primary disabled:cursor-not-allowed disabled:opacity-40"
                    :checked="allCurrentPageSelected"
                    :indeterminate="someCurrentPageSelected && !allCurrentPageSelected"
                    :disabled="items.length === 0"
                    aria-label="Pilih semua dokumen di halaman ini"
                    @change="toggleSelectAllCurrentPage"
                  />
                </th>
                <th class="px-6 py-4 font-semibold">Mahasiswa</th>
                <th class="px-6 py-4 font-semibold">Dokumen</th>
                <th class="px-6 py-4 font-semibold">{{ isJadwalTab ? "Ruang / Waktu" : "Nomor" }}</th>
                <th class="px-6 py-4 font-semibold">Tanggal</th>
                <th class="px-6 py-4 font-semibold">Judul</th>
                <th class="px-6 py-4 text-right font-semibold">Aksi</th>
              </tr>
            </thead>
            <TransitionGroup name="document-row" tag="tbody" class="divide-y divide-border-light">
              <tr v-if="items.length === 0" key="empty">
                <td colspan="7" class="px-6 py-12 text-center text-text-secondary">
                  {{ isJadwalTab ? "Tidak ada jadwal ujian pada filter ini" : "Tidak ada dokumen pada tab ini" }}
                </td>
              </tr>
              <tr
                v-for="item in items"
                :key="`${item.type}-${item.id}`"
                class="document-row-item hover:bg-sidebar-light/30 transition-colors duration-200"
                :class="isItemSelected(item) ? 'bg-blue-50/70 dark:bg-blue-900/10' : ''"
              >
                <td class="px-6 py-4">
                  <input
                    type="checkbox"
                    class="size-4 rounded border-border-light text-primary transition-all duration-200 hover:scale-110 focus:ring-primary"
                    :checked="isItemSelected(item)"
                    :aria-label="`Pilih ${item.label} ${item.mahasiswa?.nim || ''}`"
                    @change="toggleItemSelection(item)"
                  />
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <div
                      class="size-10 rounded-full flex items-center justify-center text-xs font-bold shrink-0 transition-transform duration-200"
                      :class="getAvatarColor(item.mahasiswa?.nama)"
                    >
                      {{ getInitials(item.mahasiswa?.nama) }}
                    </div>
                    <div>
                      <p class="font-bold text-text-main">{{ item.mahasiswa?.nama || "-" }}</p>
                      <p class="text-xs text-text-secondary">{{ item.mahasiswa?.nim || "-" }}</p>
                      <p class="text-xs text-text-secondary">{{ item.prodi?.nama || "-" }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <span class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-blue-50 text-primary dark:bg-blue-900/20 dark:text-blue-400 text-xs font-bold">
                    <span class="material-symbols-outlined text-[16px]">description</span>
                    {{ item.label }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <code class="text-xs text-text-main bg-background-light border border-border-light rounded px-2 py-1 dark:bg-background">
                    {{ item.nomor || (isJadwalTab ? "Belum ada ruang" : "Belum bernomor") }}
                  </code>
                </td>
                <td class="px-6 py-4 text-text-secondary whitespace-nowrap">
                  {{ formatDate(item.tanggal) }}
                </td>
                <td class="px-6 py-4 max-w-sm">
                  <p class="text-text-main line-clamp-2" :title="item.judul">
                    {{ item.judul || "-" }}
                  </p>
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center justify-end gap-2">
                  <button
                    @click="viewItem(item)"
                    :disabled="viewing === `${item.type}-${item.id}`"
                    class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-text-secondary hover:text-primary hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200 hover:-translate-y-0.5 disabled:translate-y-0 disabled:opacity-40 disabled:cursor-not-allowed"
                  >
                    <span
                      v-if="viewing === `${item.type}-${item.id}`"
                      class="animate-spin rounded-full h-4 w-4 border-b-2 border-current"
                    ></span>
                    <span v-else class="material-symbols-outlined text-[20px]">visibility</span>
                    View
                  </button>
                  <button
                    @click="downloadItem(item)"
                    :disabled="downloading === `${item.type}-${item.id}`"
                    class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-text-secondary hover:text-primary hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200 hover:-translate-y-0.5 disabled:translate-y-0 disabled:opacity-40 disabled:cursor-not-allowed"
                  >
                    <span
                      v-if="downloading === `${item.type}-${item.id}`"
                      class="animate-spin rounded-full h-4 w-4 border-b-2 border-current"
                    ></span>
                    <span v-else class="material-symbols-outlined text-[20px]">download</span>
                    Unduh
                  </button>
                  </div>
                </td>
              </tr>
            </TransitionGroup>
          </table>
        </DataTableScroll>
      </Transition>

      <TablePagination
        v-if="!isJadwalTab"
        :pagination="pagination"
        :disabled="loading"
        @page-change="goToPage"
        @per-page-change="changePerPage"
      />
    </div>
  </div>

  <Teleport to="body">
    <transition name="modal-fade">
      <div
        v-if="preview.show"
        class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/75 p-3 sm:p-6 backdrop-blur-sm"
        @click.self="closePreview"
      >
        <div class="flex h-[92vh] w-full max-w-6xl flex-col overflow-hidden rounded-xl border border-border-light bg-surface-light shadow-2xl">
          <div class="flex min-h-16 items-center justify-between gap-4 border-b border-border-light px-4 py-3 sm:px-5">
            <div class="min-w-0">
              <h2 class="truncate text-base font-bold leading-tight text-text-main sm:text-lg">
                {{ preview.title }}
              </h2>
              <p class="mt-0.5 truncate text-xs text-text-secondary">
                {{ preview.fileName }}
              </p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
              <button
                @click="downloadPreview"
                class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-text-secondary transition-colors hover:bg-blue-50 hover:text-primary dark:hover:bg-blue-900/20"
              >
                <span class="material-symbols-outlined text-[20px]">download</span>
                Unduh
              </button>
              <button
                @click="closePreview"
                class="inline-flex size-10 items-center justify-center rounded-lg text-text-secondary transition-colors hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/20"
                aria-label="Tutup preview"
              >
                <span class="material-symbols-outlined text-[22px]">close</span>
              </button>
            </div>
          </div>
          <div class="min-h-0 flex-1 bg-[#202124]">
            <iframe
              v-if="preview.url"
              :src="preview.url"
              class="h-full w-full border-0"
              title="Preview dokumen PDF"
            ></iframe>
          </div>
        </div>
      </div>
    </transition>
  </Teleport>
</template>

<script setup>
import { computed, onMounted, onUnmounted, ref } from "vue";
import SimpleBar from "simplebar-vue";
import { useToast } from "vue-toastification";
import adminService from "../../../services/adminService";
import { useAuthStore } from "../../../stores/auth";

const toast = useToast();
const authStore = useAuthStore();
const JADWAL_TYPE = "jadwal_ujian";
const loading = ref(false);
const downloading = ref(null);
const batchDownloading = ref(false);
const exportingJadwal = ref(false);
const viewing = ref(null);
const search = ref("");
const activeType = ref("sk_penguji_sempro");
const filterTahunAkademik = ref("");
const filterFakultas = ref("");
const filterProdi = ref("");
const useDateRange = ref(false);
const filterTanggal = ref("");
const filterTanggalMulai = ref("");
const filterTanggalSelesai = ref("");
const types = ref([]);
const items = ref([]);
const selectedItemIds = ref([]);
const fakultasList = ref([]);
const prodiList = ref([]);
const tahunList = ref([]);
const preview = ref({
  show: false,
  url: "",
  title: "",
  fileName: "",
  item: null,
});
const pagination = ref({
  current_page: 1,
  last_page: 1,
  per_page: 10,
  total: 0,
});
let searchTimer = null;

const visibleTypes = computed(() =>
  types.value.filter(
    (type) =>
      authStore.semhasEnabled ||
      !["sk_penguji_semhas", "ba_semhas"].includes(type.key),
  ),
);

const documentStatCards = computed(() =>
  visibleTypes.value.filter((type) => type.key !== JADWAL_TYPE),
);

const isJadwalTab = computed(() => activeType.value === JADWAL_TYPE);

const filteredProdiList = computed(() => {
  if (!filterFakultas.value) return prodiList.value;
  return prodiList.value.filter(
    (prodi) => String(prodi.fakultas_id) === String(filterFakultas.value),
  );
});

const hasActiveFilters = computed(() => {
  const hasDate = useDateRange.value
    ? filterTanggalMulai.value || filterTanggalSelesai.value
    : filterTanggal.value;

  return Boolean(
    (!isJadwalTab.value && search.value) ||
      hasDate ||
      filterTahunAkademik.value ||
      filterFakultas.value ||
      filterProdi.value,
  );
});

const canPrintJadwal = computed(() =>
  useDateRange.value
    ? Boolean(filterTanggalMulai.value && filterTanggalSelesai.value)
    : Boolean(filterTanggal.value),
);

const selectedCount = computed(() => selectedItemIds.value.length);

const currentPageItemIds = computed(() =>
  isJadwalTab.value ? [] : items.value.map((item) => item.id),
);

const allCurrentPageSelected = computed(
  () =>
    currentPageItemIds.value.length > 0 &&
    currentPageItemIds.value.every((id) => selectedItemIds.value.includes(id)),
);

const someCurrentPageSelected = computed(() =>
  currentPageItemIds.value.some((id) => selectedItemIds.value.includes(id)),
);

const toDateParam = (date) => {
  if (!date) return null;
  return String(date).slice(0, 10);
};

const buildDateParams = (specificDate = null) => {
  if (specificDate) {
    return { tanggal: toDateParam(specificDate) };
  }

  if (useDateRange.value) {
    return {
      tanggal_mulai: filterTanggalMulai.value || undefined,
      tanggal_selesai: filterTanggalSelesai.value || undefined,
    };
  }

  return {
    tanggal: filterTanggal.value || undefined,
  };
};

const buildListParams = (page) => ({
  type: activeType.value,
  search: !isJadwalTab.value && search.value ? search.value : undefined,
  tahun_akademik: filterTahunAkademik.value || undefined,
  fakultas_id: filterFakultas.value || undefined,
  prodi_id: filterProdi.value || undefined,
  page,
  per_page: pagination.value.per_page,
  ...buildDateParams(),
});

const buildJadwalPdfParams = (specificDate = null) => ({
  tahun_akademik: filterTahunAkademik.value || undefined,
  fakultas_id: filterFakultas.value || undefined,
  prodi_id: filterProdi.value || undefined,
  ...buildDateParams(specificDate),
});

const buildBatchDownloadParams = () => ({
  type: activeType.value,
  ids: selectedItemIds.value,
  search: search.value || undefined,
  tahun_akademik: filterTahunAkademik.value || undefined,
  fakultas_id: filterFakultas.value || undefined,
  prodi_id: filterProdi.value || undefined,
  ...buildDateParams(),
});

const resetSelection = () => {
  selectedItemIds.value = [];
};

const isItemSelected = (item) => selectedItemIds.value.includes(item.id);

const toggleItemSelection = (item) => {
  if (isItemSelected(item)) {
    selectedItemIds.value = selectedItemIds.value.filter((id) => id !== item.id);
    return;
  }

  selectedItemIds.value = [...selectedItemIds.value, item.id];
};

const toggleSelectAllCurrentPage = (event) => {
  if (event.target.checked) {
    selectedItemIds.value = Array.from(
      new Set([...selectedItemIds.value, ...currentPageItemIds.value]),
    );
    return;
  }

  const currentIds = new Set(currentPageItemIds.value);
  selectedItemIds.value = selectedItemIds.value.filter((id) => !currentIds.has(id));
};

const fetchData = async (page = pagination.value.current_page || 1) => {
  loading.value = true;
  try {
    const response = await adminService.getDokumenResmi(buildListParams(page));

    if (response.success) {
      types.value = response.types || [];
      const data = response.data || {};
      items.value = data.data || [];
      pagination.value = {
        current_page: data.current_page || 1,
        last_page: data.last_page || 1,
        per_page: data.per_page || 10,
        total: data.total || 0,
      };
    }
  } catch (error) {
    toast.error(error.response?.data?.message || "Gagal memuat dokumen");
  } finally {
    loading.value = false;
  }
};

const debouncedFetch = () => {
  clearTimeout(searchTimer);
  resetSelection();
  searchTimer = setTimeout(() => fetchData(1), 350);
};

const setType = (type) => {
  resetSelection();
  activeType.value = type;
  fetchData(1);
};

const applyFilters = () => {
  resetSelection();
  fetchData(1);
};

const onFakultasChange = () => {
  resetSelection();
  filterProdi.value = "";
  fetchData(1);
};

const onDateRangeToggle = () => {
  resetSelection();
  if (useDateRange.value) {
    filterTanggal.value = "";
  } else {
    filterTanggalMulai.value = "";
    filterTanggalSelesai.value = "";
  }
  fetchData(1);
};

const clearFilters = () => {
  resetSelection();
  search.value = "";
  filterTahunAkademik.value = "";
  filterFakultas.value = "";
  filterProdi.value = "";
  filterTanggal.value = "";
  filterTanggalMulai.value = "";
  filterTanggalSelesai.value = "";
  useDateRange.value = false;
  fetchData(1);
};

const goToPage = (page) => {
  if (page < 1 || page > pagination.value.last_page) return;
  fetchData(page);
};

const changePerPage = (perPage) => {
  pagination.value.per_page = perPage;
  fetchData(1);
};

const getPdfResponse = (item) => {
  if (item.type === JADWAL_TYPE) {
    return adminService.getJadwalUjianPdf(buildJadwalPdfParams(item.tanggal));
  }
  if (item.type === "sk_tugas") return adminService.getSkTugasPdf(item.skripsi_id);
  if (item.type === "nota_bimbingan") return adminService.getNotaBimbinganPdf(item.skripsi_id);
  if (item.type === "sk_yudisium") return adminService.generateSKYudisiumPdf(item.skripsi_id);
  if (item.type.startsWith("sk_penguji_")) return adminService.getSkPengujiPdf(item.seminar_id);
  if (item.type.startsWith("ba_")) return adminService.getBeritaAcaraPdf(item.seminar_id);
  throw new Error("Jenis dokumen tidak dikenal");
};

const revokePreviewUrl = () => {
  if (preview.value.url) {
    window.URL.revokeObjectURL(preview.value.url);
  }
};

const viewItem = async (item) => {
  const key = `${item.type}-${item.id}`;
  viewing.value = key;
  try {
    const response = await getPdfResponse(item);
    const blob = new Blob([response.data], { type: "application/pdf" });
    revokePreviewUrl();
    preview.value = {
      show: true,
      url: window.URL.createObjectURL(blob),
      title: item.label || "Dokumen",
      fileName: getFileName(item),
      item,
    };
  } catch (error) {
    toast.error(error.response?.data?.message || "Gagal membuka preview dokumen");
  } finally {
    viewing.value = null;
  }
};

const closePreview = () => {
  revokePreviewUrl();
  preview.value = {
    show: false,
    url: "",
    title: "",
    fileName: "",
    item: null,
  };
};

const downloadItem = async (item) => {
  const key = `${item.type}-${item.id}`;
  downloading.value = key;
  try {
    const response = await getPdfResponse(item);
    const blob = new Blob([response.data], { type: "application/pdf" });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement("a");
    link.href = url;
    link.download = getFileName(item);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    window.URL.revokeObjectURL(url);
  } catch (error) {
    toast.error(error.response?.data?.message || "Gagal mengunduh dokumen");
  } finally {
    downloading.value = null;
  }
};

const downloadBatch = async () => {
  if (isJadwalTab.value || selectedCount.value === 0) return;

  batchDownloading.value = true;
  try {
    const response = await adminService.downloadDokumenResmiBatch(buildBatchDownloadParams());
    const blob = new Blob([response.data], { type: "application/zip" });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement("a");
    link.href = url;
    link.download = getResponseFileName(response, getBatchFileName());
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    window.URL.revokeObjectURL(url);
    resetSelection();
  } catch (error) {
    toast.error(await getBlobErrorMessage(error, "Gagal mengunduh batch dokumen"));
  } finally {
    batchDownloading.value = false;
  }
};

const downloadJadwalPdf = async () => {
  if (!canPrintJadwal.value) return;

  exportingJadwal.value = true;
  try {
    const response = await adminService.getJadwalUjianPdf(buildJadwalPdfParams());
    const blob = new Blob([response.data], { type: "application/pdf" });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement("a");
    link.href = url;
    link.download = getJadwalFileName();
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    window.URL.revokeObjectURL(url);
  } catch (error) {
    toast.error(error.response?.data?.message || "Gagal mengunduh jadwal ujian");
  } finally {
    exportingJadwal.value = false;
  }
};

const downloadPreview = () => {
  if (!preview.value.url || !preview.value.item) return;
  const link = document.createElement("a");
  link.href = preview.value.url;
  link.download = preview.value.fileName;
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
};

const getFileName = (item) => {
  if (item.type === JADWAL_TYPE) {
    const date = toDateParam(item.tanggal);
    const suffix = date ? `_${date}` : "";
    return `Jadwal_Ujian_Skripsi${suffix}.pdf`;
  }

  const nim = item.mahasiswa?.nim || "mahasiswa";
  const names = {
    sk_tugas: "SK_Tugas",
    nota_bimbingan: "Nota_Bimbingan",
    sk_penguji_sempro: "SK_Penguji_Sempro",
    ba_sempro: "Berita_Acara_Sempro",
    sk_penguji_semhas: "SK_Penguji_Semhas",
    ba_semhas: "Berita_Acara_Semhas",
    sk_penguji_sidang: "SK_Penguji_Sidang",
    ba_sidang: "Berita_Acara_Sidang",
    sk_yudisium: "SK_Yudisium",
  };
  return `${names[item.type] || "Dokumen"}_${nim}.pdf`;
};

const getJadwalFileName = () => {
  if (useDateRange.value && (filterTanggalMulai.value || filterTanggalSelesai.value)) {
    const start = filterTanggalMulai.value || "awal";
    const end = filterTanggalSelesai.value || "akhir";
    return `Jadwal_Ujian_Skripsi_${start}_sd_${end}.pdf`;
  }

  if (filterTanggal.value) {
    return `Jadwal_Ujian_Skripsi_${filterTanggal.value}.pdf`;
  }

  return "Jadwal_Ujian_Skripsi.pdf";
};

const getBatchFileName = () => {
  const filters = [];
  if (filterTahunAkademik.value) filters.push(`tahun_${filterTahunAkademik.value}`);
  if (filterFakultas.value) filters.push(`fakultas_${filterFakultas.value}`);
  if (filterProdi.value) filters.push(`prodi_${filterProdi.value}`);
  if (filterTanggal.value) filters.push(`tanggal_${filterTanggal.value}`);
  if (filterTanggalMulai.value || filterTanggalSelesai.value) {
    filters.push(
      `tanggal_${filterTanggalMulai.value || "awal"}_sd_${filterTanggalSelesai.value || "akhir"}`,
    );
  }
  if (search.value) filters.push(`cari_${search.value}`);

  return `${sanitizeFilePart(activeType.value)}-${sanitizeFilePart(filters.join("-") || "semua")}.zip`;
};

const getResponseFileName = (response, fallback) => {
  const disposition = response.headers?.["content-disposition"] || "";
  const utf8Match = disposition.match(/filename\*=UTF-8''([^;]+)/i);
  if (utf8Match?.[1]) {
    return decodeURIComponent(utf8Match[1].replaceAll('"', ""));
  }

  const asciiMatch = disposition.match(/filename="?([^"]+)"?/i);
  return asciiMatch?.[1] || fallback;
};

const getBlobErrorMessage = async (error, fallback) => {
  const data = error.response?.data;
  if (data instanceof Blob) {
    try {
      const text = await data.text();
      const json = JSON.parse(text);
      return json.message || fallback;
    } catch {
      return fallback;
    }
  }

  return data?.message || fallback;
};

const sanitizeFilePart = (value) =>
  String(value || "file")
    .normalize("NFKD")
    .replace(/[^\w.-]+/g, "_")
    .replace(/^_+|_+$/g, "") || "file";

const formatDate = (date) => {
  if (!date) return "-";
  return new Date(date).toLocaleDateString("id-ID", {
    day: "2-digit",
    month: "short",
    year: "numeric",
  });
};

const getInitials = (name) =>
  (name || "?")
    .split(" ")
    .map((word) => word[0])
    .join("")
    .slice(0, 2)
    .toUpperCase();

const getAvatarColor = (name) => {
  const colors = [
    "bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300",
    "bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300",
    "bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300",
    "bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300",
  ];
  const index = (name || "").length % colors.length;
  return colors[index];
};

const getDocumentTypeIcon = (type) => {
  const icons = {
    sk_penguji_sempro: "groups",
    ba_sempro: "history_edu",
    sk_tugas: "assignment_ind",
    nota_bimbingan: "edit_note",
    sk_penguji_semhas: "fact_check",
    ba_semhas: "article",
    sk_penguji_sidang: "gavel",
    ba_sidang: "description",
    sk_yudisium: "workspace_premium",
  };
  return icons[type] || "description";
};

const getDocumentTypeTone = (type) => {
  const tones = {
    sk_penguji_sempro: "bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-300",
    ba_sempro: "bg-amber-50 text-amber-600 dark:bg-amber-900/20 dark:text-amber-300",
    sk_tugas: "bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20 dark:text-emerald-300",
    nota_bimbingan: "bg-cyan-50 text-cyan-600 dark:bg-cyan-900/20 dark:text-cyan-300",
    sk_penguji_semhas: "bg-violet-50 text-violet-600 dark:bg-violet-900/20 dark:text-violet-300",
    ba_semhas: "bg-fuchsia-50 text-fuchsia-600 dark:bg-fuchsia-900/20 dark:text-fuchsia-300",
    sk_penguji_sidang: "bg-orange-50 text-orange-600 dark:bg-orange-900/20 dark:text-orange-300",
    ba_sidang: "bg-rose-50 text-rose-600 dark:bg-rose-900/20 dark:text-rose-300",
    sk_yudisium: "bg-green-50 text-green-600 dark:bg-green-900/20 dark:text-green-300",
  };
  return tones[type] || "bg-slate-50 text-slate-600 dark:bg-slate-900/20 dark:text-slate-300";
};

const normalizeList = (response) => response?.data || response || [];

const loadFilterData = async () => {
  try {
    const [fakultasRes, prodiRes, tahunRes] = await Promise.all([
      adminService.getFakultas(),
      adminService.getProdi(),
      adminService.getTahun(),
    ]);
    fakultasList.value = normalizeList(fakultasRes);
    prodiList.value = normalizeList(prodiRes);
    tahunList.value = normalizeList(tahunRes);
  } catch (error) {
    toast.error(error.response?.data?.message || "Gagal memuat data filter");
  }
};

onMounted(() => {
  fetchData(1);
  loadFilterData();
});
onUnmounted(() => revokePreviewUrl());
</script>

<style scoped>
.stat-card-enter-active,
.stat-card-leave-active,
.stat-card-move {
  transition:
    opacity 260ms ease,
    transform 260ms cubic-bezier(0.22, 1, 0.36, 1);
}

.stat-card-enter-from,
.stat-card-leave-to {
  opacity: 0;
  transform: translateY(10px) scale(0.98);
}

.toolbar-slide-enter-active,
.toolbar-slide-leave-active,
.panel-switch-enter-active,
.panel-switch-leave-active {
  transition:
    opacity 220ms ease,
    transform 220ms cubic-bezier(0.22, 1, 0.36, 1);
}

.toolbar-slide-enter-from,
.toolbar-slide-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}

.panel-switch-enter-from,
.panel-switch-leave-to {
  opacity: 0;
  transform: translateY(8px);
}

.filter-chip-enter-active,
.filter-chip-leave-active,
.count-pop-enter-active,
.count-pop-leave-active {
  transition:
    opacity 180ms ease,
    transform 180ms cubic-bezier(0.22, 1, 0.36, 1);
}

.filter-chip-enter-from,
.filter-chip-leave-to,
.count-pop-enter-from,
.count-pop-leave-to {
  opacity: 0;
  transform: scale(0.88);
}

.document-row-enter-active,
.document-row-leave-active {
  transition:
    opacity 180ms ease,
    transform 180ms ease,
    background-color 180ms ease;
}

.document-row-enter-from,
.document-row-leave-to {
  opacity: 0;
  transform: translateY(6px);
}

.document-row-move {
  transition: transform 220ms cubic-bezier(0.22, 1, 0.36, 1);
}

.document-row-item:hover .size-10 {
  transform: scale(1.04);
}
</style>
