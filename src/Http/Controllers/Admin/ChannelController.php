<?php

namespace Wontonee\Shiprocket\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Webkul\Core\Models\CoreConfig;
use Wontonee\Shiprocket\Sdk\Client\Client;


class ChannelController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * API Password
     *
     * @var string
     */
    protected $apiPassword;

    /**
     * API Username
     *
     * @var string
     */
    protected $apiUsername;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->apiUsername = optional(CoreConfig::where('code', 'shiprocket.api_username')->first())->value;
        $this->apiPassword = optional(CoreConfig::where('code', 'shiprocket.api_password')->first())->value;
    }
    /**
     * Display the channel page
     *
     * @return \Illuminate\View\View
     */
    public function index()     
    {
        if (!$this->apiUsername || !$this->apiPassword) {
            session()->flash('error', 'Shiprocket API credentials are not configured.');
            return redirect()->route('admin.shiprocket.settings');
        }

        try {
            $client = new Client($this->apiUsername, $this->apiPassword);
            $response = $client->channel->fetchChannels();
        } catch (\Exception $e) {
            session()->flash('error', 'Shiprocket API Error: ' . $e->getMessage());
            return redirect()->route('admin.shiprocket.settings');
        }

        $channels = [];
        
        if (isset($response['data']) && is_array($response['data'])) {
            foreach ($response['data'] as $channel) {
                $channels[] = [
                    'id' => $channel['id'],
                    'name' => $channel['name'],
                    'status' => $channel['status'],
                    'brand_name' => $channel['brand_name'] ?? '',
                    'channel_updated_at' => $channel['channel_updated_at'],
                    'orders_sync' => $channel['orders_sync'],
                    'inventory_sync' => $channel['inventory_sync'],
                    'catalog_sync' => $channel['catalog_sync']
                ];
            }
        }
        
        // Get current saved channel ID from core settings
        $currentChannelId = CoreConfig::where('code', 'shiprocket.shipping.channel_id')->first();
        $currentChannelId = !empty($currentChannelId) ? (string) $currentChannelId->value : '';
        
        return view('shiprocket::admin.channel.index', [
            'apiConfigured' => true,
            'channels' => $channels,
            'currentChannelId' => $currentChannelId
        ]);    
    }

    /**
     * Save channel to core settings
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveChannel(Request $request)
    {
        $request->validate([
            'channel_id' => 'required|numeric'
        ]);
        
        $channelId = (int) $request->input('channel_id');
        
        // Save to core config
        $coreConfig = CoreConfig::updateOrCreate(
            [
                'code' => 'shiprocket.shipping.channel_id'
            ],
            [
                'value' => $channelId
            ]
        );
        
        session()->flash('success', 'Channel has been saved successfully.');
        
        return redirect()->route('admin.shiprocket.channel');
    }
}