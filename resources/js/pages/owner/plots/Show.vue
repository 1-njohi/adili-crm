<template>
    <AuthenticatedLayout :title="`Plot #${plot.plot_number} - ${project.name}`">
        <!-- Back link -->
        <div class="mb-4">
            <a :href="`/owner/projects/${project.id}`" class="text-teal-600 hover:underline text-sm md:text-base">
                ← Back to Project
            </a>
        </div>

        <!-- Plot Info Card -->
        <div class="bg-white rounded-lg shadow p-4 md:p-6 mb-4 md:mb-6 xs:min-w-[90vw] md:min-w-auto">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <div class="w-full sm:w-auto">
                    <h2 class="text-xl md:text-2xl font-bold text-gray-800">Plot #{{ plot.plot_number }}</h2>
                    <p class="text-sm md:text-base text-gray-600">Project: {{ project.name }}</p>
                    <p class="text-sm md:text-base text-gray-600">Size: {{ plot.size }} {{ plot.size_unit }}</p>
                    <p class="text-sm md:text-base text-gray-600">
                        Status: 
                        <span :class="statusColor(plot.status)" class="px-2 py-1 rounded-full text-xs">
                            {{ plot.status }}
                        </span>
                    </p>
                    <div v-if="plot.custom_attributes && Object.keys(plot.custom_attributes).length > 0" class="mt-2">
                        <p class="text-xs md:text-sm text-gray-500">Attributes:</p>
                        <ul class="text-xs md:text-sm text-gray-600">
                            <li v-for="(value, key) in plot.custom_attributes" :key="key">
                                <strong>{{ key }}:</strong> {{ value }}
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="flex gap-2 flex-wrap">
                    <a :href="`/owner/projects/${project.id}/plots/${plot.id}/edit`" class="px-3 py-1.5 bg-yellow-600 text-white rounded hover:bg-yellow-700 text-sm">
                        Edit
                    </a>
                    <a :href="`/owner/projects/${project.id}`" class="px-3 py-1.5 bg-gray-300 rounded hover:bg-gray-400 text-sm">
                        Back
                    </a>
                </div>
            </div>
        </div>

        <!-- If No Sale -->
        <div v-if="!financialSummary" class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-gray-500">This plot has not been sold yet.</p>
            <a :href="`/owner/projects/${project.id}/plots/${plot.id}/sell`" class="mt-4 inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Sell This Plot
            </a>
        </div>

        <!-- Financial Summary -->
        <div v-else>
            <!-- Stats Cards -->
            <div class="grid grid-cols-2 gap-3 md:gap-4 mb-4 md:mb-6">
                <div class="bg-white rounded-lg shadow p-3 md:p-4">
                    <p class="text-[10px] md:text-sm text-gray-500">Total</p>
                    <p class="text-sm md:text-2xl font-bold text-gray-800">{{ formatCurrency(financialSummary.total_price) }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-3 md:p-4">
                    <p class="text-[10px] md:text-sm text-gray-500">Paid</p>
                    <p class="text-sm md:text-2xl font-bold text-teal-600">{{ formatCurrency(financialSummary.total_paid) }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-3 md:p-4">
                    <p class="text-[10px] md:text-sm text-gray-500">Remaining</p>
                    <p class="text-sm md:text-2xl font-bold text-red-600">{{ formatCurrency(financialSummary.remaining_balance) }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-3 md:p-4">
                    <p class="text-[10px] md:text-sm text-gray-500">Progress</p>
                    <p class="text-sm md:text-2xl font-bold text-blue-600">{{ financialSummary.progress_percentage }}%</p>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="bg-white rounded-lg shadow p-3 md:p-4 mb-4 md:mb-6">
                <div class="flex justify-between text-xs md:text-sm text-gray-600 mb-1">
                    <span>Payment Progress</span>
                    <span>{{ financialSummary.progress_percentage }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 md:h-2.5">
                    <div class="bg-teal-600 h-2 md:h-2.5 rounded-full" :style="{ width: financialSummary.progress_percentage + '%' }"></div>
                </div>
            </div>

            <!-- Record Payment Button -->
            <div class="bg-white rounded-lg shadow p-3 md:p-4 mb-4 md:mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <div>
                    <h3 class="text-sm md:text-lg font-semibold text-gray-800">Record Payment</h3>
                    <p class="text-xs md:text-sm text-gray-500">Apply to oldest outstanding installments.</p>
                </div>
                <button @click="showPaymentModal = true" class="w-full sm:w-auto px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm md:text-base">
                    + Record Payment
                </button>
            </div>

            <!-- Buyer & Agent Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 mb-4 md:mb-6">
                <div class="bg-white rounded-lg shadow p-3 md:p-4">
                    <h3 class="text-sm md:text-lg font-semibold text-gray-800 mb-2">Buyer</h3>
                    <div v-if="financialSummary.buyer">
                        <p class="text-sm"><strong>Name:</strong> {{ financialSummary.buyer.name }}</p>
                        <p class="text-sm"><strong>Email:</strong> {{ financialSummary.buyer.email }}</p>
                        <p v-if="financialSummary.buyer.phone" class="text-sm"><strong>Phone:</strong> {{ financialSummary.buyer.phone }}</p>
                    </div>
                    <p v-else class="text-gray-500 text-sm">No buyer assigned</p>
                </div>
                <div class="bg-white rounded-lg shadow p-3 md:p-4">
                    <h3 class="text-sm md:text-lg font-semibold text-gray-800 mb-2">Agent</h3>
                    <div v-if="financialSummary.agent">
                        <p class="text-sm"><strong>Name:</strong> {{ financialSummary.agent.name }}</p>
                        <p class="text-sm"><strong>Email:</strong> {{ financialSummary.agent.email }}</p>
                        <p class="text-sm"><strong>Commission:</strong> 
                            {{ financialSummary.commission_rate }} 
                            {{ financialSummary.commission_type === 'percentage' ? '%' : 'Flat (KES)' }}
                        </p>
                    </div>
                    <p v-else class="text-gray-500 text-sm">No agent assigned</p>
                </div>
            </div>
            
            <div v-if="sortedInstallments.length > 0" class="mb-4">
                        <h4 class="font-semibold text-gray-700 text-sm mb-2">Installments</h4>
                        <div class="space-y-2">
                            <div  v-for="inst in sortedInstallments" :key="inst.id" class="flex items-center justify-between bg-gray-50 rounded px-3 py-2 text-sm">
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


                    <div v-if="sortedInstallments.length === 0" class="text-center text-gray-500 py-4">
                        No installments recorded.
                    </div>
            <!-- Payments - Card based -->
            <!-- <div class="bg-white rounded-lg shadow overflow-hidden mb-4 md:mb-6">
                <div class="px-4 py-3 md:px-6 md:py-4 border-b border-gray-200">
                    <h3 class="text-sm md:text-lg font-semibold text-gray-800">Payment History</h3>
                </div>
                <div class="p-3 md:p-4 space-y-3">
                    <div v-if="!financialSummary.payments || financialSummary.payments.length === 0" class="text-center text-gray-500 py-4">
                        No payments recorded.
                    </div>
                    <div v-for="payment in financialSummary.payments" :key="payment.id" class="bg-gray-50 rounded-lg p-3 md:p-4">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                            <div>
                                <span class="font-bold text-sm md:text-base">{{ formatCurrency(payment.amount) }}</span>
                                <span :class="payment.allocated_to === 'deposit' ? 'bg-teal-100 text-teal-700' : 'bg-blue-100 text-blue-700'" class="ml-2 px-2 py-0.5 rounded text-[10px]">
                                    {{ payment.allocated_to === 'deposit' ? 'Deposit' : 'Installment' }}
                                </span>
                            </div>
                            <div class="flex flex-wrap items-center gap-2 text-xs text-gray-500">
                                <span>{{ payment.method || 'N/A' }}</span>
                                <span>{{ formatDate(payment.created_at) }}</span>
                                <a v-if="payment.receipt_path" :href="`/storage/${payment.receipt_path}`" target="_blank" class="text-teal-600 hover:underline">
                                    View
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->


                    <!-- Payments -->
                    <div>
                        <h4 class="font-semibold text-gray-700 text-sm mb-2">Payments</h4>
                        <div v-if="!financialSummary.payments || !financialSummary.payments.length" class="text-gray-500 text-sm">
                            No payments recorded.
                        </div>
                        <div v-else class="space-y-2">
                            <div v-for="payment in financialSummary.payments" :key="payment.id" class="flex items-center justify-between bg-gray-50 rounded px-3 py-2 text-sm">
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

            <!-- Deposit Receipt -->
            <div class="bg-white rounded-lg shadow p-3 md:p-4">
                <h3 class="text-sm md:text-lg font-semibold text-gray-800 mb-2">Deposit Receipt</h3>
                <div v-if="financialSummary.deposit_receipt_path">
                    <a :href="`/storage/${financialSummary.deposit_receipt_path}`" target="_blank" class="text-teal-600 hover:underline text-sm md:text-base">
                        View Deposit Receipt
                    </a>
                </div>
                <p v-else class="text-gray-500 text-sm md:text-base">No receipt uploaded.</p>
            </div>
        </div>

        <!-- Record Payment Modal -->
        <Teleport to="body">
            <div v-if="showPaymentModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="showPaymentModal = false">
                <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-4 md:p-6">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-lg md:text-xl font-bold text-gray-800">Record Payment</h3>
                        <button @click="showPaymentModal = false" class="text-gray-500 hover:text-gray-700">
                            <X :size="24" />
                        </button>
                    </div>

                    <p class="text-sm text-gray-600 mb-4">
                        Payment will be applied to the oldest outstanding installments automatically.
                    </p>

                    <form @submit.prevent="submitPayment" enctype="multipart/form-data">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Amount (KES) *</label>
                            <input
                                v-model.number="paymentForm.amount"
                                type="number"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 md:px-4 md:py-2 focus:ring-2 focus:ring-teal-200 focus:border-teal-600"
                                required
                                min="0.01"
                                step="0.01"
                            />
                            <p v-if="paymentForm.errors.amount" class="text-red-500 text-sm mt-1">{{ paymentForm.errors.amount }}</p>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                            <select v-model="paymentForm.method" class="w-full border border-gray-300 rounded-lg px-3 py-2 md:px-4 md:py-2 focus:ring-2 focus:ring-teal-200 focus:border-teal-600">
                                <option value="">Select Method</option>
                                <option value="M-Pesa">M-Pesa</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Cash">Cash</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Reference</label>
                            <input
                                v-model="paymentForm.reference"
                                type="text"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 md:px-4 md:py-2 focus:ring-2 focus:ring-teal-200 focus:border-teal-600"
                                placeholder="M-Pesa code or bank ref"
                            />
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Receipt</label>
                            <input
                                type="file"
                                @input="paymentForm.receipt = $event.target.files[0]"
                                accept=".jpg,.jpeg,.png,.pdf"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 md:px-4 md:py-2"
                            />
                            <p class="text-xs text-gray-500 mt-1">Accepted: JPG, PNG, PDF (max 5MB)</p>
                            <p v-if="paymentForm.errors.receipt" class="text-red-500 text-sm mt-1">{{ paymentForm.errors.receipt }}</p>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                            <textarea
                                v-model="paymentForm.notes"
                                rows="2"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 md:px-4 md:py-2 focus:ring-2 focus:ring-teal-200 focus:border-teal-600"
                                placeholder="Optional notes"
                            ></textarea>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-2">
                            <button
                                type="submit"
                                class="w-full sm:flex-1 bg-blue-600 text-white font-bold px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm md:text-base"
                                :disabled="paymentForm.processing"
                            >
                                {{ paymentForm.processing ? 'Recording...' : 'Record Payment' }}
                            </button>
                            <button
                                type="button"
                                @click="showPaymentModal = false"
                                class="w-full sm:flex-1 px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition text-sm md:text-base"
                            >
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Teleport>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { X } from 'lucide-vue-next';
import AuthenticatedLayout from '@/layouts/AuthLayout.vue';

const props = defineProps({
    project: Object,
    plot: Object,
    financialSummary: Object,
});

const showPaymentModal = ref(false);

const paymentForm = useForm({
    amount: '',
    method: '',
    reference: '',
    receipt: null,
    notes: '',
});

const submitPayment = () => {
    paymentForm.post(`/owner/sales/${props.financialSummary.sale_id}/payments`, {
        forceFormData: true,
        onSuccess: () => {
            showPaymentModal.value = false;
            paymentForm.reset();
            window.location.reload();
        },
    });
};

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

function statusColor(status) {
    const colors = {
        available: 'bg-green-200 text-green-700',
        reserved: 'bg-yellow-200 text-yellow-700',
        sold: 'bg-blue-200 text-blue-700',
    };
    return colors[status] || 'bg-gray-200 text-gray-700';
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

const sortedInstallments = computed(() => {
    if (!props.financialSummary?.installments) return [];
    return [...props.financialSummary.installments].sort((a, b) => a.number - b.number);
});
</script>