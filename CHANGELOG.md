# Changelog

All notable changes to `livewire-markdown-editor` will be documented in this file.

## v1.3 - 2026-04-24

### ⚠️ Security

This release patches a **critical** arbitrary file upload vulnerability (CWE-434 + CWE-79) in the markdown editor's attachment handler. All users are encouraged to upgrade immediately.

Any authenticated user could upload HTML, SVG, JavaScript, PHP or other executable files to the configured disk. When the disk was a public cloud bucket (S3, Spaces, R2, Scaleway with `FILESYSTEM_DISK=s3`), uploaded files were served with a guessed `Content-Type`, enabling stored XSS, phishing page hosting, and malware distribution on the application's storage domain. A real-world exploit has been observed in production.

**What's fixed**

- Strict validation (`file`, `image`, `mimes:`, `extensions:`, `max:`) is now enforced on every attachment before any `store()` call
- Uploaded files are stored under a server-generated random filename with an extension derived from the actual file content (via `finfo`), never from the client-supplied name
- The original filename is sanitized (control characters and markdown breakout characters stripped, truncated to 100 chars) before being inserted into the markdown output
- The file input `accept` attribute is now derived from the config instead of a hard-coded allowlist that was never enforced server-side

**Breaking behavioral change**

Only image files are accepted by default. If your application relied on uploading PDF, DOC or other non-image types, publish the config and update the `upload` section:

```bash
php artisan vendor:publish --tag=livewire-markdown-editor-config
```

```php
// config/livewire-markdown-editor.php
'upload' => [
'max_size' => 4096,
'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'pdf', 'doc', 'docx'],
'images_only' => false,
],
```

If you do not use the attachment feature, disable it entirely via the `show-upload` prop:

```blade
<livewire:markdown-editor wire:model="content" :show-upload="false" />
```

### What's Changed

* fix: prevent arbitrary file upload in markdown editor by @mckenziearts in https://github.com/mckenziearts/livewire-markdown-editor/pull/12

**Full Changelog**: https://github.com/mckenziearts/livewire-markdown-editor/compare/v1.2...v1.3

## v1.2 - 2026-03-26

### What's Changed

* feat: Add laravel 13 support by @mckenziearts in https://github.com/mckenziearts/livewire-markdown-editor/pull/11

**Full Changelog**: https://github.com/mckenziearts/livewire-markdown-editor/compare/v1.1...v1.2

## v1.1 - 2026-02-02

### What's Changed

* chore(deps-dev): bump the php-dependencies group with 2 updates by @dependabot[bot] in https://github.com/mckenziearts/livewire-markdown-editor/pull/2
* chore(deps-dev): bump the php-dependencies group with 2 updates by @dependabot[bot] in https://github.com/mckenziearts/livewire-markdown-editor/pull/4
* chore(deps-dev): bump the php-dependencies group with 2 updates by @dependabot[bot] in https://github.com/mckenziearts/livewire-markdown-editor/pull/7
* chore(deps): bump @github/text-expander-element from 2.9.2 to 2.9.4 in the js-dependencies group by @dependabot[bot] in https://github.com/mckenziearts/livewire-markdown-editor/pull/5
* feat: add support to Livewire 4.x and remove dependabot github actions by @mckenziearts in https://github.com/mckenziearts/livewire-markdown-editor/pull/9
* chore: update tailwind classes by @mckenziearts in https://github.com/mckenziearts/livewire-markdown-editor/pull/10

### New Contributors

* @dependabot[bot] made their first contribution in https://github.com/mckenziearts/livewire-markdown-editor/pull/2

**Full Changelog**: https://github.com/mckenziearts/livewire-markdown-editor/compare/v1.0.1...v1.1

## v1.0.1 - 2026-01-07

### What's Changed

* chore: Update banner by @mckenziearts in https://github.com/mckenziearts/livewire-markdown-editor/pull/1

### New Contributors

* @mckenziearts made their first contribution in https://github.com/mckenziearts/livewire-markdown-editor/pull/1

**Full Changelog**: https://github.com/mckenziearts/livewire-markdown-editor/compare/v1.0...v1.0.1

## v1.0 - 2026-01-07

**Full Changelog**: https://github.com/mckenziearts/livewire-markdown-editor/commits/v1.0
