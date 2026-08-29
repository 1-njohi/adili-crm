<template>
    <AuthenticatedLayout title="Create Project">
        <div class="bg-white rounded-lg shadow p-6 max-w-4xl">
            <form @submit.prevent="submit" enctype="multipart/form-data">
                <!-- ===== Basic Info ===== -->
                <h3 class="text-lg font-semibold mb-4 border-b pb-2">Basic Information</h3>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Project Name *</label>
                    <input v-model="form.name" type="text" class="w-full border rounded px-3 py-2" required>
                    <p v-if="form.errors.name" class="text-red-600 text-sm">{{ form.errors.name }}</p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Location</label>
                    <input v-model="form.location" type="text" class="w-full border rounded px-3 py-2">
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Land Size</label>
                        <input v-model="form.land_size" type="text" class="w-full border rounded px-3 py-2" placeholder="e.g., 2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Unit</label>
                        <select v-model="form.land_size_unit" class="w-full border rounded px-3 py-2">
                            <option value="acres">Acres</option>
                            <option value="hectares">Hectares</option>
                            <option value="sqm">Square Meters</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Status</label>
                    <select v-model="form.status" class="w-full border rounded px-3 py-2">
                        <option value="draft">Draft</option>
                        <option value="active">Active</option>
                        <option value="sold_out">Sold Out</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Neighbor Discount (%)</label>
                    <input v-model="neighborDiscount" type="number" class="w-full border rounded px-3 py-2" placeholder="e.g., 5">
                </div>

                <!-- ===== Images ===== -->
                <h3 class="text-lg font-semibold mb-4 mt-6 border-b pb-2">Images</h3>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Primary Image</label>
                    <input type="file" @input="form.primary_image = $event.target.files[0]" accept="image/*" class="w-full border rounded px-3 py-2">
                    <p v-if="form.errors.primary_image" class="text-red-600 text-sm">{{ form.errors.primary_image }}</p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Gallery Images (select multiple)</label>
                    <input type="file" @input="form.gallery_images = $event.target.files" accept="image/*" multiple class="w-full border rounded px-3 py-2">
                    <p v-if="form.errors['gallery_images.0']" class="text-red-600 text-sm">{{ form.errors['gallery_images.0'] }}</p>
                </div>

                <!-- ===== Microsite Content ===== -->
                <h3 class="text-lg font-semibold mb-4 mt-6 border-b pb-2">Microsite Content</h3>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Hero Description</label>
                    <textarea v-model="form.hero_description" rows="3" class="w-full border rounded px-3 py-2" placeholder="Main project description..."></textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Hero Description 2</label>
                    <textarea v-model="form.hero_description_2" rows="3" class="w-full border rounded px-3 py-2" placeholder="Secondary description..."></textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Drone Video ID (YouTube)</label>
                    <input v-model="form.drone_video_id" type="text" class="w-full border rounded px-3 py-2" placeholder="e.g., aUL4d_5Gv54">
                    <p class="text-xs text-gray-500 mt-1">The part after ?v= in the YouTube URL</p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Brochure (PDF)</label>
                    <input type="file" @input="form.brochure = $event.target.files[0]" accept=".pdf" class="w-full border rounded px-3 py-2">
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Plot Size</label>
                        <input v-model="form.plot_size" type="text" class="w-full border rounded px-3 py-2" placeholder="e.g., 50×100">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Starting Price (KES)</label>
                        <input v-model="form.starting_price" type="number" step="0.01" class="w-full border rounded px-3 py-2" placeholder="e.g., 1500000">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Min Deposit (KES)</label>
                        <input v-model="form.min_deposit" type="number" step="0.01" class="w-full border rounded px-3 py-2" placeholder="e.g., 300000">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Max Months</label>
                        <input v-model="form.max_months" type="number" class="w-full border rounded px-3 py-2" placeholder="e.g., 9">
                    </div>
                </div>

                <!-- ===== Payment Tiers ===== -->
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Payment Tiers (JSON)</label>
                    <textarea v-model="paymentTiersJson" rows="4" class="w-full border rounded px-3 py-2 font-mono text-sm" placeholder='[{"months": 3, "price": 1500000, "label": "0 - 3 months"}, {"months": 6, "price": 1550000, "label": "3 - 6 months"}]'></textarea>
                    <p class="text-xs text-gray-500 mt-1">Format: [{"months": 3, "price": 1500000, "label": "0 - 3 months"}]</p>
                </div>

                <!-- ===== Feature Groups ===== -->
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Feature Groups (JSON)</label>
                    <textarea v-model="featureGroupsJson" rows="6" class="w-full border rounded px-3 py-2 font-mono text-sm" placeholder='[{"title": "Key Highlights", "icon": "MapPin", "items": ["Item 1", "Item 2"]}]'></textarea>
                    <p class="text-xs text-gray-500 mt-1">Format: [{"title": "Key Highlights", "icon": "MapPin", "items": ["item1", "item2"]}]</p>
                    <p class="text-xs text-gray-500">Available icons: MapPin, Zap, TrendingUp, Building, Car, Hotel, Landmark, ShoppingBag</p>
                </div>

                <!-- ===== Amenities ===== -->
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Amenities (JSON)</label>
                    <textarea v-model="amenitiesJson" rows="6" class="w-full border rounded px-3 py-2 font-mono text-sm" placeholder='[{"title": "Transport", "icon": "Car", "description": "..."}]'></textarea>
                    <p class="text-xs text-gray-500 mt-1">Format: [{"title": "Transport", "icon": "Car", "description": "..."}]</p>
                </div>

                <!-- ===== SEO ===== -->
                <h3 class="text-lg font-semibold mb-4 mt-6 border-b pb-2">SEO & Meta</h3>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Meta Title</label>
                    <input v-model="form.meta_title" type="text" class="w-full border rounded px-3 py-2" placeholder="SEO Title...">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Meta Description</label>
                    <textarea v-model="form.meta_description" rows="2" class="w-full border rounded px-3 py-2" placeholder="SEO Description..."></textarea>
                </div>

                <!-- ===== Actions ===== -->
                <div class="flex gap-2 mt-6">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700" :disabled="form.processing">
                        Create Project
                    </button>
                    <a href="/owner/projects" class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import AuthenticatedLayout from '@/layouts/AuthLayout.vue';

const form = useForm({
    name: '',
    location: '',
    land_size: '',
    land_size_unit: 'acres',
    status: 'draft',
    neighbor_discount: null,
    primary_image: null,
    gallery_images: null,

    // Microsite fields
    hero_description: '',
    hero_description_2: '',
    meta_title: '',
    meta_description: '',
    drone_video_id: '',
    brochure: null,
    starting_price: '',
    plot_size: '',
    min_deposit: '',
    max_months: '',
    payment_tiers: null,
    feature_groups: null,
    amenities: null,
});

const neighborDiscount = computed({
    get: () => form.neighbor_discount?.percentage || '',
    set: (value) => {
        form.neighbor_discount = value ? { percentage: parseFloat(value), active: true } : null;
    },
});

const paymentTiersJson = computed({
    get: () => form.payment_tiers ? JSON.stringify(form.payment_tiers, null, 2) : '',
    set: (value) => {
        try {
            form.payment_tiers = value ? JSON.parse(value) : null;
        } catch (e) {
            // Invalid JSON, ignore
        }
    },
});

const featureGroupsJson = computed({
    get: () => form.feature_groups ? JSON.stringify(form.feature_groups, null, 2) : '',
    set: (value) => {
        try {
            form.feature_groups = value ? JSON.parse(value) : null;
        } catch (e) {
            // Invalid JSON, ignore
        }
    },
});

const amenitiesJson = computed({
    get: () => form.amenities ? JSON.stringify(form.amenities, null, 2) : '',
    set: (value) => {
        try {
            form.amenities = value ? JSON.parse(value) : null;
        } catch (e) {
            // Invalid JSON, ignore
        }
    },
});

const submit = () => {
    form.post('/owner/projects', {
        forceFormData: true,
    });
};
</script>