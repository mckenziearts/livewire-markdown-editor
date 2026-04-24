<?php

declare(strict_types=1);

namespace Mckenziearts\LivewireMarkdownEditor\Livewire;

use Illuminate\Contracts\Filesystem\Cloud;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Extension\TaskList\TaskListExtension;
use League\CommonMark\MarkdownConverter;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Modelable;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Spatie\CommonMarkShikiHighlighter\HighlightCodeExtension;

final class MarkdownEditor extends Component
{
    use WithFileUploads;

    #[Modelable]
    public ?string $content = '';

    /** @var array<int, TemporaryUploadedFile> */
    public array $attachments = [];

    public string $placeholder = 'Leave a comment...';

    public int $rows = 10;

    public bool $showToolbar = true;

    public bool $showUpload = true;

    public ?string $class = null;

    #[Computed]
    public function parsedContent(): string
    {
        if (blank($this->content)) {
            return '';
        }

        $environment = new Environment([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        $environment->addExtension(new CommonMarkCoreExtension);
        $environment->addExtension(new GithubFlavoredMarkdownExtension);
        $environment->addExtension(new TableExtension);
        $environment->addExtension(new TaskListExtension);
        $environment->addExtension(new HighlightCodeExtension(theme: config('livewire-markdown-editor.theme'))); // @phpstan-ignore-line

        $converter = new MarkdownConverter(environment: $environment);

        return $converter->convert($this->content)->getContent();
    }

    /**
     * @return array<string, array<int, string|null>>
     */
    public function rules(): array
    {
        /** @var array{max_size: int, allowed_extensions: array<int, string>, images_only: bool} $config */
        $config = config('livewire-markdown-editor.upload');

        $extensions = implode(',', $config['allowed_extensions']);

        return [
            'attachments.*' => array_values(array_filter([
                'required',
                'file',
                $config['images_only'] ? 'image' : null,
                'mimes:'.$extensions,
                'extensions:'.$extensions,
                'max:'.$config['max_size'],
            ])),
        ];
    }

    public function updatedAttachments(): void
    {
        $this->validate();

        /** @var string $disk */
        $disk = config('livewire-markdown-editor.disk');

        foreach ($this->attachments as $attachment) {
            $extension = $attachment->extension();
            $path = $attachment->storeAs('', Str::random(40).'.'.$extension, $disk);

            if ($path === false) {
                continue;
            }

            $filesystem = Storage::disk($disk);
            /** @var Cloud $filesystem */
            $url = $filesystem->url($path);
            $filename = $this->sanitizeFilename($attachment->getClientOriginalName());

            if (str_starts_with((string) $attachment->getMimeType(), 'image/')) {
                $this->content .= "\n![{$filename}]({$url})\n";
            } else {
                $this->content .= "\n[{$filename}]({$url})\n";
            }
        }

        $this->attachments = [];
    }

    public function render(): View
    {
        return view('livewire-markdown-editor::livewire.markdown-editor');
    }

    private function sanitizeFilename(string $filename): string
    {
        $filename = preg_replace('/[\x00-\x1F\x7F]/u', '', $filename) ?? '';
        $filename = preg_replace('/[\[\]\(\)<>"\'\\\\`]/', '', $filename) ?? '';

        return Str::limit(trim($filename), 100, '');
    }
}
