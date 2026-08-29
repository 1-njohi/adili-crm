<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Mobile Navigation Bar -->
        <nav
            class="fixed top-0 right-0 left-0 z-50 flex items-center justify-between bg-white px-4 py-3 shadow-sm lg:hidden"
            v-if="isOwner || isAgent || isBuyer"
        >
            <button
                @click="sidebarOpen = true"
                class="-ml-2 rounded-lg p-2 hover:bg-gray-100"
            >
                <svg
                    class="h-6 w-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16"
                    />
                </svg>
            </button>
            <span class="text-lg font-semibold">Adili CRM</span>
            <div class="w-10"></div>
        </nav>

        <!-- Sidebar -->
        <aside
            :class="[
                'fixed top-0 left-0 z-50 h-full w-64 bg-white shadow-lg transition-transform duration-300',
                'lg:translate-x-0',
                sidebarOpen ? 'translate-x-0' : '-translate-x-full',
            ]"
            v-if="isOwner || isAgent || isBuyer"
        >
            <div class="flex h-16 items-center justify-between border-b px-4">
                <span class="text-lg font-bold">Adili CRM</span>
                <button
                    @click="sidebarOpen = false"
                    class="rounded-lg p-1 hover:bg-gray-100 lg:hidden"
                >
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>

            <div class="space-y-1 p-3">
                <!-- HARDCODED LINKS -->
                <template v-if="isOwner">
                    <a
                        href="/owner/dashboard"
                        class="flex items-center rounded-lg px-3 py-2 hover:bg-gray-100"
                        @click="sidebarOpen = false"
                    >
                        Dashboard
                    </a>
                    <a
                        href="/owner/projects"
                        class="flex items-center rounded-lg px-3 py-2 hover:bg-gray-100"
                        @click="sidebarOpen = false"
                    >
                        Projects
                    </a>
                    <a
                        href="/owner/agents"
                        class="flex items-center rounded-lg px-3 py-2 hover:bg-gray-100"
                        @click="sidebarOpen = false"
                    >
                        Agents
                    </a>
                    <a
                        href="#"
                        class="flex items-center rounded-lg px-3 py-2 hover:bg-gray-100"
                        @click="sidebarOpen = false"
                        >Site Visits</a
                    >
                    <a
                        href="#"
                        class="flex items-center rounded-lg px-3 py-2 hover:bg-gray-100"
                        @click="sidebarOpen = false"
                        >Reconciliation</a
                    >
                    <a
                        href="#"
                        class="flex items-center rounded-lg px-3 py-2 hover:bg-gray-100"
                        @click="sidebarOpen = false"
                        >Reports</a
                    >
                </template>

                <template v-if="isAgent">
                    <a
                        href="/agent/dashboard"
                        class="flex items-center rounded-lg px-3 py-2 hover:bg-gray-100"
                        @click="sidebarOpen = false"
                    >
                        Dashboard
                    </a>
                    <a
                        href="#"
                        class="flex items-center rounded-lg px-3 py-2 hover:bg-gray-100"
                        @click="sidebarOpen = false"
                        >Leads</a
                    >
                    <a
                        href="#"
                        class="flex items-center rounded-lg px-3 py-2 hover:bg-gray-100"
                        @click="sidebarOpen = false"
                        >Site Visits</a
                    >
                    <a
                        href="#"
                        class="flex items-center rounded-lg px-3 py-2 hover:bg-gray-100"
                        @click="sidebarOpen = false"
                        >Marketing</a
                    >
                    <a
                        href="#"
                        class="flex items-center rounded-lg px-3 py-2 hover:bg-gray-100"
                        @click="sidebarOpen = false"
                        >Payouts</a
                    >
                </template>

                <template v-if="isBuyer">
                    <a
                        href="#"
                        class="flex items-center rounded-lg px-3 py-2 hover:bg-gray-100"
                        @click="sidebarOpen = false"
                        >My Plot</a
                    >
                    <a
                        href="#"
                        class="flex items-center rounded-lg px-3 py-2 hover:bg-gray-100"
                        @click="sidebarOpen = false"
                        >Payments</a
                    >
                    <a
                        href="#"
                        class="flex items-center rounded-lg px-3 py-2 hover:bg-gray-100"
                        @click="sidebarOpen = false"
                        >Referrals</a
                    >
                </template>

                <hr class="my-2 lg:hidden" />

                <a
                    href="/settings"
                    class="flex items-center rounded-lg px-3 py-2 hover:bg-gray-100 lg:hidden"
                    @click="sidebarOpen = false"
                >
                    Settings
                </a>
                <a
                    href="/logout"
                    class="flex items-center rounded-lg px-3 py-2 hover:bg-gray-100 lg:hidden"
                    @click="sidebarOpen = false"
                >
                    Logout
                </a>
            </div>
        </aside>

        <!-- Overlay -->
        <div
            v-if="sidebarOpen"
            @click="sidebarOpen = false"
            class="fixed inset-0 z-40 bg-black/30 lg:hidden"
        ></div>

        <!-- Main Content -->
        <div :class="[isOwner || isAgent || isBuyer ? 'lg:ml-64' : '']">
            <!-- Desktop Header -->
            <header
                v-if="isOwner || isAgent || isBuyer"
                class="hidden h-16 items-center justify-between bg-white px-6 shadow-sm lg:flex"
            >
                <h1 class="text-xl font-semibold">{{ title }}</h1>
                <div class="relative">
                    <button
                        @click="dropdownOpen = !dropdownOpen"
                        class="flex items-center gap-2 rounded-lg px-3 py-2 hover:bg-gray-100"
                    >
                        {{ user?.name }}
                        <svg
                            class="h-4 w-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 9l-7 7-7-7"
                            />
                        </svg>
                    </button>
                    <div
                        v-if="dropdownOpen"
                        class="absolute right-0 z-50 mt-2 w-48 rounded-lg border bg-white py-1 shadow-lg"
                    >
                        <a
                            href="/settings"
                            class="block px-4 py-2 hover:bg-gray-100"
                            >Settings</a
                        >
                        <a
                            href="/logout"
                            class="block px-4 py-2 hover:bg-gray-100"
                            >Logout</a
                        >
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-4 pt-20 lg:p-6 lg:pt-6">
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    title: {
        type: String,
        default: 'Dashboard',
    },
});

const page = usePage();
const user = computed(() => page.props.auth.user);
const role = computed(() => user.value?.role);

const isOwner = computed(
    () => role.value === 'superadmin' || role.value === 'manager',
);
const isAgent = computed(() => role.value === 'agent');
const isBuyer = computed(() => role.value === 'buyer');

const sidebarOpen = ref(false);
const dropdownOpen = ref(false);

// Close dropdown on outside click
const handleClickOutside = (e) => {
    if (!e.target.closest('.relative')) {
        dropdownOpen.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>
