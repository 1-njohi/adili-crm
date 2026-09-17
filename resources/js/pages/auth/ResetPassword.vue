<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { update } from '@/routes/password';

defineOptions({
    layout: {
        title: 'Reset password',
        description: 'Please enter your new password below',
    },
});

const props = defineProps<{
    token: string;
    email: string;
    passwordRules: string;
}>();

const inputEmail = ref(props.email);
</script>

<template>
    <Head title="Reset Password - Adili Real Estate" />

    <div class="min-h-screen flex flex-col items-center justify-center bg-gray-50 px-4 py-12">
        <!-- Branding -->
        <div class="mb-8 text-center">
            <img
                src="/logo.png"
                alt="Adili Real Estate"
                class="h-20 w-auto mx-auto mb-4"
            />
            <p class="text-sm text-gray-500 italic">Defined by Trust</p>
        </div>

        <!-- Card -->
        <div class="w-full max-w-md bg-white rounded-2xl shadow-lg ring-1 ring-gray-200 p-6 sm:p-8">
            <!-- Header -->
            <div class="mb-6 text-center">
                <h2 class="text-xl font-bold text-gray-800">Set a new password</h2>
                <p class="mt-1 text-sm text-gray-500">
                    Choose a strong password to keep your account secure.
                </p>
            </div>

            <Form
                v-bind="update.form()"
                :transform="(data) => ({ ...data, token, email })"
                :reset-on-success="['password', 'password_confirmation']"
                v-slot="{ errors, processing }"
            >
                <div class="grid gap-5">
                    <!-- Email (readonly) -->
                    <div class="grid gap-2">
                        <Label for="email" class="text-sm font-medium text-gray-700">
                            Email
                        </Label>
                        <Input
                            id="email"
                            type="email"
                            name="email"
                            autocomplete="email"
                            v-model="inputEmail"
                            class="w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-3 text-gray-600 cursor-not-allowed"
                            readonly
                        />
                        <InputError :message="errors.email" />
                    </div>

                    <!-- New Password -->
                    <div class="grid gap-2">
                        <Label for="password" class="text-sm font-medium text-gray-700">
                            New Password
                        </Label>
                        <PasswordInput
                            id="password"
                            name="password"
                            autocomplete="new-password"
                            autofocus
                            placeholder="Enter new password"
                            :passwordrules="passwordRules"
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-800 placeholder-gray-400 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200"
                        />
                        <InputError :message="errors.password" />
                    </div>

                    <!-- Confirm Password -->
                    <div class="grid gap-2">
                        <Label for="password_confirmation" class="text-sm font-medium text-gray-700">
                            Confirm Password
                        </Label>
                        <PasswordInput
                            id="password_confirmation"
                            name="password_confirmation"
                            autocomplete="new-password"
                            placeholder="Re-enter password"
                            :passwordrules="passwordRules"
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-800 placeholder-gray-400 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200"
                        />
                        <InputError :message="errors.password_confirmation" />
                    </div>

                    <Button
                        type="submit"
                        class="mt-2 w-full rounded-lg bg-sky-500 px-4 py-3 text-base font-bold text-white shadow-md transition hover:bg-sky-600 disabled:opacity-50"
                        :disabled="processing"
                        data-test="reset-password-button"
                    >
                        <Spinner v-if="processing" class="mr-2" />
                        {{ processing ? 'Resetting...' : 'Reset Password' }}
                    </Button>
                </div>
            </Form>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center">
            <p class="text-xs text-gray-400">
                &copy; {{ new Date().getFullYear() }} Adili Real Estate. All rights reserved.
            </p>
        </div>
    </div>
</template>