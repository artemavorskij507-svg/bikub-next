@php($services = $this->getScenarioModules())
@php($counts = $this->getCatalogCounts())
@php($links = $this->getResourceLinks())

<x-filament-panels::page>
    <x-admin-os.page-shell
        class="bkb-admin-shell"
        eyebrow="Service catalog"
        title="Services"
        subtitle="Delivery, moving, handyman and local assistance scenarios with public checkout readiness and pricing boundaries."
        :status="($counts['active'] ?? 0).' active scenario(s)'"
        :primary-href="$links['scenarios']"
        primary-label="Open service scenarios"
        :secondary-href="$links['categories']"
        secondary-label="Open categories"
    >
        <section class="bkb-business-grid">
            @foreach([
                ['Categories', 'categories', 'review', 'Service categories visible to content and checkout.'],
                ['Active scenarios', 'active', ($counts['active'] ?? 0) ? 'ready' : 'blocked', 'Active scenarios power public request flows.'],
                ['Draft scenarios', 'draft', 'setup', 'Drafts are not production promises.'],
                ['Fields', 'fields', 'review', 'Form fields define customer intake.'],
                ['Configured', 'configured', 'ready', 'Scenarios with active fields.'],
                ['Missing fields', 'missing_fields', ($counts['missing_fields'] ?? 0) ? 'blocked' : 'ready', 'Active scenarios without fields cannot be trusted for checkout.'],
            ] as [$title, $key, $tone, $body])
                <x-admin-os.readiness-card :title="$title" :value="$counts[$key] ?? 0" :tone="$tone" :body="$body" />
            @endforeach
        </section>

        <section class="bkb-table-wrap bkb-readiness-table">
            <h2>Service readiness table</h2>
            <table class="bkb-ops-table">
                <thead>
                    <tr>
                        <th>Scenario</th>
                        <th>Status</th>
                        <th>Fields</th>
                        <th>Pricing</th>
                        <th>Payment</th>
                        <th>Tracking</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($services as $s)
                        <tr>
                            <td><strong>{{ $s['code'] }}</strong><small>{{ $s['label'] }} · {{ $s['scope'] }}</small></td>
                            <td><span class="bkb-status-chip">{{ $s['status'] }}</span></td>
                            <td>{{ $s['fields'] }}</td>
                            <td>{{ $s['pricing'] }}</td>
                            <td>{{ $s['payment'] }}</td>
                            <td>{{ $s['tracking'] }}</td>
                            <td><a href="{{ $s['edit_url'] }}">Edit</a> · <a href="{{ $s['url'] }}">Public</a> · <a href="{{ $s['request_url'] }}">Request</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7"><x-admin-os.empty-state title="No service scenarios." body="Create a real scenario before advertising a checkout route." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        <x-admin-os.action-matrix :items="[
            ['name' => 'Delivery checkout', 'status' => ($counts['active'] ?? 0) ? 'Review' : 'Blocked', 'tone' => ($counts['active'] ?? 0) ? 'review' : 'blocked', 'requirement' => 'Published service scenario, active fields and pricing boundary.', 'blocker' => ($counts['missing_fields'] ?? 0) ? 'Some active scenarios have no active fields.' : 'Payment provider still not connected for production capture.', 'url' => $services[0]['request_url'] ?? '', 'action' => 'Open request'],
            ['name' => 'Pricing rules', 'status' => 'Real resource', 'tone' => 'review', 'requirement' => 'Pricing rules must be active before quote automation can be trusted.', 'blocker' => 'Manual review remains honest where pricing is missing.', 'url' => \App\Filament\Resources\PricingRules\PricingRuleResource::getUrl(), 'action' => 'Open pricing'],
            ['name' => 'Live tracking claim', 'status' => 'Blocked', 'tone' => 'blocked', 'requirement' => 'Real worker GPS from mobile HTTPS.', 'blocker' => 'No fake map markers or route polylines are allowed.', 'url' => route('filament.admin.pages.live-operations-map'), 'action' => 'Open Live Map'],
        ]" />
    </x-admin-os.page-shell>
</x-filament-panels::page>
