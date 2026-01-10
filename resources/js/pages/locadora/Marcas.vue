<script setup lang="ts">
import { marcas } from '@/routes';
import { BreadcrumbItem } from '@/types';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Marcas',
        href: marcas().url,
    },
];

</script>


<template>
    <div>

        <Head title="Marcas"></Head>
        <AppLayout :breadcrumbs="breadcrumbs">
            <h1>Marcas</h1>
        </AppLayout>
    </div>
</template>
