<?php

namespace App\Services;

use App\Models\LeadTether;
use App\Models\User;
use App\Models\Project;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;

class LeadTetherService
{
    const TETHER_EXPIRY_DAYS = 90;

    public function createOrUpdateTether(
        string $phone,
        int $agentId,
        int $projectId,
        string $source = 'microsite',
        ?string $sourcePlatform = null,
        ?string $name = null,
        ?string $email = null,
        ?array $attributionData = null,
        ?string $fingerprint = null
    ): LeadTether {
        // ✅ Deterministic hash (same phone → same hash)
        $phoneHash = hash('sha256', $phone);
        $phoneEncrypted = Crypt::encryptString($phone);

        // Check for existing tether by deterministic hash
        $existing = LeadTether::where('phone_hash', $phoneHash)
            ->where('project_id', $projectId)
            ->first();

        if ($existing) {
            // ✅ Update existing tether (extend expiry, update metadata)
            $existing->expires_at = now()->addDays(self::TETHER_EXPIRY_DAYS);
            $existing->tethered_at = now();

            if ($existing->source === 'microsite' && $source === 'site_booking') {
                $existing->source = $source;
            }

            if ($existing->agent_id !== $agentId) {
                $existing->attribution_conflict = true;
            }

            // Optionally update other fields if they're new
            if ($name)
                $existing->name = $name;
            if ($email) {
                $existing->email_hash = hash('sha256', $email);
                $existing->email_encrypted = Crypt::encryptString($email);
            }

            $existing->save();
            return $existing;
        }

        // ✅ Create new tether
        return LeadTether::create([
            'agent_id' => $agentId,
            'project_id' => $projectId,
            'phone_hash' => $phoneHash,
            'phone_encrypted' => $phoneEncrypted,
            'email_hash' => $email ? hash('sha256', $email) : null,
            'email_encrypted' => $email ? Crypt::encryptString($email) : null,
            'name' => $name,
            'source' => $source,
            'source_platform' => $sourcePlatform,
            'attribution_data' => $attributionData,
            'fingerprint' => $fingerprint,
            'tethered_at' => now(),
            'expires_at' => now()->addDays(self::TETHER_EXPIRY_DAYS),
            'attribution_conflict' => false,
        ]);
    }

    public function findTetherByPhone(string $phone): ?LeadTether
    {
        $phoneHash = Hash::make($phone);
        return LeadTether::where('phone_hash', $phoneHash)->first();
    }

    public function resolveConflict(int $tetherId, int $winningAgentId): LeadTether
    {
        $tether = LeadTether::findOrFail($tetherId);
        $tether->agent_id = $winningAgentId;
        $tether->attribution_conflict = false;
        $tether->save();
        return $tether;
    }

    public function getActiveTethersForAgent(int $agentId)
    {
        return LeadTether::where('agent_id', $agentId)
            ->where('expires_at', '>', now())
            ->where('attribution_conflict', false)
            ->get();
    }
}