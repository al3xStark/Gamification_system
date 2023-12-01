<template>
    <div class="container-fluid">
        <div class="row user-room-iframe" v-if="iframeUrl">
            <iframe :src="iframeUrl"></iframe>
        </div>
    </div>
</template>

<style lang="scss">
@import "@/assets/styles/user-room.scss";
</style>

<script>
import UserService from '@/services/user.service'

export default {
    name: 'board-user',
    props: {
        propUserName: {
            type: String,
            default: '',
            required: false,
        },
    },
    data() {
        return {
            iframeUrl: '',
            userName: this.propUserName,
        }
    },
    mounted() {
        UserService.getUserBoard(this.userName).then(
            (response) => {
                this.iframeUrl = response.data
            },
            (error) => {
                this.content =
                    (error.response &&
                        error.response.data &&
                        error.response.data.message) ||
                    error.message ||
                    error.toString()
            },
        )
    },
    created() {
        console.log(this.userName)
    },
}
</script>
