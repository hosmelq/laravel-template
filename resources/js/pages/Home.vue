<template>
  <div class="flex min-h-screen items-center justify-center">
    <h1 class="text-3xl font-bold underline">Home - {{ now }}</h1>
  </div>
</template>

<script lang="ts" setup>
interface HomeProps {
  now: string
}

defineProps<HomeProps>()
</script>
