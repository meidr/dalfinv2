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
    // Note: Laravel can sometimes struggle with PUT + FormData.
    // Usually it's better to POST to /{id} with _method=PUT.
    // Here we assume the frontend code adds _method: PUT if needed,
    // OR we change method to POST here if it is FormData.

    let method = "put";
    let url = `/admin/skripsi/${id}`;

    if (data instanceof FormData) {
      method = "post"; // Use POST for FormData to handle file uploads correctly
      // Ensure _method is set in FormData if not already
      if (!data.has("_method")) {
        data.append("_method", "PUT");
      }
    }

    const response = await api[method](url, data);
    return response.data;
  },

  async deleteSkripsi(id) {
    const response = await api.delete(`/admin/skripsi/${id}`);
    return response.data;
  },

  // Skripsi Verification
  async getSkripsiVerification(params = {}) {
    const response = await api.get("/admin/skripsi-verification", { params });
    return response.data;
  },

  async approveSkripsiVerification(id) {
    const response = await api.post(
      `/admin/skripsi-verification/${id}/approve`,
    );
    return response.data;
  },

  async rejectSkripsiVerification(id) {
    const response = await api.post(`/admin/skripsi-verification/${id}/reject`);
    return response.data;
  },

  async bulkApproveSkripsiVerification(ids) {
    const response = await api.post(
      "/admin/skripsi-verification/bulk-approve",
      { ids },
    );
    return response.data;
  },

  async bulkRejectSkripsiVerification(ids) {
    const response = await api.post("/admin/skripsi-verification/bulk-reject", {
      ids,
    });
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

  async downloadMahasiswaTemplate() {
    const response = await api.get("/admin/mahasiswa-template", {
      responseType: "blob",
    });
    return response;
  },

  async importMahasiswa(file) {
    const formData = new FormData();
    formData.append("file", file);
    const response = await api.post("/admin/mahasiswa-import", formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });
    return response.data;
  },

  async syncMahasiswaPreview() {
    const response = await api.get("/admin/mahasiswa-sync-preview");
    return response.data;
  },

  async syncMahasiswaExecute(data) {
    const response = await api.post("/admin/mahasiswa-sync-execute", data);
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

  async downloadDosenTemplate() {
    const response = await api.get("/admin/dosen-template", {
      responseType: "blob",
    });
    return response;
  },

  async importDosen(file) {
    const formData = new FormData();
    formData.append("file", file);
    const response = await api.post("/admin/dosen-import", formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });
    return response.data;
  },

  async syncDosenPreview() {
    const response = await api.get("/admin/dosen-sync-preview", {
      timeout: 120000,
    });
    return response.data;
  },

  async syncDosenExecute(data) {
    const response = await api.post("/admin/dosen-sync-execute", data, {
      timeout: 120000,
    });
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

  async uploadSeminarProposal(seminarId, file) {
    const formData = new FormData();
    formData.append("file_skripsi", file);
    const response = await api.post(
      `/admin/seminar/${seminarId}/upload-proposal`,
      formData,
      { headers: { "Content-Type": "multipart/form-data" } },
    );
    return response.data;
  },

  // Seminar Hasil
  async getSeminarHasil(params = {}) {
    const response = await api.get("/admin/seminar-hasil", { params });
    return response.data;
  },

  async getSeminarHasilDetail(id) {
    const response = await api.get(`/admin/seminar-hasil/${id}`);
    return response.data;
  },

  async createSeminarHasil(data) {
    const response = await api.post("/admin/seminar-hasil", data);
    return response.data;
  },

  async updateSeminarHasil(id, data) {
    const response = await api.put(`/admin/seminar-hasil/${id}`, data);
    return response.data;
  },

  async deleteSeminarHasil(id) {
    const response = await api.delete(`/admin/seminar-hasil/${id}`);
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

  async updateDokumen(id, data) {
    const response = await api.put(`/admin/dokumen/${id}`, data);
    return response.data;
  },

  async getDokumenResmi(params = {}) {
    const response = await api.get("/admin/dokumen-resmi", { params });
    return response.data;
  },

  async downloadDokumenResmiBatch(data) {
    const response = await api.post("/admin/dokumen-resmi/batch-download", data, {
      responseType: "blob",
      timeout: 120000,
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
  async getEligibleSidang(params = {}) {
    const response = await api.get("/admin/ujian/eligible", { params });
    return response.data;
  },

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

  async deleteUjian(id) {
    const response = await api.delete(`/admin/ujian/${id}`);
    return response.data;
  },

  async getAvailablePenguji(ujianId, params = {}) {
    const response = await api.get(
      `/admin/ujian/${ujianId}/available-penguji`,
      { params },
    );
    return response.data;
  },

  async getSkPengujiPdf(seminarId, data = null) {
    if (data) {
      const response = await api.post(
        `/admin/pdf/sk-penguji/${seminarId}`,
        data,
        {
          responseType: "blob",
        },
      );
      return response;
    }
    const response = await api.get(`/admin/pdf/sk-penguji/${seminarId}`, {
      responseType: "blob",
    });
    return response;
  },

  async getJadwalUjianPdf(params = {}) {
    const response = await api.post("/admin/pdf/jadwal-ujian", params, {
      responseType: "blob",
    });
    return response;
  },

  // Berita Acara
  async getBeritaAcara(params = {}) {
    const response = await api.get("/admin/berita-acara", { params });
    return response.data;
  },

  async generateBeritaAcara(seminarId) {
    const response = await api.post(
      `/admin/berita-acara/${seminarId}/generate`,
      {},
      { responseType: "blob" },
    );
    return response;
  },

  async getBeritaAcaraPdf(seminarId) {
    const response = await api.get(`/admin/berita-acara/${seminarId}/pdf`, {
      responseType: "blob",
    });
    return response;
  },

  async exportBeritaAcaraExcel(params = {}) {
    const response = await api.get("/admin/berita-acara/export-excel", {
      params,
      responseType: "blob",
    });
    return response;
  },

  // Nota Bimbingan
  async getNotaBimbingan(params = {}) {
    const response = await api.get("/admin/nota-bimbingan", { params });
    return response.data;
  },

  async exportNotaBimbingan(params = {}) {
    const response = await api.get("/admin/nota-bimbingan/export", {
      params,
      responseType: "blob",
    });
    return response;
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

  async exportSKYudisium(params = {}) {
    const response = await api.get("/admin/sk-yudisium/export-excel", {
      params,
      responseType: "blob",
    });
    return response;
  },

  async exportRekapYudisiumPdf(params = {}) {
    const response = await api.post("/admin/pdf/rekap-yudisium", params, {
      responseType: "blob",
    });
    return response;
  },

  async generateSKYudisiumPdf(skripsiId) {
    const response = await api.get(`/admin/pdf/sk-yudisium/${skripsiId}`, {
      responseType: "blob",
    });
    return response;
  },

  // SK Yudisium Batch
  async getSKYudisiumBatch(params = {}) {
    const response = await api.get("/admin/sk-yudisium-batch", { params });
    return response.data;
  },

  async createSKYudisiumBatch(data) {
    const response = await api.post("/admin/sk-yudisium-batch", data);
    return response.data;
  },

  async getSKYudisiumBatchDetail(nomor, params = {}) {
    const response = await api.get(
      `/admin/sk-yudisium-batch/${encodeURIComponent(nomor)}`,
      { params },
    );
    return response.data;
  },

  async assignSKYudisiumBatch(data) {
    const response = await api.post("/admin/sk-yudisium-batch/assign", data);
    return response.data;
  },

  async removeSKYudisiumBatch(id) {
    const response = await api.delete(`/admin/sk-yudisium-batch/${id}/remove`);
    return response.data;
  },

  async destroySKYudisiumBatch(nomor) {
    const response = await api.delete(
      `/admin/sk-yudisium-batch/${encodeURIComponent(nomor)}/destroy`,
    );
    return response.data;
  },

  async updateSKYudisiumBatch(nomor, data) {
    const response = await api.put(
      `/admin/sk-yudisium-batch/${encodeURIComponent(nomor)}/update`,
      data,
    );
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

  async toggleUserStatus(id) {
    const response = await api.post(`/admin/users/${id}/toggle-status`);
    return response.data;
  },

  async resetPasswordPengguna(id) {
    const response = await api.post(`/admin/users/${id}/reset-password`);
    return response.data;
  },

  // Configuration
  async getSkTugasSignerConfig() {
    const response = await api.get("/admin/configuration/sk-tugas-signer");
    return response.data;
  },

  async saveSkTugasSignerConfig(data) {
    const response = await api.post(
      "/admin/configuration/sk-tugas-signer",
      data,
    );
    return response.data;
  },

  async getSyaratBimbingan() {
    const response = await api.get("/admin/configuration/syarat-bimbingan");
    return response.data;
  },

  async saveSyaratBimbingan(data) {
    const response = await api.post(
      "/admin/configuration/syarat-bimbingan",
      data,
    );
    return response.data;
  },

  async getKuotaBimbingan() {
    const response = await api.get("/admin/configuration/kuota-bimbingan");
    return response.data;
  },

  async saveKuotaBimbingan(data) {
    const response = await api.post(
      "/admin/configuration/kuota-bimbingan",
      data,
    );
    return response.data;
  },

  // Jenis Tanda Tangan
  async getJenisTtd() {
    const response = await api.get("/admin/configuration/jenis-ttd");
    return response.data;
  },

  async saveJenisTtd(data) {
    const response = await api.post("/admin/configuration/jenis-ttd", data);
    return response.data;
  },

  // Tanggal Penting
  async getTanggalPenting() {
    const response = await api.get("/admin/configuration/tanggal-penting");
    return response.data;
  },

  async saveTanggalPenting(data) {
    const response = await api.post(
      "/admin/configuration/tanggal-penting",
      data,
    );
    return response.data;
  },

  // Panduan Management
  async getPanduanList(type) {
    const response = await api.get(`/admin/configuration/panduan/${type}`);
    return response.data;
  },

  async uploadPanduan(type, formData) {
    const response = await api.post(
      `/admin/configuration/panduan/${type}`,
      formData,
      { headers: { "Content-Type": "multipart/form-data" } },
    );
    return response.data;
  },

  async deletePanduan(id) {
    const response = await api.delete(`/admin/configuration/panduan/${id}`);
    return response.data;
  },

  // Staff Panduan (public endpoint)
  async getStaffPanduan() {
    const response = await api.get("/public/panduan/staff");
    return response.data;
  },

  async downloadStaffPanduan(id) {
    const response = await api.get(`/public/panduan/${id}/download`, {
      responseType: "blob",
    });
    return response;
  },

  // Notifications
  async getNotifications() {
    const response = await api.get("/admin/notifications");
    return response.data;
  },

  async getUnreadNotificationCount() {
    const response = await api.get("/admin/notifications/unread-count");
    return response.data;
  },

  async markNotificationRead(id) {
    const response = await api.put(`/admin/notifications/${id}/read`);
    return response.data;
  },

  async markAllNotificationsRead() {
    const response = await api.put("/admin/notifications/read-all");
    return response.data;
  },

  // Master Data
  // Fakultas
  async getFakultas(params = {}) {
    const response = await api.get("/admin/fakultas", { params });
    return response.data;
  },

  async createFakultas(data) {
    const response = await api.post("/admin/fakultas", data);
    return response.data;
  },

  async updateFakultas(id, data) {
    const response = await api.put(`/admin/fakultas/${id}`, data);
    return response.data;
  },

  async deleteFakultas(id) {
    const response = await api.delete(`/admin/fakultas/${id}`);
    return response.data;
  },

  // Prodi
  async getProdi(params = {}) {
    const response = await api.get("/admin/prodi", { params });
    return response.data;
  },

  async createProdi(data) {
    const response = await api.post("/admin/prodi", data);
    return response.data;
  },

  async updateProdi(id, data) {
    const response = await api.put(`/admin/prodi/${id}`, data);
    return response.data;
  },

  async deleteProdi(id) {
    const response = await api.delete(`/admin/prodi/${id}`);
    return response.data;
  },

  // Nomor Surat
  async getNomorSuratTemplates() {
    const response = await api.get("/admin/nomor-surat");
    return response.data;
  },

  async updateNomorSuratTemplate(id, data) {
    const response = await api.put(`/admin/nomor-surat/${id}`, data);
    return response.data;
  },

  // Tahun
  async getTahun(params = {}) {
    const response = await api.get("/admin/tahun", { params });
    return response.data;
  },

  async createTahun(data) {
    const response = await api.post("/admin/tahun", data);
    return response.data;
  },

  async updateTahun(id, data) {
    const response = await api.put(`/admin/tahun/${id}`, data);
    return response.data;
  },

  async deleteTahun(id) {
    const response = await api.delete(`/admin/tahun/${id}`);
    return response.data;
  },

  // Jabatan
  async getJabatan(params = {}) {
    const response = await api.get("/admin/jabatan", { params });
    return response.data;
  },

  async createJabatan(data) {
    const response = await api.post("/admin/jabatan", data);
    return response.data;
  },

  async updateJabatan(id, data) {
    const response = await api.put(`/admin/jabatan/${id}`, data);
    return response.data;
  },

  async deleteJabatan(id) {
    const response = await api.delete(`/admin/jabatan/${id}`);
    return response.data;
  },

  // =====================
  // Super Admin
  // =====================
  async getActivityLogs(params = {}) {
    const response = await api.get("/super-admin/activity-logs", { params });
    return response.data;
  },

  async getSuperAdminUsers(params = {}) {
    const response = await api.get("/super-admin/users", { params });
    return response.data;
  },

  async updateUser(userId, data) {
    const response = await api.put(`/admin/users/${userId}`, data);
    return response.data;
  },

  async impersonateUser(userId) {
    const response = await api.post(`/super-admin/impersonate/${userId}`);
    return response.data;
  },

  async stopImpersonate() {
    const response = await api.post("/super-admin/stop-impersonate");
    return response.data;
  },

  async forceLogoutAll() {
    const response = await api.post("/super-admin/force-logout-all");
    return response.data;
  },

  async toggleSystemLock(message = null) {
    const response = await api.post("/super-admin/toggle-system-lock", {
      message,
    });
    return response.data;
  },

  async getSystemStatus() {
    const response = await api.get("/super-admin/system-status");
    return response.data;
  },

  async getModuleSettings() {
    const response = await api.get("/module-settings");
    return response.data;
  },

  async toggleSemhas() {
    const response = await api.post("/super-admin/toggle-semhas");
    return response.data;
  },

  async getTrashedRecords(params = {}) {
    const response = await api.get("/super-admin/trashed", { params });
    return response.data;
  },

  // Periode Jabatan
  async getPeriodeJabatan(params = {}) {
    const response = await api.get("/super-admin/periode-jabatan", { params });
    return response.data;
  },

  async createPeriodeJabatan(data) {
    const response = await api.post("/super-admin/periode-jabatan", data);
    return response.data;
  },

  async updatePeriodeJabatan(id, data) {
    const response = await api.put(`/super-admin/periode-jabatan/${id}`, data);
    return response.data;
  },

  async deletePeriodeJabatan(id) {
    const response = await api.delete(`/super-admin/periode-jabatan/${id}`);
    return response.data;
  },

  // Jabatan Pejabat
  async getJabatanPejabat(params = {}) {
    const response = await api.get("/super-admin/jabatan-pejabat", { params });
    return response.data;
  },

  async createJabatanPejabat(data) {
    const response = await api.post("/super-admin/jabatan-pejabat", data);
    return response.data;
  },

  async updateJabatanPejabat(id, data) {
    const response = await api.put(`/super-admin/jabatan-pejabat/${id}`, data);
    return response.data;
  },

  async deleteJabatanPejabat(id) {
    const response = await api.delete(`/super-admin/jabatan-pejabat/${id}`);
    return response.data;
  },

  async resolveJabatanPejabat(params) {
    const response = await api.get("/super-admin/jabatan-pejabat-resolve", {
      params,
    });
    return response.data;
  },

  // Tanda Tangan
  async getTandaTangan(params = {}) {
    const response = await api.get("/super-admin/tanda-tangan", { params });
    return response.data;
  },

  async createTandaTangan(formData) {
    const response = await api.post("/super-admin/tanda-tangan", formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });
    return response.data;
  },

  async updateTandaTangan(id, formData) {
    formData.append("_method", "PUT");
    const response = await api.post(
      `/super-admin/tanda-tangan/${id}`,
      formData,
      {
        headers: { "Content-Type": "multipart/form-data" },
      },
    );
    return response.data;
  },

  async deleteTandaTangan(id) {
    const response = await api.delete(`/super-admin/tanda-tangan/${id}`);
    return response.data;
  },
};

export default adminService;
