<template>
    <div class="col-md-12">
        <div class="card card-container">
            <img
                id="profile-img"
                src="//ssl.gstatic.com/accounts/ui/avatar_2x.png"
                class="profile-img-card"
            />
            <Form @submit="handleSend" :validation-schema="schema" v-if="activeView==='sendCode'">
                <div class="form-group">
                    <label for="email">Email</label>
                    <Field name="email" type="email" class="form-control" v-model="email"/>
                    <ErrorMessage name="email" class="error-feedback"/>
                </div>

                <div class="form-group">
                    <button class="btn btn-primary btn-block" :disabled="loading">
            <span
                v-show="loading"
                class="spinner-border spinner-border-sm"
            ></span>
                        <span>Send code</span>
                    </button>
                </div>

                <router-link to="/login" class="nav-link">Login</router-link>

                <div class="form-group">
                    <div v-if="message" class="alert alert-danger" role="alert">
                        {{ message }}
                    </div>
                </div>
            </Form>
            <Form @submit="handleReset" :validation-schema="schemaReset" v-if="activeView==='newPassword'">
                <Field name="email" type="hidden" class="form-control" v-model="email"/>
                <div class="form-group">
                    <label for="code">Code from mail</label>
                    <Field name="code" type="text" class="form-control"/>
                    <ErrorMessage name="code" class="error-feedback"/>
                </div>
                <div class="form-group">
                    <label for="password">New password</label>
                    <Field name="password" type="password" class="form-control"/>
                    <ErrorMessage name="password" class="error-feedback"/>
                </div>

                <div class="form-group">
                    <button class="btn btn-primary btn-block" :disabled="loading">
            <span
                v-show="loading"
                class="spinner-border spinner-border-sm"
            ></span>
                        <span>Send code</span>
                    </button>
                </div>

                <router-link to="/login" class="nav-link">Login</router-link>

                <div class="form-group">
                    <div v-if="message" class="alert alert-danger" role="alert">
                        {{ message }}
                    </div>
                </div>
            </Form>
        </div>
    </div>
</template>

<script>
import { Form, Field, ErrorMessage } from 'vee-validate'
import * as yup from 'yup'

export default {
    name: 'page-forgot',
    components: {
        Form,
        Field,
        ErrorMessage,
    },
    data() {
        const schema = yup.object().shape({
            email: yup.string().required('Email is required!').email('Email is invalid!').max(50, 'Must be maximum 50 characters!'),
        })
        const schemaReset = yup.object().shape({
            code: yup.string().required('code is required!').min(1, 'Must be at least 1 characters!').max(100, 'Must be maximum 40 characters!'),
            password: yup.string().required('Password is required!').min(6, 'Must be at least 6 characters!').max(40, 'Must be maximum 40 characters!'),
        })

        return {
            loading: false,
            message: '',
            activeView: 'sendCode',
            email: '',
            schema,
            schemaReset,
        }
    },
    computed: {
        loggedIn() {
            return this.$store.state.auth.status.loggedIn
        },
    },
    created() {
        if (this.loggedIn) {
            this.$router.push('/profile')
        }
    },
    methods: {
        handleSend(user) {

            let self = this

            this.loading = true

            this.$store.dispatch('auth/forgot', user).then(
                () => {
                    self.activeView='newPassword'
                    self.loading = false
                },
                (error) => {
                    this.loading = false
                    this.message =
                        (error.response &&
                            error.response.data &&
                            error.response.data.message) ||
                        error.message ||
                        error.toString()
                },
            )
        },
        handleReset(user) {

            let self = this

            this.loading = true

            this.$store.dispatch('auth/reset', user).then(
                () => {
                    this.$router.push('/login')
                    self.loading = false
                },
                (error) => {
                    this.loading = false
                    this.message =
                        (error.response &&
                            error.response.data &&
                            error.response.data.message) ||
                        error.message ||
                        error.toString()
                },
            )
        },
    },
}
</script>

<style scoped>
label {
    display: block;
    margin-top: 10px;
}

.card-container.card {
    max-width: 350px !important;
    padding: 40px 40px;
}

.card {
    background-color: #f7f7f7;
    padding: 20px 25px 30px;
    margin: 0 auto 25px;
    margin-top: 50px;
    -moz-border-radius: 2px;
    -webkit-border-radius: 2px;
    border-radius: 2px;
    -moz-box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
    -webkit-box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
    box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
}

.profile-img-card {
    width: 96px;
    height: 96px;
    margin: 0 auto 10px;
    display: block;
    -moz-border-radius: 50%;
    -webkit-border-radius: 50%;
    border-radius: 50%;
}

.error-feedback {
    color: red;
}
</style>
