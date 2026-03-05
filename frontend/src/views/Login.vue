<template>
  <main
    class="login-page flex-grow flex items-center justify-center p-4 min-h-screen bg-cover bg-center bg-no-repeat relative overflow-hidden"
    :style="{ backgroundImage: `url('/bg-login.png')` }"
  >
    <!-- Animated Overlay -->
    <div
      class="absolute inset-0 bg-gradient-to-br from-primary/30 via-transparent to-indigo-900/40 z-0"
    ></div>

    <!-- Floating Particles -->
    <div class="particles-container absolute inset-0 z-0 pointer-events-none">
      <div
        v-for="i in 20"
        :key="i"
        class="particle"
        :style="getParticleStyle(i)"
      ></div>
    </div>

    <!-- Animated Grid Lines -->
    <div
      class="absolute inset-0 z-0 pointer-events-none overflow-hidden opacity-[0.04]"
    >
      <div class="grid-lines"></div>
    </div>

    <!-- Login Card -->
    <div
      class="login-card w-full max-w-[920px] flex rounded-3xl overflow-hidden relative z-10"
      :class="{ 'card-visible': cardVisible }"
    >
      <!-- Left: Login Form -->
      <div
        class="w-full md:w-1/2 bg-white/95 backdrop-blur-xl p-10 flex flex-col justify-center relative"
      >
        <!-- Decorative Corner -->
        <div class="absolute top-0 left-0 w-20 h-20 opacity-10">
          <div
            class="w-full h-full bg-gradient-to-br from-primary to-transparent rounded-br-full"
          ></div>
        </div>

        <div class="logo-section mb-8" :class="{ 'logo-visible': logoVisible }">
          <div class="md:w-100 flex justify-center">
            <img
              src="/DALFIN-LOGO.png"
              alt="DALFIN Logo"
              class="h-[85px] w-[243px] object-cover drop-shadow-sm"
            />
          </div>
          <p
            class="text-center text-sm text-gray-400 mt-3 font-medium tracking-wide"
          >
            Sistem Informasi Manajemen Skripsi
          </p>
        </div>

        <form
          class="flex flex-col gap-5 form-section"
          :class="{ 'form-visible': formVisible }"
          @submit.prevent="handleLogin"
        >
          <!-- Username -->
          <div class="flex flex-col gap-2 input-group" style="--delay: 0.1s">
            <label class="text-[#111418] text-sm font-medium leading-normal">
              Username / NIM
            </label>
            <div class="relative group">
              <span
                class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[20px] transition-colors group-focus-within:text-primary"
              >
                person
              </span>
              <input
                v-model="username"
                type="text"
                class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-[#111418] focus:outline-0 focus:ring-2 focus:ring-primary/50 focus:border-primary border border-gray-200 h-12 placeholder:text-gray-400 pl-10 pr-4 text-base font-normal leading-normal transition-all duration-300 hover:border-primary/30 focus:shadow-[0_0_0_4px_rgba(59,130,246,0.1)]"
                placeholder="Masukkan NIM atau username"
              />
            </div>
          </div>

          <!-- Password -->
          <div class="flex flex-col gap-2 input-group" style="--delay: 0.2s">
            <label class="text-[#111418] text-sm font-medium leading-normal">
              Password
            </label>
            <div class="flex w-full flex-1 items-stretch rounded-xl group">
              <div class="relative w-full">
                <span
                  class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[20px] transition-colors group-focus-within:text-primary"
                >
                  lock
                </span>
                <input
                  v-model="password"
                  :type="showPassword ? 'text' : 'password'"
                  class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-[#111418] focus:outline-0 focus:ring-2 focus:ring-primary/50 focus:border-primary border border-gray-200 h-12 placeholder:text-gray-400 pl-10 pr-12 text-base font-normal leading-normal transition-all duration-300 hover:border-primary/30 focus:shadow-[0_0_0_4px_rgba(59,130,246,0.1)]"
                  placeholder="Masukkan kata sandi"
                />
                <div
                  class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 cursor-pointer hover:text-primary transition-all duration-200 hover:scale-110"
                  @click="togglePassword"
                >
                  <span class="material-symbols-outlined text-[22px]">
                    {{ showPassword ? "visibility_off" : "visibility" }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Remember Me -->
          <div class="flex items-center px-1 input-group" style="--delay: 0.3s">
            <label class="flex items-center gap-2 cursor-pointer group">
              <input
                v-model="rememberMe"
                type="checkbox"
                class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary transition-transform duration-200 group-hover:scale-110"
              />
              <span
                class="text-sm text-gray-600 group-hover:text-gray-800 transition-colors"
                >Ingat Saya</span
              >
            </label>
          </div>

          <!-- Error Alert -->
          <transition name="shake">
            <div
              v-if="error"
              class="p-3 bg-red-50 border border-red-200 rounded-xl flex items-center gap-2 animate-shake"
            >
              <span class="material-symbols-outlined text-red-500 text-[18px]"
                >error</span
              >
              <p class="text-sm text-red-600">{{ error }}</p>
            </div>
          </transition>

          <!-- Submit Button -->
          <div class="input-group" style="--delay: 0.4s">
            <button
              type="submit"
              :disabled="loading"
              class="login-btn flex w-full cursor-pointer items-center justify-center overflow-hidden rounded-xl h-12 px-4 bg-gradient-to-r from-primary to-blue-600 text-white text-base font-bold leading-normal tracking-[0.015em] shadow-lg shadow-primary/25 active:scale-[0.98] transition-all duration-300 disabled:opacity-70 disabled:cursor-not-allowed relative group"
            >
              <div
                class="absolute inset-0 bg-gradient-to-r from-blue-600 to-indigo-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-xl"
              ></div>
              <div class="absolute inset-0 shimmer-effect rounded-xl"></div>
              <span
                v-if="loading"
                class="flex items-center gap-2 relative z-10"
              >
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
              <span
                v-else
                class="truncate relative z-10 flex items-center gap-2"
              >
                Masuk ke Sistem
                <span
                  class="material-symbols-outlined text-[18px] transition-transform duration-300 group-hover:translate-x-1"
                  >arrow_forward</span
                >
              </span>
            </button>
          </div>
        </form>

        <div
          class="mt-6 pt-4 border-t border-gray-100 footer-section"
          :class="{ 'footer-visible': footerVisible }"
        >
          <p class="text-xs text-center text-gray-400">
            &copy; 2026 Pusat Informasi &amp; Teknologi Universitas.
            <br />Semua Hak Dilindungi.
          </p>
        </div>
      </div>

      <!-- Right: Illustration -->
      <div
        class="hidden md:flex w-1/2 items-center justify-center bg-white relative overflow-hidden illustration-section"
      >
        <img
          src="/gambar1.png"
          alt="Illustration"
          class="w-full h-full object-cover"
        />
        <!-- Floating accent overlay -->
        <div
          class="absolute inset-0 bg-gradient-to-t from-primary/10 via-transparent to-transparent pointer-events-none"
        ></div>
        <!-- Animated rings -->
        <div
          class="absolute inset-0 flex items-center justify-center pointer-events-none"
        >
          <div class="ring ring-1-anim"></div>
          <div class="ring ring-2-anim"></div>
          <div class="ring ring-3-anim"></div>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, onMounted } from "vue";
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

// Animation state
const cardVisible = ref(false);
const logoVisible = ref(false);
const formVisible = ref(false);
const footerVisible = ref(false);

const togglePassword = () => {
  showPassword.value = !showPassword.value;
};

const getParticleStyle = (i) => {
  const size = Math.random() * 4 + 2;
  const x = Math.random() * 100;
  const y = Math.random() * 100;
  const duration = Math.random() * 20 + 15;
  const delay = Math.random() * 10;
  const opacity = Math.random() * 0.4 + 0.1;
  return {
    width: `${size}px`,
    height: `${size}px`,
    left: `${x}%`,
    top: `${y}%`,
    animationDuration: `${duration}s`,
    animationDelay: `${delay}s`,
    opacity: opacity,
  };
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

onMounted(() => {
  // Staggered entrance animations
  setTimeout(() => (cardVisible.value = true), 100);
  setTimeout(() => (logoVisible.value = true), 400);
  setTimeout(() => (formVisible.value = true), 600);
  setTimeout(() => (footerVisible.value = true), 1000);
});
</script>

<style scoped>
/* === Card Entrance === */
.login-card {
  box-shadow:
    0 25px 60px rgba(0, 0, 0, 0.3),
    0 0 100px rgba(59, 130, 246, 0.08);
  opacity: 0;
  transform: translateY(40px) scale(0.95);
  transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);
}
.login-card.card-visible {
  opacity: 1;
  transform: translateY(0) scale(1);
}

/* === Logo Entrance === */
.logo-section {
  opacity: 0;
  transform: translateY(-20px);
  transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}
.logo-section.logo-visible {
  opacity: 1;
  transform: translateY(0);
}

/* === Form Entrance === */
.form-section {
  opacity: 0;
  transform: translateY(20px);
  transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}
.form-section.form-visible {
  opacity: 1;
  transform: translateY(0);
}
.form-section.form-visible .input-group {
  animation: slideUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
  animation-delay: var(--delay, 0s);
  opacity: 0;
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(15px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* === Footer Entrance === */
.footer-section {
  opacity: 0;
  transition: opacity 0.5s ease 0.3s;
}
.footer-section.footer-visible {
  opacity: 1;
}

/* === Button Shimmer === */
.shimmer-effect {
  background: linear-gradient(
    105deg,
    transparent 40%,
    rgba(255, 255, 255, 0.15) 50%,
    transparent 60%
  );
  background-size: 200% 100%;
  animation: shimmer 3s ease-in-out infinite;
}
@keyframes shimmer {
  0% {
    background-position: 200% 0;
  }
  100% {
    background-position: -200% 0;
  }
}

/* === Floating Particles === */
.particle {
  position: absolute;
  border-radius: 50%;
  background: white;
  animation: float linear infinite;
}
@keyframes float {
  0%,
  100% {
    transform: translate(0, 0) scale(1);
    opacity: 0;
  }
  10% {
    opacity: 0.6;
  }
  50% {
    transform: translate(calc(var(--tx, 30px)), calc(var(--ty, -80px)))
      scale(1.2);
    opacity: 0.3;
  }
  90% {
    opacity: 0.6;
  }
}
.particle:nth-child(odd) {
  --tx: 40px;
  --ty: -100px;
}
.particle:nth-child(even) {
  --tx: -30px;
  --ty: -60px;
}
.particle:nth-child(3n) {
  --tx: 60px;
  --ty: -120px;
  background: rgba(59, 130, 246, 0.6);
}
.particle:nth-child(5n) {
  --tx: -50px;
  --ty: -90px;
  background: rgba(99, 102, 241, 0.5);
}

/* === Animated Rings on Illustration === */
.ring {
  position: absolute;
  border: 1px solid rgba(59, 130, 246, 0.15);
  border-radius: 50%;
  animation-timing-function: ease-in-out;
  animation-iteration-count: infinite;
}
.ring-1-anim {
  width: 120px;
  height: 120px;
  animation: pulse-ring 4s 0s infinite;
}
.ring-2-anim {
  width: 200px;
  height: 200px;
  animation: pulse-ring 4s 1s infinite;
}
.ring-3-anim {
  width: 280px;
  height: 280px;
  animation: pulse-ring 4s 2s infinite;
}
@keyframes pulse-ring {
  0%,
  100% {
    transform: scale(0.8);
    opacity: 0;
  }
  50% {
    transform: scale(1.1);
    opacity: 0.4;
  }
}

/* === Grid Lines === */
.grid-lines {
  width: 100%;
  height: 100%;
  background-image:
    linear-gradient(to right, white 1px, transparent 1px),
    linear-gradient(to bottom, white 1px, transparent 1px);
  background-size: 60px 60px;
  animation: grid-scroll 20s linear infinite;
}
@keyframes grid-scroll {
  0% {
    transform: translate(0, 0);
  }
  100% {
    transform: translate(60px, 60px);
  }
}

/* === Shake Animation for Error === */
.animate-shake {
  animation: shake 0.4s ease-in-out;
}
@keyframes shake {
  0%,
  100% {
    transform: translateX(0);
  }
  20% {
    transform: translateX(-8px);
  }
  40% {
    transform: translateX(8px);
  }
  60% {
    transform: translateX(-4px);
  }
  80% {
    transform: translateX(4px);
  }
}

/* === Error Transition === */
.shake-enter-active {
  transition: all 0.3s ease;
}
.shake-leave-active {
  transition: all 0.2s ease;
}
.shake-enter-from {
  opacity: 0;
  transform: translateY(-10px);
}
.shake-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
