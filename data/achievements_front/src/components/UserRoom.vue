<template>
    <div class="container-fluid">
        <div class="row">
            <component-room :prop-user-auth-key="userAuthKey"></component-room>
        </div>
    </div>
</template>

<script>
import ComponentRoom from '@/components/modules/Room.vue'


export default {
    name: 'user-room',
    components: {
        ComponentRoom,
    },
    props: {
        propUserAuthKey: {
            type: String,
            default: '',
        },
    },
    data() {
        return {
            userAuthKey: this.propUserAuthKey,
        }
    },
}
</script>

<style>
.body {
    padding: 0;
    margin: 0;
}
</style>