import { defineStore } from "pinia";
import authService from "../services/authService";
import adminService from "../services/adminService";

export const useAuthStore = defineStore("auth", {
  state: () => ({
    user: authService.getStoredUser(),
    loading: false,
    error: null,
    semhasEnabled: JSON.parse(localStorage.getItem("semhas_enabled") ?? "true"),
  }),

  getters: {
    isAuthenticated: (state) => !!state.user,
    currentUser: (state) => state.user,
    userRole: (state) => state.user?.role,
    isAdmin: (state) =>
      state.user?.role === "admin" || state.user?.role === "super_admin",
    isSuperAdmin: (state) => state.user?.role === "super_admin",
    isDosen: (state) => state.user?.role === "dosen",
    isMahasiswa: (state) => state.user?.role === "mahasiswa",
    isStaff: (state) => state.user?.role === "staff",

    // Get profile based on role
    profile: (state) => {
      if (!state.user) return null;
      if (state.user.role === "mahasiswa") return state.user.mahasiswa;
      if (state.user.role === "dosen") return state.user.dosen;
      return state.user;
    },
  },

  actions: {
    async login(username, password) {
      this.loading = true;
      this.error = null;
      try {
        const result = await authService.login(username, password);
        if (result.success) {
          this.user = result.data.user;
        }
        return result;
      } catch (error) {
        this.error = error.response?.data?.message || "Login gagal";
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async logout() {
      this.loading = true;
      try {
        await authService.logout();
      } finally {
        this.user = null;
        this.loading = false;
      }
    },

    async fetchUser() {
      this.loading = true;
      try {
        const result = await authService.getUser();
        if (result.success) {
          this.user = result.data;
          localStorage.setItem("user", JSON.stringify(result.data));
        }
        return result;
      } catch (error) {
        // If 401, clear user
        if (error.response?.status === 401) {
          this.user = null;
          localStorage.removeItem("user");
        }
        this.error =
          error.response?.data?.message || "Gagal mengambil data user";
        throw error;
      } finally {
        this.loading = false;
      }
    },

    clearAuth() {
      this.user = null;
      localStorage.removeItem("user");
    },

    async fetchModuleSettings() {
      try {
        const result = await adminService.getModuleSettings();
        if (result.success) {
          this.semhasEnabled = result.data.semhas_enabled;
          localStorage.setItem(
            "semhas_enabled",
            JSON.stringify(this.semhasEnabled),
          );
        }
      } catch (error) {
        console.error("Failed to fetch module settings:", error);
      }
    },
  },
});
