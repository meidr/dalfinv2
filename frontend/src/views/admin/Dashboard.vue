<template>
  <div class="max-w-7xl mx-auto flex flex-col gap-8">
    <!-- Loading State -->
    <div v-if="loading" class="flex items-center justify-center py-20">
      <div class="flex flex-col items-center gap-3">
        <div
          class="animate-spin rounded-full h-10 w-10 border-b-2 border-primary"
        ></div>
        <p class="text-text-secondary text-sm">Memuat data...</p>
      </div>
    </div>

    <template v-else>
      <div class="flex flex-col gap-1 animate-fade-in-up">
        <h1 class="text-text-main text-3xl font-bold leading-tight">
          Selamat Datang, {{ userName }}
        </h1>
        <p class="text-text-secondary text-sm font-normal">
          Overview status skripsi mahasiswa & aktivitas terbaru hari ini.
        </p>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div
          class="flex flex-col gap-2 rounded-xl p-6 bg-surface-light border border-border-light shadow-sm hover:shadow-md transition-all hover:-translate-y-1 duration-300 cursor-pointer group"
          @click="$router.push('/admin/skripsi')"
        >
          <div class="flex items-center justify-between">
            <p
              class="text-text-secondary text-xs font-bold uppercase tracking-wider"
            >
              Total Skripsi
            </p>
            <div
              class="bg-primary/10 p-2 rounded-lg text-primary group-hover:bg-primary group-hover:text-white transition-colors"
            >
              <span class="material-symbols-outlined">library_books</span>
            </div>
          </div>
          <div class="mt-2">
            <p class="text-text-main text-3xl font-bold leading-tight">
              {{ stats.total_skripsi }}
            </p>
            <div class="flex items-center gap-1 mt-1">
              <span class="material-symbols-outlined text-green-600 text-[18px]"
                >trending_up</span
              >
              <p class="text-green-600 text-xs font-semibold">
                Total terdaftar
              </p>
            </div>
          </div>
        </div>

        <div
          class="flex flex-col gap-2 rounded-xl p-6 bg-surface-light border border-border-light shadow-sm hover:shadow-md transition-all hover:-translate-y-1 duration-300 cursor-pointer group"
          @click="$router.push('/admin/skripsi?status=bimbingan')"
        >
          <div class="flex items-center justify-between">
            <p
              class="text-text-secondary text-xs font-bold uppercase tracking-wider"
            >
              Sedang Bimbingan
            </p>
            <div
              class="bg-primary/10 p-2 rounded-lg text-primary group-hover:bg-primary group-hover:text-white transition-colors"
            >
              <span class="material-symbols-outlined">description</span>
            </div>
          </div>
          <div class="mt-2">
            <p class="text-text-main text-3xl font-bold leading-tight">
              {{ stats.bimbingan }}
            </p>
            <div class="flex items-center gap-1 mt-1">
              <span class="material-symbols-outlined text-blue-600 text-[18px]"
                >groups</span
              >
              <p class="text-blue-600 text-xs font-semibold">Mahasiswa aktif</p>
            </div>
          </div>
        </div>

        <div
          class="flex flex-col gap-2 rounded-xl p-6 bg-surface-light border border-border-light shadow-sm hover:shadow-md transition-all hover:-translate-y-1 duration-300 cursor-pointer group"
          @click="$router.push('/admin/skripsi?status=lulus')"
        >
          <div class="flex items-center justify-between">
            <p
              class="text-text-secondary text-xs font-bold uppercase tracking-wider"
            >
              Sudah Lulus
            </p>
            <div
              class="bg-green-100 p-2 rounded-lg text-green-600 group-hover:bg-green-500 group-hover:text-white transition-colors"
            >
              <span class="material-symbols-outlined">check_circle</span>
            </div>
          </div>
          <div class="mt-2">
            <p class="text-text-main text-3xl font-bold leading-tight">
              {{ stats.lulus }}
            </p>
            <div class="flex items-center gap-1 mt-1">
              <span class="material-symbols-outlined text-green-600 text-[18px]"
                >celebration</span
              >
              <p class="text-green-600 text-xs font-semibold">Wisuda</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Distribution Chart & Pending Table -->
      <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Distribution Chart -->
        <div
          class="xl:col-span-1 flex flex-col rounded-xl border border-border-light bg-surface-light overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300"
        >
          <div
            class="p-6 border-b border-border-light flex justify-between items-center bg-sidebar-light/50"
          >
            <div>
              <h3 class="text-text-main text-lg font-bold">
                Distribusi Status
              </h3>
              <p class="text-text-secondary text-xs">Mahasiswa semester ini</p>
            </div>
          </div>
          <div class="flex-1 p-8 flex items-end justify-center min-h-[300px]">
            <div class="flex w-full h-48 items-end justify-between gap-3">
              <div
                v-for="(item, index) in distribution"
                :key="index"
                class="flex flex-col items-center gap-3 flex-1 group cursor-pointer hover:-translate-y-2 transition-transform duration-300"
              >
                <div
                  class="w-full rounded-t-md relative transition-all duration-300"
                  :class="item.color"
                  :style="{ height: item.height + '%' }"
                >
                  <div
                    class="absolute -top-8 left-1/2 -translate-x-1/2 bg-text-main text-white text-[10px] font-bold px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity translate-y-2 group-hover:translate-y-0"
                  >
                    {{ item.count }}
                  </div>
                </div>
                <p
                  class="text-[10px] text-text-secondary font-bold group-hover:text-primary transition-colors"
                >
                  {{ item.label }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Pending Table -->
        <div
          class="xl:col-span-2 flex flex-col rounded-xl border border-border-light bg-surface-light overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300"
        >
          <div
            class="p-6 border-b border-border-light flex flex-wrap gap-4 justify-between items-center bg-sidebar-light/50"
          >
            <div>
              <h3 class="text-text-main text-lg font-bold">Perlu Diproses</h3>
              <p class="text-text-secondary text-xs">
                Mahasiswa yang baru mengubah status alur.
              </p>
            </div>
            <button
              @click="$router.push('/admin/skripsi')"
              class="text-primary text-sm font-bold hover:bg-primary/5 px-4 py-2 rounded-lg transition-colors border border-primary/20 hover:border-primary focus:ring-2 focus:ring-primary/20"
            >
              View All
            </button>
          </div>
          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="bg-sidebar-light/50 border-b border-border-light">
                  <th
                    class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase"
                  >
                    Mahasiswa
                  </th>
                  <th
                    class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase"
                  >
                    Status Sekarang
                  </th>
                  <th
                    class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase"
                  >
                    Tanggal Submit
                  </th>
                  <th
                    class="p-4 text-[10px] font-bold tracking-widest text-text-secondary uppercase text-right"
                  >
                    Aksi
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-border-light">
                <tr v-if="recentActivities.length === 0">
                  <td colspan="4" class="p-8 text-center text-text-secondary">
                    Tidak ada data yang perlu diproses
                  </td>
                </tr>
                <tr
                  v-for="item in recentActivities"
                  :key="item.id"
                  class="group hover:bg-sidebar-light/30 transition-colors"
                >
                  <td class="p-4">
                    <div class="flex items-center gap-3">
                      <div
                        class="bg-gray-200 size-10 rounded-full flex items-center justify-center text-xs font-bold"
                        :class="getAvatarColor(item.mahasiswa?.nama)"
                      >
                        {{ getInitials(item.mahasiswa?.nama) }}
                      </div>
                      <div>
                        <p
                          class="font-bold text-text-main text-sm group-hover:text-primary transition-colors"
                        >
                          {{ item.mahasiswa?.nama || "-" }}
                        </p>
                        <p class="text-xs text-text-secondary font-medium">
                          {{ item.mahasiswa?.nim || "-" }}
                        </p>
                      </div>
                    </div>
                  </td>
                  <td class="p-4">
                    <span
                      class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold"
                      :class="getStatusClass(item.status)"
                    >
                      <span
                        class="size-1.5 rounded-full animate-pulse"
                        :class="getStatusDot(item.status)"
                      ></span>
                      {{ getStatusLabel(item.status) }}
                    </span>
                  </td>
                  <td class="p-4 text-xs font-medium text-text-secondary">
                    {{ formatDate(item.updated_at) }}
                  </td>
                  <td class="p-4 text-right">
                    <button
                      @click="viewSkripsiDetail(item.id)"
                      class="inline-flex items-center justify-center px-4 py-2 text-xs font-bold text-white bg-primary rounded-lg hover:bg-primary/90 transition-all shadow-sm hover:shadow-md hover:-translate-y-0.5 active:translate-y-0"
                    >
                      Lihat Detail
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "../../stores/auth";
import adminService from "../../services/adminService";

const router = useRouter();
const authStore = useAuthStore();

const loading = ref(true);
const stats = ref({
  total_skripsi: 0,
  bimbingan: 0,
  lulus: 0,
  proposal: 0,
  sempro: 0,
  semhas: 0,
  ujian: 0,
});
const recentActivities = ref([]);

const userName = computed(() => authStore.user?.name || "Admin");

const distribution = computed(() => {
  const max = Math.max(
    stats.value.proposal,
    stats.value.bimbingan,
    stats.value.sempro,
    stats.value.ujian,
    stats.value.lulus,
    1,
  );
  return [
    {
      label: "Prop",
      count: stats.value.proposal,
      height: (stats.value.proposal / max) * 100,
      color: "bg-primary/20 group-hover:bg-primary",
    },
    {
      label: "Bim",
      count: stats.value.bimbingan,
      height: (stats.value.bimbingan / max) * 100,
      color: "bg-primary group-hover:bg-primary shadow-md shadow-primary/20",
    },
    {
      label: "Sem",
      count: stats.value.sempro + stats.value.semhas,
      height: ((stats.value.sempro + stats.value.semhas) / max) * 100,
      color: "bg-primary/20 group-hover:bg-primary",
    },
    {
      label: "Uji",
      count: stats.value.ujian,
      height: (stats.value.ujian / max) * 100,
      color: "bg-orange-500/20 group-hover:bg-orange-500",
    },
    {
      label: "Lulus",
      count: stats.value.lulus,
      height: (stats.value.lulus / max) * 100,
      color: "bg-green-500/20 group-hover:bg-green-500",
    },
  ];
});

const fetchDashboard = async () => {
  try {
    loading.value = true;
    const response = await adminService.getDashboard();
    if (response.success) {
      const data = response.data;
      stats.value = {
        total_skripsi: data.total_skripsi || 0,
        bimbingan: data.skripsi_bimbingan || 0,
        lulus: data.skripsi_lulus || 0,
        proposal: data.skripsi_proposal || 0,
        sempro: data.skripsi_sempro || 0,
        semhas: data.skripsi_semhas || 0,
        ujian: data.skripsi_ujian || 0,
      };
      recentActivities.value = data.recent_activities || [];
    }
  } catch (error) {
    console.error("Failed to fetch dashboard:", error);
  } finally {
    loading.value = false;
  }
};

const getInitials = (name) => {
  if (!name) return "?";
  return name
    .split(" ")
    .map((n) => n[0])
    .join("")
    .substring(0, 2)
    .toUpperCase();
};

const getAvatarColor = (name) => {
  const colors = [
    "bg-blue-100 text-blue-600",
    "bg-purple-100 text-purple-600",
    "bg-orange-100 text-orange-600",
    "bg-green-100 text-green-600",
  ];
  if (!name) return colors[0];
  const index = name.charCodeAt(0) % colors.length;
  return colors[index];
};

const getStatusClass = (status) => {
  const classes = {
    proposal: "bg-yellow-50 text-yellow-600 border border-yellow-100",
    bimbingan: "bg-purple-50 text-purple-600 border border-purple-100",
    sempro: "bg-blue-50 text-blue-600 border border-blue-100",
    semhas: "bg-cyan-50 text-cyan-600 border border-cyan-100",
    ujian: "bg-orange-50 text-orange-600 border border-orange-100",
    revisi: "bg-amber-50 text-amber-600 border border-amber-100",
    lulus: "bg-green-50 text-green-600 border border-green-100",
  };
  return classes[status] || "bg-gray-50 text-gray-600 border border-gray-100";
};

const getStatusDot = (status) => {
  const dots = {
    proposal: "bg-yellow-600",
    bimbingan: "bg-purple-600",
    sempro: "bg-blue-600",
    semhas: "bg-cyan-600",
    ujian: "bg-orange-600",
    revisi: "bg-amber-600",
    lulus: "bg-green-600",
  };
  return dots[status] || "bg-gray-600";
};

const getStatusLabel = (status) => {
  const labels = {
    proposal: "Proposal",
    bimbingan: "Bimbingan",
    sempro: "Seminar Proposal",
    semhas: "Seminar Hasil",
    ujian: "Ujian Sidang",
    revisi: "Revisi",
    lulus: "Lulus",
  };
  return labels[status] || status;
};

const formatDate = (date) => {
  if (!date) return "-";
  return new Date(date).toLocaleDateString("id-ID", {
    day: "numeric",
    month: "short",
    year: "numeric",
  });
};

const viewSkripsiDetail = (id) => {
  router.push(`/admin/skripsi/${id}`);
};

onMounted(() => {
  fetchDashboard();
});
</script>

<style scoped>
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in-up {
  animation: fadeInUp 0.5s ease-out forwards;
}
</style>
