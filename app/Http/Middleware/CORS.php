<?php

namespace App\Http\Middleware;

use Closure;

// use Illuminate\Support\Facades\Auth;
// use Illuminate\Http\RedirectResponse;

// use Vinkla\Hashids\Facades\Hashids;

class CORS {
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @param  string|null  $guard
	 * @return mixed
	 */
	public function handle($request, Closure $next, $guard = null) {
		$headers = [
			// 'Access-Control-Allow-Origin' => '*',
			'Access-Control-Allow-Methods' => 'DELETE, HEAD, GET, OPTIONS, POST, PUT, PATCH',
			'Access-Control-Allow-Credentials' => true,
			'Access-Control-Max-Age' => '86400',
			'Content-Security-Policy' => 'frame-src *',
			'Access-Control-Allow-Headers' => 'Overwrite, Destination, Content-Type, Accept, Application, Authorization, Depth, User-Agent, Translate, Range, Content-Range, Timeout, X-File-Size, X-Requested-With, If-Modified-Since, X-File-Name, Cache-Control, Location, Lock-Token, If',
			"Content-Type: application/json; charset=UTF-8;",
		];

		if ($request->isMethod('OPTIONS')) {
			return response()->json('{"method":"OPTIONS"}', 200, $headers);
		}

		$response = $next($request);
		foreach ($headers as $key => $value) {
			if (method_exists($response, 'header')) {
				$response->header($key, $value);
			}
		}

		return $response;
		// $response = $next($request);
		// $response = $response instanceof RedirectResponse ? $response : response($response);

		// return $response->header('Access-Control-Allow-Origin', '*')
		//     // ->header('Access-Control-Allow-Headers', 'X-Requested-With')
		//     // ->header('Content-Type', 'Authorization')
		//     ->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE, PATCH');
	}
}
