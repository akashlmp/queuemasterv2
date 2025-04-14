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

        return response()->json(['status' => 'queued', 'token' => $token]);
    }

    // /api/throttle/retry
    public function retry(Request $request)
    {
        $limit = 2; // Limit: Only 2 users per minute
        $intervalSeconds = 60; // Interval for each user

        $websiteId = $request->query('website_id');
        $fingerprint = $request->query('fingerprint');

        // Retrieve user data from session or generate unique ID
        if (!session()->has('queue_user_id')) {
            session()->put('queue_user_id', "$websiteId.$fingerprint");
        }

        $userId = session('queue_user_id');
        $queueKey = 'global_user_queue';
        $queue = Cache::get($queueKey, []);

        // Add user to queue if not already there
        if (!in_array($userId, $queue)) {
            $queue[] = $userId;
            Cache::put($queueKey, $queue, now()->addMinutes(10));
        }

        $position = array_search($userId, $queue);

        if ($position === false) {
            abort(403, 'Queue error.');
        }

        // Calculate the user's allowed time to access
        $allowedAt = Carbon::now()->startOfMinute()->addSeconds($intervalSeconds);

        if (now()->greaterThanOrEqualTo($allowedAt)) {
            return response()->json([
                'allowed' => true,
                'message' => 'Access granted. You can access the target page.',
            ]);
        }

        return response()->json([
            'allowed' => false,
            'wait' => $allowedAt->diffInSeconds(now())
            // 'redirect_url' => "https://your-cloudfront-domain.com/queue.html?wait=" . $allowedAt->diffInSeconds(now()),
        ]);
    }
    public function embedScript(Request $request)
    {
        $targetUrl = $request->query('target_url', 'http://127.0.0.1:8080/target'); // Your target page URL
        $queuePageUrl = 'http://127.0.0.1:8080/queue'; // Your queue page URL
        $api = route('check-api'); // API route to check if user is allowed to access the target page

        $script = <<<EOT
        (function() {
            const SAAS_API_URL = "{$api}";
            const TARGET_URL = "{$targetUrl}";
            const QUEUE_PAGE_URL = "{$queuePageUrl}";
            // When the user tries to go to the target page, we check if they are allowed
            document.querySelector('a').addEventListener('click', async  function(e) {
                e.preventDefault(); // Prevent the default link behavior
               if(this.href == `\${TARGET_URL}`){
                const res = await fetch(`\${SAAS_API_URL}`,{
                        method: "POST",
                        headers: { 'Content-Type': 'application/json' },
                    });
                     const data = await res.json();
            if (data.status === 'allowed') {
                window.location.href = data.redirect;
            } else if (data.status === 'queued') {
                const waitingUrl = `\${QUEUE_PAGE_URL}?token=\${data.token}`;
                window.location.href = waitingUrl;
            }
            }
            });
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
