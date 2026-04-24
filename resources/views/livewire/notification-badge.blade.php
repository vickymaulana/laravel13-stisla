<span wire:poll.30s="refreshCount">
    @if ($count > 0)
        <span class="badge bg-danger">{{ $count }}</span>
    @endif
</span>
