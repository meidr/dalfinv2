<template>
  <!-- Floating Chat Button -->
  <div class="fixed bottom-6 right-6 z-50">
    <!-- Chat Panel -->
    <transition name="chat-panel">
      <div
        v-if="isOpen"
        class="absolute bottom-16 right-0 w-[380px] h-[520px] bg-surface-light dark:bg-sidebar-light rounded-2xl shadow-2xl border border-border-light flex flex-col overflow-hidden"
      >
        <!-- Panel Header -->
        <div
          class="bg-primary text-white px-4 py-3 flex items-center justify-between shrink-0"
        >
          <template v-if="activeConversation && !showNewChat">
            <button
              @click="goBack"
              class="hover:bg-white/20 rounded-lg p-1 transition-colors mr-2"
            >
              <span class="material-symbols-outlined text-[20px]"
                >arrow_back</span
              >
            </button>
            <div class="flex items-center gap-2 flex-1 min-w-0">
              <div
                class="size-8 rounded-full flex items-center justify-center text-xs font-bold shrink-0"
                :class="getRoleBgClass(activeConversation.other_user.role)"
              >
                {{ getInitials(activeConversation.other_user.name) }}
              </div>
              <div class="min-w-0">
                <p class="text-sm font-bold truncate">
                  {{ activeConversation.other_user.name }}
                </p>
                <p class="text-[10px] opacity-80 capitalize">
                  {{ getRoleLabel(activeConversation.other_user.role) }}
                </p>
              </div>
            </div>
          </template>
          <template v-else>
            <div class="flex items-center gap-2">
              <span class="material-symbols-outlined text-[22px]">chat</span>
              <span class="font-bold text-sm">Chat</span>
            </div>
          </template>
          <button
            @click="isOpen = false"
            class="hover:bg-white/20 rounded-lg p-1 transition-colors"
          >
            <span class="material-symbols-outlined text-[20px]">close</span>
          </button>
        </div>

        <!-- Views -->
        <template v-if="activeConversation && !showNewChat">
          <!-- Message Thread -->
          <div
            ref="messagesContainer"
            class="flex-1 overflow-y-auto px-3 py-3 space-y-2"
          >
            <div v-if="loadingMessages" class="text-center py-8">
              <span
                class="material-symbols-outlined text-xl text-text-secondary animate-spin"
                >progress_activity</span
              >
            </div>
            <template v-else>
              <div v-if="messages.length === 0" class="text-center py-8">
                <p class="text-xs text-text-secondary">
                  Belum ada pesan. Kirim pesan pertama!
                </p>
              </div>
              <div v-for="(msg, index) in messages" :key="msg.id">
                <!-- Date separator -->
                <div
                  v-if="shouldShowDate(index)"
                  class="flex items-center gap-2 my-3"
                >
                  <div class="flex-1 h-px bg-border-light"></div>
                  <span class="text-[10px] text-text-secondary font-medium">{{
                    formatDateLabel(msg.created_at)
                  }}</span>
                  <div class="flex-1 h-px bg-border-light"></div>
                </div>
                <!-- Bubble -->
                <div
                  class="flex"
                  :class="
                    msg.sender_id === currentUserId
                      ? 'justify-end'
                      : 'justify-start'
                  "
                >
                  <div class="max-w-[80%] group">
                    <div
                      class="px-3 py-2 rounded-2xl text-[13px] leading-relaxed whitespace-pre-wrap break-words"
                      :class="
                        msg.sender_id === currentUserId
                          ? 'bg-primary text-white rounded-br-md'
                          : 'bg-background-light dark:bg-background-dark border border-border-light text-text-main rounded-bl-md'
                      "
                    >
                      {{ msg.body }}
                    </div>
                    <p
                      class="text-[9px] mt-0.5 px-1 opacity-0 group-hover:opacity-100 transition-opacity"
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
          <div class="px-3 py-2 border-t border-border-light shrink-0">
            <div class="flex items-end gap-2">
              <textarea
                ref="messageInput"
                v-model="newMessage"
                @keydown.enter.exact.prevent="sendMessage"
                rows="1"
                class="flex-1 px-3 py-2 rounded-xl bg-background-light dark:bg-background-dark border border-border-light text-[13px] text-text-main placeholder-gray-400 focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all outline-none resize-none"
                :style="{ height: textareaHeight }"
                placeholder="Ketik pesan..."
              ></textarea>
              <button
                @click="sendMessage"
                :disabled="!newMessage.trim() || sendingMessage"
                class="size-9 rounded-xl bg-primary hover:bg-primary/90 text-white flex items-center justify-center transition-all disabled:opacity-50 shrink-0"
              >
                <span
                  v-if="sendingMessage"
                  class="material-symbols-outlined text-[18px] animate-spin"
                  >progress_activity</span
                >
                <span v-else class="material-symbols-outlined text-[18px]"
                  >send</span
                >
              </button>
            </div>
          </div>
        </template>

        <template v-else>
          <!-- Search / New Chat -->
          <div class="px-3 py-2 border-b border-border-light shrink-0">
            <div class="relative">
              <span
                class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-text-secondary text-[16px]"
                >search</span
              >
              <input
                v-model="searchQuery"
                @input="handleSearchInput"
                @focus="showNewChat = true"
                class="w-full pl-8 pr-3 py-2 rounded-lg bg-background-light dark:bg-background-dark border border-border-light text-[13px] text-text-main placeholder-gray-400 focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all outline-none"
                placeholder="Cari kontak..."
              />
            </div>
          </div>

          <!-- New Chat Search Results -->
          <div
            v-if="showNewChat && searchQuery.length > 0"
            class="flex-1 overflow-y-auto"
          >
            <div class="px-3 py-1.5">
              <p
                class="text-[10px] font-bold text-text-secondary uppercase tracking-wider"
              >
                Mulai Chat Baru
              </p>
            </div>
            <div v-if="searchLoading" class="p-4 text-center">
              <span
                class="material-symbols-outlined text-xl text-text-secondary animate-spin"
                >progress_activity</span
              >
            </div>
            <div v-else-if="searchResults.length === 0" class="p-4 text-center">
              <p class="text-xs text-text-secondary">Tidak ditemukan</p>
            </div>
            <div
              v-for="user in searchResults"
              :key="user.id"
              @click="startChat(user)"
              class="flex items-center gap-2.5 px-3 py-2.5 hover:bg-sidebar-light/50 dark:hover:bg-background-dark cursor-pointer transition-colors"
            >
              <div
                class="size-9 rounded-full flex items-center justify-center text-xs font-bold shrink-0"
                :class="getRoleBgClass(user.role)"
              >
                {{ getInitials(user.name) }}
              </div>
              <div class="min-w-0">
                <p class="text-[13px] font-medium text-text-main truncate">
                  {{ user.name }}
                </p>
                <p class="text-[11px] text-text-secondary truncate">
                  {{ user.subtitle }}
                </p>
              </div>
            </div>
          </div>

          <!-- Conversation List -->
          <div v-else class="flex-1 overflow-y-auto">
            <div
              v-if="loadingConversations && conversations.length === 0"
              class="p-4 text-center"
            >
              <span
                class="material-symbols-outlined text-xl text-text-secondary animate-spin"
                >progress_activity</span
              >
            </div>
            <div v-else-if="conversations.length === 0" class="p-6 text-center">
              <span
                class="material-symbols-outlined text-3xl text-text-secondary/40 mb-2 block"
                >forum</span
              >
              <p class="text-xs text-text-secondary">Belum ada percakapan</p>
              <p class="text-[10px] text-text-secondary mt-0.5">
                Cari kontak untuk mulai chat
              </p>
            </div>
            <div
              v-for="conv in conversations"
              :key="conv.id"
              @click="openConversation(conv)"
              class="flex items-center gap-2.5 px-3 py-2.5 cursor-pointer transition-colors border-b border-border-light/50 hover:bg-sidebar-light/50 dark:hover:bg-background-dark"
            >
              <div
                class="size-9 rounded-full flex items-center justify-center text-xs font-bold shrink-0"
                :class="getRoleBgClass(conv.other_user.role)"
              >
                {{ getInitials(conv.other_user.name) }}
              </div>
              <div class="min-w-0 flex-1">
                <div class="flex justify-between items-center">
                  <p
                    class="text-[13px] font-medium text-text-main truncate"
                    :class="{ 'font-bold': conv.unread_count > 0 }"
                  >
                    {{ conv.other_user.name }}
                  </p>
                  <span
                    class="text-[9px] text-text-secondary whitespace-nowrap ml-1"
                    >{{ timeAgo(conv.updated_at) }}</span
                  >
                </div>
                <div class="flex justify-between items-center mt-0.5">
                  <p
                    class="text-[11px] truncate"
                    :class="
                      conv.unread_count > 0
                        ? 'text-text-main font-medium'
                        : 'text-text-secondary'
                    "
                  >
                    {{
                      conv.last_message
                        ? (conv.last_message.sender_id === currentUserId
                            ? "Anda: "
                            : "") + conv.last_message.body
                        : "Belum ada pesan"
                    }}
                  </p>
                  <span
                    v-if="conv.unread_count > 0"
                    class="ml-1 bg-primary text-white text-[9px] font-bold rounded-full size-4 flex items-center justify-center shrink-0"
                  >
                    {{ conv.unread_count > 9 ? "9+" : conv.unread_count }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </template>
      </div>
    </transition>

    <!-- FAB Button -->
    <button
      @click="toggleChat"
      class="size-14 rounded-full bg-primary hover:bg-primary/90 text-white shadow-lg shadow-primary/30 flex items-center justify-center transition-all hover:scale-105 active:scale-95"
    >
      <span v-if="isOpen" class="material-symbols-outlined text-[26px]"
        >close</span
      >
      <span v-else class="material-symbols-outlined text-[26px]">chat</span>
      <!-- Unread Badge -->
      <span
        v-if="!isOpen && totalUnread > 0"
        class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold rounded-full min-w-[20px] h-5 flex items-center justify-center px-1 animate-bounce"
      >
        {{ totalUnread > 99 ? "99+" : totalUnread }}
      </span>
    </button>
  </div>
</template>

<script setup>
import { ref, computed, nextTick, onMounted, onBeforeUnmount } from "vue";
import { useAuthStore } from "../stores/auth";
import { chatService } from "../services/chatService";

const authStore = useAuthStore();
const currentUserId = computed(() => authStore.user?.id);

// State
const isOpen = ref(false);
const conversations = ref([]);
const activeConversation = ref(null);
const messages = ref([]);
const newMessage = ref("");
const searchQuery = ref("");
const searchResults = ref([]);
const showNewChat = ref(false);
const totalUnread = ref(0);

const loadingConversations = ref(false);
const loadingMessages = ref(false);
const sendingMessage = ref(false);
const searchLoading = ref(false);

const messagesContainer = ref(null);
const messageInput = ref(null);

let conversationPollInterval = null;
let messagePollInterval = null;
let unreadPollInterval = null;
let searchDebounce = null;

// Computed
const textareaHeight = computed(() => {
  const lines = (newMessage.value.match(/\n/g) || []).length + 1;
  return `${Math.min(lines, 3) * 20 + 20}px`;
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
      return "bg-gray-100 text-gray-600";
  }
};

const getRoleLabel = (role) => {
  const labels = {
    dosen: "Dosen",
    mahasiswa: "Mahasiswa",
    admin: "Admin",
    super_admin: "Super Admin",
    staff: "Staff",
  };
  return labels[role] || role;
};

const timeAgo = (dateStr) => {
  if (!dateStr) return "";
  const diff = Math.floor((new Date() - new Date(dateStr)) / 1000);
  if (diff < 60) return "Baru";
  if (diff < 3600) return `${Math.floor(diff / 60)}m`;
  if (diff < 86400) return `${Math.floor(diff / 3600)}j`;
  if (diff < 604800) return `${Math.floor(diff / 86400)}h`;
  return new Date(dateStr).toLocaleDateString("id-ID", {
    day: "numeric",
    month: "short",
  });
};

const formatTime = (dateStr) =>
  new Date(dateStr).toLocaleTimeString("id-ID", {
    hour: "2-digit",
    minute: "2-digit",
  });

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
  return (
    new Date(messages.value[index].created_at).toDateString() !==
    new Date(messages.value[index - 1].created_at).toDateString()
  );
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

// --- API ---
const fetchUnreadCount = async () => {
  try {
    const res = await chatService.getUnreadCount();
    if (res.success) totalUnread.value = res.count;
  } catch (err) {
    /* silent */
  }
};

const fetchConversations = async () => {
  try {
    loadingConversations.value = conversations.value.length === 0;
    const res = await chatService.getConversations();
    if (res.success) {
      conversations.value = res.data;
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
    /* silent */
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
      if (newCount !== oldCount || !hadMessages) scrollToBottom(hadMessages);
    }
  } catch (err) {
    console.error(err);
  } finally {
    loadingMessages.value = false;
  }
};

const toggleChat = () => {
  isOpen.value = !isOpen.value;
  if (isOpen.value) {
    fetchConversations();
    activeConversation.value = null;
    showNewChat.value = false;
    searchQuery.value = "";
  }
};

const goBack = () => {
  activeConversation.value = null;
  clearInterval(messagePollInterval);
  fetchConversations();
};

const openConversation = async (conv) => {
  activeConversation.value = conv;
  showNewChat.value = false;
  searchQuery.value = "";
  messages.value = [];
  await fetchMessages(conv.id);

  if (conv.unread_count > 0) {
    try {
      await chatService.markRead(conv.id);
      conv.unread_count = 0;
      fetchUnreadCount();
    } catch (err) {}
  }

  clearInterval(messagePollInterval);
  messagePollInterval = setInterval(() => {
    if (activeConversation.value?.id === conv.id) {
      fetchMessages(conv.id);
      chatService.markRead(conv.id).catch(() => {});
    }
  }, 3000);

  nextTick(() => messageInput.value?.focus());
};

const startChat = async (user) => {
  try {
    const res = await chatService.startConversation(user.id);
    if (res.success) {
      showNewChat.value = false;
      searchQuery.value = "";
      await fetchConversations();
      const conv = conversations.value.find((c) => c.id === res.data.id);
      openConversation(
        conv || {
          id: res.data.id,
          other_user: res.data.other_user,
          last_message: null,
          unread_count: 0,
        },
      );
    }
  } catch (err) {
    console.error(err);
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
    console.error(err);
  } finally {
    sendingMessage.value = false;
    nextTick(() => messageInput.value?.focus());
  }
};

const handleSearchInput = () => {
  clearTimeout(searchDebounce);
  if (!searchQuery.value.trim()) {
    searchResults.value = [];
    showNewChat.value = false;
    return;
  }
  showNewChat.value = true;
  searchDebounce = setTimeout(async () => {
    searchLoading.value = true;
    try {
      const res = await chatService.searchUsers(searchQuery.value);
      if (res.success) searchResults.value = res.data;
    } catch (err) {
      /* silent */
    } finally {
      searchLoading.value = false;
    }
  }, 300);
};

// --- Lifecycle ---
onMounted(() => {
  fetchUnreadCount();
  fetchConversations();
  // Poll unread count every 10 seconds (lightweight)
  unreadPollInterval = setInterval(fetchUnreadCount, 10000);
  // Poll conversations every 5 seconds when panel is open
  conversationPollInterval = setInterval(() => {
    if (isOpen.value) fetchConversations();
  }, 5000);
});

onBeforeUnmount(() => {
  clearInterval(conversationPollInterval);
  clearInterval(messagePollInterval);
  clearInterval(unreadPollInterval);
  clearTimeout(searchDebounce);
});
</script>

<style scoped>
.chat-panel-enter-active,
.chat-panel-leave-active {
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}
.chat-panel-enter-from,
.chat-panel-leave-to {
  opacity: 0;
  transform: translateY(16px) scale(0.95);
}
</style>
