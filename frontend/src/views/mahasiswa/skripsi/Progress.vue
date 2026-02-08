<template>
  <div class="flex flex-col gap-6 animate-fade-in">
    <!-- Workflow Stepper -->
    <div
      class="bg-surface-light rounded-xl shadow-sm border border-border-light p-6 overflow-x-auto"
    >
      <h3 class="text-lg font-bold text-text-main mb-6">Workflow Progress</h3>
      <div class="min-w-[800px]">
        <ol class="flex items-center w-full relative justify-between">
          <!-- Step 1: Completed -->
          <li class="relative flex flex-col items-center group w-full">
            <div class="flex items-center w-full">
              <div
                class="flex items-center justify-center size-10 bg-green-100 rounded-full shrink-0 z-10 border-2 border-green-500"
              >
                <span
                  class="material-symbols-outlined text-green-600 text-xl font-bold"
                  >check</span
                >
              </div>
              <div class="flex-auto border-t-4 border-green-500"></div>
            </div>
            <div class="mt-3 text-center pr-6">
              <h4 class="text-sm font-bold text-green-700">Judul</h4>
              <p class="text-xs text-text-secondary">Selesai</p>
            </div>
          </li>
          <!-- Step 2: Completed -->
          <li class="relative flex flex-col items-center group w-full">
            <div class="flex items-center w-full">
              <div
                class="flex items-center justify-center size-10 bg-green-100 rounded-full shrink-0 z-10 border-2 border-green-500"
              >
                <span
                  class="material-symbols-outlined text-green-600 text-xl font-bold"
                  >check</span
                >
              </div>
              <div class="flex-auto border-t-4 border-green-500"></div>
            </div>
            <div class="mt-3 text-center pr-6">
              <h4 class="text-sm font-bold text-green-700">Dospem</h4>
              <p class="text-xs text-text-secondary">Selesai</p>
            </div>
          </li>
          <!-- Step 3: Completed -->
          <li class="relative flex flex-col items-center group w-full">
            <div class="flex items-center w-full">
              <div
                class="flex items-center justify-center size-10 bg-green-100 rounded-full shrink-0 z-10 border-2 border-green-500"
              >
                <span
                  class="material-symbols-outlined text-green-600 text-xl font-bold"
                  >check</span
                >
              </div>
              <div class="flex-auto border-t-4 border-green-500"></div>
            </div>
            <div class="mt-3 text-center pr-6">
              <h4 class="text-sm font-bold text-green-700">SK Tugas</h4>
              <p class="text-xs text-text-secondary">Terbit</p>
            </div>
          </li>
          <!-- Step 4: Current (Sempro) -->
          <li class="relative flex flex-col items-center group w-full">
            <div class="flex items-center w-full">
              <div
                class="flex items-center justify-center size-10 bg-primary/10 rounded-full shrink-0 z-10 border-2 border-primary animate-pulse"
              >
                <span
                  class="material-symbols-outlined text-primary text-xl font-bold"
                  >pending</span
                >
              </div>
              <div class="flex-auto border-t-4 border-border-light"></div>
            </div>
            <div class="mt-3 text-center pr-6">
              <h4 class="text-sm font-bold text-primary">Sempro</h4>
              <p class="text-xs text-primary font-medium">In Progress</p>
            </div>
          </li>
          <!-- Step 5: Pending -->
          <li class="relative flex flex-col items-center group w-full">
            <div class="flex items-center w-full">
              <div
                class="flex items-center justify-center size-10 bg-sidebar-light rounded-full shrink-0 z-10 border-2 border-border-light"
              >
                <span class="text-text-secondary font-bold text-sm">5</span>
              </div>
              <div class="flex-auto border-t-4 border-border-light"></div>
            </div>
            <div class="mt-3 text-center pr-6">
              <h4 class="text-sm font-medium text-text-secondary">Bimbingan</h4>
            </div>
          </li>
          <!-- Step 6: Pending -->
          <li class="relative flex flex-col items-center group w-full">
            <div class="flex items-center w-full">
              <div
                class="flex items-center justify-center size-10 bg-sidebar-light rounded-full shrink-0 z-10 border-2 border-border-light"
              >
                <span class="text-text-secondary font-bold text-sm">6</span>
              </div>
              <div class="flex-auto border-t-4 border-border-light"></div>
            </div>
            <div class="mt-3 text-center pr-6">
              <h4 class="text-sm font-medium text-text-secondary">Ujian</h4>
            </div>
          </li>
          <!-- Step 7: Pending -->
          <li class="relative flex flex-col items-center group w-full">
            <div class="flex items-center w-full">
              <div
                class="flex items-center justify-center size-10 bg-sidebar-light rounded-full shrink-0 z-10 border-2 border-border-light"
              >
                <span class="text-text-secondary font-bold text-sm">7</span>
              </div>
              <div class="flex-auto border-t-4 border-border-light"></div>
            </div>
            <div class="mt-3 text-center pr-6">
              <h4 class="text-sm font-medium text-text-secondary">Revisi</h4>
            </div>
          </li>
          <!-- Step 8: Pending (Last) -->
          <li class="relative flex flex-col items-center group">
            <div class="flex items-center w-full">
              <div
                class="flex items-center justify-center size-10 bg-sidebar-light rounded-full shrink-0 z-10 border-2 border-border-light"
              >
                <span
                  class="material-symbols-outlined text-text-secondary text-xl"
                  >school</span
                >
              </div>
            </div>
            <div class="mt-3 text-center">
              <h4 class="text-sm font-medium text-text-secondary">Yudisium</h4>
            </div>
          </li>
        </ol>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Left Column: Informasi Umum -->
      <div class="lg:col-span-2 space-y-6">
        <div
          class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden"
        >
          <div
            class="px-6 py-4 border-b border-border-light flex justify-between items-center bg-sidebar-light"
          >
            <h3 class="font-bold text-text-main flex items-center gap-2">
              <span class="material-symbols-outlined text-primary">info</span>
              Informasi Umum
            </h3>
            <router-link
              to="profil"
              class="text-sm text-primary font-medium hover:underline"
            >
              Detail Lengkap
            </router-link>
          </div>
          <div class="p-6">
            <div class="grid gap-6">
              <div>
                <label
                  class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-1"
                  >Judul Skripsi</label
                >
                <p class="text-lg font-medium text-text-main leading-snug">
                  Implementasi Deep Learning Menggunakan Metode CNN untuk
                  Deteksi Penyakit Daun Tanaman Padi Berbasis Android
                </p>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div
                  class="p-4 bg-background-light rounded-lg border border-border-light"
                >
                  <label
                    class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-2"
                    >Dosen Pembimbing 1</label
                  >
                  <div class="flex items-center gap-3">
                    <div
                      class="size-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold"
                    >
                      AW
                    </div>
                    <div>
                      <p class="text-sm font-bold text-text-main">
                        Dr. Andi Wijaya, M.Kom
                      </p>
                      <p class="text-xs text-text-secondary">
                        NIP. 198501012010011001
                      </p>
                    </div>
                  </div>
                </div>
                <div
                  class="p-4 bg-background-light rounded-lg border border-border-light"
                >
                  <label
                    class="block text-xs font-semibold text-text-secondary uppercase tracking-wider mb-2"
                    >Dosen Pembimbing 2</label
                  >
                  <div class="flex items-center gap-3">
                    <div
                      class="size-10 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 font-bold"
                    >
                      SR
                    </div>
                    <div>
                      <p class="text-sm font-bold text-text-main">
                        Siti Rahmawati, S.T., M.T.
                      </p>
                      <p class="text-xs text-text-secondary">
                        NIP. 199002022015022002
                      </p>
                    </div>
                  </div>
                </div>
              </div>
              <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-2">
                <div>
                  <label class="block text-xs text-text-secondary mb-1"
                    >Tanggal Daftar</label
                  >
                  <p class="text-sm font-medium text-text-main">12 Jan 2023</p>
                </div>
                <div>
                  <label class="block text-xs text-text-secondary mb-1"
                    >Target Lulus</label
                  >
                  <p class="text-sm font-medium text-text-main">Des 2023</p>
                </div>
                <div>
                  <label class="block text-xs text-text-secondary mb-1"
                    >SKS Lulus</label
                  >
                  <p class="text-sm font-medium text-text-main">138 SKS</p>
                </div>
                <div>
                  <label class="block text-xs text-text-secondary mb-1"
                    >IPK Saat Ini</label
                  >
                  <p class="text-sm font-medium text-text-main">3.75</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column: Documents & Actions -->
      <div class="space-y-6">
        <!-- Quick Actions -->
        <div
          class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden"
        >
          <div class="px-6 py-4 border-b border-border-light bg-sidebar-light">
            <h3 class="font-bold text-text-main flex items-center gap-2">
              <span class="material-symbols-outlined text-primary">bolt</span>
              Quick Actions
            </h3>
          </div>
          <div class="p-6 flex flex-col gap-3">
            <button
              class="w-full bg-primary hover:bg-primary/90 text-white font-medium py-2.5 px-4 rounded-lg shadow-sm transition-all flex items-center justify-center gap-2"
            >
              <span class="material-symbols-outlined text-[20px]"
                >edit_note</span
              >
              Input Nilai Sempro
            </button>
            <button
              class="w-full bg-surface-light border border-border-light hover:bg-sidebar-light text-text-main font-medium py-2.5 px-4 rounded-lg transition-all flex items-center justify-center gap-2"
            >
              <span class="material-symbols-outlined text-[20px]">edit</span>
              Edit Data Mahasiswa
            </button>
            <button
              class="w-full bg-surface-light border border-border-light hover:bg-red-50 hover:text-red-600 hover:border-red-200 text-text-main font-medium py-2.5 px-4 rounded-lg transition-all flex items-center justify-center gap-2 mt-2"
            >
              <span class="material-symbols-outlined text-[20px]">warning</span>
              Laporkan Masalah
            </button>
          </div>
        </div>

        <!-- Dokumen Terbit -->
        <div
          class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden"
        >
          <div
            class="px-6 py-4 border-b border-border-light bg-sidebar-light flex justify-between items-center"
          >
            <h3 class="font-bold text-text-main flex items-center gap-2">
              <span class="material-symbols-outlined text-primary"
                >description</span
              >
              Dokumen Terbit
            </h3>
          </div>
          <div class="p-2">
            <ul class="flex flex-col">
              <li
                class="group flex items-center justify-between p-3 rounded-lg hover:bg-sidebar-light transition-colors cursor-pointer"
              >
                <div class="flex items-center gap-3">
                  <div class="bg-red-50 text-red-500 p-2 rounded-lg">
                    <span class="material-symbols-outlined"
                      >picture_as_pdf</span
                    >
                  </div>
                  <div class="flex flex-col">
                    <span
                      class="text-sm font-medium text-text-main group-hover:text-primary transition-colors"
                      >SK Pembimbing.pdf</span
                    >
                    <span class="text-xs text-text-secondary"
                      >14 Jan 2023 • 245 KB</span
                    >
                  </div>
                </div>
                <button
                  class="text-text-secondary hover:text-primary transition-colors p-2"
                >
                  <span class="material-symbols-outlined">download</span>
                </button>
              </li>
              <li
                class="group flex items-center justify-between p-3 rounded-lg hover:bg-sidebar-light transition-colors cursor-pointer"
              >
                <div class="flex items-center gap-3">
                  <div class="bg-red-50 text-red-500 p-2 rounded-lg">
                    <span class="material-symbols-outlined"
                      >picture_as_pdf</span
                    >
                  </div>
                  <div class="flex flex-col">
                    <span
                      class="text-sm font-medium text-text-main group-hover:text-primary transition-colors"
                      >Nota Dinas.pdf</span
                    >
                    <span class="text-xs text-text-secondary"
                      >20 Jan 2023 • 1.2 MB</span
                    >
                  </div>
                </div>
                <button
                  class="text-text-secondary hover:text-primary transition-colors p-2"
                >
                  <span class="material-symbols-outlined">download</span>
                </button>
              </li>
              <li
                class="group flex items-center justify-between p-3 rounded-lg hover:bg-sidebar-light transition-colors cursor-pointer"
              >
                <div class="flex items-center gap-3">
                  <div class="bg-blue-50 text-blue-500 p-2 rounded-lg">
                    <span class="material-symbols-outlined">article</span>
                  </div>
                  <div class="flex flex-col">
                    <span
                      class="text-sm font-medium text-text-main group-hover:text-primary transition-colors"
                      >Kartu Bimbingan.docx</span
                    >
                    <span class="text-xs text-text-secondary"
                      >22 Jan 2023 • 800 KB</span
                    >
                  </div>
                </div>
                <button
                  class="text-text-secondary hover:text-primary transition-colors p-2"
                >
                  <span class="material-symbols-outlined">download</span>
                </button>
              </li>
            </ul>
          </div>
          <div
            class="px-6 py-3 border-t border-border-light bg-sidebar-light text-center"
          >
            <router-link
              to="dokumen"
              class="text-xs font-semibold text-primary uppercase tracking-wide hover:underline"
            >
              Lihat Semua Dokumen
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

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
</style>
