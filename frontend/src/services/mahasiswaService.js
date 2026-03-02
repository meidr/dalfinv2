import api from "./api";

export const mahasiswaService = {
  // Dashboard
  async getDashboard() {
    const response = await api.get("/mahasiswa/dashboard");
    return response.data;
  },

  // Skripsi
  async getSkripsiList() {
    const response = await api.get("/mahasiswa/skripsi");
    return response.data;
  },

  async getSkripsiDetail() {
    const response = await api.get("/mahasiswa/skripsi/detail");
    return response.data;
  },

  async getSkripsiDetailById(id) {
    const response = await api.get(`/mahasiswa/skripsi/${id}/detail`);
    return response.data;
  },

  async createSkripsi(data) {
    const response = await api.post("/mahasiswa/skripsi", data);
    return response.data;
  },

  async updateSkripsi(data) {
    const response = await api.put("/mahasiswa/skripsi", data);
    return response.data;
  },

  // Bimbingan
  async getBimbinganLogs() {
    const response = await api.get("/mahasiswa/skripsi/bimbingan");
    return response.data;
  },

  async addBimbingan(data) {
    const formData = new FormData();
    formData.append("tanggal", data.tanggal);
    formData.append("topik", data.topik);
    formData.append("dosen_id", data.dosen_id);
    if (data.deskripsi) formData.append("deskripsi", data.deskripsi);
    if (data.file_bukti) formData.append("file_bukti", data.file_bukti);

    const response = await api.post("/mahasiswa/skripsi/bimbingan", formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });
    return response.data;
  },

  // Dokumen
  async getDokumen(params = {}) {
    const response = await api.get("/mahasiswa/skripsi/dokumen", { params });
    return response.data;
  },

  async uploadDokumen(data) {
    const formData = new FormData();
    formData.append("jenis", data.jenis);
    formData.append("file", data.file);
    if (data.catatan) formData.append("catatan", data.catatan);

    const response = await api.post("/mahasiswa/skripsi/dokumen", formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });
    return response.data;
  },

  async deleteDokumen(id) {
    const response = await api.delete(`/mahasiswa/skripsi/dokumen/${id}`);
    return response.data;
  },

  async downloadOfficialPdf(type) {
    const response = await api.get(`/mahasiswa/skripsi/pdf/${type}`, {
      responseType: "blob",
    });
    return response;
  },

  // Ujian Skripsi
  async checkUjianEligibility() {
    const response = await api.get("/mahasiswa/skripsi/ujian-eligibility");
    return response.data;
  },

  async requestUjian() {
    const response = await api.post("/mahasiswa/skripsi/request-ujian");
    return response.data;
  },

  // Informasi Publik
  async getTanggalPenting() {
    const response = await api.get("/public/tanggal-penting");
    return response.data;
  },

  async getPanduan() {
    const response = await api.get("/public/panduan/mahasiswa");
    return response.data;
  },

  async downloadPanduan(id) {
    const response = await api.get(`/public/panduan/${id}/download`, {
      responseType: "blob",
    });
    return response;
  },
};

export default mahasiswaService;
