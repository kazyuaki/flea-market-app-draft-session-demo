<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'brand',
        'price',
        'detail',
        'img',
        'condition',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }

    //これは「この商品をいいねしているユーザー一覧」を取るためのリレーション。
    public function favorites()
    {
        return $this->belongsToMany(User::class, 'favorites', 'item_id', 'user_id')->withTimestamps();
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function isFavoritedBy(User $user)
    {
        if (!$user) return false;
        return $this->favoritedBy->contains($user);
    }

    // 商品に紐づくコメントを取得するリレーション
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // 商品が売却済みかどうかを判定するリレーション
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function order()
    {
        return $this->hasOne(Order::class);
    }

    // 商品のユーザーを取得するリレーション
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 商品の状態を表すアクセサ
    public function getConditionLabelAttribute()
    {
        switch ($this->condition) {
            case 1:
                return '良好';
            case 2:
                return '目立った傷や汚れなし';
            case 3:
                return 'やや傷や汚れあり';
            case 4:
                return '状態が悪い';
            default:
                return '不明';
        }
    }

    // 商品が売却済みかどうかを判定するアクセサ
    public function getIsSoldAttribute()
    {
        return $this->order()->exists();
    }

    public function scopeSearchByNames($query, $keyword)
    {
        return $keyword
            ? $query->where('name', 'like', '%' . $keyword . '%')
            : $query;
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * この商品が「売り切れ（購入不可）か」を判定
     * ongoing（取引中） or completed（売買完了）が存在すれば true
     */
    public function getIsSoldOutAttribute(): bool
    {
        return $this->transactions()
            ->whereIn('status', ['ongoing', 'completed'])
            ->exists();
    }
}
