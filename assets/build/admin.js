document.addEventListener('DOMContentLoaded', function() {
    const prestations = document.querySelectorAll('select[id$="_prestation"]');

    prestations.forEach(prestationSelect => {
        const parent = prestationSelect.closest('.ea-collection-entry');
        const puInput = parent.querySelector('input[id$="_pu"]');
        const quantityInput = parent.querySelector('input[id$="_quantity"]');
        const soustotalInput = parent.querySelector('input[id$="_soustotal"]');
        console.log(puInput);
        console.log(quantityInput);
        console.log(soustotalInput);

        const updateFields = () => {
            const selectedOption = prestationSelect.options[prestationSelect.selectedIndex];
            const pu = parseFloat(selectedOption.dataset.pu || 0);
            const quantity = parseFloat(quantityInput.value || 0);

            puInput.value = pu.toFixed(2);
            soustotalInput.value = (pu * quantity).toFixed(2);
        };

        prestationSelect.addEventListener('change', updateFields);
        quantityInput.addEventListener('input', updateFields);

        updateFields();
    });
});

