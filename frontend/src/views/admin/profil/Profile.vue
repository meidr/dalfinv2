<template>
  <div class="flex flex-col gap-6">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
      <div class="flex flex-col gap-1">
        <h1 class="text-3xl font-bold tracking-tight text-text-main">
          Profil Saya
        </h1>
        <p class="text-text-secondary text-sm font-normal">
          Kelola informasi akun dan preferensi keamanan Anda.
        </p>
      </div>
      <button
        class="flex items-center justify-center gap-2 bg-primary hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg transition-all shadow-md shadow-primary/20 hover:shadow-lg active:scale-95 text-sm"
      >
        <span class="material-symbols-outlined text-[18px]">save</span>
        <span>Simpan Perubahan</span>
      </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Profile Card -->
      <div class="flex flex-col gap-6 lg:col-span-1">
        <div
          class="bg-surface-light dark:bg-surface-light p-6 rounded-xl border border-border-light shadow-sm flex flex-col items-center text-center"
        >
          <div class="relative group">
            <div
              class="size-28 rounded-full bg-cover bg-center border-4 border-white dark:border-gray-700 shadow-md mb-4"
              style="
                background-image: url(&quot;https://lh3.googleusercontent.com/aida-public/AB6AXuC6fj58EKDs12x21HTipDxffh3oyce58_8S36-1PZUEVSH0Apn9oH-MWwAXvFvCgfIwMBuY4TFQOBUpWvQvZCM2piBln7AVu9-OInn3YgI6I3HUUkanApn6BfexGFdWOiebeMvZYStp-ck_aZoHx2O-q5487Oop3186czgCW16wwA4Q7x-GmuXdolN1KCu6sXA7yEmVmPK-YYpc9A0S1I-DIt6LbQnenGu2khwnzRerxEQMeTs5KE-0nGmWzphphsHQujdpNoH7ZUP_&quot;);
              "
            ></div>
            <button
              class="absolute bottom-4 right-0 p-2 bg-white dark:bg-gray-800 rounded-full shadow-md border border-gray-200 dark:border-gray-600 hover:text-primary transition-colors"
            >
              <span class="material-symbols-outlined text-[18px]"
                >photo_camera</span
              >
            </button>
          </div>
          <h2 class="text-xl font-bold text-text-main">Admin Staff</h2>
          <p class="text-text-secondary text-sm mb-4">admin@univ.ac.id</p>
          <div class="flex gap-2 w-full">
            <span
              class="inline-flex w-full items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300 border border-purple-100 dark:border-purple-800"
            >
              <span class="material-symbols-outlined text-[16px]"
                >security</span
              >
              Administrator
            </span>
          </div>
        </div>

        <div
          class="bg-surface-light dark:bg-surface-light p-6 rounded-xl border border-border-light shadow-sm flex flex-col gap-4"
        >
          <h3 class="text-sm font-bold text-text-main uppercase tracking-wider">
            Statistik Aktivitas
          </h3>
          <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between">
              <span class="text-sm text-text-secondary">Login Terakhir</span>
              <span class="text-sm font-medium text-text-main"
                >2 jam yang lalu</span
              >
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-text-secondary">Total Login</span>
              <span class="text-sm font-medium text-text-main">1,204</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-text-secondary">Password Diubah</span>
              <span class="text-sm font-medium text-text-main"
                >3 bulan lalu</span
              >
            </div>
          </div>
        </div>
      </div>

      <!-- Forms -->
      <div class="flex flex-col gap-6 lg:col-span-2">
        <!-- General Info -->
        <div
          class="bg-surface-light dark:bg-surface-light p-6 rounded-xl border border-border-light shadow-sm"
        >
          <h3
            class="text-lg font-bold text-text-main mb-4 flex items-center gap-2"
          >
            <span class="material-symbols-outlined">person</span>
            Informasi Pribadi
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex flex-col gap-1.5">
              <label class="text-xs font-bold text-text-secondary uppercase"
                >Nama Depan</label
              >
              <input
                type="text"
                value="Admin"
                class="px-3 py-2 border border-border-light rounded-lg bg-white dark:bg-sidebar-light dark:border-gray-600 text-sm focus:ring-2 focus:ring-primary/50 outline-none transition-all"
              />
            </div>
            <div class="flex flex-col gap-1.5">
              <label class="text-xs font-bold text-text-secondary uppercase"
                >Nama Belakang</label
              >
              <input
                type="text"
                value="Staff"
                class="px-3 py-2 border border-border-light rounded-lg bg-white dark:bg-sidebar-light dark:border-gray-600 text-sm focus:ring-2 focus:ring-primary/50 outline-none transition-all"
              />
            </div>
            <div class="flex flex-col gap-1.5 md:col-span-2">
              <label class="text-xs font-bold text-text-secondary uppercase"
                >Email</label
              >
              <input
                type="email"
                value="admin@univ.ac.id"
                class="px-3 py-2 border border-border-light rounded-lg bg-white dark:bg-sidebar-light dark:border-gray-600 text-sm focus:ring-2 focus:ring-primary/50 outline-none transition-all"
              />
            </div>
            <div class="flex flex-col gap-1.5">
              <label class="text-xs font-bold text-text-secondary uppercase"
                >No. Telepon</label
              >
              <input
                type="text"
                value="+62 812 3456 7890"
                class="px-3 py-2 border border-border-light rounded-lg bg-white dark:bg-sidebar-light dark:border-gray-600 text-sm focus:ring-2 focus:ring-primary/50 outline-none transition-all"
              />
            </div>
            <div class="flex flex-col gap-1.5">
              <label class="text-xs font-bold text-text-secondary uppercase"
                >NIP / ID Karyawan</label
              >
              <input
                type="text"
                value="19850101 201001 1 001"
                disabled
                class="px-3 py-2 border border-border-light rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700 text-text-secondary text-sm cursor-not-allowed"
              />
            </div>
          </div>
        </div>

        <!-- Security -->
        <div
          class="bg-surface-light dark:bg-surface-light p-6 rounded-xl border border-border-light shadow-sm"
        >
          <h3
            class="text-lg font-bold text-text-main mb-4 flex items-center gap-2"
          >
            <span class="material-symbols-outlined">lock</span>
            Keamanan
          </h3>
          <div class="flex flex-col gap-4">
            <div
              class="flex items-center justify-between p-4 border border-border-light rounded-lg"
            >
              <div>
                <p class="font-bold text-text-main text-sm">Ganti Password</p>
                <p class="text-xs text-text-secondary">
                  Disarankan mengganti password secara berkala.
                </p>
              </div>
              <button
                class="text-primary hover:text-blue-700 text-sm font-bold"
              >
                Update
              </button>
            </div>
            <div
              class="flex items-center justify-between p-4 border border-border-light rounded-lg"
            >
              <div>
                <p class="font-bold text-text-main text-sm">
                  Autentikasi Dua Faktor (2FA)
                </p>
                <p class="text-xs text-text-secondary">
                  Tambahkan lapisan keamanan ekstra.
                </p>
              </div>
              <div
                class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in"
              >
                <input
                  type="checkbox"
                  name="toggle"
                  id="toggle"
                  class="toggle-checkbox absolute block w-5 h-5 rounded-full bg-white border-4 appearance-none cursor-pointer border-gray-300"
                />
                <label
                  for="toggle"
                  class="toggle-label block overflow-hidden h-5 rounded-full bg-gray-300 cursor-pointer"
                ></label>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Toggle Switch */
.toggle-checkbox:checked {
  right: 0;
  border-color: #4ade80;
}
.toggle-checkbox:checked + .toggle-label {
  background-color: #4ade80;
}
.toggle-checkbox {
  right: auto;
  left: 0;
  transition: all 0.3s;
}
</style>
