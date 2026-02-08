<template>
  <div class="flex flex-col gap-6">
    <!-- Breadcrumbs -->
    <div class="flex flex-wrap items-center gap-2 text-sm">
      <router-link
        to="/mahasiswa/dashboard"
        class="text-text-secondary hover:text-primary font-medium"
        >Home</router-link
      >
      <span class="material-symbols-outlined text-text-secondary text-sm"
        >chevron_right</span
      >
      <span class="text-text-main font-bold">Jadwal Seminar Proposal</span>
    </div>

    <!-- Page Heading -->
    <div class="flex flex-col gap-1">
      <h1 class="text-3xl font-bold tracking-tight text-text-main">
        Jadwal Seminar Proposal
      </h1>
      <p class="text-text-secondary text-base">
        Pastikan Anda hadir 30 menit sebelum jadwal dimulai.
      </p>
    </div>

    <!-- Main Schedule Card -->
    <div
      class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden"
    >
      <div class="flex flex-col xl:flex-row w-full">
        <!-- Hero Image -->
        <div
          class="w-full xl:w-1/3 bg-cover bg-center min-h-[200px] xl:min-h-auto relative"
          style="
            background-image: url(&quot;https://lh3.googleusercontent.com/aida-public/AB6AXuC4NvgNHhl7gLN8eSDvYPxXBTozUV-ksvfWD5ornqZV0nHn_M1lYHSkGS4mVXkTgRC5n98UZDIWJxawZ7ulnHLKvu4cIkcC5tsKCrmsDroJ8HB5zVId9YFc6zTbH3ZhhmIfNEX_zhktEYL7LSNw6YcCmmXCB8ljXL6R92Hi9dbzrMfFJg0UVpEbSC48b4EGGrOiHnGUBYkCAxxT38SUYh04LaskV9BIr81SfivyqNBPslc86iiBP8Iu9vBXvpTdizbkQhfbKitdxKyY&quot;);
          "
        >
          <div
            class="absolute inset-0 bg-primary/20 backdrop-blur-[2px] flex items-center justify-center"
          >
            <div
              class="bg-white/90 dark:bg-surface-dark/90 p-4 rounded-xl text-center shadow-lg"
            >
              <p
                class="text-primary text-xs font-bold uppercase tracking-widest"
              >
                Oktober
              </p>
              <p class="text-text-main dark:text-white text-4xl font-black">
                24
              </p>
              <p class="text-text-secondary text-sm font-medium">2023</p>
            </div>
          </div>
        </div>

        <!-- Details -->
        <div class="flex-1 p-6 flex flex-col gap-6">
          <div class="flex justify-between items-center">
            <span
              class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-primary/10 text-primary uppercase tracking-wide"
            >
              <span class="size-2 rounded-full bg-primary"></span>
              Mendatang
            </span>
            <div class="text-text-secondary flex gap-2">
              <button class="hover:text-primary transition-colors">
                <span class="material-symbols-outlined">share</span>
              </button>
              <button class="hover:text-primary transition-colors">
                <span class="material-symbols-outlined">bookmark</span>
              </button>
            </div>
          </div>

          <div>
            <h2 class="text-2xl font-bold leading-tight text-text-main mb-2">
              Pengembangan Sistem Manajemen Skripsi Berbasis Cloud
            </h2>
            <p class="text-text-secondary italic">Seminar Proposal Skripsi</p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex items-start gap-3">
              <div
                class="flex items-center justify-center size-10 rounded-lg bg-gray-50 dark:bg-gray-800 text-primary"
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
                class="flex items-center justify-center size-10 rounded-lg bg-gray-50 dark:bg-gray-800 text-primary"
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

          <div
            class="flex flex-wrap gap-3 pt-4 border-t border-border-light dark:border-gray-700 mt-auto"
          >
            <button
              class="flex-1 min-w-[160px] flex items-center justify-center gap-2 rounded-lg h-11 px-4 bg-primary text-white font-bold hover:bg-blue-600 transition-colors shadow-sm"
            >
              <span class="material-symbols-outlined">download</span>
              Download Undangan
            </button>
            <button
              class="flex items-center justify-center gap-2 rounded-lg h-11 px-4 bg-gray-100 dark:bg-gray-800 text-text-main dark:text-gray-200 font-bold hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors"
            >
              <span class="material-symbols-outlined">description</span>
              Lihat Draft
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Tim Penguji -->
    <div>
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-bold text-text-main">
          Tim Penguji & Pembimbing
        </h2>
        <span
          class="text-xs font-bold bg-primary/10 text-primary px-2 py-1 rounded"
          >3 Personil</span
        >
      </div>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Penguji 1 -->
        <div
          class="bg-surface-light p-4 rounded-xl border border-border-light shadow-sm flex flex-col gap-3"
        >
          <div class="flex items-center gap-3">
            <div
              class="size-12 rounded-lg bg-gray-200 bg-cover bg-center"
              style="
                background-image: url(&quot;https://lh3.googleusercontent.com/aida-public/AB6AXuC8p8p7HnlOZOyFxSAElQkuuYc77gMUZHypT2QTAP-9ZDigZu4kc2X2YO4UJKus9LRTf3l_Ig7tlKNZmfoyVWMbKYwD7ZG_LOVxo3V-BAIaUemGAQhLoasa7wuXGtVd5qeFfnRXLf0PSpwKBxtegTMSOf2ZDTYj2FXz0aXXxCyMcZuHOS41wWxdpGTSAO8x3Z1r42bDaGDoCiBrcAR4E8h3kMv7XbMZ5cduJ1fDC5nikI11NENZmZP2FjAP4pIorUVnugECMeU0IdpI&quot;);
              "
            ></div>
            <div>
              <p class="font-bold text-sm text-text-main line-clamp-1">
                Dr. Aris Sudarsono
              </p>
              <p class="text-xs text-text-secondary">Ketua Penguji</p>
            </div>
          </div>
          <div class="mt-auto">
            <span
              class="text-[10px] font-bold bg-gray-100 dark:bg-gray-800 text-text-secondary px-2 py-1 rounded"
              >NIP: 19820310...</span
            >
          </div>
        </div>
        <!-- Penguji 2 -->
        <div
          class="bg-surface-light p-4 rounded-xl border border-border-light shadow-sm flex flex-col gap-3"
        >
          <div class="flex items-center gap-3">
            <div
              class="size-12 rounded-lg bg-gray-200 bg-cover bg-center"
              style="
                background-image: url(&quot;https://lh3.googleusercontent.com/aida-public/AB6AXuA8mjlEoPOMtIROyhlBHGL0ZLbr5zFptw5fta66VcxrvhkoMKp_nPGRetsMHvHk1v9yuoNu-z5NHVOLjRkCEutPyrqQATcFQ8HvkMCfZZ6Eolt9UdbxjRxd57JLFWE3j93x21AsPvp-W6EUtIVZ44rrXg-wdG9klkVYW6gObR5-SuiJLZ130Ts-jfvAfzicfBQdh2CwJ8Ks-LHQC249cbITEISa3qily3tEN6ZWwv4QKvp-FPdEo3t1sRwZGDRNmWSOsGSGDC0jLNrH&quot;);
              "
            ></div>
            <div>
              <p class="font-bold text-sm text-text-main line-clamp-1">
                Prof. Budi Raharjo
              </p>
              <p class="text-xs text-text-secondary">Anggota Penguji</p>
            </div>
          </div>
          <div class="mt-auto">
            <span
              class="text-[10px] font-bold bg-gray-100 dark:bg-gray-800 text-text-secondary px-2 py-1 rounded"
              >NIP: 19750512...</span
            >
          </div>
        </div>
        <!-- Pembimbing -->
        <div
          class="bg-surface-light p-4 rounded-xl border border-border-light shadow-sm flex flex-col gap-3"
        >
          <div class="flex items-center gap-3">
            <div
              class="size-12 rounded-lg bg-gray-200 bg-cover bg-center"
              style="
                background-image: url(&quot;https://lh3.googleusercontent.com/aida-public/AB6AXuBfZ7rozkpecUxSnTEZ33ERUGgqcxe98k1vI370MWplhfvIAMPeoRbes3UFnrLQSu2bPUYQQ2b90ALWhrPTMaY6U9t7_CF-BiI8LR_NNJSzwi59booAwv2sadBvbHMfOoaRAKXsvS62DpfK9ckC886k_5l1-zacyaClo6qqsS9Boj_u1dmOs_vQWEDE-j9nUZ3YdJiPzUh_B3yYUfZ0NIEPjCtBd4_ncsOELzEH-uDgT8zFzTJHABsXh1rIk-5_m-_wL7C8GQRCrsbI&quot;);
              "
            ></div>
            <div>
              <p class="font-bold text-sm text-text-main line-clamp-1">
                Sarah Melati, M.T.
              </p>
              <p class="text-xs text-text-secondary">Dosen Pembimbing</p>
            </div>
          </div>
          <div class="mt-auto">
            <span
              class="text-[10px] font-bold bg-gray-100 dark:bg-gray-800 text-text-secondary px-2 py-1 rounded"
              >NIP: 19900824...</span
            >
          </div>
        </div>
      </div>
    </div>

    <!-- Important Info -->
    <div
      class="bg-primary/5 border border-primary/20 rounded-xl p-5 flex gap-4"
    >
      <div class="text-primary shrink-0">
        <span class="material-symbols-outlined text-4xl">info</span>
      </div>
      <div class="flex flex-col gap-1">
        <h4 class="font-bold text-text-main text-sm">Catatan Penting</h4>
        <ul class="list-disc pl-4 text-xs text-text-secondary space-y-1">
          <li>Pakaian: Kemeja Putih, Celana/Rok Hitam, Jas Almamater.</li>
          <li>
            Bawa minimal 3 rangkap draft proposal yang telah disetujui
            pembimbing.
          </li>
          <li>
            Materi presentasi disiapkan dalam flashdisk (Maksimal 10 menit).
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script setup>
// Script logic if needed
</script>
