import { createRouter, createWebHistory } from "vue-router";
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
    children: [
      {
        path: "dashboard",
        name: "AdminDashboard",
        component: Dashboard,
      },
      {
        path: "skripsi",
        name: "DataSkripsi",
        component: DataSkripsi,
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
        path: "notabimbingan",
        name: "DataNotaBimbingan",
        component: () =>
          import("../views/admin/notabimbingan/DataNotaBimbingan.vue"),
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
        path: "pengguna",
        name: "DataPengguna",
        component: () => import("../views/admin/pengguna/DataPengguna.vue"),
      },
      {
        path: "profil",
        name: "Profile",
        component: () => import("../views/admin/profil/Profile.vue"),
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

export default router;
