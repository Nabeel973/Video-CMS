<template>
    <div
        class="main-section antialiased relative font-nunito text-sm font-normal"
        :class="[store.sidebar ? 'toggle-sidebar' : '', store.menu, store.layout, store.rtlClass]"
    >
        <component v-bind:is="mainLayout"></component>
    </div>
</template>

<script lang="ts" setup>
    import { computed } from 'vue';

    import appLayout from '@/layouts/app-layout.vue';
import authLayout from '@/layouts/auth-layout.vue';

    import { useMeta } from '@/composables/use-meta';
import { useAppStore } from '@/stores/index';

    const store = useAppStore();

    // meta
    useMeta({ title: 'Sales Admin' });

    const mainLayout = computed(() => {
        return store.mainLayout === 'auth' ? authLayout : appLayout;
    });
</script>
