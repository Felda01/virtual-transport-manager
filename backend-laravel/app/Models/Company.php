<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

/**
 * App\Models\Company
 *
 * @property string $id
 * @property string $name
 * @property float $money
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BankLoan[] $bankLoans
 * @property-read int|null $bank_loads_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Contract[] $contracts
 * @property-read int|null $contracts_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Driver[] $drivers
 * @property-read int|null $drivers_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Garage[] $garages
 * @property-read int|null $garages_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Market[] $markets
 * @property-read int|null $markets_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Order[] $orders
 * @property-read int|null $orders_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Trailer[] $trailers
 * @property-read int|null $trailers_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Transaction[] $transactions
 * @property-read int|null $transactions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Truck[] $trucks
 * @property-read int|null $trucks_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Company newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Company newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Company query()
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Company whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Company extends Model
{
    use HasFactory, HasUuid;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends= ['value'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function trucks()
    {
        return $this->hasMany(Truck::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function garages()
    {
        return $this->hasMany(Garage::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function drivers()
    {
        return $this->hasMany(Driver::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function trailers()
    {
        return $this->hasMany(Trailer::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function orders()
    {
        return $this->hasManyThrough(Order::class, Market::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function markets()
    {
        return $this->hasMany(Market::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bankLoans()
    {
        return $this->hasMany(BankLoan::class);
    }

    /**
     * @return Company |null
     */
    public static function currentCompany()
    {
        if (Auth::guard('api')->check()) {
            return Company::find(Auth::guard('api')->user()->company_id);
        }
        return null;
    }

    /**
     * Return company's total value
     *
     * @return float|int
     */
    public function getValueAttribute()
    {
        return $this->totalValue();
    }

    /**
     * Return total value of company
     *
     * @return float|int
     */
    public function totalValue()
    {
        $result = $this->attributes['money'];

        $result += $this->garages()->leftJoin('garage_models', 'garages.garage_model_id', '=', 'garage_models.id')->sum('garage_models.price');
        $result += $this->trucks()->leftJoin('truck_models', 'trucks.truck_model_id', '=', 'truck_models.id')->sum('truck_models.price');
        $result += $this->trailers()->leftJoin('trailer_models', 'trailers.trailer_model_id', '=', 'trailer_models.id')->sum('trailer_models.price');

        $bankLoans = $this->bankLoans()->with('bankLoanType')->get();

        $result -= $bankLoans->reduce(function ($carry, $item) {
            /** @var BankLoanType $bankLoanType */
            $bankLoanType = $item->bankLoanType;
            return $carry += ($bankLoanType->period - $item->paid) * $bankLoanType->payment;
        }, 0);

        return $result;
    }
}
