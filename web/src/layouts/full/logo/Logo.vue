<script setup lang="ts">
import { ref } from 'vue';
import { RouterLink } from 'vue-router';
import logo from "@/assets/images/logo/main.png";

</script>
<template>
  
    <div class="logo">
        <RouterLink to="/">

            <img src='/src/assets/images/logo/main.png' class="logoClass"/>
            
        </RouterLink>
    </div>
  
</template>
