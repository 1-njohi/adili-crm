<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { home } from '@/routes';
import NavFooter from '@/components/NavFooter.vue';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="flex justify-center">
        <div>
            <slot></slot>
        </div>
        <!-- <NavFooter /> -->
    </div>
</template>
