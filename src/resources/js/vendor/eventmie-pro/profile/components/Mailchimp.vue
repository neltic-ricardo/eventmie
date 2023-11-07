<template>
    <div>
        <div id="Mintegration" class="tab-pane fade in">
            <div class="panel-group">
                <div class="panel panel-default lgx-panel">
                    <div class="panel-heading">
                        <form class="form-horizontal" ref="form" :action="submitUrl()" @submit.prevent="validateForm"
                            method="POST">
                            <input type="hidden" name="_token" id="csrf-token" :value="csrf_token" />

                            <div class="form-group row">
                                <label class="col-md-3">MailChimp ApiKey</label>
                                <div class="col-md-9">
                                    <input class="form-control" name="mailchimp_apikey" type="text"
                                        v-model="mailchimp_apikey" />
                                    <span v-show="errors.has('mailchimp_apikey')" class="help text-danger">{{
                                    errors.first("mailchimp_apikey")
                                    }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-3">MailChimp List Id</label>
                                <div class="col-md-9">
                                    <input class="form-control" name="mailchimp_list_id" type="text"
                                        v-model="mailchimp_list_id" />
                                    <span v-show="errors.has('mailchimp_list_id')" class="help text-danger">{{
                                    errors.first("mailchimp_list_id")
                                    }}</span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-9 offset-md-3">
                                    <button class="lgx-btn" type="submit">
                                        <i class="fas fa-sd-card"></i>
                                        {{ trans("em.save") }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import mixinsFilters from '../../../../mixins.js';

export default {
    props: ["user", "csrf_token"],
    mixins: [mixinsFilters],
    data() {
        return {
            mailchimp_apikey: null,
            mailchimp_list_id: null
        };
    },

    methods: {

        editProfile() {
            (this.mailchimp_apikey = this.user.mailchimp_apikey),
                (this.mailchimp_list_id = this.user.mailchimp_list_id);
        },

        // validate data on form submit
        validateForm(event) {
            this.$validator.validateAll().then(result => {
                if (result) {
                    this.formSubmit(event);
                }
            });
        },

        // show server validation errors
        serverValidate(serrors) {
            this.$validator.validateAll().then(result => {
                this.$validator.errors.add(serrors);
            });
        },

        // submit form
        async formSubmit(event) {
            this.$refs.form.submit();
        },

        submitUrl() {
            return route("eventmie.updateMailchimpUser");
        },
    },
    mounted() {
        this.editProfile();
    }
};
</script>
