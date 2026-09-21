document.addEventListener('DOMContentLoaded', function() {
    const colorInputs = document.querySelectorAll('input[name="colors[]"]');
    const sizeInputs = document.querySelectorAll('input[name="sizes[]"]');
    const quantityContainer = document.getElementById('quantity-container');

    function updateQuantities() {
        quantityContainer.innerHTML = '';
        colorInputs.forEach(colorInput => {
            if (colorInput.checked) {
                sizeInputs.forEach(sizeInput => {
                    if (sizeInput.checked) {
                        const key = colorInput.value + '_' + sizeInput.value;
                        const label = document.createElement('label');
                        label.textContent = `Quantity for ${colorInput.value} - ${sizeInput.value}:`;
                        const input = document.createElement('input');
                        input.type = 'number';
                        input.name = `quantities[${key}]`;
                        input.min = '1';
                        input.value = '1';
                        quantityContainer.appendChild(label);
                        quantityContainer.appendChild(input);
                    }
                });
            }
        });
    }

    colorInputs.forEach(input => input.addEventListener('change', updateQuantities));
    sizeInputs.forEach(input => input.addEventListener('change', updateQuantities));
});
