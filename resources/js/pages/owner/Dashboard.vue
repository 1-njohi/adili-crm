<template>
    <AuthenticatedLayout title="Dashboard">
        <!-- Metrics Grid -->
        <div class="grid grid-cols-2 gap-3 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 mb-6">
            <div class="bg-white rounded-lg shadow p-3 md:p-4">
                <p class="text-[10px] md:text-xs text-gray-500">Revenue</p>
                <p class="text-sm md:text-lg font-bold text-gray-800">{{ formatCurrency(metrics.total_revenue) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-3 md:p-4">
                <p class="text-[10px] md:text-xs text-gray-500">Expenses</p>
                <p class="text-sm md:text-lg font-bold text-red-600">{{ formatCurrency(metrics.total_expenses) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-3 md:p-4">
                <p class="text-[10px] md:text-xs text-gray-500">Commission Paid</p>
                <p class="text-sm md:text-lg font-bold text-purple-600">{{ formatCurrency(metrics.total_commission_paid) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-3 md:p-4">
                <p class="text-[10px] md:text-xs text-gray-500">Net Profit</p>
                <p class="text-sm md:text-lg font-bold" :class="metrics.net_profit >= 0 ? 'text-green-600' : 'text-red-600'">
                    {{ formatCurrency(metrics.net_profit) }}
                </p>
            </div>
            <div class="bg-white rounded-lg shadow p-3 md:p-4">
                <p class="text-[10px] md:text-xs text-gray-500">Projects</p>
                <p class="text-sm md:text-lg font-bold text-gray-800">{{ metrics.active_projects }} / {{ metrics.total_projects }}</p>
                <p class="text-[8px] md:text-[10px] text-gray-400">{{ metrics.sold_out_projects }} sold out</p>
            </div>
            <div class="bg-white rounded-lg shadow p-3 md:p-4">
                <p class="text-[10px] md:text-xs text-gray-500">Leads</p>
                <p class="text-sm md:text-lg font-bold text-gray-800">{{ metrics.total_leads }}</p>
                <p class="text-[8px] md:text-[10px] text-gray-400">{{ metrics.active_leads }} active</p>
            </div>
        </div>

        <!-- Plots Summary & Upcoming -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 mb-6">
            <!-- Plots Summary -->
            <div class="bg-white rounded-lg shadow p-4 md:p-6">
                <h3 class="text-sm md:text-base font-semibold text-gray-800 mb-3">Plots Summary</h3>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <p class="text-xs text-gray-500">Total</p>
                        <p class="text-lg font-bold text-gray-800">{{ metrics.total_plots }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Sold</p>
                        <p class="text-lg font-bold text-green-600">{{ metrics.sold_plots }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Available</p>
                        <p class="text-lg font-bold text-blue-600">{{ metrics.available_plots }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Reserved</p>
                        <p class="text-lg font-bold text-yellow-600">{{ metrics.reserved_plots }}</p>
                    </div>
                </div>
            </div>

            <!-- Cash Flow Forecaster -->
            <div class="bg-white rounded-lg shadow p-4 md:p-6">
                <h3 class="text-sm md:text-base font-semibold text-gray-800 mb-3">Upcoming Installments</h3>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-xs text-gray-500">Due in next 30 days</span>
                    <span class="text-lg font-bold text-teal-600">{{ formatCurrency(metrics.upcoming_installments_amount) }}</span>
                </div>
                <div class="text-xs text-gray-500">
                    {{ metrics.upcoming_installments_count }} installments pending
                </div>
                <div v-if="upcomingInstallments.length > 0" class="mt-3 max-h-40 overflow-y-auto space-y-1">
                    <div v-for="inst in upcomingInstallments.slice(0, 5)" :key="inst.id" class="flex justify-between text-xs border-b border-gray-100 py-1">
                        <span>{{ inst.sale.buyer?.name ?? 'Buyer' }}</span>
                        <span class="font-medium">{{ formatCurrency(inst.amount) }}</span>
                        <span class="text-gray-400">{{ formatDate(inst.due_date) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Project Summary -->
        <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
            <div class="px-4 py-3 md:px-6 md:py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-sm md:text-base font-semibold text-gray-800">Projects</h3>
                <a href="/owner/projects" class="text-teal-600 hover:underline text-xs md:text-sm">View All →</a>
            </div>
            <div class="p-3 md:p-4 space-y-3">
                <div v-for="project in projects" :key="project.id" class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 border-b border-gray-100 pb-2 last:border-0">
                    <div>
                        <p class="font-medium text-gray-800">{{ project.name }}</p>
                        <p class="text-xs text-gray-500">{{ project.sold_plots_count }}/{{ project.plots_count }} sold</p>
                    </div>
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <div class="w-24 bg-gray-200 rounded-full h-2">
                            <div class="bg-teal-600 h-2 rounded-full" :style="{ width: project.progress + '%' }"></div>
                        </div>
                        <span class="text-xs font-medium text-gray-600">{{ project.progress }}%</span>
                        <span :class="{
                            'text-green-600': project.status === 'active',
                            'text-gray-400': project.status === 'draft',
                            'text-blue-600': project.status === 'sold_out'
                        }" class="text-xs font-medium capitalize">{{ project.status }}</span>
                    </div>
                </div>
                <div v-if="projects.length === 0" class="text-center text-gray-500 py-4">
                    No projects yet.
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
            <!-- Recent Payments -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-sm font-semibold text-gray-800">Recent Payments</h3>
                    <span class="text-xs text-gray-400">Last 10</span>
                </div>
                <div class="p-3 space-y-2 max-h-60 overflow-y-auto">
                    <div v-for="payment in recentPayments" :key="payment.id" class="flex justify-between items-center border-b border-gray-100 pb-2 last:border-0">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ formatCurrency(payment.amount) }}</p>
                            <p class="text-xs text-gray-500">{{ payment.sale?.buyer?.name ?? 'N/A' }} · {{ payment.sale?.plot?.plot_number ?? 'N/A' }}</p>
                        </div>
                        <span class="text-xs text-gray-400">{{ formatDate(payment.created_at) }}</span>
                    </div>
                    <div v-if="recentPayments.length === 0" class="text-center text-gray-500 py-4">
                        No payments recorded.
                    </div>
                </div>
            </div>

            <!-- Recent Leads -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-sm font-semibold text-gray-800">Recent Leads</h3>
                    <span class="text-xs text-gray-400">Last 10</span>
                </div>
                <div class="p-3 space-y-2 max-h-60 overflow-y-auto">
                    <div v-for="lead in recentLeads" :key="lead.id" class="flex justify-between items-center border-b border-gray-100 pb-2 last:border-0">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ lead.name || 'Anonymous' }}</p>
                            <p class="text-xs text-gray-500">{{ lead.agent?.name ?? 'N/A' }} · {{ lead.project?.name ?? 'N/A' }}</p>
                        </div>
                        <span class="text-xs text-gray-400">{{ formatDate(lead.created_at) }}</span>
                    </div>
                    <div v-if="recentLeads.length === 0" class="text-center text-gray-500 py-4">
                        No leads captured.
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/layouts/AuthLayout.vue';

const props = defineProps({
    metrics: Object,
    projects: Array,
    recentPayments: Array,
    recentLeads: Array,
    upcomingInstallments: Array,
});

function formatCurrency(amount) {
    return new Intl.NumberFormat('en-KE', {
        style: 'currency',
        currency: 'KES',
        minimumFractionDigits: 0,
    }).format(amount || 0);
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-KE', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
}
</script>