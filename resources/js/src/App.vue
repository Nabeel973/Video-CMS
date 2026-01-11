<template>
    <div
        class="main-section antialiased relative font-nunito text-sm font-normal"
        :class="[
            store.sidebar ? 'toggle-sidebar' : '', 
            store.menu, 
            store.layout, 
            store.rtlClass,
            { 'cinema-mode': store.mainLayout === 'cinema' }
        ]"
    >
        <component v-bind:is="mainLayout"></component>
    </div>
</template>

<script lang="ts" setup>
    import { computed } from 'vue';

    import appLayout from '@/layouts/app-layout.vue';
    import authLayout from '@/layouts/auth-layout.vue';
    import cinemaLayout from '@/layouts/cinema-layout.vue';

    import { useMeta } from '@/composables/use-meta';
    import { useAppStore } from '@/stores/index';

    const store = useAppStore();

    // meta
    useMeta({ title: 'VistroVideo' });

    const mainLayout = computed(() => {
        switch (store.mainLayout) {
            case 'auth':
                return authLayout;
            case 'cinema':
                return cinemaLayout;
            default:
                return appLayout;
        }
    });
</script>
