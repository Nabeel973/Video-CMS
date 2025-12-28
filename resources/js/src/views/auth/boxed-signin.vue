<template>
    <div class="relative min-h-screen overflow-hidden">
        <!-- Animated gradient background -->
        <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900">
            <div class="absolute inset-0 bg-[url('/assets/images/auth/map.png')] opacity-10 bg-cover bg-center"></div>
            <!-- Animated gradient orbs -->
            <div class="absolute top-0 -left-4 w-72 h-72 bg-purple-500 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob"></div>
            <div class="absolute top-0 -right-4 w-72 h-72 bg-cyan-500 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-2000"></div>
            <div class="absolute -bottom-8 left-20 w-72 h-72 bg-pink-500 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-4000"></div>
        </div>

        <!-- Video-themed decorative elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <!-- Film strip effect -->
            <div class="absolute top-0 left-0 w-full h-2 bg-black/30" style="background-image: repeating-linear-gradient(90deg, transparent, transparent 20px, rgba(255,255,255,0.1) 20px, rgba(255,255,255,0.1) 40px);"></div>
            <div class="absolute bottom-0 left-0 w-full h-2 bg-black/30" style="background-image: repeating-linear-gradient(90deg, transparent, transparent 20px, rgba(255,255,255,0.1) 20px, rgba(255,255,255,0.1) 40px);"></div>
            
            <!-- Video play icon decoration -->
            <div class="absolute top-20 right-20 opacity-5">
                <svg width="200" height="200" viewBox="0 0 24 24" fill="none" class="text-white">
                    <path d="M8 5v14l11-7z" fill="currentColor" />
                </svg>
            </div>
            <div class="absolute bottom-20 left-20 opacity-5">
                <svg width="150" height="150" viewBox="0 0 24 24" fill="none" class="text-white">
                    <path d="M8 5v14l11-7z" fill="currentColor" />
                </svg>
            </div>
        </div>

        <div class="relative flex min-h-screen items-center justify-center px-6 py-10 sm:px-16">
            <div class="relative w-full max-w-[480px]">
                <!-- Language selector -->
                <div class="absolute top-0 end-0 z-10">
                    <div class="dropdown">
                        <Popper :placement="store.rtlClass === 'rtl' ? 'bottom-start' : 'bottom-end'" offsetDistance="8">
                            <button
                                type="button"
                                class="flex items-center gap-2.5 rounded-lg border border-white/20 bg-white/10 backdrop-blur-md px-3 py-2 text-white hover:bg-white/20 transition-all"
                            >
                                <div>
                                    <img :src="currentFlag" alt="image" class="h-5 w-5 rounded-full object-cover" />
                                </div>
                                <div class="text-sm font-semibold uppercase">{{ store.locale }}</div>
                                <span class="shrink-0">
                                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M6.99989 9.79988C6.59156 9.79988 6.18322 9.64238 5.87406 9.33321L2.07072 5.52988C1.90156 5.36071 1.90156 5.08071 2.07072 4.91154C2.23989 4.74238 2.51989 4.74238 2.68906 4.91154L6.49239 8.71488C6.77239 8.99488 7.22739 8.99488 7.50739 8.71488L11.3107 4.91154C11.4799 4.74238 11.7599 4.74238 11.9291 4.91154C12.0982 5.08071 12.0982 5.36071 11.9291 5.52988L8.12572 9.33321C7.81656 9.64238 7.40822 9.79988 6.99989 9.79988Z"
                                            fill="currentColor"
                                        />
                                    </svg>
                                </span>
                            </button>
                            <template #content="{ close }">
                                <ul class="!px-2 text-dark dark:text-white-dark grid grid-cols-2 gap-2 font-semibold dark:text-white-light/90 w-[280px]">
                                    <template v-for="item in store.languageList" :key="item.code">
                                        <li>
                                            <button
                                                type="button"
                                                class="w-full hover:text-primary"
                                                :class="{ 'bg-primary/10 text-primary': i18n.locale === item.code }"
                                                @click="changeLanguage(item), close()"
                                            >
                                                <img
                                                    class="w-5 h-5 object-cover rounded-full"
                                                    :src="`/assets/images/flags/${item.code.toUpperCase()}.svg`"
                                                    alt=""
                                                />
                                                <span class="ltr:ml-3 rtl:mr-3">{{ item.name }}</span>
                                            </button>
                                        </li>
                                    </template>
                                </ul>
                            </template>
                        </Popper>
                    </div>
                </div>

                <!-- Main card -->
                <div class="relative rounded-2xl bg-white/10 backdrop-blur-xl border border-white/20 shadow-2xl p-8 md:p-10">
                    <!-- Logo/Branding section -->
                    <div class="text-center mb-8">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-purple-500 to-cyan-500 mb-4 shadow-lg">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" class="text-white">
                                <path d="M8 5v14l11-7z" fill="currentColor" />
                            </svg>
                        </div>
                        <h1 class="text-4xl font-bold text-white mb-2">VistroVideo</h1>
                        <p class="text-white/70 text-sm">Video Content Management System</p>
                    </div>

                    <!-- Welcome section -->
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-white mb-2">Welcome Back</h2>
                        <p class="text-white/60 text-sm">Sign in to manage your video content</p>
                    </div>
                    <!-- Form -->
                    <form class="space-y-6" @submit.prevent="handleSubmit">
                        <div v-if="error" class="p-4 rounded-lg bg-red-500/20 border border-red-500/50 text-red-200 text-sm backdrop-blur-sm">
                            {{ error }}
                        </div>
                        
                        <!-- Email field -->
                        <div>
                            <label for="Email" class="block text-sm font-medium text-white/90 mb-2">Email Address</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" class="text-white/50">
                                        <path
                                            opacity="0.5"
                                            d="M10.65 2.25H7.35C4.23873 2.25 2.6831 2.25 1.71655 3.23851C0.75 4.22703 0.75 5.81802 0.75 9C0.75 12.182 0.75 13.773 1.71655 14.7615C2.6831 15.75 4.23873 15.75 7.35 15.75H10.65C13.7613 15.75 15.3169 15.75 16.2835 14.7615C17.25 13.773 17.25 12.182 17.25 9C17.25 5.81802 17.25 4.22703 16.2835 3.23851C15.3169 2.25 13.7613 2.25 10.65 2.25Z"
                                            fill="currentColor"
                                        />
                                        <path
                                            d="M14.3465 6.02574C14.609 5.80698 14.6445 5.41681 14.4257 5.15429C14.207 4.89177 13.8168 4.8563 13.5543 5.07507L11.7732 6.55931C11.0035 7.20072 10.4691 7.6446 10.018 7.93476C9.58125 8.21564 9.28509 8.30993 9.00041 8.30993C8.71572 8.30993 8.41956 8.21564 7.98284 7.93476C7.53168 7.6446 6.9973 7.20072 6.22761 6.55931L4.44652 5.07507C4.184 4.8563 3.79384 4.89177 3.57507 5.15429C3.3563 5.41681 3.39177 5.80698 3.65429 6.02574L5.4664 7.53583C6.19764 8.14522 6.79033 8.63914 7.31343 8.97558C7.85834 9.32604 8.38902 9.54743 9.00041 9.54743C9.6118 9.54743 10.1425 9.32604 10.6874 8.97558C11.2105 8.63914 11.8032 8.14522 12.5344 7.53582L14.3465 6.02574Z"
                                            fill="currentColor"
                                        />
                                    </svg>
                                </div>
                                <input 
                                    id="Email" 
                                    v-model="form.email"
                                    type="email" 
                                    placeholder="you@example.com" 
                                    class="w-full pl-12 pr-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500/50 transition-all backdrop-blur-sm"
                                    :class="{ 'border-red-500/50 focus:ring-red-500/50': formErrors.email }"
                                    required
                                />
                            </div>
                            <div v-if="formErrors.email" class="mt-2 text-sm text-red-300">
                                {{ formErrors.email }}
                            </div>
                        </div>

                        <!-- Password field -->
                        <div>
                            <label for="Password" class="block text-sm font-medium text-white/90 mb-2">Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" class="text-white/50">
                                        <path
                                            opacity="0.5"
                                            d="M1.5 12C1.5 9.87868 1.5 8.81802 2.15901 8.15901C2.81802 7.5 3.87868 7.5 6 7.5H12C14.1213 7.5 15.182 7.5 15.841 8.15901C16.5 8.81802 16.5 9.87868 16.5 12C16.5 14.1213 16.5 15.182 15.841 15.841C15.182 16.5 14.1213 16.5 12 16.5H6C3.87868 16.5 2.81802 16.5 2.15901 15.841C1.5 15.182 1.5 14.1213 1.5 12Z"
                                            fill="currentColor"
                                        />
                                        <path
                                            d="M6 12.75C6.41421 12.75 6.75 12.4142 6.75 12C6.75 11.5858 6.41421 11.25 6 11.25C5.58579 11.25 5.25 11.5858 5.25 12C5.25 12.4142 5.58579 12.75 6 12.75Z"
                                            fill="currentColor"
                                        />
                                        <path
                                            d="M9 12.75C9.41421 12.75 9.75 12.4142 9.75 12C9.75 11.5858 9.41421 11.25 9 11.25C8.58579 11.25 8.25 11.5858 8.25 12C8.25 12.4142 8.58579 12.75 9 12.75Z"
                                            fill="currentColor"
                                        />
                                        <path
                                            d="M12.75 12C12.75 12.4142 12.4142 12.75 12 12.75C11.5858 12.75 11.25 12.4142 11.25 12C11.25 11.5858 11.5858 11.25 12 11.25C12.4142 11.25 12.75 11.5858 12.75 12Z"
                                            fill="currentColor"
                                        />
                                        <path
                                            d="M5.0625 6C5.0625 3.82538 6.82538 2.0625 9 2.0625C11.1746 2.0625 12.9375 3.82538 12.9375 6V7.50268C13.363 7.50665 13.7351 7.51651 14.0625 7.54096V6C14.0625 3.20406 11.7959 0.9375 9 0.9375C6.20406 0.9375 3.9375 3.20406 3.9375 6V7.54096C4.26488 7.51651 4.63698 7.50665 5.0625 7.50268V6Z"
                                            fill="currentColor"
                                        />
                                    </svg>
                                </div>
                                <input 
                                    id="Password" 
                                    v-model="form.password"
                                    type="password" 
                                    placeholder="Enter your password" 
                                    class="w-full pl-12 pr-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder:text-white/40 focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500/50 transition-all backdrop-blur-sm"
                                    :class="{ 'border-red-500/50 focus:ring-red-500/50': formErrors.password }"
                                    required
                                />
                            </div>
                            <div v-if="formErrors.password" class="mt-2 text-sm text-red-300">
                                {{ formErrors.password }}
                            </div>
                        </div>

                        <!-- Remember me -->
                        <div class="flex items-center justify-between">
                            <label class="flex cursor-pointer items-center">
                                <input type="checkbox" class="form-checkbox rounded border-white/30 bg-white/10 text-purple-500 focus:ring-purple-500" />
                                <span class="text-white/70 text-sm ml-2">Remember me</span>
                            </label>
                            <a href="#" class="text-sm text-purple-300 hover:text-purple-200 transition-colors">Forgot password?</a>
                        </div>

                        <!-- Submit button -->
                        <button 
                            type="submit" 
                            class="w-full py-3 px-4 bg-gradient-to-r from-purple-600 to-cyan-600 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl hover:from-purple-500 hover:to-cyan-500 transition-all duration-300 transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                            :disabled="loading"
                        >
                            <span v-if="loading" class="flex items-center justify-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Signing in...
                            </span>
                            <span v-else>Sign In</span>
                        </button>
                    </form>

                    <!-- Divider -->
                    <div class="relative my-8">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-white/20"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-transparent text-white/50">Or continue with</span>
                        </div>
                    </div>

                    <!-- Social login buttons -->
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <button class="flex items-center justify-center gap-2 px-4 py-2.5 bg-white/10 border border-white/20 rounded-lg text-white/80 hover:bg-white/20 transition-all backdrop-blur-sm">
                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                                <path d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.874 2.684-6.616z" fill="#4285F4"/>
                                <path d="M9 18c2.43 0 4.467-.806 5.96-2.184l-2.908-2.258c-.806.54-1.837.86-3.052.86-2.347 0-4.33-1.585-5.04-3.716H.957v2.332C2.438 15.983 5.482 18 9 18z" fill="#34A853"/>
                                <path d="M3.96 10.702c-.18-.54-.282-1.117-.282-1.702s.102-1.162.282-1.702V4.966H.957C.348 6.175 0 7.55 0 9s.348 2.825.957 4.034l3.003-2.332z" fill="#FBBC05"/>
                                <path d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0 5.482 0 2.438 2.017.957 4.966L3.96 7.298C4.67 5.163 6.653 3.58 9 3.58z" fill="#EA4335"/>
                            </svg>
                            <span class="text-sm font-medium">Google</span>
                        </button>
                        <button class="flex items-center justify-center gap-2 px-4 py-2.5 bg-white/10 border border-white/20 rounded-lg text-white/80 hover:bg-white/20 transition-all backdrop-blur-sm">
                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                                <path d="M9 0C4.03 0 0 4.03 0 9c0 4.97 4.03 9 9 9s9-4.03 9-9c0-4.97-4.03-9-9-9zm5.25 6.75h-2.25v2.25h2.25V6.75zM9 13.5c-2.48 0-4.5-2.02-4.5-4.5S6.52 4.5 9 4.5s4.5 2.02 4.5 4.5-2.02 4.5-4.5 4.5z" fill="currentColor"/>
                            </svg>
                            <span class="text-sm font-medium">GitHub</span>
                        </button>
                    </div>

                    <!-- Sign up link -->
                    <div class="text-center text-white/70 text-sm">
                        Don't have an account?
                        <router-link to="/auth/boxed-signup" class="text-purple-300 hover:text-purple-200 font-semibold transition-colors ml-1">
                            Sign up
                        </router-link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script lang="ts" setup>
    import appSetting from '@/app-setting';
import { useMeta } from '@/composables/use-meta';
import { useAuthStore } from '@/stores/auth';
import { useAppStore } from '@/stores/index';
import { computed, reactive, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { useRouter } from 'vue-router';

    useMeta({ title: 'Login Boxed' });
    const router = useRouter();
    const authStore = useAuthStore();

    const form = reactive({
        email: '',
        password: '',
    });

    const formErrors = reactive({
        email: '',
        password: '',
    });

    const error = ref('');
    const loading = ref(false);

    const validateForm = () => {
        let isValid = true;
        
        // Reset errors
        formErrors.email = '';
        formErrors.password = '';
        
        // Email validation
        if (!form.email) {
            formErrors.email = 'Email is required';
            isValid = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
            formErrors.email = 'Please enter a valid email address';
            isValid = false;
        }
        
        // Password validation
        if (!form.password) {
            formErrors.password = 'Password is required';
            isValid = false;
        } else if (form.password.length < 8) {
            formErrors.password = 'Password must be at least 8 characters';
            isValid = false;
        }
        
        return isValid;
    };

    const handleSubmit = async () => {
        // Clear previous errors
        error.value = '';
        authStore.clearErrors();
        
        // Validate form
        if (!validateForm()) {
            return;
        }
        
        loading.value = true;

        const result = await authStore.login(form.email, form.password);
        
        if (result.success) {
            router.push('/dashboard');
        } else {
            error.value = result.error;
            
            // Handle validation errors from the server
            if (result.validationErrors) {
                if (result.validationErrors.email) {
                    formErrors.email = result.validationErrors.email[0];
                }
                if (result.validationErrors.password) {
                    formErrors.password = result.validationErrors.password[0];
                }
            }
        }

        loading.value = false;
    };

    const store = useAppStore();
    // multi language
    const i18n = reactive(useI18n());
    const changeLanguage = (item: any) => {
        i18n.locale = item.code;
        appSetting.toggleLanguage(item);
    };
    const currentFlag = computed(() => {
        return `/assets/images/flags/${i18n.locale.toUpperCase()}.svg`;
    });
</script>

<style scoped>
@keyframes blob {
    0% {
        transform: translate(0px, 0px) scale(1);
    }
    33% {
        transform: translate(30px, -50px) scale(1.1);
    }
    66% {
        transform: translate(-20px, 20px) scale(0.9);
    }
    100% {
        transform: translate(0px, 0px) scale(1);
    }
}

.animate-blob {
    animation: blob 7s infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}
</style>
