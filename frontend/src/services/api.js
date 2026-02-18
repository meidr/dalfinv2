import axios from "axios";
import NProgress from "nprogress";

const API_URL = import.meta.env.VITE_API_URL || "http://localhost:8000/api";

// Create axios instance
const api = axios.create({
  baseURL: API_URL,
  headers: {
    Accept: "application/json",
  },
  withCredentials: true,
});

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

// Request interceptor to start progress bar and add auth token
api.interceptors.request.use(
  (config) => {
    if (!config.skipProgress) {
      startProgress();
    }
    // If we have a token (e.g. from impersonation), use it
    const token = localStorage.getItem("auth_token");
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    if (!error.config?.skipProgress) {
      stopProgress();
    }
    return Promise.reject(error);
  },
);

// Response interceptor to handle errors and stop progress bar
api.interceptors.response.use(
  (response) => {
    if (!response.config?.skipProgress) {
      stopProgress();
    }
    return response;
  },
  (error) => {
    if (!error.config?.skipProgress) {
      stopProgress();
    }
    if (error.response?.status === 401) {
      // Token expired or invalid
      localStorage.removeItem("auth_token");
      localStorage.removeItem("user");
      window.location.href = "/login";
    }
    return Promise.reject(error);
  },
);

export default api;
