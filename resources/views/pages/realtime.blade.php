<?php

use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Realtime')] class extends Component
{
    //
}
?>

<div>
    <x-placeholder-page
        title="Realtime"
        description="Subscribe to database changes and broadcast events."
        icon="signal"
    />
</div>
