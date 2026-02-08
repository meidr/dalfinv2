<template>
  <main
    class="flex-grow flex items-center justify-center p-4 bg-pattern dark:bg-none dark:bg-background-dark"
  >
    <div class="w-full max-w-[420px] flex flex-col gap-6">
      <div
        class="bg-white dark:bg-gray-900 shadow-xl rounded-xl overflow-hidden border border-gray-100 dark:border-gray-800"
      >
        <div class="p-8 pb-0 flex flex-col items-center">
          <div
            class="w-20 h-20 bg-primary/10 rounded-full flex items-center justify-center mb-4"
          >
            <span class="material-symbols-outlined text-primary text-4xl"
              >account_balance</span
            >
          </div>
          <h1
            class="text-[#111418] dark:text-white tracking-tight text-2xl font-bold leading-tight text-center"
          >
            Sistem Informasi Skripsi
          </h1>
          <p class="text-gray-500 dark:text-gray-400 text-sm mt-2 text-center">
            Silakan masuk menggunakan akun universitas Anda
          </p>
        </div>

        <div class="p-8">
          <form class="flex flex-col gap-5" @submit.prevent="handleLogin">
            <div class="flex flex-col gap-2">
              <label
                class="text-[#111418] dark:text-gray-200 text-sm font-medium leading-normal"
              >
                Username / NIM
              </label>
              <div class="relative">
                <span
                  class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[20px]"
                >
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
              <label
                class="text-[#111418] dark:text-gray-200 text-sm font-medium leading-normal"
              >
                Password
              </label>
              <div class="flex w-full flex-1 items-stretch rounded-lg group">
                <div class="relative w-full">
                  <span
                    class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[20px]"
                  >
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
                      {{ showPassword ? "visibility_off" : "visibility" }}
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
                <span class="text-sm text-gray-600 dark:text-gray-400"
                  >Ingat Saya</span
                >
              </label>
            </div>

            <!-- Error Alert -->
            <div
              v-if="error"
              class="mt-2 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg"
            >
              <p class="text-sm text-red-600 dark:text-red-400">{{ error }}</p>
            </div>

            <button
              type="submit"
              :disabled="loading"
              class="mt-2 flex w-full cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-4 bg-primary text-white text-base font-bold leading-normal tracking-[0.015em] hover:bg-primary/90 shadow-md active:scale-[0.98] transition-all disabled:opacity-70 disabled:cursor-not-allowed"
            >
              <span v-if="loading" class="flex items-center gap-2">
                <svg
                  class="animate-spin h-5 w-5"
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                >
                  <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                  ></circle>
                  <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                  ></path>
                </svg>
                Memproses...
              </span>
              <span v-else class="truncate">Masuk ke Sistem</span>
            </button>
          </form>
        </div>

        <div
          class="px-8 py-6 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-800 flex flex-col gap-2"
        >
          <p class="text-xs text-center text-gray-500 dark:text-gray-400">
            © 2024 Pusat Informasi &amp; Teknologi Universitas. <br />Semua Hak
            Dilindungi.
          </p>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "../stores/auth";

const router = useRouter();
const authStore = useAuthStore();

const username = ref("");
const password = ref("");
const rememberMe = ref(true);
const showPassword = ref(false);
const loading = ref(false);
const error = ref("");

const togglePassword = () => {
  showPassword.value = !showPassword.value;
};

const handleLogin = async () => {
  if (!username.value || !password.value) {
    error.value = "Mohon isi username dan password";
    return;
  }

  loading.value = true;
  error.value = "";

  try {
    const result = await authStore.login(username.value, password.value);

    if (result.success) {
      // Redirect based on role
      const role = authStore.userRole;
      if (role === "admin") {
        router.push("/admin");
      } else if (role === "dosen") {
        router.push("/dosen");
      } else if (role === "mahasiswa") {
        router.push("/mahasiswa");
      } else {
        router.push("/");
      }
    }
  } catch (err) {
    error.value =
      err.response?.data?.message ||
      "Login gagal. Periksa kembali username dan password Anda.";
  } finally {
    loading.value = false;
  }
};
</script>
