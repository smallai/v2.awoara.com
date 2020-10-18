<?php

namespace App\Models;

use App\Scopes\GoodsScope;
use App\Utils\ModelFiledTranslator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\Goods
 *
 * @property int $id 编号
 * @property int $device_id 设备编号
 * @property int|null $owner_id 创建记录的人
 * @property string|null $name
 * @property int|null $price 价格
 * @property string|null $image
 * @property int $is_sale 是否上架
 * @property int $is_recommend 是否推荐
 * @property int $seconds 可用时长
 * @property int $count 可用次数
 * @property int $days
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $today_limit 当天可用次数
 * @property-read \App\Models\Device $device
 * @property-read \App\Models\User|null $owner
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods local()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Goods onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereDeviceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereIsRecommend($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereIsSale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereSeconds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereTodayLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Goods whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Goods withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Goods withoutTrashed()
 * @mixin \Eloquent
 */
class Goods extends Model
{
    use SoftDeletes;
    use ModelFiledTranslator;

    const Sale_Yes = 1;
    const Sale_No = 0;

    const Recommend_Yes = 1;
    const Recommend_No = 0;

    protected $table = 'goods';

    protected $guard_name = 'web';

    protected $fillable = [
        'id',
        'device_id',
        'owner_id',
        'name',
        'price',
        'image',
        'is_sale',
        'is_recommend',
        'seconds',
        'count',
        'days',
        'today_limit',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'device_id' => 'integer',
        'owner_id' => 'integer',

        'is_sale' => 'integer',
        'is_recommend' => 'integer',
        'seconds' => 'integer',
        'count' => 'integer',
        'days' => 'integer',
        'price' => 'integer',
    ];

    static protected $filed_text = [
        'id' => '编号',
        'device_id' => '设备',
        'owner_id' => '拥有者',

        'name' => '名称',
        'price' => '价格',
        'image' => '图片',
        'is_sale' => '上架',
        'is_recommend' => '默认选中',
        'seconds' => '可用时间',
        'count' => '总次数',
        'today_limit' => '当日可用次数',
        'days' => '有效期',
        'created_at' => '创建时间',
        'updated_at' => '更新时间',
        'deleted_at' => '删除时间',
    ];

    static protected $filed_value_text = [
        'is_sale' => [
            Goods::Sale_No => '否',
            Goods::Sale_Yes => '是',
        ],
        'is_recommend' => [
            Goods::Recommend_No => '否',
            Goods::Recommend_Yes => '是',
        ],
    ];

    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id', 'id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function scopeLocal($query)
    {
        $user = Auth::user();
        if (isset($user))
        {
            if ($user->hasRole('superadmin'))
                ;
            else if ($user->hasRole('admin'))
                $query->where('owner_id', $user->id);
            else
                $query->where('id', PHP_INT_MIN);
        }
        else
        {
            $query->where('id', PHP_INT_MIN);
        }
    }

    //    全局过滤器
    static public function boot()
    {
        parent::boot();

//        static::addGlobalScope(new GoodsScope());
    }
}
