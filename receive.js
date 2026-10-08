
        const releaseTableBody =
            document.getElementById("releaseTableBody");

        const searchInput =
            document.getElementById("searchInput");

        let releases = [];


        // =========================================
        // LOAD RELEASE DATA
        // =========================================

        function loadReleases() {

            fetch("display_release.php")

                .then(response => {

                    if (!response.ok) {

                        throw new Error(
                            "Failed to load release data."
                        );

                    }

                    return response.json();

                })

                .then(data => {

                    releases = Array.isArray(data) ? data : [];

                    displayReleases(releases);

                })

                .catch(error => {

                    console.error(
                        "Release loading error:",
                        error
                    );

                    releaseTableBody.innerHTML = `

                        <tr>

                            <td colspan="9"
                                style="text-align:center; padding:30px;">

                                Unable to load release information.

                            </td>

                        </tr>

                    `;

                });

        }


        // =========================================
        // DISPLAY RELEASES
        // =========================================

        function displayReleases(data) {

            releaseTableBody.innerHTML = "";


            // =====================================
            // NO DATA
            // =====================================

            if (!Array.isArray(data) || data.length === 0) {

                releaseTableBody.innerHTML = `

                    <tr>

                        <td colspan="9"
                            style="text-align:center; padding:30px;">

                            No release records found.

                        </td>

                    </tr>

                `;

                return;

            }


            // =====================================
            // DISPLAY EACH RELEASE
            // =====================================

            data.forEach(release => {

                const row =
                    document.createElement("tr");

                row.innerHTML = `

                    <td>
                        ${escapeHTML(release.release_id)}
                    </td>

                    <td>
                        ${escapeHTML(release.item_name)}
                    </td>

                    <td>
                        ${escapeHTML(release.category)}
                    </td>

                    <td>
                        ${escapeHTML(release.quantity)}
                    </td>

                    <td>
                        ${escapeHTML(release.requested_by)}
                    </td>

                    <td>
                        ${escapeHTML(release.transport || "-")}
                    </td>

                    <td>
                        ${escapeHTML(formatDate(release.release_date))}
                    </td>

                    <td>

                        ${getStatusChip(release.status)}

                    </td>

                    <td>

                        ${
                            release.status === "Released" ||
                            release.status === "Cancelled"
                                ? ""
                                : `
                                    <button
                                        type="button"
                                        class="release-btn"
                                        onclick="releaseRequest(${escapeHTML(release.release_id)})">

                                        Release

                                    </button>

                                    <button
                                        type="button"
                                        class="cancel-btn"
                                        onclick="cancelRequest(${escapeHTML(release.release_id)})">

                                        Cancel

                                    </button>
                                `
                        }

                    </td>

                `;

                releaseTableBody.appendChild(row);

            });

        }


        // =========================================
        // SEARCH
        // =========================================

        searchInput.addEventListener(
            "input",
            function () {

                const search =
                    this.value.trim().toLowerCase();

                if (search === "") {

                    displayReleases(releases);

                    return;

                }

                const filtered =
                    releases.filter(release => {

                        return (
                            String(
                                release.release_id
                            )
                                .toLowerCase()
                                .includes(search)
                            ||
                            String(
                                release.item_name
                            )
                                .toLowerCase()
                                .includes(search)
                            ||
                            String(
                                release.category
                            )
                                .toLowerCase()
                                .includes(search)
                            ||
                            String(
                                release.requested_by
                            )
                                .toLowerCase()
                                .includes(search)
                            ||
                            String(
                                release.transport || ""
                            )
                                .toLowerCase()
                                .includes(search)
                        );

                    });

                displayReleases(filtered);

            }
        );


        // =========================================
        // FORMAT DATE
        // =========================================

        function formatDate(dateValue) {

            if (!dateValue) {

                return "N/A";

            }

            const date =
                new Date(
                    dateValue.replace(" ", "T")
                );

            if (isNaN(date.getTime())) {

                return dateValue;

            }

            return date.toLocaleDateString(
                "en-US",
                {
                    month: "short",
                    day: "2-digit",
                    year: "numeric"
                }
            );

        }


        // =========================================
        // HTML PROTECTION
        // =========================================

        function escapeHTML(value) {

            const div =
                document.createElement("div");

            div.textContent =
                value ?? "";

            return div.innerHTML;

        }


        // =========================================
        // STATUS CHIP
        // =========================================

        function getStatusChip(status) {

            if (status === "Released") {

                return `

                    <span class="status-chip status-released">

                        Released

                    </span>

                `;

            }

            if (status === "Cancelled") {

                return `

                    <span class="status-chip status-cancelled">

                        Cancelled

                    </span>

                `;

            }

            return `

                <span class="status-chip status-pending">

                    Pending

                </span>

            `;

        }


        // =========================================
        // RELEASE REQUEST
        // =========================================

        function releaseRequest(releaseId) {

            if (!confirm("Mark this release request as Released?")) {

                return;

            }

            fetch("release_action.php", {

                method: "POST",

                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },

                body:
                    "action=release&release_id=" +
                    encodeURIComponent(releaseId)

            })

                .then(response => response.json())

                .then(data => {

                    if (data.success) {

                        alert(data.message);

                        loadReleases();

                    } else {

                        alert(data.message || "Release failed.");

                    }

                })

                .catch(() => {

                    alert("Unable to reach the server.");

                });

        }


        // =========================================
        // CANCEL REQUEST
        // =========================================

        function cancelRequest(releaseId) {

            if (!confirm("Cancel this release request?")) {

                return;

            }

            fetch("release_action.php", {

                method: "POST",

                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },

                body:
                    "action=cancel&release_id=" +
                    encodeURIComponent(releaseId)

            })

                .then(response => response.json())

                .then(data => {

                    if (data.success) {

                        alert(data.message);

                        loadReleases();

                    } else {

                        alert(data.message || "Cancel failed.");

                    }

                })

                .catch(() => {

                    alert("Unable to reach the server.");

                });

        }


        // =========================================
        // LOAD DATA WHEN PAGE OPENS
        // =========================================

        loadReleases();


        // =========================================
        // MENU BUTTON (HAMBURGER)
        // =========================================

        const menuButton =
            document.querySelector(".menu-button");

        if (menuButton) {

            menuButton.addEventListener("click", function () {

                document.body.classList.toggle(
                    "sidebar-hidden"
                );

            });

        }

