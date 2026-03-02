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

  async downloadOfficialPdf(skripsiId, type) {
    return api.get(`/dosen/bimbingan/${skripsiId}/pdf/${type}`, {
      responseType: "blob",
    });
  },

  // Jadwal
  async getJadwal(params = {}) {
    const response = await api.get("/dosen/jadwal", { params });
    return response.data;
  },

  // Seminar Detail & Nilai
  async getSeminarDetail(seminarId) {
    const response = await api.get(`/dosen/seminar/${seminarId}`);
    return response.data;
  },

  async submitNilai(seminarId, data) {
    const response = await api.put(`/dosen/seminar/${seminarId}/nilai`, data);
    return response.data;
  },

  // Ujian Skripsi Requests
  async getUjianRequests() {
    const response = await api.get("/dosen/ujian-requests");
    return response.data;
  },

  async respondUjianRequest(skripsiId, action, alasan = null) {
    const response = await api.post(
      `/dosen/ujian-requests/${skripsiId}/respond`,
      {
        action,
        alasan,
      },
    );
    return response.data;
  },

  // Informasi Publik
  async getTanggalPenting() {
    const response = await api.get("/public/tanggal-penting");
    return response.data;
  },

  async getPanduan() {
    const response = await api.get("/public/panduan/dosen");
    return response.data;
  },

  async downloadPanduan(id) {
    const response = await api.get(`/public/panduan/${id}/download`, {
      responseType: "blob",
    });
    return response;
  },
};

export default dosenService;
