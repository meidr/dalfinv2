<template>
  <div class="max-w-7xl mx-auto flex flex-col gap-8 animate-fade-in-up">
    <div class="flex flex-col gap-1">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-text-main text-3xl font-bold leading-tight">
            Sidang Skripsi
          </h1>
          <p class="text-text-secondary text-sm font-normal">
            Daftar mahasiswa yang siap melaksanakan sidang akhir skripsi.
          </p>
        </div>
        <button
          @click="openScheduleModal"
          class="flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-600 transition-all text-sm font-bold shadow-md shadow-primary/20"
        >
          <span class="material-symbols-outlined text-[18px]">add</span>
          Jadwalkan Baru
        </button>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
        <div
          class="flex items-center p-4 bg-surface-light border border-border-light rounded-xl shadow-sm hover:shadow-md transition-shadow"
        >
          <div class="p-2.5 bg-blue-50 text-blue-600 rounded-lg mr-3">
            <span class="material-symbols-outlined text-[22px]">schedule</span>
          </div>
          <div>
            <p
              class="text-text-secondary text-[10px] font-bold uppercase tracking-wider mb-0.5"
            >
              Terjadwal
            </p>
            <h3 class="text-xl font-bold text-text-main">
              {{ stats.terjadwal }}
            </h3>
          </div>
        </div>
        <div
          class="flex items-center p-4 bg-surface-light border border-border-light rounded-xl shadow-sm hover:shadow-md transition-shadow"
        >
          <div class="p-2.5 bg-orange-50 text-orange-600 rounded-lg mr-3">
            <span class="material-symbols-outlined text-[22px]"
              >pending_actions</span
            >
          </div>
          <div>
            <p
              class="text-text-secondary text-[10px] font-bold uppercase tracking-wider mb-0.5"
            >
              Menunggu Nilai
            </p>
            <h3 class="text-xl font-bold text-text-main">
              {{ stats.sedang_ujian }}
            </h3>
          </div>
        </div>
        <div
          class="flex items-center p-4 bg-surface-light border border-border-light rounded-xl shadow-sm hover:shadow-md transition-shadow"
        >
          <div class="p-2.5 bg-green-50 text-green-600 rounded-lg mr-3">
            <span class="material-symbols-outlined text-[22px]"
              >check_circle</span
            >
          </div>
          <div>
            <p
              class="text-text-secondary text-[10px] font-bold uppercase tracking-wider mb-0.5"
            >
              Selesai
            </p>
            <h3 class="text-xl font-bold text-text-main">
              {{ stats.selesai }}
            </h3>
          </div>
        </div>
        <div
          class="flex items-center p-4 bg-surface-light border border-border-light rounded-xl shadow-sm hover:shadow-md transition-shadow"
        >
          <div class="p-2.5 bg-purple-50 text-purple-600 rounded-lg mr-3">
            <span class="material-symbols-outlined text-[22px]"
              >history_edu</span
            >
          </div>
          <div>
            <p
              class="text-text-secondary text-[10px] font-bold uppercase tracking-wider mb-0.5"
            >
              Total Sidang
            </p>
            <h3 class="text-xl font-bold text-text-main">{{ stats.total }}</h3>
          </div>
        </div>
      </div>

      <!-- Filters Row -->
      <div class="flex flex-wrap items-center gap-3 mt-3">
        <select
          v-model="filterTahunAkademik"
          @change="onFilterChange"
          class="px-3 py-2 bg-white border border-border-light rounded-lg text-sm focus:ring-1 focus:ring-primary"
        >
          <option value="">Semua Tahun Akademik</option>
          <option v-for="ta in tahunAkademikOptions" :key="ta" :value="ta">
            {{ ta }}
          </option>
        </select>
        <select
          v-model="filterProdi"
          @change="onFilterChange"
          class="px-3 py-2 bg-white border border-border-light rounded-lg text-sm focus:ring-1 focus:ring-primary"
        >
          <option value="">Semua Prodi</option>
          <option v-for="p in prodiList" :key="p.id" :value="p.id">
            {{ p.nama }}
          </option>
        </select>
        <select
          v-model="filterSemester"
          @change="onFilterChange"
          class="px-3 py-2 bg-white border border-border-light rounded-lg text-sm focus:ring-1 focus:ring-primary"
        >
          <option value="">Semua Semester</option>
          <option value="ganjil">Ganjil</option>
          <option value="genap">Genap</option>
        </select>
        <select
          v-model="filterStatus"
          @change="onFilterChange"
          class="px-3 py-2 bg-white border border-border-light rounded-lg text-sm focus:ring-1 focus:ring-primary"
        >
          <option value="">Semua Status</option>
          <option value="pending">Menunggu Nilai</option>
          <option value="selesai">Selesai</option>
          <option value="terjadwal">Terjadwal</option>
        </select>
        <div class="flex-1"></div>
        <button
          @click="exportJadwalPdf"
          :disabled="exporting"
          class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all text-sm font-bold disabled:opacity-50"
        >
          <span class="material-symbols-outlined text-[18px]">download</span>
          {{ exporting ? "Memproses..." : "Export Jadwal PDF" }}
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

    <!-- Table Container -->
    <div
      v-else
      class="flex flex-col bg-surface-light border border-border-light rounded-xl shadow-sm"
    >
      <div
        class="p-5 border-b border-border-light flex flex-col md:flex-row gap-4 items-center justify-between"
      >
        <div class="relative w-full md:max-w-md">
          <div
            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
          >
            <span class="material-symbols-outlined text-text-secondary"
              >search</span
            >
          </div>
          <input
            v-model="searchQuery"
            @input="debouncedSearch"
            class="block w-full pl-10 pr-3 py-2.5 border border-border-light rounded-lg leading-5 bg-background-light text-text-main placeholder-text-secondary focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary sm:text-sm transition-shadow dark:bg-background"
            placeholder="Cari mahasiswa..."
          />
        </div>
        <span
          class="px-3 py-2 rounded-lg bg-blue-50 text-blue-600 text-xs font-bold border border-blue-100 flex items-center whitespace-nowrap"
          >{{ pagination.total }} Data</span
        >
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
          <thead
            class="bg-sidebar-light/50 text-text-secondary font-medium border-b border-border-light"
          >
            <tr>
              <th class="px-6 py-4">Mahasiswa</th>
              <th class="px-6 py-4">Judul Skripsi</th>
              <th class="px-6 py-4">Jadwal &amp; Ruang</th>
              <th class="px-6 py-4">Penguji</th>
              <th class="px-6 py-4">Status</th>
              <th class="px-6 py-4 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border-light">
            <tr v-if="ujianList.length === 0">
              <td colspan="6" class="p-12 text-center text-text-secondary">
                Tidak ada data sidang
              </td>
            </tr>
            <tr
              v-for="item in ujianList"
              :key="item.id"
              class="group hover:bg-sidebar-light/30 transition-colors"
            >
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div
                    class="size-10 rounded-full flex items-center justify-center text-xs font-bold shrink-0"
                    :class="getAvatarColor(item.skripsi?.mahasiswa?.nama)"
                  >
                    {{ getInitials(item.skripsi?.mahasiswa?.nama) }}
                  </div>
                  <div>
                    <p class="font-bold text-text-main text-sm">
                      {{ item.skripsi?.mahasiswa?.nama || "-" }}
                    </p>
                    <p
                      class="text-xs text-text-secondary font-medium font-mono"
                    >
                      {{ item.skripsi?.mahasiswa?.nim || "-" }}
                    </p>
                  </div>
                </div>
              </td>
              <td
                class="px-6 py-4 max-w-xs truncate"
                :title="item.skripsi?.judul"
              >
                {{ item.skripsi?.judul || "-" }}
              </td>
              <td class="px-6 py-4">
                <div class="flex flex-col gap-1">
                  <div
                    class="flex items-center gap-1.5 text-text-main text-xs font-bold"
                  >
                    <span
                      class="material-symbols-outlined text-[14px] text-primary"
                      >calendar_month</span
                    >
                    {{ formatDate(item.tanggal) }}
                  </div>
                  <div
                    class="flex items-center gap-1.5 text-text-secondary text-xs"
                  >
                    <span class="material-symbols-outlined text-[14px]"
                      >schedule</span
                    >
                    {{ formatTime(item.waktu) }}
                  </div>
                  <div
                    class="flex items-center gap-1.5 text-text-secondary text-xs"
                  >
                    <span class="material-symbols-outlined text-[14px]"
                      >meeting_room</span
                    >
                    {{ item.ruangan || "-" }}
                  </div>
                </div>
              </td>
              <td class="px-6 py-4">
                <div v-if="item.penguji?.length" class="flex flex-col gap-1">
                  <div
                    v-for="p in item.penguji"
                    :key="p.id"
                    class="flex items-center gap-1.5 text-xs"
                  >
                    <span
                      class="material-symbols-outlined text-[14px] text-primary"
                      >person</span
                    >
                    <span class="text-text-main font-medium">{{
                      p.dosen?.full_name || p.dosen?.nama || "-"
                    }}</span>
                    <span
                      class="text-[9px] px-1.5 py-0.5 rounded bg-gray-100 text-text-secondary"
                      >{{ getPeranLabel(p.peran) }}</span
                    >
                  </div>
                </div>
                <span v-else class="text-xs text-text-secondary italic"
                  >Belum ada</span
                >
              </td>
              <td class="px-6 py-4">
                <span
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium"
                  :class="getStatusClass(item.status)"
                >
                  <span
                    class="w-1.5 h-1.5 rounded-full"
                    :class="getStatusDot(item.status)"
                  ></span>
                  {{ getStatusLabel(item.status) }}
                </span>
                <div
                  v-if="item.status === 'selesai' && item.hasil"
                  class="mt-1"
                >
                  <span
                    class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold"
                    :class="getHasilClass(item.hasil)"
                  >
                    {{ getHasilLabel(item.hasil) }}
                  </span>
                </div>
                <div v-if="item.nilai" class="mt-1 flex items-center gap-1.5">
                  <span class="text-xs text-text-secondary"
                    >Rata-rata:
                    <strong class="text-text-main">{{
                      item.nilai
                    }}</strong></span
                  >
                  <span
                    v-if="item.grade"
                    class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-indigo-100 text-indigo-700 border border-indigo-200"
                    >{{ item.grade }}</span
                  >
                </div>
              </td>
              <td class="px-6 py-4 text-right">
                <div class="flex items-center justify-end gap-1.5">
                  <button
                    @click="openEditModal(item)"
                    class="size-8 flex items-center justify-center rounded-lg border border-border-light text-text-secondary hover:text-orange-500 hover:bg-orange-50 hover:border-orange-300 transition-all"
                    title="Edit Sidang"
                  >
                    <span class="material-symbols-outlined text-[18px]"
                      >edit</span
                    >
                  </button>
                  <button
                    @click="viewDetail(item)"
                    class="size-8 flex items-center justify-center rounded-lg border border-border-light text-text-secondary hover:text-primary hover:bg-blue-50 hover:border-primary transition-all"
                    title="Lihat Detail"
                  >
                    <span class="material-symbols-outlined text-[18px]"
                      >visibility</span
                    >
                  </button>
                  <button
                    v-if="item.penguji?.length"
                    @click="downloadSkPenguji(item)"
                    class="size-8 flex items-center justify-center rounded-lg border border-border-light text-text-secondary hover:text-green-600 hover:bg-green-50 hover:border-green-300 transition-all"
                    title="Cetak SK Penguji"
                  >
                    <span class="material-symbols-outlined text-[18px]"
                      >print</span
                    >
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div
        class="flex items-center justify-between px-6 py-4 border-t border-border-light"
      >
        <p class="text-sm text-text-secondary">
          Menampilkan
          <span class="font-medium text-text-main">{{
            pagination.from || 0
          }}</span>
          -
          <span class="font-medium text-text-main">{{
            pagination.to || 0
          }}</span>
          dari
          <span class="font-medium text-text-main">{{ pagination.total }}</span>
        </p>
        <div class="flex gap-1">
          <button
            @click="goToPage(pagination.current_page - 1)"
            :disabled="pagination.current_page <= 1"
            class="size-8 flex items-center justify-center rounded border border-border-light hover:bg-gray-50 text-text-secondary disabled:opacity-50"
          >
            <span class="material-symbols-outlined text-sm">chevron_left</span>
          </button>
          <button
            class="size-8 flex items-center justify-center rounded border border-primary bg-primary text-white text-xs font-bold"
          >
            {{ pagination.current_page }}
          </button>
          <button
            @click="goToPage(pagination.current_page + 1)"
            :disabled="pagination.current_page >= pagination.last_page"
            class="size-8 flex items-center justify-center rounded border border-border-light hover:bg-gray-50 text-text-secondary"
          >
            <span class="material-symbols-outlined text-sm">chevron_right</span>
          </button>
        </div>
      </div>
    </div>

    <!-- ========== SCHEDULE / CREATE MODAL ========== -->
    <Transition name="modal-fade">
      <div
        v-if="showScheduleModal"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
        @click.self="showScheduleModal = false"
      >
        <div
          class="bg-white dark:bg-surface-light rounded-xl shadow-2xl w-full max-w-lg"
        >
          <div class="p-6 border-b border-border-light">
            <h2 class="text-xl font-bold text-text-main">
              Jadwalkan Sidang Baru
            </h2>
            <p class="text-sm text-text-secondary mt-1">
              Pilih mahasiswa dan atur jadwal sidang skripsi
            </p>
          </div>
          <form @submit.prevent="saveSchedule" class="p-6 space-y-4">
            <!-- Searchable Mahasiswa -->
            <div class="relative">
              <label class="block text-sm font-medium text-text-main mb-1"
                >Mahasiswa (Skripsi)</label
              >
              <div class="relative">
                <span
                  class="material-symbols-outlined absolute left-3 top-2.5 text-[18px] text-text-secondary"
                  >search</span
                >
                <input
                  v-model="mahasiswaSearch"
                  @input="onMahasiswaSearch"
                  @focus="showMahasiswaDropdown = true"
                  type="text"
                  class="w-full pl-10 pr-3 py-2 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm"
                  placeholder="Ketik nama atau NIM mahasiswa..."
                  autocomplete="off"
                />
              </div>
              <div
                v-if="scheduleForm.skripsi_id && selectedMahasiswaName"
                class="flex items-center gap-2 mt-2 px-3 py-2 bg-blue-50 border border-blue-200 rounded-lg text-sm"
              >
                <span class="material-symbols-outlined text-primary text-[16px]"
                  >check_circle</span
                >
                <span class="text-primary font-medium">{{
                  selectedMahasiswaName
                }}</span>
                <button
                  type="button"
                  @click="clearMahasiswaSelection"
                  class="ml-auto text-text-secondary hover:text-red-500"
                >
                  <span class="material-symbols-outlined text-[16px]"
                    >close</span
                  >
                </button>
              </div>
              <div
                v-if="showMahasiswaDropdown && filteredSkripsi.length > 0"
                class="absolute z-10 mt-1 w-full bg-white border border-border-light rounded-lg shadow-lg max-h-48 overflow-y-auto"
              >
                <button
                  v-for="s in filteredSkripsi"
                  :key="s.id"
                  type="button"
                  @click="selectMahasiswa(s)"
                  class="w-full text-left px-4 py-2.5 hover:bg-blue-50 transition-colors flex items-center gap-3 border-b border-border-light last:border-0"
                >
                  <div
                    class="size-8 rounded-full flex items-center justify-center text-[10px] font-bold shrink-0"
                    :class="getAvatarColor(s.mahasiswa?.nama)"
                  >
                    {{ getInitials(s.mahasiswa?.nama) }}
                  </div>
                  <div>
                    <p class="text-sm font-medium text-text-main">
                      {{ s.mahasiswa?.nama }}
                    </p>
                    <p class="text-xs text-text-secondary font-mono">
                      {{ s.mahasiswa?.nim }}
                    </p>
                  </div>
                </button>
              </div>
              <div
                v-if="
                  showMahasiswaDropdown &&
                  mahasiswaSearch.length >= 2 &&
                  filteredSkripsi.length === 0 &&
                  !loadingSkripsi
                "
                class="absolute z-10 mt-1 w-full bg-white border border-border-light rounded-lg shadow-lg p-4 text-center text-sm text-text-secondary"
              >
                Tidak ditemukan mahasiswa
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-text-main mb-1"
                >Tanggal</label
              >
              <input
                v-model="scheduleForm.tanggal"
                type="date"
                class="w-full px-3 py-2 border border-border-light rounded-lg text-sm"
                required
              />
            </div>
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-sm font-medium text-text-main mb-1"
                  >Waktu</label
                >
                <input
                  v-model="scheduleForm.waktu"
                  type="time"
                  class="w-full px-3 py-2 border border-border-light rounded-lg text-sm"
                  required
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-text-main mb-1"
                  >Ruangan</label
                >
                <input
                  v-model="scheduleForm.ruangan"
                  type="text"
                  placeholder="Ruang Sidang A"
                  class="w-full px-3 py-2 border border-border-light rounded-lg text-sm"
                  required
                />
              </div>
            </div>
            <div class="flex gap-3 pt-4">
              <button
                type="button"
                @click="showScheduleModal = false"
                class="flex-1 px-4 py-2.5 border border-border-light rounded-lg text-text-secondary hover:bg-background-light transition-colors text-sm font-medium"
              >
                Batal
              </button>
              <button
                type="submit"
                :disabled="savingSchedule"
                class="flex-1 px-4 py-2.5 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors disabled:opacity-50 text-sm font-bold"
              >
                {{ savingSchedule ? "Menyimpan..." : "Simpan Jadwal" }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Transition>

    <!-- ========== EDIT UJIAN MODAL (Jadwal + Penguji + Nilai) ========== -->
    <Transition name="modal-fade">
      <div
        v-if="showEditModal"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
        @click.self="showEditModal = false"
      >
        <div
          class="bg-white dark:bg-surface-light rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto"
        >
          <div
            class="p-6 border-b border-border-light flex items-center justify-between sticky top-0 bg-white z-10"
          >
            <div>
              <h2 class="text-xl font-bold text-text-main">Edit Sidang</h2>
              <p class="text-sm text-text-secondary mt-0.5">
                {{ editingUjian?.skripsi?.mahasiswa?.nama }}
                ({{ editingUjian?.skripsi?.mahasiswa?.nim }})
              </p>
            </div>
            <button
              @click="showEditModal = false"
              class="text-text-secondary hover:text-text-main"
            >
              <span class="material-symbols-outlined">close</span>
            </button>
          </div>
          <form @submit.prevent="saveEdit" class="p-6 space-y-6">
            <!-- Tabs -->
            <div class="flex border-b border-border-light">
              <button
                type="button"
                @click="editTab = 'jadwal'"
                class="px-4 py-2.5 text-sm font-medium border-b-2 transition-colors"
                :class="
                  editTab === 'jadwal'
                    ? 'border-primary text-primary'
                    : 'border-transparent text-text-secondary hover:text-text-main'
                "
              >
                Jadwal &amp; Ruang
              </button>
              <button
                type="button"
                @click="editTab = 'penguji'"
                class="px-4 py-2.5 text-sm font-medium border-b-2 transition-colors"
                :class="
                  editTab === 'penguji'
                    ? 'border-primary text-primary'
                    : 'border-transparent text-text-secondary hover:text-text-main'
                "
              >
                Dosen Penguji
              </button>
              <button
                type="button"
                @click="editTab = 'nilai'"
                class="px-4 py-2.5 text-sm font-medium border-b-2 transition-colors"
                :class="
                  editTab === 'nilai'
                    ? 'border-primary text-primary'
                    : 'border-transparent text-text-secondary hover:text-text-main'
                "
              >
                Nilai &amp; Hasil
              </button>
            </div>

            <!-- TAB: Jadwal -->
            <div v-show="editTab === 'jadwal'" class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-text-main mb-1"
                  >Tanggal</label
                >
                <input
                  v-model="editForm.tanggal"
                  type="date"
                  class="w-full px-3 py-2 border border-border-light rounded-lg text-sm"
                />
              </div>
              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="block text-sm font-medium text-text-main mb-1"
                    >Waktu</label
                  >
                  <input
                    v-model="editForm.waktu"
                    type="time"
                    class="w-full px-3 py-2 border border-border-light rounded-lg text-sm"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-text-main mb-1"
                    >Ruangan</label
                  >
                  <input
                    v-model="editForm.ruangan"
                    type="text"
                    class="w-full px-3 py-2 border border-border-light rounded-lg text-sm"
                  />
                </div>
              </div>
              <div>
                <label class="block text-sm font-medium text-text-main mb-1"
                  >Status</label
                >
                <select
                  v-model="editForm.status"
                  class="w-full px-3 py-2 border border-border-light rounded-lg text-sm"
                >
                  <option value="pending">Menunggu</option>
                  <option value="terjadwal">Terjadwal</option>
                  <option value="selesai">Selesai</option>
                  <option value="batal">Batal</option>
                </select>
              </div>
            </div>

            <!-- TAB: Penguji -->
            <div v-show="editTab === 'penguji'" class="space-y-4">
              <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-text-main">
                  Dosen Penguji (max 3)
                </p>
                <button
                  type="button"
                  v-if="editForm.penguji.length < 3"
                  @click="addPengujiSlot"
                  class="text-xs px-3 py-1.5 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors font-medium flex items-center gap-1"
                >
                  <span class="material-symbols-outlined text-[14px]">add</span>
                  Tambah
                </button>
              </div>

              <!-- Existing pembimbing notice -->
              <div
                class="px-3 py-2 bg-yellow-50 border border-yellow-200 rounded-lg text-xs text-yellow-700 flex items-center gap-2"
              >
                <span class="material-symbols-outlined text-[16px]">info</span>
                Dosen pembimbing tidak bisa menjadi penguji.
              </div>

              <div
                v-for="(pSlot, idx) in editForm.penguji"
                :key="idx"
                class="border border-border-light rounded-lg p-4 space-y-3"
              >
                <div class="flex items-center justify-between">
                  <span class="text-sm font-bold text-text-main"
                    >Penguji {{ idx + 1 }}</span
                  >
                  <button
                    type="button"
                    @click="removePengujiSlot(idx)"
                    class="text-red-400 hover:text-red-600 transition-colors"
                  >
                    <span class="material-symbols-outlined text-[18px]"
                      >delete</span
                    >
                  </button>
                </div>
                <!-- Dosen search -->
                <div class="relative">
                  <div class="relative">
                    <span
                      class="material-symbols-outlined absolute left-3 top-2.5 text-[16px] text-text-secondary"
                      >search</span
                    >
                    <input
                      v-model="pSlot.searchQuery"
                      @input="searchDosenPenguji(idx)"
                      @focus="pSlot.showDropdown = true"
                      type="text"
                      class="w-full pl-9 pr-3 py-2 border border-border-light rounded-lg text-sm"
                      placeholder="Cari dosen penguji..."
                      autocomplete="off"
                    />
                  </div>
                  <!-- Selected dosen -->
                  <div
                    v-if="pSlot.dosen_id"
                    class="flex items-center gap-2 mt-2 px-3 py-2 bg-blue-50 border border-blue-200 rounded-lg text-sm"
                  >
                    <span
                      class="material-symbols-outlined text-primary text-[16px]"
                      >check_circle</span
                    >
                    <span class="text-primary font-medium">{{
                      pSlot.dosenName
                    }}</span>
                    <button
                      type="button"
                      @click="clearPengujiSelection(idx)"
                      class="ml-auto text-text-secondary hover:text-red-500"
                    >
                      <span class="material-symbols-outlined text-[14px]"
                        >close</span
                      >
                    </button>
                  </div>
                  <!-- Dropdown -->
                  <div
                    v-if="pSlot.showDropdown && pSlot.options.length > 0"
                    class="absolute z-10 mt-1 w-full bg-white border border-border-light rounded-lg shadow-lg max-h-40 overflow-y-auto"
                  >
                    <button
                      v-for="d in pSlot.options"
                      :key="d.id"
                      type="button"
                      @click="selectPengujiDosen(idx, d)"
                      class="w-full text-left px-4 py-2.5 hover:bg-blue-50 transition-colors text-sm border-b border-border-light last:border-0"
                    >
                      <p class="font-medium text-text-main">
                        {{ d.full_name || d.nama }}
                      </p>
                      <p class="text-xs text-text-secondary font-mono">
                        {{ d.nip }}
                      </p>
                    </button>
                  </div>
                  <div
                    v-if="
                      pSlot.showDropdown &&
                      pSlot.searchQuery.length >= 2 &&
                      pSlot.options.length === 0 &&
                      !pSlot.loading
                    "
                    class="absolute z-10 mt-1 w-full bg-white border border-border-light rounded-lg shadow-lg p-3 text-center text-xs text-text-secondary"
                  >
                    Tidak ditemukan
                  </div>
                </div>
                <!-- Peran -->
                <div>
                  <label
                    class="block text-xs font-medium text-text-secondary mb-1"
                    >Peran</label
                  >
                  <select
                    v-model="pSlot.peran"
                    class="w-full px-3 py-2 border border-border-light rounded-lg text-sm"
                  >
                    <option value="ketua">Ketua</option>
                    <option value="penguji_1">Penguji 1</option>
                    <option value="penguji_2">Penguji 2</option>
                  </select>
                </div>
              </div>

              <div
                v-if="editForm.penguji.length === 0"
                class="text-center py-6 text-sm text-text-secondary"
              >
                Belum ada penguji. Klik "Tambah" untuk menambahkan.
              </div>
            </div>

            <!-- TAB: Nilai -->
            <div v-show="editTab === 'nilai'" class="space-y-4">
              <!-- No penguji notice -->
              <div
                v-if="editForm.penguji.length === 0"
                class="text-center py-8 text-sm text-text-secondary"
              >
                <span
                  class="material-symbols-outlined text-3xl text-text-secondary/30 mb-2 block"
                  >person_off</span
                >
                Tambahkan dosen penguji terlebih dahulu di tab "Dosen Penguji".
              </div>

              <!-- Per-penguji score inputs -->
              <div v-else>
                <p class="text-sm font-medium text-text-main mb-3">
                  Nilai dari masing-masing Penguji
                </p>
                <div
                  v-for="(pSlot, idx) in editForm.penguji"
                  :key="idx"
                  class="border border-border-light rounded-lg p-4 mb-3"
                >
                  <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                      <div
                        class="bg-orange-100 size-7 rounded-full flex items-center justify-center text-orange-600 border border-orange-200 shrink-0"
                      >
                        <span class="material-symbols-outlined text-[14px]"
                          >school</span
                        >
                      </div>
                      <div>
                        <p class="text-sm font-bold text-text-main">
                          {{ pSlot.dosenName || "Belum dipilih" }}
                        </p>
                        <p class="text-[10px] text-text-secondary">
                          {{ getPeranLabel(pSlot.peran) }}
                        </p>
                      </div>
                    </div>
                    <!-- Per-penguji average badge -->
                    <div v-if="getPengujiAvg(idx) !== null" class="text-right">
                      <p class="text-[10px] text-text-secondary">Rata-rata</p>
                      <span class="text-lg font-black text-text-main">
                        {{ getPengujiAvg(idx) }}
                      </span>
                    </div>
                  </div>
                  <!-- 4 Criteria Grid -->
                  <div class="grid grid-cols-2 gap-3">
                    <div>
                      <label
                        class="block text-xs font-medium text-text-secondary mb-1"
                        >Metodologi & Teknik (MT)</label
                      >
                      <input
                        v-model.number="pSlot.nilai_mt"
                        type="number"
                        min="0"
                        max="100"
                        step="0.01"
                        class="w-full px-3 py-2 border border-border-light rounded-lg text-sm font-bold text-center"
                        placeholder="-"
                      />
                    </div>
                    <div>
                      <label
                        class="block text-xs font-medium text-text-secondary mb-1"
                        >Materi Skripsi (MS)</label
                      >
                      <input
                        v-model.number="pSlot.nilai_ms"
                        type="number"
                        min="0"
                        max="100"
                        step="0.01"
                        class="w-full px-3 py-2 border border-border-light rounded-lg text-sm font-bold text-center"
                        placeholder="-"
                      />
                    </div>
                    <div>
                      <label
                        class="block text-xs font-medium text-text-secondary mb-1"
                        >Penampilan Mahasiswa (PM)</label
                      >
                      <input
                        v-model.number="pSlot.nilai_pm"
                        type="number"
                        min="0"
                        max="100"
                        step="0.01"
                        class="w-full px-3 py-2 border border-border-light rounded-lg text-sm font-bold text-center"
                        placeholder="-"
                      />
                    </div>
                    <div>
                      <label
                        class="block text-xs font-medium text-text-secondary mb-1"
                        >Penguasaan Isi (PI)</label
                      >
                      <input
                        v-model.number="pSlot.nilai_pi"
                        type="number"
                        min="0"
                        max="100"
                        step="0.01"
                        class="w-full px-3 py-2 border border-border-light rounded-lg text-sm font-bold text-center"
                        placeholder="-"
                      />
                    </div>
                  </div>
                  <!-- Catatan -->
                  <div class="mt-3">
                    <label
                      class="block text-xs font-medium text-text-secondary mb-1"
                      >Catatan</label
                    >
                    <input
                      v-model="pSlot.catatan"
                      type="text"
                      class="w-full px-3 py-2 border border-border-light rounded-lg text-sm"
                      placeholder="Catatan penguji (opsional)"
                    />
                  </div>
                </div>

                <!-- Average + Grade Summary -->
                <div
                  class="bg-gradient-to-r from-slate-50 to-indigo-50 rounded-xl p-4 border border-indigo-100 mt-4"
                >
                  <div class="flex items-center justify-between">
                    <div>
                      <p class="text-xs text-text-secondary font-medium mb-1">
                        Nilai Rata-rata Akhir
                      </p>
                      <p class="text-3xl font-black text-text-main">
                        {{ computedAverage !== null ? computedAverage : "-" }}
                      </p>
                    </div>
                    <div class="text-center">
                      <p class="text-xs text-text-secondary font-medium mb-1">
                        Grade
                      </p>
                      <span
                        v-if="computedGrade"
                        class="inline-flex items-center justify-center size-12 rounded-xl text-lg font-black"
                        :class="getGradeClass(computedGrade)"
                        >{{ computedGrade }}</span
                      >
                      <span
                        v-else
                        class="text-2xl text-text-secondary/30 font-bold"
                        >-</span
                      >
                    </div>
                    <div class="text-right">
                      <p class="text-xs text-text-secondary font-medium mb-1">
                        Terisi
                      </p>
                      <p class="text-lg font-bold text-text-main">
                        {{ scoredPengujiCount }}/{{ editForm.penguji.length }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Hasil override -->
              <div
                v-if="editForm.penguji.length > 0"
                class="pt-3 border-t border-border-light"
              >
                <label class="block text-sm font-medium text-text-main mb-1"
                  >Hasil Akhir</label
                >
                <select
                  v-model="editForm.hasil"
                  class="w-full px-3 py-2 border border-border-light rounded-lg text-sm"
                >
                  <option value="">-- Otomatis dari Nilai --</option>
                  <option value="lulus">Lulus</option>
                  <option value="lulus_revisi">Lulus dengan Revisi</option>
                  <option value="tidak_lulus">Tidak Lulus</option>
                </select>
                <p class="text-[10px] text-text-secondary mt-1">
                  Kosongkan untuk hasil otomatis. Jika semua penguji sudah
                  memberi nilai, sistem akan menentukan hasil berdasarkan
                  rata-rata.
                </p>
              </div>

              <div>
                <label class="block text-sm font-medium text-text-main mb-1"
                  >Catatan Umum</label
                >
                <textarea
                  v-model="editForm.catatan"
                  rows="3"
                  class="w-full px-3 py-2 border border-border-light rounded-lg text-sm"
                  placeholder="Catatan tambahan (opsional)"
                ></textarea>
              </div>
            </div>

            <!-- Common Buttons -->
            <div class="flex gap-3 pt-4 border-t border-border-light">
              <button
                type="button"
                @click="showEditModal = false"
                class="flex-1 px-4 py-2.5 border border-border-light rounded-lg text-text-secondary hover:bg-background-light transition-colors text-sm font-medium"
              >
                Batal
              </button>
              <button
                type="submit"
                :disabled="saving"
                class="flex-1 px-4 py-2.5 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors disabled:opacity-50 text-sm font-bold"
              >
                {{ saving ? "Menyimpan..." : "Simpan Perubahan" }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Transition>

    <!-- ========== DETAIL MODAL ========== -->
    <Transition name="modal-fade">
      <div
        v-if="showDetailModal"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
        @click.self="showDetailModal = false"
      >
        <div
          class="bg-white dark:bg-surface-light rounded-xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto"
        >
          <div
            class="p-6 border-b border-border-light flex justify-between items-center sticky top-0 bg-white z-10"
          >
            <h2 class="text-xl font-bold text-text-main">Detail Sidang</h2>
            <button
              @click="showDetailModal = false"
              class="text-text-secondary hover:text-text-main"
            >
              <span class="material-symbols-outlined">close</span>
            </button>
          </div>
          <div class="p-6 space-y-4" v-if="detailUjian">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <p class="text-xs text-text-secondary font-medium mb-1">
                  Mahasiswa
                </p>
                <p class="text-sm font-bold text-text-main">
                  {{ detailUjian.skripsi?.mahasiswa?.nama }}
                </p>
                <p class="text-xs text-text-secondary font-mono">
                  {{ detailUjian.skripsi?.mahasiswa?.nim }}
                </p>
              </div>
              <div>
                <p class="text-xs text-text-secondary font-medium mb-1">
                  Status
                </p>
                <span
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium"
                  :class="getStatusClass(detailUjian.status)"
                >
                  {{ getStatusLabel(detailUjian.status) }}
                </span>
              </div>
            </div>
            <div>
              <p class="text-xs text-text-secondary font-medium mb-1">
                Judul Skripsi
              </p>
              <p class="text-sm text-text-main">
                {{ detailUjian.skripsi?.judul }}
              </p>
            </div>
            <div class="grid grid-cols-3 gap-4">
              <div>
                <p class="text-xs text-text-secondary font-medium mb-1">
                  Tanggal
                </p>
                <p class="text-sm font-medium text-text-main">
                  {{ formatDate(detailUjian.tanggal) }}
                </p>
              </div>
              <div>
                <p class="text-xs text-text-secondary font-medium mb-1">
                  Waktu
                </p>
                <p class="text-sm font-medium text-text-main">
                  {{ formatTime(detailUjian.waktu) }}
                </p>
              </div>
              <div>
                <p class="text-xs text-text-secondary font-medium mb-1">
                  Ruangan
                </p>
                <p class="text-sm font-medium text-text-main">
                  {{ detailUjian.ruangan || "-" }}
                </p>
              </div>
            </div>
            <!-- Pembimbing -->
            <div v-if="detailUjian.skripsi?.pembimbing?.length">
              <p class="text-xs text-text-secondary font-medium mb-2">
                Pembimbing
              </p>
              <div class="flex flex-col gap-2">
                <div
                  v-for="p in detailUjian.skripsi.pembimbing"
                  :key="p.id"
                  class="flex items-center gap-2 text-sm"
                >
                  <div
                    class="bg-blue-100 size-7 rounded-full flex items-center justify-center text-primary border border-blue-200"
                  >
                    <span class="material-symbols-outlined text-[14px]"
                      >person</span
                    >
                  </div>
                  <span class="font-medium text-text-main">{{
                    p.dosen?.full_name || "-"
                  }}</span>
                  <span
                    class="text-[10px] text-text-secondary bg-gray-100 px-1.5 py-0.5 rounded"
                    >{{
                      p.jenis === "pembimbing_1"
                        ? "Pembimbing 1"
                        : "Pembimbing 2"
                    }}</span
                  >
                </div>
              </div>
            </div>
            <!-- Penguji -->
            <div v-if="detailUjian.penguji?.length">
              <p class="text-xs text-text-secondary font-medium mb-2">
                Tim Penguji
              </p>
              <div class="flex flex-col gap-2">
                <div
                  v-for="p in detailUjian.penguji"
                  :key="p.id"
                  class="flex items-center gap-2 text-sm"
                >
                  <div
                    class="bg-orange-100 size-7 rounded-full flex items-center justify-center text-orange-600 border border-orange-200"
                  >
                    <span class="material-symbols-outlined text-[14px]"
                      >school</span
                    >
                  </div>
                  <span class="font-medium text-text-main">{{
                    p.dosen?.full_name || "-"
                  }}</span>
                  <span
                    class="text-[10px] text-text-secondary bg-gray-100 px-1.5 py-0.5 rounded"
                    >{{ getPeranLabel(p.peran) }}</span
                  >
                </div>
              </div>
            </div>
            <!-- Nilai Per Penguji -->
            <div
              v-if="
                detailUjian.penguji?.length &&
                detailUjian.penguji.some((p) => p.nilai !== null)
              "
              class="bg-gray-50 rounded-lg p-4 space-y-3"
            >
              <p
                class="text-xs text-text-secondary font-bold uppercase tracking-wider"
              >
                Nilai Penguji
              </p>
              <div class="space-y-3">
                <div
                  v-for="p in detailUjian.penguji"
                  :key="p.id"
                  class="bg-white rounded-lg px-3 py-3 border border-border-light"
                >
                  <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                      <div
                        class="bg-orange-100 size-6 rounded-full flex items-center justify-center text-orange-600 text-[11px]"
                      >
                        <span class="material-symbols-outlined text-[12px]"
                          >school</span
                        >
                      </div>
                      <span class="text-sm font-medium text-text-main">{{
                        p.dosen?.full_name || "-"
                      }}</span>
                      <span
                        class="text-[9px] px-1.5 py-0.5 rounded bg-gray-100 text-text-secondary"
                        >{{ getPeranLabel(p.peran) }}</span
                      >
                    </div>
                    <span class="text-sm font-black text-primary">{{
                      p.nilai !== null ? p.nilai : "-"
                    }}</span>
                  </div>
                  <div class="grid grid-cols-4 gap-2 text-center">
                    <div class="bg-gray-50 rounded px-2 py-1">
                      <p class="text-[9px] text-text-secondary font-medium">
                        MT
                      </p>
                      <p class="text-xs font-bold text-text-main">
                        {{ p.nilai_mt ?? "-" }}
                      </p>
                    </div>
                    <div class="bg-gray-50 rounded px-2 py-1">
                      <p class="text-[9px] text-text-secondary font-medium">
                        MS
                      </p>
                      <p class="text-xs font-bold text-text-main">
                        {{ p.nilai_ms ?? "-" }}
                      </p>
                    </div>
                    <div class="bg-gray-50 rounded px-2 py-1">
                      <p class="text-[9px] text-text-secondary font-medium">
                        PM
                      </p>
                      <p class="text-xs font-bold text-text-main">
                        {{ p.nilai_pm ?? "-" }}
                      </p>
                    </div>
                    <div class="bg-gray-50 rounded px-2 py-1">
                      <p class="text-[9px] text-text-secondary font-medium">
                        PI
                      </p>
                      <p class="text-xs font-bold text-text-main">
                        {{ p.nilai_pi ?? "-" }}
                      </p>
                    </div>
                  </div>
                  <p
                    v-if="p.catatan"
                    class="text-xs text-text-secondary mt-2 italic"
                  >
                    {{ p.catatan }}
                  </p>
                </div>
              </div>
            </div>
            <!-- Hasil Akhir -->
            <div
              v-if="detailUjian.nilai || detailUjian.hasil"
              class="bg-gradient-to-r from-slate-50 to-indigo-50 rounded-lg p-4 border border-indigo-100"
            >
              <p
                class="text-xs text-text-secondary font-bold uppercase tracking-wider mb-3"
              >
                Hasil Ujian Akhir
              </p>
              <div class="grid grid-cols-3 gap-4">
                <div>
                  <p class="text-xs text-text-secondary mb-1">Rata-rata</p>
                  <p class="text-2xl font-black text-text-main">
                    {{ detailUjian.nilai || "-" }}
                  </p>
                </div>
                <div class="text-center">
                  <p class="text-xs text-text-secondary mb-1">Grade</p>
                  <span
                    v-if="detailUjian.grade"
                    class="inline-flex items-center justify-center size-10 rounded-lg text-base font-black"
                    :class="getGradeClass(detailUjian.grade)"
                    >{{ detailUjian.grade }}</span
                  >
                  <span v-else class="text-lg text-text-secondary/30">-</span>
                </div>
                <div class="text-right">
                  <p class="text-xs text-text-secondary mb-1">Keputusan</p>
                  <span
                    v-if="detailUjian.hasil"
                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold"
                    :class="getHasilClass(detailUjian.hasil)"
                  >
                    {{ getHasilLabel(detailUjian.hasil) }}
                  </span>
                  <span v-else class="text-sm text-text-secondary">-</span>
                </div>
              </div>
              <div
                v-if="detailUjian.catatan"
                class="mt-3 pt-3 border-t border-border-light"
              >
                <p class="text-xs text-text-secondary mb-1">Catatan</p>
                <p class="text-sm text-text-main">
                  {{ detailUjian.catatan }}
                </p>
              </div>
            </div>
            <!-- SK Penguji button in detail -->
            <div
              v-if="detailUjian.penguji?.length"
              class="pt-3 border-t border-border-light"
            >
              <button
                @click="downloadSkPenguji(detailUjian)"
                class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm font-bold"
              >
                <span class="material-symbols-outlined text-[18px]"
                  >picture_as_pdf</span
                >
                Cetak SK Penguji
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive, computed } from "vue";
import adminService from "../../../services/adminService";

const loading = ref(true);
const saving = ref(false);
const savingSchedule = ref(false);
const ujianList = ref([]);
const searchQuery = ref("");
const filterStatus = ref("");
const filterProdi = ref("");
const filterTahunAkademik = ref("");
const filterSemester = ref("");
const exporting = ref(false);
const prodiList = ref([]);
const showScheduleModal = ref(false);
const showEditModal = ref(false);
const showDetailModal = ref(false);
const editingUjian = ref(null);
const detailUjian = ref(null);
const editTab = ref("jadwal");

// Generate tahun akademik options (last 5 years)
const tahunAkademikOptions = (() => {
  const currentYear = new Date().getFullYear();
  const opts = [];
  for (let y = currentYear + 1; y >= currentYear - 4; y--) {
    opts.push(`${y - 1}/${y}`);
  }
  return opts;
})();

// Mahasiswa search (schedule modal)
const mahasiswaSearch = ref("");
const showMahasiswaDropdown = ref(false);
const loadingSkripsi = ref(false);
const filteredSkripsi = ref([]);
const selectedMahasiswaName = ref("");

const pagination = reactive({
  current_page: 1,
  last_page: 1,
  total: 0,
  from: 0,
  to: 0,
});

const stats = reactive({
  terjadwal: 0,
  sedang_ujian: 0,
  selesai: 0,
  total: 0,
});

const scheduleForm = reactive({
  skripsi_id: "",
  tanggal: "",
  waktu: "",
  ruangan: "",
});

const editForm = reactive({
  tanggal: "",
  waktu: "",
  ruangan: "",
  status: "",
  hasil: "",
  catatan: "",
  penguji: [],
});

// ---- COMPUTED: Average + Grade ----
const getPengujiAvg = (idx) => {
  const p = editForm.penguji[idx];
  if (!p) return null;
  const vals = [p.nilai_mt, p.nilai_ms, p.nilai_pm, p.nilai_pi];
  const filled = vals.filter((v) => v !== null && v !== "" && !isNaN(v));
  if (filled.length !== 4) return null;
  const avg = filled.reduce((a, b) => a + Number(b), 0) / 4;
  return Math.round(avg * 100) / 100;
};

const scoredPengujiCount = computed(() => {
  return editForm.penguji.filter(
    (p) => getPengujiAvg(editForm.penguji.indexOf(p)) !== null,
  ).length;
});

const computedAverage = computed(() => {
  const avgs = editForm.penguji
    .map((_, idx) => getPengujiAvg(idx))
    .filter((v) => v !== null);
  if (avgs.length === 0) return null;
  const avg = avgs.reduce((a, b) => a + b, 0) / avgs.length;
  return Math.round(avg * 100) / 100;
});

const computedGrade = computed(() => {
  if (computedAverage.value === null) return null;
  return getGrade(computedAverage.value);
});

let searchTimeout = null;
let mahasiswaSearchTimeout = null;

// ---- DATA FETCH ----
const fetchUjian = async () => {
  try {
    loading.value = true;
    const params = {
      page: pagination.current_page,
      search: searchQuery.value,
    };
    if (filterStatus.value) params.status = filterStatus.value;
    if (filterProdi.value) params.prodi_id = filterProdi.value;
    if (filterTahunAkademik.value)
      params.tahun_akademik = filterTahunAkademik.value;
    if (filterSemester.value) params.semester = filterSemester.value;
    const response = await adminService.getUjian(params);
    if (response.success) {
      ujianList.value = response.data.data || response.data;
      if (response.data.current_page) {
        Object.assign(pagination, {
          current_page: response.data.current_page,
          last_page: response.data.last_page,
          total: response.data.total,
          from: response.data.from,
          to: response.data.to,
        });
      }
      if (response.stats) {
        Object.assign(stats, response.stats);
      }
    }
  } catch (error) {
    console.error("Failed to fetch ujian:", error);
  } finally {
    loading.value = false;
  }
};

const fetchProdi = async () => {
  try {
    const response = await adminService.getProdi();
    if (response.success) {
      prodiList.value = response.data;
    }
  } catch (error) {
    console.error("Failed to fetch prodi:", error);
  }
};

const exportJadwalPdf = async () => {
  try {
    exporting.value = true;
    const params = {};
    if (filterProdi.value) params.prodi_id = filterProdi.value;
    if (filterTahunAkademik.value)
      params.tahun_akademik = filterTahunAkademik.value;
    if (filterSemester.value) params.semester = filterSemester.value;
    if (filterStatus.value) params.status = filterStatus.value;

    const response = await adminService.getJadwalUjianPdf(params);
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement("a");
    link.href = url;
    link.setAttribute("download", "Jadwal_Ujian_Skripsi.pdf");
    document.body.appendChild(link);
    link.click();
    link.remove();
    window.URL.revokeObjectURL(url);
  } catch (error) {
    console.error("Failed to export jadwal:", error);
    alert("Gagal mengexport jadwal ujian");
  } finally {
    exporting.value = false;
  }
};

const debouncedSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    pagination.current_page = 1;
    fetchUjian();
  }, 300);
};

const onFilterChange = () => {
  pagination.current_page = 1;
  fetchUjian();
};

const goToPage = (page) => {
  if (page >= 1 && page <= pagination.last_page) {
    pagination.current_page = page;
    fetchUjian();
  }
};

// ---- SCHEDULE MODAL (CREATE) ----
const fetchSkripsiBySearch = async (query) => {
  try {
    loadingSkripsi.value = true;
    const response = await adminService.getSkripsi({
      search: query,
      per_page: 20,
    });
    if (response.success) {
      filteredSkripsi.value = (response.data.data || response.data).filter(
        (s) => s.mahasiswa,
      );
    }
  } catch (error) {
    console.error("Failed to fetch skripsi:", error);
  } finally {
    loadingSkripsi.value = false;
  }
};

const onMahasiswaSearch = () => {
  clearTimeout(mahasiswaSearchTimeout);
  if (mahasiswaSearch.value.length < 2) {
    filteredSkripsi.value = [];
    return;
  }
  showMahasiswaDropdown.value = true;
  mahasiswaSearchTimeout = setTimeout(() => {
    fetchSkripsiBySearch(mahasiswaSearch.value);
  }, 300);
};

const selectMahasiswa = (s) => {
  scheduleForm.skripsi_id = s.id;
  selectedMahasiswaName.value = `${s.mahasiswa?.nama} (${s.mahasiswa?.nim})`;
  mahasiswaSearch.value = "";
  showMahasiswaDropdown.value = false;
  filteredSkripsi.value = [];
};

const clearMahasiswaSelection = () => {
  scheduleForm.skripsi_id = "";
  selectedMahasiswaName.value = "";
  mahasiswaSearch.value = "";
};

const openScheduleModal = () => {
  scheduleForm.skripsi_id = "";
  scheduleForm.tanggal = "";
  scheduleForm.waktu = "";
  scheduleForm.ruangan = "";
  mahasiswaSearch.value = "";
  selectedMahasiswaName.value = "";
  filteredSkripsi.value = [];
  showMahasiswaDropdown.value = false;
  showScheduleModal.value = true;
};

const saveSchedule = async () => {
  try {
    savingSchedule.value = true;
    await adminService.createUjian({
      skripsi_id: scheduleForm.skripsi_id,
      tanggal: scheduleForm.tanggal,
      waktu: scheduleForm.waktu,
      ruangan: scheduleForm.ruangan,
    });
    showScheduleModal.value = false;
    fetchUjian();
  } catch (error) {
    console.error("Failed to save schedule:", error);
    alert(
      "Gagal menyimpan jadwal: " +
        (error.response?.data?.message || error.message),
    );
  } finally {
    savingSchedule.value = false;
  }
};

// ---- EDIT MODAL ----
const openEditModal = (item) => {
  editingUjian.value = item;
  editTab.value = "jadwal";
  editForm.tanggal = item.tanggal
    ? new Date(item.tanggal).toISOString().split("T")[0]
    : "";
  editForm.waktu = item.waktu ? item.waktu.substring(0, 5) : "";
  editForm.ruangan = item.ruangan || "";
  editForm.status = item.status || "pending";
  editForm.hasil = item.hasil || "";
  editForm.catatan = item.catatan || "";

  // Load existing penguji with their component scores
  editForm.penguji = (item.penguji || []).map((p) => ({
    dosen_id: p.dosen_id,
    dosenName: p.dosen?.full_name || p.dosen?.nama || "",
    peran: p.peran || "anggota",
    nilai_mt:
      p.nilai_mt !== null && p.nilai_mt !== undefined
        ? Number(p.nilai_mt)
        : null,
    nilai_ms:
      p.nilai_ms !== null && p.nilai_ms !== undefined
        ? Number(p.nilai_ms)
        : null,
    nilai_pm:
      p.nilai_pm !== null && p.nilai_pm !== undefined
        ? Number(p.nilai_pm)
        : null,
    nilai_pi:
      p.nilai_pi !== null && p.nilai_pi !== undefined
        ? Number(p.nilai_pi)
        : null,
    catatan: p.catatan || "",
    searchQuery: "",
    showDropdown: false,
    options: [],
    loading: false,
  }));

  showEditModal.value = true;
};

const addPengujiSlot = () => {
  if (editForm.penguji.length < 3) {
    editForm.penguji.push({
      dosen_id: null,
      dosenName: "",
      peran: "anggota",
      nilai_mt: null,
      nilai_ms: null,
      nilai_pm: null,
      nilai_pi: null,
      catatan: "",
      searchQuery: "",
      showDropdown: false,
      options: [],
      loading: false,
    });
  }
};

const removePengujiSlot = (idx) => {
  editForm.penguji.splice(idx, 1);
};

const searchDosenPenguji = (idx) => {
  const slot = editForm.penguji[idx];
  clearTimeout(slot._timeout);
  if (slot.searchQuery.length < 2) {
    slot.options = [];
    return;
  }
  slot.showDropdown = true;
  slot.loading = true;
  slot._timeout = setTimeout(async () => {
    try {
      const response = await adminService.getAvailablePenguji(
        editingUjian.value.id,
        { search: slot.searchQuery },
      );
      if (response.success) {
        // Additionally filter out already-selected dosen
        const selectedIds = editForm.penguji
          .map((p) => p.dosen_id)
          .filter(Boolean);
        slot.options = response.data.filter(
          (d) => !selectedIds.includes(d.id) || d.id === slot.dosen_id,
        );
      }
    } catch (error) {
      console.error("Failed to search dosen:", error);
    } finally {
      slot.loading = false;
    }
  }, 300);
};

const selectPengujiDosen = (idx, dosen) => {
  const slot = editForm.penguji[idx];
  slot.dosen_id = dosen.id;
  slot.dosenName = dosen.full_name || dosen.nama;
  slot.searchQuery = "";
  slot.showDropdown = false;
  slot.options = [];
};

const clearPengujiSelection = (idx) => {
  const slot = editForm.penguji[idx];
  slot.dosen_id = null;
  slot.dosenName = "";
  slot.searchQuery = "";
};

const saveEdit = async () => {
  try {
    saving.value = true;

    const payload = {
      tanggal: editForm.tanggal,
      waktu: editForm.waktu,
      ruangan: editForm.ruangan,
      status: editForm.status,
      catatan: editForm.catatan,
    };

    if (editForm.hasil) {
      payload.hasil = editForm.hasil;
    }

    // Include penguji with their component scores
    payload.penguji = editForm.penguji
      .filter((p) => p.dosen_id)
      .map((p) => ({
        dosen_id: p.dosen_id,
        peran: p.peran,
        nilai_mt: p.nilai_mt !== null && p.nilai_mt !== "" ? p.nilai_mt : null,
        nilai_ms: p.nilai_ms !== null && p.nilai_ms !== "" ? p.nilai_ms : null,
        nilai_pm: p.nilai_pm !== null && p.nilai_pm !== "" ? p.nilai_pm : null,
        nilai_pi: p.nilai_pi !== null && p.nilai_pi !== "" ? p.nilai_pi : null,
        catatan: p.catatan || null,
      }));

    await adminService.updateUjian(editingUjian.value.id, payload);
    showEditModal.value = false;
    fetchUjian();
  } catch (error) {
    console.error("Failed to save edit:", error);
    alert(
      "Gagal menyimpan: " + (error.response?.data?.message || error.message),
    );
  } finally {
    saving.value = false;
  }
};

// ---- DETAIL ----
const viewDetail = async (item) => {
  try {
    const response = await adminService.getUjianDetail(item.id);
    if (response.success) {
      detailUjian.value = response.data;
      showDetailModal.value = true;
    }
  } catch (error) {
    console.error("Failed to fetch detail:", error);
    alert("Gagal memuat detail ujian");
  }
};

// ---- SK PENGUJI PDF ----
const downloadSkPenguji = async (item) => {
  try {
    const response = await adminService.getSkPengujiPdf(item.id);
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement("a");
    link.href = url;
    link.setAttribute(
      "download",
      `SK_Penguji_${item.skripsi?.mahasiswa?.nim || item.id}.pdf`,
    );
    document.body.appendChild(link);
    link.click();
    link.remove();
    window.URL.revokeObjectURL(url);
  } catch (error) {
    console.error("Failed to download SK Penguji:", error);
    alert("Gagal mencetak SK Penguji");
  }
};

// ---- HELPERS ----
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
  return colors[name.charCodeAt(0) % colors.length];
};

const formatDate = (date) => {
  if (!date) return "-";
  return new Date(date).toLocaleDateString("id-ID", {
    day: "numeric",
    month: "short",
    year: "numeric",
  });
};

const formatTime = (time) => {
  if (!time) return "-";
  if (typeof time === "string" && time.includes("T")) {
    return new Date(time).toLocaleTimeString("id-ID", {
      hour: "2-digit",
      minute: "2-digit",
    });
  }
  return time.substring(0, 5);
};

const getStatusClass = (status) => {
  const classes = {
    pending: "bg-orange-50 text-orange-600 border border-orange-100",
    terjadwal: "bg-blue-50 text-blue-600 border border-blue-100",
    berlangsung: "bg-yellow-50 text-yellow-600 border border-yellow-100",
    selesai: "bg-green-50 text-green-600 border border-green-100",
    batal: "bg-red-50 text-red-600 border border-red-100",
  };
  return classes[status] || "bg-gray-50 text-gray-600 border border-gray-100";
};

const getStatusDot = (status) => {
  const dots = {
    pending: "bg-orange-600",
    terjadwal: "bg-blue-600",
    berlangsung: "bg-yellow-600",
    selesai: "bg-green-600",
    batal: "bg-red-600",
  };
  return dots[status] || "bg-gray-600";
};

const getStatusLabel = (status) => {
  const labels = {
    pending: "Menunggu Nilai",
    terjadwal: "Terjadwal",
    berlangsung: "Berlangsung",
    selesai: "Selesai",
    batal: "Dibatalkan",
  };
  return labels[status] || status || "-";
};

const getHasilClass = (hasil) => {
  const classes = {
    lulus: "bg-green-100 text-green-700",
    lulus_revisi: "bg-yellow-100 text-yellow-700",
    tidak_lulus: "bg-red-100 text-red-700",
  };
  return classes[hasil] || "bg-gray-100 text-gray-600";
};

const getHasilLabel = (hasil) => {
  const labels = {
    lulus: "Lulus",
    lulus_revisi: "Lulus (Revisi)",
    tidak_lulus: "Tidak Lulus",
  };
  return labels[hasil] || hasil || "-";
};

const getPeranLabel = (peran) => {
  const labels = {
    ketua: "Ketua",
    sekretaris: "Sekretaris",
    anggota: "Anggota",
  };
  return labels[peran] || peran || "-";
};

const getGrade = (nilai) => {
  if (nilai >= 85) return "A";
  if (nilai >= 80) return "B+";
  if (nilai >= 70) return "B";
  if (nilai >= 65) return "C+";
  if (nilai >= 55) return "C";
  return "D";
};

const getGradeClass = (grade) => {
  const classes = {
    A: "bg-green-100 text-green-700 border border-green-200",
    "B+": "bg-emerald-100 text-emerald-700 border border-emerald-200",
    B: "bg-blue-100 text-blue-700 border border-blue-200",
    "C+": "bg-yellow-100 text-yellow-700 border border-yellow-200",
    C: "bg-orange-100 text-orange-700 border border-orange-200",
    D: "bg-red-100 text-red-700 border border-red-200",
  };
  return classes[grade] || "bg-gray-100 text-gray-600";
};

onMounted(() => {
  fetchUjian();
  fetchProdi();
});
</script>

<style scoped>
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.2s ease;
}
.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}
</style>
