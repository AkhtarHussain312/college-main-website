<script setup>
import { nextTick, ref } from 'vue';
import { sendChatMessage } from '../services/api';

const isOpen = ref(false);
const inputMessage = ref('');
const loading = ref(false);
const messagesContainer = ref(null);
const inputField = ref(null);

const initialGreeting = {
  id: 1,
  role: 'model',
  text: "Hello! 👋 I'm the official AI Assistant for Dir College of Nursing (DCN).\n\nAsk me anything about our academic programs, fee structures, eligibility criteria, or how to apply!",
  time: getCurrentTime(),
};

const messages = ref([initialGreeting]);

const suggestionChips = [
  'What programs do you offer?',
  'What is the fee structure?',
  'How do I apply for admission?',
  'What are your office hours & contact numbers?',
];

function getCurrentTime() {
  const now = new Date();
  return now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}

function toggleChat() {
  isOpen.value = !isOpen.value;
  if (isOpen.value) {
    nextTick(() => {
      scrollToBottom();
      inputField.value?.focus();
    });
  }
}

function closeChat() {
  isOpen.value = false;
}

function resetChat() {
  messages.value = [
    {
      id: Date.now(),
      role: 'model',
      text: "Conversation reset! How can I assist you with programs, fees, or admissions at DCN today?",
      time: getCurrentTime(),
    },
  ];
  nextTick(() => scrollToBottom());
}

function scrollToBottom() {
  if (messagesContainer.value) {
    messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
  }
}

async function handleSend(textToSend) {
  const text = (typeof textToSend === 'string' ? textToSend : inputMessage.value).trim();
  if (!text || loading.value) return;

  const userMsg = {
    id: Date.now(),
    role: 'user',
    text,
    time: getCurrentTime(),
  };

  messages.value.push(userMsg);
  inputMessage.value = '';
  loading.value = true;
  nextTick(() => scrollToBottom());

  // Prepare recent history payload
  const historyPayload = messages.value
    .filter((m) => m.role === 'user' || m.role === 'model')
    .slice(-6)
    .map((m) => ({
      role: m.role,
      text: m.text,
    }));

  try {
    const res = await sendChatMessage({
      message: text,
      history: historyPayload,
    });

    const replyText = res.data?.reply || 'Thank you for your message. Please contact our admissions team directly for detailed assistance.';
    messages.value.push({
      id: Date.now() + 1,
      role: 'model',
      text: replyText,
      time: getCurrentTime(),
    });
  } catch (err) {
    messages.value.push({
      id: Date.now() + 1,
      role: 'model',
      text: "I'm having trouble connecting at the moment. Please contact our Admissions Office at +92 301 1234567 or WhatsApp +92 300 1234567.",
      time: getCurrentTime(),
    });
  } finally {
    loading.value = false;
    nextTick(() => {
      scrollToBottom();
      inputField.value?.focus();
    });
  }
}

function onKeydown(e) {
  if (e.key === 'Enter' && !e.shiftKey) {
    e.preventDefault();
    handleSend();
  }
}

// Simple and safe text formatting for bot responses
function formatMessage(raw) {
  if (!raw) return '';
  // Escape HTML entities to prevent XSS
  let text = raw
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;');

  // Bold **text**
  text = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');

  // Convert bullet points (* or -) into bulleted lines
  text = text.replace(/^[\*\-]\s+(.*)$/gm, '<li class="dcn-bullet">$1</li>');
  text = text.replace(/(<li class="dcn-bullet">.*?<\/li>)+/gs, '<ul class="dcn-list">$&</ul>');

  // Convert URLs or relative /apply links
  text = text.replace(/\/apply/g, '<a href="/apply" class="dcn-link">/apply</a>');

  // Newlines to <br> outside <ul>
  text = text.replace(/\n\n/g, '<div class="dcn-p-break"></div>');
  text = text.replace(/\n/g, '<br>');

  return text;
}
</script>

<template>
  <div class="dcn-chat-root">
    <!-- Floating Trigger Button -->
    <button
      type="button"
      class="dcn-chat-trigger"
      :class="{ 'dcn-trigger-active': isOpen }"
      :aria-label="isOpen ? 'Close chat assistant' : 'Ask AI Assistant'"
      @click="toggleChat"
    >
      <span v-if="!isOpen" class="dcn-trigger-content">
        <i class="bi bi-chat-dots-fill dcn-trigger-icon"></i>
        <span class="dcn-trigger-label">Ask Assistant</span>
        <span class="dcn-pulse-dot" title="Online"></span>
      </span>
      <span v-else class="dcn-trigger-content">
        <i class="bi bi-x-lg dcn-close-icon"></i>
      </span>
    </button>

    <!-- Chat Modal Window -->
    <transition name="dcn-chat-slide">
      <div v-if="isOpen" class="dcn-chat-window" role="dialog" aria-modal="true" aria-label="DCN AI Chat Assistant">
        <!-- Header -->
        <div class="dcn-chat-header">
          <div class="dcn-header-info">
            <div class="dcn-avatar">
              <i class="bi bi-mortarboard-fill"></i>
              <span class="dcn-status-indicator"></span>
            </div>
            <div class="dcn-header-text">
              <h3 class="dcn-title">DCN Admissions AI</h3>
              <p class="dcn-subtitle">
                <span class="dcn-live-dot"></span>
                Online &bull; Powered by Gemini 2.5
              </p>
            </div>
          </div>
          <div class="dcn-header-actions">
            <button
              type="button"
              class="dcn-icon-btn"
              title="Reset conversation"
              aria-label="Reset conversation"
              @click="resetChat"
            >
              <i class="bi bi-arrow-counterclockwise"></i>
            </button>
            <button
              type="button"
              class="dcn-icon-btn"
              title="Close chat"
              aria-label="Close chat"
              @click="closeChat"
            >
              <i class="bi bi-x-lg"></i>
            </button>
          </div>
        </div>

        <!-- Messages Area -->
        <div ref="messagesContainer" class="dcn-chat-body">
          <div
            v-for="msg in messages"
            :key="msg.id"
            class="dcn-message-row"
            :class="msg.role === 'user' ? 'dcn-user-row' : 'dcn-bot-row'"
          >
            <div v-if="msg.role === 'model'" class="dcn-bot-badge">
              <i class="bi bi-stars"></i>
            </div>
            <div class="dcn-message-bubble" :class="msg.role === 'user' ? 'dcn-user-bubble' : 'dcn-bot-bubble'">
              <div class="dcn-message-text" v-html="formatMessage(msg.text)"></div>
              <div class="dcn-message-time">{{ msg.time }}</div>
            </div>
          </div>

          <!-- Typing Indicator -->
          <div v-if="loading" class="dcn-message-row dcn-bot-row">
            <div class="dcn-bot-badge">
              <i class="bi bi-stars"></i>
            </div>
            <div class="dcn-message-bubble dcn-bot-bubble dcn-typing-bubble">
              <span class="dcn-typing-dot"></span>
              <span class="dcn-typing-dot"></span>
              <span class="dcn-typing-dot"></span>
            </div>
          </div>

          <!-- Suggestion Chips (when user hasn't sent a message yet) -->
          <div v-if="messages.length === 1 && !loading" class="dcn-chips-container">
            <p class="dcn-chips-label">Frequently asked questions:</p>
            <div class="dcn-chips-list">
              <button
                v-for="(chip, i) in suggestionChips"
                :key="i"
                type="button"
                class="dcn-chip-btn"
                @click="handleSend(chip)"
              >
                {{ chip }}
              </button>
            </div>
          </div>
        </div>

        <!-- Input Area -->
        <div class="dcn-chat-footer">
          <form class="dcn-input-form" @submit.prevent="handleSend()">
            <input
              ref="inputField"
              v-model="inputMessage"
              type="text"
              class="dcn-input"
              placeholder="Ask about programs, fees, admissions..."
              :disabled="loading"
              maxlength="1000"
              @keydown="onKeydown"
            />
            <button
              type="submit"
              class="dcn-send-btn"
              :disabled="!inputMessage.trim() || loading"
              aria-label="Send inquiry"
            >
              <i class="bi bi-send-fill"></i>
            </button>
          </form>
          <div class="dcn-footer-note">
            Official DCN AI Assistant &bull; Verify critical admissions requirements with staff
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<style scoped>
.dcn-chat-root {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
  position: relative;
  z-index: 99999;
}

/* Floating Trigger Button */
.dcn-chat-trigger {
  position: fixed;
  bottom: 24px;
  right: 24px;
  z-index: 99999;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 12px 20px;
  background: linear-gradient(135deg, #071f33 0%, #0d9488 100%);
  color: #ffffff;
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 9999px;
  box-shadow: 0 10px 25px -5px rgba(13, 148, 136, 0.4), 0 8px 10px -6px rgba(7, 31, 51, 0.3);
  cursor: pointer;
  transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
  outline: none;
}

.dcn-chat-trigger:hover {
  transform: translateY(-2px) scale(1.02);
  box-shadow: 0 14px 28px -5px rgba(13, 148, 136, 0.5), 0 10px 10px -5px rgba(7, 31, 51, 0.3);
}

.dcn-trigger-active {
  padding: 14px;
  background: #0f172a;
  border-radius: 50%;
  box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.4);
}

.dcn-trigger-content {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  font-weight: 600;
  letter-spacing: 0.01em;
}

.dcn-trigger-icon {
  font-size: 18px;
}

.dcn-close-icon {
  font-size: 18px;
}

.dcn-pulse-dot {
  width: 8px;
  height: 8px;
  background-color: #22c55e;
  border-radius: 50%;
  box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.8), 0 0 8px #22c55e;
}

/* Chat Window */
.dcn-chat-window {
  position: fixed;
  bottom: 88px;
  right: 24px;
  width: 380px;
  max-width: calc(100vw - 32px);
  height: 560px;
  max-height: calc(100vh - 110px);
  background: #ffffff;
  border-radius: 20px;
  box-shadow: 0 20px 40px -15px rgba(2, 6, 23, 0.3), 0 0 0 1px rgba(226, 232, 240, 0.9);
  display: flex;
  flex-direction: column;
  overflow: hidden;
  z-index: 99999;
  animation: dcnFadeIn 0.25s ease-out;
}

/* Slide Transition */
.dcn-chat-slide-enter-active,
.dcn-chat-slide-leave-active {
  transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}

.dcn-chat-slide-enter-from,
.dcn-chat-slide-leave-to {
  opacity: 0;
  transform: translateY(16px) scale(0.96);
}

/* Header */
.dcn-chat-header {
  padding: 16px 18px;
  background: linear-gradient(135deg, #071f33 0%, #0f2d4a 100%);
  color: #ffffff;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.dcn-header-info {
  display: flex;
  align-items: center;
  gap: 12px;
}

.dcn-avatar {
  position: relative;
  width: 38px;
  height: 38px;
  border-radius: 12px;
  background: rgba(13, 148, 136, 0.25);
  border: 1px solid rgba(13, 148, 136, 0.5);
  color: #2dd4bf;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
}

.dcn-status-indicator {
  position: absolute;
  bottom: -2px;
  right: -2px;
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background-color: #22c55e;
  border: 2px solid #071f33;
}

.dcn-header-text {
  display: flex;
  flex-direction: column;
}

.dcn-title {
  margin: 0;
  font-size: 15px;
  font-weight: 700;
  line-height: 1.2;
  color: #ffffff;
}

.dcn-subtitle {
  margin: 2px 0 0 0;
  font-size: 11px;
  color: #94a3b8;
  display: flex;
  align-items: center;
  gap: 4px;
}

.dcn-live-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background-color: #22c55e;
  display: inline-block;
}

.dcn-header-actions {
  display: flex;
  align-items: center;
  gap: 4px;
}

.dcn-icon-btn {
  background: transparent;
  border: none;
  color: #94a3b8;
  padding: 6px;
  border-radius: 8px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.15s ease;
}

.dcn-icon-btn:hover {
  color: #ffffff;
  background: rgba(255, 255, 255, 0.1);
}

/* Chat Body */
.dcn-chat-body {
  flex: 1;
  padding: 16px;
  overflow-y: auto;
  background-color: #f8fafc;
  display: flex;
  flex-direction: column;
  gap: 14px;
  scroll-behavior: smooth;
}

.dcn-chat-body::-webkit-scrollbar {
  width: 6px;
}

.dcn-chat-body::-webkit-scrollbar-thumb {
  background-color: #cbd5e1;
  border-radius: 3px;
}

/* Message Rows */
.dcn-message-row {
  display: flex;
  gap: 8px;
  align-items: flex-end;
  max-width: 90%;
}

.dcn-bot-row {
  align-self: flex-start;
}

.dcn-user-row {
  align-self: flex-end;
  flex-direction: row-reverse;
}

.dcn-bot-badge {
  width: 26px;
  height: 26px;
  border-radius: 50%;
  background: #0d9488;
  color: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  flex-shrink: 0;
  margin-bottom: 4px;
}

.dcn-message-bubble {
  padding: 10px 14px;
  font-size: 13.5px;
  line-height: 1.5;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
  word-break: break-word;
}

.dcn-bot-bubble {
  background-color: #ffffff;
  color: #1e293b;
  border: 1px solid #e2e8f0;
  border-radius: 16px 16px 16px 4px;
}

.dcn-user-bubble {
  background: linear-gradient(135deg, #071f33 0%, #0d9488 100%);
  color: #ffffff;
  border-radius: 16px 16px 4px 16px;
}

.dcn-message-text :deep(.dcn-p-break) {
  height: 8px;
}

.dcn-message-text :deep(.dcn-list) {
  margin: 6px 0 6px 18px;
  padding: 0;
}

.dcn-message-text :deep(.dcn-bullet) {
  margin-bottom: 4px;
}

.dcn-message-text :deep(.dcn-link) {
  color: #0d9488;
  font-weight: 600;
  text-decoration: underline;
}

.dcn-user-bubble .dcn-message-text :deep(.dcn-link) {
  color: #a7f3d0;
}

.dcn-message-time {
  font-size: 10px;
  margin-top: 4px;
  opacity: 0.65;
  text-align: right;
}

/* Typing Bubble */
.dcn-typing-bubble {
  display: flex;
  align-items: center;
  gap: 5px;
  padding: 12px 16px;
}

.dcn-typing-dot {
  width: 6px;
  height: 6px;
  background-color: #94a3b8;
  border-radius: 50%;
  animation: dcnBounce 1.4s infinite ease-in-out both;
}

.dcn-typing-dot:nth-child(1) {
  animation-delay: -0.32s;
}

.dcn-typing-dot:nth-child(2) {
  animation-delay: -0.16s;
}

@keyframes dcnBounce {
  0%, 80%, 100% {
    transform: scale(0);
  }
  40% {
    transform: scale(1);
  }
}

/* Suggestion Chips */
.dcn-chips-container {
  margin-top: 4px;
}

.dcn-chips-label {
  font-size: 11px;
  font-weight: 600;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin-bottom: 8px;
}

.dcn-chips-list {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.dcn-chip-btn {
  background-color: #ffffff;
  border: 1px solid #cbd5e1;
  color: #0f172a;
  padding: 8px 12px;
  border-radius: 10px;
  font-size: 12.5px;
  text-align: left;
  cursor: pointer;
  transition: all 0.15s ease;
  line-height: 1.3;
}

.dcn-chip-btn:hover {
  background-color: rgba(13, 148, 136, 0.08);
  border-color: #0d9488;
  color: #0d9488;
  transform: translateX(3px);
}

/* Footer & Input */
.dcn-chat-footer {
  padding: 12px 14px;
  background: #ffffff;
  border-top: 1px solid #e2e8f0;
}

.dcn-input-form {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #f1f5f9;
  border: 1px solid #cbd5e1;
  border-radius: 12px;
  padding: 4px 6px 4px 12px;
  transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

.dcn-input-form:focus-within {
  border-color: #0d9488;
  box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.15);
  background: #ffffff;
}

.dcn-input {
  flex: 1;
  border: none;
  background: transparent;
  font-size: 13.5px;
  color: #0f172a;
  outline: none;
  padding: 6px 0;
}

.dcn-input::placeholder {
  color: #94a3b8;
}

.dcn-send-btn {
  background: #0d9488;
  border: none;
  color: #ffffff;
  width: 32px;
  height: 32px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.15s ease;
  flex-shrink: 0;
}

.dcn-send-btn:hover:not(:disabled) {
  background: #0f766e;
  transform: scale(1.05);
}

.dcn-send-btn:disabled {
  background: #cbd5e1;
  cursor: not-allowed;
  opacity: 0.7;
}

.dcn-footer-note {
  margin-top: 8px;
  font-size: 10px;
  color: #94a3b8;
  text-align: center;
}

/* Mobile Adjustments */
@media (max-width: 640px) {
  .dcn-chat-window {
    right: 16px;
    bottom: 80px;
    width: calc(100vw - 32px);
    height: calc(100vh - 100px);
    border-radius: 16px;
  }

  .dcn-chat-trigger {
    right: 16px;
    bottom: 16px;
    padding: 10px 16px;
  }
}
</style>
