import { ref, readonly } from 'vue';

const visible = ref(false);
const message = ref('');
const type = ref('info');
let timeout = null;

export const useAlert = () => {
  const showAlert = (msg, alertType = 'info', duration = 6000) => {
    message.value = msg;
    type.value = alertType;
    visible.value = true;

    if (timeout) clearTimeout(timeout);

    timeout = setTimeout(() => {
      visible.value = false;
    }, duration);
  };

  const hideAlert = () => {
    visible.value = false;
    if (timeout) clearTimeout(timeout);
  };

  return {
    visible: readonly(visible),
    message: readonly(message),
    type: readonly(type),
    showAlert,
    hideAlert
  };
};