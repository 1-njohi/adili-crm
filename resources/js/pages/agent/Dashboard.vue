<template>
    <AuthenticatedLayout title="Agent Dashboard">
        <!-- Stats Cards -->
        <div class="mb-6 grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-5">
            <div class="rounded-lg bg-white p-4 shadow">
                <p class="text-sm text-gray-500">Total Leads</p>
                <p class="text-2xl font-bold text-gray-800">
                    {{ stats.total_leads }}
                </p>
            </div>
            <div class="rounded-lg bg-white p-4 shadow">
                <p class="text-sm text-gray-500">Active Leads</p>
                <p class="text-2xl font-bold text-green-600">
                    {{ stats.active_leads }}
                </p>
            </div>
            <div class="hidden rounded-lg bg-white p-4 shadow md:block">
                <p class="text-sm text-gray-500">Expired</p>
                <p class="text-2xl font-bold text-red-600">
                    {{ stats.expired_leads }}
                </p>
            </div>
            <div class="rounded-lg bg-white p-4 shadow">
                <p class="text-sm text-gray-500">Site Visits</p>
                <p class="text-2xl font-bold text-blue-600">
                    {{ stats.total_visits }}
                </p>
            </div>
            <div class="rounded-lg bg-white p-4 shadow">
                <p class="text-sm text-gray-500">Commission Earned</p>
                <p class="text-2xl font-bold text-teal-600">
                    {{ formatCurrency(stats.total_commission) }}
                </p>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mb-6 rounded-lg bg-white p-4 shadow">
            <h3 class="mb-4 text-lg font-semibold text-gray-800">
                Quick Actions
            </h3>
            <div class="flex flex-wrap gap-3">
                <a
                    href="/agent/leads"
                    class="rounded-lg bg-teal-600 px-4 py-2 text-white hover:bg-teal-700"
                >
                    View My Leads
                </a>
                <a
                    href="/agent/site-visits/create"
                    class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"
                >
                    Book Site Visit
                </a>
                <a
                    href="/agent/site-visits"
                    class="rounded-lg bg-gray-600 px-4 py-2 text-white hover:bg-gray-700"
                >
                    My Site Visits
                </a>
                <a
                    href="/agent/payouts"
                    class="rounded-lg bg-purple-600 px-4 py-2 text-white hover:bg-purple-700"
                >
                    My Payouts
                </a>
            </div>
        </div>

        <!-- Add a source breakdown card -->
        <div class="my-6 rounded-lg bg-white p-4 shadow">
            <h3 class="mb-4 text-lg font-semibold text-gray-800">
                Lead Sources
            </h3>
            <div v-if="sourceStats.length === 0" class="text-gray-500">
                No source data yet.
            </div>
            <div v-else class="grid grid-cols-2 gap-4 md:grid-cols-4">
                <div
                    v-for="stat in sourceStats"
                    :key="stat.source_platform"
                    class="rounded border p-3 text-center"
                >
                    <p class="text-sm text-gray-500">
                        {{ stat.source_platform || 'Unknown' }}
                    </p>
                    <p class="text-xl font-bold text-teal-600">
                        {{ stat.total }}
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Recent Leads -->
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div
                    class="flex items-center justify-between border-b border-gray-200 px-6 py-4"
                >
                    <h3 class="text-lg font-semibold text-gray-800">
                        Recent Leads
                    </h3>
                    <a
                        href="/agent/leads"
                        class="text-sm text-teal-600 hover:underline"
                        >View All →</a
                    >
                </div>
                <div
                    v-if="recentLeads.length === 0"
                    class="p-6 text-center text-gray-500"
                >
                    No leads yet.
                </div>
                <div v-else>
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-gray-600">
                                    Name
                                </th>
                                <th class="px-4 py-2 text-left text-gray-600">
                                    Project
                                </th>
                                <th class="px-4 py-2 text-left text-gray-600">
                                    Source
                                </th>
                                <th class="px-4 py-2 text-left text-gray-600">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="lead in recentLeads"
                                :key="lead.id"
                                class="border-t hover:bg-gray-50"
                            >
                                <td class="px-4 py-2">
                                    {{ lead.name || 'N/A' }}
                                </td>
                                <td class="px-4 py-2">
                                    {{ lead.project?.name || 'N/A' }}
                                </td>
                                <td class="px-4 py-2">
                                    <span class="capitalize">{{
                                        lead.source
                                    }}</span>
                                    <span
                                        v-if="lead.source_platform"
                                        class="block text-xs text-gray-500"
                                        >{{ lead.source_platform }}</span
                                    >
                                </td>
                                <td class="px-4 py-2">
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
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Upcoming Site Visits -->
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div
                    class="flex items-center justify-between border-b border-gray-200 px-6 py-4"
                >
                    <h3 class="text-lg font-semibold text-gray-800">
                        Upcoming Site Visits
                    </h3>
                    <a
                        href="/agent/site-visits"
                        class="text-sm text-teal-600 hover:underline"
                        >View All →</a
                    >
                </div>
                <div
                    v-if="upcomingVisits.length === 0"
                    class="p-6 text-center text-gray-500"
                >
                    No upcoming site visits.
                </div>
                <div v-else>
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-gray-600">
                                    Project
                                </th>
                                <th class="px-4 py-2 text-left text-gray-600">
                                    Date
                                </th>
                                <th class="px-4 py-2 text-left text-gray-600">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="visit in upcomingVisits"
                                :key="visit.id"
                                class="border-t hover:bg-gray-50"
                            >
                                <td class="px-4 py-2">
                                    {{ visit.project?.name || 'N/A' }}
                                </td>
                                <td class="px-4 py-2">
                                    {{
                                        new Date(
                                            visit.scheduled_at,
                                        ).toLocaleString()
                                    }}
                                </td>
                                <td class="px-4 py-2">
                                    <span
                                        :class="{
                                            'text-yellow-600':
                                                visit.status ===
                                                'pending_owner_confirmation',
                                            'text-green-600':
                                                visit.status === 'confirmed',
                                            'text-gray-600':
                                                visit.status === 'completed',
                                        }"
                                        class="font-medium capitalize"
                                    >
                                        {{ visit.status.replace('_', ' ') }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/layouts/AuthLayout.vue';

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({}),
    },
    recentLeads: {
        type: Array,
        default: () => [],
    },
    upcomingVisits: {
        type: Array,
        default: () => [],
    },
    sourceStats: {
        type: Array,
        default: () => [],
    },
});

function formatCurrency(amount) {
    return new Intl.NumberFormat('en-KE', {
        style: 'currency',
        currency: 'KES',
        minimumFractionDigits: 0,
    }).format(amount || 0);
}
</script>
