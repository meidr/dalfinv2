<template>
  <div class="max-w-7xl mx-auto flex flex-col gap-8">
    <div class="flex flex-col gap-1 animate-fade-in-up">
      <h1 class="text-text-main text-3xl font-bold leading-tight">
        Selamat Datang, Admin
      </h1>
      <p class="text-text-secondary text-sm font-normal">
        Overview status skripsi mahasiswa &amp; aktivitas terbaru hari ini.
      </p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div
        class="flex flex-col gap-2 rounded-xl p-6 bg-surface-light border border-border-light shadow-sm hover:shadow-md transition-all hover:-translate-y-1 duration-300 cursor-pointer group"
      >
        <div class="flex items-center justify-between">
          <p
            class="text-text-secondary text-xs font-bold uppercase tracking-wider"
          >
            Total Skripsi
          </p>
          <div
            class="bg-primary/10 p-2 rounded-lg text-primary group-hover:bg-primary group-hover:text-white transition-colors"
          >
            <span class="material-symbols-outlined">library_books</span>
          </div>
        </div>
        <div class="mt-2">
          <p class="text-text-main text-3xl font-bold leading-tight">1,240</p>
          <div class="flex items-center gap-1 mt-1">
            <span class="material-symbols-outlined text-green-600 text-[18px]"
              >trending_up</span
            >
            <p class="text-green-600 text-xs font-semibold">
              +5% from last month
            </p>
          </div>
        </div>
      </div>
      <div
        class="flex flex-col gap-2 rounded-xl p-6 bg-surface-light border border-border-light shadow-sm hover:shadow-md transition-all hover:-translate-y-1 duration-300 cursor-pointer group"
      >
        <div class="flex items-center justify-between">
          <p
            class="text-text-secondary text-xs font-bold uppercase tracking-wider"
          >
            Proposal Aktif
          </p>
          <div
            class="bg-primary/10 p-2 rounded-lg text-primary group-hover:bg-primary group-hover:text-white transition-colors"
          >
            <span class="material-symbols-outlined">description</span>
          </div>
        </div>
        <div class="mt-2">
          <p class="text-text-main text-3xl font-bold leading-tight">45</p>
          <div class="flex items-center gap-1 mt-1">
            <span class="material-symbols-outlined text-green-600 text-[18px]"
              >trending_up</span
            >
            <p class="text-green-600 text-xs font-semibold">
              +12% new submissions
            </p>
          </div>
        </div>
      </div>
      <div
        class="flex flex-col gap-2 rounded-xl p-6 bg-surface-light border border-border-light shadow-sm hover:shadow-md transition-all hover:-translate-y-1 duration-300 cursor-pointer group"
      >
        <div class="flex items-center justify-between">
          <p
            class="text-text-secondary text-xs font-bold uppercase tracking-wider"
          >
            Menunggu SK
          </p>
          <div
            class="bg-orange-100 p-2 rounded-lg text-orange-600 group-hover:bg-orange-500 group-hover:text-white transition-colors"
          >
            <span class="material-symbols-outlined">pending_actions</span>
          </div>
        </div>
        <div class="mt-2">
          <p class="text-text-main text-3xl font-bold leading-tight">12</p>
          <div class="flex items-center gap-1 mt-1">
            <span class="material-symbols-outlined text-orange-600 text-[18px]"
              >priority_high</span
            >
            <p class="text-orange-600 text-xs font-semibold">
              Requires attention
            </p>
          </div>
        </div>
      </div>
    </div>
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
      <div
        class="xl:col-span-1 flex flex-col rounded-xl border border-border-light bg-surface-light overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300"
      >
        <div
          class="p-6 border-b border-border-light flex justify-between items-center bg-sidebar-light/50"
        >
          <div>
            <h3 class="text-text-main text-lg font-bold">Distribusi Status</h3>
            <p class="text-text-secondary text-xs">Mahasiswa semester ini</p>
          </div>
          <button
            class="text-text-secondary hover:text-primary transition-colors p-1 rounded-full hover:bg-gray-100"
          >
            <span class="material-symbols-outlined text-[20px]">more_vert</span>
          </button>
        </div>
        <div class="flex-1 p-8 flex items-end justify-center min-h-[300px]">
          <div class="flex w-full h-48 items-end justify-between gap-3">
            <div
              class="flex flex-col items-center gap-3 flex-1 group cursor-pointer hover:-translate-y-2 transition-transform duration-300"
            >
              <div
                class="w-full bg-primary/20 rounded-t-md h-[60%] relative group-hover:bg-primary transition-all duration-300 overflow-hidden"
              >
                <div
                  class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-500"
                ></div>
                <div
                  class="absolute -top-8 left-1/2 -translate-x-1/2 bg-text-main text-white text-[10px] font-bold px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity translate-y-2 group-hover:translate-y-0"
                >
                  150
                </div>
              </div>
              <p
                class="text-[10px] text-text-secondary font-bold group-hover:text-primary transition-colors"
              >
                Prop
              </p>
            </div>
            <div
              class="flex flex-col items-center gap-3 flex-1 group cursor-pointer hover:-translate-y-2 transition-transform duration-300 delay-75"
            >
              <div
                class="w-full bg-primary/20 rounded-t-md h-[30%] relative group-hover:bg-primary transition-all duration-300"
              >
                <div
                  class="absolute -top-8 left-1/2 -translate-x-1/2 bg-text-main text-white text-[10px] font-bold px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity translate-y-2 group-hover:translate-y-0"
                >
                  75
                </div>
              </div>
              <p
                class="text-[10px] text-text-secondary font-bold group-hover:text-primary transition-colors"
              >
                Sem
              </p>
            </div>
            <div
              class="flex flex-col items-center gap-3 flex-1 group cursor-pointer hover:-translate-y-2 transition-transform duration-300 delay-100"
            >
              <div
                class="w-full bg-primary rounded-t-md h-[80%] relative transition-all duration-300 shadow-md shadow-primary/20"
              >
                <div
                  class="absolute -top-8 left-1/2 -translate-x-1/2 bg-text-main text-white text-[10px] font-bold px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity translate-y-2 group-hover:translate-y-0"
                >
                  200
                </div>
              </div>
              <p
                class="text-[10px] text-text-secondary font-bold group-hover:text-primary transition-colors"
              >
                Bim
              </p>
            </div>
            <div
              class="flex flex-col items-center gap-3 flex-1 group cursor-pointer hover:-translate-y-2 transition-transform duration-300 delay-150"
            >
              <div
                class="w-full bg-primary/20 rounded-t-md h-[40%] relative group-hover:bg-primary transition-all duration-300"
              >
                <div
                  class="absolute -top-8 left-1/2 -translate-x-1/2 bg-text-main text-white text-[10px] font-bold px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity translate-y-2 group-hover:translate-y-0"
                >
                  90
                </div>
              </div>
              <p
                class="text-[10px] text-text-secondary font-bold group-hover:text-primary transition-colors"
              >
                Uji
              </p>
            </div>
            <div
              class="flex flex-col items-center gap-3 flex-1 group cursor-pointer hover:-translate-y-2 transition-transform duration-300 delay-200"
            >
              <div
                class="w-full bg-green-500/20 rounded-t-md h-[20%] relative group-hover:bg-green-500 transition-all duration-300"
              >
                <div
                  class="absolute -top-8 left-1/2 -translate-x-1/2 bg-text-main text-white text-[10px] font-bold px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity translate-y-2 group-hover:translate-y-0"
                >
                  45
                </div>
              </div>
              <p
                class="text-[10px] text-text-secondary font-bold group-hover:text-green-500 transition-colors"
              >
                Lulus
              </p>
            </div>
          </div>
        </div>
      </div>
      <div
        class="xl:col-span-2 flex flex-col rounded-xl border border-border-light bg-surface-light overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300"
      >
        <div
          class="p-6 border-b border-border-light flex flex-wrap gap-4 justify-between items-center bg-sidebar-light/50"
        >
          <div>
            <h3 class="text-text-main text-lg font-bold">Perlu Diproses</h3>
            <p class="text-text-secondary text-xs">
              Mahasiswa yang baru mengubah status alur.
            </p>
          </div>
          <button
            class="text-primary text-sm font-bold hover:bg-primary/5 px-4 py-2 rounded-lg transition-colors border border-primary/20 hover:border-primary focus:ring-2 focus:ring-primary/20"
          >
            View All
          </button>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-sidebar-light/50 border-b border-border-light">
                <th
                  class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase"
                >
                  Mahasiswa
                </th>
                <th
                  class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase"
                >
                  Status Sekarang
                </th>
                <th
                  class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase"
                >
                  Tanggal Submit
                </th>
                <th
                  class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase text-right"
                >
                  Aksi
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-border-light">
              <tr class="group hover:bg-sidebar-light/30 transition-colors">
                <td class="p-4">
                  <div class="flex items-center gap-3">
                    <div
                      class="bg-gray-200 size-10 rounded-full bg-cover bg-center border border-border-light shadow-sm transition-transform group-hover:scale-105"
                      data-alt="Student Portrait 1"
                      style="
                        background-image: url(&quot;https://lh3.googleusercontent.com/aida-public/AB6AXuCXvmzpNr4yS293bcSUOqs1k0_0T3xBHHwPvluP2og08U-Pmekxd-lNCbHkg4l_ebu9pQp84cfHsau6-I-Loo86_6lN4k7FAYpMXo2M8gLPbEmwMhxkImXktkSZuWnkqjuWbqooz7ty-6u73H6Ww1mo00MhCdgeUOEIo9ciWJAM13shkp7T-5f66DTAYns5Co-T2rBySywlsV7JMpyqqLNvqISxOJQ8Z_kdUpsLms_CGCqTmOhzaZq9rjsu2GBl23JEAP9HMC838l60&quot;);
                      "
                    ></div>
                    <div>
                      <p
                        class="font-bold text-text-main text-sm group-hover:text-primary transition-colors"
                      >
                        Budi Santoso
                      </p>
                      <p class="text-xs text-text-secondary font-medium">
                        201910234
                      </p>
                    </div>
                  </div>
                </td>
                <td class="p-4">
                  <span
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-600 border border-blue-100 group-hover:bg-blue-100 transition-colors"
                  >
                    <span
                      class="size-1.5 rounded-full bg-blue-600 animate-pulse"
                    ></span>
                    Seminar Proposal
                  </span>
                </td>
                <td class="p-4 text-xs font-medium text-text-secondary">
                  12 Okt 2023
                </td>
                <td class="p-4 text-right">
                  <button
                    class="inline-flex items-center justify-center px-4 py-2 text-xs font-bold text-white bg-primary rounded-lg hover:bg-primary/90 transition-all shadow-sm hover:shadow-md hover:-translate-y-0.5 active:translate-y-0"
                  >
                    Verifikasi
                  </button>
                </td>
              </tr>
              <tr class="group hover:bg-sidebar-light/30 transition-colors">
                <td class="p-4">
                  <div class="flex items-center gap-3">
                    <div
                      class="bg-gray-200 size-10 rounded-full bg-cover bg-center border border-border-light shadow-sm transition-transform group-hover:scale-105"
                      data-alt="Student Portrait 2"
                      style="
                        background-image: url(&quot;https://lh3.googleusercontent.com/aida-public/AB6AXuDGtEzfJbqFw60krz6Rx-r9nY2eGR03hTU-JrE4a9UUCRSVRSIoqGGg9ZXSb-StEeDNGhLxU8KtR4mweFY8Q7hjyqvioiT8DQcg04eUjXkVjEOSS1iUMSWjTEXfnzoRTlGLItklfOGPbOxxJYCc0ue2fH_Ei6Cv0K8-9MpF6rk9zWPzsOxHZAhMlIkwjyYDzLFklK6THvb9PX22L8Cs5hq5TcdL-NVDoJNaqCX4xEmnOJJKx4GrNWlmXNXFGrwyr_shcZuEQtxq7b94&quot;);
                      "
                    ></div>
                    <div>
                      <p
                        class="font-bold text-text-main text-sm group-hover:text-primary transition-colors"
                      >
                        Siti Aminah
                      </p>
                      <p class="text-xs text-text-secondary font-medium">
                        201910255
                      </p>
                    </div>
                  </div>
                </td>
                <td class="p-4">
                  <span
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-purple-50 text-purple-600 border border-purple-100 group-hover:bg-purple-100 transition-colors"
                  >
                    <span
                      class="size-1.5 rounded-full bg-purple-600 animate-pulse"
                    ></span>
                    SK Pembimbing
                  </span>
                </td>
                <td class="p-4 text-xs font-medium text-text-secondary">
                  11 Okt 2023
                </td>
                <td class="p-4 text-right">
                  <button
                    class="inline-flex items-center justify-center px-4 py-2 text-xs font-bold text-white bg-primary rounded-lg hover:bg-primary/90 transition-all shadow-sm hover:shadow-md hover:-translate-y-0.5 active:translate-y-0"
                  >
                    Buat SK
                  </button>
                </td>
              </tr>
              <tr class="group hover:bg-sidebar-light/30 transition-colors">
                <td class="p-4">
                  <div class="flex items-center gap-3">
                    <div
                      class="bg-gray-200 size-10 rounded-full bg-cover bg-center border border-border-light shadow-sm transition-transform group-hover:scale-105"
                      data-alt="Student Portrait 3"
                      style="
                        background-image: url(&quot;https://lh3.googleusercontent.com/aida-public/AB6AXuCzRZ4qfdCRtOwpchjjp-oRKQI4Rcgkv08vx0OHa00hvnRk3qNENQwuZjOW9rqhiXEpbfOrruMMP-phbXyM_xr_HR2g4235Zzj6x-4SX_gQKrcKkbn8hdhBjGL5DaxRhHD38VC6LViR0X3_HBw7iI2Hs_WqUkj7if0NslGJWsosBcW0Q_gowYR_8noaFgX7Yrn_g8-tgkOaZ5sWmQmPy4ZROvec6WewHl77MQfKlJpClmm5Kr5d39bNgT1TdhZY-Lua_zkjNOLKJUUp&quot;);
                      "
                    ></div>
                    <div>
                      <p
                        class="font-bold text-text-main text-sm group-hover:text-primary transition-colors"
                      >
                        Ahmad Rizky
                      </p>
                      <p class="text-xs text-text-secondary font-medium">
                        201910301
                      </p>
                    </div>
                  </div>
                </td>
                <td class="p-4">
                  <span
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-orange-50 text-orange-600 border border-orange-100 group-hover:bg-orange-100 transition-colors"
                  >
                    <span
                      class="size-1.5 rounded-full bg-orange-600 animate-pulse"
                    ></span>
                    Daftar Ujian
                  </span>
                </td>
                <td class="p-4 text-xs font-medium text-text-secondary">
                  10 Okt 2023
                </td>
                <td class="p-4 text-right">
                  <button
                    class="inline-flex items-center justify-center px-4 py-2 text-xs font-bold text-white bg-primary rounded-lg hover:bg-primary/90 transition-all shadow-sm hover:shadow-md hover:-translate-y-0.5 active:translate-y-0"
                  >
                    Cek Berkas
                  </button>
                </td>
              </tr>
              <tr class="group hover:bg-sidebar-light/30 transition-colors">
                <td class="p-4">
                  <div class="flex items-center gap-3">
                    <div
                      class="bg-gray-200 size-10 rounded-full bg-cover bg-center border border-border-light shadow-sm transition-transform group-hover:scale-105"
                      data-alt="Student Portrait 4"
                      style="
                        background-image: url(&quot;https://lh3.googleusercontent.com/aida-public/AB6AXuBeaKH1QCMKreLriqbuoliRGX5npqn48UgNZGW6tzdFMorOG5eiMOCiZjs0K7Cr-Lk2VydWByGxwZymMeagD8mq5v5wIUbvOVV2tie2gCcudplM9CzYB7uP8QGBk1VNof1731pRIDvIyd0026DVzzqeoxJYEi4roRrGSYRjsXsTRq39oz2dTvtI5VMN3Fjkdwqi8Z5scvSKowKW70t4Nd3YIGwov1ah-6Mg0i_b40kTUGIzEjWMt99XqNuS5-HV82JvEiAL0s1dc34d&quot;);
                      "
                    ></div>
                    <div>
                      <p
                        class="font-bold text-text-main text-sm group-hover:text-primary transition-colors"
                      >
                        Dewi Lestari
                      </p>
                      <p class="text-xs text-text-secondary font-medium">
                        201910412
                      </p>
                    </div>
                  </div>
                </td>
                <td class="p-4">
                  <span
                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-green-50 text-green-600 border border-green-100 group-hover:bg-green-100 transition-colors"
                  >
                    <span
                      class="size-1.5 rounded-full bg-green-600 animate-pulse"
                    ></span>
                    Revisi
                  </span>
                </td>
                <td class="p-4 text-xs font-medium text-text-secondary">
                  09 Okt 2023
                </td>
                <td class="p-4 text-right">
                  <button
                    class="inline-flex items-center justify-center px-4 py-2 text-xs font-bold text-white bg-primary rounded-lg hover:bg-primary/90 transition-all shadow-sm hover:shadow-md hover:-translate-y-0.5 active:translate-y-0"
                  >
                    Lihat Detail
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup></script>

<style scoped>
/* Custom keyframe for fade-in-up if Tailwind config doesn't have it */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in-up {
  animation: fadeInUp 0.5s ease-out forwards;
}
</style>
