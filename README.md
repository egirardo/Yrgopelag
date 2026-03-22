# Yrgopelag 

A desktop-only showcase website for **Sbargle's Luxury Building** on the fictional island of Humanitopia.

**Live site:** [elsagirardo.com/Yrgopelag](https://elsagirardo.com/Yrgopelag)

---

## About

Yrgopelag is a multi-page PHP website built to present a luxury accommodation on the island of Humanitopia. Visitors can explore the property, learn more about it, and make a booking — all through a clean, desktop-optimised interface.

This project was created as a school assignment for the **WU25** class at [Yrgo](https://www.yrgo.se/).

---

## Tech Stack

| Technology | Purpose |
|---|---|
| PHP | Server-side templating and routing |
| SQL | Database (bookings / data storage) |
| SCSS / CSS | Styling and layout |
| JavaScript | Client-side interactivity |
| Composer | PHP dependency management |

---

## Project Structure

```
Yrgopelag/
├── app/          # Application logic (controllers, models, etc.)
├── assets/       # Static assets (images, fonts, icons)
├── scss/         # SCSS source files
├── views/        # PHP view templates
├── index.php     # Homepage
├── about.php     # About page
├── book.php      # Booking page
└── composer.json # PHP dependencies
```

---

## Pages

| Page | Description |
|---|---|
| `/index.php` | Landing page — hero section and property highlights |
| `/about.php` | About the building and its story |
| `/book.php` | Booking / contact form |

---

## Notes

- **Desktop only** — the site is not optimised for mobile or tablet viewports.
- SCSS is compiled to CSS; if you modify styles, recompile the SCSS source files in `/scss/`.

---

## License

This project is licensed under the [MIT License](LICENSE).

---

*Made by [Elsa Girardo](https://github.com/egirardo) · Yrgo WU25*
