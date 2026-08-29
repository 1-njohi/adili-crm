<template>
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <!-- <aside class="w-64 bg-white shadow-md">
            <div class="p-4 text-lg font-bold border-b">Adili CRM</div>
            <nav class="mt-4">
                
                <a href="#" class="block px-4 py-2 hover:bg-gray-200">Agents</a>
                <a href="#" class="block px-4 py-2 hover:bg-gray-200">Site Visits</a>
                <a href="#" class="block px-4 py-2 hover:bg-gray-200">Reconciliation</a>
                <a href="#" class="block px-4 py-2 hover:bg-gray-200">Reports</a>
            </nav>
        </aside> -->

        <!-- Main Content -->
        <main class="flex-1 p-6 overflow-y-auto">
            <div class="mb-4">
                <h1 class="text-2xl font-bold">{{ title }}</h1>
            </div>
            <slot />
        </main>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    title: {
        type: String,
        default: 'Dashboard',
    },
});
</script>