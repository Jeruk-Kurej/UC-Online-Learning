import TomSelect from 'tom-select/dist/js/tom-select.complete.js';

// --- Role-dependent sections ---
// Admin accounts have no student status, contact details, academic extras or business assignments.

const roleSections = [
    'student_status_container',
    'identity_contact_section',
    'academic_extra_section',
    'business_assignment_section',
];

function toggleStudentFields(role) {
    const isAdmin = role === 'admin';

    roleSections.forEach((id) => document.getElementById(id)?.classList.toggle('hidden', isAdmin));

    const studentStatusSelect = document.getElementById('student_status');
    if (studentStatusSelect) {
        if (isAdmin) {
            studentStatusSelect.removeAttribute('required');
        } else {
            studentStatusSelect.setAttribute('required', 'required');
        }
    }
}

export default function initUserForm() {
    const roleSelect = document.getElementById('role');

    if (!roleSelect) {
        return;
    }

    new TomSelect(roleSelect, {
        create: false,
        placeholder: 'Select Role',
        searchField: ['text'],
        onChange: (value) => toggleStudentFields(value),
    });

    toggleStudentFields(roleSelect.value);
}
