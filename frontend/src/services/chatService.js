import api from "./api";

export const chatService = {
  // Get conversation list
  async getConversations() {
    const response = await api.get("/chat/conversations", {
      skipProgress: true,
    });
    return response.data;
  },

  // Get all conversations (Admin)
  async getAllConversations(params = {}) {
    const response = await api.get("/chat/admin/conversations", {
      params,
    });
    return response.data;
  },

  // Start or get existing conversation
  async startConversation(userId) {
    const response = await api.post(
      "/chat/conversations",
      {
        user_id: userId,
      },
      { skipProgress: true },
    );
    return response.data;
  },

  // Get messages for a conversation
  async getMessages(conversationId) {
    const response = await api.get(
      `/chat/conversations/${conversationId}/messages`,
      { skipProgress: true },
    );
    return response.data;
  },

  // Send a message
  async sendMessage(conversationId, body) {
    const response = await api.post(
      `/chat/conversations/${conversationId}/messages`,
      { body },
      { skipProgress: true },
    );
    return response.data;
  },

  // Mark conversation as read
  async markRead(conversationId) {
    const response = await api.put(
      `/chat/conversations/${conversationId}/read`,
      {},
      { skipProgress: true },
    );
    return response.data;
  },

  // Get total unread count
  async getUnreadCount() {
    const response = await api.get("/chat/unread-count", {
      skipProgress: true,
    });
    return response.data;
  },

  // Search users to start chat
  async searchUsers(search = "") {
    const response = await api.get("/chat/users", {
      params: { search },
      skipProgress: true,
    });
    return response.data;
  },
};

export default chatService;
