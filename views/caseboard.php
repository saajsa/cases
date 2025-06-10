<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php 
init_head();
echo load_cases_css(['cards', 'buttons', 'status'], 'caseboard');
echo cases_page_wrapper_start(
    'Caseboard',
    'Dashboard overview of active cases and upcoming hearings',
    [
        [
            'text' => 'View All Cases',
            'href' => admin_url('cases'),
            'class' => 'cases-btn'
        ],
        [
            'text' => 'Manage Hearings',
            'href' => admin_url('cases/hearings'),
            'class' => 'cases-btn cases-btn-primary'
        ]
    ]
);
?>

<!-- Filter Pills -->
<div class="cases-flex cases-flex-center cases-mb-lg cases-flex-wrap">
    <button class="cases-btn cases-btn-sm active" data-filter="all">All</button>
    <button class="cases-btn cases-btn-sm" data-filter="active-cases">Active Cases</button>
    <button class="cases-btn cases-btn-sm" data-filter="pending">Pending</button>
    <button class="cases-btn cases-btn-sm" data-filter="completed">Completed</button>
    <button class="cases-btn cases-btn-sm" data-filter="recent-consultations">Recent Consultations</button>
</div>

<!-- Upcoming Hearings Section -->
<?php echo cases_section_start('Upcoming Hearings (Next 7 Days)', [
    'class' => 'cases-section-header',
    'id' => 'upcoming-hearings-section'
]); ?>
<div class="cases-flex cases-flex-end cases-mb-md">
    <a href="<?php echo admin_url('cases/hearings'); ?>" class="cases-text-muted cases-font-size-sm">
        View All Hearings →
    </a>
</div>

<div class="cases-grid cases-grid-responsive">
    <?php if (!empty($upcoming_hearings)): ?>
        <?php foreach ($upcoming_hearings as $hearing): ?>
            <div class="cases-card cases-hover-lift">
                <div class="cases-card-header">
                    <div class="cases-card-title"><?php echo date('d M', strtotime($hearing['date'])); ?></div>
                    <?php 
                        $status = $hearing['status'];
                        $status_type = strtolower($status);
                        if ($status == 'Scheduled') $status_type = 'scheduled';
                        elseif ($status == 'Completed') $status_type = 'completed';
                        else $status_type = 'adjourned';
                        
                        echo cases_status_badge($status, $status_type);
                    ?>
                </div>
                
                <div class="cases-card-body">
                    <div class="cases-card-subtitle">
                        <?php 
                            echo !empty($hearing['case_title']) ? htmlspecialchars($hearing['case_title']) : 'Case #'.$hearing['case_id']; 
                        ?>
                    </div>
                    
                    <div class="cases-card-meta-grid">
                        <div class="cases-card-meta-item">
                            <span class="cases-card-meta-label">Time:</span>
                            <span class="cases-card-meta-value"><?php echo date('h:i A', strtotime($hearing['time'])); ?></span>
                        </div>
                        <div class="cases-card-meta-item">
                            <span class="cases-card-meta-label">Purpose:</span>
                            <span class="cases-card-meta-value">
                                <?php 
                                    $purpose = !empty($hearing['hearing_purpose']) ? $hearing['hearing_purpose'] : 'N/A';
                                    echo strlen($purpose) > 25 ? substr(htmlspecialchars($purpose), 0, 22) . '...' : htmlspecialchars($purpose);
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="cases-card-footer">
                    <div class="cases-flex cases-flex-wrap">
                        <?php echo cases_action_button('Update', [
                            'type' => 'primary',
                            'href' => admin_url('cases/hearings/edit/'.$hearing['id'])
                        ]); ?>
                        
                        <?php echo cases_action_button('Add Doc', [
                            'type' => 'info',
                            'href' => admin_url('documents/upload'),
                            'onclick' => "localStorage.setItem('document_upload_data', JSON.stringify({
                                hearing_id: {$hearing['id']},
                                case_id: {$hearing['case_id']},
                                customer_id: " . (isset($hearing['client_id']) ? $hearing['client_id'] : 0) . ",
                                doc_type: 'hearing'
                            }));"
                        ]); ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <?php echo cases_empty_state(
            'No Upcoming Hearings',
            'No hearings scheduled for the next 7 days',
            ['icon' => 'fas fa-calendar-times']
        ); ?>
    <?php endif; ?>
</div>

<?php echo cases_section_end(); ?>

<!-- Active Cases Section -->
<?php echo cases_section_start('Active Cases', [
    'class' => 'cases-section-header',
    'id' => 'active-cases-section'
]); ?>
<div class="cases-flex cases-flex-end cases-mb-md">
    <a href="<?php echo admin_url('cases'); ?>" class="cases-text-muted cases-font-size-sm">
        View All Cases →
    </a>
</div>

<div class="cases-grid cases-grid-responsive">
    <?php if (!empty($cases)): ?>
        <?php foreach ($cases as $case): ?>
            <div class="cases-card cases-hover-lift">
                <div class="cases-card-header">
                    <div class="cases-card-title">
                        <?php echo !empty($case['case_number']) ? htmlspecialchars($case['case_number']) : '#'.$case['id']; ?>
                    </div>
                    <?php echo cases_status_badge('Active', 'active'); ?>
                </div>
                
                <div class="cases-card-body">
                    <div class="cases-card-subtitle">
                        <?php 
                            $title = !empty($case['case_title']) ? $case['case_title'] : 'Untitled Case';
                            echo strlen($title) > 35 ? substr(htmlspecialchars($title), 0, 32) . '...' : htmlspecialchars($title);
                        ?>
                    </div>
                    
                    <div class="cases-card-meta-grid">
                        <div class="cases-card-meta-item">
                            <span class="cases-card-meta-label">Client:</span>
                            <span class="cases-card-meta-value">
                                <?php 
                                    $client = isset($case['client_name']) && $case['client_name'] !== '' ? $case['client_name'] : 'N/A';
                                    echo strlen($client) > 20 ? substr(htmlspecialchars($client), 0, 17) . '...' : htmlspecialchars($client);
                                ?>
                            </span>
                        </div>
                        <div class="cases-card-meta-item">
                            <span class="cases-card-meta-label">Filed:</span>
                            <span class="cases-card-meta-value">
                                <?php echo !empty($case['date_filed']) ? date('d M Y', strtotime($case['date_filed'])) : 'N/A'; ?>
                            </span>
                        </div>
                        <div class="cases-card-meta-item">
                            <span class="cases-card-meta-label">Hearings:</span>
                            <span class="cases-card-meta-value">
                                <?php echo cases_count_badge(isset($case['hearing_count']) ? $case['hearing_count'] : '0'); ?>
                            </span>
                        </div>
                        <div class="cases-card-meta-item">
                            <span class="cases-card-meta-label">Documents:</span>
                            <span class="cases-card-meta-value">
                                <?php echo cases_count_badge(isset($case['document_count']) ? $case['document_count'] : '0'); ?>
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="cases-card-footer">
                    <?php echo cases_action_button('View Details', [
                        'type' => 'primary',
                        'href' => admin_url('cases/details?id='.$case['id'])
                    ]); ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <?php echo cases_empty_state(
            'No Active Cases',
            'No active cases found',
            ['icon' => 'fas fa-briefcase']
        ); ?>
    <?php endif; ?>
</div>

<?php echo cases_section_end(); ?>

<!-- Recent Consultations Section -->
<?php echo cases_section_start('Recent Consultations', [
    'class' => 'cases-section-header',
    'id' => 'recent-consultations-section'
]); ?>
<div class="cases-flex cases-flex-end cases-mb-md">
    <a href="<?php echo admin_url('cases'); ?>" class="cases-text-muted cases-font-size-sm">
        View All Consultations →
    </a>
</div>

<div class="cases-grid cases-grid-responsive">
    <?php if (!empty($consultations)): ?>
        <?php foreach ($consultations as $consultation): ?>
            <div class="cases-card cases-hover-lift">
                <div class="cases-card-header">
                    <div class="cases-card-title"><?php echo date('d M', strtotime($consultation['date_added'])); ?></div>
                    <?php 
                        $phase_type = $consultation['phase'] == 'consultation' ? 'consultation' : 'litigation';
                        echo cases_status_badge(ucfirst($consultation['phase']), $phase_type);
                    ?>
                </div>
                
                <div class="cases-card-body">
                    <div class="cases-card-subtitle">
                        <?php 
                            $client = !empty($consultation['client_name']) ? $consultation['client_name'] : 'Unknown Client';
                            echo strlen($client) > 35 ? substr(htmlspecialchars($client), 0, 32) . '...' : htmlspecialchars($client);
                        ?>
                    </div>
                    
                    <div class="cases-card-meta-grid">
                        <div class="cases-card-meta-item">
                            <span class="cases-card-meta-label">Contact:</span>
                            <span class="cases-card-meta-value">
                                <?php 
                                    $contact = !empty($consultation['contact_name']) ? $consultation['contact_name'] : 'N/A';
                                    echo strlen($contact) > 20 ? substr(htmlspecialchars($contact), 0, 17) . '...' : htmlspecialchars($contact);
                                ?>
                            </span>
                        </div>
                        <div class="cases-card-meta-item">
                            <span class="cases-card-meta-label">Tag:</span>
                            <span class="cases-card-meta-value">
                                <?php 
                                    $tag = !empty($consultation['tag']) ? $consultation['tag'] : 'N/A';
                                    echo strlen($tag) > 25 ? substr(htmlspecialchars($tag), 0, 22) . '...' : htmlspecialchars($tag);
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="cases-card-footer">
                    <div class="cases-flex cases-flex-wrap">
                        <?php echo cases_action_button('View Notes', [
                            'type' => 'default',
                            'onclick' => "viewConsultationNote({$consultation['id']})"
                        ]); ?>
                        
                        <?php if ($consultation['phase'] == 'consultation'): ?>
                            <?php echo cases_action_button('Upgrade to Case', [
                                'type' => 'success',
                                'href' => admin_url('cases/upgrade_to_litigation?consultation_id='.$consultation['id'])
                            ]); ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <?php echo cases_empty_state(
            'No Recent Consultations',
            'No recent consultations found',
            ['icon' => 'fas fa-comments']
        ); ?>
    <?php endif; ?>
</div>

<?php echo cases_section_end(); ?>

<?php echo cases_page_wrapper_end(); ?>

<!-- Consultation Note Modal -->
<div class="modal fade" id="consultation_note_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="cases-modal-content">
            <div class="cases-modal-header">
                <h5 class="cases-modal-title">Consultation Notes</h5>
                <button type="button" class="cases-modal-close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="cases-modal-body">
                <!-- Consultation note will be loaded here -->
            </div>
            <div class="cases-modal-footer">
                <?php echo cases_button('Close', [
                    'type' => 'default',
                    'data' => ['dismiss' => 'modal']
                ]); ?>
            </div>
        </div>
    </div>
</div>

<script>
// Clean, minimal JavaScript using framework utilities
document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    const filterButtons = document.querySelectorAll('[data-filter]');
    const sections = document.querySelectorAll('[id$="-section"]');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.dataset.filter;
            
            // Update active state
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Show/hide sections based on filter
            sections.forEach(section => {
                section.style.display = 'block';
            });
            
            // Hide specific sections based on filter
            switch(filter) {
                case 'active-cases':
                    hideSection('upcoming-hearings-section');
                    hideSection('recent-consultations-section');
                    break;
                case 'pending':
                    hideSection('active-cases-section');
                    hideSection('recent-consultations-section');
                    break;
                case 'completed':
                    hideSection('upcoming-hearings-section');
                    hideSection('recent-consultations-section');
                    break;
                case 'recent-consultations':
                    hideSection('upcoming-hearings-section');
                    hideSection('active-cases-section');
                    break;
            }
        });
    });
    
    function hideSection(sectionId) {
        const section = document.getElementById(sectionId);
        if (section) {
            section.style.display = 'none';
        }
    }
    
    // Enhanced card animations
    const cards = document.querySelectorAll('.cases-card');
    cards.forEach((card, index) => {
        // Stagger entrance animations
        card.classList.add('cases-fade-in');
        setTimeout(() => {
            card.classList.add('active');
        }, index * 50);
        
        // Enhanced hover effects
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-4px)';
            this.style.boxShadow = 'var(--cases-shadow-md)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = 'var(--cases-shadow-sm)';
        });
    });
});

// Global function for consultation note viewing
function viewConsultationNote(consultationId) {
    const modalBody = document.querySelector('#consultation_note_modal .cases-modal-body');
    
    // Show loading state
    modalBody.innerHTML = `
        <div class="cases-text-center cases-p-lg">
            <i class="fas fa-spinner fa-spin cases-text-muted" style="font-size: 2rem; margin-bottom: 20px;"></i>
            <p class="cases-text-muted">Loading consultation note...</p>
        </div>
    `;
    
    // Show modal
    $('#consultation_note_modal').modal('show');
    
    // Fetch consultation note
    fetch(admin_url + 'cases/get_consultation_note/' + consultationId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                modalBody.innerHTML = `
                    <div class="cases-consultation-note">
                        ${data.note || 'No note content available.'}
                    </div>
                `;
            } else {
                modalBody.innerHTML = `
                    <div class="cases-text-center cases-p-lg cases-text-danger">
                        <i class="fas fa-exclamation-triangle" style="font-size: 2rem; margin-bottom: 20px;"></i>
                        <p>Could not retrieve consultation note</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            modalBody.innerHTML = `
                <div class="cases-text-center cases-p-lg cases-text-danger">
                    <i class="fas fa-exclamation-triangle" style="font-size: 2rem; margin-bottom: 20px;"></i>
                    <p>An error occurred while retrieving consultation note</p>
                </div>
            `;
        });
}
</script>

<?php init_tail(); ?>