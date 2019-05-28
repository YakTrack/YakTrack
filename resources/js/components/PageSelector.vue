<template>
    <div class="flex justify-center mx-4">
        <button
            class="py-2 px-4 hover:bg-grey-light text-grey-dark rounded"
            :disabled="page < 2"
            :class="page < 2 ? 'btn-disabled' : 'hover:text-white hover:shadow'"
            @click="selectPage(page - 1)"
            >
            <i class="fa fa-caret-left"></i>
        </button>
        <button class="bg-white text-grey-dark p-2" @click="selectPage(key)">
            {{ ((page - 1) * perPage) + 1 }} - {{ Math.min(page * perPage, total) }} of {{ total }}
        </button>
        <button
            class="py-2 px-4 hover:bg-grey-light text-grey-dark rounded"
            :class="isLastPage ? 'btn-disabled' : ' hover:text-white hover:shadow'"
            :disabled="isLastPage"
            @click="selectPage(parseInt(page) + 1)"
        >
            <i class="fa fa-caret-right"></i>
        </button>
    </div>
</template>

<script>
    export default {
        props: [
            'total',
            'perPage',
            'page',
            'lastPage',
            'onPageSelect',
        ],
        computed: {
            totalButtons() {
                return Math.min(10, this.total);
            },
            leftButtons() {
                return Math.min(5, this.totalButtons);
            },
            rightButtons() {
                return Math.max(0, this.totalButtons - this.leftButtons);
            },
            isLastPage() {
                return this.page * this.perPage >= this.total;
            },
        },
        methods: {
            selectPage(key) {
                this.onPageSelect(key);
            }
        }
    }
</script>