<template>
    <AuthenticatedLayout :title="`Agents - ${project.name}`">
        <!-- Assigned Agents -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Assigned Agents</h3>
                <a href="/owner/projects" class="text-blue-600 hover:underline text-sm">Back to Project</a>
            </div>

            <div v-if="assignedAgents.length === 0" class="text-center text-gray-500 py-4">
                No agents assigned yet.
            </div>

            <div v-else class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="text-left px-3 py-2">Name</th>
                            <th class="text-left px-3 py-2">Commission</th>
                            <th class="text-left px-3 py-2">Status</th>
                            <th class="text-left px-3 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="agent in assignedAgents" :key="agent.id" class="border-t">
                            <td class="px-3 py-2">{{ agent.name }}</td>
                            <td class="px-3 py-2">
                                <span v-if="agent.pivot.commission_type === 'percentage'">
                                    {{ agent.pivot.commission_rate }}%
                                </span>
                                <span v-else>
                                    {{ agent.pivot.commission_currency }} {{ agent.pivot.commission_rate }}
                                </span>
                            </td>
                            <td class="px-3 py-2">
                                <span :class="statusColor(agent.pivot.status)" class="px-2 py-1 rounded-full text-xs">
                                    {{ agent.pivot.status }}
                                </span>
                            </td>
                            <td class="px-3 py-2">
                                <div class="flex gap-2 text-xs">
                                    <button @click="editCommission(agent)" class="text-yellow-600 hover:underline">Edit Rate</button>
                                    <button v-if="agent.pivot.status !== 'inactive'" @click="barAgent(agent)" class="text-red-600 hover:underline">Bar</button>
                                    <button v-if="agent.pivot.status === 'inactive'" @click="unbarAgent(agent)" class="text-green-600 hover:underline">Reinstate</button>
                                    <button @click="removeAgent(agent)" class="text-red-600 hover:underline">Remove</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Available Agents -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Assign Agent to Project</h3>
            <form @submit.prevent="assignAgent">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Select Agent</label>
                        <select v-model="assignment.agent_id" class="w-full border rounded px-3 py-2" required>
                            <option value="">Select Agent</option>
                            <option v-for="agent in availableAgents" :key="agent.id" :value="agent.id">
                                {{ agent.name }} ({{ agent.email }})
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Commission Type</label>
                        <select v-model="assignment.commission_type" class="w-full border rounded px-3 py-2" required>
                            <option value="percentage">Percentage (%)</option>
                            <option value="flat">Flat Rate</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Commission Rate</label>
                        <input v-model="assignment.commission_rate" type="number" step="0.01" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700" :disabled="assignment.processing">
                            Assign Agent
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Edit Commission Modal -->
        <div v-if="showEditModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow p-6 max-w-md w-full">
                <h3 class="text-lg font-semibold mb-4">Edit Commission</h3>
                <form @submit.prevent="updateCommission">
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">Agent: {{ editingAgent?.name }}</label>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">Commission Type</label>
                        <select v-model="editForm.commission_type" class="w-full border rounded px-3 py-2">
                            <option value="percentage">Percentage (%)</option>
                            <option value="flat">Flat Rate</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">Commission Rate</label>
                        <input v-model="editForm.commission_rate" type="number" step="0.01" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700" :disabled="editForm.processing">
                            Update
                        </button>
                        <button type="button" @click="showEditModal = false" class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AuthLayout.vue';

const props = defineProps({
    project: Object,
    assignedAgents: Array,
    availableAgents: Array,
});

const assignment = useForm({
    agent_id: '',
    commission_type: 'percentage',
    commission_rate: '',
});

const showEditModal = ref(false);
const editingAgent = ref(null);
const editForm = useForm({
    commission_type: 'percentage',
    commission_rate: '',
});

const statusColor = (status) => {
    const colors = {
        active: 'bg-green-200 text-green-700',
        inactive: 'bg-red-200 text-red-700',
        pending_removal: 'bg-yellow-200 text-yellow-700',
    };
    return colors[status] || 'bg-gray-200 text-gray-700';
};

const assignAgent = () => {
    assignment.post(`/owner/projects/${props.project.id}/agents`);
};

const editCommission = (agent) => {
    editingAgent.value = agent;
    editForm.commission_type = agent.pivot.commission_type;
    editForm.commission_rate = agent.pivot.commission_rate;
    showEditModal.value = true;
};

const updateCommission = () => {
    editForm.put(`/owner/projects/${props.project.id}/agents/${editingAgent.value.id}`);
    showEditModal.value = false;
};

const barAgent = (agent) => {
    if (confirm(`Bar ${agent.name} from this project?`)) {
        router.post(`/owner/projects/${props.project.id}/agents/${agent.id}/bar`);
    }
};

const unbarAgent = (agent) => {
    if (confirm(`Reinstate ${agent.name}?`)) {
        router.post(`/owner/projects/${props.project.id}/agents/${agent.id}/unbar`);
    }
};

const removeAgent = (agent) => {
    if (confirm(`Remove ${agent.name} from this project?`)) {
        router.delete(`/owner/projects/${props.project.id}/agents/${agent.id}`);
    }
};
</script>