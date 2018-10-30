<template>
    <div>
        <multi-select v-model="selectedInvoice" label="number" :options="invoices"></multi-select>
        <input type="hidden" name="invoice_id" :value="invoiceId">
    </div>
</template>

<script>

    import multiSelect from 'vue-multiselect';

    export default {
        props: [
            'invoices',
            'invoice'
        ],
        components: {
            multiSelect: multiSelect,
        },
        data() {
            var _this = this;

            return {
                selectedInvoice: this.invoices.find(function (invoice) {
                    return invoice.id == _this.invoice;
                }),
            }
        },
        computed: {
            invoiceId() {
                if (this.selectedInvoice == null) {
                    return null;
                }

                return this.selectedInvoice.id;
            }
        }
    }

</script>