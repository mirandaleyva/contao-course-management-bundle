# Contao Course Management Bundle

```text
╭──────────────────────────────────────────────────────────────╮
│                                                              │
│   CONTΛO COURSE MANAGEMENT BUNDLE                            │
│   ───────────────────────────────                            │
│   Manage courses. Plan dates. Enable registrations.          │
│                                                              │
╰──────────────────────────────────────────────────────────────╯
```

A Contao bundle for managing courses, course dates, and date-specific registrations.

The bundle is designed for websites that offer courses or events with multiple sessions, for example:

- Prenatal classes
- Yoga or fitness courses
- Workshops
- Therapy, health, or training programs
- Courses with multiple dates, locations, or limited availability

---

## What does the bundle do?

The bundle extends Contao with a dedicated backend module **Courses**.

In the backend, courses can be created and managed. Each course can have multiple course dates. In the frontend, visitors can first view a course overview, then open the detail view of a course, and finally register for a specific course date.

The focus is on a clear separation between:

```text
Course list  →  Course details  →  Registration
```

This ensures a clean user flow and guarantees that each registration is tied to a specific date.

---

## Main Features

### Backend

- dedicated backend module **Courses**
- manage courses
- manage multiple course dates per course
- parent-child structure between course and course dates
- structured input fields for course data
- structured input fields for date and location data
- publish/unpublish courses and dates
- mark dates as fully booked
- assign an existing Contao form to a course

### Frontend

The bundle provides three frontend modules:

| Module                | Purpose                                              |
| --------------------- | ---------------------------------------------------- |
| `course_list`         | displays a course overview                           |
| `course_reader`       | displays the course detail view                      |
| `course_registration` | displays the registration for a selected course date |

---

## Requirements

| Component | Version  |
| --------- | -------- |
| PHP       | `^8.4`   |
| Contao    | `^5.7`   |
| Composer  | required |

---

## Installation

```bash
composer require mirandaleyva/contao-course-management-bundle
```

```bash
php vendor/bin/contao-console contao:migrate --no-interaction
php vendor/bin/contao-console cache:clear --env=prod
```

---

## License

MIT

---

## Author

Miranda Leyva
