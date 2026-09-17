<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    BadgeHelp,
    Facebook,
    Home,
    Instagram,
    LayoutGrid,
    Mail,
    Map,
    MessageCircle,
    Phone,
    Twitter,
} from 'lucide-vue-next';
import { Button } from './ui/button';
</script>

<template>
    <footer class="mt-12 bg-primary text-white">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                <!-- About -->
                <div>
                    <h2 class="mb-4 text-center font-bold uppercase md:text-left">About Us</h2>
                    <p class="text-sm leading-relaxed text-white/90">
                        Adili Real Estate is a leading and trusted real estate company based in Diani. 
                        We are a team of highly qualified professionals with years of experience. 
                        Our mission is to empower individuals to own a genuine slice of the coast’s paradise 
                        through a simple, transparent, and hassle‑free journey.
                    </p>
                    <Link href="https://adilirealestate.com/about" target="_blank">
                        <Button class="mt-3 bg-red-500 text-white hover:bg-red-600">Learn More</Button>
                    </Link>
                </div>

                <!-- Quick Links -->
                <div>
                    <h2 class="mb-4 text-center font-bold uppercase md:text-left">Quick Links</h2>
                    <ul class="space-y-2 text-sm">
                        <li>
                            <a href="https://adilirealestate.com/FAQ" target="_blank" class="flex items-center rounded px-3 py-1 hover:bg-red-500 hover:text-black">
                                <BadgeHelp class="mr-3 h-4 w-4" /> Frequently Asked Questions
                            </a>
                        </li>
                        <li>
                            <a href="https://adilirealestate.com/properties" target="_blank" class="flex items-center rounded px-3 py-1 hover:bg-red-500 hover:text-black">
                                <Map class="mr-3 h-4 w-4" /> Plots For Sale in Diani
                            </a>
                        </li>
                        <li>
                            <a href="https://adilirealestate.com/how-to-buy" target="_blank" class="flex items-center rounded px-3 py-1 hover:bg-red-500 hover:text-black">
                                <LayoutGrid class="mr-3 h-4 w-4" /> How to Buy
                            </a>
                        </li>
                        <li>
                            <a href="https://wa.me/+254720244744?text=Hello%20there.%20I%20am%20interested%20in%20visiting%20the%20site." class="flex items-center rounded px-3 py-1 hover:bg-red-500 hover:text-black">
                                <LayoutGrid class="mr-3 h-4 w-4" /> Site Visit Booking
                            </a>
                        </li>
                        <li>
                            <a href="https://adilirealestate.com/Blogs"  target="_blank" class="flex items-center rounded px-3 py-1 hover:bg-red-500 hover:text-black">
                                <LayoutGrid class="mr-3 h-4 w-4" /> Blog & Tips
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Contact & Social -->
                <div>
                    <h2 class="mb-4 text-center font-bold uppercase md:text-left">Our Contacts</h2>
                    <ul class="space-y-2 text-sm">
                        <li class="flex items-center">
                            <Home class="mr-3 h-4 w-4 flex-shrink-0" />
                            <span>Diani Plaza along Diani Beach Road, Ground Floor.</span>
                        </li>
                        <li>
                            <a href="tel:+254720244744" class="flex items-center hover:text-red-500">
                                <Phone class="mr-3 h-4 w-4" /> 0720 244 744
                            </a>
                        </li>
                        <li>
                            <a href="https://wa.me/+254720244744" target="_blank" class="flex items-center hover:text-red-500">
                                <MessageCircle class="mr-3 h-4 w-4" /> 0720 244 744 (WhatsApp)
                            </a>
                        </li>
                        <li>
                            <a href="mailto:info@adilirealestate.com" class="flex items-center hover:text-red-500">
                                <Mail class="mr-3 h-4 w-4" /> info@adilirealestate.com
                            </a>
                        </li>
                    </ul>

                    <h2 class="mb-2 mt-4 text-center font-bold uppercase md:text-left">Follow Us</h2>
                    <div class="flex gap-3">
                        <a href="https://www.facebook.com/AdiliRealEstate" target="_blank" class="rounded-full bg-white/10 p-2 transition hover:bg-red-500">
                            <Facebook class="h-5 w-5" />
                        </a>
                        <a href="https://twitter.com/AdiliRealEstate" target="_blank" class="rounded-full bg-white/10 p-2 transition hover:bg-red-500">
                            <Twitter class="h-5 w-5" />
                        </a>
                        <a href="https://www.instagram.com/adilirealestate" target="_blank" class="rounded-full bg-white/10 p-2 transition hover:bg-red-500">
                            <Instagram class="h-5 w-5" />
                        </a>
                    </div>
                </div>
            </div>

            <!-- Copyright -->
            <div class="mt-8 border-t border-white/20 pt-4 text-center text-xs text-white/70">
                All rights reserved | &copy; 2026 Adili Real Estate
            </div>
        </div>
    </footer>
</template>