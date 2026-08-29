<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import {
    Building,
    Car,
    Check,
    ChevronLeft,
    ChevronRight,
    Download,
    Hotel,
    Landmark,
    MapPin,
    MessageCircle,
    Phone,
    ShoppingBag,
    TrendingUp,
    X,
    Zap,
    ZoomIn,
    ZoomOut,
} from 'lucide-vue-next';
import PaymentCalculator from '@/components/PaymentCalculator.vue';

// ===== Props =====
const props = defineProps({
    agent: Object,
    projects: Array,
    singleProject: Object,
    ref: String,
    hasMultipleProjects: Boolean,
});

// ===== Select Project =====
const selectedProjectId = ref(null);
const selectedProject = computed(() => {
    if (props.singleProject) {
        return props.singleProject;
    }
    if (selectedProjectId.value) {
        return props.projects.find((p) => p.id === selectedProjectId.value);
    }
    if (props.projects && props.projects.length > 0) {
        selectedProjectId.value = props.projects[0].id;
        return props.projects[0];
    }
    return null;
});

const selectProject = (projectId) => {
    selectedProjectId.value = projectId;
    videoUnlocked.value = false;
};

// ===== SEO & Meta =====
const canonicalUrl = computed(() => {
    return `https://adilirealestate.com/ref/${props.agent?.id}`;
});

const ogImage = computed(() => {
    if (selectedProject.value?.primary_image) {
        return `/storage/${selectedProject.value.primary_image}`;
    }
    return 'https://adilirealestate.com/images/og-default.jpg';
});

// ===== Source Detection =====
function getUtmSource(): string | null {
    const params = new URLSearchParams(window.location.search);
    return params.get('utm_source') || params.get('src');
}

function detectSourceFromReferrer(): string {
    const referrer = document.referrer;
    if (!referrer) return 'direct';

    try {
        const url = new URL(referrer);
        const hostname = url.hostname.toLowerCase();

        const platformMap: Record<string, string> = {
            'instagram.com': 'instagram',
            'www.instagram.com': 'instagram',
            'facebook.com': 'facebook',
            'www.facebook.com': 'facebook',
            'm.facebook.com': 'facebook',
            'twitter.com': 'twitter',
            'www.twitter.com': 'twitter',
            'x.com': 'twitter',
            'tiktok.com': 'tiktok',
            'www.tiktok.com': 'tiktok',
            'wa.me': 'whatsapp',
            'whatsapp.com': 'whatsapp',
            'youtube.com': 'youtube',
            'www.youtube.com': 'youtube',
            'linkedin.com': 'linkedin',
            'www.linkedin.com': 'linkedin',
            'telegram.org': 'telegram',
            't.me': 'telegram',
            'pinterest.com': 'pinterest',
            'www.pinterest.com': 'pinterest',
            'snapchat.com': 'snapchat',
            'www.snapchat.com': 'snapchat',
            'google.com': 'google',
            'www.google.com': 'google',
            'bing.com': 'bing',
            'www.bing.com': 'bing',
        };

        // Check exact match or subdomain
        for (const [domain, platform] of Object.entries(platformMap)) {
            if (hostname === domain || hostname.endsWith('.' + domain)) {
                return platform;
            }
        }

        // Fallback: use the domain name
        const parts = hostname.split('.');
        if (parts.length >= 2) {
            return parts[parts.length - 2]; // e.g., "google.com" → "google"
        }
        return 'direct';
    } catch (e) {
        return 'direct';
    }
}

// ===== Lead Capture =====
const showLeadModal = ref(false);
const targetAction = ref<'visit' | 'whatsapp' | 'call' | null>(null);
const leadForm = useForm({
    phone: '',
    name: '',
    email: '',
    consent: false,
    agent_id: props.agent?.id || null,
    project_id: null,
    source_platform: 'direct',
});

const submitLead = () => {
    if (!leadForm.agent_id) {
        alert(
            'This referral link is invalid. Please contact the agent directly.',
        );
        return;
    }
    leadForm.project_id = selectedProject.value?.id || null;
    if (!leadForm.project_id) {
        alert('Please select a project.');
        return;
    }

    leadForm.post('/capture-lead', {
        onSuccess: () => {
            showLeadModal.value = false;
            leadForm.reset();
            videoUnlocked.value = true;

            if (targetAction.value === 'visit') {
                alert(
                    'Thank you! We will contact you to arrange a site visit.',
                );
            } else if (targetAction.value === 'whatsapp') {
                const phoneNumber = '254720244744';
                const message = encodeURIComponent(
                    `Hi, I'm interested in ${selectedProject.value?.name || 'this project'}.`,
                );
                window.open(
                    `https://wa.me/${phoneNumber}?text=${message}`,
                    '_blank',
                );
            } else if (targetAction.value === 'call') {
                window.location.href = 'tel:+254720244744';
            }
            targetAction.value = null;
        },
        onError: (errors) => {
            console.error('Lead capture failed:', errors);
            alert('Something went wrong. Please try again.');
        },
    });
};

const triggerLeadCapture = (action: 'visit' | 'whatsapp' | 'call') => {
    if (!selectedProject.value) {
        alert('Please select a project first.');
        return;
    }
    targetAction.value = action;
    showLeadModal.value = true;
};

// ===== Video Lock =====
const videoUnlocked = ref(false);

const unlockVideo = () => {
    if (videoUnlocked.value) return;
    if (!selectedProject.value) {
        alert('Please select a project first.');
        return;
    }
    targetAction.value = 'visit';
    showLeadModal.value = true;
};

// ===== Gallery & Lightbox =====
const galleryImages = computed(() => {
    const images = [];
    const project = selectedProject.value;
    if (project?.primary_image) {
        images.push(`/storage/${project.primary_image}`);
    }
    if (project?.gallery_images) {
        project.gallery_images.forEach((img: string) => {
            images.push(`/storage/${img}`);
        });
    }
    return images.length > 0 ? images : ['/images/placeholder-project.jpg'];
});

const lightboxOpen = ref(false);
const lightboxIndex = ref(0);
const lightboxZoom = ref(1);

function openLightbox(index: number) {
    lightboxIndex.value = index;
    lightboxZoom.value = 1;
    lightboxOpen.value = true;
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    lightboxOpen.value = false;
    document.body.style.overflow = '';
}

function prevImage() {
    lightboxIndex.value =
        (lightboxIndex.value - 1 + galleryImages.value.length) %
        galleryImages.value.length;
    lightboxZoom.value = 1;
}

function nextImage() {
    lightboxIndex.value =
        (lightboxIndex.value + 1) % galleryImages.value.length;
    lightboxZoom.value = 1;
}

function handleZoomIn() {
    lightboxZoom.value = Math.min(lightboxZoom.value + 0.25, 3);
}

function handleZoomOut() {
    lightboxZoom.value = Math.max(lightboxZoom.value - 0.25, 0.5);
}

function handleKeydown(e: KeyboardEvent) {
    if (e.key === 'Escape') closeLightbox();
    if (e.key === 'ArrowLeft') prevImage();
    if (e.key === 'ArrowRight') nextImage();
}

function handleWheel(e: WheelEvent) {
    e.preventDefault();
    const delta = e.deltaY > 0 ? -0.1 : 0.1;
    lightboxZoom.value = Math.min(Math.max(lightboxZoom.value + delta, 0.5), 3);
}

let initialPinchDist = 0;
let initialZoom = 1;

function handleTouchStart(e: TouchEvent) {
    if (e.touches.length === 2) {
        const touch1 = e.touches[0];
        const touch2 = e.touches[1];
        const dx = touch1.clientX - touch2.clientX;
        const dy = touch1.clientY - touch2.clientY;
        initialPinchDist = Math.sqrt(dx * dx + dy * dy);
        initialZoom = lightboxZoom.value;
    }
}

function handleTouchMove(e: TouchEvent) {
    if (e.touches.length === 2) {
        const touch1 = e.touches[0];
        const touch2 = e.touches[1];
        const dx = touch1.clientX - touch2.clientX;
        const dy = touch1.clientY - touch2.clientY;
        const dist = Math.sqrt(dx * dx + dy * dy);
        const scale = dist / initialPinchDist;
        lightboxZoom.value = Math.min(Math.max(initialZoom * scale, 0.5), 3);
    }
}

// ===== Download Brochure =====
const isDownloading = ref(false);

function downloadBrochure() {
    const project = selectedProject.value;
    if (!project?.brochure_path) {
        alert('Brochure not available for this project.');
        return;
    }
    isDownloading.value = true;
    const pdfUrl = `/storage/${project.brochure_path}`;
    setTimeout(() => {
        window.open(pdfUrl, '_blank');
        isDownloading.value = false;
    }, 300);
}

// ===== Formatting Helpers =====
function formatCurrency(amount: number) {
    return new Intl.NumberFormat('en-KE', {
        style: 'currency',
        currency: 'KES',
        minimumFractionDigits: 0,
    }).format(amount);
}

// ===== Payment Tiers =====
const paymentTiers = computed(() => {
    const project = selectedProject.value;
    if (project?.payment_tiers && project.payment_tiers.length > 0) {
        return project.payment_tiers;
    }
    if (
        project?.plots &&
        project.plots.length > 0 &&
        project.plots[0].payment_tiers
    ) {
        return project.plots[0].payment_tiers;
    }
    return [
        { months: 3, price: 1500000, label: '0 - 3 months' },
        { months: 6, price: 1550000, label: '4 - 6 months' },
        { months: 9, price: 1600000, label: '7 - 9 months' },
    ];
});

const minDeposit = computed(() => selectedProject.value?.min_deposit || 300000);
const maxMonths = computed(() => selectedProject.value?.max_months || 9);

// ===== Amenities =====
const amenities = computed(() => {
    const project = selectedProject.value;
    if (project?.amenities && project.amenities.length > 0) {
        return project.amenities;
    }
    return [
        {
            title: 'Transport',
            icon: Car,
            description:
                '12km from Diani Airport and approximately an hour from SGR & Mombasa International Airport.',
        },
        {
            title: 'Healthcare',
            icon: Building,
            description:
                'Quality healthcare with premier private hospitals within a 15 km radius.',
        },
        {
            title: 'Shopping Malls',
            icon: ShoppingBag,
            description:
                'Close proximity to leading supermarket chains and shopping malls.',
        },
        {
            title: '5 Star Beach Resorts',
            icon: Hotel,
            description:
                'Near Resorts such as Villa Kalista, Neptune Palm Beach Resort, and Chale Island Resort.',
        },
        {
            title: 'Tourist Attractions',
            icon: Landmark,
            description:
                'Close to Diani Beach, Kaya Kinondo conservancy, Chale Island, and Robinson Island.',
        },
    ];
});

// ===== Feature Groups =====
const featureGroups = computed(() => {
    const project = selectedProject.value;
    if (project?.feature_groups && project.feature_groups.length > 0) {
        return project.feature_groups;
    }
    return [
        {
            title: 'Key Highlights',
            icon: MapPin,
            items: [
                '100 Meters off Highway',
                '3KM to Diani Beach',
                '12 KM to Diani Airport',
                'Secure, value-added Gated Community',
                'Ready Freehold Title Deeds',
                'Ready To build Plots',
                'Spacious 50 by 100 plots',
                'Piped water & electricity on Site',
            ],
        },
        {
            title: 'Value Addition',
            icon: Zap,
            items: [
                'Electricity available on site.',
                'Beautiful and Controlled Gate access',
                'Direct Road Access for ease of accessibility.',
                'Bush cleared and ready to build.',
                'All round perimeter fenced with poles for security',
            ],
        },
        {
            title: 'Strategic Opportunity',
            icon: TrendingUp,
            items: [
                'Located near existing and upcoming housing estates.',
                'Located just 100 metres from the existing highway.',
                "Backed by a <a href='https://www.hassconsult.com/hassindex' target='_blank' class='text-teal-600 hover:underline'>79% increase in land prices since 2020</a>.",
            ],
        },
    ];
});

const hasNoProjects = computed(() => {
    return (
        !props.agent ||
        (!props.singleProject &&
            (!props.projects || props.projects.length === 0))
    );
});

const showProjectSelector = computed(() => {
    return (
        props.hasMultipleProjects && props.projects && props.projects.length > 1
    );
});

const iconComponent = (name: string) => {
    const iconMap: Record<string, any> = {
        MapPin,
        Building,
        Car,
        Hotel,
        Landmark,
        ShoppingBag,
        TrendingUp,
        Zap,
    };
    return iconMap[name] || MapPin;
};

// ===== JSON-LD =====
onMounted(() => {
    const project = selectedProject.value;
    if (!project) return;

    const jsonLdData = {
        '@context': 'https://schema.org',
        '@type': 'Product',
        name: project.name || 'Diani Silver Court',
        description:
            project.hero_description || 'Prime plots for sale in Diani.',
        image: ogImage.value,
        offers: {
            '@type': 'AggregateOffer',
            priceCurrency: 'KES',
            lowPrice: project.starting_price || 1500000,
            highPrice:
                paymentTiers.value[paymentTiers.value.length - 1]?.price ||
                1600000,
            offerCount: project.plots?.length || 8,
            availability: 'https://schema.org/InStock',
            url: canonicalUrl.value,
        },
    };

    const script = document.createElement('script');
    script.type = 'application/ld+json';
    script.innerHTML = JSON.stringify(jsonLdData);
    document.head.appendChild(script);
    const utmSource = getUtmSource();
    const referrerSource = detectSourceFromReferrer();
    leadForm.source_platform = utmSource || referrerSource || 'direct';

    console.log('Lead source detected:', leadForm.source_platform);
});
</script>

<template>
    <Head>
        <title>
            {{
                selectedProject?.meta_title ||
                selectedProject?.name ||
                'Prime Plots for Sale in Diani, Kenya'
            }}
        </title>
        <meta
            name="description"
            :content="
                selectedProject?.meta_description ||
                selectedProject?.hero_description ||
                'Discover prime plots for sale in Diani, Kenya. Secure gated community with flexible payment plans available.'
            "
        />
        <link rel="canonical" :href="canonicalUrl" />
        <meta name="robots" content="index, follow" />
        <meta name="author" content="Adili Real Estate" />

        <meta property="og:type" content="website" />
        <meta property="og:url" :content="canonicalUrl" />
        <meta
            property="og:title"
            :content="
                selectedProject?.meta_title ||
                selectedProject?.name ||
                'Prime Plots for Sale in Diani, Kenya'
            "
        />
        <meta
            property="og:description"
            :content="
                selectedProject?.meta_description ||
                selectedProject?.hero_description ||
                'Discover prime plots for sale in Diani, Kenya.'
            "
        />
        <meta property="og:image" :content="ogImage" />
        <meta property="og:image:width" content="1200" />
        <meta property="og:image:height" content="630" />
        <meta property="og:site_name" content="Adili Real Estate" />
        <meta property="og:locale" content="en_KE" />

        <meta name="twitter:card" content="summary_large_image" />
        <meta
            name="twitter:title"
            :content="
                selectedProject?.meta_title ||
                selectedProject?.name ||
                'Prime Plots for Sale in Diani, Kenya'
            "
        />
        <meta
            name="twitter:description"
            :content="
                selectedProject?.meta_description ||
                selectedProject?.hero_description ||
                'Discover prime plots for sale in Diani, Kenya.'
            "
        />
        <meta name="twitter:image" :content="ogImage" />
        <meta name="twitter:site" content="@AdiliRealEstate" />

        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>

    <!-- No Projects State -->
    <div
        v-if="hasNoProjects"
        class="flex min-h-screen items-center justify-center bg-gray-50"
    >
        <div class="p-8 text-center">
            <h1 class="mb-4 text-2xl font-bold text-gray-700">
                No Projects Available
            </h1>
            <p class="text-gray-500">
                This agent does not have any active projects at the moment.
            </p>
        </div>
    </div>

    <!-- Main Content -->
    <div
        v-else
        class="relative w-full px-4 sm:px-6 lg:grid lg:grid-cols-[42%_58%] lg:gap-8 lg:px-8"
    >
        <!-- LEFT COLUMN -->
        <div class="py-4">
            <!-- Project Selector -->
            <div
                v-if="showProjectSelector"
                class="mb-4 rounded-xl border border-gray-200 bg-white p-4 shadow"
            >
                <label class="mb-2 block text-sm font-medium text-gray-700"
                    >Select a Project:</label
                >
                <div class="flex flex-wrap gap-2">
                    <button
                        v-for="project in projects"
                        :key="project.id"
                        @click="selectProject(project.id)"
                        :class="[
                            'rounded-lg px-4 py-2 text-sm font-medium transition',
                            selectedProjectId === project.id
                                ? 'bg-teal-600 text-white'
                                : 'bg-gray-100 text-gray-700 hover:bg-gray-200',
                        ]"
                    >
                        {{ project.name }}
                    </button>
                </div>
            </div>

            <!-- Video -->
            <div
                class="relative aspect-video w-full overflow-hidden rounded-xl shadow-xl"
            >
                <div
                    v-if="!videoUnlocked"
                    class="absolute inset-0 z-10 flex cursor-pointer flex-col items-center justify-center rounded-xl bg-black/70"
                    @click="unlockVideo"
                >
                    <button
                        class="rounded-lg bg-red-500 px-6 py-3 text-lg font-bold text-white transition hover:bg-red-600"
                    >
                        View Drone Video
                    </button>
                    <p class="mt-4 text-sm text-white/70">
                        Enter your details to unlock
                    </p>
                </div>
                <iframe
                    v-show="videoUnlocked"
                    class="h-full w-full"
                    :src="`https://www.youtube.com/embed/${selectedProject?.drone_video_id || 'aUL4d_5Gv54'}?si=${Math.random().toString(36).substring(2, 10)}`"
                    title="YouTube video player"
                    frameborder="0"
                    allow="
                        accelerometer;
                        autoplay;
                        clipboard-write;
                        encrypted-media;
                        gyroscope;
                        picture-in-picture;
                        web-share;
                    "
                    referrerpolicy="strict-origin-when-cross-origin"
                    allowfullscreen
                ></iframe>
            </div>

            <!-- Gallery -->
            <div class="mt-4 space-y-4">
                <div
                    v-for="(img, idx) in galleryImages"
                    :key="idx"
                    class="aspect-video w-full cursor-pointer overflow-hidden rounded-xl shadow-md transition hover:scale-[1.02] hover:shadow-xl"
                    @click="openLightbox(idx)"
                >
                    <img
                        :src="img"
                        :alt="`${selectedProject?.name || 'Project'} gallery image ${idx + 1}`"
                        class="h-full w-full object-cover"
                    />
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN -->
        <div class="py-6 lg:py-8">
            <main id="main" class="space-y-12 md:pr-6">
                <!-- Agent Info -->
                <div
                    v-if="agent"
                    class="mb-4 rounded-lg border border-teal-200 bg-teal-50 p-4"
                >
                    <p class="text-sm text-gray-600">
                        Brought to you by
                        <span class="font-bold text-teal-600">{{
                            agent.name
                        }}</span>
                        on behalf of
                        <span class="cursor-pointer font-bold text-teal-600"
                            >Adili Real Estate</span
                        >
                    </p>
                </div>

                <!-- Hero Text -->
                <div class="space-y-6">
                    <h1
                        class="text-3xl font-extrabold tracking-tight text-gray-800 sm:text-4xl"
                    >
                        {{ selectedProject?.name || 'Diani Silver Court' }}
                    </h1>
                    <p class="text-lg text-gray-600">
                        {{
                            selectedProject?.tagline ||
                            'Prime Plots in a Secure Gated Community'
                        }}
                    </p>
                    <p
                        v-if="selectedProject?.hero_description"
                        class="text-lg text-gray-600"
                        v-html="selectedProject.hero_description"
                    ></p>
                    <p
                        v-if="selectedProject?.hero_description_2"
                        class="text-lg text-gray-600"
                        v-html="selectedProject.hero_description_2"
                    ></p>

                    <p
                        v-if="!selectedProject?.hero_description"
                        class="text-lg text-gray-600"
                    >
                        Discover prime plots for sale in Diani at Diani Silver
                        Court, a secure gated community located just 100 metres
                        from the highway, only 3 km from the award-winning Diani
                        Beach, Kenya.
                    </p>

                    <div
                        class="flex flex-col items-stretch justify-center gap-4 pt-2 sm:flex-row"
                    >
                        <!-- Price Badge -->
                        <div
                            class="inline-flex min-h-[60px] items-center gap-4 rounded-xl bg-red-500 px-6 py-3 text-white shadow-lg"
                        >
                            <div
                                class="flex items-center gap-2 border-r border-white/30 pr-4"
                            >
                                <span class="text-sm font-medium"
                                    >Starting from</span
                                >
                            </div>
                            <div>
                                <span class="block text-xl font-bold">{{
                                    formatCurrency(
                                        selectedProject?.starting_price ||
                                            1500000,
                                    )
                                }}</span>
                            </div>
                        </div>

                        <!-- View Property Button -->
                        <button
                            @click="triggerLeadCapture('visit')"
                            class="min-w-[160px] flex-1 rounded-xl bg-teal-600 px-4 py-3 text-center font-bold text-white shadow-md transition hover:bg-teal-700"
                        >
                            Book a Site Visit
                        </button>

                        <!-- Download Brochure -->
                        <button
                            @click="downloadBrochure"
                            :disabled="
                                isDownloading || !selectedProject?.brochure_path
                            "
                            class="flex min-w-[160px] flex-1 items-center justify-center gap-2 rounded-xl border border-teal-200 px-4 py-3 text-center font-medium text-teal-600 transition hover:bg-teal-50 disabled:opacity-50"
                        >
                            <Download :size="18" class="inline-block" />
                            {{
                                isDownloading
                                    ? 'Loading...'
                                    : 'Download Brochure'
                            }}
                        </button>
                    </div>
                </div>

                <!-- Features -->
                <section>
                    <h2
                        class="mb-8 text-center text-2xl font-bold text-gray-800 md:text-3xl"
                    >
                        Why Choose
                        {{ selectedProject?.name || 'Diani Silver Court' }}
                    </h2>
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                        <div
                            v-for="(group, idx) in featureGroups"
                            :key="idx"
                            class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition hover:shadow-md"
                        >
                            <div
                                class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-50 text-red-500"
                            >
                                <component
                                    :is="iconComponent(group.icon)"
                                    :size="24"
                                />
                            </div>
                            <h3 class="mb-3 text-xl font-bold text-gray-800">
                                {{ group.title }}
                            </h3>
                            <ul class="space-y-2">
                                <li
                                    v-for="(item, i) in group.items"
                                    :key="i"
                                    class="flex items-start gap-2 text-sm text-gray-600"
                                >
                                    <Check
                                        :size="16"
                                        class="mt-0.5 flex-shrink-0 rounded-full border border-red-500 p-[1px] text-red-500"
                                    />
                                    <span v-html="item"></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </section>

                <!-- Amenities -->
                <section>
                    <h2
                        class="mb-8 text-center text-2xl font-bold text-gray-800 md:text-3xl"
                    >
                        Amenities & Attractions
                    </h2>
                    <div
                        class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-2"
                    >
                        <div
                            v-for="(amenity, idx) in amenities"
                            :key="idx"
                            class="flex items-start gap-4 rounded-xl bg-gray-50 p-5 transition hover:bg-teal-50"
                        >
                            <div
                                class="flex-shrink-0 rounded-full bg-red-50 p-2 text-red-500"
                            >
                                <component :is="amenity.icon" :size="20" />
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800">
                                    {{ amenity.title }}
                                </h4>
                                <p class="text-sm text-gray-600">
                                    {{ amenity.description }}
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Pricing Table -->
                <section>
                    <div
                        class="mb-8 overflow-hidden rounded-xl bg-teal-600 text-white shadow-xl"
                    >
                        <div class="p-6 md:p-8">
                            <p
                                class="text-center text-xl font-medium md:text-2xl"
                            >
                                Build your dream home or investment <br />
                                <span class="block font-bold">
                                    Title deed will be ready 30-60 days after
                                    making the final payment.
                                </span>
                            </p>
                        </div>
                        <div
                            class="flex flex-wrap items-center justify-center gap-4 bg-teal-700/90 px-6 py-4"
                        >
                            <button
                                @click="triggerLeadCapture('call')"
                                class="rounded-lg bg-red-500 px-8 py-3 font-bold text-white shadow transition hover:bg-red-600"
                            >
                                Get in Touch
                            </button>
                        </div>
                    </div>

                    <h3
                        class="mb-2 text-center text-2xl font-bold text-gray-800"
                    >
                        Installment Plans
                    </h3>
                    <p class="mb-4 text-center text-gray-600">
                        Minimum Deposit of
                        <span
                            class="ml-1 rounded bg-teal-600 px-3 py-1 font-bold text-white"
                            >{{ formatCurrency(minDeposit) }}</span
                        >
                    </p>

                    <div
                        class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm"
                    >
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-4 py-3 text-left font-bold text-gray-800"
                                    >
                                        Payment Plan
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left font-bold text-gray-800"
                                    >
                                        Total Price
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left font-bold text-gray-800"
                                    >
                                        Instalment
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(tier, idx) in paymentTiers"
                                    :key="idx"
                                    :class="
                                        idx === 1
                                            ? 'relative bg-teal-50'
                                            : 'border-t border-gray-100'
                                    "
                                >
                                    <td
                                        class="px-4 py-3 font-medium text-gray-800"
                                    >
                                        {{ tier.label }}
                                        <span
                                            v-if="idx === 1"
                                            class="ml-2 inline-block rounded-full bg-green-500 px-2 py-0.5 text-xs font-bold text-white"
                                            >Popular</span
                                        >
                                    </td>
                                    <td
                                        class="px-4 py-3"
                                        :class="
                                            idx === 1
                                                ? 'font-bold text-teal-600'
                                                : 'text-gray-700'
                                        "
                                    >
                                        {{ formatCurrency(tier.price) }}
                                    </td>
                                    <td
                                        class="px-4 py-3"
                                        :class="
                                            idx === 1
                                                ? 'font-bold text-teal-600'
                                                : 'text-gray-700'
                                        "
                                    >
                                        {{
                                            formatCurrency(
                                                Math.round(
                                                    (tier.price - minDeposit) /
                                                        tier.months,
                                                ),
                                            )
                                        }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div
                        class="mt-4 flex flex-wrap items-center justify-center gap-2 text-center text-sm text-gray-600"
                    >
                        <Check
                            :size="18"
                            class="rounded-full bg-red-100 p-[2px] text-red-500"
                        />
                        <span class="font-medium text-red-500"
                            >All plots come with ready individual freehold title
                            deeds.</span
                        >
                        <span class="hidden sm:inline">|</span>
                        <span class="hidden sm:inline"
                            >Secure, hassle-free ownership guaranteed.</span
                        >
                    </div>
                </section>

                <!-- Payment Calculator -->
                <div class="mt-4 pt-4">
                    <PaymentCalculator
                        :pricing-tiers="paymentTiers"
                        :min-deposit="minDeposit"
                        :max-months="maxMonths"
                        :project-name="selectedProject?.name || 'this project'"
                        @book-visit="triggerLeadCapture('visit')"
                    />
                </div>

                <div class="h-24 md:h-32"></div>
            </main>
        </div>
    </div>

    <!-- Lightbox -->
    <Teleport to="body">
        <div
            v-if="lightboxOpen"
            class="fixed inset-0 z-[100] flex items-center justify-center bg-black/90"
            @click.self="closeLightbox"
            @keydown="handleKeydown"
            @wheel.prevent="handleWheel"
            @touchstart="handleTouchStart"
            @touchmove="handleTouchMove"
            tabindex="-1"
        >
            <button
                @click="closeLightbox"
                class="absolute top-4 right-4 z-10 text-white transition hover:text-red-500"
                aria-label="Close lightbox"
            >
                <X :size="32" />
            </button>

            <div
                class="absolute bottom-6 left-1/2 z-10 flex -translate-x-1/2 gap-4 rounded-full bg-black/50 px-4 py-2 backdrop-blur-sm"
            >
                <button
                    @click="handleZoomOut"
                    class="text-white transition hover:text-green-500"
                    aria-label="Zoom out"
                >
                    <ZoomOut :size="24" />
                </button>
                <span class="text-sm font-medium text-white"
                    >{{ Math.round(lightboxZoom * 100) }}%</span
                >
                <button
                    @click="handleZoomIn"
                    class="text-white transition hover:text-green-500"
                    aria-label="Zoom in"
                >
                    <ZoomIn :size="24" />
                </button>
            </div>

            <button
                v-if="galleryImages.length > 1"
                @click="prevImage"
                class="absolute top-1/2 left-4 z-10 -translate-y-1/2 text-white transition hover:text-green-500"
                aria-label="Previous image"
            >
                <ChevronLeft :size="40" />
            </button>
            <button
                v-if="galleryImages.length > 1"
                @click="nextImage"
                class="absolute top-1/2 right-4 z-10 -translate-y-1/2 text-white transition hover:text-green-500"
                aria-label="Next image"
            >
                <ChevronRight :size="40" />
            </button>

            <div class="flex h-full w-full items-center justify-center p-8">
                <img
                    :src="galleryImages[lightboxIndex]"
                    :alt="`Gallery image ${lightboxIndex + 1}`"
                    class="max-h-[90vh] max-w-[90vw] object-contain transition-transform duration-200"
                    :style="{ transform: `scale(${lightboxZoom})` }"
                    draggable="false"
                />
            </div>

            <div
                v-if="galleryImages.length > 1"
                class="absolute bottom-20 left-1/2 -translate-x-1/2 rounded-full bg-black/50 px-3 py-1 text-sm text-white backdrop-blur-sm"
            >
                {{ lightboxIndex + 1 }} / {{ galleryImages.length }}
            </div>
        </div>
    </Teleport>

    <!-- Lead Capture Modal -->
    <Teleport to="body">
        <div
            v-if="showLeadModal"
            class="fixed inset-0 z-[100] flex items-center justify-center bg-black/70"
            @click.self="showLeadModal = false"
        >
            <div
                class="mx-4 w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl"
            >
                <div class="mb-4 flex items-start justify-between">
                    <h3 class="text-xl font-bold text-gray-800">
                        {{
                            targetAction === 'visit'
                                ? 'Book a Site Visit'
                                : targetAction === 'whatsapp'
                                  ? 'Chat on WhatsApp'
                                  : 'Call Us'
                        }}
                    </h3>
                    <button
                        @click="showLeadModal = false"
                        class="text-gray-500 hover:text-gray-700"
                    >
                        <X :size="24" />
                    </button>
                </div>

                <p class="mb-4 text-sm text-gray-600">
                    {{
                        targetAction === 'visit'
                            ? 'Enter your details to book a site visit and view the drone video.'
                            : "Enter your details and we'll connect you directly."
                    }}
                </p>

                <form @submit.prevent="submitLead">
                    <div class="mb-4">
                        <label
                            class="mb-1 block text-sm font-medium text-gray-700"
                            >Phone Number *</label
                        >
                        <input
                            v-model="leadForm.phone"
                            type="tel"
                            class="w-full text-black rounded-lg border border-gray-300 px-4 py-3 focus:border-teal-600 focus:ring-2 focus:ring-teal-200 focus:outline-none"
                            required
                            placeholder="+254 700 000 000"
                        />
                        <p
                            v-if="leadForm.errors.phone"
                            class="mt-1 text-sm text-red-500"
                        >
                            {{ leadForm.errors.phone }}
                        </p>
                    </div>

                    <div class="mb-4">
                        <label
                            class="mb-1 block text-sm font-medium text-gray-700"
                            >Name</label
                        >
                        <input
                            v-model="leadForm.name"
                            type="text"
                            class="w-full text-black rounded-lg border border-gray-300 px-4 py-3 focus:border-teal-600 focus:ring-2 focus:ring-teal-200 focus:outline-none"
                            placeholder="John Doe"
                        />
                    </div>

                    <div class="mb-4">
                        <label
                            class="mb-1 block text-sm font-medium text-gray-700"
                            >Email</label
                        >
                        <input
                            v-model="leadForm.email"
                            type="email"
                            class="w-full text-black rounded-lg border border-gray-300 px-4 py-3 focus:border-teal-600 focus:ring-2 focus:ring-teal-200 focus:outline-none"
                            placeholder="john@example.com"
                        />
                    </div>

                    <div class="mb-4">
                        <label class="flex items-center">
                            <input
                                v-model="leadForm.consent"
                                type="checkbox"
                                class="mr-2"
                                required
                            />
                            <span class="text-sm text-black"
                                >I agree to be contacted via SMS/WhatsApp</span
                            >
                        </label>
                        <p
                            v-if="leadForm.errors.consent"
                            class="mt-1 text-sm text-red-500"
                        >
                            {{ leadForm.errors.consent }}
                        </p>
                    </div>

                    <div class="flex gap-3">
                        <button
                            type="submit"
                            class="flex-1 rounded-lg bg-teal-600 px-4 py-3 font-bold text-white transition hover:bg-teal-700"
                            :disabled="leadForm.processing"
                        >
                            {{
                                leadForm.processing
                                    ? 'Submitting...'
                                    : 'Continue'
                            }}
                        </button>
                        <button
                            type="button"
                            @click="showLeadModal = false"
                            class="rounded-lg bg-gray-200 px-4 py-3 transition hover:bg-gray-300"
                        >
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Teleport>

    <!-- Sticky Action Bar -->
    <div
        class="fixed right-0 bottom-0 left-0 z-50 border-t border-gray-200 bg-white/95 px-4 py-3 shadow-lg backdrop-blur-sm md:px-8"
    >
        <div
            class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-3 overflow-hidden"
        >
            <div class="block md:hidden">
                <div>
                    <span class="text-sm text-gray-500">Starting From</span>
                </div>
                <div>
                    <span class="ml-2 text-xl font-bold text-green-600">{{
                        formatCurrency(
                            selectedProject?.starting_price || 1500000,
                        )
                    }}</span>
                </div>
            </div>
            <div class="hidden lg:block">
                <span class="text-sm text-gray-500">Starting from</span>
                <span class="ml-2 text-xl font-bold text-green-600">{{
                    formatCurrency(selectedProject?.starting_price || 1500000)
                }}</span>
            </div>

            <div
                class="flex flex-1 flex-wrap items-center justify-center gap-3 sm:justify-end"
            >
                <button
                    @click="triggerLeadCapture('call')"
                    class="flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 sm:px-6"
                >
                    <Phone :size="18" /> Call
                </button>
                <button
                    @click="triggerLeadCapture('whatsapp')"
                    class="flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-bold text-white transition hover:bg-green-700 sm:px-6"
                >
                    <MessageCircle :size="18" /> WhatsApp
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
body:has(.fixed.inset-0.z-\[100\]) {
    overflow: hidden;
}
</style>
