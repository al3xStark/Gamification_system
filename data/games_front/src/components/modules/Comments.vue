<template>
    <div class="comments">
        <div class="comments-list">
            <div v-if="!issetComments">Comments not added</div>
            <div v-for="commentItem in comments" :key="commentItem">
                <div class="username">{{commentItem.user.name}}</div>
                <div class="comment-text">{{commentItem.comment}}</div>
            </div>
        </div>
        <div class="comments-input">
            <textarea  maxlength='255' v-model="comment" placeholder="add message"></textarea>
            <button class="btn btn-primary m-auto" name="set" @click.prevent="sendComment()">Send</button>
        </div>
    </div>
</template>

<style lang="scss">
@import "@/assets/styles/comments.scss";
</style>

<script>
import GamesService from '@/services/game.service'

export default {
    name: 'module-comments',
    props: {
        propGameId: {
            type: Number,
            required: true,
        },
    },
    data() {
        return {
            comments: [],
            comment: null,
            gameId: this.propGameId,
        };
    },
    computed: {
        issetComments() {
            return this.comments.length
        }
    },
    methods: {
        sendComment() {
            let self = this
            if (this.comment != null && this.comment.length) {
                GamesService.setComment(this.gameId, this.comment).then(
                    () => {
                        self.getComments()
                        self.comment = null
                    },
                    (error) => {
                        console.log(error)
                    },
                )
            }
        },
        getComments() {
            let self = this
            GamesService.comments(this.gameId).then(
                (response) => {
                    self.comments = response.data
                },
                (error) => {
                    console.log(error)
                },
            )
        }
    },
    created() {
        this.getComments()
    }
};
</script>


<style lang="scss">
@import "@/assets/styles/modules/viewer.scss";
</style>