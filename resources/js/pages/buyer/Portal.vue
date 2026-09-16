<template>
    <AuthenticatedLayout title="My Properties">
        <!-- No Sales State -->
        <div v-if="!hasSales" class="bg-white rounded-lg shadow p-6 text-center">
            <h2 class="text-xl font-semibold text-gray-700 mb-2">No Properties Yet</h2>
            <p class="text-gray-500">{{ message }}</p>
        </div>

        <!-- Sales List -->
        <div v-else class="min-w-[50vw]">
            <h2 class="text-xl font-bold text-gray-800 mb-4">My Properties</h2>

            <!-- Property Cards -->
            <div v-for="item in salesData" :key="item.sale.id" class="bg-white rounded-lg shadow mb-4 overflow-hidden">
                <!-- Header -->
                <div class="p-4 bg-gray-50 cursor-pointer hover:bg-gray-100 transition flex justify-between items-center w-[90vw]" @click="toggleSale(item.sale.id)">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-base font-bold text-gray-800 truncate">{{ item.project.name }}</h3>
                        <p class="text-sm text-gray-600">Plot #{{ item.plot.plot_number }}</p>
                    </div>
                    <div class="flex items-center gap-2 ml-2">
                        <span :class="{
                            'bg-green-100 text-green-700': item.sale.status === 'completed',
                            'bg-yellow-100 text-yellow-700': item.sale.status === 'active',
                            'bg-red-100 text-red-700': item.sale.status === 'defaulted',
                        }" class="px-2 py-0.5 rounded text-xs font-medium">
                            {{ item.sale.status }}
                        </span>
                        <svg class="w-5 h-5 text-gray-400 transition-transform flex-shrink-0" :class="{ 'rotate-180': expandedSale === item.sale.id }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </div>

                <!-- Expanded Details -->
                <div v-show="expandedSale === item.sale.id" class="p-4 border-t border-gray-100">
                    <!-- Stats -->
                    <div class="grid grid-cols-2 gap-2 mb-4">
                        <div class="bg-gray-50 rounded p-3 text-center">
                            <p class="text-xs text-gray-500">Total</p>
                            <p class="text-sm font-bold text-gray-800">{{ formatCurrency(item.sale.total_price) }}</p>
                        </div>
                        <div class="bg-gray-50 rounded p-3 text-center">
                            <p class="text-xs text-gray-500">Paid</p>
                            <p class="text-sm font-bold text-teal-600">{{ formatCurrency(item.total_paid) }}</p>
                        </div>
                        <div class="bg-gray-50 rounded p-3 text-center">
                            <p class="text-xs text-gray-500">Remaining</p>
                            <p class="text-sm font-bold text-red-600">{{ formatCurrency(item.remaining_balance) }}</p>
                        </div>
                        <div class="bg-gray-50 rounded p-3 text-center">
                            <p class="text-xs text-gray-500">Next Due</p>
                            <p v-if="item.nextInstallment" class="text-sm font-bold text-yellow-600">
                                {{ formatDate(item.nextInstallment.due_date) }}
                            </p>
                            <p v-else class="text-sm font-bold text-green-600">✓ All Paid</p>
                        </div>
                    </div>

                    <!-- Progress -->
                    <div class="mb-4">
                        <div class="flex justify-between text-xs text-gray-600 mb-1">
                            <span>Progress</span>
                            <span>{{ item.progress }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-teal-600 h-2 rounded-full" :style="{ width: item.progress + '%' }"></div>
                        </div>
                    </div>

                    <!-- Installments -->
                    <div v-if="item.installments && item.installments.length" class="mb-4">
                        <h4 class="font-semibold text-gray-700 text-sm mb-2">Installments</h4>
                        <div class="space-y-2">
                            <div v-for="inst in item.installments" :key="inst.id" class="flex items-center justify-between bg-gray-50 rounded px-3 py-2 text-sm">
                                <div class="flex items-center gap-3">
                                    <span class="font-bold text-gray-700">#{{ inst.number }}</span>
                                    <span :class="statusClass(inst.status)" class="text-xs font-medium capitalize">
                                        {{ inst.status }}
                                    </span>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold">{{ formatCurrency(inst.amount) }}</p>
                                    <p v-if="inst.paid_amount > 0" class="text-xs text-gray-500">Paid: {{ formatCurrency(inst.paid_amount) }}</p>
                                    <p v-else class="text-xs text-gray-400">Due {{ formatDate(inst.due_date) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payments -->
                    <div>
                        <h4 class="font-semibold text-gray-700 text-sm mb-2">Payments</h4>
                        <div v-if="!item.payments || !item.payments.length" class="text-gray-500 text-sm">
                            No payments recorded.
                        </div>
                        <div v-else class="space-y-2">
                            <div v-for="payment in item.payments" :key="payment.id" class="flex items-center justify-between bg-gray-50 rounded px-3 py-2 text-sm">
                                <div>
                                    <span class="font-bold">{{ formatCurrency(payment.amount) }}</span>
                                    <span :class="payment.allocated_to === 'deposit' ? 'bg-teal-100 text-teal-700' : 'bg-blue-100 text-blue-700'" class="ml-2 px-1.5 py-0.5 rounded text-[10px]">
                                        {{ payment.allocated_to === 'deposit' ? 'Deposit' : 'Installment' }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-gray-500">{{ payment.method || 'N/A' }}</span>
                                    <span class="text-xs text-gray-400">{{ formatDate(payment.created_at) }}</span>
                                    <a v-if="payment.receipt_path" :href="`/storage/${payment.receipt_path}`" target="_blank" class="text-teal-600 hover:underline text-xs">
                                        View
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/layouts/AuthLayout.vue';

const props = defineProps({
    hasSales: Boolean,
    message: String,
    salesData: Array,
});

const expandedSale = ref(null);

function toggleSale(saleId) {
    expandedSale.value = expandedSale.value === saleId ? null : saleId;
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('en-KE', {
        style: 'currency',
        currency: 'KES',
        minimumFractionDigits: 0,
    }).format(amount || 0);
}

// ✅ Human-readable date formatter
function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-KE', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
}

function statusClass(status) {
    const classes = {
        paid: 'text-green-600',
        pending: 'text-yellow-600',
        partial: 'text-orange-600',
        overdue: 'text-red-600',
    };
    return classes[status] || 'text-gray-600';
}
</script>