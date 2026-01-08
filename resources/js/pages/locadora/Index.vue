<script setup lang="ts">
import { Button } from "@/components/ui/button"
import { Link } from "@inertiajs/vue3";
import { login } from "@/routes";
</script>


<template>
  <div class="min-h-screen flex flex-col items-center mt-40 px-6">
    <h1 class="text-4xl font-bold mb-4 text-center">
      Locadora de Carros
    </h1>

    <p class="text-muted-foreground text-center mb-6 max-w-md">
      Alugue carros de forma rápida, simples e sem burocracia.
    </p>


    <Link :href="login()">
        <Button class="cursor-pointer">
        Entrar ou criar conta
        </Button>
    </Link>

  </div>
</template>
