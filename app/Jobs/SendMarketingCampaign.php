<?php

namespace App\Jobs;

use App\Models\MarketingCampaign;
use App\Notifications\MarketingMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMarketingCampaign implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly int $campaignId
    ) {}

    public function handle(): void
    {
        $campaign = MarketingCampaign::findOrFail($this->campaignId);

        if ($campaign->status === 'sent') {
            return;
        }

        $clients = $campaign->getTargetClients();
        $sentCount = 0;

        foreach ($clients as $client) {
            try {
                $client->notify(new MarketingMessage($campaign));
                $sentCount++;
            } catch (\Exception $e) {
                logger()->error("Marketing failed for client {$client->id}: " . $e->getMessage());
            }
        }

        $campaign->update([
            'status' => 'sent',
            'sent_at' => now(),
            'sent_count' => $sentCount,
        ]);
    }
}
