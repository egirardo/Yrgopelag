<footer>
    <section class="footer-info">
        <div class="footer-info-container">
            <img class="footer-logo" src="../assets/images/logo.png">
            <h5>Rating: ☆ ☆ ☆</h5>
            <p>Sbargle aims to give you the best experience <em>humanly</em> possible. If there is anything Sbargle can improve upon please fill out a feedback form at the reception.</p>
        </div>
    </section>
    <section class="footer-contact">
        <div class="contact-container">
            <div class="address">
                <h5>Address</h5>
                <p>1312 Perfect Stay Lane</p>
                <p>Resort Quarters, 813 37</p>
                <p>Humanitopia Island</p>
            </div>
            <div class="phone">
                <h5>Phone</h5>
                <p>+77 854 378 0031</p>
            </div>
            <div class="email">
                <h5>Email</h5>
                <p>support@sbargles.lb</p>
            </div>
        </div>
    </section>
    <section class="footer-links">
        <div class="links-container">
            <h5>Links</h5>
            <a href="index.php">Home</a>
            <a href="about.php">About</a>
            <a href="index.php#booking-section">Book Your Stay</a>
            <span>&copy; Elsa Girardo 2026</span>
        </div>
    </section>
</footer>

<script src=" assets/scripts/script.js"></script>
<?php if (basename($_SERVER['PHP_SELF']) === 'book.php'): ?>
    <script src="assets/scripts/transfercode.js"></script>
    <script src="assets/scripts/booking-calculator.js"></script>
<?php endif; ?>
<script src="assets/scripts/slider.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
</body>

</html>