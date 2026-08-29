<template>
    <AuthenticatedLayout title="My Leads">
        <!-- Header -->
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <h2 class="text-xl font-semibold text-gray-800">My Leads</h2>
            <div class="flex flex-wrap items-center gap-2">
                <!-- Search -->
                <input
                    v-model="search"
                    type="text"
                    placeholder="Search leads..."
                    class="rounded-lg border border-gray-300 px-4 py-2 focus:border-teal-600 focus:ring-2 focus:ring-teal-200 focus:outline-none"
                    @input="searchLeads"
                />
                <!-- Filter -->
                <select
                    v-model="filter"
                    class="rounded-lg border border-gray-300 px-4 py-2 focus:border-teal-600 focus:ring-2 focus:ring-teal-200 focus:outline-none"
                    @change="applyFilter"
                >
                    <option value="all">All Leads</option>
                    <option value="active">Active</option>
                    <option value="expired">Expired</option>
                </select>
            </div>
        </div>

        <!-- Leads Table -->
        <div class="overflow-hidden rounded-lg bg-white shadow">
            <div
                v-if="leads.data.length === 0"
                class="p-6 text-center text-gray-500"
            >
                No leads found.
            </div>
            <div v-else>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left font-medium text-gray-600"
                                >
                                    Name
                                </th>
                                <th
                                    class="px-4 py-3 text-left font-medium text-gray-600"
                                >
                                    Phone
                                </th>
                                <th
                                    class="px-4 py-3 text-left font-medium text-gray-600"
                                >
                                    Project
                                </th>
                                <th
                                    class="px-4 py-3 text-left font-medium text-gray-600"
                                >
                                    Source
                                </th>
                                <th
                                    class="px-4 py-3 text-left font-medium text-gray-600"
                                >
                                    Created
                                </th>
                                <th
                                    class="px-4 py-3 text-left font-medium text-gray-600"
                                >
                                    Expires
                                </th>
                                <th
                                    class="px-4 py-3 text-left font-medium text-gray-600"
                                >
                                    Status
                                </th>
                                <th
                                    class="px-4 py-3 text-left font-medium text-gray-600"
                                >
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="lead in leads.data"
                                :key="lead.id"
                                class="border-t hover:bg-gray-50"
                            >
                                <td class="px-4 py-3">
                                    {{ lead.name || 'N/A' }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ lead.phone_encrypted || 'N/A' }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ lead.project?.name || 'N/A' }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="capitalize">{{
                                        lead.source_platform || 'direct'
                                    }}</span>
                                    <span
                                        v-if="lead.source_platform"
                                        class="block text-xs text-gray-500"
                                        >{{ lead.source_platform }}</span
                                    >
                                </td>
                                <td class="px-4 py-3">
                                    {{
                                        new Date(
                                            lead.created_at,
                                        ).toLocaleDateString()
                                    }}
                                </td>
                                <td class="px-4 py-3">
                                    {{
                                        new Date(
                                            lead.expires_at,
                                        ).toLocaleDateString()
                                    }}
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        :class="{
                                            'text-green-600':
                                                lead.expires_at >
                                                new Date().toISOString(),
                                            'text-red-600':
                                                lead.expires_at <=
                                                new Date().toISOString(),
                                        }"
                                        class="font-medium"
                                    >
                                        {{
                                            lead.expires_at >
                                            new Date().toISOString()
                                                ? 'Active'
                                                : 'Expired'
                                        }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <a
                                        :href="`/agent/leads/${lead.id}`"
                                        class="text-teal-600 hover:underline"
                                        >View</a
                                    >
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="border-t border-gray-200 px-6 py-4">
                    <div
                        class="flex flex-wrap items-center justify-between gap-2"
                    >
                        <div>
                            <span class="text-sm text-gray-600">
                                Showing {{ leads.from }} to {{ leads.to }} of
                                {{ leads.total }} results
                            </span>
                        </div>
                        <div class="flex gap-2">
                            <Link
                                v-for="link in leads.links"
                                :key="link.label"
                                :href="link.url || '#'"
                                class="rounded border px-3 py-1"
                                :class="{
                                    'bg-teal-600 text-white': link.active,
                                    'bg-white text-gray-700 hover:bg-gray-100':
                                        !link.active,
                                    'cursor-not-allowed opacity-50': !link.url,
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
import { Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AuthenticatedLayout from '@/layouts/AuthLayout.vue';

const props = defineProps({
    leads: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');
const filter = ref(props.filters.filter || 'all');

const searchLeads = () => {
    router.get(
        '/agent/leads',
        {
            search: search.value,
            filter: filter.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
};

const applyFilter = () => {
    searchLeads();
};
</script>
