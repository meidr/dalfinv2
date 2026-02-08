<template>
  <div class="flex flex-col gap-8 animate-fade-in">
    <!-- Section: Upload Dokumen -->
    <div
      class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden"
    >
      <div
        class="px-6 py-4 border-b border-border-light bg-sidebar-light flex justify-between items-center"
      >
        <div>
          <h3 class="font-bold text-lg">Upload Dokumen</h3>
          <p class="text-xs text-text-secondary">
            Unggah berkas persyaratan skripsi Anda di sini.
          </p>
        </div>
      </div>
      <div class="p-6 flex flex-col gap-6">
        <!-- 1. Proposal -->
        <div
          class="flex flex-col md:flex-row gap-4 p-4 border border-border-light rounded-xl hover:border-primary/50 transition-colors"
        >
          <div
            class="size-12 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center shrink-0"
          >
            <span class="material-symbols-outlined">description</span>
          </div>
          <div class="flex-1 flex flex-col gap-2">
            <div class="flex justify-between items-start">
              <div>
                <h4 class="font-bold text-sm text-text-main">
                  Proposal Skripsi
                </h4>
                <p class="text-xs text-text-secondary">Format PDF, Maks. 2MB</p>
              </div>
              <span
                class="px-2 py-0.5 rounded text-[10px] font-bold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400"
              >
                Disetujui
              </span>
            </div>
            <!-- File Input (Disabled/View Mode if approved) -->
            <div class="flex items-center gap-2 mt-1">
              <div
                class="flex-1 px-3 py-2 bg-gray-50 dark:bg-gray-800 rounded-lg border border-border-light text-sm text-text-secondary flex items-center gap-2"
              >
                <span class="material-symbols-outlined text-red-500 text-sm"
                  >picture_as_pdf</span
                >
                <span class="truncate">proposal_budi_vinal.pdf</span>
              </div>
              <button class="text-primary hover:text-blue-600 p-2">
                <span class="material-symbols-outlined">visibility</span>
              </button>
            </div>
          </div>
        </div>

        <!-- 2. Skripsi (Full Draft) -->
        <div
          class="flex flex-col md:flex-row gap-4 p-4 border border-border-light rounded-xl hover:border-primary/50 transition-colors"
        >
          <div
            class="size-12 bg-purple-50 text-purple-600 rounded-lg flex items-center justify-center shrink-0"
          >
            <span class="material-symbols-outlined">book</span>
          </div>
          <div class="flex-1 flex flex-col gap-3">
            <div class="flex justify-between items-start">
              <div>
                <h4 class="font-bold text-sm text-text-main">Draft Skripsi</h4>
                <p class="text-xs text-text-secondary">
                  Upload draft skripsi lengkap atau per bab.
                </p>
              </div>
              <span
                class="px-2 py-0.5 rounded text-[10px] font-bold bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400"
              >
                Menunggu Review
              </span>
            </div>

            <!-- Keterangan Input -->
            <div>
              <label
                class="text-[10px] font-bold text-text-secondary uppercase tracking-wider mb-1 block"
                >Keterangan</label
              >
              <input
                type="text"
                placeholder="Contoh: Bab 1 - Bab 3"
                class="w-full px-3 py-2 text-sm rounded-lg border border-border-light bg-background-light focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all"
              />
            </div>

            <!-- File Input -->
            <div class="relative">
              <input type="file" class="hidden" id="upload-skripsi" />
              <label
                for="upload-skripsi"
                class="flex items-center justify-center gap-2 w-full px-4 py-8 border-2 border-dashed border-border-light rounded-lg cursor-pointer hover:border-primary/50 hover:bg-primary/5 transition-all group"
              >
                <div
                  class="flex flex-col items-center gap-1 text-text-secondary group-hover:text-primary"
                >
                  <span class="material-symbols-outlined text-3xl"
                    >cloud_upload</span
                  >
                  <span class="text-xs font-bold">Klik untuk upload file</span>
                </div>
              </label>
            </div>

            <!-- Riwayat Upload (Draft) -->
            <div class="mt-2">
              <p
                class="text-[10px] font-bold text-text-secondary uppercase tracking-wider mb-2"
              >
                Riwayat Draft
              </p>
              <div class="flex flex-col gap-2">
                <!-- Item 1 -->
                <div
                  class="flex items-center gap-3 p-2 bg-background-light rounded-lg border border-border-light hover:bg-sidebar-light transition-colors group/item"
                >
                  <div
                    class="p-1.5 bg-white dark:bg-gray-700 rounded border border-border-light text-purple-600"
                  >
                    <span class="material-symbols-outlined text-[16px]"
                      >book</span
                    >
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-text-main truncate">
                      Draft_Bab_1.pdf
                    </p>
                    <p class="text-[10px] text-text-secondary">
                      bab 1 pendahuluan
                    </p>
                  </div>
                  <div class="flex items-center gap-2">
                    <span class="text-[10px] text-text-secondary"
                      >10 Sep 2023</span
                    >
                    <button
                      class="text-red-500 opacity-0 group-hover/item:opacity-100 transition-opacity p-1 hover:bg-red-50 rounded"
                    >
                      <span class="material-symbols-outlined text-[16px]"
                        >delete</span
                      >
                    </button>
                  </div>
                </div>
                <!-- Item 2 -->
                <div
                  class="flex items-center gap-3 p-2 bg-background-light rounded-lg border border-border-light hover:bg-sidebar-light transition-colors group/item"
                >
                  <div
                    class="p-1.5 bg-white dark:bg-gray-700 rounded border border-border-light text-purple-600"
                  >
                    <span class="material-symbols-outlined text-[16px]"
                      >book</span
                    >
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-text-main truncate">
                      Draft_Bab_2_3.pdf
                    </p>
                    <p class="text-[10px] text-text-secondary">
                      lanjut bab 2 dan 3
                    </p>
                  </div>
                  <div class="flex items-center gap-2">
                    <span class="text-[10px] text-text-secondary"
                      >25 Sep 2023</span
                    >
                    <button
                      class="text-red-500 opacity-0 group-hover/item:opacity-100 transition-opacity p-1 hover:bg-red-50 rounded"
                    >
                      <span class="material-symbols-outlined text-[16px]"
                        >delete</span
                      >
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- 3. Revisi Skripsi -->
        <div
          class="flex flex-col md:flex-row gap-4 p-4 border border-border-light rounded-xl hover:border-primary/50 transition-colors bg-surface-light group"
        >
          <div
            class="size-12 bg-orange-50 text-orange-600 rounded-lg flex items-center justify-center shrink-0 group-hover:bg-orange-600 group-hover:text-white transition-colors"
          >
            <span class="material-symbols-outlined">rate_review</span>
          </div>
          <div class="flex-1 flex flex-col gap-3">
            <div class="flex justify-between items-start">
              <div>
                <h4 class="font-bold text-sm text-text-main">Revisi Skripsi</h4>
                <p class="text-xs text-text-secondary">
                  Upload hasil perbaikan setelah sidang.
                </p>
              </div>
              <span
                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-600 border border-blue-100 dark:bg-blue-900/30 dark:text-blue-400"
              >
                <span
                  class="size-1.5 rounded-full bg-blue-600 animate-pulse"
                ></span>
                Silakan Upload
              </span>
            </div>
            <!-- Inputs Enabled -->
            <div>
              <label
                class="text-[10px] font-bold text-text-secondary uppercase tracking-wider mb-1 block"
                >Catatan Revisi</label
              >
              <textarea
                rows="2"
                placeholder="Jelaskan bagian yang direvisi..."
                class="w-full px-3 py-2 text-sm rounded-lg border border-border-light bg-background-light focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all resize-none"
              ></textarea>
              <!-- File Input -->
              <div class="relative mt-3">
                <input type="file" class="hidden" id="upload-revisi" />
                <label
                  for="upload-revisi"
                  class="flex items-center justify-center gap-2 w-full px-4 py-8 border-2 border-dashed border-border-light rounded-lg cursor-pointer hover:border-primary/50 hover:bg-primary/5 transition-all group/upload"
                >
                  <div
                    class="flex flex-col items-center gap-1 text-text-secondary group-hover/upload:text-primary transition-colors"
                  >
                    <span class="material-symbols-outlined text-3xl"
                      >cloud_upload</span
                    >
                    <span class="text-xs font-bold"
                      >Klik untuk upload file revisi</span
                    >
                  </div>
                </label>
              </div>

              <!-- Riwayat Upload (Revisi) -->
              <div class="mt-4">
                <p
                  class="text-[10px] font-bold text-text-secondary uppercase tracking-wider mb-2"
                >
                  Riwayat Revisi
                </p>
                <div class="flex flex-col gap-2">
                  <!-- Item 1 -->
                  <div
                    class="flex items-center gap-3 p-2 bg-background-light rounded-lg border border-border-light hover:bg-sidebar-light transition-colors group/item"
                  >
                    <div
                      class="p-1.5 bg-white dark:bg-gray-700 rounded border border-border-light text-orange-600"
                    >
                      <span class="material-symbols-outlined text-[16px]"
                        >rate_review</span
                      >
                    </div>
                    <div class="flex-1 min-w-0">
                      <p class="text-xs font-bold text-text-main truncate">
                        Revisi_Sidang_1.pdf
                      </p>
                      <p class="text-[10px] text-text-secondary">
                        perbaikan bab 4 sesuai catatan penguji 1
                      </p>
                    </div>
                    <div class="flex items-center gap-2">
                      <span class="text-[10px] text-text-secondary"
                        >15 Okt 2023</span
                      >
                      <button
                        class="text-red-500 opacity-0 group-hover/item:opacity-100 transition-opacity p-1 hover:bg-red-50 rounded"
                      >
                        <span class="material-symbols-outlined text-[16px]"
                          >delete</span
                        >
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- 4. Final Skripsi -->
        <div
          class="flex flex-col md:flex-row gap-4 p-4 border border-border-light rounded-xl hover:border-primary/50 transition-colors bg-surface-light group"
        >
          <div
            class="size-12 bg-green-50 text-green-600 rounded-lg flex items-center justify-center shrink-0 group-hover:bg-green-600 group-hover:text-white transition-colors"
          >
            <span class="material-symbols-outlined">task</span>
          </div>
          <div class="flex-1 flex flex-col gap-3">
            <div class="flex justify-between items-start">
              <div>
                <h4 class="font-bold text-sm text-text-main">Final Skripsi</h4>
                <p class="text-xs text-text-secondary">
                  Dokumen skripsi final yang telah disahkan (Hardcover).
                </p>
              </div>
              <span
                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-600 border border-blue-100 dark:bg-blue-900/30 dark:text-blue-400"
              >
                <span
                  class="size-1.5 rounded-full bg-blue-600 animate-pulse"
                ></span>
                Silakan Upload
              </span>
            </div>

            <!-- File Input -->
            <div class="relative">
              <input type="file" class="hidden" id="upload-final" />
              <label
                for="upload-final"
                class="flex items-center justify-center gap-2 w-full px-4 py-8 border-2 border-dashed border-border-light rounded-lg cursor-pointer hover:border-primary/50 hover:bg-primary/5 transition-all group/upload"
              >
                <div
                  class="flex flex-col items-center gap-1 text-text-secondary group-hover/upload:text-primary transition-colors"
                >
                  <span class="material-symbols-outlined text-3xl"
                    >cloud_upload</span
                  >
                  <span class="text-xs font-bold">Klik untuk upload file</span>
                </div>
              </label>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Section: Dokumen Resmi (Downloads) -->
    <div
      class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden"
    >
      <div class="px-6 py-4 border-b border-border-light bg-sidebar-light">
        <h3 class="font-bold text-lg">Dokumen Resmi</h3>
        <p class="text-xs text-text-secondary">
          Unduh surat keputusan dan dokumen resmi lainnya.
        </p>
      </div>
      <div class="p-2 flex flex-col gap-2">
        <div
          class="flex items-center justify-between p-4 hover:bg-sidebar-light rounded-lg transition-colors group"
        >
          <div class="flex items-center gap-4">
            <div
              class="size-10 bg-red-50 text-red-500 rounded-lg flex items-center justify-center"
            >
              <span class="material-symbols-outlined">picture_as_pdf</span>
            </div>
            <div>
              <h4
                class="font-bold text-sm text-text-main dark:text-white group-hover:text-primary transition-colors"
              >
                SK Pembimbing Skripsi
              </h4>
              <p class="text-xs text-text-secondary">
                Diterbitkan 25 Agustus 2023
              </p>
            </div>
          </div>
          <button
            class="flex items-center gap-2 px-3 py-1.5 border border-border-light rounded-lg text-xs font-bold hover:bg-primary hover:text-white hover:border-primary transition-all"
          >
            <span class="material-symbols-outlined text-[16px]">download</span>
            Unduh
          </button>
        </div>

        <div
          class="flex items-center justify-between p-4 hover:bg-sidebar-light rounded-lg transition-colors group"
        >
          <div class="flex items-center gap-4">
            <div
              class="size-10 bg-red-50 text-red-500 rounded-lg flex items-center justify-center"
            >
              <span class="material-symbols-outlined">picture_as_pdf</span>
            </div>
            <div>
              <h4
                class="font-bold text-sm text-text-main dark:text-white group-hover:text-primary transition-colors"
              >
                Berita Acara Seminar Proposal
              </h4>
              <p class="text-xs text-text-secondary">
                Diterbitkan 15 Oktober 2023
              </p>
            </div>
          </div>
          <button
            class="flex items-center gap-2 px-3 py-1.5 border border-border-light rounded-lg text-xs font-bold hover:bg-primary hover:text-white hover:border-primary transition-all"
          >
            <span class="material-symbols-outlined text-[16px]">download</span>
            Unduh
          </button>
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
