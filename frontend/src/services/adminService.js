import api from "./api";

export const adminService = {
  // Dashboard
  async getDashboard() {
    const response = await api.get("/admin/dashboard");
    return response.data;
  },

  // Skripsi
  async getSkripsi(params = {}) {
    const response = await api.get("/admin/skripsi", { params });
    return response.data;
  },

  async getSkripsiDetail(id) {
    const response = await api.get(`/admin/skripsi/${id}`);
    return response.data;
  },

  async createSkripsi(data) {
    const response = await api.post("/admin/skripsi", data);
    return response.data;
  },

  async updateSkripsi(id, data) {
    const response = await api.put(`/admin/skripsi/${id}`, data);
    return response.data;
  },

  async deleteSkripsi(id) {
    const response = await api.delete(`/admin/skripsi/${id}`);
    return response.data;
  },

  // Pembimbing
  async getPendingPembimbing(params = {}) {
    const response = await api.get("/admin/pembimbing", { params });
    return response.data;
  },

  async getAvailableDosen(params = {}) {
    const response = await api.get("/admin/pembimbing/available-dosen", {
      params,
    });
    return response.data;
  },

  async assignPembimbing(data) {
    const response = await api.post("/admin/pembimbing", data);
    return response.data;
  },

  // Master Mahasiswa
  async getMahasiswa(params = {}) {
    const response = await api.get("/admin/mahasiswa", { params });
    return response.data;
  },

  async getMahasiswaDetail(id) {
    const response = await api.get(`/admin/mahasiswa/${id}`);
    return response.data;
  },

  async createMahasiswa(data) {
    const response = await api.post("/admin/mahasiswa", data);
    return response.data;
  },

  async updateMahasiswa(id, data) {
    const response = await api.put(`/admin/mahasiswa/${id}`, data);
    return response.data;
  },

  async deleteMahasiswa(id) {
    const response = await api.delete(`/admin/mahasiswa/${id}`);
    return response.data;
  },

  // Master Dosen
  async getDosen(params = {}) {
    const response = await api.get("/admin/dosen", { params });
    return response.data;
  },

  async getDosenDetail(id) {
    const response = await api.get(`/admin/dosen/${id}`);
    return response.data;
  },

  async createDosen(data) {
    const response = await api.post("/admin/dosen", data);
    return response.data;
  },

  async updateDosen(id, data) {
    const response = await api.put(`/admin/dosen/${id}`, data);
    return response.data;
  },

  async deleteDosen(id) {
    const response = await api.delete(`/admin/dosen/${id}`);
    return response.data;
  },

  // Seminar
  async getSeminar(params = {}) {
    const response = await api.get("/admin/seminar", { params });
    return response.data;
  },

  async getSeminarDetail(id) {
    const response = await api.get(`/admin/seminar/${id}`);
    return response.data;
  },

  async createSeminar(data) {
    const response = await api.post("/admin/seminar", data);
    return response.data;
  },

  async updateSeminar(id, data) {
    const response = await api.put(`/admin/seminar/${id}`, data);
    return response.data;
  },

  async deleteSeminar(id) {
    const response = await api.delete(`/admin/seminar/${id}`);
    return response.data;
  },

  // Dokumen
  async getDokumen(params = {}) {
    const response = await api.get("/admin/dokumen", { params });
    return response.data;
  },

  async uploadDokumen(data) {
    const response = await api.post("/admin/dokumen", data, {
      headers: { "Content-Type": "multipart/form-data" },
    });
    return response.data;
  },

  async downloadDokumen(id) {
    const response = await api.get(`/admin/dokumen/${id}/download`, {
      responseType: "blob",
    });
    return response;
  },

  // PDF Generation
  async getSkTugasPdf(skripsiId) {
    const response = await api.get(`/admin/pdf/sk-tugas/${skripsiId}`, {
      responseType: "blob",
    });
    return response;
  },

  async getNotaBimbinganPdf(skripsiId) {
    const response = await api.get(`/admin/pdf/nota-bimbingan/${skripsiId}`, {
      responseType: "blob",
    });
    return response;
  },

  async getBeritaAcaraPdf(seminarId) {
    const response = await api.get(`/admin/pdf/berita-acara/${seminarId}`, {
      responseType: "blob",
    });
    return response;
  },

  // Assign Pembimbing (fixed)
  async assignPembimbing(skripsiId, data) {
    const response = await api.post(`/admin/pembimbing`, {
      skripsi_id: skripsiId,
      ...data,
    });
    return response.data;
  },

  // Bimbingan
  async getBimbingan(params = {}) {
    const response = await api.get("/admin/bimbingan", { params });
    return response.data;
  },

  async getBimbinganDetail(skripsiId) {
    const response = await api.get(`/admin/bimbingan/${skripsiId}`);
    return response.data;
  },

  // Ujian
  async getUjian(params = {}) {
    const response = await api.get("/admin/ujian", { params });
    return response.data;
  },

  async getUjianDetail(id) {
    const response = await api.get(`/admin/ujian/${id}`);
    return response.data;
  },

  async createUjian(data) {
    const response = await api.post("/admin/ujian", data);
    return response.data;
  },

  async updateUjian(id, data) {
    const response = await api.put(`/admin/ujian/${id}`, data);
    return response.data;
  },

  // Berita Acara
  async getBeritaAcara(params = {}) {
    const response = await api.get("/admin/berita-acara", { params });
    return response.data;
  },

  // Nota Bimbingan
  async getNotaBimbingan(params = {}) {
    const response = await api.get("/admin/nota-bimbingan", { params });
    return response.data;
  },

  // SK Tugas
  async getSKTugas(params = {}) {
    const response = await api.get("/admin/sk-tugas", { params });
    return response.data;
  },

  async updateSKTugas(id, data) {
    const response = await api.put(`/admin/sk-tugas/${id}`, data);
    return response.data;
  },

  // SK Yudisium
  async getSKYudisium(params = {}) {
    const response = await api.get("/admin/sk-yudisium", { params });
    return response.data;
  },

  async createSKYudisium(data) {
    const response = await api.post("/admin/sk-yudisium", data);
    return response.data;
  },

  // Pengguna (Users)
  async getPengguna(params = {}) {
    const response = await api.get("/admin/users", { params });
    return response.data;
  },

  async getPenggunaDetail(id) {
    const response = await api.get(`/admin/users/${id}`);
    return response.data;
  },

  async createPengguna(data) {
    const response = await api.post("/admin/users", data);
    return response.data;
  },

  async updatePengguna(id, data) {
    const response = await api.put(`/admin/users/${id}`, data);
    return response.data;
  },

  async deletePengguna(id) {
    const response = await api.delete(`/admin/users/${id}`);
    return response.data;
  },

  async resetPasswordPengguna(id) {
    const response = await api.post(`/admin/users/${id}/reset-password`);
    return response.data;
  },
};

export default adminService;
