<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SitemapVersion
 *
 * @property int $id
 * @property int $sitemap_id
 * @property int $user_id
 * @property string $label
 * @property array $payload
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|SitemapVersion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SitemapVersion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SitemapVersion query()
 * @method static \Illuminate\Database\Eloquent\Builder|SitemapVersion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SitemapVersion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SitemapVersion whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SitemapVersion wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SitemapVersion whereSitemapId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SitemapVersion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SitemapVersion whereUserId($value)
 * @mixin \Eloquent
 */
class SitemapVersion extends Model
{
    use HasFactory;

    protected $guarded = ["id", "created_at", "updated_at"];
    protected $hidden = ["updated_at"];
    protected $casts = [
        'payload' => 'array',
    ];
}
