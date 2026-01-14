import { ColumnDef } from "@tanstack/vue-table";
import { h } from "vue";

interface Marcas {
    id: string,
    nome: string
}

export const columns: ColumnDef<Marcas>[] = [
    {
        accessorKey: 'id',
        header: 'ID'
    },
    {
        accessorKey: 'nome',
        header: 'Marca'
    }
]

