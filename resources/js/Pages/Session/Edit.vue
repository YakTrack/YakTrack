<template>
    <layout>
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
                <task-select :tasks="tasks" :task="session.task_id" :on-change="selectTask"></task-select>
            </div>
            <div class="form-group">
                <label for="sprint_id"> Sprint </label>
                <sprint-select :sprints="sprints" :sprint="session.sprint_id" :on-change="selectSprint"></sprint-select>
            </div>
            <div class="form-group">
                <label for="invoice_id"> Invoice </label>
                <invoice-select :invoices="invoices" :invoice="session.invoice_id" :on-change="selectInvoice"></invoice-select>
            </div>
            <div class="flex mt-4">
                <div class="flex-1 mt-2">
                    <inertia-link :href="route('session.index')" class="btn btn-default"> Cancel </inertia-link>
                </div>
                <div class="flex-1 float-right">
                    <button class="btn btn-blue float-right"> Update </button>
                </div>
            </div>
        </form>
    </layout>
</template>

<script>

    import layout from '@/Shared/Layout';
    import taskSelect from '@/components/TaskSelect';
    import sprintSelect from '@/components/SprintSelect';
    import invoiceSelect from '@/components/InvoiceSelect';
    import dateTime from '@/filters/DateTime';

    export default {
        props: [
            'session',
            'tasks',
            'invoices',
            'sprints', 
        ],
        components: {
            layout: layout,
            taskSelect: taskSelect,
            sprintSelect: sprintSelect,
            invoiceSelect: invoiceSelect,
        },
        data() {
            return {
                form: {
                    started_at: dateTime.toDateTimeString(new Date(this.session.localStartedAt.date)),
                    ended_at: this.session.localEndedAt ? dateTime.toDateTimeString(new Date(this.session.localEndedAt.date)) : null,
                    task_id: this.session.task_id,
                    sprint_id: this.session.sprint_id,
                    invoice_id: this.session.invoice_id,
                }
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
                this.$inertia.put(route('session.update', this.session.id), this.form);
            }
        }
    }

</script>