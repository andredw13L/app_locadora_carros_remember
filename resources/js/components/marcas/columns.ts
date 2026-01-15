import { ColumnDef } from '@tanstack/vue-table';
import { h } from 'vue';

interface Marcas {
    id: string;
    nome: string;
    imagem: string;
    created_at: Date,
    updated_at: Date
}

export const columns: ColumnDef<Marcas>[] = [
    {
        accessorKey: 'id',
        header: 'ID',
    },
    {
        accessorKey: 'nome',
        header: 'Marca',
    },
    {
        accessorKey: 'imagem',
        header: 'Imagem',
        cell: ({ row }) => {
            const url = row.getValue('imagem') as string;
            return h('img', 
            { 
                src: 'http://localhost:8000/storage/' + url, 
                class: 'w-10 h-10' 
            });
        },
    },

    {
        accessorKey: 'created_at',
        header: 'Data de Criação',
        cell: ({ row }) => {
            const data = new Date(row.getValue('created_at'))
            return data.toLocaleString('pt-BR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
            })
        }
    },
    {
        accessorKey: 'updated_at',
        header: 'Data de Atualização',
        cell: ({ row }) => {
            const data = new Date(row.getValue('updated_at'))
            return data.toLocaleString('pt-BR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
            })
        }
    },
];
