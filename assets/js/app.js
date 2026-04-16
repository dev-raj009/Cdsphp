(function () {
  'use strict';

  /* ── Batch search ─────────────────────────── */
  var searchInput = document.getElementById('batchSearch');
  if (searchInput) {
    searchInput.addEventListener('input', function () {
      var q = this.value.toLowerCase().trim();
      filterCards(q);
    });
  }

  function filterCards(q) {
    var cards = document.querySelectorAll('.batch-card');
    var activeFilter = document.querySelector('.filter-btn.active');
    var src = activeFilter ? activeFilter.dataset.filter : 'all';

    cards.forEach(function (card) {
      var nameMatch = !q || card.dataset.name.includes(q);
      var srcMatch  = src === 'all' || card.dataset.source === src;
      card.style.display = (nameMatch && srcMatch) ? '' : 'none';
    });
  }

  function clearSearch() {
    if (searchInput) { searchInput.value = ''; filterCards(''); }
  }
  window.clearSearch = clearSearch;

  /* ── Filter tabs ──────────────────────────── */
  document.querySelectorAll('.filter-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      document.querySelectorAll('.filter-btn').forEach(function (b) {
        b.classList.remove('active');
      });
      this.classList.add('active');
      var q = searchInput ? searchInput.value.toLowerCase().trim() : '';
      filterCards(q);
    });
  });

  /* ── Scroll to search ─────────────────────── */
  window.scrollToSearch = function () {
    var el = document.getElementById('searchSection');
    if (el) { el.scrollIntoView({ behavior: 'smooth' }); }
  };

})();
