<template>
  <main
    class="flex-grow flex items-center justify-center p-4 min-h-screen bg-cover bg-center bg-no-repeat"
    :style="{ backgroundImage: `url('/bg-login.png')` }"
  >
    <div
      class="w-full max-w-[900px] flex rounded-2xl overflow-hidden"
      style="box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4)"
    >
      <!-- Left: Login Form -->
      <div class="w-full md:w-1/2 bg-white p-10 flex flex-col justify-center">
        <div class="mb-8">
          <div class="md:w-100 flex justify-center">
            <img
              src="/DALFIN-LOGO.png"
              alt="DALFIN Logo"
              class="h-[85px] w-[243px] object-cover"
            />
          </div>
        </div>

        <form class="flex flex-col gap-5" @submit.prevent="handleLogin">
          <div class="flex flex-col gap-2">
            <label class="text-[#111418] text-sm font-medium leading-normal">
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
                class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-[#111418] focus:outline-0 focus:ring-2 focus:ring-primary/50 border border-gray-200 h-12 placeholder:text-gray-400 pl-10 pr-4 text-base font-normal leading-normal"
                placeholder="Masukkan NIM atau username"
              />
            </div>
          </div>

          <div class="flex flex-col gap-2">
            <label class="text-[#111418] text-sm font-medium leading-normal">
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
                  class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-[#111418] focus:outline-0 focus:ring-2 focus:ring-primary/50 border border-gray-200 h-12 placeholder:text-gray-400 pl-10 pr-12 text-base font-normal leading-normal"
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

          <div class="flex items-center px-1">
            <label class="flex items-center gap-2 cursor-pointer">
              <input
                v-model="rememberMe"
                type="checkbox"
                class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary"
              />
              <span class="text-sm text-gray-600">Ingat Saya</span>
            </label>
          </div>

          <!-- Error Alert -->
          <div
            v-if="error"
            class="p-3 bg-red-50 border border-red-200 rounded-lg"
          >
            <p class="text-sm text-red-600">{{ error }}</p>
          </div>

          <button
            type="submit"
            :disabled="loading"
            class="flex w-full cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-4 bg-primary text-white text-base font-bold leading-normal tracking-[0.015em] hover:bg-primary/90 shadow-md active:scale-[0.98] transition-all disabled:opacity-70 disabled:cursor-not-allowed"
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

        <div class="mt-6 pt-4 border-t border-gray-100">
          <p class="text-xs text-center text-gray-500">
            &copy; 2026 Pusat Informasi &amp; Teknologi Universitas.
            <br />Semua Hak Dilindungi.
          </p>
        </div>
      </div>

      <!-- Right: Illustration -->
      <div
        class="hidden md:flex w-1/2 items-center justify-center bg-white 200"
      >
        <img
          src="/gambar1.png"
          alt="Illustration"
          class="w-full h-full object-cover rounded-xl"
        />
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
      if (role === "admin" || role === "super_admin" || role === "staff") {
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
