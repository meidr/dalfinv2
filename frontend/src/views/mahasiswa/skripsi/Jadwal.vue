<template>
  <div class="flex flex-col gap-8 animate-fade-in">
    <!-- Seminar Proposal Section -->
    <div>
      <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
        <span class="material-symbols-outlined text-primary">event</span>
        Jadwal Seminar Proposal
      </h3>
      <div
        class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden"
      >
        <div class="flex flex-col xl:flex-row w-full">
          <!-- Hero Image (Optional or simplified) -->
          <div
            class="w-full xl:w-1/4 bg-cover bg-center min-h-[150px] xl:min-h-auto relative hidden md:block"
            style="
              background-image: url(&quot;https://lh3.googleusercontent.com/aida-public/AB6AXuC4NvgNHhl7gLN8eSDvYPxXBTozUV-ksvfWD5ornqZV0nHn_M1lYHSkGS4mVXkTgRC5n98UZDIWJxawZ7ulnHLKvu4cIkcC5tsKCrmsDroJ8HB5zVId9YFc6zTbH3ZhhmIfNEX_zhktEYL7LSNw6YcCmmXCB8ljXL6R92Hi9dbzrMfFJg0UVpEbSC48b4EGGrOiHnGUBYkCAxxT38SUYh04LaskV9BIr81SfivyqNBPslc86iiBP8Iu9vBXvpTdizbkQhfbKitdxKyY&quot;);
            "
          >
            <div
              class="absolute inset-0 bg-primary/20 backdrop-blur-[1px] flex items-center justify-center"
            >
              <div
                class="bg-white/90 dark:bg-surface-dark/90 p-3 rounded-lg text-center shadow-md"
              >
                <p
                  class="text-primary text-[10px] font-bold uppercase tracking-widest"
                >
                  Oktober
                </p>
                <p class="text-text-main dark:text-white text-2xl font-black">
                  24
                </p>
                <p class="text-text-secondary text-xs font-medium">2023</p>
              </div>
            </div>
          </div>

          <!-- Details -->
          <div class="flex-1 p-6 flex flex-col gap-4">
            <div class="flex justify-between items-center">
              <span
                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400"
              >
                <span class="material-symbols-outlined text-[14px]"
                  >check_circle</span
                >
                Selesai
              </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="flex items-start gap-3">
                <div
                  class="flex items-center justify-center size-10 rounded-lg bg-sidebar-light text-primary"
                >
                  <span class="material-symbols-outlined">schedule</span>
                </div>
                <div>
                  <p class="text-xs text-text-secondary font-medium uppercase">
                    Waktu
                  </p>
                  <p class="font-semibold text-text-main">09:00 - 10:30 WIB</p>
                </div>
              </div>
              <div class="flex items-start gap-3">
                <div
                  class="flex items-center justify-center size-10 rounded-lg bg-sidebar-light text-primary"
                >
                  <span class="material-symbols-outlined">location_on</span>
                </div>
                <div>
                  <p class="text-xs text-text-secondary font-medium uppercase">
                    Lokasi
                  </p>
                  <p class="font-semibold text-text-main">
                    Ruang Sidang 1, Gedung A
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Ujian Skripsi Section -->
    <div>
      <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
        <span class="material-symbols-outlined text-primary">school</span>
        Jadwal Ujian Skripsi
      </h3>
      <div
        class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden p-6"
      >
        <div class="flex flex-col lg:flex-row gap-8">
          <div class="flex-1">
            <div class="flex items-center gap-3 mb-4">
              <span
                class="px-3 py-1 bg-blue-100 text-primary dark:bg-blue-900/30 dark:text-blue-300 text-xs font-bold rounded-full uppercase tracking-wider"
                >Terjadwal</span
              >
              <span class="text-text-secondary">•</span>
              <span class="text-sm text-text-secondary">Sidang Akhir</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-12">
              <div class="flex items-start gap-3">
                <div class="p-2 bg-sidebar-light rounded-lg text-primary">
                  <span class="material-symbols-outlined">calendar_today</span>
                </div>
                <div>
                  <p
                    class="text-xs text-text-secondary uppercase font-bold tracking-wide"
                  >
                    Tanggal
                  </p>
                  <p class="font-semibold text-text-main">Senin, 25 Nov 2024</p>
                </div>
              </div>
              <div class="flex items-start gap-3">
                <div class="p-2 bg-sidebar-light rounded-lg text-primary">
                  <span class="material-symbols-outlined">schedule</span>
                </div>
                <div>
                  <p
                    class="text-xs text-text-secondary uppercase font-bold tracking-wide"
                  >
                    Waktu
                  </p>
                  <p class="font-semibold text-text-main">10:00 - 12:00 WIB</p>
                </div>
              </div>
              <div class="flex items-start gap-3">
                <div class="p-2 bg-sidebar-light rounded-lg text-primary">
                  <span class="material-symbols-outlined">location_on</span>
                </div>
                <div>
                  <p
                    class="text-xs text-text-secondary uppercase font-bold tracking-wide"
                  >
                    Ruangan
                  </p>
                  <p class="font-semibold text-text-main">Ruang Sidang Utama</p>
                </div>
              </div>
              <!-- Penguji Info could go here -->
            </div>
          </div>
        </div>

        <!-- Tim Penguji (Simplified) -->
        <div class="mt-8 pt-6 border-t border-border-light">
          <h4 class="font-bold text-sm text-text-main mb-4">Dewan Penguji</h4>
          <div class="flex flex-wrap gap-4">
            <div
              class="flex items-center gap-3 bg-sidebar-light px-4 py-2 rounded-lg border border-border-light"
            >
              <div
                class="size-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs"
              >
                KP
              </div>
              <div>
                <p class="text-xs font-bold text-text-main">
                  Prof. Dr. Ahmad Dahlan
                </p>
                <p class="text-[10px] text-text-secondary">Ketua Penguji</p>
              </div>
            </div>
            <div
              class="flex items-center gap-3 bg-sidebar-light px-4 py-2 rounded-lg border border-border-light"
            >
              <div
                class="size-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs"
              >
                SP
              </div>
              <div>
                <p class="text-xs font-bold text-text-main">
                  Siti Aminah, M.Cs.
                </p>
                <p class="text-[10px] text-text-secondary">Sekretaris</p>
              </div>
            </div>
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
