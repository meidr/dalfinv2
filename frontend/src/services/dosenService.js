import api from "./api";

export const dosenService = {
  // Dashboard
  async getDashboard() {
    const response = await api.get("/dosen/dashboard");
    return response.data;
  },

  // Bimbingan
  async getBimbinganList(params = {}) {
    const response = await api.get("/dosen/bimbingan", { params });
    return response.data;
  },

  async getBimbinganDetail(skripsiId) {
    const response = await api.get(`/dosen/bimbingan/${skripsiId}`);
    return response.data;
  },

  async getBimbinganLogs(skripsiId) {
    const response = await api.get(`/dosen/bimbingan/${skripsiId}/logs`);
    return response.data;
  },

  async updateBimbinganStatus(bimbinganId, status, catatanDosen = null) {
    const response = await api.put(
      `/dosen/bimbingan/log/${bimbinganId}/status`,
      {
        status,
        catatan_dosen: catatanDosen,
      },
    );
    return response.data;
  },

  async approveBimbingan(bimbinganId, catatanDosen = null) {
    return this.updateBimbinganStatus(bimbinganId, "approved", catatanDosen);
  },

  async requestRevision(bimbinganId, catatanDosen) {
    return this.updateBimbinganStatus(bimbinganId, "revision", catatanDosen);
  },

  async rejectBimbingan(bimbinganId, catatanDosen) {
    return this.updateBimbinganStatus(bimbinganId, "rejected", catatanDosen);
  },
};

export default dosenService;
