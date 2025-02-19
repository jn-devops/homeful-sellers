<script setup>

</script>
<template>
    <div class="fixed inset-0 bg-opacity-50 flex items-center justify-center z-10 bg-black/70">
        <div class="bg-white w-full max-w-[450px] h-fit overflow-y-auto shadow-lg">
            <slot />
        </div>
    </div>
</template>