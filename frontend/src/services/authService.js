import api from "./api";

export const authService = {
  /**
   * Login with email/NIM/NIP and password
   */
  async login(email, password) {
    // Fetch CSRF token first
    try {
      await api.get("/sanctum/csrf-cookie");
      console.log("CSRF Cookie fetch successful. Cookies:", document.cookie);
    } catch (e) {
      console.error("CSRF Cookie fetch failed:", e);
    }

    const response = await api.post("/auth/login", { email, password });
    if (response.data.success) {
      localStorage.setItem("auth_token", response.data.data.token);
      localStorage.setItem("user", JSON.stringify(response.data.data.user));
    }
    return response.data;
  },

  /**
   * Logout current user
   */
  async logout() {
    try {
      await api.post("/auth/logout");
    } finally {
      localStorage.removeItem("auth_token");
      localStorage.removeItem("user");
    }
  },

  /**
   * Get current user profile
   */
  async getUser() {
    const response = await api.get("/auth/user");
    return response.data;
  },

  /**
   * Update profile
   */
  async updateProfile(data) {
    const response = await api.put("/auth/profile", data);
    return response.data;
  },

  /**
   * Change password
   */
  async changePassword(currentPassword, password, passwordConfirmation) {
    const response = await api.put("/auth/password", {
      current_password: currentPassword,
      password: password,
      password_confirmation: passwordConfirmation,
    });
    return response.data;
  },

  /**
   * Check if user is logged in
   */
  isAuthenticated() {
    return !!localStorage.getItem("auth_token");
  },

  /**
   * Get stored user
   */
  getStoredUser() {
    const user = localStorage.getItem("user");
    return user ? JSON.parse(user) : null;
  },

  /**
   * Get user role
   */
  getRole() {
    const user = this.getStoredUser();
    return user?.role || null;
  },
};

export default authService;
