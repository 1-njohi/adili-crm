<template>
    <AuthenticatedLayout title="Edit Project">
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
                    <div v-if="form.existing_primary" class="mb-2">
                        <img :src="`/storage/${form.existing_primary}`" class="max-h-32 rounded border" alt="Primary">
                        <button type="button" @click="removePrimary" class="text-red-600 text-sm hover:underline">Remove</button>
                    </div>
                    <input type="file" @input="form.primary_image = $event.target.files[0]" accept="image/*" class="w-full border rounded px-3 py-2">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Gallery Images</label>
                    <div v-if="form.existing_gallery && form.existing_gallery.length" class="flex flex-wrap gap-2 mb-2">
                        <div v-for="(img, index) in form.existing_gallery" :key="index" class="relative border rounded p-1">
                            <img :src="`/storage/${img}`" class="h-16 w-16 object-cover rounded" alt="Gallery">
                            <button type="button" @click="removeGallery(index)" class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">×</button>
                        </div>
                    </div>
                    <input type="file" @input="form.gallery_images = $event.target.files" accept="image/*" multiple class="w-full border rounded px-3 py-2">
                </div>

                <!-- ===== Microsite Content ===== -->
                <h3 class="text-lg font-semibold mb-4 mt-6 border-b pb-2">Microsite Content</h3>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Hero Description</label>
                    <textarea v-model="form.hero_description" rows="3" class="w-full border rounded px-3 py-2"></textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Hero Description 2</label>
                    <textarea v-model="form.hero_description_2" rows="3" class="w-full border rounded px-3 py-2"></textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Drone Video ID (YouTube)</label>
                    <input v-model="form.drone_video_id" type="text" class="w-full border rounded px-3 py-2">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Brochure (PDF)</label>
                    <div v-if="form.existing_brochure" class="mb-2">
                        <a :href="`/storage/${form.existing_brochure}`" target="_blank" class="text-blue-600 hover:underline">View Current Brochure</a>
                    </div>
                    <input type="file" @input="form.brochure = $event.target.files[0]" accept=".pdf" class="w-full border rounded px-3 py-2">
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Plot Size</label>
                        <input v-model="form.plot_size" type="text" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Starting Price (KES)</label>
                        <input v-model="form.starting_price" type="number" step="0.01" class="w-full border rounded px-3 py-2">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Min Deposit (KES)</label>
                        <input v-model="form.min_deposit" type="number" step="0.01" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Max Months</label>
                        <input v-model="form.max_months" type="number" class="w-full border rounded px-3 py-2">
                    </div>
                </div>

                <!-- ===== Payment Tiers ===== -->
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Payment Tiers (JSON)</label>
                    <textarea v-model="paymentTiersJson" rows="4" class="w-full border rounded px-3 py-2 font-mono text-sm"></textarea>
                    <button type="button" @click="syncPaymentTiers" class="mt-1 text-sm text-blue-600 hover:underline">Sync from First Plot</button>
                </div>

                <!-- ===== Feature Groups ===== -->
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Feature Groups (JSON)</label>
                    <textarea v-model="featureGroupsJson" rows="6" class="w-full border rounded px-3 py-2 font-mono text-sm"></textarea>
                </div>

                <!-- ===== Amenities ===== -->
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Amenities (JSON)</label>
                    <textarea v-model="amenitiesJson" rows="6" class="w-full border rounded px-3 py-2 font-mono text-sm"></textarea>
                </div>

                <!-- ===== SEO ===== -->
                <h3 class="text-lg font-semibold mb-4 mt-6 border-b pb-2">SEO & Meta</h3>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Meta Title</label>
                    <input v-model="form.meta_title" type="text" class="w-full border rounded px-3 py-2">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Meta Description</label>
                    <textarea v-model="form.meta_description" rows="2" class="w-full border rounded px-3 py-2"></textarea>
                </div>

                <!-- ===== Actions ===== -->
                <div class="flex gap-2 mt-6">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700" :disabled="form.processing">
                        Update Project
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
import { computed } from 'vue';
import AuthenticatedLayout from '@/layouts/AuthLayout.vue';

const props = defineProps({
    project: Object,
});

const form = useForm({
    name: props.project.name,
    location: props.project.location || '',
    land_size: props.project.land_size || '',
    land_size_unit: props.project.land_size_unit || 'acres',
    status: props.project.status || 'draft',
    neighbor_discount: props.project.neighbor_discount || null,
    primary_image: null,
    gallery_images: null,
    existing_primary: props.project.primary_image || null,
    existing_gallery: props.project.gallery_images || [],
    remove_primary: false,
    remove_gallery: [],

    // Microsite fields
    hero_description: props.project.hero_description || '',
    hero_description_2: props.project.hero_description_2 || '',
    meta_title: props.project.meta_title || '',
    meta_description: props.project.meta_description || '',
    drone_video_id: props.project.drone_video_id || '',
    brochure: null,
    existing_brochure: props.project.brochure_path || null,
    starting_price: props.project.starting_price || '',
    plot_size: props.project.plot_size || '',
    min_deposit: props.project.min_deposit || '',
    max_months: props.project.max_months || '',
    payment_tiers: props.project.payment_tiers || null,
    feature_groups: props.project.feature_groups || null,
    amenities: props.project.amenities || null,
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

const removePrimary = () => {
    if (confirm('Remove primary image?')) {
        form.remove_primary = true;
        form.existing_primary = null;
    }
};

const removeGallery = (index) => {
    const img = form.existing_gallery[index];
    if (confirm('Remove this image?')) {
        form.remove_gallery.push(img);
        form.existing_gallery.splice(index, 1);
    }
};

const syncPaymentTiers = () => {
    if (props.project.plots && props.project.plots.length > 0 && props.project.plots[0].payment_tiers) {
        form.payment_tiers = props.project.plots[0].payment_tiers;
    } else {
        alert('No plots found or plot has no payment tiers.');
    }
};

const submit = () => {
    form.put(`/owner/projects/${props.project.id}`, {
        forceFormData: true,
    });
};
</script>