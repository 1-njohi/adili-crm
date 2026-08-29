<template>
    <div class="flex justify-center">
        <div class="max-w-2xl min-w-[50%] rounded-lg bg-white p-6 shadow">
            <h2 class="mb-4 text-xl font-semibold">
                Create Sale for Plot #{{ plot.plot_number }}
            </h2>

            <form @submit.prevent="submit" enctype="multipart/form-data">
                <!-- Buyer Name -->
                <div class="mb-4">
                    <label class="mb-1 block text-sm font-medium"
                        >Buyer's Full Name *</label
                    >
                    <input
                        v-model="form.name"
                        type="text"
                        class="w-full rounded border px-3 py-2"
                        required
                        placeholder="e.g., John Doe"
                    />
                    <p v-if="form.errors.name" class="text-sm text-red-600">
                        {{ form.errors.name }}
                    </p>
                </div>

                <!-- Buyer Email -->
                <div class="mb-4">
                    <label class="mb-1 block text-sm font-medium"
                        >Buyer's Email *</label
                    >
                    <input
                        v-model="form.email"
                        type="email"
                        class="w-full rounded border px-3 py-2"
                        required
                        placeholder="john@example.com"
                    />
                    <p v-if="form.errors.email" class="text-sm text-red-600">
                        {{ form.errors.email }}
                    </p>
                </div>

                <!-- Buyer Phone -->
                <div class="mb-4">
                    <label class="mb-1 block text-sm font-medium"
                        >Buyer's Phone *</label
                    >
                    <input
                        v-model="form.phone"
                        type="tel"
                        class="w-full rounded border px-3 py-2"
                        required
                        placeholder="+254 700 000 000"
                    />
                    <p v-if="form.errors.phone" class="text-sm text-red-600">
                        {{ form.errors.phone }}
                    </p>
                </div>

                <!-- Deposit -->
                <div class="mb-4">
                    <label class="mb-1 block text-sm font-medium"
                        >Deposit (KES) *</label
                    >
                    <input
                        v-model.number="form.deposit"
                        type="number"
                        class="w-full rounded border px-3 py-2"
                        required
                        min="0"
                        placeholder="e.g., 300000"
                    />
                    <p v-if="form.errors.deposit" class="text-sm text-red-600">
                        {{ form.errors.deposit }}
                    </p>
                </div>

                <!-- Deposit Receipt (File Upload) -->
                <div class="mb-4">
                    <label class="mb-1 block text-sm font-medium"
                        >Deposit Receipt *</label
                    >
                    <input
                        type="file"
                        @input="form.deposit_receipt = $event.target.files[0]"
                        accept=".jpg,.jpeg,.png,.pdf"
                        class="w-full rounded border px-3 py-2"
                        required
                    />
                    <p class="mt-1 text-xs text-gray-500">
                        Accepted: JPG, PNG, PDF (max 5MB)
                    </p>
                    <p
                        v-if="form.errors.deposit_receipt"
                        class="text-sm text-red-600"
                    >
                        {{ form.errors.deposit_receipt }}
                    </p>
                </div>

                <!-- Months -->
                <div class="mb-4">
                    <label class="mb-1 block text-sm font-medium"
                        >Months *</label
                    >
                    <input
                        v-model.number="form.months"
                        type="number"
                        class="w-full rounded border px-3 py-2"
                        required
                        min="1"
                        placeholder="e.g., 6"
                    />
                    <p v-if="form.errors.months" class="text-sm text-red-600">
                        {{ form.errors.months }}
                    </p>
                </div>

                <!-- Total Price -->
                <div class="mb-4">
                    <label class="mb-1 block text-sm font-medium"
                        >Total Price (KES) *</label
                    >
                    <input
                        v-model.number="form.total_price"
                        type="number"
                        class="w-full rounded border px-3 py-2"
                        required
                        min="0"
                        placeholder="e.g., 1500000"
                    />
                    <p
                        v-if="form.errors.total_price"
                        class="text-sm text-red-600"
                    >
                        {{ form.errors.total_price }}
                    </p>
                </div>

                <!-- Monthly Payment (computed) -->
                <div class="mb-4">
                    <label class="mb-1 block text-sm font-medium"
                        >Monthly Payment</label
                    >
                    <div
                        class="w-full rounded border bg-gray-100 px-3 py-2 text-gray-700"
                    >
                        {{
                            monthlyPayment > 0
                                ? `KES ${monthlyPayment.toFixed(2)}`
                                : '-'
                        }}
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-4 flex gap-2">
                    <button
                        type="submit"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700 disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Creating...' : 'Create Sale' }}
                    </button>
                    <button
                        type="button"
                        class="rounded-lg bg-gray-300 px-4 py-2 hover:bg-gray-400"
                        @click="$emit('close')"
                    >
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    project: {
        type: Object,
        required: true,
    },
    plot: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['close']);

const form = useForm({
    name: '',
    email: '',
    phone: '',
    deposit: 0,
    months: 0,
    total_price: 0,
    deposit_receipt: null,
});

const monthlyPayment = computed(() => {
    if (form.months > 0 && form.total_price > form.deposit) {
        return (form.total_price - form.deposit) / form.months;
    }
    return 0;
});

const submit = () => {
    form.post(
        `/owner/projects/${props.project.id}/plots/${props.plot.id}/sales`,
        {
            forceFormData: true, // Required for file uploads
            onSuccess: () => {
                emit('close');
                window.location.reload();
            },
            onError: (errors) => {
                console.error('Sale creation failed', errors);
            },
        },
    );
};
</script>
