<template>
    <AuthenticatedLayout title="Projects">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold">All Projects</h2>
            <a href="/owner/projects/create" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                + Add Project
            </a>
        </div>

        <div v-if="projects.length === 0" class="bg-white rounded-lg shadow p-8 text-center">
            <p class="text-gray-500">No projects yet. Click "Add Project" to get started.</p>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="project in projects" :key="project.id" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
                <h3 class="text-lg font-semibold">{{ project.name }}</h3>
                <p class="text-gray-600 text-sm">{{ project.location || 'Location not set' }}</p>
                <div class="mt-4 flex items-center gap-4 text-sm">
                    <span class="text-gray-600">Plots: {{ project.plots_count }}</span>
                    <span class="text-gray-600">Expenses: {{ project.expenses_count }}</span>
                    <span :class="statusColor(project.status)" class="px-2 py-1 rounded-full text-xs">
                        {{ project.status }}
                    </span>
                </div>
                <div class="mt-4 flex gap-2">
                    <a :href="`/owner/projects/${project.id}`" class="text-blue-600 hover:underline text-sm">View</a>
                    <a :href="`/owner/projects/${project.id}/edit`" class="text-yellow-600 hover:underline text-sm">Edit</a>
                    <button @click="deleteProject(project)" class="text-red-600 hover:underline text-sm">Delete</button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthLayout.vue';;

const props = defineProps({
    projects: Array,
});

const statusColor = (status) => {
    const colors = {
        draft: 'bg-gray-200 text-gray-700',
        active: 'bg-green-200 text-green-700',
        sold_out: 'bg-blue-200 text-blue-700',
    };
    return colors[status] || 'bg-gray-200 text-gray-700';
};

const deleteProject = (project) => {
    if (confirm(`Are you sure you want to delete "${project.name}"?`)) {
        router.delete(`/owner/projects/${project.id}`);
    }
};
</script>