<template>
    <AuthenticatedLayout :title="`Add Expense to ${project.name}`">
        <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
            <form @submit.prevent="submit" enctype="multipart/form-data">
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Amount (KES) *</label>
                    <input v-model="form.amount" type="number" step="0.01" class="w-full border rounded px-3 py-2" required>
                    <p v-if="form.errors.amount" class="text-red-600 text-sm">{{ form.errors.amount }}</p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Category *</label>
                    <select v-model="form.category" class="w-full border rounded px-3 py-2" required>
                        <option value="">Select Category</option>
                        <option value="Land Purchase">Land Purchase</option>
                        <option value="Survey">Survey</option>
                        <option value="Roads">Roads</option>
                        <option value="Water">Water</option>
                        <option value="Legal">Legal</option>
                        <option value="Marketing">Marketing</option>
                        <option value="Logistics">Logistics</option>
                        <option value="Client Entertainment">Client Entertainment</option>
                        <option value="Miscellaneous">Miscellaneous</option>
                    </select>
                    <p v-if="form.errors.category" class="text-red-600 text-sm">{{ form.errors.category }}</p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Description</label>
                    <textarea v-model="form.description" rows="2" class="w-full border rounded px-3 py-2"></textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Date Incurred</label>
                    <input v-model="form.incurred_at" type="date" class="w-full border rounded px-3 py-2">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Type</label>
                    <select v-model="form.type" class="w-full border rounded px-3 py-2">
                        <option value="pre_launch">Pre-Launch (Development)</option>
                        <option value="post_launch">Post-Launch (Selling)</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Receipt (optional)</label>
                    <input type="file" @input="form.receipt = $event.target.files[0]" class="w-full border rounded px-3 py-2">
                    <p v-if="form.errors.receipt" class="text-red-600 text-sm">{{ form.errors.receipt }}</p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Internal Notes</label>
                    <textarea v-model="form.internal_notes" rows="2" class="w-full border rounded px-3 py-2" placeholder="Private notes (owners only)"></textarea>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700" :disabled="form.processing">
                        Add Expense
                    </button>
                    <a :href="`/owner/projects/${project.id}`" class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthLayout.vue';

const props = defineProps({
    project: Object,
});

const form = useForm({
    amount: '',
    currency: 'KES',
    category: '',
    description: '',
    incurred_at: new Date().toISOString().split('T')[0],
    type: 'pre_launch',
    receipt: null,
    internal_notes: '',
});

const submit = () => {
    form.post(`/owner/projects/${props.project.id}/expenses`, {
        forceFormData: true,
    });
};
</script>