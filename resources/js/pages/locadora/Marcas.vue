<script setup lang="ts">
import { marcas } from '@/routes';
import { BreadcrumbItem } from '@/types';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import DataTable from '@/components/marcas/DataTable.vue';
import { columns } from '@/components/marcas/columns';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Marcas',
        href: marcas().url,
    },
];

interface Props {
  marcas: {
    id: string
    nome: string,
    imagem: string,
    created_at: Date,
    updated_at: Date
  }[]
}

const props = defineProps<Props>()

</script>


<template>
    <div>

        <Head title="Marcas"></Head>
        <AppLayout :breadcrumbs="breadcrumbs">
           <DataTable :columns="columns" :data="props.marcas"></DataTable>
        </AppLayout>
    </div>
</template>
