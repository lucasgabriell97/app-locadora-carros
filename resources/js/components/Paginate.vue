<template>
    <nav aria-label="Page navigation example">
        <ul class="pagination d-flex flex-direction-column align-items-center justify-content-between">
            <slot></slot>
        </ul>
    </nav>
</template>

<script>
export default {};
</script>

<style scoped>
    nav {
        height: 2.29rem;
    }

    nav ul {
        cursor: pointer;
    }
</style>