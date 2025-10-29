<template>
  <VAlert
    v-if="visible"
    :type="type"
    dense
    closable
    variant="flat"
    class="custom-alert"
  >
    <span class="alert-text">{{ message }}</span>
  </VAlert>
</template>

<script setup>
import { useAlert } from '@/composables/useAlert.js'

const { visible, message, type } = useAlert()
</script>

<style scoped>
.custom-alert {
  position: fixed;
  bottom: 24px;
  right: 24px;
  left: auto;
  max-width: 600px;
  min-width: 320px;
  width: auto;
  z-index: 9999;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  border-radius: 8px;
}

.alert-text {
  font-size: 1rem;
  line-height: 1.5;
  color: white;
  word-break: break-word;
}

@media (min-width: 1920px) {
  .custom-alert {
    right: 48px;
    bottom: 48px;
    max-width: 700px;
  }
  
  .alert-text {
    font-size: 1.125rem;
  }
}

@media (min-width: 1024px) and (max-width: 1919px) {
  .custom-alert {
    right: 32px;
    bottom: 32px;
    max-width: 550px;
  }
}

@media (min-width: 768px) and (max-width: 1023px) {
  .custom-alert {
    right: 24px;
    bottom: 24px;
    max-width: 450px;
    min-width: 280px;
  }
  
  .alert-text {
    font-size: 0.9375rem;
  }
}

@media (min-width: 600px) and (max-width: 767px) {
  .custom-alert {
    right: 16px;
    left: 16px;
    bottom: 20px;
    max-width: none;
    min-width: auto;
    width: auto;
  }
  
  .alert-text {
    font-size: 0.875rem;
  }
}

@media (min-width: 480px) and (max-width: 599px) {
  .custom-alert {
    right: 12px;
    left: 12px;
    bottom: 16px;
    max-width: none;
    min-width: auto;
    width: auto;
  }
  
  .alert-text {
    font-size: 0.875rem;
    line-height: 1.4;
  }
}

@media (max-width: 479px) {
  .custom-alert {
    right: 8px;
    left: 8px;
    bottom: 12px;
    max-width: none;
    min-width: auto;
    width: auto;
  }
  
  .alert-text {
    font-size: 0.8125rem;
    line-height: 1.3;
  }
}

@media (max-width: 319px) {
  .custom-alert {
    right: 4px;
    left: 4px;
    bottom: 8px;
  }
  
  .alert-text {
    font-size: 0.75rem;
  }
}

@media (max-height: 500px) and (orientation: landscape) {
  .custom-alert {
    bottom: 8px;
    right: 16px;
    max-width: 400px;
  }
  
  .alert-text {
    font-size: 0.8125rem;
  }
}

@media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
  .custom-alert {
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
  }
}

@media (prefers-reduced-motion: reduce) {
  .custom-alert {
    transition: none;
  }
}

@media (prefers-color-scheme: dark) {
  .custom-alert {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
  }
}
</style>