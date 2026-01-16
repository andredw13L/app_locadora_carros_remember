<script setup lang="ts" generic="TData, TValue">
import type { ColumnDef, ColumnFiltersState } from '@tanstack/vue-table'
import {
  FlexRender,
  getCoreRowModel,
  useVueTable,
  getFilteredRowModel,
} from '@tanstack/vue-table'

import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'

import { Input } from '@/components/ui/input'
import { ref } from 'vue';
import { valueUpdater } from '@/lib/utils'

const props = defineProps<{
  columns: ColumnDef<TData, TValue>[]
  data: TData[]
}>()

const columnFilters = ref<ColumnFiltersState>([])

const table = useVueTable({
  get data() { return props.data },
  get columns() { return props.columns },
  getCoreRowModel: getCoreRowModel(),
  onColumnFiltersChange: updaterOrValue => valueUpdater(updaterOrValue, columnFilters),
  getFilteredRowModel: getFilteredRowModel(),
  state: {
    get columnFilters() { return columnFilters.value },
  }

})
</script>

<template>
  <div class="flex items-center py-7 ml-0.5">
    <Input class="max-w-sm" placeholder="Filtrar marcas..."
      :model-value="table.getColumn('nome')?.getFilterValue() as string"
      @update:model-value=" table.getColumn('nome')?.setFilterValue($event)" />
  </div>
  <div class="ml-0.5 border rounded-md w-full overflow-hidden">
    <Table>
      <TableHeader class="bg-muted/50">
        <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
          <TableHead v-for="header in headerGroup.headers" :key="header.id">
            <FlexRender v-if="!header.isPlaceholder" :render="header.column.columnDef.header"
              :props="header.getContext()" />
          </TableHead>
        </TableRow>
      </TableHeader>

      <TableBody>
        <template v-if="table.getRowModel().rows?.length">
          <TableRow v-for="row in table.getRowModel().rows" :key="row.id" class="hover:bg-muted/30 transition-colors">
            <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
              <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
            </TableCell>
          </TableRow>
        </template>

        <template v-else>
          <TableRow>
            <TableCell :colspan="columns.length" class="h-24 text-center">
              Nenhuma marca encontrada.
            </TableCell>
          </TableRow>
        </template>
      </TableBody>
    </Table>
  </div>
</template>
