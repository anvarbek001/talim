<?php

namespace App\Models;

use App\Contracts\Purchasable;
use App\Contracts\Subscribable;
use App\Models\Concerns\IsPurchasable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

/**
 * O'qituvchilar uchun guruh (jonli darslar) tarifi — necha nechta guruh
 * ochish mumkinligini belgilaydi. Xuddi Section/Book/User (o'qituvchiga
 * obuna) kabi Purchasable+Subscribable — balans yoki Click.uz orqali
 * sotib olinadi, oyiga bir marta yangilanadi (qarang: PurchaseService,
 * ClickPaymentService).
 */
class GroupPlan extends Model implements Purchasable, Subscribable
{
    use IsPurchasable;

    protected $fillable = [
        'name',
        'max_groups',
        'price',
    ];

    /**
     * Generic xarid sahifalari (masalan student/partials/locked.blade.php)
     * $purchasable->title kutadi — User::title() naqshi bo'yicha aliaslanadi.
     */
    protected function title(): Attribute
    {
        return Attribute::make(get: fn () => $this->name);
    }
}
