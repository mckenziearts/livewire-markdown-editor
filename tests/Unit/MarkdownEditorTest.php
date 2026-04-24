<?php

declare(strict_types=1);

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mckenziearts\LivewireMarkdownEditor\Livewire\MarkdownEditor;

it('renders successfully', function (): void {
    Livewire\Livewire::test(MarkdownEditor::class)
        ->assertOk();
});

it('display correct editor content value', function (): void {
    Livewire\Livewire::test(MarkdownEditor::class)
        ->set('content', 'foo')
        ->assertSet('content', 'foo');
});

it('rejects html file uploads', function (): void {
    Storage::fake('local');

    $htmlFile = UploadedFile::fake()->createWithContent(
        'phishing.html',
        '<html><body>phishing</body></html>',
    );

    Livewire\Livewire::test(MarkdownEditor::class)
        ->set('attachments', [$htmlFile])
        ->assertHasErrors(['attachments.0']);

    expect(Storage::disk('local')->allFiles())->toBeEmpty();
});

it('rejects svg file uploads by default', function (): void {
    Storage::fake('local');

    $svgFile = UploadedFile::fake()->createWithContent(
        'payload.svg',
        '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>',
    );

    Livewire\Livewire::test(MarkdownEditor::class)
        ->set('attachments', [$svgFile])
        ->assertHasErrors(['attachments.0']);

    expect(Storage::disk('local')->allFiles())->toBeEmpty();
});

it('rejects files with disallowed extension even if renamed', function (): void {
    Storage::fake('local');

    $exeFile = UploadedFile::fake()->createWithContent(
        'malware.exe',
        'MZ binary content',
    );

    Livewire\Livewire::test(MarkdownEditor::class)
        ->set('attachments', [$exeFile])
        ->assertHasErrors(['attachments.0']);

    expect(Storage::disk('local')->allFiles())->toBeEmpty();
});

it('rejects files exceeding the configured max size', function (): void {
    Storage::fake('local');
    config()->set('livewire-markdown-editor.upload.max_size', 100);

    $largeImage = UploadedFile::fake()->image('large.png')->size(200);

    Livewire\Livewire::test(MarkdownEditor::class)
        ->set('attachments', [$largeImage])
        ->assertHasErrors(['attachments.0']);

    expect(Storage::disk('local')->allFiles())->toBeEmpty();
});

it('accepts valid image uploads and inserts sanitized markdown', function (): void {
    Storage::fake('local');

    $image = UploadedFile::fake()->image('photo.png');

    Livewire\Livewire::test(MarkdownEditor::class)
        ->set('attachments', [$image])
        ->assertHasNoErrors()
        ->assertSet('attachments', []);

    expect(Storage::disk('local')->allFiles())->toHaveCount(1);
});

it('sanitizes malicious filenames to prevent markdown breakout', function (): void {
    Storage::fake('local');

    $image = UploadedFile::fake()->image('evil](javascript:alert(1))![x.png');

    $component = Livewire\Livewire::test(MarkdownEditor::class)
        ->set('attachments', [$image])
        ->assertHasNoErrors();

    $content = (string) $component->get('content');

    expect($content)
        ->not->toContain('](javascript:')
        ->not->toContain(')![')
        ->not->toContain('](http://evil')
        ->toMatch('/^\n!\[[^\[\]\(\)]*\]\([^)]+\)\n$/');
});

it('generates a random filename on disk independent of client input', function (): void {
    Storage::fake('local');

    $image = UploadedFile::fake()->image('original-name.png');

    Livewire\Livewire::test(MarkdownEditor::class)
        ->set('attachments', [$image])
        ->assertHasNoErrors();

    $files = Storage::disk('local')->allFiles();

    expect($files)->toHaveCount(1);
    expect($files[0])->not->toContain('original-name');
    expect($files[0])->toEndWith('.png');
});
