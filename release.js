
                    const searchInput = document.getElementById("searchInput");
                    const stockTableBody = document.getElementById("stockTableBody");


                    searchInput.addEventListener("input", function () {

                        const search = this.value.trim();

                        // Clear table
                        stockTableBody.innerHTML = "";


                        // Do not display anything until user searches
                        if (search === "") {

                            stockTableBody.innerHTML = `
                                <tr>
                                    <td colspan="6" style="text-align:center; padding:30px;">
                                        Search for an item to display stock.
                                    </td>
                                </tr>
                            `;

                            return;
                        }


                        // Search tb_stock through PHP
                        fetch("search_stock.php?search=" + encodeURIComponent(search))

                            .then(response => response.json())

                            .then(data => {

                                if (!Array.isArray(data) || data.length === 0) {

                                    stockTableBody.innerHTML = `
                                        <tr>
                                            <td colspan="6" style="text-align:center; padding:30px;">
                                                No matching stock item found.
                                            </td>
                                        </tr>
                                    `;

                                    return;
                                }


                                // Display matching records
                                data.forEach(stock => {

                                    let status = stock.stock_status || "";

                                    let statusClass = status
                                        .toLowerCase()
                                        .replace(/\s+/g, "-");


                                    let lastUpdated = "N/A";


                                    if (stock.last_updated) {

                                        const date = new Date(
                                            stock.last_updated.replace(" ", "T")
                                        );


                                        if (!isNaN(date)) {

                                            lastUpdated =
                                                date.toLocaleDateString("en-US", {
                                                    month: "short",
                                                    day: "2-digit",
                                                    year: "numeric"
                                                })
                                                + " "
                                                +
                                                date.toLocaleTimeString("en-US", {
                                                    hour: "2-digit",
                                                    minute: "2-digit"
                                                });

                                        }

                                    }


                                    const row = document.createElement("tr");


                                    // Make the entire row clickable
                                    row.style.cursor = "pointer";


                                    row.innerHTML = `

                                        <td>
                                            ${escapeHTML(stock.stock_id)}
                                        </td>

                                        <td>
                                            ${escapeHTML(stock.item_name)}
                                        </td>

                                        <td>
                                            ${escapeHTML(stock.category)}
                                        </td>

                                        <td>
                                            ${escapeHTML(stock.current_stock)}
                                        </td>

                                        <td>
                                            <span class="status-badge ${escapeHTML(statusClass)}">
                                                ${escapeHTML(status)}
                                            </span>
                                        </td>

                                        <td>
                                            ${escapeHTML(lastUpdated)}
                                        </td>

                                    `;


                                    /*
                                     * =========================================
                                     * CLICK TABLE ITEM
                                     * =========================================
                                     *
                                     * When the user clicks a table row:
                                     *
                                     * 1. Highlight the selected row.
                                     * 2. Put ONLY the Item Name into the
                                     *    Release Item form.
                                     * 3. Keep the stock ID hidden for the
                                     *    release_item.php backend.
                                     * 4. Scroll down to the release form.
                                     *
                                     */

                                    row.addEventListener("click", function () {

                                        // Remove previous selection
                                        document.querySelectorAll("#stockTableBody tr").forEach(r => {
                                            r.classList.remove("selected-row");
                                        });

                                        // Highlight selected row
                                        row.classList.add("selected-row");

                                        // Put selected item name into the release form
                                        document.getElementById("releaseItemName").value =
                                            stock.item_name;

                                        // Store stock ID in hidden field
                                        document.getElementById("releaseItemId").value =
                                            stock.stock_id;

                                        // Scroll to release form
                                        document.querySelector(".release-form-box").scrollIntoView({
                                            behavior: "smooth",
                                            block: "start"
                                        });

                                    });


                                    stockTableBody.appendChild(row);

                                });

                            })


                            .catch(error => {

                                console.error("Search error:", error);

                                stockTableBody.innerHTML = `
                                    <tr>
                                        <td colspan="6" style="text-align:center; padding:30px;">
                                            Unable to search stock.
                                        </td>
                                    </tr>
                                `;

                            });

                    });


                    // =========================================
                    // PREVENT HTML INJECTION
                    // =========================================
                    function escapeHTML(value) {

                        const div = document.createElement("div");

                        div.textContent = value ?? "";

                        return div.innerHTML;

                    }


        document.addEventListener('DOMContentLoaded', function () {
            var menuButton = document.querySelector('.page-title .menu-button');

            if (menuButton) {
                menuButton.addEventListener('click', function () {
                    document.body.classList.toggle('sidebar-hidden');
                });
            }
        });

        document.addEventListener('DOMContentLoaded', function () {

            var releaseForm = document.querySelector('.release-form-box form');
            var confirmModal = document.getElementById('confirmModal');

            var confirmItemName = document.getElementById('confirmItemName');
            var confirmReleaseDate = document.getElementById('confirmReleaseDate');
            var confirmQuantity = document.getElementById('confirmQuantity');
            var confirmClientName = document.getElementById('confirmClientName');
            var confirmTransport = document.getElementById('confirmTransport');

            var releaseItemName = document.getElementById('releaseItemName');
            var releaseDate = document.getElementById('releaseDate');
            var releaseQuantity = document.getElementById('releaseQuantity');
            var clientName = document.getElementById('clientName');
            var transportSelect = document.getElementById('transport');

            function closeConfirmModal() {
                confirmModal.style.display = 'none';
            }

            function showConfirmModal() {
                confirmItemName.textContent = releaseItemName.value || '-';
                confirmReleaseDate.textContent = releaseDate.value || '-';
                confirmQuantity.textContent = releaseQuantity.value || '-';
                confirmClientName.textContent = clientName.value || '-';
                confirmTransport.textContent =
                    (transportSelect && transportSelect.value)
                        ? transportSelect.value
                        : '-';

                confirmModal.style.display = 'flex';
            }

            if (releaseForm) {

                releaseForm.addEventListener('submit', function (event) {

                    if (!releaseForm.checkValidity()) {
                        return;
                    }

                    if (confirmModal.getAttribute('data-confirmed') === 'true') {
                        confirmModal.removeAttribute('data-confirmed');
                        return;
                    }

                    event.preventDefault();
                    showConfirmModal();
                });

            }

            document.getElementById('confirmReleaseBtn').addEventListener('click', function () {
                confirmModal.setAttribute('data-confirmed', 'true');
                document.getElementById('submitMode').value = 'release';
                releaseForm.submit();
            });

            document.getElementById('confirmModalRequest').addEventListener('click', function () {
                closeConfirmModal();
                document.getElementById('submitMode').value = 'request';
                releaseForm.submit();
            });
            document.getElementById('confirmModalClose').addEventListener('click', closeConfirmModal);

            confirmModal.addEventListener('click', function (event) {
                if (event.target === confirmModal) {
                    closeConfirmModal();
                }
            });

        });

