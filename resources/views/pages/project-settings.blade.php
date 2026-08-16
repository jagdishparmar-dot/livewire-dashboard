<?php

use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Project Settings')] class extends Component
{
    //
}
?>

<div>
    <x-placeholder-page
        title="Project Settings"
        description="General project configuration, API keys, and team access."
        icon="cog-6-tooth"
    />
</div>
