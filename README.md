## About Doppar Blog

Doppar Blog is a lightweight publishing CMS built on top of the Doppar PHP framework — the official journal for the Doppar project, and a reference example of what you can build with it.

It ships with two sides:

- **A public blog** — a fast, editorial-style reading experience with a latest/featured stories feed, cursor-based pagination, individual post pages (cover image, reading time, view counts, tags, category, social sharing), and an author-driven design system built with Tailwind CSS.
- **An admin panel** (`/admin`) — full CRUD management for posts, categories, tags, media and users, a rich-text post editor (Tiptap) with image uploads and code blocks, per-user profiles, and two-factor authentication for account security.

## Requirements

- PHP >= 8.3 with the `pdo_sqlite` extension (the app uses SQLite by default)
- [Composer](https://getcomposer.org)
- Node.js >= 18 and npm

## Local Setup

1. Install PHP dependencies
   ```bash
   composer install
   ```

2. Create your environment file and app key
   ```bash
   cp .env.example .env
   php pool key:generate
   ```

3. Create the SQLite database and run migrations
   ```bash
   touch database/database.sqlite
   php pool migrate
   ```

4. Seed some sample content — posts, categories, tags and a user
   ```bash
   php pool db:seed
   ```

5. Install frontend dependencies
   ```bash
   npm install
   ```

6. Run the app. This needs **two terminals** running side by side:
   ```bash
   # Terminal 1 — PHP app server (http://localhost:8000)
   php pool server:start

   # Terminal 2 — Vite dev server (compiles Tailwind CSS/JS, hot reload)
   npm run dev
   ```

7. Open [http://localhost:8000](http://localhost:8000) in your browser.

> Steps 1–3 can also be run in one shot with `composer run setup`.
