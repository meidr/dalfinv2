import axios from "axios";
import NProgress from "nprogress";

const BASE_URL = "https://dalfinapp.uiidalwa.web.id";

// Helper ambil cookie
function getCookie(name) {
  const value = `; ${document.cookie}`;
  const parts = value.split(`; ${name}=`);
  if (parts.length === 2) {
    return decodeURIComponent(parts.pop().split(";").shift());
  }
  return null;
}

// Create axios instance
const api = axios.create({
  baseURL: BASE_URL + "/api",
  headers: {
    Accept: "application/json",
  },
  withCredentials: true,
});

// 🔥 WAJIB untuk Sanctum
api.defaults.withCredentials = true;
api.defaults.xsrfCookieName = "XSRF-TOKEN";
api.defaults.xsrfHeaderName = "X-XSRF-TOKEN";

// NProgress configuration
NProgress.configure({ showSpinner: false });

let activeRequests = 0;
let progressTimeout = null;

export const startProgress = () => {
  if (progressTimeout) {
    clearTimeout(progressTimeout);
    progressTimeout = null;
  }
  if (activeRequests === 0) {
    NProgress.start();
  }
  activeRequests++;
};

export const stopProgress = () => {
  activeRequests--;
  if (activeRequests <= 0) {
    activeRequests = 0;
    progressTimeout = setTimeout(() => {
      NProgress.done();
      progressTimeout = null;
    }, 200);
  }
};

// 🔥 REQUEST INTERCEPTOR (FIX UTAMA ADA DI SINI)
api.interceptors.request.use(
  (config) => {
    if (!config.skipProgress) startProgress();

    // 🔥 Ambil CSRF token manual
    const xsrfToken = getCookie("XSRF-TOKEN");
    if (xsrfToken) {
      config.headers["X-XSRF-TOKEN"] = xsrfToken;
    }

    // Optional Bearer token
    const token = localStorage.getItem("auth_token");
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }

    return config;
  },
  (error) => {
    stopProgress();
    return Promise.reject(error);
  }
);

// RESPONSE INTERCEPTOR
api.interceptors.response.use(
  (response) => {
    if (!response.config?.skipProgress) stopProgress();
    return response;
  },
  (error) => {
    if (!error.config?.skipProgress) stopProgress();

    if (error.response?.status === 401) {
      localStorage.removeItem("auth_token");
      localStorage.removeItem("user");
      window.location.href = "/login";
    }

    return Promise.reject(error);
  }
);

export default api;
export const BASE_URL_API = BASE_URL;