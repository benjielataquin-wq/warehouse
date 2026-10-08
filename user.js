
        // Load profile data from tb_users
        document.addEventListener('DOMContentLoaded', function () {

            // Toggle Sidebar
            var button = document.querySelector('.menu-button');

            if (button) {
                button.addEventListener('click', function () {
                    document.body.classList.toggle('sidebar-hidden');
                });
            }

            // Load user data
            loadUserProfile();

        });


        // Fetch tb_users data and fill the profile page
        function loadUserProfile() {

            fetch('user_data.php')
                .then(function (response) {
                    return response.json();
                })
                .then(function (data) {

                    if (data.error) {
                        return;
                    }

                    var fullName =
                        (data.firstname || '') + ' ' + (data.lastname || '');

                    var displayName = fullName.trim() !== '' ? fullName.trim() : 'User';

                    document.querySelector('.profile-info h2').textContent = displayName;

                    document.getElementById('avatarInitial').textContent =
                        (data.firstname || '').charAt(0).toUpperCase() ||
                        displayName.charAt(0).toUpperCase();

                    document.getElementById('firstName').textContent =
                        data.firstname || '—';
                    document.getElementById('lastName').textContent =
                        data.lastname || '—';
                    document.getElementById('birthDate').textContent =
                        data.birth || '—';
                    document.getElementById('email').textContent =
                        data.email || '—';
                    document.getElementById('phone').textContent =
                        data.contact || '—';

                    document.getElementById('editFirstName').value =
                        data.firstname || '';
                    document.getElementById('editLastName').value =
                        data.lastname || '';
                    document.getElementById('editBirthDate').value =
                        data.birth || '';
                    document.getElementById('editEmail').value =
                        data.email || '';
                    document.getElementById('editPhone').value =
                        data.contact || '';

                    document.getElementById('country').textContent =
                        data.country || '—';
                    document.getElementById('city').textContent =
                        data.city || '—';
                    document.getElementById('postal').textContent =
                        data.postal || '—';

                    document.getElementById('editCountry').value =
                        data.country || '';
                    document.getElementById('editCity').value =
                        data.city || '';
                    document.getElementById('editPostal').value =
                        data.postal || '';

                })
                .catch(function () {

                });
        }


        // Open Personal Information Modal
        function openPersonalModal() {
            document.getElementById("personalModal").style.display = "flex";
        }

        // Close Personal Information Modal
        function closePersonalModal() {
            document.getElementById("personalModal").style.display = "none";
        }


        // Save Personal Information
        function savePersonalInfo() {

            var data = {
                firstname: document.getElementById("editFirstName").value,
                lastname: document.getElementById("editLastName").value,
                birth: document.getElementById("editBirthDate").value,
                email: document.getElementById("editEmail").value,
                contact: document.getElementById("editPhone").value
            };

            saveUserData(data, function () {
                closePersonalModal();
            });
        }


        // Open Address Modal
        function openAddressModal() {
            document.getElementById("addressModal").style.display = "flex";
        }


        // Close Address Modal
        function closeAddressModal() {
            document.getElementById("addressModal").style.display = "none";
        }


        // Save Address
        function saveAddress() {

            var data = {
                country: document.getElementById("editCountry").value,
                city: document.getElementById("editCity").value,
                postal: document.getElementById("editPostal").value
            };

            saveUserData(data, function () {
                closeAddressModal();
            });
        }


        // Save data to update_user.php and refresh the profile page
        function saveUserData(fields, onSuccess) {

            var formData = new FormData();

            formData.append("firstname", document.getElementById("editFirstName").value);
            formData.append("lastname", document.getElementById("editLastName").value);
            formData.append("birth", document.getElementById("editBirthDate").value);
            formData.append("email", document.getElementById("editEmail").value);
            formData.append("contact", document.getElementById("editPhone").value);
            formData.append("country", document.getElementById("editCountry").value);
            formData.append("city", document.getElementById("editCity").value);
            formData.append("postal", document.getElementById("editPostal").value);

            fetch("update_user.php", {
                method: "POST",
                body: formData
            })
                .then(function (response) {
                    return response.json();
                })
                .then(function (result) {

                    if (result.success) {

                        if (onSuccess) {
                            onSuccess();
                        }

                        loadUserProfile();

                        var initial = (document.getElementById("editFirstName").value || "").charAt(0)
                            .toUpperCase();

                        document.getElementById("avatarInitial").textContent =
                            initial || "U";

                        document.querySelector(".profile-info h2").textContent =
                            document.getElementById("editFirstName").value +
                            " " +
                            document.getElementById("editLastName").value;

                    } else {

                        alert(result.message || "Failed to update profile.");
                    }

                })
                .catch(function () {
                    alert("Unable to reach the server.");
                });
        }


        // Close modal when clicking outside
        window.onclick = function (event) {

            if (event.target === document.getElementById("personalModal")) {
                closePersonalModal();
            }

            if (event.target === document.getElementById("addressModal")) {
                closeAddressModal();
            }

        }

