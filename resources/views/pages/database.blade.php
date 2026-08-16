<?php

use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Database')] class extends Component
{
    //
}
?>

<div>
    <x-placeholder-page
        title="Database"
        description="Manage schemas, roles, extensions, and connection settings."
        icon="circle-stack"
    />
</div>
