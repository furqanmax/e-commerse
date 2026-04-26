<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Shopper\Core\Models\Channel;

class BannerController extends Controller
{
    public function index(): JsonResponse
    {
        $banners = $this->getBanners();

        return response()->json([
            'data' => $banners,
            'message' => $banners->isEmpty() ? 'No banners configured' : null,
        ]);
    }

    protected function getBanners(): Collection
    {
        try {
            if (! $this->tablesExist(['channels'])) {
                return collect();
            }

            return Channel::query()
                ->enabled()
                ->whereNotNull('metadata')
                ->where('metadata', '!=', '[]')
                ->get()
                ->filter(function (Channel $channel) {
                    $metadata = $channel->metadata;

                    return is_array($metadata) && ! empty($metadata['banners']);
                })
                ->flatMap(function (Channel $channel) {
                    $channelName = $channel->name;
                    $channelUrl = $channel->url;

                    return collect($channel->metadata['banners'])->map(function (array $banner) use ($channelName, $channelUrl) {
                        return [
                            'id' => $banner['id'] ?? uniqid(),
                            'title' => $banner['title'] ?? $channelName,
                            'subtitle' => $banner['subtitle'] ?? null,
                            'image_url' => $banner['image_url'] ?? null,
                            'link_url' => $banner['link_url'] ?? $channelUrl,
                            'position' => $banner['position'] ?? 0,
                            'is_active' => $banner['is_active'] ?? true,
                        ];
                    });
                })
                ->sortBy('position')
                ->values();
        } catch (\Exception $e) {
            return collect();
        }
    }

    protected function tablesExist(array $tables): bool
    {
        foreach ($tables as $table) {
            try {
                $tableName = shopper_table($table);
                DB::table($tableName)->limit(1)->get();
            } catch (\Exception $e) {
                return false;
            }
        }

        return true;
    }
}
