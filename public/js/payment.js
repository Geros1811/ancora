function selectPlan(planId, element) {
    // Remove 'selected' class from all product items
    document.querySelectorAll('.product-item').forEach(item => item.classList.remove('selected'));

    // Add 'selected' class to the clicked product item
    element.classList.add('selected');

    // Set the value of the hidden input field
    document.getElementById('selected_plan').value = planId;
}

function showPaymentOptions() {
    document.getElementById('payment_options').style.display = 'block';
}
