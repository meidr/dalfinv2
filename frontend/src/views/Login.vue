<template>
  <main class="flex-grow flex items-center justify-center p-4 bg-pattern dark:bg-none dark:bg-background-dark">
    <div class="w-full max-w-[420px] flex flex-col gap-6">
      <div class="bg-white dark:bg-gray-900 shadow-xl rounded-xl overflow-hidden border border-gray-100 dark:border-gray-800">
        <div class="p-8 pb-0 flex flex-col items-center">
          <div class="w-20 h-20 bg-primary/10 rounded-full flex items-center justify-center mb-4">
            <span class="material-symbols-outlined text-primary text-4xl">account_balance</span>
          </div>
          <h1 class="text-[#111418] dark:text-white tracking-tight text-2xl font-bold leading-tight text-center">
            Sistem Informasi Skripsi
          </h1>
          <p class="text-gray-500 dark:text-gray-400 text-sm mt-2 text-center">
            Silakan masuk menggunakan akun universitas Anda
          </p>
        </div>

        <div class="p-8">
          <form class="flex flex-col gap-5" @submit.prevent="handleLogin">
            <div class="flex flex-col gap-2">
              <label class="text-[#111418] dark:text-gray-200 text-sm font-medium leading-normal">
                Username / NIM
              </label>
              <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[20px]">
                  person
                </span>
                <input
                  v-model="username"
                  type="text"
                  class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-[#111418] dark:text-white dark:bg-gray-800 focus:outline-0 focus:ring-2 focus:ring-primary/50 border border-gray-200 dark:border-gray-700 h-12 placeholder:text-gray-400 pl-10 pr-4 text-base font-normal leading-normal"
                  placeholder="Masukkan NIM atau username"
                />
              </div>
            </div>

            <div class="flex flex-col gap-2">
              <label class="text-[#111418] dark:text-gray-200 text-sm font-medium leading-normal">
                Password
              </label>
              <div class="flex w-full flex-1 items-stretch rounded-lg group">
                <div class="relative w-full">
                  <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[20px]">
                    lock
                  </span>
                  <input
                    v-model="password"
                    :type="showPassword ? 'text' : 'password'"
                    class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-[#111418] dark:text-white dark:bg-gray-800 focus:outline-0 focus:ring-2 focus:ring-primary/50 border border-gray-200 dark:border-gray-700 h-12 placeholder:text-gray-400 pl-10 pr-12 text-base font-normal leading-normal"
                    placeholder="Masukkan kata sandi"
                  />
                  <div
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 cursor-pointer hover:text-primary transition-colors"
                    @click="togglePassword"
                  >
                    <span class="material-symbols-outlined text-[22px]">
                      {{ showPassword ? 'visibility_off' : 'visibility' }}
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <div class="flex items-center mt-2 px-1">
              <label class="flex items-center gap-2 cursor-pointer">
                <input
                  v-model="rememberMe"
                  type="checkbox"
                  class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary"
                />
                <span class="text-sm text-gray-600 dark:text-gray-400">Ingat Saya</span>
              </label>
            </div>

            <button
              type="submit"
              class="mt-2 flex w-full cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-4 bg-primary text-white text-base font-bold leading-normal tracking-[0.015em] hover:bg-primary/90 shadow-md active:scale-[0.98] transition-all"
            >
              <span class="truncate">Masuk ke Sistem</span>
            </button>
          </form>
        </div>

        <div class="px-8 py-6 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-800 flex flex-col gap-2">
          <p class="text-xs text-center text-gray-500 dark:text-gray-400">
            © 2024 Pusat Informasi &amp; Teknologi Universitas. <br />Semua Hak Dilindungi.
          </p>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref } from 'vue'

const username = ref('')
const password = ref('')
const rememberMe = ref(true)
const showPassword = ref(false)

const togglePassword = () => {
  showPassword.value = !showPassword.value
}

const handleLogin = () => {
  // Frontend only - just log the values
  console.log('Login attempted:', {
    username: username.value,
    password: password.value,
    rememberMe: rememberMe.value
  })
}
</script>
