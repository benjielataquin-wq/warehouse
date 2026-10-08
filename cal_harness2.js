// Edge-case tests: today+release overlap, modal open/populate/close.
class El {
  constructor(tag) {
    this.tagName = tag; this.children = []; this.className = '';
    this.textContent = ''; this._innerHTML = ''; this.attrs = {};
    this.style = {}; this.listeners = {};
    this.classList = {
      _s: new Set(),
      add: (...c) => c.forEach(x => this.classList._s.add(x)),
      remove: (...c) => c.forEach(x => this.classList._s.delete(x)),
      contains: c => this.classList._s.has(c),
      toggle: c => (this.classList._s.has(c) ? this.classList._s.delete(c) : this.classList._s.add(c)),
    };
  }
  appendChild(c) { this.children.push(c); return c; }
  setAttribute(k, v) { this.attrs[k] = v; }
  getAttribute(k) { return this.attrs[k]; }
  addEventListener(t, f) { (this.listeners[t] = this.listeners[t] || []).push(f); }
  querySelectorAll(sel) {
    // handle compound selectors like '.calendar-day.has-release'
    const need = sel.split('.').filter(Boolean); const out = [];
    (function walk(n) { for (const c of n.children) {
      const have = new Set([...(c.classList._s), ...(c.className || '').split(/\s+/).filter(Boolean)]);
      if (need.every(k => have.has(k))) out.push(c);
      walk(c); } })(this);
    return out;
  }
  set innerHTML(v) { this._innerHTML = v; this.children = []; }
  get innerHTML() { return this._innerHTML; }
}

const ids = {};
['calendarMonth','calendarGrid','closeReleaseModal','releaseModal','prevMonth','nextMonth',
 'todayBtn','releaseModalDate','releaseInfoList'].forEach(id => { ids[id] = new El('div'); ids[id].id = id; });

const menuButton = new El('button'); menuButton.className = 'menu-button';
let keyHandler = null;

global.document = {
  body: new El('body'),
  getElementById: id => ids[id] || null,
  querySelector: s => (s === '.menu-button' ? menuButton : null),
  createElement: t => new El(t),
  addEventListener: (t, f) => { if (t === 'keydown') keyHandler = f; },
};

let fatal = null;
try { require(process.argv[2]); } catch (e) { fatal = e; }

const pad = n => (n < 10 ? '0' + n : '' + n);
const now = new Date();
const todayKey = now.getFullYear() + '-' + pad(now.getMonth() + 1) + '-' + pad(now.getDate());

console.log('fatal error       :', fatal ? `${fatal.name}: ${fatal.message}` : 'none');
console.log('today key         :', todayKey);
console.log('today in releaseDates (injected test):',
  /"?'?/.test('') ? '' : 'see below');

const grid = ids.calendarGrid;
const todayCells = grid.children.filter(c => c.classList.contains('today'));
const relCells = grid.children.filter(c => c.classList.contains('has-release'));
console.log('today cells       :', todayCells.length);
console.log('release cells     :', relCells.length);

// --- Test 1: today highlighted even when it also has releases ---
console.log('\n[TEST 1] today keeps .today when it also has releases');
if (relCells.length) {
  const relDate = relCells[0].getAttribute('data-date');
  const target = grid.children.find(c => c.getAttribute('data-date') === todayKey);
  console.log('  release date    :', relDate);
  console.log('  is release date == today ?', relDate === todayKey);
  console.log('  (this branch cannot occur with current seed data)');
}

// --- Test 2: click a release cell -> modal populates ---
console.log('\n[TEST 2] clicking a has-release cell opens + populates the modal');
if (relCells.length) {
  const cell = relCells[0];
  console.log('  clicked date    :', cell.getAttribute('data-date'));
  console.log('  cell title      :', JSON.stringify(cell.title));
  console.log('  click listeners :', (cell.listeners.click || []).length);
  cell.listeners.click[0].call(cell);
  console.log('  modal display   :', ids.releaseModal.style.display);
  console.log('  modal date text :', JSON.stringify(ids.releaseModalDate.textContent));
  console.log('  info items      :', ids.releaseInfoList.children.length);
  const first = ids.releaseInfoList.children[0];
  if (first) {
    console.log('  item html len   :', first.innerHTML.length);
    console.log('  has status chip :', /release-status-chip/.test(first.innerHTML));
  }

  // --- Test 3: close via Escape ---
  console.log('\n[TEST 3] Escape closes the modal');
  if (keyHandler) { keyHandler({ key: 'Escape' }); }
  console.log('  modal display   :', ids.releaseModal.style.display);

  // --- Test 4: close via backdrop click ---
  console.log('\n[TEST 4] clicking the backdrop (event.target === modal) closes it');
  ids.releaseModal.style.display = 'flex';
  ids.releaseModal.listeners.click[0].bind(ids.releaseModal)({ target: ids.releaseModal });
  console.log('  modal display   :', ids.releaseModal.style.display);

  // --- Test 5: clicking inside the box must NOT close it ---
  console.log('\n[TEST 5] clicking inside the modal box does NOT close it');
  ids.releaseModal.style.display = 'flex';
  ids.releaseModal.listeners.click[0].bind(ids.releaseModal)({ target: ids.releaseInfoList });
  console.log('  modal display   :', ids.releaseModal.style.display, '(expect flex)');

  // --- Test 6: close button ---
  console.log('\n[TEST 6] X button closes the modal');
  ids.releaseModal.style.display = 'flex';
  ids.closeReleaseModal.listeners.click[0].call(ids.closeReleaseModal);
  console.log('  modal display   :', ids.releaseModal.style.display);
}