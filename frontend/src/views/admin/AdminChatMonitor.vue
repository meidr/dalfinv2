<template>
  <div class="max-w-7xl mx-auto flex flex-col gap-8 animate-fade-in-up">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
      <div class="flex flex-col gap-1">
        <h1 class="text-3xl font-bold tracking-tight text-text-main">
          Monitoring Chat
        </h1>
        <p class="text-text-secondary text-sm font-normal">
          Pantau seluruh percakapan antara Dosen dan Mahasiswa.
        </p>
      </div>
    </div>
    <!-- Filters -->
    <div
      class="bg-surface-light dark:bg-surface-light rounded-xl border border-border-light shadow-sm p-4"
    >
      <div class="flex flex-col md:flex-row gap-3 items-end">
        <!-- Search Participant -->
        <div class="flex-1 min-w-0">
          <label class="block text-xs font-medium text-text-secondary mb-1"
            >Cari Partisipan</label
          >
          <div class="relative">
            <span
              class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-text-secondary text-[16px]"
              >search</span
            >
            <input
              v-model="filters.search"
              @input="handleFilterChange"
              type="text"
              placeholder="Nama, NIM, atau email..."
              class="w-full pl-8 pr-3 py-2 rounded-lg bg-background-light dark:bg-background-dark border border-border-light text-sm text-text-main placeholder-gray-400 focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all outline-none"
            />
          </div>
        </div>
        <!-- Date From -->
        <div class="w-full md:w-44">
          <label class="block text-xs font-medium text-text-secondary mb-1"
            >Dari Tanggal</label
          >
          <input
            v-model="filters.date_from"
            @change="applyFilters"
            type="date"
            class="w-full px-3 py-2 rounded-lg bg-background-light dark:bg-background-dark border border-border-light text-sm text-text-main focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all outline-none"
          />
        </div>
        <!-- Date To -->
        <div class="w-full md:w-44">
          <label class="block text-xs font-medium text-text-secondary mb-1"
            >Sampai Tanggal</label
          >
          <input
            v-model="filters.date_to"
            @change="applyFilters"
            type="date"
            class="w-full px-3 py-2 rounded-lg bg-background-light dark:bg-background-dark border border-border-light text-sm text-text-main focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all outline-none"
          />
        </div>
        <!-- Reset -->
        <button
          @click="resetFilters"
          class="px-4 py-2 rounded-lg border border-border-light text-text-secondary text-sm font-medium hover:bg-background-light transition-colors flex items-center gap-1.5 whitespace-nowrap"
        >
          <span class="material-symbols-outlined text-[16px]">refresh</span>
          Reset
        </button>
      </div>
    </div>

    <!-- Conversations List -->
    <div
      class="bg-surface-light dark:bg-surface-light rounded-xl border border-border-light shadow-sm overflow-hidden"
    >
      <div v-if="loading" class="p-12 text-center">
        <div
          class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto"
        ></div>
        <p class="text-text-secondary text-sm mt-3">Memuat percakapan...</p>
      </div>

      <DataTableScroll v-else>
        <table class="w-full text-left text-sm whitespace-nowrap">
          <thead
            class="bg-sidebar-light/50 text-text-secondary font-medium border-b border-border-light"
          >
            <tr>
              <th class="px-6 py-4">Partisipan 1</th>
              <th class="px-6 py-4">Partisipan 2</th>
              <th class="px-6 py-4">Pesan Terakhir</th>
              <th class="px-6 py-4">Waktu</th>
              <th class="px-6 py-4 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border-light">
            <tr v-if="conversations.length === 0">
              <td colspan="5" class="p-12 text-center text-text-secondary">
                Belum ada percakapan
              </td>
            </tr>
            <tr
              v-for="conv in conversations"
              :key="conv.id"
              class="group hover:bg-sidebar-light/30 transition-colors"
            >
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div
                    class="size-8 rounded-full flex items-center justify-center font-bold text-xs border shrink-0"
                    :class="getAvatarColor(conv.participants[0]?.name)"
                  >
                    {{ getInitials(conv.participants[0]?.name) }}
                  </div>
                  <div>
                    <div class="font-bold text-text-main">
                      {{ conv.participants[0]?.name || "Unknown" }}
                    </div>
                    <div class="text-xs text-text-secondary capitalize">
                      {{ conv.participants[0]?.role || "-" }}
                    </div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div
                    class="size-8 rounded-full flex items-center justify-center font-bold text-xs border shrink-0"
                    :class="getAvatarColor(conv.participants[1]?.name)"
                  >
                    {{ getInitials(conv.participants[1]?.name) }}
                  </div>
                  <div>
                    <div class="font-bold text-text-main">
                      {{ conv.participants[1]?.name || "Unknown" }}
                    </div>
                    <div class="text-xs text-text-secondary capitalize">
                      {{ conv.participants[1]?.role || "-" }}
                    </div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 max-w-[300px] truncate text-text-secondary">
                {{ conv.last_message?.body || "Belum ada pesan" }}
              </td>
              <td class="px-6 py-4 text-text-secondary text-xs">
                {{ formatDate(conv.updated_at) }}
              </td>
              <td class="px-6 py-4 text-right">
                <button
                  @click="openConversation(conv)"
                  class="text-primary font-medium hover:underline text-xs"
                >
                  Lihat Detail
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </DataTableScroll>

      <!-- Pagination -->
      <TablePagination
        :pagination="pagination"
        :disabled="loading"
        @page-change="goToPage"
        @per-page-change="changePerPage"
      />
    </div>

    <!-- Chat Detail Modal -->
    <Transition name="modal-fade">
      <div
        v-if="showDetailModal"
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
        @click.self="closeDetailModal"
      >
        <div
          class="bg-white dark:bg-surface-light rounded-xl shadow-2xl w-full max-w-2xl h-[80vh] flex flex-col"
        >
          <div
            class="p-4 border-b border-border-light flex justify-between items-center bg-gray-50 dark:bg-gray-800 rounded-t-xl"
          >
            <div>
              <h3 class="font-bold text-text-main pb-1">Detail Percakapan</h3>
              <div class="text-xs text-text-secondary flex gap-2">
                <span>{{ selectedConversation?.participants[0]?.name }}</span>
                <span>•</span>
                <span>{{ selectedConversation?.participants[1]?.name }}</span>
              </div>
            </div>
            <button
              @click="closeDetailModal"
              class="p-1 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors"
            >
              <span class="material-symbols-outlined text-text-secondary"
                >close</span
              >
            </button>
          </div>

          <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-background-light">
            <div
              v-if="loadingMessages"
              class="flex justify-center items-center h-full"
            >
              <div
                class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"
              ></div>
            </div>
            <div
              v-else-if="messages.length === 0"
              class="text-center text-text-secondary py-10"
            >
              Belum ada pesan
            </div>
            <div v-else class="space-y-4">
              <div
                v-for="msg in messages"
                :key="msg.id"
                class="flex flex-col gap-1"
                :class="
                  msg.sender_id === selectedConversation.participants[0].id
                    ? 'items-start'
                    : 'items-end'
                "
              >
                <div class="flex items-center gap-2 mb-1">
                  <span class="text-xs font-bold text-text-main">{{
                    msg.sender_name
                  }}</span>
                  <span class="text-[10px] text-text-secondary">{{
                    formatTime(msg.created_at)
                  }}</span>
                </div>
                <div
                  class="px-4 py-2 rounded-2xl max-w-[80%] text-sm leading-relaxed shadow-sm break-words whitespace-pre-wrap"
                  :class="
                    msg.sender_id === selectedConversation.participants[0].id
                      ? 'bg-white dark:bg-sidebar-light text-text-main rounded-tl-sm border border-border-light'
                      : 'bg-primary/10 text-text-main rounded-tr-sm border border-primary/20'
                  "
                >
                  {{ msg.body }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from "vue";
import { chatService } from "../../services/chatService";
import { formatDistanceToNow, format } from "date-fns";
import { id } from "date-fns/locale";

const loading = ref(true);
const conversations = ref([]);
const pagination = reactive({
  current_page: 1,
  last_page: 1,
  per_page: 20,
  total: 0,
  from: 0,
  to: 0,
});

const filters = reactive({
  search: "",
  date_from: "",
  date_to: "",
});
let filterDebounce = null;

const showDetailModal = ref(false);
const selectedConversation = ref(null);
const messages = ref([]);
const loadingMessages = ref(false);

const fetchConversations = async (page = 1) => {
  try {
    loading.value = true;
    const params = { page, per_page: pagination.per_page };
    if (filters.search) params.search = filters.search;
    if (filters.date_from) params.date_from = filters.date_from;
    if (filters.date_to) params.date_to = filters.date_to;
    const response = await chatService.getAllConversations(params);
    if (response.success) {
      conversations.value = response.data.data;
      pagination.current_page = response.data.current_page;
      pagination.last_page = response.data.last_page;
      pagination.per_page = response.data.per_page || pagination.per_page;
      pagination.total = response.data.total || 0;
      pagination.from = response.data.from || 0;
      pagination.to = response.data.to || 0;
    }
  } catch (error) {
    console.error("Failed to fetch conversations:", error);
  } finally {
    loading.value = false;
  }
};

const handleFilterChange = () => {
  clearTimeout(filterDebounce);
  filterDebounce = setTimeout(() => {
    fetchConversations(1);
  }, 400);
};

const applyFilters = () => {
  fetchConversations(1);
};

const resetFilters = () => {
  filters.search = "";
  filters.date_from = "";
  filters.date_to = "";
  fetchConversations(1);
};

const goToPage = (page) => {
  if (page >= 1 && page <= pagination.last_page) {
    fetchConversations(page);
  }
};

const changePerPage = (perPage) => {
  pagination.per_page = perPage;
  pagination.current_page = 1;
  fetchConversations(1);
};

const openConversation = async (conv) => {
  selectedConversation.value = conv;
  showDetailModal.value = true;
  loadingMessages.value = true;
  try {
    const response = await chatService.getMessages(conv.id);
    if (response.success) {
      messages.value = response.data;
    }
  } catch (error) {
    // Fixed missing error arg
    console.error("Failed to fetch messages:", error);
  } finally {
    loadingMessages.value = false;
  }
};

const closeDetailModal = () => {
  showDetailModal.value = false;
  selectedConversation.value = null;
  messages.value = [];
};

// Utils
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

const formatDate = (dateString) => {
  if (!dateString) return "-";
  return formatDistanceToNow(new Date(dateString), {
    addSuffix: true,
    locale: id,
  });
};

const formatTime = (dateString) => {
  return format(new Date(dateString), "dd MMM HH:mm", { locale: id });
};

onMounted(() => {
  fetchConversations();
});
</script>
