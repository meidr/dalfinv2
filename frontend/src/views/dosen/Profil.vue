<template>
  <div class="flex flex-col gap-6 animate-fade-in">
    <!-- Header -->
    <div class="mb-4">
      <div class="flex items-center gap-2 text-sm text-text-secondary mb-2">
        <span class="material-symbols-outlined text-[16px]">home</span>
        <span>/</span>
        <span>Pengaturan</span>
        <span>/</span>
        <span class="text-primary font-medium">Profil</span>
      </div>
      <h1 class="text-3xl font-bold text-text-main tracking-tight">
        Profil Dosen
      </h1>
      <p class="text-text-secondary mt-1">
        Kelola informasi pribadi dan keamanan akun Anda.
      </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
      <!-- Left Column: Data Diri -->
      <div class="lg:col-span-4 space-y-6">
        <div
          class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden"
        >
          <div
            class="h-32 bg-gradient-to-r from-primary/10 to-primary/30 relative"
          >
            <div
              class="absolute -bottom-16 left-1/2 -translate-x-1/2 p-1.5 bg-surface-light rounded-full"
            >
              <div
                class="size-32 rounded-full bg-cover bg-center border border-border-light"
                style="
                  background-image: url(&quot;https://lh3.googleusercontent.com/aida-public/AB6AXuAXvz2bJMCa2XPAeNBKDrSOFQtnaT9UOfYuLYQa8-1q5vB1693Cv3X3y2g51vxTMjSEfp6gi5qk6mPiDe9wyvoj8YxaPA-tFooJDfbE3FIFemFIcuwO3omF1gLVJksz-FpiGOYXqqDxAZTYnGqjgSp1UbVwNyn1Zx2ShwcEpCyRvGBdMw-G69KyVA6yuSukaMZma0tjLOPMpLfC0N5L6VjJBpjXFlvz4rSMJY09n7spPIr2_ovHr2fEyFuiyPdGBVhUIiOhyKLxdEdK&quot;);
                "
              ></div>
            </div>
          </div>
          <div class="pt-20 pb-8 px-6 text-center">
            <h2 class="text-xl font-bold text-text-main">
              Dr. Ahmad Fauzi, M.Kom
            </h2>
            <span
              class="inline-flex items-center gap-1 mt-2 px-3 py-1 rounded-full bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs font-bold"
            >
              <span
                class="size-1.5 rounded-full bg-green-600 animate-pulse"
              ></span>
              Dosen Aktif
            </span>
            <div
              class="mt-8 flex flex-col gap-0 divide-y divide-border-light text-left"
            >
              <div class="flex flex-col py-4 border-t border-border-light">
                <span
                  class="text-xs font-medium text-text-secondary uppercase tracking-wider mb-1"
                  >NIDN / NIP</span
                >
                <span class="text-text-main font-medium font-mono"
                  >0012345678</span
                >
              </div>
              <div class="flex flex-col py-4">
                <span
                  class="text-xs font-medium text-text-secondary uppercase tracking-wider mb-1"
                  >Email Institusi</span
                >
                <span class="text-text-main font-medium"
                  >ahmad.fauzi@university.ac.id</span
                >
              </div>
              <div class="flex flex-col py-4">
                <span
                  class="text-xs font-medium text-text-secondary uppercase tracking-wider mb-1"
                  >Fakultas</span
                >
                <span class="text-text-main font-medium">Ilmu Komputer</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Optional Helper Card -->
        <div
          class="bg-blue-50 dark:bg-blue-900/10 rounded-xl p-5 border border-blue-100 dark:border-blue-900/30 flex gap-4 items-start"
        >
          <span class="material-symbols-outlined text-primary mt-0.5"
            >info</span
          >
          <div>
            <h4 class="text-sm font-bold text-text-main mb-1">
              Butuh Bantuan?
            </h4>
            <p class="text-sm text-text-secondary leading-relaxed">
              Jika terdapat kesalahan data diri, silakan hubungi bagian
              administrasi akademik atau kirim tiket bantuan.
            </p>
          </div>
        </div>
      </div>

      <!-- Right Column: Ganti Password -->
      <div class="lg:col-span-8">
        <div
          class="bg-surface-light rounded-xl shadow-sm border border-border-light h-full flex flex-col"
        >
          <div class="border-b border-border-light px-8 py-6">
            <h2 class="text-xl font-bold text-text-main">Ganti Password</h2>
            <p class="text-text-secondary text-sm mt-1">
              Pastikan password baru Anda aman dan mudah diingat.
            </p>
          </div>
          <div class="p-8 flex-1">
            <form class="max-w-xl flex flex-col gap-6">
              <!-- Current Password -->
              <div class="space-y-2">
                <label
                  class="text-sm font-semibold text-text-main"
                  for="current_password"
                  >Password Lama</label
                >
                <div class="relative group">
                  <div
                    class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-text-secondary"
                  >
                    <span class="material-symbols-outlined text-[20px]"
                      >lock</span
                    >
                  </div>
                  <input
                    class="w-full pl-10 pr-10 py-3 rounded-lg bg-background-light border border-border-light text-text-main placeholder-gray-400 focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all text-sm outline-none"
                    id="current_password"
                    placeholder="Masukkan password saat ini"
                    type="password"
                  />
                  <button
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-text-secondary hover:text-text-main cursor-pointer"
                    type="button"
                  >
                    <span class="material-symbols-outlined text-[20px]"
                      >visibility</span
                    >
                  </button>
                </div>
              </div>
              <hr class="border-border-light my-2" />
              <!-- New Password -->
              <div class="space-y-2">
                <label
                  class="text-sm font-semibold text-text-main"
                  for="new_password"
                  >Password Baru</label
                >
                <div class="relative group">
                  <div
                    class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-text-secondary"
                  >
                    <span class="material-symbols-outlined text-[20px]"
                      >vpn_key</span
                    >
                  </div>
                  <input
                    class="w-full pl-10 pr-10 py-3 rounded-lg bg-background-light border border-border-light text-text-main placeholder-gray-400 focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all text-sm outline-none"
                    id="new_password"
                    placeholder="Minimal 8 karakter"
                    type="password"
                  />
                  <button
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-text-secondary hover:text-text-main cursor-pointer"
                    type="button"
                  >
                    <span class="material-symbols-outlined text-[20px]"
                      >visibility_off</span
                    >
                  </button>
                </div>
                <p class="text-xs text-text-secondary mt-1 pl-1">
                  Tips: Gunakan kombinasi huruf besar, kecil, dan angka.
                </p>
              </div>
              <!-- Confirm Password -->
              <div class="space-y-2">
                <label
                  class="text-sm font-semibold text-text-main"
                  for="confirm_password"
                  >Konfirmasi Password Baru</label
                >
                <div class="relative group">
                  <div
                    class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-text-secondary"
                  >
                    <span class="material-symbols-outlined text-[20px]"
                      >check_circle</span
                    >
                  </div>
                  <input
                    class="w-full pl-10 pr-10 py-3 rounded-lg bg-background-light border border-border-light text-text-main placeholder-gray-400 focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all text-sm outline-none"
                    id="confirm_password"
                    placeholder="Ulangi password baru"
                    type="password"
                  />
                </div>
              </div>
              <!-- Action Buttons -->
              <div class="pt-6 flex flex-col sm:flex-row gap-4">
                <button
                  class="flex-1 bg-primary hover:bg-primary/90 text-white font-bold py-3 px-6 rounded-lg shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2"
                  type="button"
                >
                  <span class="material-symbols-outlined text-[20px]"
                    >save</span
                  >
                  Update Password
                </button>
                <button
                  class="sm:w-auto w-full bg-surface-light border border-border-light text-text-main font-medium py-3 px-6 rounded-lg hover:bg-sidebar-light transition-colors"
                  type="button"
                >
                  Batal
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
