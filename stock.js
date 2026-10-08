
        function openAddStockModal()
        {
            document.getElementById("item_name").value = "";
            document.getElementById("category").value = "";
            document.getElementById("supplier").value = "";
            document.getElementById("quantity").value = "";

            document.getElementById("modalTitle").textContent =
                "Add Stock";

            document.getElementById("modalDescription").textContent =
                "Enter the item information to add stock.";

            document.getElementById("modalSubmitButton").textContent =
                "+ Add Stock";

            document.getElementById("item_name").readOnly = false;
            document.getElementById("supplier").readOnly = false;

            document
                .getElementById("addStockModal")
                .classList.add("show");
        }

        function openEditStockModal(itemName, category, supplier)
        {
            document.getElementById("item_name").value = itemName;
            document.getElementById("category").value = category;
            document.getElementById("supplier").value = supplier;
            document.getElementById("quantity").value = "";

            document.getElementById("modalTitle").textContent =
                "Add Stock";

            document.getElementById("modalDescription").textContent =
                "Add more quantity to this existing item. Supplier is permanent.";

            document.getElementById("modalSubmitButton").textContent =
                "+ Add Stock";

            document.getElementById("item_name").readOnly = true;
            document.getElementById("supplier").readOnly = true;

            document
                .getElementById("addStockModal")
                .classList.add("show");
        }

        function closeAddStockModal()
        {
            document
                .getElementById("addStockModal")
                .classList.remove("show");
        }

        window.addEventListener("click", function(event)
        {
            const modal =
                document.getElementById("addStockModal");

            if (event.target === modal)
            {
                closeAddStockModal();
            }
        });

        function filterStock()
        {
            const searchInput =
                document.getElementById("searchInput");

            const categoryFilter =
                document.getElementById("categoryFilter");

            const statusFilter =
                document.getElementById("statusFilter");

            const search =
                searchInput.value.toLowerCase().trim();

            const selectedCategory =
                categoryFilter.value.toLowerCase().trim();

            const selectedStatus =
                statusFilter.value.toLowerCase().trim();

            const rows =
                document.querySelectorAll(
                    "#stockTable tbody tr"
                );

            rows.forEach(function(row)
            {
                const cells = row.cells;

                if (cells.length < 9)
                {
                    return;
                }

                const itemName =
                    cells[1].textContent
                        .toLowerCase()
                        .trim();

                const category =
                    cells[2].textContent
                        .toLowerCase()
                        .trim();

                const supplier =
                    cells[3].textContent
                        .toLowerCase()
                        .trim();

                const status =
                    cells[6].textContent
                        .toLowerCase()
                        .trim();

                const matchSearch =
                    search === "" ||
                    itemName.includes(search) ||
                    supplier.includes(search);

                const matchCategory =
                    selectedCategory === "" ||
                    category === selectedCategory;

                const matchStatus =
                    selectedStatus === "" ||
                    status === selectedStatus;

                if (
                    matchSearch &&
                    matchCategory &&
                    matchStatus
                )
                {
                    row.style.display = "";
                }
                else
                {
                    row.style.display = "none";
                }
            });
        }

        document
            .querySelector(".menu-button")
            .addEventListener("click", function () {
                document
                    .body
                    .classList
                    .toggle("sidebar-hidden");
            });

