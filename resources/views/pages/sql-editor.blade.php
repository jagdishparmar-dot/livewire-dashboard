<?php

use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('SQL Editor')] class extends Component
{
    //
}
?>

<div>
    <x-placeholder-page
        title="SQL Editor"
        description="Write and run SQL queries against your project database."
        icon="command-line"
    />
</div>
