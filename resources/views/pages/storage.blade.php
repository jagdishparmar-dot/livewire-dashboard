<?php

use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Storage')] class extends Component
{
    //
}
?>

<div>
    <x-placeholder-page
        title="Storage"
        description="Upload files and manage buckets for this project."
        icon="archive-box"
    />
</div>
