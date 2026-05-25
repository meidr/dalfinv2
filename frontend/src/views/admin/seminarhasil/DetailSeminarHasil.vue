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
          to="/admin/seminarhasil"
          class="text-text-secondary hover:text-primary font-medium"
        >
          Seminar Hasil
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
            class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-red-600 bg-red-50 rounded-lg hover:bg-red-100 border border-red-200 transition-colors"
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
                  class="flex items-center gap-3 p-2 bg-background-light border border-border-light rounded-lg"
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
              Daftar dosen penguji seminar hasil
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
              class="flex items-center justify-between p-4 bg-gray-50 dark:bg-background rounded-xl border border-border-light"
            >
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
                      penguji.dosen?.nama_lengkap || penguji.dosen?.nama || "-"
                    }}
                  </p>
                  <span
                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium mt-0.5"
                    :class="getPeranClass(penguji.peran)"
                  >
                    {{ getPeranLabel(penguji.peran) }}
                  </span>
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
                  <p class="text-xs text-text-secondary">Nilai</p>
                </div>
                <span
                  v-else
                  class="text-xs text-text-secondary italic bg-gray-100 px-2 py-1 rounded"
                >
                  Belum dinilai
                </span>
                <button
                  v-if="seminar.status === 'terjadwal' || !isLocked"
                  @click="removePenguji(penguji)"
                  class="p-1.5 text-text-secondary hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                  title="Hapus Penguji"
                >
                  <span class="material-symbols-outlined text-[18px]"
                    >close</span
                  >
                </button>
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
              class="flex flex-col sm:flex-row sm:items-center gap-3 p-4 bg-gray-50 dark:bg-background rounded-xl border border-border-light"
            >
              <div class="flex items-center gap-3 flex-1">
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
                  <span
                    class="text-xs font-medium capitalize"
                    :class="getPeranClass(penguji.peran)"
                  >
                    {{ getPeranLabel(penguji.peran) }}
                  </span>
                </div>
              </div>
              <div class="flex items-center gap-3">
                <div class="w-24">
                  <input
                    v-model.number="penguji.nilai"
                    type="number"
                    min="0"
                    max="100"
                    step="0.01"
                    placeholder="0-100"
                    class="w-full px-3 py-2 border border-border-light rounded-lg text-center text-sm font-bold focus:ring-2 focus:ring-primary/20 focus:border-primary"
                    :disabled="isLocked"
                  />
                </div>
                <div class="w-48">
                  <input
                    v-model="penguji.catatan"
                    type="text"
                    placeholder="Catatan (opsional)"
                    class="w-full px-3 py-2 border border-border-light rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary"
                    :disabled="isLocked"
                  />
                </div>
              </div>
            </div>

            <!-- Rata-rata Nilai -->
            <div
              class="flex items-center justify-between p-4 bg-primary/5 rounded-xl border border-primary/20"
            >
              <div>
                <p class="text-sm font-bold text-text-main">Rata-rata Nilai</p>
                <p class="text-xs text-text-secondary">
                  Dari
                  {{
                    nilaiForm.pengujiNilai.filter(
                      (p) => p.nilai !== null && p.nilai !== "",
                    ).length
                  }}
                  penguji
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
                  class="w-full px-3 py-2 border border-border-light rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary"
                  :disabled="isLocked"
                >
                  <option value="">Pilih Hasil</option>
                  <option value="lulus">Lulus</option>
                  <option value="lulus_bersyarat">Lulus Bersyarat</option>
                  <option value="tidak_lulus">Tidak Lulus</option>
                  <option value="mengulang">Mengulang</option>
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
                class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-orange-600 bg-orange-50 rounded-lg hover:bg-orange-100 border border-orange-200 transition-colors"
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
                class="flex items-center gap-2 px-4 py-2 bg-green-50 text-green-600 rounded-lg border border-green-200"
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
        to="/admin/seminarhasil"
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
                class="w-full px-3 py-2 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
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
                  class="w-full px-3 py-2 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
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
                  class="w-full px-3 py-2 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
                  required
                />
              </div>
            </div>
            <div class="flex gap-3 pt-4">
              <button
                type="button"
                @click="showEditJadwalModal = false"
                class="flex-1 px-4 py-2.5 border border-border-light rounded-lg text-text-secondary hover:bg-background-light transition-colors"
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
                class="w-full px-3 py-2 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
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
                class="w-full px-3 py-2 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary"
                required
              >
                <option value="">Pilih Peran</option>
                <option value="ketua">Ketua</option>
                <option value="penguji_1">Penguji 1</option>
                <option value="penguji_2">Penguji 2</option>
              </select>
            </div>
            <div class="flex gap-3 pt-4">
              <button
                type="button"
                @click="showAddPengujiModal = false"
                class="flex-1 px-4 py-2.5 border border-border-light rounded-lg text-text-secondary hover:bg-background-light transition-colors"
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
const filteredDosenList = ref([]);

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

const averageNilai = computed(() => {
  const validNilai = nilaiForm.pengujiNilai.filter(
    (p) => p.nilai !== null && p.nilai !== "" && !isNaN(p.nilai),
  );
  if (validNilai.length === 0) return "-";
  const sum = validNilai.reduce((acc, p) => acc + Number(p.nilai), 0);
  return (sum / validNilai.length).toFixed(2);
});

const fetchSeminarDetail = async () => {
  try {
    loading.value = true;
    const response = await adminService.getSeminarHasilDetail(route.params.id);
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
      nilai: p.nilai,
      catatan: p.catatan || "",
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
    await adminService.deleteSeminarHasil(seminar.value.id);
    alert("Seminar berhasil dihapus.");
    router.push("/admin/seminarhasil");
  } catch (error) {
    console.error("Failed to delete seminar:", error);
    alert(
      "Gagal menghapus seminar: " +
        (error.response?.data?.message || error.message),
    );
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
    await adminService.updateSeminarHasil(seminar.value.id, {
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
    const api = (await import("../../../services/api")).default;
    await api.post(`/admin/seminar-hasil/${seminar.value.id}/penguji`, {
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
      `/admin/seminar-hasil/${seminar.value.id}/penguji/${penguji.id}`,
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

    // Update nilai per penguji
    for (const p of nilaiForm.pengujiNilai) {
      if (p.nilai !== null && p.nilai !== "") {
        await api.put(
          `/admin/seminar-hasil/${seminar.value.id}/penguji/${p.penguji_id}`,
          {
            nilai: Number(p.nilai),
            catatan: p.catatan,
          },
        );
      }
    }

    // Update seminar nilai (average) and status
    await adminService.updateSeminarHasil(seminar.value.id, {
      nilai: averageNilai.value !== "-" ? Number(averageNilai.value) : null,
      catatan: nilaiForm.catatan,
      status: "selesai",
    });

    // Update skripsi status to sidang (next step after semhas)
    if (seminar.value.skripsi?.id) {
      await adminService.updateSkripsi(seminar.value.skripsi.id, {
        status: "sidang",
        _method: "PUT",
      });
    }

    // Create berita acara
    try {
      const now = new Date();
      const nomorBA = `BA/SEMHAS/${now.getFullYear()}/${String(now.getMonth() + 1).padStart(2, "0")}/${seminar.value.id}`;
      await api.post(`/admin/seminar-hasil/${seminar.value.id}/berita-acara`, {
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

onMounted(() => {
  fetchSeminarDetail();
});
</script>
