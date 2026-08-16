<?php

use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Logs')] class extends Component
{
    //
}
?>

<div>
    <x-placeholder-page
        title="Logs"
        description="Inspect API, auth, and function logs for this project."
        icon="document-text"
    />
</div>
