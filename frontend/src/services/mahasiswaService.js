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
};

export default mahasiswaService;
