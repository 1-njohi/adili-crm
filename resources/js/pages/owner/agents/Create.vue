<template>
    <AuthenticatedLayout title="Create Agent">
        <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
            <form @submit.prevent="submit">
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Name *</label>
                    <input v-model="form.name" type="text" class="w-full border rounded px-3 py-2" required>
                    <p v-if="form.errors.name" class="text-red-600 text-sm">{{ form.errors.name }}</p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Email *</label>
                    <input v-model="form.email" type="email" class="w-full border rounded px-3 py-2" required>
                    <p v-if="form.errors.email" class="text-red-600 text-sm">{{ form.errors.email }}</p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Phone</label>
                    <input v-model="form.phone" type="text" class="w-full border rounded px-3 py-2">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Social Handles (JSON format)</label>
                    <textarea v-model="form.social_handles_string" rows="2" class="w-full border rounded px-3 py-2" placeholder='{"instagram": "@agentname", "facebook": "agentname"}'></textarea>
                    <p v-if="form.errors.social_handles" class="text-red-600 text-sm">{{ form.errors.social_handles }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Bank Name</label>
                        <input v-model="form.bank_name" type="text" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Account Number</label>
                        <input v-model="form.account_number" type="text" class="w-full border rounded px-3 py-2">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Account Holder Name</label>
                    <input v-model="form.account_holder_name" type="text" class="w-full border rounded px-3 py-2">
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700" :disabled="form.processing">
                        Create Agent
                    </button>
                    <a href="/owner/agents" class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import AuthenticatedLayout from '@/layouts/AuthLayout.vue';

const form = useForm({
    name: '',
    email: '',
    phone: '',
    social_handles_string: '',
    bank_name: '',
    account_number: '',
    account_holder_name: '',
});

const submit = () => {
    // Parse social handles string to JSON
    let social_handles = null;
    if (form.social_handles_string) {
        try {
            social_handles = JSON.parse(form.social_handles_string);
        } catch (e) {
            // Invalid JSON, show error
            form.errors.social_handles = 'Invalid JSON format. Please check your input.';
            return;
        }
    }

    form.social_handles = social_handles;
    delete form.social_handles_string;

    form.post('/owner/agents');
};
</script>