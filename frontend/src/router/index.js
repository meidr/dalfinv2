import { createRouter, createWebHistory } from "vue-router";
import { startProgress, stopProgress } from "../services/api";
import Login from "../views/Login.vue";
import AdminLayout from "../layouts/AdminLayout.vue";
import Dashboard from "../views/admin/Dashboard.vue";
import DataSkripsi from "../views/admin/skripsi/DataSkripsi.vue";

const routes = [
  {
    path: "/",
    redirect: "/login",
  },
  {
    path: "/login",
    name: "Login",
    component: Login,
  },
  {
    path: "/admin",
    component: AdminLayout,
    redirect: "/admin/dashboard",
    children: [
      {
        path: "dashboard",
        name: "AdminDashboard",
        component: Dashboard,
      },
      {
        path: "profil",
        name: "AdminProfil",
        component: () => import("../views/admin/ProfilAdmin.vue"),
      },
      {
        path: "skripsi",
        name: "DataSkripsi",
        component: DataSkripsi,
      },
      {
        path: "skripsi/verifikasi",
        name: "VerificationSkripsi",
        component: () =>
          import("../views/admin/skripsi/VerificationSkripsi.vue"),
      },
      {
        path: "skripsi/:id",
        name: "AdminDetailSkripsi",
        component: () => import("../views/admin/skripsi/DetailSkripsi.vue"),
      },
      {
        path: "pembimbing",
        name: "DataPembimbing",
        component: () => import("../views/admin/pembimbing/DataPembimbing.vue"),
      },
      {
        path: "ujian",
        name: "DataUjian",
        component: () => import("../views/admin/ujian/DataUjian.vue"),
      },
      {
        path: "seminar",
        name: "DataSeminar",
        component: () => import("../views/admin/seminar/DataSeminar.vue"),
      },
      {
        path: "seminar/:id",
        name: "DetailSeminar",
        component: () => import("../views/admin/seminar/DetailSeminar.vue"),
      },
      {
        path: "sktugas",
        name: "DataSKTugas",
        component: () => import("../views/admin/sktugas/DataSKTugas.vue"),
      },
      {
        path: "bimbingan",
        name: "DataBimbingan",
        component: () => import("../views/admin/bimbingan/DataBimbingan.vue"),
      },
      {
        path: "bimbingan/:id",
        name: "AdminDetailBimbingan",
        component: () => import("../views/admin/bimbingan/DetailBimbingan.vue"),
      },
      {
        path: "notabimbingan",
        name: "DataNotaBimbingan",
        component: () =>
          import("../views/admin/notabimbingan/DataNotaBimbingan.vue"),
      },
      {
        path: "seminarhasil",
        name: "DataSeminarHasil",
        component: () =>
          import("../views/admin/seminarhasil/DataSeminarHasil.vue"),
      },
      {
        path: "seminarhasil/:id",
        name: "DetailSeminarHasil",
        component: () =>
          import("../views/admin/seminarhasil/DetailSeminarHasil.vue"),
      },
      {
        path: "beritaacara",
        name: "DataBeritaAcara",
        component: () =>
          import("../views/admin/beritaacara/DataBeritaAcara.vue"),
      },
      {
        path: "skyudisium",
        name: "DataSKYudisium",
        component: () => import("../views/admin/skyudisium/DataSKYudisium.vue"),
      },
      {
        path: "master/mahasiswa",
        name: "MasterMahasiswa",
        component: () => import("../views/admin/master/MasterMahasiswa.vue"),
      },
      {
        path: "master/dosen",
        name: "MasterDosen",
        component: () => import("../views/admin/master/MasterDosen.vue"),
      },
      {
        path: "master/fakultas",
        name: "MasterFakultas",
        component: () => import("../views/admin/master/MasterFakultas.vue"),
      },
      {
        path: "master/prodi",
        name: "MasterProdi",
        component: () => import("../views/admin/master/MasterProdi.vue"),
      },
      {
        path: "master/tahun",
        name: "MasterTahun",
        component: () => import("../views/admin/master/MasterTahun.vue"),
      },
      {
        path: "master/jabatan",
        name: "MasterJabatan",
        component: () => import("../views/admin/master/MasterJabatan.vue"),
      },
      {
        path: "pengguna",
        name: "DataPengguna",
        component: () => import("../views/admin/pengguna/DataPengguna.vue"),
      },
      {
        path: "profil",
        name: "Profile",
        component: () => import("../views/admin/profil/Profile.vue"),
      },
      {
        path: "super-admin",
        name: "SuperAdmin",
        component: () => import("../views/admin/superadmin/SuperAdmin.vue"),
      },
      {
        path: "chat-monitor",
        name: "AdminChatMonitor",
        component: () => import("../views/admin/AdminChatMonitor.vue"),
      },
      {
        path: "otoritas-jabatan",
        name: "OtoritasJabatan",
        component: () =>
          import("../views/admin/superadmin/OtoritasJabatan.vue"),
      },
      {
        path: "otoritas-tandatangan",
        name: "OtoritasTandaTangan",
        component: () =>
          import("../views/admin/superadmin/OtoritasTandaTangan.vue"),
      },
      {
        path: "pengaturan-modul",
        name: "PengaturanModul",
        component: () =>
          import("../views/admin/superadmin/PengaturanModul.vue"),
      },
      {
        path: "konfigurasi",
        name: "AdminKonfigurasi",
        component: () => import("../views/admin/Konfigurasi.vue"),
      },
    ],
  },
  {
    path: "/dosen",
    component: () => import("../layouts/DosenLayout.vue"),
    redirect: "/dosen/dashboard",
    children: [
      {
        path: "dashboard",
        name: "DosenDashboard",
        component: () => import("../views/dosen/Dashboard.vue"),
      },
      {
        path: "bimbingan",
        name: "DosenBimbingan",
        component: () => import("../views/dosen/Bimbingan.vue"),
      },
      {
        path: "bimbingan/:id",
        component: () => import("../views/dosen/DetailBimbingan.vue"),
        children: [
          {
            path: "",
            redirect: { name: "DosenBimbinganProgress" },
          },
          {
            path: "progress",
            name: "DosenBimbinganProgress",
            component: () => import("../views/mahasiswa/skripsi/Progress.vue"),
          },
          {
            path: "profil",
            name: "DosenBimbinganProfil",
            component: () => import("../views/mahasiswa/skripsi/Profil.vue"),
          },
          {
            path: "pembimbing",
            name: "DosenBimbinganPembimbing",
            component: () =>
              import("../views/mahasiswa/skripsi/Pembimbing.vue"),
          },
          {
            path: "log",
            name: "DosenBimbinganLog",
            component: () =>
              import("../views/mahasiswa/skripsi/LogBimbingan.vue"),
            props: { isDosen: true },
          },
          {
            path: "jadwal",
            name: "DosenBimbinganJadwal",
            component: () => import("../views/mahasiswa/skripsi/Jadwal.vue"),
          },
          {
            path: "nilai",
            name: "DosenBimbinganNilai",
            component: () => import("../views/mahasiswa/skripsi/Nilai.vue"),
          },
          {
            path: "dokumen",
            name: "DosenBimbinganDokumen",
            component: () => import("../views/mahasiswa/skripsi/Dokumen.vue"),
          },
        ],
      },
      {
        path: "jadwal",
        name: "DosenJadwal",
        component: () => import("../views/dosen/Jadwal.vue"),
      },
      {
        path: "profil",
        name: "DosenProfil",
        component: () => import("../views/dosen/Profil.vue"),
      },
    ],
  },
  {
    path: "/mahasiswa",
    component: () => import("../layouts/MahasiswaLayout.vue"),
    redirect: "/mahasiswa/dashboard",
    children: [
      {
        path: "dashboard",
        name: "MahasiswaDashboard",
        component: () => import("../views/mahasiswa/Dashboard.vue"),
      },
      {
        path: "skripsi",
        name: "DaftarSkripsi",
        component: () => import("../views/mahasiswa/DaftarSkripsi.vue"),
      },
      {
        path: "skripsi/detail",
        component: () => import("../views/mahasiswa/DetailSkripsi.vue"),
        children: [
          {
            path: "",
            redirect: { name: "SkripsiProgress" },
          },
          {
            path: "progress",
            name: "SkripsiProgress",
            component: () => import("../views/mahasiswa/skripsi/Progress.vue"),
          },
          {
            path: "profil",
            name: "SkripsiProfil",
            component: () => import("../views/mahasiswa/skripsi/Profil.vue"),
          },
          {
            path: "pembimbing",
            name: "SkripsiPembimbing",
            component: () =>
              import("../views/mahasiswa/skripsi/Pembimbing.vue"),
          },
          {
            path: "log",
            name: "SkripsiLog",
            component: () =>
              import("../views/mahasiswa/skripsi/LogBimbingan.vue"),
          },
          {
            path: "jadwal",
            name: "SkripsiJadwal",
            component: () => import("../views/mahasiswa/skripsi/Jadwal.vue"),
          },
          {
            path: "nilai",
            name: "SkripsiNilai",
            component: () => import("../views/mahasiswa/skripsi/Nilai.vue"),
          },
          {
            path: "dokumen",
            name: "SkripsiDokumen",
            component: () => import("../views/mahasiswa/skripsi/Dokumen.vue"),
          },
        ],
      },
      {
        path: "skripsi/history/:id",
        component: () => import("../views/mahasiswa/DetailSkripsiHistory.vue"),
        children: [
          {
            path: "",
            redirect: { name: "HistorySkripsiProgress" },
          },
          {
            path: "progress",
            name: "HistorySkripsiProgress",
            component: () => import("../views/mahasiswa/skripsi/Progress.vue"),
          },
          {
            path: "profil",
            name: "HistorySkripsiProfil",
            component: () => import("../views/mahasiswa/skripsi/Profil.vue"),
          },
          {
            path: "pembimbing",
            name: "HistorySkripsiPembimbing",
            component: () =>
              import("../views/mahasiswa/skripsi/Pembimbing.vue"),
          },
          {
            path: "log",
            name: "HistorySkripsiLog",
            component: () =>
              import("../views/mahasiswa/skripsi/LogBimbingan.vue"),
          },
          {
            path: "jadwal",
            name: "HistorySkripsiJadwal",
            component: () => import("../views/mahasiswa/skripsi/Jadwal.vue"),
          },
          {
            path: "nilai",
            name: "HistorySkripsiNilai",
            component: () => import("../views/mahasiswa/skripsi/Nilai.vue"),
          },
          {
            path: "dokumen",
            name: "HistorySkripsiDokumen",
            component: () => import("../views/mahasiswa/skripsi/Dokumen.vue"),
          },
        ],
      },
      {
        path: "profil",
        name: "ProfilMahasiswa",
        component: () => import("../views/mahasiswa/ProfilMahasiswa.vue"),
      },
      {
        path: "jadwal-seminar",
        name: "JadwalSeminarMahasiswa",
        component: () => import("../views/mahasiswa/JadwalSeminar.vue"),
      },
      {
        path: "jadwal-sidang",
        name: "JadwalSidangMahasiswa",
        component: () => import("../views/mahasiswa/JadwalSidang.vue"),
      },
    ],
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

let moduleSettingsFetched = false;

router.beforeEach(async (to, from, next) => {
  startProgress();

  // Fetch module settings once per session for authenticated users
  if (!moduleSettingsFetched) {
    const user = JSON.parse(localStorage.getItem("user") || "null");
    if (user) {
      try {
        const { useAuthStore } = await import("../stores/auth");
        const { createPinia, setActivePinia } = await import("pinia");
        const authStore = useAuthStore();
        await authStore.fetchModuleSettings();
        moduleSettingsFetched = true;

        // Block seminarhasil routes if module disabled
        if (
          !authStore.semhasEnabled &&
          (to.name === "DataSeminarHasil" || to.name === "DetailSeminarHasil")
        ) {
          return next("/admin/dashboard");
        }
      } catch (e) {
        // Silently continue if fetch fails
        moduleSettingsFetched = true;
      }
    }
  } else {
    // Check on subsequent navigations too
    const user = JSON.parse(localStorage.getItem("user") || "null");
    if (user) {
      const semhasEnabled = JSON.parse(
        localStorage.getItem("semhas_enabled") ?? "true",
      );
      if (
        !semhasEnabled &&
        (to.name === "DataSeminarHasil" || to.name === "DetailSeminarHasil")
      ) {
        return next("/admin/dashboard");
      }
    }
  }

  next();
});

router.afterEach(() => {
  stopProgress();
});

export default router;
