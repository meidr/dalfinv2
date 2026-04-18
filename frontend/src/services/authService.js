import api, { BASE_URL_API } from "./api";

export const authService = {
  async login(username, password) {
    try {
      // 🔥 FIX: HARUS KE DOMAIN BACKEND (BUKAN FRONTEND)
      await api.get("/sanctum/csrf-cookie", {
        baseURL: BASE_URL_API,
        withCredentials: true,
      });

    } catch (e) {
      console.error("CSRF ERROR:", e);
      throw e;
    }

    const response = await api.post(
      "/auth/login",
      { username, password },
      {
        withCredentials: true,
      }
    );

    if (response.data.success) {
      localStorage.setItem("user", JSON.stringify(response.data.data.user));
    }

    return response.data;
  },

  async logout() {
    try {
      await api.post("/auth/logout", {}, { withCredentials: true });
    } finally {
      localStorage.removeItem("user");
    }
  },

  async getUser() {
    const response = await api.get("/auth/user", {
      withCredentials: true,
    });
    return response.data;
  },

  async updateProfile(data) {
    const response = await api.put("/auth/profile", data, {
      withCredentials: true,
    });
    return response.data;
  },

  async changePassword(currentPassword, password, passwordConfirmation) {
    const response = await api.put(
      "/auth/password",
      {
        current_password: currentPassword,
        password,
        password_confirmation: passwordConfirmation,
      },
      { withCredentials: true }
    );
    return response.data;
  },

  isAuthenticated() {
    return !!localStorage.getItem("user");
  },

  getStoredUser() {
    const user = localStorage.getItem("user");
    return user ? JSON.parse(user) : null;
  },

  getRole() {
    const user = this.getStoredUser();
    return user?.role || null;
  },
};

export default authService;