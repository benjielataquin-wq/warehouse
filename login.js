
        // Remember the last user that logged in and display their email
        document.addEventListener('DOMContentLoaded', function () {

            var emailInput = document.getElementById('email');
            var form = document.querySelector('form');

            // Display the remembered email on load
            var rememberedEmail = localStorage.getItem('remembered_email');

            if (rememberedEmail && emailInput) {
                emailInput.value = rememberedEmail;
            }

            // Save the email when the login form is submitted
            if (form) {
                form.addEventListener('submit', function () {
                    if (emailInput && emailInput.value) {
                        localStorage.setItem('remembered_email', emailInput.value);
                    }
                });
            }

        });

