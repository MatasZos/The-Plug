document.addEventListener('DOMContentLoaded', function () {
    const saveInfoCheckbox = document.getElementById('save-info');
    if (saveInfoCheckbox) {
        saveInfoCheckbox.addEventListener('change', function () {
            if (this.checked) {
                const fullName = document.querySelector('input[name="full_name"]').value;
                const address = document.querySelector('input[name="address"]').value;
                const contact = document.querySelector('input[name="contact"]').value;

                fetch('save_user_info.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `full_name=${encodeURIComponent(fullName)}&address=${encodeURIComponent(address)}&contact=${encodeURIComponent(contact)}`
                });
            }
        });
    }
});
