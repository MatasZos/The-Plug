document.addEventListener("DOMContentLoaded", function () {
    const deliveryTab = document.getElementById("delivery-tab");
    const pickupTab = document.getElementById("pickup-tab");
    const deliveryForm = document.getElementById("delivery-form");
    const pickupForm = document.getElementById("pickup-form");
    const paymentSection = document.getElementById("payment-section");
    const reviewSection = document.getElementById("review-section");
    const stepIndicators = document.querySelectorAll(".checkout-step");
    const deliveryContinue = document.getElementById("delivery-continue");
    const pickupContinue = document.getElementById("pickup-continue");
    const paymentContinue = document.getElementById("payment-continue");
    const remember = document.getElementById("remember-info");

    function updateStep(step) {
        stepIndicators.forEach((indicator, index) => {
            indicator.classList.toggle("active", index + 1 === step);
        });
    }

    function resetSections() {
        deliveryForm.style.display = "none";
        pickupForm.style.display = "none";
        paymentSection.style.display = "none";
        reviewSection.style.display = "none";
    }

    function resetTabStyles() {
        deliveryTab.classList.remove("active-tab");
        pickupTab.classList.remove("active-tab");
    }

    resetSections();
    deliveryForm.style.display = "block";
    updateStep(1);
    resetTabStyles();
    deliveryTab.classList.add("active-tab");

    if (localStorage.getItem("checkout_name")) {
        document.querySelector('input[name="full_name"]').value = localStorage.getItem("checkout_name") || '';
        document.querySelector('input[name="address"]').value = localStorage.getItem("checkout_address") || '';
        document.querySelector('input[name="contact"]').value = localStorage.getItem("checkout_contact") || '';
        if (remember) remember.checked = true;
    }

    deliveryTab.addEventListener("click", function () {
        resetSections();
        resetTabStyles();
        deliveryForm.style.display = "block";
        deliveryTab.classList.add("active-tab");
        updateStep(1);
    });

    pickupTab.addEventListener("click", function () {
        resetSections();
        resetTabStyles();
        pickupForm.style.display = "block";
        pickupTab.classList.add("active-tab");
        updateStep(1);
    });

    deliveryContinue.addEventListener("click", function () {
        const fields = deliveryForm.querySelectorAll("[required]");
        let valid = true;
        fields.forEach(field => {
            field.classList.remove("invalid");
            if (!field.value.trim()) {
                field.classList.add("invalid");
                valid = false;
            }
        });
        if (!valid) return;
        resetSections();
        paymentSection.style.display = "block";
        updateStep(2);
    });

    pickupContinue.addEventListener("click", function () {
        const e = pickupForm.querySelector("input[name='pickup_eircode']");
        e.classList.remove("invalid");
        if (!e || !e.value.trim()) {
            e.classList.add("invalid");
            return;
        }
        resetSections();
        paymentSection.style.display = "block";
        updateStep(2);
    });

    paymentContinue.addEventListener("click", function () {
        const fields = paymentSection.querySelectorAll("[required]");
        let valid = true;
        fields.forEach(field => {
            field.classList.remove("invalid");
            if (!field.value.trim()) {
                field.classList.add("invalid");
                valid = false;
            }
        });
        if (!valid) return;
        resetSections();
        reviewSection.style.display = "block";
        updateStep(3);
    });

    const reviewForm = document.querySelector("#review-section form");
    if (reviewForm) {
        reviewForm.addEventListener("submit", function () {
            if (remember && remember.checked) {
                localStorage.setItem("checkout_name", document.getElementById("full_name_hidden").value);
                localStorage.setItem("checkout_address", document.getElementById("address_hidden").value);
                localStorage.setItem("checkout_contact", document.getElementById("contact_hidden").value);
            } else {
                localStorage.removeItem("checkout_name");
                localStorage.removeItem("checkout_address");
                localStorage.removeItem("checkout_contact");
            }
        });
    }
});
