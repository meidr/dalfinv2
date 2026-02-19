<template>
  <div class="max-w-7xl mx-auto flex flex-col gap-8 animate-fade-in-up">
    <!-- Header -->
    <div class="flex flex-col gap-1">
      <div class="flex items-center gap-2 text-sm text-text-secondary">
        <router-link
          to="/admin/dashboard"
          class="hover:text-primary transition-colors"
          >Dashboard</router-link
        >
        <span>/</span>
        <span class="text-text-main font-medium">Otoritas Jabatan</span>
      </div>
      <h1 class="text-text-main text-3xl font-bold leading-tight">
        Otoritas Jabatan
      </h1>
      <p class="text-text-secondary text-sm font-normal">
        Kelola periode kepemimpinan dan pejabat penandatangan dokumen
      </p>
    </div>

    <!-- Tabs -->
    <div class="flex p-1 bg-sidebar-light rounded-xl w-fit">
      <button
        v-for="tab in tabs"
        :key="tab.key"
        @click="activeTab = tab.key"
        :class="[
          'px-5 py-2.5 text-sm font-medium rounded-lg transition-all',
          activeTab === tab.key
            ? 'bg-background-light text-primary shadow-sm border border-border-light/50 dark:bg-surface'
            : 'text-text-secondary hover:text-text-main hover:bg-background-light/50',
        ]"
      >
        <span
          class="material-symbols-outlined text-[18px] mr-1.5 align-middle"
          >{{ tab.icon }}</span
        >
        {{ tab.label }}
      </button>
    </div>

    <!-- Tab: Periode Jabatan -->
    <div
      v-if="activeTab === 'periode'"
      class="flex flex-col bg-surface-light border border-border-light rounded-xl shadow-sm"
    >
      <div
        class="p-5 border-b border-border-light flex flex-col md:flex-row gap-4 items-center justify-between"
      >
        <div>
          <h3 class="text-text-main text-lg font-bold">Periode Jabatan</h3>
          <p class="text-text-secondary text-xs">
            Kelola periode kepemimpinan organisasi
          </p>
        </div>
        <button
          @click="openPeriodeModal()"
          class="flex items-center gap-2 px-4 py-2.5 bg-primary hover:bg-blue-600 text-white rounded-lg font-medium text-sm shadow-sm shadow-blue-500/20 transition-all whitespace-nowrap"
        >
          <span class="material-symbols-outlined text-[20px]">add</span>
          Tambah Periode
        </button>
      </div>

      <div v-if="loadingPeriode" class="p-12 text-center">
        <div
          class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto"
        ></div>
        <p class="text-text-secondary text-sm mt-3">Memuat data...</p>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
          <thead
            class="bg-sidebar-light/50 text-text-secondary font-medium border-b border-border-light"
          >
            <tr>
              <th class="px-6 py-4">Nama Periode</th>
              <th class="px-6 py-4">Tanggal Mulai</th>
              <th class="px-6 py-4">Tanggal Selesai</th>
              <th class="px-6 py-4">Status</th>
              <th class="px-6 py-4">Pejabat</th>
              <th class="px-6 py-4 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border-light">
            <tr v-if="periodes.length === 0">
              <td
                colspan="6"
                class="px-6 py-12 text-center text-text-secondary"
              >
                Tidak ada data ditemukan
              </td>
            </tr>
            <tr
              v-else
              v-for="item in periodes"
              :key="item.id"
              class="group hover:bg-sidebar-light/30 transition-colors"
            >
              <td class="px-6 py-4 text-text-main font-medium">
                {{ item.nama }}
              </td>
              <td class="px-6 py-4 text-text-secondary">
                {{ formatDate(item.tgl_mulai) }}
              </td>
              <td class="px-6 py-4 text-text-secondary">
                {{ formatDate(item.tgl_selesai) }}
              </td>
              <td class="px-6 py-4">
                <span
                  :class="
                    item.is_active
                      ? 'bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-400'
                      : 'bg-gray-50 text-gray-500 dark:bg-gray-800 dark:text-gray-400'
                  "
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium"
                >
                  <span
                    class="w-1.5 h-1.5 rounded-full"
                    :class="item.is_active ? 'bg-green-500' : 'bg-gray-400'"
                  ></span>
                  {{ item.is_active ? "Aktif" : "Tidak Aktif" }}
                </span>
              </td>
              <td class="px-6 py-4">
                <span
                  class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400"
                  >{{ item.pejabat_count || 0 }}</span
                >
              </td>
              <td class="px-6 py-4 text-right">
                <div
                  class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity"
                >
                  <button
                    @click="openPeriodeModal(item)"
                    class="p-2 text-text-secondary hover:text-primary hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
                    title="Edit"
                  >
                    <span class="material-symbols-outlined text-[20px]"
                      >edit</span
                    >
                  </button>
                  <button
                    @click="confirmDeletePeriode(item)"
                    class="p-2 text-text-secondary hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                    title="Hapus"
                  >
                    <span class="material-symbols-outlined text-[20px]"
                      >delete</span
                    >
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Tab: Pejabat -->
    <div v-if="activeTab === 'pejabat'" class="flex flex-col gap-4">
      <!-- Filters -->
      <div class="flex flex-col sm:flex-row gap-3">
        <select
          v-model="pejabatFilter.periode_id"
          @change="fetchPejabat"
          class="px-3 py-2.5 border border-border-light rounded-lg bg-background-light text-text-main text-sm focus:ring-1 focus:ring-primary focus:border-primary dark:bg-background"
        >
          <option value="">Semua Periode</option>
          <option v-for="p in periodes" :key="p.id" :value="p.id">
            {{ p.nama }} {{ p.is_active ? "(Aktif)" : "" }}
          </option>
        </select>
        <select
          v-model="pejabatFilter.jabatan_id"
          @change="fetchPejabat"
          class="px-3 py-2.5 border border-border-light rounded-lg bg-background-light text-text-main text-sm focus:ring-1 focus:ring-primary focus:border-primary dark:bg-background"
        >
          <option value="">Semua Jabatan</option>
          <option v-for="j in jabatanList" :key="j.id" :value="j.id">
            {{ j.nama }} ({{ j.level }})
          </option>
        </select>
        <div class="relative flex-1">
          <div
            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
          >
            <span
              class="material-symbols-outlined text-text-secondary text-[20px]"
              >search</span
            >
          </div>
          <input
            v-model="pejabatFilter.search"
            @input="debouncedSearchPejabat"
            placeholder="Cari nama dosen..."
            class="block w-full pl-10 pr-3 py-2.5 border border-border-light rounded-lg bg-background-light text-text-main placeholder-text-secondary focus:ring-1 focus:ring-primary focus:border-primary text-sm dark:bg-background"
          />
        </div>
      </div>

      <div
        class="flex flex-col bg-surface-light border border-border-light rounded-xl shadow-sm"
      >
        <div
          class="p-5 border-b border-border-light flex items-center justify-between"
        >
          <div>
            <h3 class="text-text-main text-lg font-bold">Pejabat</h3>
            <p class="text-text-secondary text-xs">
              Penugasan jabatan berdasarkan periode
            </p>
          </div>
          <button
            @click="openPejabatModal()"
            class="flex items-center gap-2 px-4 py-2.5 bg-primary hover:bg-blue-600 text-white rounded-lg font-medium text-sm shadow-sm shadow-blue-500/20 transition-all whitespace-nowrap"
          >
            <span class="material-symbols-outlined text-[20px]">add</span>
            Tambah Pejabat
          </button>
        </div>

        <div v-if="loadingPejabat" class="p-12 text-center">
          <div
            class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto"
          ></div>
          <p class="text-text-secondary text-sm mt-3">Memuat data...</p>
        </div>

        <div v-else class="overflow-x-auto">
          <table class="w-full text-left text-sm whitespace-nowrap">
            <thead
              class="bg-sidebar-light/50 text-text-secondary font-medium border-b border-border-light"
            >
              <tr>
                <th class="px-6 py-4">Dosen</th>
                <th class="px-6 py-4">Jabatan</th>
                <th class="px-6 py-4">Periode</th>
                <th class="px-6 py-4">Prodi / Fakultas</th>
                <th class="px-6 py-4">Tgl Mulai</th>
                <th class="px-6 py-4">Tgl Selesai</th>
                <th class="px-6 py-4">Status</th>
                <th class="px-6 py-4 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-border-light">
              <tr v-if="pejabatList.length === 0">
                <td
                  colspan="8"
                  class="px-6 py-12 text-center text-text-secondary"
                >
                  Tidak ada data ditemukan
                </td>
              </tr>
              <tr
                v-else
                v-for="item in pejabatList"
                :key="item.id"
                class="group hover:bg-sidebar-light/30 transition-colors"
              >
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <div
                      class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold"
                      :class="getAvatarColor(item.dosen?.nama)"
                    >
                      {{ getInitials(item.dosen?.nama) }}
                    </div>
                    <div>
                      <p class="text-text-main font-medium text-sm">
                        {{ getDosenName(item.dosen) }}
                      </p>
                      <p class="text-text-secondary text-xs">
                        {{ item.dosen?.nip || "-" }}
                      </p>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <span
                    class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400"
                  >
                    {{ item.jabatan?.nama || "-" }}
                  </span>
                </td>
                <td class="px-6 py-4 text-text-secondary text-xs">
                  {{ item.periode?.nama || "-" }}
                </td>
                <td class="px-6 py-4 text-text-secondary text-xs">
                  <span v-if="item.prodi">{{ item.prodi.nama }}</span>
                  <span v-else-if="item.fakultas">{{
                    item.fakultas.nama_fakultas
                  }}</span>
                  <span v-else class="text-text-secondary/50">—</span>
                </td>
                <td class="px-6 py-4 text-text-secondary text-xs">
                  {{ formatDate(item.tgl_mulai) }}
                </td>
                <td class="px-6 py-4 text-text-secondary text-xs">
                  <span v-if="item.tgl_selesai">{{
                    formatDate(item.tgl_selesai)
                  }}</span>
                  <span
                    v-else
                    class="text-green-600 dark:text-green-400 font-medium"
                    >Aktif</span
                  >
                </td>
                <td class="px-6 py-4">
                  <span
                    v-if="item.is_plt"
                    class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-yellow-50 text-yellow-700 dark:bg-yellow-900/20 dark:text-yellow-400 border border-yellow-200 dark:border-yellow-800"
                  >
                    Plt.
                  </span>
                  <span
                    v-else
                    class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-400 border border-green-200 dark:border-green-800"
                  >
                    Definitif
                  </span>
                </td>
                <td class="px-6 py-4 text-right">
                  <div
                    class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity"
                  >
                    <button
                      @click="openPejabatModal(item)"
                      class="p-2 text-text-secondary hover:text-primary hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
                      title="Edit"
                    >
                      <span class="material-symbols-outlined text-[20px]"
                        >edit</span
                      >
                    </button>
                    <button
                      @click="confirmDeletePejabat(item)"
                      class="p-2 text-text-secondary hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                      title="Hapus"
                    >
                      <span class="material-symbols-outlined text-[20px]"
                        >delete</span
                      >
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Periode Modal -->
    <Transition name="modal-fade">
      <div
        v-if="showPeriodeModal"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
      >
        <div
          class="bg-white dark:bg-surface-light rounded-xl shadow-2xl w-full max-w-md"
        >
          <div class="p-6 border-b border-border-light">
            <h2 class="text-xl font-bold text-text-main">
              {{ periodeEditing ? "Edit Periode" : "Tambah Periode" }}
            </h2>
          </div>
          <form @submit.prevent="savePeriode" class="p-6 space-y-4">
            <div>
              <label class="block text-sm font-medium text-text-main mb-1"
                >Nama Periode <span class="text-red-500">*</span></label
              >
              <input
                v-model="periodeForm.nama"
                type="text"
                required
                maxlength="50"
                placeholder="Contoh: 2024-2028"
                class="w-full px-3 py-2.5 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary bg-background-light text-text-main dark:bg-background"
              />
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-text-main mb-1"
                  >Tanggal Mulai <span class="text-red-500">*</span></label
                >
                <input
                  v-model="periodeForm.tgl_mulai"
                  type="date"
                  required
                  class="w-full px-3 py-2.5 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary bg-background-light text-text-main dark:bg-background"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-text-main mb-1"
                  >Tanggal Selesai <span class="text-red-500">*</span></label
                >
                <input
                  v-model="periodeForm.tgl_selesai"
                  type="date"
                  required
                  class="w-full px-3 py-2.5 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary bg-background-light text-text-main dark:bg-background"
                />
              </div>
            </div>
            <div class="flex items-center gap-3">
              <label class="relative inline-flex items-center cursor-pointer">
                <input
                  type="checkbox"
                  v-model="periodeForm.is_active"
                  class="sr-only peer"
                />
                <div
                  class="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary/20 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary"
                ></div>
              </label>
              <span class="text-sm text-text-main">Periode Aktif</span>
            </div>
            <div class="flex gap-3 pt-4">
              <button
                type="button"
                @click="showPeriodeModal = false"
                class="flex-1 px-4 py-2.5 border border-border-light rounded-lg text-text-secondary hover:bg-background-light transition-colors"
              >
                Batal
              </button>
              <button
                type="submit"
                :disabled="savingPeriode"
                class="flex-1 px-4 py-2.5 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors disabled:opacity-50"
              >
                {{ savingPeriode ? "Menyimpan..." : "Simpan" }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Transition>

    <!-- Pejabat Modal -->
    <Transition name="modal-fade">
      <div
        v-if="showPejabatModal"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
      >
        <div
          class="bg-white dark:bg-surface-light rounded-xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto"
        >
          <div class="p-6 border-b border-border-light">
            <h2 class="text-xl font-bold text-text-main">
              {{ pejabatEditing ? "Edit Pejabat" : "Tambah Pejabat" }}
            </h2>
          </div>
          <form @submit.prevent="savePejabat" class="p-6 space-y-4">
            <!-- Periode -->
            <div>
              <label class="block text-sm font-medium text-text-main mb-1"
                >Periode <span class="text-red-500">*</span></label
              >
              <select
                v-model="pejabatForm.periode_id"
                required
                class="w-full px-3 py-2.5 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary bg-background-light text-text-main dark:bg-background"
              >
                <option value="">Pilih Periode</option>
                <option v-for="p in periodes" :key="p.id" :value="p.id">
                  {{ p.nama }}
                </option>
              </select>
            </div>
            <!-- Jabatan -->
            <div>
              <label class="block text-sm font-medium text-text-main mb-1"
                >Jabatan <span class="text-red-500">*</span></label
              >
              <select
                v-model="pejabatForm.jabatan_id"
                required
                @change="onJabatanChange"
                class="w-full px-3 py-2.5 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary bg-background-light text-text-main dark:bg-background"
              >
                <option value="">Pilih Jabatan</option>
                <option v-for="j in jabatanList" :key="j.id" :value="j.id">
                  {{ j.nama }} ({{ j.level }})
                </option>
              </select>
            </div>
            <!-- Prodi (if level = prodi) -->
            <div v-if="selectedJabatanLevel === 'prodi'">
              <label class="block text-sm font-medium text-text-main mb-1"
                >Program Studi <span class="text-red-500">*</span></label
              >
              <select
                v-model="pejabatForm.prodi_id"
                required
                class="w-full px-3 py-2.5 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary bg-background-light text-text-main dark:bg-background"
              >
                <option value="">Pilih Prodi</option>
                <option v-for="p in prodiList" :key="p.id" :value="p.id">
                  {{ p.nama }}
                </option>
              </select>
            </div>
            <!-- Fakultas (if level = fakultas) -->
            <div v-if="selectedJabatanLevel === 'fakultas'">
              <label class="block text-sm font-medium text-text-main mb-1"
                >Fakultas <span class="text-red-500">*</span></label
              >
              <select
                v-model="pejabatForm.fakultas_id"
                required
                class="w-full px-3 py-2.5 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary bg-background-light text-text-main dark:bg-background"
              >
                <option value="">Pilih Fakultas</option>
                <option v-for="f in fakultasList" :key="f.id" :value="f.id">
                  {{ f.nama_fakultas }}
                </option>
              </select>
            </div>
            <!-- Dosen -->
            <div>
              <label class="block text-sm font-medium text-text-main mb-1"
                >Dosen <span class="text-red-500">*</span></label
              >
              <div class="relative">
                <input
                  v-model="dosenSearch"
                  @input="debouncedSearchDosen"
                  @focus="showDosenDropdown = true"
                  placeholder="Cari nama/NIP dosen..."
                  class="w-full px-3 py-2.5 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary bg-background-light text-text-main dark:bg-background"
                />
                <div
                  v-if="selectedDosen"
                  class="absolute right-3 top-1/2 -translate-y-1/2"
                >
                  <button
                    type="button"
                    @click="clearDosen"
                    class="text-text-secondary hover:text-red-500"
                  >
                    <span class="material-symbols-outlined text-[18px]"
                      >close</span
                    >
                  </button>
                </div>
              </div>
              <div
                v-if="showDosenDropdown && dosenOptions.length > 0"
                class="mt-1 max-h-40 overflow-y-auto bg-white dark:bg-surface-light border border-border-light rounded-lg shadow-lg z-10 relative"
              >
                <button
                  v-for="d in dosenOptions"
                  :key="d.id"
                  type="button"
                  @click="selectDosen(d)"
                  class="w-full px-3 py-2 text-left text-sm hover:bg-sidebar-light/50 transition-colors flex items-center gap-2"
                >
                  <div>
                    <p class="text-text-main font-medium">
                      {{ getDosenName(d) }}
                    </p>
                    <p class="text-text-secondary text-xs">
                      NIP: {{ d.nip || "-" }}
                    </p>
                  </div>
                </button>
              </div>
            </div>
            <!-- Dates -->
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-text-main mb-1"
                  >Tanggal Mulai <span class="text-red-500">*</span></label
                >
                <input
                  v-model="pejabatForm.tgl_mulai"
                  type="date"
                  required
                  class="w-full px-3 py-2.5 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary bg-background-light text-text-main dark:bg-background"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-text-main mb-1"
                  >Tanggal Selesai</label
                >
                <input
                  v-model="pejabatForm.tgl_selesai"
                  type="date"
                  class="w-full px-3 py-2.5 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary bg-background-light text-text-main dark:bg-background"
                />
              </div>
            </div>
            <!-- Plt & Keterangan -->
            <div class="flex items-center gap-3">
              <label class="relative inline-flex items-center cursor-pointer">
                <input
                  type="checkbox"
                  v-model="pejabatForm.is_plt"
                  class="sr-only peer"
                />
                <div
                  class="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary/20 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-yellow-500"
                ></div>
              </label>
              <span class="text-sm text-text-main">Pelaksana Tugas (Plt.)</span>
            </div>
            <div>
              <label class="block text-sm font-medium text-text-main mb-1"
                >Keterangan</label
              >
              <input
                v-model="pejabatForm.keterangan"
                type="text"
                maxlength="255"
                placeholder="Opsional"
                class="w-full px-3 py-2.5 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary bg-background-light text-text-main dark:bg-background"
              />
            </div>
            <div class="flex gap-3 pt-4">
              <button
                type="button"
                @click="showPejabatModal = false"
                class="flex-1 px-4 py-2.5 border border-border-light rounded-lg text-text-secondary hover:bg-background-light transition-colors"
              >
                Batal
              </button>
              <button
                type="submit"
                :disabled="savingPejabat"
                class="flex-1 px-4 py-2.5 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors disabled:opacity-50"
              >
                {{ savingPejabat ? "Menyimpan..." : "Simpan" }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from "vue";
import { useToast } from "vue-toastification";
import Swal from "sweetalert2";
import adminService from "../../../services/adminService";

const toast = useToast();

const tabs = [
  { key: "periode", label: "Periode Jabatan", icon: "calendar_month" },
  { key: "pejabat", label: "Pejabat", icon: "badge" },
];
const activeTab = ref("periode");

// ================= Shared Data =================
const jabatanList = ref([]);
const prodiList = ref([]);
const fakultasList = ref([]);
const periodes = ref([]);

const fetchJabatan = async () => {
  try {
    const response = await adminService.getJabatan();
    if (response.success) jabatanList.value = response.data;
  } catch (e) {
    console.error(e);
  }
};

const fetchProdi = async () => {
  try {
    const response = await adminService.getProdi({ active_only: true });
    if (response.success)
      prodiList.value = response.data?.data || response.data;
  } catch (e) {
    console.error(e);
  }
};

const fetchFakultas = async () => {
  try {
    const response = await adminService.getFakultas();
    if (response.success)
      fakultasList.value = response.data?.data || response.data;
  } catch (e) {
    console.error(e);
  }
};

// ================= Periode =================
const loadingPeriode = ref(false);
const showPeriodeModal = ref(false);
const periodeEditing = ref(false);
const periodeEditId = ref(null);
const savingPeriode = ref(false);
const periodeForm = reactive({
  nama: "",
  tgl_mulai: "",
  tgl_selesai: "",
  is_active: false,
});

const fetchPeriodes = async () => {
  loadingPeriode.value = true;
  try {
    const response = await adminService.getPeriodeJabatan();
    if (response.success) periodes.value = response.data;
  } catch (e) {
    toast.error("Gagal memuat data periode");
  } finally {
    loadingPeriode.value = false;
  }
};

const openPeriodeModal = (item = null) => {
  if (item) {
    periodeEditing.value = true;
    periodeEditId.value = item.id;
    periodeForm.nama = item.nama;
    periodeForm.tgl_mulai = item.tgl_mulai?.split("T")[0] || "";
    periodeForm.tgl_selesai = item.tgl_selesai?.split("T")[0] || "";
    periodeForm.is_active = item.is_active;
  } else {
    periodeEditing.value = false;
    periodeEditId.value = null;
    periodeForm.nama = "";
    periodeForm.tgl_mulai = "";
    periodeForm.tgl_selesai = "";
    periodeForm.is_active = false;
  }
  showPeriodeModal.value = true;
};

const savePeriode = async () => {
  savingPeriode.value = true;
  try {
    if (periodeEditing.value) {
      await adminService.updatePeriodeJabatan(periodeEditId.value, periodeForm);
      toast.success("Periode berhasil diperbarui");
    } else {
      await adminService.createPeriodeJabatan(periodeForm);
      toast.success("Periode berhasil ditambahkan");
    }
    showPeriodeModal.value = false;
    fetchPeriodes();
  } catch (e) {
    toast.error(e.response?.data?.message || "Gagal menyimpan");
  } finally {
    savingPeriode.value = false;
  }
};

const confirmDeletePeriode = async (item) => {
  const result = await Swal.fire({
    title: "Apakah Anda yakin?",
    text: `Akan menghapus periode ${item.nama}`,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Ya, Hapus!",
    cancelButtonText: "Batal",
  });
  if (result.isConfirmed) {
    try {
      await adminService.deletePeriodeJabatan(item.id);
      toast.success("Periode berhasil dihapus");
      fetchPeriodes();
    } catch (e) {
      toast.error(e.response?.data?.message || "Gagal menghapus");
    }
  }
};

// ================= Pejabat =================
const loadingPejabat = ref(false);
const pejabatList = ref([]);
const showPejabatModal = ref(false);
const pejabatEditing = ref(false);
const pejabatEditId = ref(null);
const savingPejabat = ref(false);
const pejabatFilter = reactive({ periode_id: "", jabatan_id: "", search: "" });
let searchPejabatTimeout = null;

const pejabatForm = reactive({
  periode_id: "",
  jabatan_id: "",
  dosen_id: "",
  prodi_id: "",
  fakultas_id: "",
  tgl_mulai: "",
  tgl_selesai: "",
  is_plt: false,
  keterangan: "",
});

const selectedJabatanLevel = computed(() => {
  if (!pejabatForm.jabatan_id) return null;
  const j = jabatanList.value.find((x) => x.id == pejabatForm.jabatan_id);
  return j?.level || null;
});

const onJabatanChange = () => {
  pejabatForm.prodi_id = "";
  pejabatForm.fakultas_id = "";
};

// Dosen search
const dosenSearch = ref("");
const dosenOptions = ref([]);
const selectedDosen = ref(null);
const showDosenDropdown = ref(false);
let dosenSearchTimeout = null;

const searchDosenApi = async () => {
  if (!dosenSearch.value || dosenSearch.value.length < 2) {
    dosenOptions.value = [];
    return;
  }
  try {
    const response = await adminService.getDosen({
      search: dosenSearch.value,
      per_page: 10,
    });
    dosenOptions.value = response.data?.data || response.data || [];
  } catch (e) {
    console.error(e);
  }
};

const debouncedSearchDosen = () => {
  clearTimeout(dosenSearchTimeout);
  dosenSearchTimeout = setTimeout(searchDosenApi, 300);
};

const debouncedSearchPejabat = () => {
  clearTimeout(searchPejabatTimeout);
  searchPejabatTimeout = setTimeout(fetchPejabat, 300);
};

const selectDosen = (d) => {
  selectedDosen.value = d;
  pejabatForm.dosen_id = d.id;
  dosenSearch.value = getDosenName(d);
  showDosenDropdown.value = false;
  dosenOptions.value = [];
};

const clearDosen = () => {
  selectedDosen.value = null;
  pejabatForm.dosen_id = "";
  dosenSearch.value = "";
  dosenOptions.value = [];
};

const fetchPejabat = async () => {
  loadingPejabat.value = true;
  try {
    const params = {};
    if (pejabatFilter.periode_id) params.periode_id = pejabatFilter.periode_id;
    if (pejabatFilter.jabatan_id) params.jabatan_id = pejabatFilter.jabatan_id;
    if (pejabatFilter.search) params.search = pejabatFilter.search;
    const response = await adminService.getJabatanPejabat(params);
    if (response.success) pejabatList.value = response.data;
  } catch (e) {
    toast.error("Gagal memuat data pejabat");
  } finally {
    loadingPejabat.value = false;
  }
};

const openPejabatModal = (item = null) => {
  if (item) {
    pejabatEditing.value = true;
    pejabatEditId.value = item.id;
    pejabatForm.periode_id = item.periode_id;
    pejabatForm.jabatan_id = item.jabatan_id;
    pejabatForm.dosen_id = item.dosen_id;
    pejabatForm.prodi_id = item.prodi_id || "";
    pejabatForm.fakultas_id = item.fakultas_id || "";
    pejabatForm.tgl_mulai = item.tgl_mulai?.split("T")[0] || "";
    pejabatForm.tgl_selesai = item.tgl_selesai?.split("T")[0] || "";
    pejabatForm.is_plt = item.is_plt;
    pejabatForm.keterangan = item.keterangan || "";
    selectedDosen.value = item.dosen;
    dosenSearch.value = getDosenName(item.dosen);
  } else {
    pejabatEditing.value = false;
    pejabatEditId.value = null;
    pejabatForm.periode_id = "";
    pejabatForm.jabatan_id = "";
    pejabatForm.dosen_id = "";
    pejabatForm.prodi_id = "";
    pejabatForm.fakultas_id = "";
    pejabatForm.tgl_mulai = "";
    pejabatForm.tgl_selesai = "";
    pejabatForm.is_plt = false;
    pejabatForm.keterangan = "";
    clearDosen();
  }
  showPejabatModal.value = true;
};

const savePejabat = async () => {
  if (!pejabatForm.dosen_id) {
    toast.error("Pilih dosen terlebih dahulu");
    return;
  }
  savingPejabat.value = true;
  try {
    const payload = { ...pejabatForm };
    // Clean nullables
    if (!payload.tgl_selesai) payload.tgl_selesai = null;
    if (selectedJabatanLevel.value !== "prodi") payload.prodi_id = null;
    if (selectedJabatanLevel.value !== "fakultas") payload.fakultas_id = null;

    if (pejabatEditing.value) {
      await adminService.updateJabatanPejabat(pejabatEditId.value, payload);
      toast.success("Pejabat berhasil diperbarui");
    } else {
      await adminService.createJabatanPejabat(payload);
      toast.success("Pejabat berhasil ditambahkan");
    }
    showPejabatModal.value = false;
    fetchPejabat();
  } catch (e) {
    toast.error(e.response?.data?.message || "Gagal menyimpan");
  } finally {
    savingPejabat.value = false;
  }
};

const confirmDeletePejabat = async (item) => {
  const result = await Swal.fire({
    title: "Apakah Anda yakin?",
    text: `Akan menghapus data pejabat ${getDosenName(item.dosen)}`,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Ya, Hapus!",
    cancelButtonText: "Batal",
  });
  if (result.isConfirmed) {
    try {
      await adminService.deleteJabatanPejabat(item.id);
      toast.success("Pejabat berhasil dihapus");
      fetchPejabat();
    } catch (e) {
      toast.error(e.response?.data?.message || "Gagal menghapus");
    }
  }
};

// ================= Helpers =================
const getDosenName = (dosen) => {
  if (!dosen) return "-";
  const parts = [];
  if (dosen.gelar_depan) parts.push(dosen.gelar_depan);
  if (dosen.nama) parts.push(dosen.nama);
  if (dosen.gelar_belakang) parts.push(dosen.gelar_belakang);
  return parts.join(" ") || dosen.nama || "-";
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
    "bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400",
    "bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400",
    "bg-orange-100 text-orange-600 dark:bg-orange-900/30 dark:text-orange-400",
    "bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400",
  ];
  if (!name) return colors[0];
  return colors[name.charCodeAt(0) % colors.length];
};

const formatDate = (dateStr) => {
  if (!dateStr) return "-";
  const d = new Date(dateStr);
  return d.toLocaleDateString("id-ID", {
    day: "2-digit",
    month: "short",
    year: "numeric",
  });
};

// ================= Init =================
onMounted(async () => {
  await Promise.all([
    fetchPeriodes(),
    fetchJabatan(),
    fetchProdi(),
    fetchFakultas(),
  ]);
  fetchPejabat();
});
</script>
