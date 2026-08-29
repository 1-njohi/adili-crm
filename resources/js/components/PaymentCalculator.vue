<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue';
import { Calculator, CheckCircle, AlertCircle, Info } from 'lucide-vue-next';

const emit = defineEmits(['bookVisit']);

const props = defineProps<{
    pricingTiers?: any[];
    minDeposit?: number;
    maxMonths?: number;
    projectName?: string;
}>();

// ===== Defaults =====
const defaultTiers = [
    { months: 3, price: 1500000, label: '0 - 3 months' },
    { months: 6, price: 1550000, label: '4 - 6 months' },
    { months: 9, price: 1600000, label: '7 - 9 months' },
];

// ===== Reactive Props =====
const tiers = computed(() => props.pricingTiers?.length ? props.pricingTiers : defaultTiers);
const minDeposit = computed(() => props.minDeposit ?? 300000);
const maxMonths = computed(() => props.maxMonths ?? 9);
const projectName = computed(() => props.projectName ?? 'this project');

// ===== State =====
const deposit = ref<number>(minDeposit.value);
const monthlyPayment = ref<number | null>(null);
const numberOfMonths = ref<number | null>(null);
const calculationMode = ref<'months' | 'payment'>('payment');

const errors = ref({
    deposit: '',
    monthlyPayment: '',
    numberOfMonths: '',
});

// ===== Helpers =====
const getPriceForMonths = (months: number): number => {
    const tier = tiers.value.find(t => months <= t.months);
    return tier ? tier.price : tiers.value[tiers.value.length - 1].price;
};

const getTierLabel = (months: number): string => {
    const tier = tiers.value.find(t => months <= t.months);
    return tier ? tier.label : tiers.value[tiers.value.length - 1].label;
};

const getTierDetails = (months: number) => {
    const tier = tiers.value.find(t => months <= t.months);
    return tier || tiers.value[tiers.value.length - 1];
};

// ===== Computed =====
const totalPrice = computed(() => {
    if (calculationMode.value === 'payment' && numberOfMonths.value) {
        return getPriceForMonths(numberOfMonths.value);
    }
    if (calculationMode.value === 'months' && monthlyPayment.value && remainingAmount.value > 0) {
        const estimatedMonths = Math.ceil(remainingAmount.value / monthlyPayment.value);
        const clampedMonths = Math.min(Math.max(estimatedMonths, 1), maxMonths.value);
        return getPriceForMonths(clampedMonths);
    }
    return tiers.value[0].price;
});

const remainingAmount = computed(() => {
    const remaining = totalPrice.value - deposit.value;
    return Math.max(0, remaining);
});

const selectedTier = computed(() => {
    if (calculationMode.value === 'payment' && numberOfMonths.value) {
        return getTierDetails(numberOfMonths.value);
    }
    if (calculationMode.value === 'months' && monthlyPayment.value && remainingAmount.value > 0) {
        const estimatedMonths = Math.ceil(remainingAmount.value / monthlyPayment.value);
        const clampedMonths = Math.min(Math.max(estimatedMonths, 1), maxMonths.value);
        return getTierDetails(clampedMonths);
    }
    return tiers.value[0];
});

const calculatedMonthlyPayment = computed(() => {
    if (calculationMode.value === 'payment' && numberOfMonths.value && numberOfMonths.value > 0) {
        const price = getPriceForMonths(numberOfMonths.value);
        const remaining = price - deposit.value;
        return Math.round(remaining / numberOfMonths.value);
    }
    return null;
});

const calculatedMonths = computed(() => {
    if (calculationMode.value === 'months' && monthlyPayment.value && monthlyPayment.value > 0) {
        const price = getPriceForMonths(maxMonths.value);
        const remaining = price - deposit.value;
        const months = Math.ceil(remaining / monthlyPayment.value);
        return Math.min(Math.max(months, 1), maxMonths.value);
    }
    return null;
});

const isPaymentValid = computed(() => {
    if (calculationMode.value === 'months' && monthlyPayment.value && remainingAmount.value > 0) {
        const months = Math.ceil(remainingAmount.value / monthlyPayment.value);
        return months <= maxMonths.value;
    }
    return true;
});

const isValid = computed(() => {
    const depositValid = deposit.value >= minDeposit.value && deposit.value < totalPrice.value;
    
    if (calculationMode.value === 'payment') {
        return depositValid && numberOfMonths.value !== null && numberOfMonths.value > 0 && numberOfMonths.value <= maxMonths.value;
    } else {
        return depositValid && monthlyPayment.value !== null && monthlyPayment.value > 0 && isPaymentValid.value;
    }
});

function validateInputs() {
    errors.value.deposit = '';
    errors.value.monthlyPayment = '';
    errors.value.numberOfMonths = '';

    const currentTotal = totalPrice.value;

    if (deposit.value < minDeposit.value) {
        errors.value.deposit = `Minimum deposit is ${formatCurrency(minDeposit.value)}`;
    }
    if (deposit.value >= currentTotal) {
        errors.value.deposit = `Deposit cannot exceed the total price of ${formatCurrency(currentTotal)}`;
    }
    
    if (calculationMode.value === 'payment') {
        if (numberOfMonths.value !== null) {
            if (numberOfMonths.value <= 0) {
                errors.value.numberOfMonths = 'Number of months must be greater than 0';
            } else if (numberOfMonths.value > maxMonths.value) {
                errors.value.numberOfMonths = `Maximum payment period is ${maxMonths.value} months`;
            }
        }
    } else {
        if (monthlyPayment.value !== null && monthlyPayment.value <= 0) {
            errors.value.monthlyPayment = 'Monthly payment must be greater than 0';
        }
        if (monthlyPayment.value !== null && monthlyPayment.value < 1000) {
            errors.value.monthlyPayment = 'Minimum monthly payment is KES 1,000';
        }
        if (monthlyPayment.value !== null && !isPaymentValid.value) {
            const minPayment = Math.ceil(remainingAmount.value / maxMonths.value);
            errors.value.monthlyPayment = `Monthly payment must be at least ${formatCurrency(minPayment)} to complete within ${maxMonths.value} months`;
        }
    }
}

function formatCurrency(amount: number) {
    return 'KES ' + amount.toLocaleString();
}

function resetCalculator() {
    deposit.value = minDeposit.value;
    monthlyPayment.value = null;
    numberOfMonths.value = null;
    errors.value = { deposit: '', monthlyPayment: '', numberOfMonths: '' };
}

watch([() => props.minDeposit, () => props.pricingTiers], () => {
    deposit.value = minDeposit.value;
    monthlyPayment.value = null;
    numberOfMonths.value = null;
    errors.value = { deposit: '', monthlyPayment: '', numberOfMonths: '' };
}, { deep: true });

watch([deposit, monthlyPayment, numberOfMonths, calculationMode], () => {
    validateInputs();
}, { deep: true });

onMounted(() => {
    deposit.value = minDeposit.value;
    validateInputs();
});
</script>

<template>
    <div class="rounded-2xl bg-white p-6 shadow-lg ring-1 ring-gray-200 md:p-8">
        <!-- Header -->
        <div class="mb-6 flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-teal-100 text-teal-600">
                <Calculator class="h-5 w-5" />
            </div>
            <div>
                <h3 class="text-xl font-bold text-gray-800">Payment Calculator</h3>
                <p class="text-sm text-gray-500">Plan your investment for {{ projectName }}</p>
            </div>
        </div>

        <!-- Total Price -->
        <div class="mb-4 rounded-xl bg-teal-600 p-4 text-center text-white">
            <p class="text-sm text-white/80">Total Price</p>
            <p class="text-2xl font-bold">{{ formatCurrency(totalPrice) }}</p>
            <p v-if="selectedTier" class="text-xs text-white/70">
                {{ selectedTier.label }} payment plan
            </p>
        </div>

        <!-- Pricing Tiers Info -->
        <div class="mb-4 grid grid-cols-3 gap-2 rounded-lg bg-blue-50 p-2 text-center text-xs">
            <div
                v-for="tier in tiers"
                :key="tier.months"
                class="rounded bg-white p-2 shadow-sm"
            >
                <p class="font-bold text-teal-600">{{ tier.label }}</p>
                <p class="text-gray-700">{{ formatCurrency(tier.price) }}</p>
            </div>
        </div>

        <!-- Deposit Input -->
        <div class="mb-4">
            <label class="mb-1 block text-sm font-medium text-gray-700">
                Deposit (Min. {{ formatCurrency(minDeposit) }})
            </label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">KES</span>
                <input
                    v-model.number="deposit"
                    type="number"
                    class="w-full rounded-lg text-black border border-gray-300 px-4 py-3 pl-16 focus:border-teal-600 focus:outline-none focus:ring-2 focus:ring-teal-200"
                    step="10000"
                />
            </div>
            <div class="mt-1 flex justify-between text-xs text-gray-500">
                <span>Min: {{ formatCurrency(minDeposit) }}</span>
                <span>Max: {{ formatCurrency(totalPrice - 1) }}</span>
            </div>
            <p v-if="errors.deposit" class="mt-1 text-sm text-red-500">{{ errors.deposit }}</p>
        </div>

        <!-- Divider -->
        <div class="my-4 flex items-center gap-4">
            <div class="flex-1 border-t border-gray-200"></div>
            <span class="text-xs font-medium uppercase text-gray-400">Remaining: {{ formatCurrency(remainingAmount) }}</span>
            <div class="flex-1 border-t border-gray-200"></div>
        </div>

        <!-- Info Banner -->
        <div class="mb-4 flex items-start gap-2 rounded-lg bg-blue-50 p-3 text-sm text-blue-700">
            <Info class="mt-0.5 h-4 w-4 flex-shrink-0" />
            <div>
                <p>💡 Payment period: <strong>1–{{ maxMonths }} months</strong></p>
                <p class="mt-1 text-xs text-blue-600">
                    Price varies based on your chosen payment period. Shorter periods = lower total price.
                </p>
            </div>
        </div>

        <!-- Calculation Mode Toggle -->
        <div class="mb-4 grid grid-cols-2 gap-2">
            <button
                @click="calculationMode = 'payment'"
                class="rounded-lg px-4 py-2 text-sm font-medium transition"
                :class="
                    calculationMode === 'payment'
                        ? 'bg-teal-600 text-white'
                        : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                "
            >
                Calculate Monthly Payment
            </button>
            <button
                @click="calculationMode = 'months'"
                class="rounded-lg px-4 py-2 text-sm font-medium transition"
                :class="
                    calculationMode === 'months'
                        ? 'bg-teal-600 text-white'
                        : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                "
            >
                Calculate Number of Months
            </button>
        </div>

        <!-- Input: Number of Months -->
        <div v-if="calculationMode === 'payment'" class="mb-4">
            <label class="mb-1 block text-sm font-medium text-gray-700">
                Number of Months (1–{{ maxMonths }})
            </label>
            <input
                v-model.number="numberOfMonths"
                type="number"
                class="w-full rounded-lg border text-black border-gray-300 px-4 py-3 focus:border-teal-600 focus:outline-none focus:ring-2 focus:ring-teal-200"
                placeholder="e.g., 6"
                min="1"
                :max="maxMonths"
            />
            <p v-if="errors.numberOfMonths" class="mt-1 text-sm text-red-500">{{ errors.numberOfMonths }}</p>
            <p class="mt-1 text-xs text-gray-500">Choose between 1 and {{ maxMonths }} months</p>
            <p v-if="numberOfMonths && numberOfMonths > 0" class="mt-1 text-xs text-teal-600">
                Price for this plan: {{ formatCurrency(getPriceForMonths(numberOfMonths)) }}
                ({{ getTierLabel(numberOfMonths) }})
            </p>
        </div>

        <!-- Input: Monthly Payment -->
        <div v-if="calculationMode === 'months'" class="mb-4">
            <label class="mb-1 block text-sm font-medium text-gray-700">
                Monthly Payment (KES)
            </label>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">KES</span>
                <input
                    v-model.number="monthlyPayment"
                    type="number"
                    class="w-full rounded-lg border text-black border-gray-300 px-4 py-3 pl-16 focus:border-teal-600 focus:outline-none focus:ring-2 focus:ring-teal-200"
                    placeholder="e.g., 100000"
                    min="1000"
                    step="1000"
                />
            </div>
            <p v-if="errors.monthlyPayment" class="mt-1 text-sm text-red-500">{{ errors.monthlyPayment }}</p>
            <p class="mt-1 text-xs text-gray-500">
                Minimum: {{ formatCurrency(Math.ceil(remainingAmount / maxMonths)) }} to finish in {{ maxMonths }} months
            </p>
        </div>

        <!-- Results -->
        <div v-if="isValid" class="mt-6 space-y-3">
            <div class="rounded-xl border border-green-200 bg-green-50 p-4">
                <div class="flex items-center gap-2 text-green-700">
                    <CheckCircle class="h-5 w-5" />
                    <span class="font-semibold">Payment Plan Summary</span>
                </div>

                <div class="mt-3 grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Total Price</p>
                        <p class="font-bold text-gray-900">{{ formatCurrency(totalPrice) }}</p>
                        <p v-if="selectedTier" class="text-xs text-gray-500">{{ selectedTier.label }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Deposit</p>
                        <p class="font-bold text-gray-900">{{ formatCurrency(deposit) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Remaining Balance</p>
                        <p class="font-bold text-teal-600">{{ formatCurrency(remainingAmount) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Amount to Pay</p>
                        <p class="font-bold text-gray-900">{{ formatCurrency(remainingAmount) }}</p>
                    </div>
                </div>

                <div class="mt-3 border-t border-green-200 pt-3">
                    <div v-if="calculationMode === 'payment' && calculatedMonthlyPayment">
                        <p class="text-sm text-gray-600">Monthly Payment</p>
                        <p class="text-2xl font-bold text-teal-600">{{ formatCurrency(calculatedMonthlyPayment) }}</p>
                        <p class="text-xs text-gray-500">
                            Over {{ numberOfMonths }} months 
                            ({{ getTierLabel(numberOfMonths || 1) }} tier)
                        </p>
                    </div>
                    <div v-if="calculationMode === 'months' && calculatedMonths">
                        <p class="text-sm text-gray-600">Number of Months</p>
                        <p class="text-2xl font-bold text-teal-600">{{ calculatedMonths }} months</p>
                        <p class="text-xs text-gray-500">
                            At {{ formatCurrency(monthlyPayment || 0) }} per month
                            ({{ getTierLabel(calculatedMonths) }} tier)
                        </p>
                        <p v-if="calculatedMonths === maxMonths" class="mt-1 text-xs text-amber-600">
                            ⚡ Maximum payment period reached
                        </p>
                    </div>
                </div>
            </div>

            <!-- ===== Updated CTA Button ===== -->
            <div class="text-center">
                <p class="text-sm text-gray-600">Ready to secure your plot?</p>
                <button
                    @click="emit('bookVisit')"
                    class="mt-2 inline-block rounded-lg bg-green-600 px-6 py-3 font-bold text-white transition hover:bg-green-700"
                >
                    Book a Site Visit Now
                </button>
            </div>
        </div>

        <div v-else-if="deposit > 0 || monthlyPayment || numberOfMonths" class="mt-6">
            <div class="rounded-xl border border-yellow-200 bg-yellow-50 p-4">
                <div class="flex items-center gap-2 text-yellow-700">
                    <AlertCircle class="h-5 w-5" />
                    <span class="font-semibold">Please check your inputs</span>
                </div>
                <ul class="mt-2 list-inside list-disc text-sm text-yellow-700">
                    <li>Deposit must be at least {{ formatCurrency(minDeposit) }}</li>
                    <li>Payment period cannot exceed {{ maxMonths }} months</li>
                    <li>Monthly payment must be sufficient to complete within {{ maxMonths }} months</li>
                </ul>
            </div>
        </div>

        <button
            @click="resetCalculator"
            class="mt-4 text-sm text-gray-500 transition hover:text-teal-600"
        >
            Reset Calculator
        </button>
    </div>
</template>

<style scoped>
input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
input[type="number"] {
    -moz-appearance: textfield;
}
</style>