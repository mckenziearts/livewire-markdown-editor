# Laravel Markdown Editor

<p>
    <img src="art/banner.png" alt="Markdown Livewire Banner" />
</p>

<p>
    <a href="https://github.com/mckenziearts/livewire-markdown-editor/actions"><img src="https://github.com/mckenziearts/livewire-markdown-editor/actions/workflows/ci.yml/badge.svg" alt="Build Status"></a>
    <a href="https://packagist.org/packages/mckenziearts/livewire-markdown-editor"><img src="https://img.shields.io/packagist/dt/mckenziearts/livewire-markdown-editor" alt="Total Downloads"></a>
    <a href="https://packagist.org/packages/mckenziearts/livewire-markdown-editor"><img src="https://img.shields.io/packagist/v/mckenziearts/livewire-markdown-editor" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/mckenziearts/livewire-markdown-editor"><img src="https://img.shields.io/packagist/l/mckenziearts/livewire-markdown-editor" alt="License"></a>
</p>

GitHub-style Markdown editor for Laravel with Livewire and Alpine.js. This module provides a complete, standalone Markdown editing experience with full dark mode support.

## Dependencies

- Laravel 11+
- Livewire 3.6+
- Tailwind CSS 4.1
- League CommonMark
- GitHub Markdown Toolbar Element
- GitHub Text Expander Element

## Features

- 🎨 GitHub-style toolbar with all formatting options
- 📝 Live markdown preview
- 🌓 Full dark mode support
- 📎 File upload with automatic Markdown insertion
- ✨ GitHub Flavored Markdown (GFM) support
- 🔖 Spatie Shiki Highlight code blocks
- 📋 Tables, task lists, and more
- 🔄 Livewire integration with two-way binding
- 🎯 Multiple editor instances support

## Installation

Livewire Markdown editor can be installed via composer from your project root:

```bash
composer require mckenziearts/livewire-markdown-editor
```

Include the Markdown formatting buttons for text inputs Editor into your project:

```bash
npm install --save @github/markdown-toolbar-element @github/text-expander-element
```

### 2. Load assets

Add the module's JavaScript to your main `resources/js/app.js`:

```js
import '../../vendor/mckenziearts/livewire-markdown-editor/resources/js/markdown-editor.js';
```

And the CSS file to your main `resources/css/app.css`:

```css
@import "../../vendor/mckenziearts/livewire-markdown-editor/resources/css/markdown-editor.css";
```

Then build:

```bash
npm run build
```

### 3. Register the module

The service provider is auto-discovered via Laravel's package discovery.

## Usage

### Basic Usage

```blade
<livewire:markdown-editor wire:model="content" />
```

### With Custom Configuration

```blade
<livewire:markdown-editor
    wire:model="comment"
    placeholder="Write your comment..."
    :rows="15"
    :show-toolbar="true"
    :show-upload="true"
/>
```

### In Livewire Components

```php
use Livewire\Component;

class CreatePost extends Component
{
    public string $content = '';

    public function save()
    {
        $this->validate([
            'content' => 'required|min:10',
        ]);

        // $this->content contains the markdown
    }

    public function render()
    {
        return view('livewire.create-post');
    }
}
```

```blade
<div>
    <livewire:markdown-editor wire:model="content" />

    <button wire:click="save">Save</button>
</div>
```

## Component Properties

| Property      | Type   | Default                | Description                                  |
|---------------|--------|------------------------|----------------------------------------------|
| `content`     | string | `''`                   | The markdown content (use with `wire:model`) |
| `placeholder` | string | `'Leave a comment...'` | Textarea placeholder text                    |
| `class`       | string | `null`                 | Textarea custom classes                      |
| `rows`        | int    | `10`                   | Number of textarea rows                      |
| `showToolbar` | bool   | `true`                 | Show/hide the markdown toolbar               |
| `showUpload`  | bool   | `true`                 | Show/hide the file upload button             |

## Toolbar Features

- **Heading** - Insert heading
- **Bold** - Make text bold
- **Italic** - Make text italic
- **Quote** - Insert blockquote
- **Code** - Insert code block
- **Link** - Insert link
- **Unordered List** - Insert bullet list
- **Ordered List** - Insert numbered list
- **Task List** - Insert checklist
- **File Upload** - Upload and insert files/images

## File Uploads

Files are automatically uploaded to the configured disk when selected. Images are inserted as `![filename](url)` and other files as `[filename](url)`.

Make sure your storage is properly configured:

```bash
php artisan storage:link
```

### Security

To prevent arbitrary file upload vulnerabilities (stored XSS, phishing page hosting, malware distribution), only images are accepted by default. Uploaded files are stored under a randomly generated filename with the validated extension, and the original client-provided filename is sanitized before being inserted into the markdown output.

You can customize the allowed file types and max size via the `upload` config key:

```php
// config/livewire-markdown-editor.php
'upload' => [
    'max_size' => 4096, // kilobytes
    'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif'],
    'images_only' => true,
],
```

If you need to allow non-image files, extend `allowed_extensions` and set `images_only` to `false`. When using a public cloud disk (S3, Spaces, R2, Scaleway), review the bucket policy to ensure non-whitelisted Content-Types cannot be served inline.

If you do not use file uploads at all, disable the feature entirely:

```blade
<livewire:markdown-editor
    wire:model="content"
    :show-upload="false"
/>
```

## Markdown Support

The editor supports full GitHub Flavored Markdown including:

- Headings
- Bold, italic, strikethrough
- Links and images
- Code blocks with syntax highlighting
- Task lists
- Blockquotes
- Horizontal rules

## Dark Mode

Dark mode is fully supported and automatically follows your Tailwind CSS dark mode configuration.

## Customization

### Publishing Views

```bash
php artisan vendor:publish --tag=livewire-markdown-editor-views
```

Views will be published to `resources/views/vendor/livewire-markdown-editor/`.

### Publishing Assets

```bash
php artisan vendor:publish --tag=livewire-markdown-editor-assets
```

## License

Distributed under the MIT license. See LICENSE for details.
