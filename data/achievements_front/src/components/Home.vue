<template>
    <div class="container-fluid">
        <div class="row">
            <div class="room" ref="canvasWrapper">
                <h3>User achievements</h3>
            </div>
        </div>
    </div>
</template>
<script>

export default {
    name: 'page-home',
    data() {
        return {
            loading: true,
        }
    },
    mounted() {
        this.loading = false
    },
}
</script>
