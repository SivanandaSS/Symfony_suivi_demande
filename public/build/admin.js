setInterval(function() {
    
    const collectionContainer = document.querySelector('.ea-form-collection-items'); // conteneur global
    
    if (!collectionContainer) return;

    collectionContainer.querySelectorAll("select.form-select").forEach((elt)=> {
        console.log(elt.options[elt.selectedIndex].dataset.pu);
        const selectedOption = elt.options[elt.selectedIndex];
        if (!selectedOption) return;
        const puValue = parseFloat(selectedOption.dataset.pu);
        

        // On remonte dans le DOM pour trouver l'input correspondant dans le même "entry"
        const entry = elt.closest('.field-collection-item');
            if (!entry) return;
        // set l'input pu
        const puInput = entry.querySelector('input[id$="_pu"]');
        puInput.value = puValue;
        // Calcul du soustotal
        const quantityInput = entry.querySelector('input[id$="_quantity"]');
        const quantityValue = quantityInput.value;
        const sousTotal = quantityValue * puValue
        // Set su Sous total
        const soustotalInput = entry.querySelector('input[id$="_soustotal"]');
        soustotalInput.value = sousTotal;


    });
    

}
,1000);

