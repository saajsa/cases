<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php 
init_head();
echo load_cases_css(['cards', 'buttons', 'status', 'tables', 'modals'], 'caseboard');
echo cases_page_wrapper_start(
    'Legal Practice Dashboard',
    'Focus on today\'s items',
    [
        [
            'text' => 'New Consultation',
            'href' => admin_url('cases?new_consultation=1'),
            'class' => 'cases-btn cases-btn-primary',
            'icon' => 'fas fa-plus'
        ],
        [
            'text' => 'Schedule Hearing',
            'href' => admin_url('cases/hearings/add'),
            'class' => 'cases-btn cases-btn-success',
            'icon' => 'fas fa-gavel'
        ],
        [
            'text' => 'View All Cases',
            'href' => admin_url('cases'),
            'class' => 'cases-btn',
            'icon' => 'fas fa-briefcase'
        ],
        [
            'text' => 'View Consultations',
            'href' => admin_url('cases?tab=consultations'),
            'class' => 'cases-btn',
            'icon' => 'fas fa-comments'
        ],
        [
            'text' => 'View Hearings',
            'href' => admin_url('cases/hearings'),
            'class' => 'cases-btn',
            'icon' => 'fas fa-calendar'
        ]
    ]
);
?>

<!-- Main Dashboard Grid: Two columns -->
<div class="cases-grid" style="grid-template-columns: 2fr 1fr; gap: var(--cases-spacing-lg);">
    <!-- Left Column: Today's Consultations and Today's Hearings -->
    <div>
        <!-- Today's Consultations -->
        <div class="cases-section cases-mb-lg">
            <h3 class="cases-section-title">Today's Consultations</h3>
            <div id="consultations-container">
                <?php echo cases_loading_state('Loading consultations...'); ?>
            </div>
        </div>
        <!-- Today's Hearings -->
        <div class="cases-section cases-mb-lg">
            <h3 class="cases-section-title">Today's Hearings</h3>
            <div id="hearings-today-container">
                <?php echo cases_loading_state('Loading hearings...'); ?>
            </div>
        </div>
    </div>
    <!-- Right Column: Quick Access Buttons and Mini Calendar -->
    <div>
        <!-- Quick Access Buttons -->
        <div class="cases-info-card cases-mb-md">
            <div class="cases-info-card-header">
                <h4 class="cases-info-card-title">Quick Access</h4>
            </div>
            <div class="cases-grid cases-grid-responsive cases-p-2">
                <button class="cases-btn cases-btn-primary cases-mb-xs" onclick="window.location.href='<?=admin_url('cases?new_consultation=1')?>'">
                    <i class="fas fa-plus"></i> New Consultation
                </button>
                <button class="cases-btn cases-btn-success cases-mb-xs" onclick="window.location.href='<?=admin_url('cases/hearings/add')?>'">
                    <i class="fas fa-gavel"></i> Schedule Hearing
                </button>
                <button class="cases-btn cases-btn" onclick="window.location.href='<?=admin_url('cases')?>'">
                    <i class="fas fa-briefcase"></i> All Cases
                </button>
                <button class="cases-btn cases-btn" onclick="window.location.href='<?=admin_url('cases?tab=consultations')?>'">
                    <i class="fas fa-comments"></i> Consultations
                </button>
                <button class="cases-btn cases-btn" onclick="window.location.href='<?=admin_url('cases/hearings')?>'">
                    <i class="fas fa-calendar"></i> Hearings
                </button>
                <button class="cases-btn cases-btn" onclick="window.location.href='<?=admin_url('clients')?>'">
                    <i class="fas fa-users"></i> Clients
                </button>
                <button class="cases-btn cases-btn" onclick="window.location.href='<?=admin_url('invoices')?>'">
                    <i class="fas fa-file-invoice"></i> Invoices
                </button>
            </div>
        </div>
        <!-- Mini Calendar -->
        <div class="cases-info-card cases-mb-md">
            <div class="cases-info-card-header">
                <h4 class="cases-info-card-title">Calendar</h4>
            </div>
            <div class="cases-flex cases-flex-between cases-flex-center cases-mb-md">
                <button class="cases-btn cases-btn-sm" id="prev-month">‹</button>
                <h5 id="calendar-month" class="cases-m-0">Loading...</h5>
                <button class="cases-btn cases-btn-sm" id="next-month">›</button>
            </div>
            <div id="calendar-grid" style="display:grid; grid-template-columns:repeat(7,1fr); gap:2px; font-size:var(--cases-font-size-sm);"></div>
            <div class="cases-mt-sm cases-font-size-xs cases-text-muted">
                <span style="color: var(--cases-warning);">●</span> Hearings
            </div>
        </div>
    </div>
</div>

<?php echo cases_page_wrapper_end(); ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const adminBase = admin_url;
    // Utility functions
    function htmlEscape(str) {
        if (str == null) return '';
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
    }
    function formatDate(d) {
        if (!d) return '';
        const dt = new Date(d);
        return dt.toLocaleDateString(undefined,{year:'numeric',month:'short',day:'numeric'});
    }
    function formatTime(t) {
        if (!t) return '';
        const dt = new Date(t.includes('T')? t : ('1970-01-01T'+t));
        return dt.toLocaleTimeString(undefined,{hour:'numeric',minute:'2-digit'});
    }
    function showLoading(id,msg='Loading...'){
        const c=document.getElementById(id);
        if(c) c.innerHTML=`<div style="text-align:center;padding:20px;color:var(--cases-text-muted);"><i class="fas fa-spinner fa-spin"></i><p>${htmlEscape(msg)}</p></div>`;
    }
    function showEmptyState(id,title,msg){
        const c=document.getElementById(id);
        if(c) c.innerHTML=`<div class="cases-empty-state"><i class="fas fa-check-circle" style="font-size:2rem;color:var(--cases-text-muted);"></i><h5 style="color:var(--cases-text-light);">${htmlEscape(title)}</h5><p style="color:var(--cases-text-muted);">${htmlEscape(msg)}</p></div>`;
    }
    // Load Today's Consultations
    function loadTodayConsultations() {
        showLoading('consultations-container','Loading consultations...');
        fetch(adminBase + 'cases/consultations_list', {headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}})
        .then(r=>r.json()).then(data=>{
            const container = document.getElementById('consultations-container');
            if(data.success && Array.isArray(data.data)){
                const today = new Date().toISOString().split('T')[0];
                const todays = data.data.filter(c => c.date_added && c.date_added.startsWith(today));
                if(todays.length){
                    let html = '<div class="cases-grid cases-grid-responsive">';
                    todays.forEach(c=>{
                        html += `<div class="cases-card"><div class="cases-card-header"><div class="cases-card-title">${htmlEscape(c.client_name||c.client_id)}</div><span class="cases-status-badge">${htmlEscape(c.tag||'')}</span></div><div class="cases-card-body"><div class="cases-card-meta-grid"><div class="cases-card-meta-item"><span class="cases-card-meta-label">Note:</span><span class="cases-card-meta-value">${htmlEscape(c.note)}</span></div></div></div><div class="cases-card-footer"><button class="cases-btn cases-btn-primary" onclick="window.location.href='${adminBase}cases?edit_consultation=${c.id}'">Edit</button></div></div>`;
                    }); html += '</div>';
                    container.innerHTML = html;
                } else showEmptyState('consultations-container','No consultations today','');
            } else showEmptyState('consultations-container','No consultations today','');
        }).catch(err=>{console.error(err); showEmptyState('consultations-container','No consultations today','');});
    }
    // Load Today's Hearings
    function loadTodayHearings() {
        showLoading('hearings-today-container','Loading hearings...');
        const today = new Date().toISOString().split('T')[0];
        fetch(adminBase + 'cases/hearings/get_causelist?date=' + today, {headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}})
        .then(r=>r.json()).then(data=>{
            const container = document.getElementById('hearings-today-container');
            const arr = Array.isArray(data.data)?data.data:[];
            if(arr.length){ let html = '<div class="cases-grid cases-grid-responsive">';
                arr.forEach(h=>{
                    html += `<div class="cases-card"><div class="cases-card-header"><div class="cases-card-title">${htmlEscape(h.case_title)}</div><div class="cases-card-date-badge">${formatDate(h.date)}</div></div><div class="cases-card-body"><div class="cases-card-meta-grid"><div class="cases-card-meta-item"><span class="cases-card-meta-label">Time:</span><span class="cases-card-meta-value">${formatTime(h.time)}</span></div><div class="cases-card-meta-item"><span class="cases-card-meta-label">Court:</span><span class="cases-card-meta-value">${htmlEscape(h.court_name)}</span></div></div></div><div class="cases-card-footer"><button class="cases-btn cases-btn-primary" onclick="window.location.href='${adminBase}cases/hearings/edit/${h.hearing_id}'">Details</button></div></div>`;
                }); html += '</div>'; container.innerHTML = html;
            } else showEmptyState('hearings-today-container','No hearings today','');
        }).catch(err=>{console.error(err); showEmptyState('hearings-today-container','No hearings today','');});
    }
    // Calendar
    let currentDate=new Date();
    function initializeCalendar(){ renderCalendar(currentDate); document.getElementById('prev-month').addEventListener('click',()=>{currentDate.setMonth(currentDate.getMonth()-1); renderCalendar(currentDate);}); document.getElementById('next-month').addEventListener('click',()=>{currentDate.setMonth(currentDate.getMonth()+1); renderCalendar(currentDate);}); }
    function renderCalendar(date){ const monthNames=['January','February','March','April','May','June','July','August','September','October','November','December']; const mElem=document.getElementById('calendar-month'); if(mElem) mElem.textContent=monthNames[date.getMonth()]+' '+date.getFullYear(); const grid=document.getElementById('calendar-grid'); if(!grid) return; grid.innerHTML=''; const days=['S','M','T','W','T','F','S']; days.forEach(d=>{const e=document.createElement('div'); e.textContent=d; e.style.cssText='padding:4px;text-align:center;font-weight:600;color:var(--cases-text-muted);font-size:var(--cases-font-size-xs);background:var(--cases-bg-tertiary);'; grid.appendChild(e);}); const firstDay=new Date(date.getFullYear(),date.getMonth(),1).getDay(); const daysCount=new Date(date.getFullYear(),date.getMonth()+1,0).getDate(); for(let i=0;i<firstDay;i++){const e=document.createElement('div');e.style.cssText='padding:4px;';grid.appendChild(e);} const today=new Date(); for(let d=1;d<=daysCount;d++){ const e=document.createElement('div'); e.textContent=d; e.style.cssText='padding:4px;text-align:center;cursor:pointer;'; if(date.getFullYear()===today.getFullYear()&&date.getMonth()===today.getMonth()&&d===today.getDate()){e.style.backgroundColor='var(--cases-primary)';e.style.color='#fff';} e.addEventListener('click',()=>{ const sel=new Date(date.getFullYear(),date.getMonth(),d); const ds=sel.toISOString().split('T')[0]; window.location.href=adminBase+'cases/hearings/causelist?date='+ds;}); grid.appendChild(e);} }
    // Init
    function initialize(){ loadTodayConsultations(); loadTodayHearings(); initializeCalendar(); }
    initialize();
});
</script>
<?php init_tail(); ?>