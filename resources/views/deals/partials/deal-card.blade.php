<!-- Deal Card Component -->
<div class="card mb-2 shadow-sm deal-card" data-deal-id="{{ $deal->id }}" draggable="true">
    <div class="card-body p-2">
        <div class="d-flex justify-content-between align-items-start mb-1">
            <h6 class="mb-0 small font-weight-bold text-truncate" style="max-width: 100px;" title="{{ $deal->title }}">
                {{ $deal->title }}
            </h6>
            <span class="badge badge-primary">${{ number_format($deal->value, 0) }}</span>
        </div>
        <p class="mb-1 text-muted" style="font-size: 0.75rem;">
            <i class="fas fa-user fa-fw"></i> {{ $deal->customer->name ?? 'N/A' }}
        </p>
        <div class="d-flex justify-content-between align-items-center mt-2">
            <a href="{{ route('deals.show', $deal->id) }}" class="btn btn-xs btn-outline-info" title="View Details">
                <i class="fas fa-eye"></i>
            </a>
            @if($deal->isActive() && $deal->getNextStage())
                <form action="{{ route('deals.updateStage', $deal->id) }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="stage" value="{{ $deal->getNextStage() }}">
                    <button type="submit" class="btn btn-xs btn-success" title="Move to {{ \App\Models\Deal::STAGES[$deal->getNextStage()] ?? 'Next' }}">
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>

<style>
.btn-xs {
    padding: 0.15rem 0.35rem;
    font-size: 0.7rem;
}
</style>

