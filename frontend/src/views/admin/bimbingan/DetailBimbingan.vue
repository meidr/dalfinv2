<template>
  <div class="max-w-7xl mx-auto flex flex-col gap-6 animate-fade-in-up">
    <!-- Breadcrumb -->
    <div class="flex flex-wrap items-center gap-2 text-sm">
      <router-link
        to="/admin/bimbingan"
        class="text-text-secondary hover:text-primary font-medium transition-colors"
        >Log Bimbingan</router-link
      >
      <span class="material-symbols-outlined text-text-secondary text-sm"
        >chevron_right</span
      >
      <span class="text-text-main font-bold">Detail</span>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="p-12 text-center">
      <div
        class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto"
      ></div>
      <p class="text-text-secondary text-sm mt-3">Memuat data...</p>
    </div>

    <template v-else>
      <!-- Mahasiswa Info Header -->
      <div
        class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-surface-light border border-border-light rounded-xl p-6 shadow-sm"
      >
        <div class="flex items-center gap-4">
          <div
            class="size-14 rounded-full flex items-center justify-center text-lg font-bold shrink-0"
            :class="getAvatarColor(skripsi?.mahasiswa?.nama)"
          >
            {{ getInitials(skripsi?.mahasiswa?.nama) }}
          </div>
          <div>
            <h1 class="text-xl font-bold text-text-main">
              {{ skripsi?.mahasiswa?.nama || "-" }}
            </h1>
            <p class="text-sm text-text-secondary font-mono font-medium mt-0.5">
              {{ skripsi?.mahasiswa?.nim || "-" }}
            </p>
            <p class="text-sm text-text-secondary mt-1 max-w-xl line-clamp-2">
              {{ skripsi?.judul || "-" }}
            </p>
          </div>
        </div>
        <div class="flex flex-col items-end gap-2">
          <div class="flex items-center gap-3">
            <div class="text-center">
              <span class="text-2xl font-bold text-primary">{{
                bimbinganList.length
              }}</span>
              <p
                class="text-[10px] text-text-secondary uppercase tracking-wider font-semibold"
              >
                Total Bimbingan
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Tabbed Bimbingan per Pembimbing -->
      <div
        class="flex flex-col rounded-xl border border-border-light bg-surface-light overflow-hidden shadow-sm"
      >
        <!-- Header with buttons -->
        <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-4 border-b border-border-light">
          <h2 class="font-bold text-lg text-text-main">Riwayat Bimbingan</h2>
          <div class="flex items-center gap-2">
            <button
              v-if="isSuperAdmin"
              @click="openGenerateModal"
              class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-linear-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white text-xs font-semibold rounded-lg shadow-sm transition-all duration-200 hover:shadow-md"
            >
              <span class="material-symbols-outlined text-[16px]">bolt</span>
              Generate Otomatis
            </button>
            <button
              v-if="canAddBimbingan"
              @click="openAddModal"
              class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-primary hover:bg-primary-dark text-white text-xs font-semibold rounded-lg shadow-sm transition-all duration-200 hover:shadow-md"
            >
              <span class="material-symbols-outlined text-[16px]">add</span>
              Tambah Bimbingan
            </button>
          </div>
        </div>

        <!-- Pembimbing Tabs -->
        <div v-if="pembimbingTabs.length > 0" class="border-b border-border-light bg-sidebar-light/50">
          <div class="flex">
            <button
              v-for="(tab, idx) in pembimbingTabs"
              :key="tab.dosenId"
              @click="activeTab = idx"
              class="relative flex items-center gap-2 px-5 py-3.5 text-sm font-medium transition-all duration-200 border-b-2 -mb-px"
              :class="activeTab === idx
                ? 'text-primary border-primary bg-surface-light'
                : 'text-text-secondary border-transparent hover:text-text-main hover:bg-surface-light/60'
              "
            >
              <div
                class="flex items-center justify-center size-7 rounded-full text-[11px] font-bold shrink-0"
                :class="activeTab === idx
                  ? 'bg-primary/15 text-primary'
                  : 'bg-border-light text-text-secondary'
                "
              >
                {{ getInitials(tab.dosenName) }}
              </div>
              <div class="flex flex-col items-start">
                <span class="leading-tight">{{ tab.dosenName }}</span>
                <span
                  class="text-[10px] font-medium leading-tight"
                  :class="activeTab === idx ? 'text-primary/70' : 'text-text-secondary/70'"
                >
                  {{ tab.label }}
                </span>
              </div>
              <span
                class="ml-1 inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full text-[10px] font-bold"
                :class="activeTab === idx
                  ? 'bg-primary text-white'
                  : 'bg-border-light text-text-secondary'
                "
              >
                {{ tab.count }}
              </span>
            </button>
          </div>
        </div>

        <!-- Table for Active Tab -->
        <DataTableScroll>
          <table class="w-full text-left text-sm whitespace-nowrap">
            <thead
              class="bg-sidebar-light/50 text-text-secondary font-medium border-b border-border-light"
            >
              <tr>
                <th class="px-6 py-3">No</th>
                <th class="px-6 py-3">Tanggal</th>
                <th class="px-6 py-3">Topik</th>
                <th class="px-6 py-3">Deskripsi</th>
                <th class="px-6 py-3">Catatan Dosen</th>
                <th class="px-6 py-3 text-center">Status</th>
                <th class="px-6 py-3 text-center">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-border-light">
              <tr v-if="filteredBimbingan.length === 0">
                <td colspan="7" class="p-12 text-center text-text-secondary">
                  <span
                    class="material-symbols-outlined text-4xl text-gray-300 block mb-2"
                    >history_edu</span
                  >
                  Belum ada riwayat bimbingan untuk pembimbing ini
                </td>
              </tr>
              <tr
                v-for="(log, index) in filteredBimbingan"
                :key="log.id"
                class="hover:bg-sidebar-light/30 transition-colors"
              >
                <td class="px-6 py-4 text-text-secondary font-medium">
                  {{ index + 1 }}
                </td>
                <td class="px-6 py-4 font-medium">
                  {{ formatDate(log.tanggal) }}
                </td>
                <td class="px-6 py-4">
                  <p class="font-bold text-text-main">
                    {{ log.topik || "-" }}
                  </p>
                </td>
                <td class="px-6 py-4">
                  <p
                    class="text-text-secondary max-w-xs whitespace-normal line-clamp-2"
                  >
                    {{ log.deskripsi || "-" }}
                  </p>
                </td>
                <td class="px-6 py-4">
                  <p
                    class="text-text-secondary max-w-xs whitespace-normal line-clamp-2"
                  >
                    {{ log.catatan_dosen || "-" }}
                  </p>
                </td>
                <td class="px-6 py-4 text-center">
                  <span
                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold"
                    :class="getStatusClass(log.status)"
                  >
                    <span class="material-symbols-outlined text-[14px]">{{
                      getStatusIcon(log.status)
                    }}</span>
                    {{ getStatusLabel(log.status) }}
                  </span>
                </td>
                <td class="px-6 py-4 text-center">
                  <div class="flex items-center justify-center gap-1">
                    <button
                      @click="openEditModal(log)"
                      class="p-1.5 text-blue-500 hover:bg-blue-50 rounded-lg transition-colors"
                      title="Edit"
                    >
                      <span class="material-symbols-outlined text-[18px]">edit</span>
                    </button>
                    <button
                      @click="confirmDelete(log)"
                      class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                      title="Hapus"
                    >
                      <span class="material-symbols-outlined text-[18px]">delete</span>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </DataTableScroll>

        <!-- Tab Summary Footer -->
        <div v-if="pembimbingTabs.length > 0 && filteredBimbingan.length > 0" class="px-6 py-3 bg-sidebar-light/50 border-t border-border-light flex items-center justify-between">
          <p class="text-xs text-text-secondary">
            Menampilkan <strong>{{ filteredBimbingan.length }}</strong> bimbingan untuk <strong>{{ pembimbingTabs[activeTab]?.dosenName }}</strong>
          </p>
          <div class="flex items-center gap-4 text-xs text-text-secondary">
            <span class="flex items-center gap-1">
              <span class="size-2 rounded-full bg-green-500"></span>
              Disetujui: {{ countByStatus('approved') }}
            </span>
            <span class="flex items-center gap-1">
              <span class="size-2 rounded-full bg-yellow-500"></span>
              Menunggu: {{ countByStatus('pending') }}
            </span>
          </div>
        </div>
      </div>

      <!-- Back Button -->
      <div>
        <router-link
          to="/admin/bimbingan"
          class="inline-flex items-center gap-2 text-text-secondary hover:text-primary font-medium text-sm transition-colors"
        >
          <span class="material-symbols-outlined text-[18px]">arrow_back</span>
          Kembali ke daftar
        </router-link>
      </div>
    </template>

    <!-- Add/Edit Bimbingan Modal -->
    <Teleport to="body">
      <div
        v-if="showFormModal"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
        @click.self="closeFormModal"
      >
        <div
          class="bg-surface-light rounded-xl shadow-xl border border-border-light w-full max-w-lg max-h-[90vh] overflow-y-auto animate-fade-in-up"
        >
          <div class="flex items-center justify-between px-6 py-4 border-b border-border-light">
            <h3 class="text-lg font-bold text-text-main">
              {{ isEditing ? "Edit Bimbingan" : "Tambah Bimbingan" }}
            </h3>
            <button
              @click="closeFormModal"
              class="p-1 text-text-secondary hover:text-text-main rounded-lg hover:bg-gray-100 transition-colors"
            >
              <span class="material-symbols-outlined">close</span>
            </button>
          </div>
          <form @submit.prevent="submitForm" class="p-6 flex flex-col gap-4">
            <!-- Dosen (Pembimbing) -->
            <div>
              <label class="block text-sm font-semibold text-text-main mb-1.5">Dosen Pembimbing <span class="text-red-500">*</span></label>
              <select
                v-model="form.dosen_id"
                class="w-full px-3 py-2.5 border border-border-light rounded-lg text-sm bg-white focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all"
                required
              >
                <option value="">Pilih Pembimbing</option>
                <option
                  v-for="p in skripsi?.pembimbing || []"
                  :key="p.dosen?.id"
                  :value="p.dosen?.id"
                >
                  {{ p.dosen?.full_name || p.dosen?.nama }} ({{ p.jenis === 'pembimbing_1' ? 'Pembimbing 1' : 'Pembimbing 2' }})
                </option>
              </select>
            </div>

            <!-- Tanggal -->
            <div>
              <label class="block text-sm font-semibold text-text-main mb-1.5">Tanggal <span class="text-red-500">*</span></label>
              <input
                v-model="form.tanggal"
                type="date"
                class="w-full px-3 py-2.5 border border-border-light rounded-lg text-sm bg-white focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all"
                required
              />
            </div>

            <!-- Topik -->
            <div>
              <label class="block text-sm font-semibold text-text-main mb-1.5">Topik <span class="text-red-500">*</span></label>
              <input
                v-model="form.topik"
                type="text"
                placeholder="Contoh: Konsultasi BAB I"
                class="w-full px-3 py-2.5 border border-border-light rounded-lg text-sm bg-white focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all"
                required
              />
            </div>

            <!-- Deskripsi -->
            <div>
              <label class="block text-sm font-semibold text-text-main mb-1.5">Deskripsi</label>
              <textarea
                v-model="form.deskripsi"
                rows="3"
                placeholder="Deskripsi kegiatan bimbingan..."
                class="w-full px-3 py-2.5 border border-border-light rounded-lg text-sm bg-white focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all resize-none"
              ></textarea>
            </div>

            <!-- Catatan Dosen -->
            <div>
              <label class="block text-sm font-semibold text-text-main mb-1.5">Catatan Dosen</label>
              <textarea
                v-model="form.catatan_dosen"
                rows="2"
                placeholder="Catatan dari dosen..."
                class="w-full px-3 py-2.5 border border-border-light rounded-lg text-sm bg-white focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all resize-none"
              ></textarea>
            </div>

            <!-- Status -->
            <div>
              <label class="block text-sm font-semibold text-text-main mb-1.5">Status</label>
              <select
                v-model="form.status"
                class="w-full px-3 py-2.5 border border-border-light rounded-lg text-sm bg-white focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all"
              >
                <option value="approved">Disetujui</option>
                <option value="pending">Menunggu</option>
                <option value="revision">Revisi</option>
                <option value="rejected">Ditolak</option>
              </select>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 pt-2">
              <button
                type="button"
                @click="closeFormModal"
                class="px-4 py-2.5 text-sm font-medium text-text-secondary hover:text-text-main border border-border-light rounded-lg hover:bg-gray-50 transition-all"
              >
                Batal
              </button>
              <button
                type="submit"
                :disabled="submitting"
                class="px-5 py-2.5 text-sm font-semibold bg-primary hover:bg-primary-dark text-white rounded-lg shadow-sm transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
              >
                <span v-if="submitting" class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></span>
                {{ isEditing ? "Simpan Perubahan" : "Tambah" }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>

    <!-- Generate Bulk Modal -->
    <Teleport to="body">
      <div
        v-if="showGenerateModal"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
        @click.self="closeGenerateModal"
      >
        <div
          class="bg-surface-light rounded-xl shadow-xl border border-border-light w-full max-w-lg max-h-[90vh] overflow-y-auto animate-fade-in-up"
        >
          <div class="flex items-center justify-between px-6 py-4 border-b border-border-light">
            <div class="flex items-center gap-2">
              <div class="p-2 bg-linear-to-br from-amber-100 to-orange-100 rounded-lg">
                <span class="material-symbols-outlined text-amber-600 text-[20px]">bolt</span>
              </div>
              <h3 class="text-lg font-bold text-text-main">Generate Bimbingan Otomatis</h3>
            </div>
            <button
              @click="closeGenerateModal"
              class="p-1 text-text-secondary hover:text-text-main rounded-lg hover:bg-gray-100 transition-colors"
            >
              <span class="material-symbols-outlined">close</span>
            </button>
          </div>
          <form @submit.prevent="submitGenerate" class="p-6 flex flex-col gap-4">
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 text-xs text-amber-800">
              <span class="material-symbols-outlined text-[14px] align-middle mr-1">info</span>
              Fitur ini akan otomatis membuat data bimbingan dengan topik dan tanggal yang bervariasi.
            </div>

            <!-- Jumlah -->
            <div>
              <label class="block text-sm font-semibold text-text-main mb-1.5">Jumlah Bimbingan <span class="text-red-500">*</span></label>
              <input
                v-model.number="generateForm.jumlah"
                type="number"
                min="1"
                max="50"
                placeholder="Contoh: 8"
                class="w-full px-3 py-2.5 border border-border-light rounded-lg text-sm bg-white focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all"
                required
              />
            </div>

            <!-- Dosen -->
            <div>
              <label class="block text-sm font-semibold text-text-main mb-1.5">Dosen Pembimbing <span class="text-red-500">*</span></label>
              <select
                v-model="generateForm.dosen_id"
                class="w-full px-3 py-2.5 border border-border-light rounded-lg text-sm bg-white focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all"
                required
              >
                <option value="">Pilih Pembimbing</option>
                <option
                  v-for="p in skripsi?.pembimbing || []"
                  :key="p.dosen?.id"
                  :value="p.dosen?.id"
                >
                  {{ p.dosen?.full_name || p.dosen?.nama }} ({{ p.jenis === 'pembimbing_1' ? 'Pembimbing 1' : 'Pembimbing 2' }})
                </option>
              </select>
            </div>

            <!-- Tanggal Mulai -->
            <div>
              <label class="block text-sm font-semibold text-text-main mb-1.5">Tanggal Mulai</label>
              <input
                v-model="generateForm.tanggal_mulai"
                type="date"
                class="w-full px-3 py-2.5 border border-border-light rounded-lg text-sm bg-white focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all"
              />
              <p class="text-xs text-text-secondary mt-1">Kosongkan untuk otomatis menentukan</p>
            </div>

            <!-- Interval Hari -->
            <div>
              <label class="block text-sm font-semibold text-text-main mb-1.5">Interval (Hari)</label>
              <input
                v-model.number="generateForm.interval_hari"
                type="number"
                min="1"
                max="30"
                placeholder="7"
                class="w-full px-3 py-2.5 border border-border-light rounded-lg text-sm bg-white focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all"
              />
              <p class="text-xs text-text-secondary mt-1">Jarak hari antar bimbingan (default: 7 hari)</p>
            </div>

            <!-- Status -->
            <div>
              <label class="block text-sm font-semibold text-text-main mb-1.5">Status</label>
              <select
                v-model="generateForm.status"
                class="w-full px-3 py-2.5 border border-border-light rounded-lg text-sm bg-white focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all"
              >
                <option value="approved">Disetujui</option>
                <option value="pending">Menunggu</option>
              </select>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 pt-2">
              <button
                type="button"
                @click="closeGenerateModal"
                class="px-4 py-2.5 text-sm font-medium text-text-secondary hover:text-text-main border border-border-light rounded-lg hover:bg-gray-50 transition-all"
              >
                Batal
              </button>
              <button
                type="submit"
                :disabled="submitting"
                class="px-5 py-2.5 text-sm font-semibold bg-linear-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white rounded-lg shadow-sm transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
              >
                <span v-if="submitting" class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></span>
                <span v-else class="material-symbols-outlined text-[16px]">bolt</span>
                Generate {{ generateForm.jumlah || 0 }} Bimbingan
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>

    <!-- Delete Confirmation Modal -->
    <Teleport to="body">
      <div
        v-if="showDeleteModal"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
        @click.self="closeDeleteModal"
      >
        <div
          class="bg-surface-light rounded-xl shadow-xl border border-border-light w-full max-w-sm animate-fade-in-up"
        >
          <div class="p-6 text-center">
            <div class="mx-auto w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mb-4">
              <span class="material-symbols-outlined text-red-600 text-2xl">delete_forever</span>
            </div>
            <h3 class="text-lg font-bold text-text-main mb-2">Hapus Bimbingan?</h3>
            <p class="text-sm text-text-secondary mb-1">
              Topik: <strong>{{ deleteTarget?.topik }}</strong>
            </p>
            <p class="text-sm text-text-secondary mb-6">
              Data yang dihapus tidak dapat dikembalikan.
            </p>
            <div class="flex items-center justify-center gap-3">
              <button
                @click="closeDeleteModal"
                class="px-4 py-2.5 text-sm font-medium text-text-secondary hover:text-text-main border border-border-light rounded-lg hover:bg-gray-50 transition-all"
              >
                Batal
              </button>
              <button
                @click="doDelete"
                :disabled="submitting"
                class="px-5 py-2.5 text-sm font-semibold bg-red-600 hover:bg-red-700 text-white rounded-lg shadow-sm transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
              >
                <span v-if="submitting" class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></span>
                Hapus
              </button>
            </div>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Toast Notification -->
    <Teleport to="body">
      <Transition name="toast">
        <div
          v-if="toast.show"
          class="fixed top-6 right-6 z-60 px-4 py-3 rounded-lg shadow-lg text-sm font-medium flex items-center gap-2 max-w-sm"
          :class="{
            'bg-green-600 text-white': toast.type === 'success',
            'bg-red-600 text-white': toast.type === 'error',
          }"
        >
          <span class="material-symbols-outlined text-[18px]">
            {{ toast.type === "success" ? "check_circle" : "error" }}
          </span>
          {{ toast.message }}
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRoute } from "vue-router";
import adminService from "../../../services/adminService";
import { useAuthStore } from "../../../stores/auth";

const authStore = useAuthStore();
const isSuperAdmin = computed(() => authStore.isSuperAdmin);
const canAddBimbingan = computed(() => {
  const gender = authStore.user?.jenis_kelamin;
  if (gender === 'P') {
    return authStore.isAdmin; // admin or super_admin only
  }
  return true; // laki-laki or null: all roles
});

const route = useRoute();
const loading = ref(true);
const skripsi = ref(null);
const bimbinganList = ref([]);
const submitting = ref(false);
const activeTab = ref(0);

// Build tabs from pembimbing data
const pembimbingTabs = computed(() => {
  const pembimbing = skripsi.value?.pembimbing || [];
  return pembimbing.map((p) => {
    const dosenId = p.dosen?.id;
    const dosenName = p.dosen?.full_name || p.dosen?.nama || "-";
    const label = p.jenis === "pembimbing_1" ? "Pembimbing 1" : "Pembimbing 2";
    const count = bimbinganList.value.filter(
      (b) => b.dosen_id === dosenId || b.dosen?.id === dosenId
    ).length;
    return { dosenId, dosenName, label, count };
  });
});

// Filter bimbingan by active tab's dosen
const filteredBimbingan = computed(() => {
  if (pembimbingTabs.value.length === 0) return bimbinganList.value;
  const tab = pembimbingTabs.value[activeTab.value];
  if (!tab) return bimbinganList.value;
  return bimbinganList.value.filter(
    (b) => b.dosen_id === tab.dosenId || b.dosen?.id === tab.dosenId
  );
});

// Count by status within active tab
const countByStatus = (status) => {
  return filteredBimbingan.value.filter((b) => b.status === status).length;
};

// Form modal state
const showFormModal = ref(false);
const isEditing = ref(false);
const editingId = ref(null);
const form = ref({
  dosen_id: "",
  tanggal: "",
  topik: "",
  deskripsi: "",
  catatan_dosen: "",
  status: "approved",
});

// Generate modal state
const showGenerateModal = ref(false);
const generateForm = ref({
  jumlah: 8,
  dosen_id: "",
  tanggal_mulai: "",
  interval_hari: 7,
  status: "approved",
});

// Delete modal state
const showDeleteModal = ref(false);
const deleteTarget = ref(null);

// Toast state
const toast = ref({ show: false, message: "", type: "success" });

const showToast = (message, type = "success") => {
  toast.value = { show: true, message, type };
  setTimeout(() => {
    toast.value.show = false;
  }, 3000);
};

const fetchData = async () => {
  try {
    loading.value = true;
    const skripsiId = route.params.id;

    // Fetch skripsi info (mahasiswa, pembimbing)
    const skripsiRes = await adminService.getSkripsiDetail(skripsiId);
    if (skripsiRes.success) {
      skripsi.value = skripsiRes.data;
    }

    // Fetch bimbingan logs
    const bimbinganRes = await adminService.getBimbinganDetail(skripsiId);
    if (bimbinganRes.success) {
      bimbinganList.value = bimbinganRes.data;
    }
  } catch (error) {
    console.error("Failed to fetch data:", error);
  } finally {
    loading.value = false;
  }
};

// --- Add / Edit ---
const openAddModal = () => {
  isEditing.value = false;
  editingId.value = null;
  // Pre-select the active tab's dosen
  const currentTab = pembimbingTabs.value[activeTab.value];
  form.value = {
    dosen_id: currentTab?.dosenId || skripsi.value?.pembimbing?.[0]?.dosen?.id || "",
    tanggal: new Date().toISOString().split("T")[0],
    topik: "",
    deskripsi: "",
    catatan_dosen: "",
    status: "approved",
  };
  showFormModal.value = true;
};

const openEditModal = (log) => {
  isEditing.value = true;
  editingId.value = log.id;
  form.value = {
    dosen_id: log.dosen_id || log.dosen?.id || "",
    tanggal: log.tanggal ? log.tanggal.split("T")[0] : "",
    topik: log.topik || "",
    deskripsi: log.deskripsi || "",
    catatan_dosen: log.catatan_dosen || "",
    status: log.status || "approved",
  };
  showFormModal.value = true;
};

const closeFormModal = () => {
  showFormModal.value = false;
};

const submitForm = async () => {
  try {
    submitting.value = true;
    const skripsiId = route.params.id;

    if (isEditing.value) {
      await adminService.updateBimbingan(editingId.value, {
        ...form.value,
      });
      showToast("Bimbingan berhasil diperbarui");
    } else {
      await adminService.storeBimbingan({
        skripsi_id: skripsiId,
        ...form.value,
      });
      showToast("Bimbingan berhasil ditambahkan");
    }

    closeFormModal();
    await fetchBimbinganOnly();
  } catch (error) {
    showToast(error.response?.data?.message || "Gagal menyimpan bimbingan", "error");
  } finally {
    submitting.value = false;
  }
};

// --- Generate ---
const openGenerateModal = () => {
  const currentTab = pembimbingTabs.value[activeTab.value];
  generateForm.value = {
    jumlah: 8,
    dosen_id: currentTab?.dosenId || skripsi.value?.pembimbing?.[0]?.dosen?.id || "",
    tanggal_mulai: "",
    interval_hari: 7,
    status: "approved",
  };
  showGenerateModal.value = true;
};

const closeGenerateModal = () => {
  showGenerateModal.value = false;
};

const submitGenerate = async () => {
  try {
    submitting.value = true;
    const skripsiId = route.params.id;
    const payload = {
      skripsi_id: skripsiId,
      jumlah: generateForm.value.jumlah,
      dosen_id: generateForm.value.dosen_id,
      status: generateForm.value.status,
    };
    if (generateForm.value.tanggal_mulai) {
      payload.tanggal_mulai = generateForm.value.tanggal_mulai;
    }
    if (generateForm.value.interval_hari) {
      payload.interval_hari = generateForm.value.interval_hari;
    }

    const res = await adminService.generateBulkBimbingan(payload);
    showToast(res.message || `Berhasil generate ${generateForm.value.jumlah} bimbingan`);
    closeGenerateModal();
    await fetchBimbinganOnly();
  } catch (error) {
    showToast(error.response?.data?.message || "Gagal generate bimbingan", "error");
  } finally {
    submitting.value = false;
  }
};

// --- Delete ---
const confirmDelete = (log) => {
  deleteTarget.value = log;
  showDeleteModal.value = true;
};

const closeDeleteModal = () => {
  showDeleteModal.value = false;
  deleteTarget.value = null;
};

const doDelete = async () => {
  try {
    submitting.value = true;
    await adminService.deleteBimbingan(deleteTarget.value.id);
    showToast("Bimbingan berhasil dihapus");
    closeDeleteModal();
    await fetchBimbinganOnly();
  } catch (error) {
    showToast(error.response?.data?.message || "Gagal menghapus bimbingan", "error");
  } finally {
    submitting.value = false;
  }
};

// --- Helpers ---
const fetchBimbinganOnly = async () => {
  const skripsiId = route.params.id;
  const bimbinganRes = await adminService.getBimbinganDetail(skripsiId);
  if (bimbinganRes.success) {
    bimbinganList.value = bimbinganRes.data;
  }
};

const formatDate = (dateStr) => {
  if (!dateStr) return "-";
  const date = new Date(dateStr);
  return date.toLocaleDateString("id-ID", {
    day: "2-digit",
    month: "short",
    year: "numeric",
  });
};

const getStatusClass = (status) => {
  const classes = {
    approved:
      "bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400",
    pending:
      "bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400",
    revision:
      "bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400",
    rejected: "bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400",
  };
  return classes[status] || "bg-gray-100 text-gray-600";
};

const getStatusIcon = (status) => {
  const icons = {
    approved: "check_circle",
    pending: "schedule",
    revision: "edit_note",
    rejected: "cancel",
  };
  return icons[status] || "help";
};

const getStatusLabel = (status) => {
  const labels = {
    approved: "Disetujui",
    pending: "Menunggu",
    revision: "Revisi",
    rejected: "Ditolak",
  };
  return labels[status] || status || "-";
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
  fetchData();
});
</script>

<style scoped>
.toast-enter-active {
  animation: slideInRight 0.3s ease-out;
}
.toast-leave-active {
  animation: slideOutRight 0.3s ease-in;
}
@keyframes slideInRight {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}
@keyframes slideOutRight {
  from {
    transform: translateX(0);
    opacity: 1;
  }
  to {
    transform: translateX(100%);
    opacity: 0;
  }
}
</style>
