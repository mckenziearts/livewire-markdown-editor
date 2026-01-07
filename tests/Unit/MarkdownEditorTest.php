<?php

declare(strict_types=1);

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
