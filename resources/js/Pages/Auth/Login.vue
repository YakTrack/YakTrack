<template>
    <div class="w-full max-w-md mt-10 mx-auto px-6">
        <div class="max-w-xl">
            <div class="text-center">
                <a :href="route('home')" class="text-4xl no-underline text-gray-900">
                    <b>Y</b>ak<b>T</b>rack
                </a>
            </div>
            <div class="text-center mt-4 text-gray-700">Log in to start your session</div>
            <div class="card mt-8 bg-grey-100">
                <div class="card-body">

                    <form role="form" method="POST" action="/login" @submit.prevent="submit">
                        <div class="form-group">
                            <label for="email"> Email </label> 
                            <input type="email" class="form-control" placeholder="Email" name="email" v-model="form.email">
                        </div>
                        <div class="form-group">
                            <label for="password"> Password </label>
                            <input type="password" class="form-control" placeholder="Password" name="password" v-model="form.password">
                        </div>
                        <div class="form-group">
                            <label class="">
                                <input type="checkbox" class="form-checkbox" name="remember"> Remember Me
                            </label>
                        </div>
                        <div class="flex">
                            <div class="flex-1 pt-3">
                                <a class="text-gray-300" href="/password/reset">Forgot Your Password?</a>
                            </div>
                            <div class="flex text-right">
                                <loading-button :is-loading="sending" class="btn btn-primary flex-1" type="submit">Login</loading-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /.login-card-body -->
        </div>
        <!-- /.login-box -->
    </div>
</template>

<script>

import LoadingButton from '@/Shared/LoadingButton'
import Logo from '@/Shared/Logo'

export default {
    components: {
        LoadingButton,
        Logo,
    },
    props: {
        errors: Object,
    },
    data() {
        return {
            sending: false,
            form: {
                email: null,
                password: null,
                remember: null,
            },
        }
    },
    mounted() {
        document.title = 'Login'
    },
    methods: {
        submit() {
            this.sending = true
            this.$inertia.post(this.route('login.attempt'), {
                email: this.form.email,
                password: this.form.password,
                remember: this.form.remember,
            }).then(
                (response) => {
                    this.sending = false
                }
            )
        },
    },
}

</script>