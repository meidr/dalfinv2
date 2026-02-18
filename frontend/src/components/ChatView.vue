<template>
  <div class="flex flex-col gap-6 animate-fade-in">
    <!-- Header -->
    <div>
      <div class="flex items-center gap-2 text-sm text-text-secondary mb-2">
        <span class="material-symbols-outlined text-[16px]">home</span>
        <span>/</span>
        <span class="text-primary font-medium">Chat</span>
      </div>
      <h1 class="text-3xl font-bold text-text-main tracking-tight">Chat</h1>
      <p class="text-text-secondary mt-1">
        Kirim pesan ke dosen, mahasiswa, atau admin.
      </p>
    </div>

    <!-- Chat Container -->
    <div
      class="bg-surface-light rounded-xl shadow-sm border border-border-light overflow-hidden flex"
      style="height: calc(100vh - 260px); min-height: 480px"
    >
      <!-- LEFT: Conversation List -->
      <div
        class="w-80 flex-shrink-0 border-r border-border-light flex flex-col"
        :class="{ 'hidden md:flex': activeConversation }"
      >
        <!-- Search & New Chat -->
        <div class="p-4 border-b border-border-light space-y-3">
          <div class="relative">
            <span
              class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-text-secondary text-[18px]"
              >search</span
            >
            <input
              v-model="searchQuery"
              @focus="showUserSearch = true"
              @input="handleSearchInput"
              class="w-full pl-9 pr-4 py-2.5 rounded-lg bg-background-light border border-border-light text-sm text-text-main placeholder-gray-400 focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all outline-none"
              placeholder="Cari kontak atau mulai chat baru..."
            />
          </div>
        </div>

        <!-- User Search Results (overlay) -->
        <div
          v-if="showUserSearch && searchQuery.length > 0"
          class="flex-1 overflow-y-auto"
        >
          <div class="px-4 py-2">
            <p
              class="text-xs font-bold text-text-secondary uppercase tracking-wider"
            >
              Mulai Chat Baru
            </p>
          </div>
          <div v-if="searchLoading" class="p-6 text-center">
            <span
              class="material-symbols-outlined text-2xl text-text-secondary animate-spin"
              >progress_activity</span
            >
          </div>
          <div v-else-if="searchResults.length === 0" class="p-6 text-center">
            <p class="text-sm text-text-secondary">Tidak ditemukan</p>
          </div>
          <div
            v-for="user in searchResults"
            :key="user.id"
            @click="startChat(user)"
            class="flex items-center gap-3 px-4 py-3 hover:bg-sidebar-light/50 cursor-pointer transition-colors"
          >
            <div
              class="size-10 rounded-full flex items-center justify-center text-sm font-bold shrink-0"
              :class="getRoleBgClass(user.role)"
            >
              {{ getInitials(user.name) }}
            </div>
            <div class="min-w-0 flex-1">
              <p class="text-sm font-medium text-text-main truncate">
                {{ user.name }}
              </p>
              <p class="text-xs text-text-secondary truncate">
                {{ user.subtitle }}
              </p>
            </div>
          </div>
        </div>

        <!-- Conversation List -->
        <div v-else class="flex-1 overflow-y-auto">
          <div
            v-if="loadingConversations && conversations.length === 0"
            class="p-6 text-center"
          >
            <span
              class="material-symbols-outlined text-2xl text-text-secondary animate-spin"
              >progress_activity</span
            >
          </div>
          <div v-else-if="conversations.length === 0" class="p-6 text-center">
            <span
              class="material-symbols-outlined text-4xl text-text-secondary/50 mb-2 block"
              >forum</span
            >
            <p class="text-sm text-text-secondary">Belum ada percakapan</p>
            <p class="text-xs text-text-secondary mt-1">
              Cari kontak untuk memulai chat
            </p>
          </div>
          <div
            v-for="conv in conversations"
            :key="conv.id"
            @click="openConversation(conv)"
            class="flex items-center gap-3 px-4 py-3 cursor-pointer transition-colors border-b border-border-light/50"
            :class="
              activeConversation?.id === conv.id
                ? 'bg-primary/5 border-l-2 border-l-primary'
                : 'hover:bg-sidebar-light/50'
            "
          >
            <div
              class="size-10 rounded-full flex items-center justify-center text-sm font-bold shrink-0"
              :class="getRoleBgClass(conv.other_user.role)"
            >
              {{ getInitials(conv.other_user.name) }}
            </div>
            <div class="min-w-0 flex-1">
              <div class="flex justify-between items-center">
                <p
                  class="text-sm font-medium text-text-main truncate"
                  :class="{ 'font-bold': conv.unread_count > 0 }"
                >
                  {{ conv.other_user.name }}
                </p>
                <span
                  class="text-[10px] text-text-secondary whitespace-nowrap ml-2"
                >
                  {{ timeAgo(conv.updated_at) }}
                </span>
              </div>
              <div class="flex justify-between items-center mt-0.5">
                <p
                  class="text-xs truncate"
                  :class="
                    conv.unread_count > 0
                      ? 'text-text-main font-medium'
                      : 'text-text-secondary'
                  "
                >
                  {{
                    conv.last_message
                      ? conv.last_message.sender_id === currentUserId
                        ? "Anda: " + conv.last_message.body
                        : conv.last_message.body
                      : "Belum ada pesan"
                  }}
                </p>
                <span
                  v-if="conv.unread_count > 0"
                  class="ml-2 bg-primary text-white text-[10px] font-bold rounded-full size-5 flex items-center justify-center shrink-0"
                >
                  {{ conv.unread_count > 9 ? "9+" : conv.unread_count }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- RIGHT: Message Thread -->
      <div
        class="flex-1 flex flex-col"
        :class="{ 'hidden md:flex': !activeConversation }"
      >
        <!-- Empty state -->
        <div
          v-if="!activeConversation"
          class="flex-1 flex flex-col items-center justify-center text-center p-8"
        >
          <div
            class="size-20 rounded-full bg-primary/5 flex items-center justify-center mb-4"
          >
            <span class="material-symbols-outlined text-4xl text-primary/40"
              >chat</span
            >
          </div>
          <h3 class="text-lg font-bold text-text-main mb-1">
            Mulai Percakapan
          </h3>
          <p class="text-sm text-text-secondary max-w-xs">
            Pilih percakapan dari daftar atau cari kontak untuk memulai chat
            baru.
          </p>
        </div>

        <!-- Active Conversation -->
        <template v-else>
          <!-- Chat Header -->
          <div
            class="px-5 py-3 border-b border-border-light flex items-center gap-3"
          >
            <button
              @click="activeConversation = null"
              class="md:hidden text-text-secondary hover:text-primary mr-1"
            >
              <span class="material-symbols-outlined">arrow_back</span>
            </button>
            <div
              class="size-9 rounded-full flex items-center justify-center text-sm font-bold shrink-0"
              :class="getRoleBgClass(activeConversation.other_user.role)"
            >
              {{ getInitials(activeConversation.other_user.name) }}
            </div>
            <div class="min-w-0">
              <p class="text-sm font-bold text-text-main truncate">
                {{ activeConversation.other_user.name }}
              </p>
              <p class="text-xs text-text-secondary capitalize">
                {{ getRoleLabel(activeConversation.other_user.role) }}
              </p>
            </div>
          </div>

          <!-- Messages Area -->
          <div
            ref="messagesContainer"
            class="flex-1 overflow-y-auto px-5 py-4 space-y-3"
          >
            <div v-if="loadingMessages" class="text-center py-8">
              <span
                class="material-symbols-outlined text-2xl text-text-secondary animate-spin"
                >progress_activity</span
              >
            </div>

            <template v-else>
              <div v-if="messages.length === 0" class="text-center py-8">
                <p class="text-sm text-text-secondary">
                  Belum ada pesan. Kirim pesan pertama!
                </p>
              </div>

              <div v-for="(msg, index) in messages" :key="msg.id">
                <!-- Date separator -->
                <div
                  v-if="shouldShowDate(index)"
                  class="flex items-center gap-3 my-4"
                >
                  <div class="flex-1 h-px bg-border-light"></div>
                  <span
                    class="text-[10px] text-text-secondary font-medium px-2"
                  >
                    {{ formatDateLabel(msg.created_at) }}
                  </span>
                  <div class="flex-1 h-px bg-border-light"></div>
                </div>

                <!-- Message Bubble -->
                <div
                  class="flex"
                  :class="
                    msg.sender_id === currentUserId
                      ? 'justify-end'
                      : 'justify-start'
                  "
                >
                  <div class="max-w-[75%] group">
                    <div
                      class="px-4 py-2.5 rounded-2xl text-sm leading-relaxed whitespace-pre-wrap break-words"
                      :class="
                        msg.sender_id === currentUserId
                          ? 'bg-primary text-white rounded-br-md'
                          : 'bg-background-light border border-border-light text-text-main rounded-bl-md'
                      "
                    >
                      {{ msg.body }}
                    </div>
                    <p
                      class="text-[10px] mt-1 px-1 opacity-0 group-hover:opacity-100 transition-opacity"
                      :class="
                        msg.sender_id === currentUserId
                          ? 'text-right text-text-secondary'
                          : 'text-left text-text-secondary'
                      "
                    >
                      {{ formatTime(msg.created_at) }}
                    </p>
                  </div>
                </div>
              </div>
            </template>
          </div>

          <!-- Message Input -->
          <div class="px-4 py-3 border-t border-border-light">
            <div class="flex items-end gap-2">
              <div class="flex-1 relative">
                <textarea
                  ref="messageInput"
                  v-model="newMessage"
                  @keydown.enter.exact.prevent="sendMessage"
                  rows="1"
                  class="w-full px-4 py-3 rounded-xl bg-background-light border border-border-light text-sm text-text-main placeholder-gray-400 focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all outline-none resize-none"
                  :style="{ height: textareaHeight }"
                  placeholder="Ketik pesan..."
                ></textarea>
              </div>
              <button
                @click="sendMessage"
                :disabled="!newMessage.trim() || sendingMessage"
                class="size-11 rounded-xl bg-primary hover:bg-primary/90 text-white flex items-center justify-center transition-all disabled:opacity-50 disabled:cursor-not-allowed shrink-0"
              >
                <span
                  v-if="sendingMessage"
                  class="material-symbols-outlined text-[20px] animate-spin"
                  >progress_activity</span
                >
                <span v-else class="material-symbols-outlined text-[20px]"
                  >send</span
                >
              </button>
            </div>
          </div>
        </template>
      </div>
    </div>
  </div>
</template>

<script setup>
import {
  ref,
  computed,
  nextTick,
  onMounted,
  onBeforeUnmount,
  watch,
} from "vue";
import { useAuthStore } from "../stores/auth";
import { chatService } from "../services/chatService";

const authStore = useAuthStore();
const currentUserId = computed(() => authStore.user?.id);

// State
const conversations = ref([]);
const activeConversation = ref(null);
const messages = ref([]);
const newMessage = ref("");
const searchQuery = ref("");
const searchResults = ref([]);
const showUserSearch = ref(false);

const loadingConversations = ref(false);
const loadingMessages = ref(false);
const sendingMessage = ref(false);
const searchLoading = ref(false);

const messagesContainer = ref(null);
const messageInput = ref(null);

let conversationPollInterval = null;
let messagePollInterval = null;
let searchDebounce = null;

// Computed
const textareaHeight = computed(() => {
  const lines = (newMessage.value.match(/\n/g) || []).length + 1;
  const h = Math.min(lines, 4) * 22 + 24;
  return `${h}px`;
});

// --- Helpers ---
const getInitials = (name) => {
  if (!name) return "?";
  return name
    .split(" ")
    .map((n) => n[0])
    .join("")
    .substring(0, 2)
    .toUpperCase();
};

const getRoleBgClass = (role) => {
  switch (role) {
    case "dosen":
      return "bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300";
    case "mahasiswa":
      return "bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300";
    case "admin":
    case "super_admin":
      return "bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300";
    case "staff":
      return "bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300";
    default:
      return "bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400";
  }
};

const getRoleLabel = (role) => {
  switch (role) {
    case "dosen":
      return "Dosen";
    case "mahasiswa":
      return "Mahasiswa";
    case "admin":
      return "Admin";
    case "super_admin":
      return "Super Admin";
    case "staff":
      return "Staff";
    default:
      return role;
  }
};

const timeAgo = (dateStr) => {
  if (!dateStr) return "";
  const now = new Date();
  const date = new Date(dateStr);
  const diff = Math.floor((now - date) / 1000);

  if (diff < 60) return "Baru saja";
  if (diff < 3600) return `${Math.floor(diff / 60)}m`;
  if (diff < 86400) return `${Math.floor(diff / 3600)}j`;
  if (diff < 604800) return `${Math.floor(diff / 86400)}h`;
  return date.toLocaleDateString("id-ID", { day: "numeric", month: "short" });
};

const formatTime = (dateStr) => {
  return new Date(dateStr).toLocaleTimeString("id-ID", {
    hour: "2-digit",
    minute: "2-digit",
  });
};

const formatDateLabel = (dateStr) => {
  const d = new Date(dateStr);
  const today = new Date();
  const yesterday = new Date(today);
  yesterday.setDate(yesterday.getDate() - 1);

  if (d.toDateString() === today.toDateString()) return "Hari ini";
  if (d.toDateString() === yesterday.toDateString()) return "Kemarin";
  return d.toLocaleDateString("id-ID", {
    day: "numeric",
    month: "long",
    year: "numeric",
  });
};

const shouldShowDate = (index) => {
  if (index === 0) return true;
  const curr = new Date(messages.value[index].created_at).toDateString();
  const prev = new Date(messages.value[index - 1].created_at).toDateString();
  return curr !== prev;
};

const scrollToBottom = (smooth = false) => {
  nextTick(() => {
    if (messagesContainer.value) {
      messagesContainer.value.scrollTo({
        top: messagesContainer.value.scrollHeight,
        behavior: smooth ? "smooth" : "auto",
      });
    }
  });
};

// --- API Calls ---
const fetchConversations = async () => {
  try {
    loadingConversations.value = conversations.value.length === 0;
    const res = await chatService.getConversations();
    if (res.success) {
      conversations.value = res.data;

      // Update active conversation's unread if open
      if (activeConversation.value) {
        const updated = res.data.find(
          (c) => c.id === activeConversation.value.id,
        );
        if (updated) {
          activeConversation.value.unread_count = updated.unread_count;
          activeConversation.value.last_message = updated.last_message;
        }
      }
    }
  } catch (err) {
    // silent
  } finally {
    loadingConversations.value = false;
  }
};

const fetchMessages = async (conversationId) => {
  try {
    loadingMessages.value = messages.value.length === 0;
    const res = await chatService.getMessages(conversationId);
    if (res.success) {
      const hadMessages = messages.value.length > 0;
      const newCount = res.data.length;
      const oldCount = messages.value.length;

      messages.value = res.data;

      // Scroll to bottom if new messages arrived or first load
      if (newCount !== oldCount || !hadMessages) {
        scrollToBottom(hadMessages);
      }
    }
  } catch (err) {
    console.error("Failed to fetch messages:", err);
  } finally {
    loadingMessages.value = false;
  }
};

const openConversation = async (conv) => {
  activeConversation.value = conv;
  messages.value = [];
  showUserSearch.value = false;
  searchQuery.value = "";

  await fetchMessages(conv.id);

  // Mark as read
  if (conv.unread_count > 0) {
    try {
      await chatService.markRead(conv.id);
      conv.unread_count = 0;
    } catch (err) {
      // silent
    }
  }

  // Start polling messages
  clearInterval(messagePollInterval);
  messagePollInterval = setInterval(() => {
    if (activeConversation.value?.id === conv.id) {
      fetchMessages(conv.id);
      chatService.markRead(conv.id).catch(() => {});
    }
  }, 3000);

  nextTick(() => {
    messageInput.value?.focus();
  });
};

const startChat = async (user) => {
  try {
    const res = await chatService.startConversation(user.id);
    if (res.success) {
      showUserSearch.value = false;
      searchQuery.value = "";

      // Refresh conversations and open the new one
      await fetchConversations();
      const conv = conversations.value.find((c) => c.id === res.data.id);
      if (conv) {
        openConversation(conv);
      } else {
        // Newly created, build a temp object
        openConversation({
          id: res.data.id,
          other_user: res.data.other_user,
          last_message: null,
          unread_count: 0,
        });
      }
    }
  } catch (err) {
    console.error("Failed to start conversation:", err);
  }
};

const sendMessage = async () => {
  const body = newMessage.value.trim();
  if (!body || sendingMessage.value || !activeConversation.value) return;

  sendingMessage.value = true;
  try {
    const res = await chatService.sendMessage(
      activeConversation.value.id,
      body,
    );
    if (res.success) {
      newMessage.value = "";
      messages.value.push(res.data);
      scrollToBottom(true);

      // Update conversation list preview
      const conv = conversations.value.find(
        (c) => c.id === activeConversation.value.id,
      );
      if (conv) {
        conv.last_message = {
          body: res.data.body,
          sender_id: res.data.sender_id,
          created_at: res.data.created_at,
        };
        conv.updated_at = res.data.created_at;
      }
    }
  } catch (err) {
    console.error("Failed to send message:", err);
  } finally {
    sendingMessage.value = false;
    nextTick(() => messageInput.value?.focus());
  }
};

const handleSearchInput = () => {
  clearTimeout(searchDebounce);
  if (!searchQuery.value.trim()) {
    searchResults.value = [];
    showUserSearch.value = false;
    return;
  }
  showUserSearch.value = true;
  searchDebounce = setTimeout(async () => {
    searchLoading.value = true;
    try {
      const res = await chatService.searchUsers(searchQuery.value);
      if (res.success) {
        searchResults.value = res.data;
      }
    } catch (err) {
      // silent
    } finally {
      searchLoading.value = false;
    }
  }, 300);
};

// --- Click outside to close search ---
const handleClickOutside = (e) => {
  const input = document.querySelector("input[placeholder*='Cari kontak']");
  if (input && !input.contains(e.target)) {
    // Allow clicks on search results
    const resultsArea = input.closest(".space-y-3")?.parentElement;
    if (resultsArea && !resultsArea.contains(e.target)) {
      showUserSearch.value = false;
    }
  }
};

// --- Lifecycle ---
onMounted(() => {
  fetchConversations();

  // Poll conversations every 5 seconds
  conversationPollInterval = setInterval(fetchConversations, 5000);

  document.addEventListener("click", handleClickOutside);
});

onBeforeUnmount(() => {
  clearInterval(conversationPollInterval);
  clearInterval(messagePollInterval);
  clearTimeout(searchDebounce);
  document.removeEventListener("click", handleClickOutside);
});
</script>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.3s ease;
}
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
