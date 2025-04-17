<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ThrottleController extends Controller
{
    public function check(Request $request)
    {
        $domain = "http://127.0.0.1:8080";
        $target = "http://127.0.0.1:8080/target";
        $queue = "http://127.0.0.1:8080/queue";
        $limit = 3;
        $key = "queue_count:{$domain}:" . now()->format('YmdHi');
        if ((Redis::get($key) ?? 0) < $limit) {
            Redis::incr($key);
            Redis::expire($key, 60);
            return response()->json(['status' => 'allowed', 'redirect' => $target]);
        }
        // Add to queue
        $token = Str::uuid();
        Redis::rpush("queue:{$domain}", $token);
        Redis::setex("user_token:{$token}", 600, $domain);
        return response()->json([
            'status' => 'queued',
            'token' => $token,
            'redirect' => "$queue?token=$token"
        ]);
    }

    // /api/throttle/retry
    public function retry(Request $request)
    {
        $token = $request->query('token');
        $domain = Redis::get("user_token:{$token}");

        if (!$domain) {
            // Token expired
            return response()->json([
                'status' => 'expired',
                'redirect' => $domain ?? 'http://127.0.0.1:8080'
            ]);
        }

        $queueKey = "queue:{$domain}";
        $queue = Redis::lrange($queueKey, 0, -1);
        $position = array_search($token, $queue);

        if ($position === false) {
            // Token not found in queue
            return response()->json([
                'status' => 'not_found',
                'redirect' => $domain
            ]);
        }

        // Check wait time
        $createdAtKey = "user_token_time:{$token}";
        $createdAt = Redis::get($createdAtKey);

        if (!$createdAt) {
            // First time setting creation time
            Redis::setex($createdAtKey, 600, now()->timestamp);
            $createdAt = now()->timestamp;
        }

        $waitTime = now()->timestamp - intval($createdAt);

        if ($position === 0 || $waitTime >= 60) {
            // Pop from queue and allow access
            Redis::lrem($queueKey, 0, $token);
            Redis::del("user_token:{$token}");
            Redis::del($createdAtKey);

            $countKey = "queue_count:{$domain}:" . now()->format('YmdHi');
            Redis::incr($countKey);
            Redis::expire($countKey, 60);

            return response()->json([
                'status' => 'allowed',
                'redirect' => "{$domain}/target"
            ]);
        }

        return response()->json([
            'status' => 'waiting',
            'position' => $position,
            'waited' => $waitTime
        ]);
    }


    public function embedScript(Request $request)
    {
        $targetUrl = $request->query('target_url', 'http://127.0.0.1:8080/target');
        $queuePageUrl = 'http://127.0.0.1:8080/queue';
        $api = route('check-api');

        $script = <<<EOT
(async function() {
    const SAAS_API_URL = "{$api}";
    const TARGET_URL = "{$targetUrl}";
    const QUEUE_PAGE_URL = "{$queuePageUrl}";

    try {
        const res = await fetch(SAAS_API_URL, {
            method: "POST",
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ url: window.location.href })
        });

        const data = await res.json();

        if (data.status === 'allowed') {
            // window.location.href = data.redirect;
        } else if (data.status === 'queued') {
         console.log(data)
            window.location.href = data.redirect;
        } else {
            console.warn('Unhandled status:', data.status);
        }
    } catch (err) {
        console.error('Throttle script error:', err);
    }
})();
EOT;

        return response($script, 200)
            ->header('Content-Type', 'application/javascript');
    }





    public function embedQueueScript(Request $request)
    {
        $websiteId = $request->query('website_id', 150);
        $queuePageUrl = 'test';
        $saasApiUrl = route('retry-api');
        $script = <<<EOT
                (function() {
                    const WEBSITE_ID = "{$websiteId}";
                    const SAAS_API_URL = "{$saasApiUrl}";
                    const QUEUE_PAGE_URL = "{$queuePageUrl}";
                    const fingerprint = new URLSearchParams(window.location.search).get('fingerprint');
                    setTimeout(() => {
                        fetch(`\${SAAS_API_URL}?website_id=\${WEBSITE_ID}&fingerprint=\${fingerprint}`)
                            .then(res => res.json())
                            .then(data => {
                                if (data.allowed) {
                                    window.location.href = `\${data.target_url}`;
                                }
                            });
                    }, 8000); // Check every 8 seconds to see if user is allowed

                })();
                EOT;

        return response($script, 200)
            ->header('Content-Type', 'application/javascript');
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
