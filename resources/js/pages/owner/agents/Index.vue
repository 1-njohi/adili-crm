<template>
    <AuthenticatedLayout title="Agents">
        <div class="mb-6 flex items-center justify-between">
            <h2 class="text-xl font-semibold">All Agents</h2>
            <a
                href="/owner/agents/create"
                class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
            >
                + Add Agent
            </a>
        </div>

        <div
            v-if="agents.length === 0"
            class="rounded-lg bg-white p-8 text-center shadow"
        >
            <p class="text-gray-500">
                No agents yet. Click "Add Agent" to get started.
            </p>
        </div>

        <div v-else class="overflow-x-auto rounded-lg bg-white shadow">
            <table class="w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium">Name</th>
                        <th class="px-4 py-3 text-left font-medium">Email</th>
                        <th class="px-4 py-3 text-left font-medium">Phone</th>
                        <th class="px-4 py-3 text-left font-medium">Bank</th>
                        <th class="px-4 py-3 text-left font-medium">Leads</th>
                        <!-- ✅ New Column -->
                        <th class="px-4 py-3 text-left font-medium">Created</th>
                        <th class="px-4 py-3 text-left font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="agent in agents"
                        :key="agent.id"
                        class="border-t hover:bg-gray-50"
                    >
                        <td class="px-4 py-3 font-medium">{{ agent.name }}</td>
                        <td class="px-4 py-3">{{ agent.email }}</td>
                        <td class="px-4 py-3">{{ agent.phone || '-' }}</td>
                        <td class="px-4 py-3">{{ agent.bank_name || '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="font-semibold text-teal-600">{{
                                agent.unique_leads_count ?? 0
                            }}</span>
                        </td>
                        <td class="px-4 py-3">
                            {{
                                new Date(agent.created_at).toLocaleDateString()
                            }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex gap-2">
                                <a
                                    :href="`/owner/agents/${agent.id}`"
                                    class="text-blue-600 hover:underline"
                                    >View</a
                                >
                                <a
                                    :href="`/owner/agents/${agent.id}/edit`"
                                    class="text-yellow-600 hover:underline"
                                    >Edit</a
                                >
                                <button
                                    @click="deleteAgent(agent)"
                                    class="text-red-600 hover:underline"
                                >
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthLayout.vue';

const props = defineProps({
    agents: Array,
});

const deleteAgent = (agent) => {
    if (confirm(`Are you sure you want to delete "${agent.name}"?`)) {
        router.delete(`/owner/agents/${agent.id}`);
    }
};
</script>
