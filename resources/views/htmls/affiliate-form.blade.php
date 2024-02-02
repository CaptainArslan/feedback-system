
<form class="form-control">
    <div class="row">
 <label for="affiliate_url" class="py-2">Your Webhook URL</label>
        <input type="text" name="affiliate_url"
            class="form-control form-control-lg form-control-solid affiliate_url mb-3 mb-lg-0"
            placeholder="your_webhook_url" value="{{ $main_url ?? '' }}">
    </div>

    <div class="row">
        <div class="col-md-12" style="text-align: left !important">
            <button type="button" class="btn btn-primary copy_affiliate" id="kt_account_profile_details_submit">Copy
                URL</button>
        </div>
    </div>
</form>
