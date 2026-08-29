<template>
    <AuthenticatedLayout title="Agent Details">
        <!-- Back button -->
        <div class="mb-4">
            <a href="/owner/agents" class="text-teal-600 hover:underline">← Back to Agents</a>
        </div>

        <!-- Agent Info Card -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">{{ agent.name }}</h2>
                    <p class="text-gray-600"><strong>Email:</strong> {{ agent.email }}</p>
                    <p class="text-gray-600"><strong>Phone:</strong> {{ agent.phone || 'N/A' }}</p>
                    <p class="text-gray-600"><strong>Bank:</strong> {{ agent.bank_name || 'N/A' }}</p>
                    <p class="text-gray-600"><strong>Account:</strong> {{ agent.account_number || 'N/A' }}</p>
                    <p class="text-gray-600"><strong>Account Holder:</strong> {{ agent.account_holder_name || 'N/A' }}</p>
                    <p class="text-gray-600"><strong>Social Handles:</strong> 
                        <span v-if="agent.social_handles">
                            {{ JSON.stringify(agent.social_handles) }}
                        </span>
                        <span v-else>N/A</span>
                    </p>
                    <p class="text-gray-600"><strong>Total Leads:</strong> {{ leads.total || 0 }}</p>
                </div>
                <div>
                    <p class="text-gray-600"><strong>Created At:</strong> {{ new Date(agent.created_at).toLocaleDateString() }}</p>
                    <p class="text-gray-600"><strong>Updated At:</strong> {{ new Date(agent.updated_at).toLocaleDateString() }}</p>
                </div>
            </div>
        </div>

        <!-- Leads Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Leads</h3>
            </div>

            <div v-if="leads.data.length === 0" class="p-6 text-center text-gray-500">
                No leads found for this agent.
            </div>

            <div v-else>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium text-gray-600">Name</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-600">Phone</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-600">Email</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-600">Project</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-600">Source</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-600">Created</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-600">Expires</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-600">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="lead in leads.data" :key="lead.id" class="border-t hover:bg-gray-50">
                                <td class="px-4 py-3">{{ lead.name || 'N/A' }}</td>
                                <td class="px-4 py-3">{{ lead.decrypted_phone || 'N/A' }}</td>
                                <td class="px-4 py-3">{{ lead.decrypted_email || 'N/A' }}</td>
                                <td class="px-4 py-3">{{ lead.project?.name || 'N/A' }}</td>
                                <td class="px-4 py-3">
                                    <span class="capitalize">{{ lead.source || 'N/A' }}</span>
                                    <span v-if="lead.source_platform" class="text-xs text-gray-500 block">{{ lead.source_platform }}</span>
                                </td>
                                <td class="px-4 py-3">{{ new Date(lead.created_at).toLocaleDateString() }}</td>
                                <td class="px-4 py-3">{{ new Date(lead.expires_at).toLocaleDateString() }}</td>
                                <td class="px-4 py-3">
                                    <span :class="{
                                        'text-green-600': lead.expires_at > new Date().toISOString(),
                                        'text-red-600': lead.expires_at <= new Date().toISOString()
                                    }" class="font-medium">
                                        {{ lead.expires_at > new Date().toISOString() ? 'Active' : 'Expired' }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div>
                            <span class="text-sm text-gray-600">
                                Showing {{ leads.from }} to {{ leads.to }} of {{ leads.total }} results
                            </span>
                        </div>
                        <div class="flex gap-2">
                            <Link
                                v-for="link in leads.links"
                                :key="link.label"
                                :href="link.url || '#'"
                                class="px-3 py-1 rounded border"
                                :class="{
                                    'bg-teal-600 text-white': link.active,
                                    'bg-white text-gray-700 hover:bg-gray-100': !link.active,
                                    'opacity-50 cursor-not-allowed': !link.url
                                }"
                                v-html="link.label"
                                preserve-scroll
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthLayout.vue';

const props = defineProps({
    agent: Object,
    leads: Object,
});
</script>