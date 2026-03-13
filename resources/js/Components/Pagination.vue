<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: Array,
});
</script>

<template>
    <div v-if="links.length > 3">
        <div class="flex flex-wrap -mb-1">
            <template v-for="(link, key) in links" :key="key">
                <div v-if="link.url === null" 
                     class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-400 border rounded-xl dark:border-gray-700"
                     v-html="link.label" />
                <Link v-else
                      class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded-xl hover:bg-white focus:border-blue-500 focus:text-blue-500 transition-all dark:border-gray-700 dark:hover:bg-gray-700"
                      :class="{ 'bg-blue-600 text-white hover:bg-blue-700 font-bold': link.active, 'dark:text-gray-300': !link.active }"
                      :href="link.url"
                      v-html="link.label"
                      preserve-scroll
                />
            </template>
        </div>
    </div>
</template>
