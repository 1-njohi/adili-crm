<template>
    <AuthenticatedLayout :title="project.name">
        <!-- Project Details -->
        <div class="mb-4 md:mb-6 rounded-lg bg-white p-4 md:p-6 shadow">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div>
                    <p class="text-gray-600 text-sm md:text-base">
                        {{ project.location || 'Location not set' }}
                    </p>
                    <p class="text-sm text-gray-500">
                        Land: {{ project.land_size }}
                        {{ project.land_size_unit }}
                    </p>
                    <p class="text-sm text-gray-500">
                        Status:
                        <span
                            :class="statusColor(project.status)"
                            class="rounded-full px-2 py-1 text-xs"
                            >{{ project.status }}</span
                        >
                    </p>
                </div>
                <div class="flex gap-2 flex-wrap">
                    <a
                        :href="`/owner/projects/${project.id}/edit`"
                        class="rounded bg-yellow-600 px-3 py-1.5 text-sm text-white hover:bg-yellow-700"
                    >
                        Edit
                    </a>
                    <a
                        href="/owner/projects"
                        class="rounded bg-gray-300 px-3 py-1.5 text-sm hover:bg-gray-400"
                    >
                        Back
                    </a>
                </div>
            </div>
        </div>

        <!-- Images Section -->
        <div class="mb-4 md:mb-6 rounded-lg bg-white p-4 md:p-6 shadow">
            <h3 class="mb-3 md:mb-4 text-base md:text-lg font-semibold">Images</h3>
            <div
                v-if="
                    project.primary_image ||
                    (project.gallery_images && project.gallery_images.length)
                "
                class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 md:gap-4"
            >
                <div v-if="project.primary_image" class="relative">
                    <img
                        :src="`/storage/${project.primary_image}`"
                        class="h-32 md:h-48 w-full rounded-lg border object-cover"
                        alt="Primary"
                    />
                    <span
                        class="absolute top-1 left-1 md:top-2 md:left-2 rounded bg-blue-600 px-1.5 py-0.5 md:px-2 md:py-1 text-[10px] md:text-xs text-white"
                        >Primary</span
                    >
                </div>
                <div
                    v-for="(img, index) in project.gallery_images"
                    :key="index"
                >
                    <img
                        :src="`/storage/${img}`"
                        class="h-32 md:h-48 w-full rounded-lg border object-cover"
                        :alt="`Gallery ${index + 1}`"
                    />
                </div>
            </div>
            <div v-else class="py-4 text-center text-gray-500">
                No images uploaded.
            </div>
        </div>

        <!-- Assigned Agents Section -->
        <div class="mb-4 md:mb-6 rounded-lg bg-white p-4 md:p-6 shadow">
            <div class="mb-3 md:mb-4 flex flex-wrap items-center justify-between gap-2">
                <h3 class="text-base md:text-lg font-semibold">Assigned Agents</h3>
                <a
                    :href="`/owner/projects/${project.id}/agents`"
                    class="rounded bg-purple-600 px-3 py-1.5 text-sm text-white hover:bg-purple-700"
                >
                    + Assign Agent
                </a>
            </div>

            <div
                v-if="!project.agents || project.agents.length === 0"
                class="py-4 text-center text-gray-500"
            >
                No agents assigned yet.
            </div>

            <!-- ✅ Render both, hide with Tailwind – no v-else -->
            <div v-if="project.agents && project.agents.length > 0">
                <!-- Desktop Table -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-3 py-2 text-left">Name</th>
                                <th class="px-3 py-2 text-left">Email</th>
                                <th class="px-3 py-2 text-left">Commission</th>
                                <th class="px-3 py-2 text-left">Status</th>
                                <th class="px-3 py-2 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="agent in project.agents"
                                :key="agent.id"
                                class="border-t"
                            >
                                <td class="px-3 py-2 font-medium">
                                    {{ agent.name }}
                                </td>
                                <td class="px-3 py-2">{{ agent.email }}</td>
                                <td class="px-3 py-2">
                                    <span
                                        v-if="
                                            agent.pivot.commission_type ===
                                            'percentage'
                                        "
                                    >
                                        {{ agent.pivot.commission_rate }}%
                                    </span>
                                    <span v-else>
                                        {{ agent.pivot.commission_currency }}
                                        {{
                                            Number(
                                                agent.pivot.commission_rate,
                                            ).toLocaleString()
                                        }}
                                    </span>
                                </td>
                                <td class="px-3 py-2">
                                    <span
                                        :class="
                                            agentStatusColor(agent.pivot.status)
                                        "
                                        class="rounded-full px-2 py-1 text-xs"
                                    >
                                        {{ agent.pivot.status }}
                                    </span>
                                </td>
                                <td class="px-3 py-2">
                                    <div class="flex gap-2 text-xs">
                                        <a
                                            :href="`/owner/projects/${project.id}/agents`"
                                            class="text-blue-600 hover:underline"
                                        >
                                            Manage
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards -->
                <div class="md:hidden space-y-3">
                    <div
                        v-for="agent in project.agents"
                        :key="agent.id"
                        class="bg-gray-50 rounded-lg p-3"
                    >
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-medium text-gray-800">{{ agent.name }}</p>
                                <p class="text-sm text-gray-500">{{ agent.email }}</p>
                            </div>
                            <span
                                :class="agentStatusColor(agent.pivot.status)"
                                class="rounded-full px-2 py-0.5 text-xs"
                            >
                                {{ agent.pivot.status }}
                            </span>
                        </div>
                        <div class="mt-1 text-sm">
                            <span class="text-gray-500">Commission:</span>
                            <span class="font-medium">
                                <span
                                    v-if="
                                        agent.pivot.commission_type ===
                                        'percentage'
                                    "
                                >
                                    {{ agent.pivot.commission_rate }}%
                                </span>
                                <span v-else>
                                    {{ agent.pivot.commission_currency }}
                                    {{
                                        Number(
                                            agent.pivot.commission_rate,
                                        ).toLocaleString()
                                    }}
                                </span>
                            </span>
                        </div>
                        <div class="mt-2">
                            <a
                                :href="`/owner/projects/${project.id}/agents`"
                                class="text-blue-600 hover:underline text-sm"
                            >
                                Manage →
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Plots Section -->
        <div class="mb-4 md:mb-6 rounded-lg bg-white p-4 md:p-6 shadow">
            <div class="mb-3 md:mb-4 flex flex-wrap items-center justify-between gap-2">
                <h3 class="text-base md:text-lg font-semibold">Plots</h3>
                <a
                    :href="`/owner/projects/${project.id}/plots/create`"
                    class="rounded bg-blue-600 px-3 py-1.5 text-sm text-white hover:bg-blue-700"
                >
                    + Add Plot
                </a>
            </div>

            <div
                v-if="project.plots.length === 0"
                class="py-4 text-center text-gray-500"
            >
                No plots added yet.
            </div>

            <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 md:gap-4">
                <div
                    v-for="plot in project.plots"
                    :key="plot.id"
                    class="rounded border p-3 md:p-4 hover:shadow-md transition"
                >
                    <div class="font-semibold text-sm md:text-base">
                        Plot #{{ plot.plot_number }}
                    </div>
                    <div class="text-sm text-gray-600">
                        Size: {{ plot.size }} {{ plot.size_unit }}
                    </div>
                    <div class="text-sm">
                        <span
                            :class="statusColor(plot.status)"
                            class="rounded-full px-2 py-0.5 text-xs"
                        >
                            {{ plot.status }}
                        </span>
                    </div>
                    <div class="mt-1 text-xs text-gray-500">
                        {{ Object.keys(plot.custom_attributes || {}).length }}
                        custom attributes
                    </div>
                    <div class="mt-2 flex flex-wrap gap-2 text-xs">
                        <a
                            v-if="
                                plot.status == 'available' ||
                                plot.status == 'reserved'
                            "
                            :href="`/owner/projects/${project.id}/plots/${plot.id}/sell`"
                            class="text-green-600 hover:underline"
                            >Sell</a
                        >
                        <a
                            v-else
                            :href="`/owner/projects/${project.id}/plots/${plot.id}`"
                            class="text-green-600 hover:underline"
                            >View</a
                        >
                        <a
                            :href="`/owner/projects/${project.id}/plots/${plot.id}/edit`"
                            class="text-yellow-600 hover:underline"
                            >Edit</a
                        >
                        <button
                            @click="deletePlot(plot)"
                            class="cursor-pointer text-red-600 hover:underline"
                        >
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Expenses Section -->
        <div class="rounded-lg bg-white p-4 md:p-6 shadow">
            <div class="mb-3 md:mb-4 flex flex-wrap items-center justify-between gap-2">
                <h3 class="text-base md:text-lg font-semibold">Expenses</h3>
                <a
                    :href="`/owner/projects/${project.id}/expenses/create`"
                    class="rounded bg-blue-600 px-3 py-1.5 text-sm text-white hover:bg-blue-700"
                >
                    + Add Expense
                </a>
            </div>

            <div
                v-if="project.expenses.length === 0"
                class="py-4 text-center text-gray-500"
            >
                No expenses recorded yet.
            </div>

            <!-- ✅ Render both, hide with Tailwind – no v-else -->
            <div v-if="project.expenses && project.expenses.length > 0">
                <!-- Desktop Table -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-3 py-2 text-left">Category</th>
                                <th class="px-3 py-2 text-left">Description</th>
                                <th class="px-3 py-2 text-right">Amount</th>
                                <th class="px-3 py-2 text-left">Type</th>
                                <th class="px-3 py-2 text-left">Date</th>
                                <th class="px-3 py-2 text-left">Receipt</th>
                                <th class="px-3 py-2 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="expense in project.expenses"
                                :key="expense.id"
                                class="border-t"
                            >
                                <td class="px-3 py-2">{{ expense.category }}</td>
                                <td class="px-3 py-2">
                                    {{ expense.description || '-' }}
                                </td>
                                <td class="px-3 py-2 text-right">
                                    {{ formatCurrency(expense.amount) }}
                                </td>
                                <td class="px-3 py-2">
                                    <span
                                        :class="
                                            expense.type === 'pre_launch'
                                                ? 'bg-blue-100 text-blue-700'
                                                : 'bg-green-100 text-green-700'
                                        "
                                        class="rounded-full px-2 py-1 text-xs"
                                    >
                                        {{ expense.type }}
                                    </span>
                                </td>
                                <td class="px-3 py-2">{{ formatDate(expense.incurred_at) }}</td>
                                <td class="px-3 py-2">
                                    <a
                                        v-if="expense.receipt_path"
                                        :href="`/storage/${expense.receipt_path}`"
                                        target="_blank"
                                        class="text-sm text-blue-600 hover:underline"
                                    >
                                        View
                                    </a>
                                    <span v-else class="text-sm text-gray-400"
                                        >None</span
                                    >
                                </td>
                                <td class="px-3 py-2">
                                    <div class="flex gap-2 text-xs">
                                        <a
                                            :href="`/owner/projects/${project.id}/expenses/${expense.id}/edit`"
                                            class="text-yellow-600 hover:underline"
                                            >Edit</a
                                        >
                                        <button
                                            @click="deleteExpense(expense)"
                                            class="text-red-600 hover:underline"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards -->
                <div class="md:hidden space-y-3">
                    <div
                        v-for="expense in project.expenses"
                        :key="expense.id"
                        class="bg-gray-50 rounded-lg p-3"
                    >
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-medium text-gray-800">{{ expense.category }}</p>
                                <p class="text-sm text-gray-500">{{ expense.description || '-' }}</p>
                            </div>
                            <span
                                :class="
                                    expense.type === 'pre_launch'
                                        ? 'bg-blue-100 text-blue-700'
                                        : 'bg-green-100 text-green-700'
                                "
                                class="rounded-full px-2 py-0.5 text-xs"
                            >
                                {{ expense.type }}
                            </span>
                        </div>
                        <div class="mt-1 flex justify-between items-center">
                            <span class="font-bold text-gray-800">{{ formatCurrency(expense.amount) }}</span>
                            <span class="text-xs text-gray-500">{{ formatDate(expense.incurred_at) }}</span>
                        </div>
                        <div class="mt-2 flex items-center justify-between">
                            <a
                                v-if="expense.receipt_path"
                                :href="`/storage/${expense.receipt_path}`"
                                target="_blank"
                                class="text-sm text-blue-600 hover:underline"
                            >
                                View Receipt
                            </a>
                            <span v-else class="text-sm text-gray-400">No receipt</span>
                            <div class="flex gap-2 text-xs">
                                <a
                                    :href="`/owner/projects/${project.id}/expenses/${expense.id}/edit`"
                                    class="text-yellow-600 hover:underline"
                                    >Edit</a
                                >
                                <button
                                    @click="deleteExpense(expense)"
                                    class="text-red-600 hover:underline"
                                >
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthLayout.vue';

const props = defineProps({
    project: Object,
});

const statusColor = (status) => {
    const colors = {
        available: 'bg-green-200 text-green-700',
        reserved: 'bg-yellow-200 text-yellow-700',
        sold: 'bg-blue-200 text-blue-700',
        draft: 'bg-gray-200 text-gray-700',
        active: 'bg-green-200 text-green-700',
        sold_out: 'bg-blue-200 text-blue-700',
    };
    return colors[status] || 'bg-gray-200 text-gray-700';
};

const agentStatusColor = (status) => {
    const colors = {
        active: 'bg-green-200 text-green-700',
        inactive: 'bg-red-200 text-red-700',
        pending_removal: 'bg-yellow-200 text-yellow-700',
    };
    return colors[status] || 'bg-gray-200 text-gray-700';
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-KE', {
        style: 'currency',
        currency: 'KES',
        minimumFractionDigits: 0,
    }).format(amount);
};

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-KE', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
};

const deletePlot = (plot) => {
    if (confirm(`Are you sure you want to delete Plot #${plot.plot_number}?`)) {
        router.delete(`/owner/projects/${props.project.id}/plots/${plot.id}`);
    }
};

const deleteExpense = (expense) => {
    if (confirm(`Are you sure you want to delete this expense?`)) {
        router.delete(
            `/owner/projects/${props.project.id}/expenses/${expense.id}`,
        );
    }
};
</script>