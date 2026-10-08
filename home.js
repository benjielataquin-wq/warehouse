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

    var calendarData = JSON.parse(document.getElementById('calendarData').textContent);
    var releaseDates = calendarData.releaseDates;
    var releasesByDate = calendarData.releasesByDate;

    var monthNames = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];

    var calendarDate = new Date();
    calendarDate.setDate(1);

    function pad(num) {
        return num < 10 ? '0' + num : '' + num;
    }

    var today = new Date();

    var todayKey =
        today.getFullYear() + '-' +
        pad(today.getMonth() + 1) + '-' +
        pad(today.getDate());

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

            // "today" is applied independently so a day that ALSO has
            // release requests keeps its today highlight.
            if (key === todayKey) {
                cell.classList.add('today');
            }

            if (releaseDates[key]) {
                cell.classList.add('has-release');
                cell.title = releaseDates[key] + ' release request(s)';
                cell.setAttribute('data-date', key);

                var badge = document.createElement('span');
                badge.className = 'release-badge';
                badge.textContent = releaseDates[key];
                cell.appendChild(badge);
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
                '<div class="release-item-title">Release #' + escapeHtml(r.release_id) + '</div>' +
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
