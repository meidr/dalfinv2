<template>
  <div class="flex flex-col gap-6 animate-fade-in">
    <div
      class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4"
    >
      <div class="flex flex-col gap-1">
        <h1 class="text-3xl font-bold tracking-tight text-text-main">
          Jadwal Sidang & Seminar
        </h1>
        <p class="text-text-secondary text-base">
          Lihat jadwal sidang skripsi dan seminar mahasiswa bimbingan Anda.
        </p>
      </div>
    </div>

    <!-- Tabs -->
    <div class="flex border-b border-border-light mb-0 gap-6 overflow-x-auto">
      <button
        v-for="tab in tabs"
        :key="tab.value"
        @click="
          activeTab = tab.value;
          currentPage = 1;
          fetchJadwal();
        "
        class="flex items-center gap-2 border-b-[3px] pb-3 pt-2 px-1 whitespace-nowrap font-bold text-sm transition-colors"
        :class="
          activeTab === tab.value
            ? 'border-primary text-primary'
            : 'border-transparent text-text-secondary hover:text-text-main'
        "
      >
        <span class="material-symbols-outlined text-[18px]">{{
          tab.icon
        }}</span>
        {{ tab.label }}
        <span
          v-if="tab.count > 0"
          class="size-5 rounded-full text-[10px] font-bold flex items-center justify-center"
          :class="
            activeTab === tab.value
              ? 'bg-primary text-white'
              : 'bg-gray-200 dark:bg-gray-700 text-text-secondary'
          "
          >{{ tab.count }}</span
        >
      </button>
    </div>

    <!-- Date Filter -->
    <div
      class="flex flex-col sm:flex-row items-start sm:items-center gap-3 bg-surface-light border border-border-light rounded-xl px-4 py-3"
    >
      <div class="flex items-center gap-2 text-text-secondary text-sm">
        <span class="material-symbols-outlined text-[18px]">filter_list</span>
        <span class="font-medium">Filter Tanggal:</span>
      </div>
      <div class="flex flex-wrap items-center gap-3">
        <div class="flex items-center gap-2">
          <label class="text-xs text-text-secondary font-medium">Dari</label>
          <input
            v-model="dateFrom"
            type="date"
            class="border border-border-light rounded-lg px-3 py-1.5 text-sm bg-white dark:bg-white/5 text-text-main focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
          />
        </div>
        <div class="flex items-center gap-2">
          <label class="text-xs text-text-secondary font-medium">Sampai</label>
          <input
            v-model="dateTo"
            type="date"
            class="border border-border-light rounded-lg px-3 py-1.5 text-sm bg-white dark:bg-white/5 text-text-main focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
          />
        </div>
        <button
          v-if="dateFrom || dateTo"
          @click="
            dateFrom = '';
            dateTo = '';
            currentPage = 1;
          "
          class="text-xs text-red-500 hover:text-red-700 font-medium flex items-center gap-1"
        >
          <span class="material-symbols-outlined text-[14px]">close</span>
          Reset
        </button>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex flex-col gap-3">
      <div
        v-for="i in 3"
        :key="i"
        class="bg-surface-light border border-border-light rounded-xl p-4 animate-pulse"
      >
        <div class="flex items-center gap-4">
          <div class="size-12 rounded-lg bg-gray-200 dark:bg-gray-700"></div>
          <div class="flex-1">
            <div
              class="h-4 w-32 bg-gray-200 dark:bg-gray-700 rounded mb-2"
            ></div>
            <div class="h-3 w-48 bg-gray-200 dark:bg-gray-700 rounded"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Jadwal List -->
    <div v-else-if="paginatedList.length" class="flex flex-col gap-3">
      <div
        v-for="s in paginatedList"
        :key="s.id"
        class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden"
      >
        <div class="flex flex-col md:flex-row w-full">
          <!-- Date Badge (compact) -->
          <div
            class="w-full md:w-28 shrink-0 bg-gradient-to-br from-primary/10 to-primary/5 flex items-center justify-center p-4"
          >
            <div
              class="bg-white dark:bg-white/10 px-3 py-2 rounded-lg text-center shadow border border-primary/20"
            >
              <p
                class="text-primary text-xs font-extrabold uppercase tracking-widest"
              >
                {{ getMonth(s.tanggal) }}
              </p>
              <p class="text-primary text-2xl font-black leading-tight">
                {{ getDay(s.tanggal) }}
              </p>
              <p class="text-primary/60 text-[11px] font-bold">
                {{ getYear(s.tanggal) }}
              </p>
            </div>
          </div>

          <!-- Details (compact) -->
          <div class="flex-1 p-4 flex flex-col gap-2">
            <div class="flex justify-between items-start flex-wrap gap-2">
              <div>
                <div class="flex items-center gap-2 flex-wrap">
                  <span class="font-bold text-text-main text-sm">
                    {{ getJenisLabel(s.jenis) }}
                  </span>
                  <!-- Role Badge -->
                  <span
                    v-if="s.is_penguji"
                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400 border border-orange-300 dark:border-orange-800"
                  >
                    <span class="material-symbols-outlined text-[12px]"
                      >school</span
                    >
                    Sebagai Penguji
                  </span>
                  <span
                    v-else-if="s.role === 'pembimbing'"
                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 border border-blue-300 dark:border-blue-800"
                  >
                    <span class="material-symbols-outlined text-[12px]"
                      >supervisor_account</span
                    >
                    Sebagai Pembimbing
                  </span>
                </div>
                <p class="text-xs text-text-secondary mt-0.5">
                  {{ s.skripsi?.mahasiswa?.nama || "-" }}
                  <span class="text-text-secondary/60"
                    >({{ s.skripsi?.mahasiswa?.nim || "-" }})</span
                  >
                </p>
              </div>
              <span
                :class="getStatusClass(s.status)"
                class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold"
              >
                <span class="material-symbols-outlined text-[13px]">{{
                  getStatusIcon(s.status)
                }}</span>
                {{ getStatusLabel(s.status) }}
              </span>
            </div>

            <!-- Judul Skripsi -->
            <p
              v-if="s.skripsi?.judul"
              class="text-xs text-primary font-medium line-clamp-1"
            >
              {{ s.skripsi.judul }}
            </p>

            <div
              class="flex flex-wrap items-center gap-4 text-xs text-text-secondary"
            >
              <span v-if="s.waktu" class="flex items-center gap-1">
                <span class="material-symbols-outlined text-[14px] text-primary"
                  >schedule</span
                >
                {{ formatTime(s.waktu) }} WIB
              </span>
              <span v-if="s.ruangan" class="flex items-center gap-1">
                <span class="material-symbols-outlined text-[14px] text-primary"
                  >location_on</span
                >
                {{ s.ruangan }}
              </span>
              <span v-if="s.penguji?.length" class="flex items-center gap-1">
                <span class="material-symbols-outlined text-[14px] text-primary"
                  >groups</span
                >
                {{ s.penguji.length }} Penguji
              </span>
              <!-- Nilai status for penguji -->
              <span
                v-if="s.is_penguji && s.own_penguji?.nilai !== null"
                class="flex items-center gap-1 text-green-600 font-bold"
              >
                <span class="material-symbols-outlined text-[14px]"
                  >check_circle</span
                >
                Sudah Dinilai ({{ s.own_penguji.nilai }})
              </span>
              <span
                v-else-if="s.is_penguji && !s.own_penguji?.nilai"
                class="flex items-center gap-1 text-amber-600 font-bold"
              >
                <span class="material-symbols-outlined text-[14px]"
                  >pending</span
                >
                Belum Dinilai
              </span>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-2 pt-1 flex-wrap">
              <button
                v-if="s.is_penguji"
                @click="openNilaiModal(s)"
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-colors"
                :class="
                  s.own_penguji?.nilai !== null
                    ? 'bg-green-50 text-green-700 border border-green-200 hover:bg-green-100'
                    : 'bg-primary text-white hover:bg-blue-600 shadow-sm'
                "
              >
                <span class="material-symbols-outlined text-[14px]">{{
                  s.own_penguji?.nilai !== null ? "edit" : "rate_review"
                }}</span>
                {{
                  s.own_penguji?.nilai !== null ? "Edit Nilai" : "Input Nilai"
                }}
              </button>
              <button
                v-if="s.is_penguji"
                @click="openDetailModal(s)"
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-gray-50 dark:bg-white/5 text-text-secondary border border-border-light hover:bg-gray-100 dark:hover:bg-white/10 transition-colors"
              >
                <span class="material-symbols-outlined text-[14px]"
                  >visibility</span
                >
                Detail Nilai
              </button>
              <button
                @click="goToSkripsiDetail(s.skripsi_id)"
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800 hover:bg-indigo-100 dark:hover:bg-indigo-900/40 transition-colors"
              >
                <span class="material-symbols-outlined text-[14px]"
                  >description</span
                >
                Lihat Skripsi
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div
        v-if="totalPages > 1"
        class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-2 pt-4 border-t border-border-light"
      >
        <p class="text-sm text-text-secondary">
          Menampilkan {{ startIndex + 1 }}-{{ endIndex }} dari
          {{ filteredList.length }} jadwal
        </p>
        <div class="flex items-center gap-1">
          <button
            @click="currentPage = currentPage - 1"
            :disabled="currentPage <= 1"
            class="size-8 rounded-lg flex items-center justify-center text-sm border border-border-light hover:bg-sidebar-light transition-colors disabled:opacity-30 disabled:cursor-not-allowed"
          >
            <span class="material-symbols-outlined text-[16px]"
              >chevron_left</span
            >
          </button>
          <button
            v-for="page in visiblePages"
            :key="page"
            @click="currentPage = page"
            class="size-8 rounded-lg flex items-center justify-center text-sm font-medium transition-colors"
            :class="
              currentPage === page
                ? 'bg-primary text-white shadow-sm'
                : 'border border-border-light hover:bg-sidebar-light text-text-main'
            "
          >
            {{ page }}
          </button>
          <button
            @click="currentPage = currentPage + 1"
            :disabled="currentPage >= totalPages"
            class="size-8 rounded-lg flex items-center justify-center text-sm border border-border-light hover:bg-sidebar-light transition-colors disabled:opacity-30 disabled:cursor-not-allowed"
          >
            <span class="material-symbols-outlined text-[16px]"
              >chevron_right</span
            >
          </button>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div
      v-else
      class="bg-surface-light rounded-xl shadow-sm border border-border-light p-12 flex flex-col items-center justify-center gap-3 text-center"
    >
      <span
        class="material-symbols-outlined text-5xl text-text-secondary opacity-40"
        >event_busy</span
      >
      <h3 class="text-lg font-bold text-text-main">Belum Ada Jadwal</h3>
      <p class="text-text-secondary text-sm max-w-md">
        {{ getEmptyMessage() }}
      </p>
    </div>

    <!-- ========== INPUT NILAI MODAL ========== -->
    <Teleport to="body">
    <Transition name="modal-fade">
      <div
        v-if="showNilaiModal"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
        @click.self="showNilaiModal = false"
      >
        <div
          class="bg-white dark:bg-surface-light rounded-xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto"
        >
          <!-- Header -->
          <div
            class="p-5 border-b border-border-light sticky top-0 bg-white dark:bg-surface-light z-10"
          >
            <div class="flex items-center justify-between">
              <div>
                <h2 class="text-lg font-bold text-text-main">
                  Input Nilai Sidang
                </h2>
                <p class="text-xs text-text-secondary mt-0.5">
                  {{ nilaiSeminar?.skripsi?.mahasiswa?.nama }}
                  ({{ nilaiSeminar?.skripsi?.mahasiswa?.nim }})
                </p>
              </div>
              <button
                @click="showNilaiModal = false"
                class="text-text-secondary hover:text-text-main"
              >
                <span class="material-symbols-outlined">close</span>
              </button>
            </div>
            <!-- Jenis + Judul -->
            <div class="mt-2">
              <span
                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-primary/10 text-primary"
              >
                {{ getJenisLabel(nilaiSeminar?.jenis) }}
              </span>
              <p
                v-if="nilaiSeminar?.skripsi?.judul"
                class="text-xs text-text-secondary mt-1 line-clamp-2"
              >
                {{ nilaiSeminar.skripsi.judul }}
              </p>
            </div>
          </div>

          <!-- Body -->
          <div class="p-5 space-y-4">
            <!-- 4 Criteria Grid -->
            <div>
              <p class="text-sm font-medium text-text-main mb-3">
                Komponen Penilaian
              </p>
              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label
                    class="block text-xs font-medium text-text-secondary mb-1"
                    >Metodologi & Teknik (MT)</label
                  >
                  <input
                    v-model.number="nilaiForm.nilai_mt"
                    type="number"
                    min="0"
                    max="100"
                    step="0.01"
                    class="w-full px-3 py-2.5 border border-border-light rounded-lg text-sm font-bold text-center bg-white dark:bg-white/5 text-text-main focus:ring-2 focus:ring-primary/20 focus:border-primary"
                    placeholder="0-100"
                  />
                </div>
                <div>
                  <label
                    class="block text-xs font-medium text-text-secondary mb-1"
                    >Materi Skripsi (MS)</label
                  >
                  <input
                    v-model.number="nilaiForm.nilai_ms"
                    type="number"
                    min="0"
                    max="100"
                    step="0.01"
                    class="w-full px-3 py-2.5 border border-border-light rounded-lg text-sm font-bold text-center bg-white dark:bg-white/5 text-text-main focus:ring-2 focus:ring-primary/20 focus:border-primary"
                    placeholder="0-100"
                  />
                </div>
                <div>
                  <label
                    class="block text-xs font-medium text-text-secondary mb-1"
                    >Penampilan Mahasiswa (PM)</label
                  >
                  <input
                    v-model.number="nilaiForm.nilai_pm"
                    type="number"
                    min="0"
                    max="100"
                    step="0.01"
                    class="w-full px-3 py-2.5 border border-border-light rounded-lg text-sm font-bold text-center bg-white dark:bg-white/5 text-text-main focus:ring-2 focus:ring-primary/20 focus:border-primary"
                    placeholder="0-100"
                  />
                </div>
                <div>
                  <label
                    class="block text-xs font-medium text-text-secondary mb-1"
                    >Penguasaan Isi (PI)</label
                  >
                  <input
                    v-model.number="nilaiForm.nilai_pi"
                    type="number"
                    min="0"
                    max="100"
                    step="0.01"
                    class="w-full px-3 py-2.5 border border-border-light rounded-lg text-sm font-bold text-center bg-white dark:bg-white/5 text-text-main focus:ring-2 focus:ring-primary/20 focus:border-primary"
                    placeholder="0-100"
                  />
                </div>
              </div>
            </div>

            <!-- Per-penguji average preview -->
            <div
              v-if="nilaiFormAvg !== null"
              class="bg-gradient-to-r from-slate-50 to-indigo-50 dark:from-slate-800/50 dark:to-indigo-900/30 rounded-xl p-4 border border-indigo-100 dark:border-indigo-800"
            >
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-xs text-text-secondary font-medium mb-1">
                    Rata-rata Anda
                  </p>
                  <p class="text-2xl font-black text-text-main">
                    {{ nilaiFormAvg }}
                  </p>
                </div>
                <div class="text-center">
                  <p class="text-xs text-text-secondary font-medium mb-1">
                    Grade
                  </p>
                  <span
                    class="inline-flex items-center justify-center size-10 rounded-xl text-base font-black"
                    :class="getGradeClass(getGrade(nilaiFormAvg))"
                    >{{ getGrade(nilaiFormAvg) }}</span
                  >
                </div>
              </div>
            </div>

            <!-- Catatan -->
            <div>
              <label class="block text-sm font-medium text-text-main mb-1"
                >Catatan</label
              >
              <textarea
                v-model="nilaiForm.catatan"
                rows="3"
                class="w-full px-3 py-2 border border-border-light rounded-lg text-sm bg-white dark:bg-white/5 text-text-main focus:ring-2 focus:ring-primary/20 focus:border-primary"
                placeholder="Catatan untuk mahasiswa (opsional)"
              ></textarea>
            </div>

            <!-- Hasil Sidang/Seminar (ketua penguji only) -->
            <div
              v-if="
                nilaiSeminar?.own_penguji?.peran === 'ketua' &&
                ['sempro', 'semhas', 'sidang'].includes(nilaiSeminar?.jenis)
              "
              class="bg-amber-50 dark:bg-amber-900/20 rounded-xl p-4 border border-amber-200 dark:border-amber-800"
            >
              <div class="flex items-center gap-2 mb-2">
                <span class="material-symbols-outlined text-amber-600 text-base"
                  >gavel</span
                >
                <p class="text-sm font-bold text-amber-800 dark:text-amber-300">
                  Keputusan {{ getJenisLabel(nilaiSeminar?.jenis) }} (Ketua
                  Penguji)
                </p>
              </div>
              <select
                v-model="nilaiForm.hasil"
                class="w-full px-3 py-2.5 border border-amber-300 dark:border-amber-700 rounded-lg text-sm font-bold text-text-main focus:ring-2 focus:ring-amber-200 focus:border-amber-400 bg-white dark:bg-white/5"
              >
                <option value="">— Pilih Hasil —</option>
                <option value="lulus">✅ Lulus</option>
                <option value="lulus_revisi">📝 Lulus dengan Revisi</option>
                <option value="tidak_lulus">❌ Tidak Lulus</option>
              </select>
            </div>

            <!-- Perbaikan Proposal (ketua + sempro only) -->
            <div
              v-if="
                nilaiSeminar?.own_penguji?.peran === 'ketua' &&
                nilaiSeminar?.jenis === 'sempro'
              "
              class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 border border-blue-200 dark:border-blue-800"
            >
              <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                  <span
                    class="material-symbols-outlined text-blue-600 text-base"
                    >edit_note</span
                  >
                  <p class="text-sm font-bold text-blue-800 dark:text-blue-300">
                    Lembar Perbaikan Proposal
                  </p>
                </div>
                <button
                  @click="addPerbaikanRow"
                  type="button"
                  class="flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-600 text-white hover:bg-blue-700 transition-colors"
                >
                  <span class="material-symbols-outlined text-[14px]">add</span>
                  Tambah
                </button>
              </div>

              <div
                v-if="nilaiForm.perbaikan.length === 0"
                class="text-center py-4"
              >
                <p class="text-xs text-blue-600/70">
                  Belum ada item perbaikan. Klik "Tambah" untuk menambahkan.
                </p>
              </div>

              <div
                v-for="(item, idx) in nilaiForm.perbaikan"
                :key="idx"
                class="bg-white dark:bg-white/5 rounded-lg p-3 border border-blue-100 dark:border-blue-800 mb-2"
              >
                <div class="flex items-center justify-between mb-2">
                  <span class="text-xs font-bold text-blue-700"
                    >Perbaikan #{{ idx + 1 }}</span
                  >
                  <button
                    @click="nilaiForm.perbaikan.splice(idx, 1)"
                    type="button"
                    class="text-red-400 hover:text-red-600 transition-colors"
                  >
                    <span class="material-symbols-outlined text-[16px]"
                      >delete</span
                    >
                  </button>
                </div>
                <div class="grid grid-cols-3 gap-2">
                  <div class="col-span-2">
                    <label
                      class="block text-[10px] font-medium text-text-secondary mb-0.5"
                      >Topik *</label
                    >
                    <input
                      v-model="item.topik"
                      type="text"
                      placeholder="Topik perbaikan"
                      class="w-full px-2.5 py-1.5 border border-border-light rounded-lg text-xs focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                    />
                  </div>
                  <div>
                    <label
                      class="block text-[10px] font-medium text-text-secondary mb-0.5"
                      >Halaman</label
                    >
                    <input
                      v-model="item.halaman"
                      type="text"
                      placeholder="cth: 12-15"
                      class="w-full px-2.5 py-1.5 border border-border-light rounded-lg text-xs focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                    />
                  </div>
                </div>
                <div class="mt-2">
                  <label
                    class="block text-[10px] font-medium text-text-secondary mb-0.5"
                    >Uraian Perbaikan</label
                  >
                  <textarea
                    v-model="item.uraian"
                    rows="2"
                    placeholder="Uraikan perbaikan yang diperlukan..."
                    class="w-full px-2.5 py-1.5 border border-border-light rounded-lg text-xs focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                  ></textarea>
                </div>
              </div>
            </div>

            <!-- Buttons -->
            <div class="flex gap-3 pt-2">
              <button
                @click="showNilaiModal = false"
                class="flex-1 px-4 py-2.5 border border-border-light rounded-lg text-text-secondary hover:bg-gray-50 dark:hover:bg-white/5 transition-colors text-sm font-medium"
              >
                Batal
              </button>
              <button
                @click="saveNilai"
                :disabled="!canSubmitNilai || savingNilai"
                class="flex-1 px-4 py-2.5 bg-primary text-white rounded-lg hover:bg-blue-600 transition-colors disabled:opacity-50 text-sm font-bold"
              >
                {{ savingNilai ? "Menyimpan..." : "Simpan Nilai" }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
    </Teleport>

    <!-- ========== DETAIL MODAL ========== -->
    <Teleport to="body">
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
            class="p-5 border-b border-border-light sticky top-0 bg-white dark:bg-surface-light z-10 flex justify-between items-center"
          >
            <div>
              <h2 class="text-lg font-bold text-text-main">Detail Seminar</h2>
              <p class="text-xs text-text-secondary">
                {{ detailData?.skripsi?.mahasiswa?.nama }}
              </p>
            </div>
            <button
              @click="showDetailModal = false"
              class="text-text-secondary hover:text-text-main"
            >
              <span class="material-symbols-outlined">close</span>
            </button>
          </div>

          <div v-if="loadingDetail" class="p-8 text-center">
            <span
              class="material-symbols-outlined animate-spin text-3xl text-primary"
              >progress_activity</span
            >
          </div>

          <div v-else-if="detailData" class="p-5 space-y-4">
            <!-- Info -->
            <div class="grid grid-cols-3 gap-3 text-sm">
              <div>
                <p class="text-xs text-text-secondary">Tanggal</p>
                <p class="font-medium">
                  {{ formatDate(detailData.tanggal) }}
                </p>
              </div>
              <div>
                <p class="text-xs text-text-secondary">Waktu</p>
                <p class="font-medium">
                  {{ formatTime(detailData.waktu) }} WIB
                </p>
              </div>
              <div>
                <p class="text-xs text-text-secondary">Ruangan</p>
                <p class="font-medium">{{ detailData.ruangan || "-" }}</p>
              </div>
            </div>

            <!-- All Penguji Scores -->
            <div v-if="detailData.penguji?.length">
              <p
                class="text-xs text-text-secondary font-bold uppercase tracking-wider mb-2"
              >
                Nilai Penguji
              </p>
              <div class="space-y-2">
                <div
                  v-for="p in detailData.penguji"
                  :key="p.id"
                  class="bg-gray-50 dark:bg-white/5 rounded-lg px-3 py-3 border border-border-light"
                >
                  <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                      <div
                        class="bg-orange-100 dark:bg-orange-900/30 size-6 rounded-full flex items-center justify-center text-orange-600"
                      >
                        <span class="material-symbols-outlined text-[12px]"
                          >school</span
                        >
                      </div>
                      <span class="text-sm font-medium text-text-main">{{
                        p.dosen?.full_name || p.dosen?.nama || "-"
                      }}</span>
                      <span
                        class="text-[9px] px-1.5 py-0.5 rounded bg-gray-200 dark:bg-gray-700 text-text-secondary"
                        >{{ getPeranLabel(p.peran) }}</span
                      >
                    </div>
                    <span
                      v-if="p.nilai !== null"
                      class="text-sm font-black text-primary"
                      >{{ p.nilai }}</span
                    >
                    <span v-else class="text-xs text-text-secondary italic"
                      >Belum dinilai</span
                    >
                  </div>
                  <!-- Component scores -->
                  <div
                    v-if="p.nilai !== null"
                    class="grid grid-cols-4 gap-2 text-center"
                  >
                    <div
                      class="bg-white dark:bg-white/5 rounded px-2 py-1 border border-border-light"
                    >
                      <p class="text-[9px] text-text-secondary font-medium">
                        MT
                      </p>
                      <p class="text-xs font-bold text-text-main">
                        {{ p.nilai_mt ?? "-" }}
                      </p>
                    </div>
                    <div
                      class="bg-white dark:bg-white/5 rounded px-2 py-1 border border-border-light"
                    >
                      <p class="text-[9px] text-text-secondary font-medium">
                        MS
                      </p>
                      <p class="text-xs font-bold text-text-main">
                        {{ p.nilai_ms ?? "-" }}
                      </p>
                    </div>
                    <div
                      class="bg-white dark:bg-white/5 rounded px-2 py-1 border border-border-light"
                    >
                      <p class="text-[9px] text-text-secondary font-medium">
                        PM
                      </p>
                      <p class="text-xs font-bold text-text-main">
                        {{ p.nilai_pm ?? "-" }}
                      </p>
                    </div>
                    <div
                      class="bg-white dark:bg-white/5 rounded px-2 py-1 border border-border-light"
                    >
                      <p class="text-[9px] text-text-secondary font-medium">
                        PI
                      </p>
                      <p class="text-xs font-bold text-text-main">
                        {{ p.nilai_pi ?? "-" }}
                      </p>
                    </div>
                  </div>
                  <!-- Catatan -->
                  <div
                    v-if="p.catatan"
                    class="mt-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg px-3 py-2 border border-blue-100 dark:border-blue-800"
                  >
                    <p class="text-[10px] text-blue-600 font-semibold mb-0.5">
                      Catatan:
                    </p>
                    <p class="text-xs text-text-main">{{ p.catatan }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Final Result -->
            <div
              v-if="detailData.all_scored"
              class="bg-gradient-to-r from-slate-50 to-indigo-50 dark:from-slate-800/50 dark:to-indigo-900/30 rounded-xl p-4 border border-indigo-100 dark:border-indigo-800"
            >
              <p
                class="text-xs text-text-secondary font-bold uppercase tracking-wider mb-2"
              >
                Hasil Akhir
              </p>
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-xs text-text-secondary">Nilai Akhir</p>
                  <p class="text-2xl font-black text-text-main">
                    {{ detailData.nilai ?? "-" }}
                  </p>
                </div>
                <div class="text-center">
                  <p class="text-xs text-text-secondary">Grade</p>
                  <span
                    v-if="detailData.grade"
                    class="inline-flex items-center justify-center size-10 rounded-xl text-base font-black"
                    :class="getGradeClass(detailData.grade)"
                    >{{ detailData.grade }}</span
                  >
                </div>
                <div class="text-right">
                  <p class="text-xs text-text-secondary">Hasil</p>
                  <span
                    v-if="detailData.hasil"
                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold"
                    :class="getHasilClass(detailData.hasil)"
                  >
                    {{ getHasilLabel(detailData.hasil) }}
                  </span>
                </div>
              </div>
            </div>
            <div
              v-else-if="detailData.total_penguji > 0"
              class="bg-amber-50 dark:bg-amber-900/20 rounded-xl p-4 border border-amber-200 dark:border-amber-800 text-center"
            >
              <p class="text-sm text-amber-700 dark:text-amber-400 font-medium">
                {{ detailData.scored_count }}/{{ detailData.total_penguji }}
                penguji sudah memberikan nilai
              </p>
            </div>
          </div>
        </div>
      </div>
    </Transition>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, reactive, onMounted } from "vue";
import { useRouter } from "vue-router";
import dosenService from "../../services/dosenService";

const router = useRouter();

const loading = ref(true);
const jadwalList = ref([]);
const activeTab = ref("");
const allJadwal = ref([]);
const semhasEnabled = ref(true);
const dateFrom = ref("");
const dateTo = ref("");
const currentPage = ref(1);
const perPage = 5;

// Nilai modal
const showNilaiModal = ref(false);
const nilaiSeminar = ref(null);
const savingNilai = ref(false);
const nilaiForm = reactive({
  nilai_mt: null,
  nilai_ms: null,
  nilai_pm: null,
  nilai_pi: null,
  catatan: "",
  hasil: "",
  perbaikan: [],
});

// Detail modal
const showDetailModal = ref(false);
const loadingDetail = ref(false);
const detailData = ref(null);

const tabs = computed(() => {
  const all = allJadwal.value;
  const allTabs = [
    {
      value: "",
      label: "Semua",
      icon: "calendar_month",
      count: all.length,
    },
    {
      value: "sempro",
      label: "Seminar Proposal",
      icon: "record_voice_over",
      count: all.filter((j) => j.jenis === "sempro").length,
    },
    {
      value: "semhas",
      label: "Seminar Hasil",
      icon: "assignment",
      count: all.filter((j) => j.jenis === "semhas").length,
    },
    {
      value: "sidang",
      label: "Sidang Skripsi",
      icon: "school",
      count: all.filter((j) => j.jenis === "sidang").length,
    },
  ];
  if (!semhasEnabled.value) {
    return allTabs.filter((t) => t.value !== "semhas");
  }
  return allTabs;
});

// Date-filtered list
const filteredList = computed(() => {
  let list = jadwalList.value;
  if (dateFrom.value) {
    list = list.filter((s) => {
      const d = s.tanggal?.substring(0, 10);
      return d && d >= dateFrom.value;
    });
  }
  if (dateTo.value) {
    list = list.filter((s) => {
      const d = s.tanggal?.substring(0, 10);
      return d && d <= dateTo.value;
    });
  }
  return list;
});

// Pagination
const totalPages = computed(() =>
  Math.ceil(filteredList.value.length / perPage),
);
const startIndex = computed(() => (currentPage.value - 1) * perPage);
const endIndex = computed(() =>
  Math.min(startIndex.value + perPage, filteredList.value.length),
);
const paginatedList = computed(() =>
  filteredList.value.slice(startIndex.value, endIndex.value),
);
const visiblePages = computed(() => {
  const pages = [];
  const total = totalPages.value;
  const current = currentPage.value;
  let start = Math.max(1, current - 2);
  let end = Math.min(total, start + 4);
  if (end - start < 4) start = Math.max(1, end - 4);
  for (let i = start; i <= end; i++) pages.push(i);
  return pages;
});

// Nilai form computed
const nilaiFormAvg = computed(() => {
  const vals = [
    nilaiForm.nilai_mt,
    nilaiForm.nilai_ms,
    nilaiForm.nilai_pm,
    nilaiForm.nilai_pi,
  ];
  const filled = vals.filter((v) => v !== null && v !== "" && !isNaN(v));
  if (filled.length !== 4) return null;
  const avg = filled.reduce((a, b) => a + Number(b), 0) / 4;
  return Math.round(avg * 100) / 100;
});

const canSubmitNilai = computed(() => {
  return (
    nilaiForm.nilai_mt !== null &&
    nilaiForm.nilai_mt !== "" &&
    nilaiForm.nilai_ms !== null &&
    nilaiForm.nilai_ms !== "" &&
    nilaiForm.nilai_pm !== null &&
    nilaiForm.nilai_pm !== "" &&
    nilaiForm.nilai_pi !== null &&
    nilaiForm.nilai_pi !== ""
  );
});

const fetchJadwal = async () => {
  loading.value = true;
  try {
    const params = {};
    if (activeTab.value) params.jenis = activeTab.value;
    const res = await dosenService.getJadwal(params);
    if (res.success) {
      jadwalList.value = res.data || [];
      if (!activeTab.value) {
        allJadwal.value = res.data || [];
      }
    }
  } catch (err) {
    console.error("Failed to fetch jadwal:", err);
  } finally {
    loading.value = false;
  }
};

const openNilaiModal = (seminar) => {
  nilaiSeminar.value = seminar;
  const own = seminar.own_penguji;
  nilaiForm.nilai_mt =
    own?.nilai_mt !== null && own?.nilai_mt !== undefined
      ? Number(own.nilai_mt)
      : null;
  nilaiForm.nilai_ms =
    own?.nilai_ms !== null && own?.nilai_ms !== undefined
      ? Number(own.nilai_ms)
      : null;
  nilaiForm.nilai_pm =
    own?.nilai_pm !== null && own?.nilai_pm !== undefined
      ? Number(own.nilai_pm)
      : null;
  nilaiForm.nilai_pi =
    own?.nilai_pi !== null && own?.nilai_pi !== undefined
      ? Number(own.nilai_pi)
      : null;
  nilaiForm.catatan = own?.catatan || "";
  nilaiForm.hasil = seminar.hasil || "";
  // Load existing perbaikan items
  nilaiForm.perbaikan = (seminar.perbaikan_proposal || []).map((p) => ({
    topik: p.topik || "",
    halaman: p.halaman || "",
    uraian: p.uraian || "",
  }));
  showNilaiModal.value = true;
};

const addPerbaikanRow = () => {
  nilaiForm.perbaikan.push({ topik: "", halaman: "", uraian: "" });
};

const saveNilai = async () => {
  if (!nilaiSeminar.value) return;
  savingNilai.value = true;
  try {
    const payload = {
      nilai_mt: nilaiForm.nilai_mt,
      nilai_ms: nilaiForm.nilai_ms,
      nilai_pm: nilaiForm.nilai_pm,
      nilai_pi: nilaiForm.nilai_pi,
      catatan: nilaiForm.catatan || null,
    };
    // Only ketua penguji can send hasil
    if (nilaiSeminar.value?.own_penguji?.peran === "ketua" && nilaiForm.hasil) {
      payload.hasil = nilaiForm.hasil;
    }
    // Send perbaikan items (ketua + sempro)
    if (
      nilaiSeminar.value?.own_penguji?.peran === "ketua" &&
      nilaiSeminar.value?.jenis === "sempro" &&
      nilaiForm.perbaikan.length > 0
    ) {
      payload.perbaikan = nilaiForm.perbaikan.filter((p) => p.topik?.trim());
    }
    await dosenService.submitNilai(nilaiSeminar.value.id, payload);
    showNilaiModal.value = false;
    fetchJadwal();
  } catch (err) {
    console.error("Failed to save nilai:", err);
    alert(
      "Gagal menyimpan nilai: " + (err.response?.data?.message || err.message),
    );
  } finally {
    savingNilai.value = false;
  }
};

const openDetailModal = async (seminar) => {
  showDetailModal.value = true;
  loadingDetail.value = true;
  detailData.value = null;
  try {
    const res = await dosenService.getSeminarDetail(seminar.id);
    if (res.success) {
      detailData.value = res.data;
    }
  } catch (err) {
    console.error("Failed to load detail:", err);
  } finally {
    loadingDetail.value = false;
  }
};

const goToSkripsiDetail = (skripsiId) => {
  if (skripsiId) {
    router.push(`/dosen/bimbingan/${skripsiId}`);
  }
};

onMounted(async () => {
  try {
    const moduleRes = await dosenService.getModuleSettings();
    if (moduleRes.success) {
      semhasEnabled.value = moduleRes.data.semhas_enabled;
    }
  } catch (e) {
    console.error("Failed to load module settings:", e);
  }
  fetchJadwal();
});

// Helpers
const getJenisLabel = (jenis) => {
  const map = {
    sempro: "Seminar Proposal",
    semhas: "Seminar Hasil",
    sidang: "Sidang Skripsi",
  };
  return map[jenis] || jenis || "Seminar";
};

const getPeranLabel = (peran) => {
  const map = {
    ketua: "Ketua Penguji",
    sekretaris: "Sekretaris",
    penguji: "Penguji",
    penguji_1: "Penguji 1",
    penguji_2: "Penguji 2",
    pembimbing_1: "Pembimbing 1",
    pembimbing_2: "Pembimbing 2",
  };
  return map[peran] || peran || "Penguji";
};

const getStatusLabel = (status) => {
  const map = {
    terjadwal: "Terjadwal",
    selesai: "Selesai",
    batal: "Dibatalkan",
    menunggu_nilai: "Menunggu Nilai",
    berlangsung: "Berlangsung",
    pending: "Menunggu",
  };
  return map[status] || status || "Terjadwal";
};

const getStatusIcon = (status) => {
  const map = {
    terjadwal: "event",
    selesai: "check_circle",
    batal: "cancel",
    menunggu_nilai: "hourglass_top",
    berlangsung: "play_circle",
    pending: "pending",
  };
  return map[status] || "event";
};

const getStatusClass = (status) => {
  const map = {
    terjadwal: "bg-blue-100 text-blue-700 border border-blue-300",
    selesai: "bg-green-100 text-green-700 border border-green-300",
    batal: "bg-red-100 text-red-700 border border-red-300",
    menunggu_nilai: "bg-amber-100 text-amber-700 border border-amber-300",
    berlangsung: "bg-purple-100 text-purple-700 border border-purple-300",
    pending: "bg-gray-100 text-gray-600 border border-gray-300",
  };
  return map[status] || "bg-blue-100 text-blue-700 border border-blue-300";
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

const getHasilClass = (hasil) => {
  const map = {
    lulus: "bg-green-100 text-green-700",
    lulus_revisi: "bg-yellow-100 text-yellow-700",
    tidak_lulus: "bg-red-100 text-red-700",
  };
  return map[hasil] || "bg-gray-100 text-gray-600";
};

const getHasilLabel = (hasil) => {
  const map = {
    lulus: "Lulus",
    lulus_revisi: "Lulus (Revisi)",
    tidak_lulus: "Tidak Lulus",
  };
  return map[hasil] || hasil || "-";
};

const getMonth = (dateStr) => {
  if (!dateStr) return "-";
  return new Date(dateStr).toLocaleDateString("id-ID", { month: "short" });
};

const getDay = (dateStr) => {
  if (!dateStr) return "-";
  return new Date(dateStr).getDate();
};

const getYear = (dateStr) => {
  if (!dateStr) return "-";
  return new Date(dateStr).getFullYear();
};

const formatDate = (dateStr) => {
  if (!dateStr) return "-";
  return new Date(dateStr).toLocaleDateString("id-ID", {
    day: "numeric",
    month: "short",
    year: "numeric",
  });
};

const formatTime = (timeStr) => {
  if (!timeStr) return "-";
  if (timeStr.includes("T") || timeStr.includes(" ")) {
    const d = new Date(timeStr);
    return d.toLocaleTimeString("id-ID", {
      hour: "2-digit",
      minute: "2-digit",
    });
  }
  return timeStr.substring(0, 5);
};

const getEmptyMessage = () => {
  const tabMap = {
    sempro: "Belum ada jadwal seminar proposal untuk mahasiswa bimbingan Anda.",
    semhas: "Belum ada jadwal seminar hasil untuk mahasiswa bimbingan Anda.",
    sidang: "Belum ada jadwal sidang skripsi untuk mahasiswa bimbingan Anda.",
  };
  return (
    tabMap[activeTab.value] ||
    "Belum ada jadwal seminar atau sidang untuk saat ini."
  );
};
</script>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(5px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.2s ease;
}
.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}
</style>
