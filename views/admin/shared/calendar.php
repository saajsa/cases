<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?php echo _l('hearings_calendar'); ?></h3>
            </div>
            <div class="panel-body">
                <div id="calendar"></div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#calendar').fullCalendar({
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        events: function(start, end, timezone, callback) {
            $.ajax({
                url: '<?php echo admin_url("cases/hearings/get_calendar_events"); ?>',
                type: 'GET',
                data: {
                    start: start.format(),
                    end: end.format()
                },
                success: function(data) {
                    callback(data);
                }
            });
        },
        eventClick: function(event) {
            window.location.href = '<?php echo admin_url("cases/hearings/view/"); ?>' + event.id;
        }
    });
});
</script>