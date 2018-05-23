<?php

namespace App\Http\Middleware;

use App\Client;
use Closure;

class DomainRouting {
    public function handle($request, Closure $next){
        $host = $request->getHost();
        //you would put this domain into your .env
        //but for brevity, I will just assume the domain
        //e.g. config('app.parent_domain') == 'formdaddy.com'

        if(str_contains($host, 'formforest.com')){
            //cool, it's a subdomain of formdaddy.com
            //so, let's stick the client into the service container
            $slug = explode('.formforest.com', $host)[0];
            $client = Client::where('slug', $slug)->get();
            if($client->count() == 1){
                //client exists, hooray
                //let's put it in the service container
                app()->instance(Client::class, $client->first());
            } else {
                //aw, doesn't exist
                //probably redirect
                return redirect('/error');
            }
        } else {
            //not a subdomain
            //so let's look for the current hostname
            $client = Client::where('host', $host)->get();
            if($client->count() == 1){
                app()->instance(Client::class, $client->first());
            } else {
                //doesn't exist
                //probably redirect
                return redirect('/error');
            }
        }
        return $next($request);
    }
}
