<template>
  <div class="verify-page">
    <!-- Animated Background -->
    <div class="bg-gradient"></div>
    <div class="bg-pattern"></div>

    <!-- Header -->
    <header class="verify-header">
      <div class="header-content">
        <div class="logo-area">
          <img :src="logoUrl" alt="DALFIN" class="header-logo" />
          <div class="header-divider"></div>
          <div class="header-text">
            <span class="header-title">Verifikasi Dokumen</span>
            <span class="header-subtitle">Digital Authentication System</span>
          </div>
        </div>
        <div class="institution-badge">
          <svg
            width="14"
            height="14"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
          >
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
          </svg>
          <span>UII Darullughah Wadda'wah</span>
        </div>
      </div>
    </header>

    <!-- Loading -->
    <div v-if="loading" class="state-container">
      <div class="glass-card loading-card">
        <div class="pulse-ring"></div>
        <div class="spinner"></div>
        <p class="loading-text">
          Memverifikasi keaslian dokumen<span class="dots"></span>
        </p>
        <p class="loading-sub">Mohon tunggu sebentar</p>
      </div>
    </div>

    <!-- Not Found -->
    <div v-else-if="error" class="state-container">
      <div class="glass-card error-card">
        <div class="error-icon-wrap">
          <svg
            width="48"
            height="48"
            viewBox="0 0 24 24"
            fill="none"
            stroke-width="1.5"
          >
            <circle cx="12" cy="12" r="10" stroke="url(#errorGrad)" />
            <line x1="15" y1="9" x2="9" y2="15" stroke="url(#errorGrad)" />
            <line x1="9" y1="9" x2="15" y2="15" stroke="url(#errorGrad)" />
            <defs>
              <linearGradient id="errorGrad" x1="0" y1="0" x2="24" y2="24">
                <stop offset="0%" stop-color="#f56565" />
                <stop offset="100%" stop-color="#e53e3e" />
              </linearGradient>
            </defs>
          </svg>
        </div>
        <h2>Dokumen Tidak Ditemukan</h2>
        <p>
          Token verifikasi tidak valid atau dokumen tidak ditemukan dalam
          sistem.
        </p>
        <div class="error-hint">
          <svg
            width="14"
            height="14"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
          >
            <circle cx="12" cy="12" r="10" />
            <line x1="12" y1="16" x2="12" y2="12" />
            <line x1="12" y1="8" x2="12.01" y2="8" />
          </svg>
          <span
            >Pastikan QR code yang di-scan adalah QR code yang benar dan masih
            berlaku</span
          >
        </div>
      </div>
    </div>

    <!-- Verified Content -->
    <div v-else-if="doc" class="verified-container">
      <!-- Verified Badge -->
      <div class="verified-badge" :class="{ 'animate-in': !loading }">
        <div class="badge-glow"></div>
        <div class="badge-content">
          <div class="badge-icon">
            <svg
              width="22"
              height="22"
              viewBox="0 0 24 24"
              fill="none"
              stroke="white"
              stroke-width="2.5"
            >
              <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
              <polyline points="22 4 12 14.01 9 11.01" />
            </svg>
          </div>
          <div class="badge-text">
            <span class="badge-title">Dokumen Terverifikasi</span>
            <span class="badge-desc"
              >Dokumen ini sah dan tercatat dalam sistem</span
            >
          </div>
        </div>
      </div>

      <!-- Detail Card -->
      <div class="glass-card detail-card">
        <div class="card-header">
          <div class="card-icon">
            <svg
              width="18"
              height="18"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <path
                d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"
              />
              <polyline points="14 2 14 8 20 8" />
              <line x1="16" y1="13" x2="8" y2="13" />
              <line x1="16" y1="17" x2="8" y2="17" />
            </svg>
          </div>
          <h3>Detail Dokumen</h3>
        </div>
        <div class="detail-grid">
          <div
            class="detail-item"
            v-for="(item, i) in detailItems"
            :key="i"
            :style="{ animationDelay: `${i * 60}ms` }"
          >
            <span class="detail-label">{{ item.label }}</span>
            <span class="detail-value">{{ item.value }}</span>
          </div>
        </div>

        <!-- Metadata -->
        <div
          v-if="doc.metadata && Object.keys(doc.metadata).length"
          class="metadata-section"
        >
          <div class="section-divider">
            <span>Informasi Tambahan</span>
          </div>
          <div class="detail-grid">
            <div
              v-for="(val, key) in doc.metadata"
              :key="key"
              class="detail-item"
            >
              <span class="detail-label">{{ formatKey(key) }}</span>
              <span class="detail-value">{{ val }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- PDF Viewer -->
      <div class="glass-card pdf-card">
        <div class="pdf-toolbar">
          <div class="toolbar-left">
            <div class="pdf-badge">PDF</div>
            <span class="toolbar-title">Pratinjau Dokumen</span>
          </div>
          <a :href="pdfUrl" target="_blank" class="download-btn">
            <svg
              width="15"
              height="15"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2.5"
            >
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
              <polyline points="7 10 12 15 17 10" />
              <line x1="12" y1="15" x2="12" y2="3" />
            </svg>
            <span>Unduh</span>
          </a>
        </div>
        <div class="pdf-viewer">
          <object :data="pdfUrl" type="application/pdf" class="pdf-object">
            <div class="pdf-fallback">
              <svg
                width="48"
                height="48"
                viewBox="0 0 24 24"
                fill="none"
                stroke="#94a3b8"
                stroke-width="1.5"
              >
                <path
                  d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"
                />
                <polyline points="14 2 14 8 20 8" />
              </svg>
              <p>PDF tidak dapat ditampilkan di browser ini</p>
              <a :href="pdfUrl" target="_blank" class="fallback-btn"
                >Buka PDF di Tab Baru</a
              >
            </div>
          </object>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <footer class="verify-footer">
      <div class="footer-content">
        <div class="footer-line"></div>
        <p>Sistem Informasi Skripsi · UII Darullughah Wadda'wah</p>
        <p class="footer-sub">Dokumen ini divalidasi secara digital</p>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { useRoute } from "vue-router";
import axios from "axios";

const route = useRoute();
const loading = ref(true);
const error = ref(false);
const doc = ref(null);

const API_URL = import.meta.env.VITE_API_URL || "/api";
const _rawApi = import.meta.env.VITE_API_URL || "";
const BACKEND_URL = _rawApi.startsWith("http")
  ? _rawApi.replace(/\/api\/?$/, "")
  : import.meta.env.DEV
    ? "http://localhost:8000"
    : "";
const logoUrl = `${BACKEND_URL}/images/DALFIN-LOGO.png`;

const pdfUrl = computed(() => {
  if (!doc.value) return "";
  return `${BACKEND_URL}/api/verify/${doc.value.token}/pdf`;
});

const formatDocType = (type) => {
  const map = {
    sk_tugas: "SK Tugas Pembimbing",
    berita_acara: "Berita Acara Seminar",
    sk_penguji: "SK Penguji",
    nota_bimbingan: "Nota Bimbingan",
    sk_yudisium: "SK Yudisium",
  };
  return map[type] || type;
};

const formatKey = (key) => {
  return key.replace(/_/g, " ").replace(/\b\w/g, (c) => c.toUpperCase());
};

const detailItems = computed(() => {
  if (!doc.value) return [];
  const items = [
    { label: "Nama Berkas", value: doc.value.nama_berkas },
    { label: "Jenis Dokumen", value: formatDocType(doc.value.document_type) },
  ];
  if (doc.value.nomor_surat && doc.value.nomor_surat !== "-") {
    items.push({ label: "Nomor Surat", value: doc.value.nomor_surat });
  }
  items.push(
    { label: "Penandatangan", value: doc.value.nama_penandatangan },
    { label: "Jabatan", value: doc.value.jabatan_penandatangan },
    { label: "Tanggal Terbit", value: doc.value.tanggal_terbit },
  );
  return items;
});

onMounted(async () => {
  try {
    const token = route.params.token;
    const res = await axios.get(`${API_URL}/verify/${token}`);
    if (res.data.success) {
      doc.value = res.data.data;
    } else {
      error.value = true;
    }
  } catch (e) {
    error.value = true;
  } finally {
    loading.value = false;
  }
});
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap");

/* ======= Page ======= */
.verify-page {
  min-height: 100vh;
  font-family:
    "Inter",
    -apple-system,
    sans-serif;
  display: flex;
  flex-direction: column;
  position: relative;
  background: #0f172a;
  color: #e2e8f0;
  overflow-x: hidden;
}

/* ======= Animated Background ======= */
.bg-gradient {
  position: fixed;
  inset: 0;
  background:
    radial-gradient(
      ellipse 80% 60% at 20% 10%,
      rgba(56, 189, 248, 0.08) 0%,
      transparent 60%
    ),
    radial-gradient(
      ellipse 60% 50% at 80% 80%,
      rgba(139, 92, 246, 0.06) 0%,
      transparent 60%
    ),
    radial-gradient(
      ellipse 50% 40% at 50% 50%,
      rgba(16, 185, 129, 0.04) 0%,
      transparent 60%
    ),
    linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
  z-index: 0;
}

.bg-pattern {
  position: fixed;
  inset: 0;
  background-image: radial-gradient(
    rgba(148, 163, 184, 0.04) 1px,
    transparent 1px
  );
  background-size: 32px 32px;
  z-index: 0;
}

/* ======= Header ======= */
.verify-header {
  position: sticky;
  top: 0;
  z-index: 50;
  background: rgba(15, 23, 42, 0.8);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border-bottom: 1px solid rgba(148, 163, 184, 0.08);
  padding: 12px 24px;
}

.header-content {
  max-width: 960px;
  margin: 0 auto;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.logo-area {
  display: flex;
  align-items: center;
  gap: 14px;
}

.header-logo {
  height: 42px;
  width: auto;
  object-fit: contain;
  filter: brightness(0) invert(1);
  opacity: 0.95;
}

.header-divider {
  width: 1px;
  height: 30px;
  background: linear-gradient(
    to bottom,
    transparent,
    rgba(148, 163, 184, 0.3),
    transparent
  );
}

.header-text {
  display: flex;
  flex-direction: column;
  gap: 1px;
}

.header-title {
  font-size: 15px;
  font-weight: 600;
  color: #f1f5f9;
  letter-spacing: -0.01em;
}

.header-subtitle {
  font-size: 10px;
  color: #64748b;
  font-weight: 400;
  letter-spacing: 0.5px;
  text-transform: uppercase;
}

.institution-badge {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 11px;
  color: #64748b;
  padding: 6px 12px;
  border: 1px solid rgba(148, 163, 184, 0.1);
  border-radius: 20px;
  background: rgba(148, 163, 184, 0.04);
}

/* ======= Glass Card ======= */
.glass-card {
  background: rgba(30, 41, 59, 0.6);
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
  border: 1px solid rgba(148, 163, 184, 0.08);
  border-radius: 16px;
  overflow: hidden;
  transition: border-color 0.3s ease;
}

.glass-card:hover {
  border-color: rgba(148, 163, 184, 0.15);
}

/* ======= States (Loading & Error) ======= */
.state-container {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 40px 24px;
  position: relative;
  z-index: 1;
}

/* Loading */
.loading-card {
  text-align: center;
  padding: 56px 48px;
  max-width: 400px;
  position: relative;
}

.pulse-ring {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 160px;
  height: 160px;
  border-radius: 50%;
  border: 1px solid rgba(56, 189, 248, 0.1);
  animation: pulse-expand 2s ease-out infinite;
}

@keyframes pulse-expand {
  0% {
    transform: translate(-50%, -50%) scale(0.8);
    opacity: 1;
  }
  100% {
    transform: translate(-50%, -50%) scale(1.5);
    opacity: 0;
  }
}

.spinner {
  width: 36px;
  height: 36px;
  border: 3px solid rgba(148, 163, 184, 0.1);
  border-top: 3px solid #38bdf8;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  margin: 0 auto 20px;
  position: relative;
  z-index: 1;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.loading-text {
  font-size: 14px;
  color: #cbd5e1;
  font-weight: 500;
  margin: 0;
}

.dots::after {
  content: "";
  animation: dots 1.5s steps(3) infinite;
}

@keyframes dots {
  0% {
    content: "";
  }
  33% {
    content: ".";
  }
  66% {
    content: "..";
  }
  100% {
    content: "...";
  }
}

.loading-sub {
  font-size: 12px;
  color: #475569;
  margin: 6px 0 0;
}

/* Error */
.error-card {
  text-align: center;
  padding: 48px 40px;
  max-width: 440px;
}

.error-icon-wrap {
  margin-bottom: 20px;
}

.error-card h2 {
  color: #f1f5f9;
  margin: 0 0 8px;
  font-size: 20px;
  font-weight: 600;
}

.error-card > p {
  color: #94a3b8;
  margin: 0;
  font-size: 14px;
  line-height: 1.6;
}

.error-hint {
  display: flex;
  align-items: flex-start;
  gap: 8px;
  margin-top: 20px;
  padding: 12px 16px;
  background: rgba(148, 163, 184, 0.06);
  border-radius: 10px;
  border: 1px solid rgba(148, 163, 184, 0.06);
  text-align: left;
}

.error-hint svg {
  flex-shrink: 0;
  margin-top: 1px;
  color: #64748b;
}
.error-hint span {
  font-size: 12px;
  color: #64748b;
  line-height: 1.5;
}

/* ======= Verified Container ======= */
.verified-container {
  flex: 1;
  max-width: 960px;
  width: 100%;
  margin: 0 auto;
  padding: 28px 24px;
  position: relative;
  z-index: 1;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

/* Verified Badge */
.verified-badge {
  position: relative;
  border-radius: 14px;
  overflow: hidden;
  background: linear-gradient(
    135deg,
    rgba(16, 185, 129, 0.12) 0%,
    rgba(56, 189, 248, 0.08) 100%
  );
  border: 1px solid rgba(16, 185, 129, 0.2);
  padding: 16px 20px;
  animation: fadeSlideUp 0.5s ease-out;
}

.badge-glow {
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: radial-gradient(
    circle at 30% 50%,
    rgba(16, 185, 129, 0.08) 0%,
    transparent 50%
  );
  animation: glow-shift 6s ease-in-out infinite alternate;
}

@keyframes glow-shift {
  0% {
    transform: translate(0, 0);
  }
  100% {
    transform: translate(10%, 10%);
  }
}

.badge-content {
  display: flex;
  align-items: center;
  gap: 14px;
  position: relative;
  z-index: 1;
}

.badge-icon {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  background: linear-gradient(135deg, #10b981, #059669);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
}

.badge-title {
  font-size: 15px;
  font-weight: 600;
  color: #ecfdf5;
  display: block;
}

.badge-desc {
  font-size: 12px;
  color: #6ee7b7;
  display: block;
  margin-top: 2px;
}

/* Detail Card */
.detail-card {
  padding: 0;
}

.card-header {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 18px 22px;
  border-bottom: 1px solid rgba(148, 163, 184, 0.08);
}

.card-icon {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  background: rgba(56, 189, 248, 0.1);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #38bdf8;
}

.card-header h3 {
  margin: 0;
  font-size: 14px;
  font-weight: 600;
  color: #f1f5f9;
}

.detail-grid {
  padding: 16px 22px;
  display: flex;
  flex-direction: column;
  gap: 0;
}

.detail-item {
  display: flex;
  align-items: center;
  padding: 11px 0;
  border-bottom: 1px solid rgba(148, 163, 184, 0.05);
  animation: fadeSlideUp 0.4s ease-out both;
}

.detail-item:last-child {
  border-bottom: none;
}

.detail-label {
  min-width: 155px;
  font-size: 12px;
  color: #64748b;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.3px;
}

.detail-value {
  font-size: 13px;
  color: #e2e8f0;
  font-weight: 500;
}

.section-divider {
  padding: 0 22px;
  margin: 4px 0 0;
}

.section-divider span {
  display: block;
  font-size: 11px;
  color: #475569;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  font-weight: 600;
  padding-top: 12px;
  border-top: 1px solid rgba(148, 163, 184, 0.08);
}

.metadata-section {
  padding-bottom: 6px;
}

/* PDF Card */
.pdf-card {
  padding: 0;
}

.pdf-toolbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 14px 22px;
  border-bottom: 1px solid rgba(148, 163, 184, 0.08);
  background: rgba(15, 23, 42, 0.4);
}

.toolbar-left {
  display: flex;
  align-items: center;
  gap: 10px;
}

.pdf-badge {
  font-size: 10px;
  font-weight: 700;
  color: #ef4444;
  background: rgba(239, 68, 68, 0.1);
  border: 1px solid rgba(239, 68, 68, 0.15);
  padding: 3px 8px;
  border-radius: 5px;
  letter-spacing: 0.5px;
}

.toolbar-title {
  font-size: 13px;
  font-weight: 500;
  color: #94a3b8;
}

.download-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 16px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 600;
  text-decoration: none;
  color: white;
  background: linear-gradient(135deg, #3b82f6, #2563eb);
  border: 1px solid rgba(59, 130, 246, 0.3);
  transition: all 0.25s ease;
  box-shadow: 0 2px 8px rgba(37, 99, 235, 0.2);
}

.download-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 16px rgba(37, 99, 235, 0.35);
  background: linear-gradient(135deg, #60a5fa, #3b82f6);
}

.pdf-viewer {
  position: relative;
  width: 100%;
  aspect-ratio: 210 / 297;
  max-height: 800px;
  background: #0f172a;
}

.pdf-object {
  width: 100%;
  height: 100%;
  border: none;
  display: block;
}

.pdf-fallback {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100%;
  gap: 14px;
  color: #64748b;
}

.pdf-fallback p {
  font-size: 13px;
  margin: 0;
}

.fallback-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 10px 20px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 500;
  color: #38bdf8;
  text-decoration: none;
  border: 1px solid rgba(56, 189, 248, 0.2);
  background: rgba(56, 189, 248, 0.06);
  transition: all 0.2s;
}

.fallback-btn:hover {
  background: rgba(56, 189, 248, 0.12);
  border-color: rgba(56, 189, 248, 0.3);
}

/* ======= Footer ======= */
.verify-footer {
  position: relative;
  z-index: 1;
  padding: 20px 24px;
}

.footer-content {
  max-width: 960px;
  margin: 0 auto;
  text-align: center;
}

.footer-line {
  width: 40px;
  height: 2px;
  background: linear-gradient(
    90deg,
    transparent,
    rgba(148, 163, 184, 0.2),
    transparent
  );
  margin: 0 auto 14px;
}

.footer-content p {
  font-size: 11px;
  color: #475569;
  margin: 0;
  letter-spacing: 0.3px;
}

.footer-sub {
  margin-top: 3px !important;
  font-size: 10px !important;
  color: #334155 !important;
}

/* ======= Animations ======= */
@keyframes fadeSlideUp {
  from {
    opacity: 0;
    transform: translateY(12px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* ======= Responsive ======= */
@media (max-width: 640px) {
  .header-content {
    flex-direction: column;
    gap: 8px;
  }
  .institution-badge {
    display: none;
  }
  .verified-container {
    padding: 20px 16px;
  }
  .detail-item {
    flex-direction: column;
    gap: 3px;
    align-items: flex-start;
  }
  .detail-label {
    min-width: unset;
  }
  .pdf-viewer {
    aspect-ratio: unset;
    height: 55vh;
    max-height: unset;
  }
  .verified-badge {
    padding: 14px 16px;
  }
  .badge-icon {
    width: 34px;
    height: 34px;
    border-radius: 10px;
  }
  .badge-title {
    font-size: 14px;
  }
}
</style>
