(() => {
    const form = document.getElementById('selection');
    if (!form) return;

    const roomPrice = parseInt(form.dataset.roomPrice, 10);

    const start = document.getElementById('start_date');
    const end   = document.getElementById('end_date');
    const boxes = form.querySelectorAll('input[name="activities[]"]');
    const total = document.getElementById('totalCost');

    function nights() {
        if (!start.value || !end.value) return 0;
        return Math.max(
            (new Date(end.value) - new Date(start.value)) / 86400000,
            0
        );
    }

    function activityTotal() {
        let sum = 0;
        boxes.forEach(b => b.checked && (sum += +b.dataset.price));
        return sum;
    }

    function update() {
        const t = nights() * roomPrice + activityTotal();
        total.value = t ? `$${t}` : '';
    }

    start.addEventListener('change', update);
    end.addEventListener('change', update);
    boxes.forEach(b => b.addEventListener('change', update));
})();