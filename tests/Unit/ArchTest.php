<?php

declare(strict_types=1);

use Mckenziearts\LivewireMarkdownEditor\Livewire\MarkdownEditor;

arch('package')
    ->expect(MarkdownEditor::class)
    ->toUseStrictTypes();
