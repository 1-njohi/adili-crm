<template>
    <AuthenticatedLayout :title="`Add Plot to ${project.name}`">
        <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
            <form @submit.prevent="submit">
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Plot Number *</label>
                    <input v-model="form.plot_number" type="number" class="w-full border rounded px-3 py-2" required>
                    <p v-if="form.errors.plot_number" class="text-red-600 text-sm">{{ form.errors.plot_number }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Size</label>
                        <input v-model="form.size" type="text" class="w-full border rounded px-3 py-2" placeholder="e.g., 0.25">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Unit</label>
                        <select v-model="form.size_unit" class="w-full border rounded px-3 py-2">
                            <option value="acres">Acres</option>
                            <option value="hectares">Hectares</option>
                            <option value="sqm">Square Meters</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Payment Tiers</label>
                    <div v-for="(tier, index) in form.payment_tiers" :key="index" class="flex gap-2 mb-2">
                        <input v-model="tier.tier" type="text" class="flex-1 border rounded px-3 py-2" placeholder="e.g., 1-3 months">
                        <input v-model="tier.price" type="number" class="flex-1 border rounded px-3 py-2" placeholder="Price (KES)">
                        <button type="button" @click="removeTier(index)" class="px-3 py-2 bg-red-500 text-white rounded">×</button>
                    </div>
                    <button type="button" @click="addTier" class="text-blue-600 hover:underline text-sm">+ Add Tier</button>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Custom Attributes</label>
                    <div v-for="(attr, key) in form.custom_attributes" :key="key" class="flex gap-2 mb-2">
                        <input v-model="form.custom_attributes[key].key" type="text" class="flex-1 border rounded px-3 py-2" placeholder="Attribute name">
                        <input v-model="form.custom_attributes[key].value" type="text" class="flex-1 border rounded px-3 py-2" placeholder="Value">
                        <button type="button" @click="removeAttribute(key)" class="px-3 py-2 bg-red-500 text-white rounded">×</button>
                    </div>
                    <button type="button" @click="addAttribute" class="text-blue-600 hover:underline text-sm">+ Add Attribute</button>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Status</label>
                    <select v-model="form.status" class="w-full border rounded px-3 py-2">
                        <option value="available">Available</option>
                        <option value="reserved">Reserved</option>
                        <option value="sold">Sold</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700" :disabled="form.processing">
                        Add Plot
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
    plot_number: '',
    size: '',
    size_unit: 'acres',
    payment_tiers: [],
    custom_attributes: {},
    status: 'available',
});

const addTier = () => {
    form.payment_tiers.push({ tier: '', price: '' });
};

const removeTier = (index) => {
    form.payment_tiers.splice(index, 1);
};

const addAttribute = () => {
    const key = Object.keys(form.custom_attributes).length;
    form.custom_attributes[key] = { key: '', value: '' };
};

const removeAttribute = (key) => {
    delete form.custom_attributes[key];
};

const submit = () => {
    form.post(`/owner/projects/${props.project.id}/plots`);
};
</script>