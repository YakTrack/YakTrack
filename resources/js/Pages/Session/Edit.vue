<template>
    <layout>
        <template slot="breadcrumbs">
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home',         url: route('home')},
                    {title: 'Sessions',     url: route('session.index')},
                    {title: (isCreateForm ? 'Create' : 'Edit') + ' Session'},
                ]"
            ></breadcrumbs>
        </template>
        <template slot="title"> Edit Session </template>
        <form @submit.prevent="submit">
            <div class="form-group">
                <label for="name"> Started At </label>
                <input type="text" name="started_at" v-model="form.started_at" class="form-control" placeholder="YYYY-MM-DD HH:MM:SS">
            </div>
            <div class="form-group">
                <label for="ended_at"> Ended At </label>
                <input type="text" name="ended_at" v-model="form.ended_at" class="form-control" placeholder="YYYY-MM-DD HH:MM:SS">
            </div>
            <div class="form-group">
                <label for="task_id"> Task </label>
                <task-select :tasks="tasks" :task="form.task_id" :on-change="selectTask"></task-select>
            </div>
            <div class="form-group">
                <label for="sprint_id"> Sprint </label>
                <sprint-select :sprints="sprints" :sprint="form.sprint_id" :on-change="selectSprint"></sprint-select>
            </div>
            <div class="form-group">
                <label for="invoice_id"> Invoice </label>
                <invoice-select :invoices="invoices" :invoice="form.invoice_id" :on-change="selectInvoice"></invoice-select>
            </div>
            <div class="form-group">
                <label for="is_billable"> Is Billable </label>
                <input type="checkbox" class="form-checkbox" name="is_billable" v-model="form.is_billable"/>
            </div>
            <div class="flex mt-4">
                <div class="flex-1 mt-2">
                    <button-link :href="route('session.index')"> Cancel </button-link>
                </div>
                <div class="flex-1 float-right">
                    <button class="btn btn-blue float-right"> {{ isCreateForm ? 'Create' : 'Update' }} </button>
                </div>
            </div>
        </form>
    </layout>
</template>

<script>

    import breadcrumbs from '@/Shared/Breadcrumbs';
    import layout from '@/Shared/Layout';
    import taskSelect from '@/Shared/TaskSelect';
    import sprintSelect from '@/Shared/SprintSelect';
    import invoiceSelect from '@/Shared/InvoiceSelect';
    import dateTime from '@/filters/DateTime';

    export default {
        props: [
            'session',
            'tasks',
            'invoices',
            'sprints', 
        ],
        components: {
            breadcrumbs: breadcrumbs,
            layout: layout,
            taskSelect: taskSelect,
            sprintSelect: sprintSelect,
            invoiceSelect: invoiceSelect,
        },
        data() {
            return {
                form: this.session ? {
                    started_at: dateTime.toDateTimeString(new Date(this.session.localStartedAt)),
                    ended_at: this.session.localEndedAt ? dateTime.toDateTimeString(new Date(this.session.localEndedAt)) : null,
                    task_id: this.session.task_id,
                    sprint_id: this.session.sprint_id,
                    invoice_id: this.session.invoice_id,
                } : {}
            };
        },
        methods: {
            selectSprint(sprintId) {
                this.form.sprint_id = sprintId;
            },
            selectInvoice(invoiceId) {
                this.form.invoice_id = invoiceId;
            },
            selectTask(taskId) {
                this.form.task_id = taskId;
            },
            submit() {
                this.$inertia[this.isCreateForm ? 'post' : 'patch'](
                    this.isCreateForm ? route('session.store') : route('session.update', this.session.id),
                    this.form
                );
            }
        },
        computed: {
            isCreateForm() {
                return this.session == null;
            },
        }
    }

</script>