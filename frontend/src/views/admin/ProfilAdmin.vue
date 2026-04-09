<template>
  <div class="max-w-3xl mx-auto flex flex-col gap-8 animate-fade-in-up">
    <!-- Header -->
    <div class="flex flex-col gap-1">
      <div class="flex items-center gap-2 text-sm text-text-secondary">
        <router-link
          to="/admin/dashboard"
          class="hover:text-primary transition-colors"
          >Dashboard</router-link
        >
        <span>/</span>
        <span class="text-text-main font-medium">Profil Saya</span>
      </div>
      <h1 class="text-text-main text-3xl font-bold leading-tight">
        Profil Saya
      </h1>
      <p class="text-text-secondary text-sm font-normal">
        Kelola informasi akun Anda.
      </p>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="p-12 text-center">
      <div
        class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto"
      ></div>
      <p class="text-text-secondary text-sm mt-3">Memuat data...</p>
    </div>

    <template v-else-if="user">
      <!-- Profile Card -->
      <div
        class="bg-surface-light border border-border-light rounded-xl shadow-sm overflow-hidden"
      >
        <div class="h-24 bg-gradient-to-r from-primary/80 to-blue-400"></div>
        <div class="px-6 pb-6 -mt-10">
          <div class="flex items-end gap-5">
            <div
              class="size-20 rounded-full flex items-center justify-center text-2xl font-bold bg-white text-primary border-4 border-white shadow-lg shrink-0"
            >
              {{ userInitials }}
            </div>
            <div class="pb-1">
              <h2 class="text-xl font-bold text-text-main">{{ user.name }}</h2>
              <span
                class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold mt-1"
                :class="
                  user.role === 'super_admin'
                    ? 'bg-purple-100 text-purple-700'
                    : 'bg-blue-100 text-blue-700'
                "
              >
                {{
                  user.role === "super_admin"
                    ? "Super Administrator"
                    : user.role === "staff"
                      ? "Staff"
                      : "Administrator"
                }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Edit Form -->
      <div
        class="bg-surface-light border border-border-light rounded-xl shadow-sm"
      >
        <div class="p-5 border-b border-border-light">
          <h3 class="text-lg font-bold text-text-main">Data Pribadi</h3>
          <p class="text-sm text-text-secondary">
            Perbarui informasi data pribadi Anda.
          </p>
        </div>
        <form @submit.prevent="saveProfile" class="p-5 space-y-5">
          <!-- Success/Error Alert -->
          <div
            v-if="successMsg"
            class="flex items-center gap-2 p-3 bg-green-50 text-green-700 rounded-lg border border-green-200 text-sm font-medium"
          >
            <span class="material-symbols-outlined text-[18px]"
              >check_circle</span
            >
            {{ successMsg }}
          </div>
          <div
            v-if="errorMsg"
            class="flex items-center gap-2 p-3 bg-red-50 text-red-700 rounded-lg border border-red-200 text-sm font-medium"
          >
            <span class="material-symbols-outlined text-[18px]">error</span>
            {{ errorMsg }}
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
              <label class="block text-sm font-medium text-text-main mb-1.5"
                >Nama <span class="text-red-500">*</span></label
              >
              <input
                v-model="form.name"
                type="text"
                required
                class="w-full px-3 py-2.5 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm"
                placeholder="Nama lengkap"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-text-main mb-1.5"
                >Email <span class="text-red-500">*</span></label
              >
              <input
                v-model="form.email"
                type="email"
                required
                class="w-full px-3 py-2.5 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm"
                placeholder="email@example.com"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-text-main mb-1.5"
                >No. Telepon</label
              >
              <input
                v-model="form.phone"
                type="text"
                class="w-full px-3 py-2.5 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm"
                placeholder="08xxxxxxxxxx"
              />
            </div>
            <div class="sm:col-span-2">
              <label class="block text-sm font-medium text-text-main mb-1.5"
                >Alamat</label
              >
              <textarea
                v-model="form.address"
                rows="2"
                class="w-full px-3 py-2.5 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm resize-none"
                placeholder="Alamat lengkap"
              ></textarea>
            </div>
          </div>

          <div class="flex justify-end pt-2">
            <button
              type="submit"
              :disabled="saving"
              class="px-6 py-2.5 bg-primary text-white rounded-lg font-medium text-sm hover:bg-blue-600 transition-colors disabled:opacity-50 flex items-center gap-2 shadow-sm"
            >
              <span class="material-symbols-outlined text-[18px]">save</span>
              {{ saving ? "Menyimpan..." : "Simpan Perubahan" }}
            </button>
          </div>
        </form>
      </div>

      <!-- Change Password -->
      <div
        class="bg-surface-light border border-border-light rounded-xl shadow-sm"
      >
        <div class="p-5 border-b border-border-light">
          <h3 class="text-lg font-bold text-text-main">Ubah Password</h3>
          <p class="text-sm text-text-secondary">
            Pastikan password baru Anda cukup kuat.
          </p>
        </div>
        <form @submit.prevent="savePassword" class="p-5 space-y-5">
          <div
            v-if="pwSuccessMsg"
            class="flex items-center gap-2 p-3 bg-green-50 text-green-700 rounded-lg border border-green-200 text-sm font-medium"
          >
            <span class="material-symbols-outlined text-[18px]"
              >check_circle</span
            >
            {{ pwSuccessMsg }}
          </div>
          <div
            v-if="pwErrorMsg"
            class="flex items-center gap-2 p-3 bg-red-50 text-red-700 rounded-lg border border-red-200 text-sm font-medium"
          >
            <span class="material-symbols-outlined text-[18px]">error</span>
            {{ pwErrorMsg }}
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
              <label class="block text-sm font-medium text-text-main mb-1.5"
                >Password Baru <span class="text-red-500">*</span></label
              >
              <div class="relative">
                <input
                  v-model="pwForm.password"
                  :type="showPassword ? 'text' : 'password'"
                  required
                  minlength="8"
                  class="w-full px-3 py-2.5 pr-10 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm"
                  placeholder="Minimal 8 karakter"
                />
                <button
                  type="button"
                  @click="showPassword = !showPassword"
                  class="absolute inset-y-0 right-0 flex items-center pr-3 text-text-secondary hover:text-primary transition-colors"
                >
                  <span class="material-symbols-outlined text-[20px]">{{
                    showPassword ? "visibility_off" : "visibility"
                  }}</span>
                </button>
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-text-main mb-1.5"
                >Konfirmasi Password <span class="text-red-500">*</span></label
              >
              <div class="relative">
                <input
                  v-model="pwForm.password_confirmation"
                  :type="showConfirmPassword ? 'text' : 'password'"
                  required
                  minlength="8"
                  class="w-full px-3 py-2.5 pr-10 border border-border-light rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm"
                  placeholder="Ulangi password baru"
                />
                <button
                  type="button"
                  @click="showConfirmPassword = !showConfirmPassword"
                  class="absolute inset-y-0 right-0 flex items-center pr-3 text-text-secondary hover:text-primary transition-colors"
                >
                  <span class="material-symbols-outlined text-[20px]">{{
                    showConfirmPassword ? "visibility_off" : "visibility"
                  }}</span>
                </button>
              </div>
            </div>
          </div>

          <div class="flex justify-end pt-2">
            <button
              type="submit"
              :disabled="savingPw"
              class="px-6 py-2.5 bg-orange-600 text-white rounded-lg font-medium text-sm hover:bg-orange-700 transition-colors disabled:opacity-50 flex items-center gap-2 shadow-sm"
            >
              <span class="material-symbols-outlined text-[18px]"
                >lock_reset</span
              >
              {{ savingPw ? "Menyimpan..." : "Ubah Password" }}
            </button>
          </div>
        </form>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, computed, reactive, onMounted } from "vue";
import { useAuthStore } from "../../stores/auth";
import api from "../../services/api";

const authStore = useAuthStore();
const loading = ref(true);
const saving = ref(false);
const savingPw = ref(false);
const user = ref(null);
const successMsg = ref("");
const errorMsg = ref("");
const pwSuccessMsg = ref("");
const pwErrorMsg = ref("");
const showPassword = ref(false);
const showConfirmPassword = ref(false);

const form = reactive({
  name: "",
  email: "",
  phone: "",
  address: "",
});

const pwForm = reactive({
  password: "",
  password_confirmation: "",
});

const userInitials = computed(() => {
  const name = user.value?.name || "";
  return name
    .split(" ")
    .map((n) => n[0])
    .join("")
    .substring(0, 2)
    .toUpperCase();
});

const loadUser = async () => {
  try {
    await authStore.fetchUser();
    user.value = authStore.user;
    if (user.value) {
      form.name = user.value.name || "";
      form.email = user.value.email || "";
      form.phone = user.value.phone || "";
      form.address = user.value.address || "";
    }
  } catch (e) {
    user.value = authStore.user;
  } finally {
    loading.value = false;
  }
};

const saveProfile = async () => {
  successMsg.value = "";
  errorMsg.value = "";
  try {
    saving.value = true;
    const response = await api.put("/admin/profile", {
      name: form.name,
      email: form.email,
      phone: form.phone,
      address: form.address,
    });
    if (response.data.success) {
      successMsg.value = "Profil berhasil diperbarui.";
      // Update the auth store
      user.value = response.data.data;
      localStorage.setItem("user", JSON.stringify(response.data.data));
      authStore.user = response.data.data;
    }
  } catch (error) {
    const errors = error.response?.data?.errors;
    if (errors) {
      errorMsg.value = Object.values(errors).flat().join(", ");
    } else {
      errorMsg.value =
        error.response?.data?.message || "Gagal memperbarui profil.";
    }
  } finally {
    saving.value = false;
  }
};

const savePassword = async () => {
  pwSuccessMsg.value = "";
  pwErrorMsg.value = "";

  if (pwForm.password !== pwForm.password_confirmation) {
    pwErrorMsg.value = "Password dan konfirmasi password tidak cocok.";
    return;
  }

  try {
    savingPw.value = true;
    const response = await api.put("/admin/profile", {
      password: pwForm.password,
      password_confirmation: pwForm.password_confirmation,
    });
    if (response.data.success) {
      pwSuccessMsg.value = "Password berhasil diubah.";
      pwForm.password = "";
      pwForm.password_confirmation = "";
    }
  } catch (error) {
    const errors = error.response?.data?.errors;
    if (errors) {
      pwErrorMsg.value = Object.values(errors).flat().join(", ");
    } else {
      pwErrorMsg.value =
        error.response?.data?.message || "Gagal mengubah password.";
    }
  } finally {
    savingPw.value = false;
  }
};

onMounted(() => {
  loadUser();
});
</script>
