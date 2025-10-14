<?php

namespace App\Modules\BacklinkHistory\Models;

use Addweb\Base\Model\BaseModel;
use App\Modules\BacklinkHistory\Observers\BacklinkHistoryObserver;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([BacklinkHistoryObserver::class])]
class BacklinkHistory extends BaseModel
{
    use HasFactory;
    use SoftDeletes;


    protected $table = 'backlink_history';


    protected $fillable = [
        'client_id',
        'client_domain_id',
        'rival_domain_id',
        'target_domain',
        'history_date',
        'rank',
        'backlinks',
        'new_backlinks',
        'lost_backlinks',
        'referring_domains',
        'new_referring_domains',
        'lost_referring_domains',
        'crawled_pages',
        'internal_links_count',
        'external_links_count',
        'broken_backlinks',
        'broken_pages',
        'referring_domains_nofollow',
        'referring_main_domains',
        'referring_main_domains_nofollow',
        'referring_ips',
        'referring_subnets',
        'referring_pages',
        'referring_pages_nofollow',
        'referring_links_tld',
        'referring_links_types',
        'referring_links_attributes',
        'referring_links_platform_types',
        'referring_links_semantic_locations',
        'referring_links_countries'
    ];

    protected $casts = [
        'referring_links_tld' => 'array',
        'referring_links_types' => 'array',
        'referring_links_attributes' => 'array',
        'referring_links_platform_types' => 'array',
        'referring_links_semantic_locations' => 'array',
        'referring_links_countries' => 'array',
    ];

    public function client()
    {
        return $this->belongsTo(\App\Modules\User\Models\User::class, 'client_id', 'id');
    }

    public function clientDomain()
    {
        return $this->belongsTo(\App\Modules\ClientDomain\Models\ClientDomain::class, 'client_domain_id', 'id');
    }

    public function rivalDomain()
    {
        return $this->belongsTo(\App\Modules\RivalDomain\Models\RivalDomain::class, 'rival_domain_id', 'id');
    }

}
