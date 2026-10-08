
    document.addEventListener('DOMContentLoaded', function () {
        var button = document.querySelector('.menu-button');

        if (button) {
            button.addEventListener('click', function () {
                document.body.classList.toggle('sidebar-hidden');
            });
        }
    });

    // =========================
    // CALENDAR
    // =========================

    var releaseDates = {"2026-10-02":2,"0021-12-11":1,"1111-04-05":1,"1111-11-11":1,"2025-05-02":1,"2026-02-20":1,"2026-09-20":1,"2026-09-21":1,"2026-09-26":2,"2026-09-27":1,"2026-10-15":1,"2212-11-12":1,"2222-02-22":3,"2333-02-02":1,"3223-03-22":1};
    var releasesByDate = {"0021-12-11":[{"release_id":8,"item_name":"fun","category":"Furniture","quantity":2,"requested_by":"ada","release_date":"0021-12-11","status":"Pending"}],"1111-04-05":[{"release_id":7,"item_name":"q21","category":"Electronics","quantity":4,"requested_by":"jhj","release_date":"1111-04-05","status":"Pending"}],"1111-11-11":[{"release_id":6,"item_name":"q21","category":"Electronics","quantity":35,"requested_by":"a1q","release_date":"1111-11-11","status":"Pending"}],"2025-05-02":[{"release_id":12,"item_name":"Outlets","category":"Electronics","quantity":150,"requested_by":"Benjie","release_date":"2025-05-02","status":"Pending"}],"2026-02-20":[{"release_id":16,"item_name":"xasas","category":"Electronics","quantity":223,"requested_by":"sasa","release_date":"2026-02-20","status":"Pending"}],"2026-09-20":[{"release_id":17,"item_name":"xasas","category":"Electronics","quantity":32,"requested_by":"czx","release_date":"2026-09-20","status":"Released"}],"2026-09-21":[{"release_id":18,"item_name":"q21","category":"Electronics","quantity":23,"requested_by":"12q","release_date":"2026-09-21","status":"Released"}],"2026-09-26":[{"release_id":20,"item_name":"q21","category":"Electronics","quantity":11,"requested_by":"cedric","release_date":"2026-09-26","status":"Released"},{"release_id":22,"item_name":"q21","category":"Electronics","quantity":20,"requested_by":"zar","release_date":"2026-09-26","status":"Released"}],"2026-09-27":[{"release_id":21,"item_name":"q21","category":"Electronics","quantity":10,"requested_by":"ben","release_date":"2026-09-27","status":"Released"}],"2026-10-15":[{"release_id":23,"item_name":"q21","category":"Electronics","quantity":21,"requested_by":"2swq","release_date":"2026-10-15","status":"Released"}],"2212-11-12":[{"release_id":15,"item_name":"xasas","category":"Electronics","quantity":22,"requested_by":"wdad","release_date":"2212-11-12","status":"Cancelled"}],"2222-02-22":[{"release_id":9,"item_name":"fun","category":"Furniture","quantity":22,"requested_by":"asasa","release_date":"2222-02-22","status":"Pending"},{"release_id":13,"item_name":"q21","category":"Electronics","quantity":50,"requested_by":"kail","release_date":"2222-02-22","status":"Pending"},{"release_id":14,"item_name":"q21","category":"Electronics","quantity":2,"requested_by":"ada","release_date":"2222-02-22","status":"Pending"}],"2333-02-02":[{"release_id":2,"item_name":"xasas","category":"Electronics","quantity":2,"requested_by":"saaax","release_date":"2333-02-02","status":"Pending"}],"3223-03-22":[{"release_id":3,"item_name":"xasas","category":"Electronics","quantity":2,"requested_by":"1s","release_date":"3223-03-22","status":"Pending"}]};

    var monthNames = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];

    var calendarDate = new Date();
    calendarDate.setDate(1);

    function pad(num) {
        return num < 10 ? '0' + num : '' + num;
    }

    function renderCalendar() {
        var year = calendarDate.getFullYear();
        var month = calendarDate.getMonth();

        document.getElementById('calendarMonth').textContent =
            monthNames[month] + ' ' + year;

        var grid = document.getElementById('calendarGrid');
        grid.innerHTML = '';

        var firstDay = new Date(year, month, 1).getDay();
        var daysInMonth = new Date(year, month + 1, 0).getDate();

        for (var i = 0; i < firstDay; i++) {
            var empty = document.createElement('div');
            empty.className = 'calendar-day empty';
            grid.appendChild(empty);
        }

        for (var day = 1; day <= daysInMonth; day++) {
            var key = year + '-' + pad(month + 1) + '-' + pad(day);

            var cell = document.createElement('div');
            cell.className = 'calendar-day';

            var num = document.createElement('span');
            num.className = 'day-number';
            num.textContent = day;
            cell.appendChild(num);

            if (releaseDates[key]) {
                cell.classList.add('has-release');
                cell.title = releaseDates[key] + ' release request(s)';
                cell.setAttribute('data-date', key);

                var badge = document.createElement('span');
                badge.className = 'release-badge';
                badge.textContent = releaseDates[key];
                cell.appendChild(badge);
            } else if (key === todayKey) {
                cell.classList.add('today');
            }

            grid.appendChild(cell);
        }

        var releaseCells = grid.querySelectorAll('.calendar-day.has-release');

        for (var j = 0; j < releaseCells.length; j++) {
            releaseCells[j].addEventListener('click', function () {
                openReleaseModal(this.getAttribute('data-date'));
            });
        }
    }

    function openReleaseModal(dateKey) {
        var releases = releasesByDate[dateKey] || [];

        if (releases.length === 0) {
            return;
        }

        document.getElementById('releaseModalDate').textContent = 'Releases on ' + dateKey;

        var list = document.getElementById('releaseInfoList');
        list.innerHTML = '';

        for (var i = 0; i < releases.length; i++) {
            var r = releases[i];

            var item = document.createElement('div');
            item.className = 'release-info-item';

            item.innerHTML =
                '<div class="release-item-title">Release #' + r.release_id + '</div>' +
                '<div class="release-fields">' +
                    '<div class="release-field">' +
                        '<label>Item Name</label>' +
                        '<input type="text" readonly value="' + escapeHtml(r.item_name) + '">' +
                    '</div>' +
                    '<div class="release-field">' +
                        '<label>Category</label>' +
                        '<input type="text" readonly value="' + escapeHtml(r.category) + '">' +
                    '</div>' +
                    '<div class="release-field">' +
                        '<label>Quantity</label>' +
                        '<input type="text" readonly value="' + escapeHtml(r.quantity) + '">' +
                    '</div>' +
                    '<div class="release-field">' +
                        '<label>Requested By</label>' +
                        '<input type="text" readonly value="' + escapeHtml(r.requested_by) + '">' +
                    '</div>' +
                    '<div class="release-field">' +
                        '<label>Release Date</label>' +
                        '<input type="text" readonly value="' + escapeHtml(r.release_date) + '">' +
                    '</div>' +
                '</div>' +
                '<div class="release-footer">' +
                    getStatusChipHtml(r) +
                '</div>';

            list.appendChild(item);
        }

        document.getElementById('releaseModal').style.display = 'flex';
    }

    function getStatusChipHtml(r) {
        if (r.status === 'Cancelled') {
            return '<span class="release-status-chip cancelled">Cancelled</span>';
        }

        var isReleased = r.status === 'Released';
        var status = isReleased ? 'Completed' : 'Pending';
        var cls = isReleased ? 'released' : 'pending';

        return '<span class="release-status-chip ' + cls + '">' + status + '</span>';
    }

    function closeReleaseModal() {
        document.getElementById('releaseModal').style.display = 'none';
    }

    function escapeHtml(str) {
        var s = String(str == null ? '' : str);
        return s.replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
    }

    document.getElementById('closeReleaseModal').addEventListener('click', closeReleaseModal);

    var releaseModal = document.getElementById('releaseModal');

    releaseModal.addEventListener('click', function (event) {
        if (event.target === releaseModal) {
            closeReleaseModal();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeReleaseModal();
        }
    });

    var today = new Date();
    var todayKey =
        today.getFullYear() + '-' +
        pad(today.getMonth() + 1) + '-' +
        pad(today.getDate());

    document.getElementById('prevMonth').addEventListener('click', function () {
        calendarDate.setMonth(calendarDate.getMonth() - 1);
        renderCalendar();
    });

    document.getElementById('nextMonth').addEventListener('click', function () {
        calendarDate.setMonth(calendarDate.getMonth() + 1);
        renderCalendar();
    });

    document.getElementById('todayBtn').addEventListener('click', function () {
        var now = new Date();
        calendarDate = new Date(now.getFullYear(), now.getMonth(), 1);
        renderCalendar();
    });

    renderCalendar();
