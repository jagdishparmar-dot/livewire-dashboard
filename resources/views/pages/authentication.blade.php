<?php

use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Authentication')] class extends Component
{
    //
}
?>

<div>
    <x-placeholder-page
        title="Authentication"
        description="Configure users, providers, and session settings."
        icon="lock-closed"
    />
</div>
