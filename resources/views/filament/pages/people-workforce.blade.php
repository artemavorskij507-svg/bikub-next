@php($c = $this->getWorkerCounts())

<x-filament-panels::page>
    <x-admin-os.page-shell
        class="bkb-admin-shell"
        eyebrow="People and workforce"
        title="People & Workforce"
        subtitle="Applications, compliance documents, worker availability and payout readiness in one workforce control surface."
        :status="$c['eligible'].' dispatch eligible'"
        :primary-href="\App\Filament\Resources\WorkerProfiles\WorkerProfileResource::getUrl()"
        primary-label="Open worker profiles"
        :secondary-href="route('filament.admin.pages.dispatch-center')"
        secondary-label="Open Dispatch Center"
    >
        <section class="bkb-business-grid">
            @foreach([
                ['Users', 'users', 'review', 'All user accounts in the current runtime.'],
                ['Submitted applications', 'submitted', 'setup', 'Applications waiting for admin review.'],
                ['Pending invitations', 'pending_invitations', 'review', 'Worker account invitations still pending.'],
                ['Approved workers', 'approved', 'ready', 'Workers approved for operational use.'],
                ['Online workers', 'online', 'review', 'Presence state from real worker availability.'],
                ['Eligible workers', 'eligible', $c['eligible'] ? 'ready' : 'blocked', $c['eligible'] ? 'Dispatch can use at least one worker.' : 'No approved online capability-matched worker.'],
            ] as [$title, $key, $tone, $body])
                <x-admin-os.readiness-card :title="$title" :value="$c[$key]" :tone="$tone" :body="$body" />
            @endforeach
        </section>

        <x-admin-os.action-matrix :items="[
            ['name' => 'Review applications', 'status' => 'Real queue', 'tone' => $c['submitted'] ? 'review' : 'ready', 'requirement' => 'Submitted worker applications require Admin OS review.', 'blocker' => $c['submitted'] ? $c['submitted'].' application(s) waiting.' : 'No submitted applications.', 'url' => \App\Filament\Resources\WorkerApplications\WorkerApplicationResource::getUrl(), 'action' => 'Open applications'],
            ['name' => 'Review documents', 'status' => 'Compliance queue', 'tone' => $c['documents'] ? 'review' : 'ready', 'requirement' => 'Worker documents must be reviewed before eligibility.', 'blocker' => $c['documents'] ? $c['documents'].' document(s) pending.' : 'No pending worker documents.', 'url' => \App\Filament\Resources\WorkerDocuments\WorkerDocumentResource::getUrl(), 'action' => 'Open documents'],
            ['name' => 'Dispatch eligibility', 'status' => $c['eligible'] ? 'Ready' : 'Blocked', 'tone' => $c['eligible'] ? 'ready' : 'blocked', 'requirement' => 'Approved worker profile, availability and capability match.', 'blocker' => $c['eligible'] ? 'Eligible worker available.' : 'No approved online capability-matched worker.', 'url' => route('filament.admin.pages.dispatch-center'), 'action' => 'Open dispatch'],
        ]" />

        <section class="bkb-os-two-column">
            <article class="bkb-os-command-panel">
                <div class="bkb-section-heading"><div><p class="bkb-kicker">Worker documents</p><h2>Compliance readiness</h2></div></div>
                <div class="bkb-os-pipeline">
                    @foreach(['documents_missing'=>'Missing evidence','documents_ready'=>'Ready review','documents_approved'=>'Approved','documents_expired'=>'Expired','documents_rejected'=>'Rejected','documents'=>'Pending'] as $k=>$l)
                        <div><span>{{ $l }}</span><strong>{{ $c[$k] }}</strong></div>
                    @endforeach
                </div>
            </article>
            <article class="bkb-os-command-panel is-blocked">
                <div class="bkb-section-heading"><div><p class="bkb-kicker">Presence telemetry</p><h2>Real GPS only</h2></div></div>
                <p>Last seen: <strong>{{ $c['last_seen'] }}</strong>. Real browser pings: <strong>{{ $c['location_pings'] }}</strong>.</p>
                <ul class="bkb-blocked-list">
                    <li>Background GPS requires mobile HTTPS UAT.</li>
                    <li>Customer live tracking remains gated by visibility rules.</li>
                    <li>No fake online workers or fake map markers are created here.</li>
                </ul>
            </article>
        </section>
    </x-admin-os.page-shell>
</x-filament-panels::page>
