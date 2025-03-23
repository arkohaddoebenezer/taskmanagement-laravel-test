<template>
    <header>
        <div v-if="$slots.header" class="px-3 mx-auto">
            <slot name="header" />
        </div>
    </header>
    <div class="container">
        <slot />
        <cart-count />
    </div>
</template>
<script>
</script>
