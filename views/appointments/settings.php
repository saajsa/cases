<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
/* Consistent styling with other module pages */
* {
    box-sizing: border-box;
}

body {
    background: #fafafa;
    color: #2c2c2c;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.page-header {
    background: #ffffff;
    border: 1px solid #e1e1e1;
    padding: 40px;
    margin-bottom: 30px;
    border-radius: 2px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
}

.page-header h1 {
    margin: 0 0 8px 0;
    font-weight: 600;
    font-size: 2.2rem;
    color: #1a1a1a;
    letter-spacing: -0.02em;
}

.page-header .subtitle {
    font-size: 1rem;
    color: #666666;
    font-weight: 400;
    margin-bottom: 25px;
}

.main-content {
    background: #ffffff;
    border: 1px solid #e1e1e1;
    border-radius: 2px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    padding: 30px;
}

.settings-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 500;
    color: #1a1a1a;
    margin-bottom: 8px;
}

.form-control {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid #d1d1d1;
    border-radius: 1px;
    font-size: 0.875rem;
    background: #ffffff;
    color: #2c2c2c;
}

.form-control:focus {
    outline: none;
    border-color: #1a1a1a;
}
</style>

<div id="wrapper">
    <div class="content">
        <div class="page-header">
            <h1>Appointment Settings</h1>
            <div class="subtitle">Configure default appointment rules</div>
        </div>
        <div class="main-content">
            <?php echo form_open(admin_url('cases/settings')); ?>
                <div class="settings-grid">
                    <div class="form-group">
                        <label class="form-label" for="time_slot_interval">Time Slot Interval (minutes)</label>
                        <input type="number" id="time_slot_interval" name="settings[time_slot_interval]" class="form-control" value="<?php echo htmlspecialchars($settings['time_slot_interval'] ?? 30); ?>" min="1">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="buffer_time_before">Buffer Time Before (minutes)</label>
                        <input type="number" id="buffer_time_before" name="settings[buffer_time_before]" class="form-control" value="<?php echo htmlspecialchars($settings['buffer_time_before'] ?? 15); ?>" min="0">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="buffer_time_after">Buffer Time After (minutes)</label>
                        <input type="number" id="buffer_time_after" name="settings[buffer_time_after]" class="form-control" value="<?php echo htmlspecialchars($settings['buffer_time_after'] ?? 15); ?>" min="0">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="require_prepayment">
                            <input type="checkbox" id="require_prepayment" name="settings[require_prepayment]" value="1" <?php echo !empty($settings['require_prepayment']) ? 'checked' : ''; ?>>
                            Require Prepayment
                        </label>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="invoice_prefix">Invoice Prefix</label>
                        <input type="text" id="invoice_prefix" name="settings[invoice_prefix]" class="form-control" value="<?php echo htmlspecialchars($settings['invoice_prefix'] ?? 'APPT-'); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="default_payment_terms">Default Payment Terms</label>
                        <input type="text" id="default_payment_terms" name="settings[default_payment_terms]" class="form-control" value="<?php echo htmlspecialchars($settings['default_payment_terms'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="send_invoice_immediately">
                            <input type="checkbox" id="send_invoice_immediately" name="settings[send_invoice_immediately]" value="1" <?php echo !empty($settings['send_invoice_immediately']) ? 'checked' : ''; ?>>
                            Send Invoice Immediately
                        </label>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="cancellation_fee_hours">Cancellation Fee Applies Within (hours)</label>
                        <input type="number" id="cancellation_fee_hours" name="settings[cancellation_fee_hours]" class="form-control" value="<?php echo htmlspecialchars($settings['cancellation_fee_hours'] ?? 24); ?>" min="0">
                    </div>
                </div>
                <div class="text-right mtop20">
                    <button type="submit" class="btn btn-primary">Save Settings</button>
                </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<?php init_tail(); ?>
