<template>
  <RouterView></RouterView>
</template>

<script setup lang="ts">
import { RouterView } from "vue-router";
import { useAuthStore } from '@/stores/auth';
const auth: any = useAuthStore();

if (auth.user || auth.hse_tok_passer) {
  setTimeout(() => {
    auth.refresh();
  }, 10000)

}
</script>