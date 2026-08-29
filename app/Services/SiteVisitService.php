<?php

namespace App\Services;

use App\Models\SiteVisit;
use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;

class SiteVisitService
{
    public function getAvailableSlots(Project $project, Carbon $date, int $durationMinutes = 60)
    {
        $existingVisits = SiteVisit::where('project_id', $project->id)
            ->where('status', '!=', 'cancelled')
            ->whereDate('scheduled_at', $date->toDateString())
            ->get();

        $slots = [];
        $startHour = 8; // 8 AM
        $endHour = 17; // 5 PM

        for ($hour = $startHour; $hour < $endHour; $hour++) {
            $slotStart = $date->copy()->setHour($hour)->setMinute(0);
            $slotEnd = $slotStart->copy()->addMinutes($durationMinutes);

            // Check if slot overlaps with existing visits
            $isAvailable = true;
            foreach ($existingVisits as $visit) {
                $visitStart = $visit->scheduled_at;
                $visitEnd = $visitStart->copy()->addMinutes($visit->duration_minutes ?? 60);

                if ($slotStart->lt($visitEnd) && $slotEnd->gt($visitStart)) {
                    $isAvailable = false;
                    break;
                }
            }

            if ($isAvailable) {
                $slots[] = [
                    'start' => $slotStart->toTimeString(),
                    'end' => $slotEnd->toTimeString(),
                    'available' => true,
                ];
            }
        }

        return $slots;
    }

    public function getChecklistSuggestions(Project $project): array
    {
        // Get all completed visit checklists for this project
        $completedVisits = SiteVisit::where('project_id', $project->id)
            ->where('status', 'completed')
            ->whereNotNull('checklist')
            ->get();

        if ($completedVisits->isEmpty()) {
            return [
                ['item' => 'Survey beacons clearly marked', 'suggested' => true],
                ['item' => 'Brochures/price lists in hand', 'suggested' => true],
                ['item' => 'Water test results available', 'suggested' => true],
            ];
        }

        // Count frequency of checklist items across all visits
        $itemCounts = [];
        foreach ($completedVisits as $visit) {
            foreach ($visit->checklist as $item) {
                if (!isset($itemCounts[$item['item']])) {
                    $itemCounts[$item['item']] = 0;
                }
                if ($item['checked'] ?? false) {
                    $itemCounts[$item['item']]++;
                }
            }
        }

        // Sort by frequency and return top items
        arsort($itemCounts);
        $suggestions = [];
        foreach (array_slice($itemCounts, 0, 5) as $item => $count) {
            $suggestions[] = [
                'item' => $item,
                'suggested' => true,
                'frequency' => $count,
            ];
        }

        return $suggestions;
    }

    public function createSoftHold($plotId, $leadTetherId, $siteVisitId, $days = 7)
    {
        return SoftHold::create([
            'plot_id' => $plotId,
            'lead_tether_id' => $leadTetherId,
            'site_visit_id' => $siteVisitId,
            'expires_at' => now()->addDays($days),
            'status' => 'active',
        ]);
    }
}