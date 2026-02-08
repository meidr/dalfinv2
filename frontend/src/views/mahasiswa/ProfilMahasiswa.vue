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
      <span class="text-text-main font-bold">Profil</span>
    </div>

    <div class="mb-4">
      <h1 class="text-3xl font-bold tracking-tight text-text-main">
        Profil Mahasiswa
      </h1>
      <p class="text-text-secondary text-sm">
        Kelola informasi akun dan keamanan Anda.
      </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
      <!-- Left: Profile Card -->
      <section class="lg:col-span-4 w-full">
        <div
          class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden"
        >
          <!-- Banner -->
          <div
            class="h-24 w-full bg-gradient-to-r from-blue-500 to-primary"
          ></div>

          <div class="px-6 pb-8 -mt-12 flex flex-col items-center">
            <!-- Avatar -->
            <div class="relative">
              <div
                class="size-32 rounded-full border-4 border-surface-light shadow-md bg-cover bg-center"
                style="
                  background-image: url(&quot;https://lh3.googleusercontent.com/aida-public/AB6AXuA8y9CNh5MhBGZObdkg3traTlC22PfVNf0UpUeKu8xQNOufwkWI1Y63GZh3m7OzpWXCiQEbNNkUjiKpw4iWuqQP-w0uPD9_Kng2qJZZPWnFLsDss1NWHDsdMIaW-ajIFcnrfXgDl9FKxxykFN03ELfe0NKGmH1EbjbTBKLX1MBIZHqFrOSBObEaGjeV8d_dVn-vNWwWc4tRPKdpUZ09Nzg2PhBJpAhw0Mu8Mixs6KprZrAL5CzKeNhCKeRezkDR7SoQbdqBSVMepy0r&quot;);
                "
              ></div>
              <button
                class="absolute bottom-2 right-0 p-2 bg-surface-light rounded-full shadow border border-gray-200 dark:border-gray-600 hover:text-primary transition-colors"
              >
                <span class="material-symbols-outlined text-sm"
                  >photo_camera</span
                >
              </button>
            </div>

            <div class="text-center w-full mb-6 mt-4">
              <h3 class="text-xl font-bold text-text-main">Budi Santoso</h3>
              <p class="text-sm font-medium text-text-secondary">
                Teknik Informatika
              </p>
              <div class="mt-3">
                <span
                  class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-400 text-xs font-bold border border-green-100 dark:border-green-800"
                >
                  <span
                    class="size-2 rounded-full bg-green-500 animate-pulse"
                  ></span>
                  Mahasiswa Aktif
                </span>
              </div>
            </div>

            <div class="w-full flex flex-col gap-3">
              <div class="flex justify-between p-3 bg-sidebar-light rounded-lg">
                <span class="text-sm text-text-secondary">NIM</span>
                <span class="text-sm font-bold text-text-main">12345678</span>
              </div>
              <div class="flex justify-between p-3 bg-sidebar-light rounded-lg">
                <span class="text-sm text-text-secondary">Angkatan</span>
                <span class="text-sm font-bold text-text-main">2020</span>
              </div>
              <div class="flex flex-col gap-1 p-3 bg-sidebar-light rounded-lg">
                <span
                  class="text-xs font-bold text-text-secondary uppercase tracking-wider"
                  >Email Institusi</span
                >
                <span class="text-sm font-medium text-text-main truncate"
                  >budi.s@university.ac.id</span
                >
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Right: Password Form -->
      <section class="lg:col-span-8 w-full">
        <div
          class="bg-surface-light rounded-xl shadow-sm border border-border-light p-6 md:p-8"
        >
          <div
            class="flex items-center gap-3 mb-6 border-b border-border-light pb-4"
          >
            <div class="p-2 bg-primary/10 rounded-lg text-primary">
              <span class="material-symbols-outlined mb-0.5">lock_reset</span>
            </div>
            <div>
              <h2 class="text-lg font-bold text-text-main">Ganti Password</h2>
              <p class="text-sm text-text-secondary">
                Pastikan akun Anda tetap aman.
              </p>
            </div>
          </div>

          <form class="flex flex-col gap-6 max-w-2xl">
            <div
              class="flex gap-3 bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-100 dark:border-blue-800 text-sm text-blue-800 dark:text-blue-300"
            >
              <span
                class="material-symbols-outlined text-blue-600 dark:text-blue-400"
                >info</span
              >
              <p>Password baru harus terdiri dari minimal 8 karakter.</p>
            </div>

            <div class="flex flex-col gap-2">
              <label class="text-sm font-bold text-text-main"
                >Password Lama</label
              >
              <input
                type="password"
                placeholder="Masukkan password saat ini"
                class="px-4 py-2.5 rounded-lg border border-border-light bg-background-light focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm"
              />
            </div>

            <div class="h-px bg-border-light w-full"></div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="flex flex-col gap-2">
                <label class="text-sm font-bold text-text-main"
                  >Password Baru</label
                >
                <input
                  type="password"
                  placeholder="Buat password baru"
                  class="px-4 py-2.5 rounded-lg border border-border-light bg-background-light focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm"
                />
              </div>
              <div class="flex flex-col gap-2">
                <label class="text-sm font-bold text-text-main"
                  >Konfirmasi Password</label
                >
                <input
                  type="password"
                  placeholder="Ulangi password baru"
                  class="px-4 py-2.5 rounded-lg border border-border-light bg-background-light focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm"
                />
              </div>
            </div>

            <div class="flex justify-end gap-3 mt-4">
              <button
                type="button"
                class="px-6 py-2.5 rounded-lg text-text-secondary font-bold hover:bg-sidebar-light transition-colors text-sm"
              >
                Batal
              </button>
              <button
                type="submit"
                class="px-6 py-2.5 rounded-lg bg-primary text-white font-bold hover:bg-blue-600 transition-colors shadow-sm text-sm flex items-center gap-2"
              >
                <span class="material-symbols-outlined text-[18px]">save</span>
                Update Password
              </button>
            </div>
          </form>
        </div>
      </section>
    </div>
  </div>
</template>
