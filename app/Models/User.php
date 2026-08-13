<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Contracts\Purchasable;
use App\Contracts\Subscribable;
use App\Models\Concerns\IsPurchasable;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

/**
 * A teacher's own `purchases()` (via IsPurchasable) are the students
 * subscribed *to them* — buying full monthly access to all of the
 * teacher's sections and books at once (see PurchaseService).
 */
class User extends Authenticatable implements Purchasable, Subscribable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, IsPurchasable, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'subscription_price',
        'google_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'balance' => 'integer',
        ];
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }

    public function topics(): HasMany
    {
        return $this->hasMany(Topic::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    public function topicTests(): HasMany
    {
        return $this->hasMany(TopicTest::class);
    }

    public function dtmTests(): HasMany
    {
        return $this->hasMany(DtmTest::class);
    }

    public function sertifikatTests(): HasMany
    {
        return $this->hasMany(SertifikatTest::class);
    }

    public function languageExamTests(): HasMany
    {
        return $this->hasMany(LanguageExamTest::class);
    }

    public function savedLessons(): BelongsToMany
    {
        return $this->belongsToMany(Lesson::class, 'saved_lessons')->withTimestamps();
    }

    public function testAttempts(): HasMany
    {
        return $this->hasMany(TestAttempt::class);
    }

    /**
     * O'qituvchi sifatida yaratgan guruhlari.
     */
    public function groupsAsTeacher(): HasMany
    {
        return $this->hasMany(Group::class, 'teacher_id');
    }

    /**
     * A'zo bo'lgan (o'quvchi sifatida qo'shilgan) guruhlari.
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_members')
            ->withPivot('joined_at')
            ->withTimestamps();
    }

    /**
     * O'qituvchining hozir faol guruh tarifi (GroupPlan) — eng oxirgi
     * muddati o'tmagan xaridi. Yo'q bo'lsa null (guruh yaratish taqiqlanadi).
     */
    public function activeGroupPlan(): ?GroupPlan
    {
        $purchase = Purchase::where('user_id', $this->id)
            ->where('purchasable_type', GroupPlan::class)
            ->latest()
            ->get()
            ->first(fn (Purchase $purchase) => $purchase->isActive());

        return $purchase?->purchasable;
    }

    /**
     * Joriy tarifga ko'ra necha nechta guruh ochish mumkin (tarif yo'q
     * bo'lsa 0 — hali birorta ham guruh ochib bo'lmaydi).
     */
    public function groupSlotLimit(): int
    {
        return $this->activeGroupPlan()?->max_groups ?? 0;
    }

    /**
     * Hozir band bo'lgan joylar — o'chirilmagan (hali mavjud) guruhlar
     * soni. Guruh o'chirilsa, bu son kamayadi va joy o'zi bo'shaydi.
     */
    public function groupSlotsUsed(): int
    {
        return $this->groupsAsTeacher()->count();
    }

    public function groupSlotsRemaining(): int
    {
        return max(0, $this->groupSlotLimit() - $this->groupSlotsUsed());
    }

    /**
     * Purchasable::price — reused by IsPurchasable (isFree/isPurchasedBy)
     * and PurchaseService when a student subscribes to this teacher.
     */
    protected function price(): Attribute
    {
        return Attribute::make(get: fn () => (int) $this->subscription_price);
    }

    /**
     * Lets views that render any Purchasable generically (locked paywall,
     * payment history, admin purchases) show a name for a teacher
     * subscription the same way they show a Section/Book title.
     */
    protected function title(): Attribute
    {
        return Attribute::make(get: fn () => $this->name);
    }

    public function avatarUrl(): ?string
    {
        return $this->avatar ? asset('storage/'.$this->avatar) : null;
    }

    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn ($part) => mb_substr($part, 0, 1))
            ->take(2)
            ->implode('');
    }
}
