<x-filament-panels::page>
<main class="bkb-admin-shell bkb-operator">
    <header class="bkb-operator-head"><div><h1>Audit Log</h1><p>Real model changes recorded by Spatie Activitylog.</p></div></header>
    <section class="bkb-os-card">
        <div class="bkb-table-wrap"><table class="bkb-ops-table"><thead><tr><th>Time</th><th>Actor</th><th>Action</th><th>Subject</th><th>Changes</th></tr></thead><tbody>
        @forelse($this->getActivities() as $activity)
            <tr><td>{{ $activity->created_at?->format('Y-m-d H:i:s') }}</td><td>{{ $activity->causer?->name ?? $activity->causer?->email ?? 'System' }}</td><td>{{ str($activity->event ?? $activity->description)->replace('_',' ')->title() }}</td><td>{{ class_basename($activity->subject_type ?? '') }} {{ $activity->subject_id }}</td><td><code>{{ json_encode($activity->changes(), JSON_UNESCAPED_SLASHES) }}</code></td></tr>
        @empty
            <tr><td colspan="5">No audit activities recorded yet. Existing domain events remain in their dedicated event tables.</td></tr>
        @endforelse
        </tbody></table></div>
    </section>
</main>
</x-filament-panels::page>
