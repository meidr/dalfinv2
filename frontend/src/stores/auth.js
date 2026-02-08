import { defineStore } from "pinia";
import authService from "../services/authService";

export const useAuthStore = defineStore("auth", {
  state: () => ({
    user: authService.getStoredUser(),
    token: localStorage.getItem("auth_token"),
    loading: false,
    error: null,
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    currentUser: (state) => state.user,
    userRole: (state) => state.user?.role,
    isAdmin: (state) => state.user?.role === "admin",
    isDosen: (state) => state.user?.role === "dosen",
    isMahasiswa: (state) => state.user?.role === "mahasiswa",

    // Get profile based on role
    profile: (state) => {
      if (!state.user) return null;
      if (state.user.role === "mahasiswa") return state.user.mahasiswa;
      if (state.user.role === "dosen") return state.user.dosen;
      return state.user;
    },
  },

  actions: {
    async login(email, password) {
      this.loading = true;
      this.error = null;
      try {
        const result = await authService.login(email, password);
        if (result.success) {
          this.user = result.data.user;
          this.token = result.data.token;
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
        this.token = null;
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
        this.error =
          error.response?.data?.message || "Gagal mengambil data user";
        throw error;
      } finally {
        this.loading = false;
      }
    },

    clearAuth() {
      this.user = null;
      this.token = null;
      localStorage.removeItem("auth_token");
      localStorage.removeItem("user");
    },
  },
});
