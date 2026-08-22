<?php

namespace App\Services\Deeplink;

use App\Enums\InvitationStatus;
use App\Models\Event;
use App\Models\EventRegistrationLink;
use App\Models\Invitation;
use App\Models\RegistrationLinkHit;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RegistrationLinkMetricsService
{
    private const SERIES_DAYS = 30;

    /**
     * @return array{
     *   active_link: EventRegistrationLink|null,
     *   total_views: int,
     *   unique_visitors: int,
     *   devices: array<string, int>,
     *   device_pct: array<string, float>,
     *   os: array<string, int>,
     *   store_clicks: array{play: int, app_store: int, total: int},
     *   daily_series: list<array{date: string, label: string, views: int}>,
     *   soft_conversion: array{
     *     views: int,
     *     invitations: int,
     *     ratio: float|null,
     *     since: string|null
     *   },
     *   recent_hits: Collection<int, RegistrationLinkHit>
     * }
     */
    public function forEvent(Event $event): array
    {
        $activeLink = EventRegistrationLink::query()
            ->where('event_id', $event->id)
            ->active()
            ->latest('id')
            ->first();

        $hitsQuery = RegistrationLinkHit::query()
            ->where('event_id', $event->id)
            ->where('is_store_click', false);

        $totalViews = (clone $hitsQuery)->count();
        $uniqueVisitors = (int) (clone $hitsQuery)->distinct('ip_hash')->count('ip_hash');

        $devices = (clone $hitsQuery)
            ->select('device_type', DB::raw('COUNT(*) as total'))
            ->groupBy('device_type')
            ->pluck('total', 'device_type')
            ->map(fn ($n) => (int) $n)
            ->all();

        $devicePct = [];
        foreach (['mobile', 'tablet', 'desktop', 'bot', 'unknown'] as $type) {
            $count = $devices[$type] ?? 0;
            $devicePct[$type] = $totalViews > 0 ? round(($count / $totalViews) * 100, 1) : 0.0;
        }

        $os = (clone $hitsQuery)
            ->select(DB::raw("COALESCE(os, 'unknown') as os_key"), DB::raw('COUNT(*) as total'))
            ->groupBy('os_key')
            ->pluck('total', 'os_key')
            ->map(fn ($n) => (int) $n)
            ->all();

        $playClicks = RegistrationLinkHit::query()
            ->where('event_id', $event->id)
            ->where('is_store_click', true)
            ->where('store', 'play')
            ->count();

        $appStoreClicks = RegistrationLinkHit::query()
            ->where('event_id', $event->id)
            ->where('is_store_click', true)
            ->where('store', 'app_store')
            ->count();

        $dailySeries = $this->dailySeries($event->id);

        $firstLink = EventRegistrationLink::query()
            ->where('event_id', $event->id)
            ->orderBy('created_at')
            ->first();

        $softViews = $totalViews;
        $invitationsSince = 0;
        $sinceLabel = null;

        if ($firstLink) {
            $since = $firstLink->created_at;
            $sinceLabel = $since->timezone(config('app.timezone'))->format('d/m/Y H:i');
            $invitationsSince = Invitation::query()
                ->where('event_id', $event->id)
                ->whereIn('status', [
                    InvitationStatus::Pending->value,
                    InvitationStatus::Confirmed->value,
                ])
                ->where('created_at', '>=', $since)
                ->count();
        }

        $ratio = ($softViews > 0 && $firstLink)
            ? round(($invitationsSince / $softViews) * 100, 1)
            : null;

        $recentHits = RegistrationLinkHit::query()
            ->where('event_id', $event->id)
            ->where('is_store_click', false)
            ->orderByDesc('visited_at')
            ->limit(20)
            ->get();

        return [
            'active_link' => $activeLink,
            'total_views' => $totalViews,
            'unique_visitors' => $uniqueVisitors,
            'devices' => $devices,
            'device_pct' => $devicePct,
            'os' => $os,
            'store_clicks' => [
                'play' => $playClicks,
                'app_store' => $appStoreClicks,
                'total' => $playClicks + $appStoreClicks,
            ],
            'daily_series' => $dailySeries,
            'soft_conversion' => [
                'views' => $softViews,
                'invitations' => $invitationsSince,
                'ratio' => $ratio,
                'since' => $sinceLabel,
            ],
            'recent_hits' => $recentHits,
        ];
    }

    /**
     * @return list<array{date: string, label: string, views: int}>
     */
    private function dailySeries(int $eventId): array
    {
        $tz = config('app.timezone');
        $end = Carbon::now($tz)->startOfDay();
        $start = $end->copy()->subDays(self::SERIES_DAYS - 1);

        $rows = RegistrationLinkHit::query()
            ->where('event_id', $eventId)
            ->where('is_store_click', false)
            ->where('visited_at', '>=', $start->copy()->timezone('UTC'))
            ->select(DB::raw('DATE(visited_at) as day'), DB::raw('COUNT(*) as total'))
            ->groupBy('day')
            ->pluck('total', 'day')
            ->all();

        $series = [];
        $cursor = $start->copy();

        while ($cursor->lte($end)) {
            $key = $cursor->toDateString();
            $series[] = [
                'date' => $key,
                'label' => $cursor->format('d/m'),
                'views' => (int) ($rows[$key] ?? 0),
            ];
            $cursor->addDay();
        }

        return $series;
    }
}
