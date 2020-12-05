<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

/**
 * Class UniqueTranslationRule
 * @package App\Rules
 */
class UniqueTranslationRule implements Rule
{
    public $table;
    public $column;
    public $ignore;

    public $locale;

    /**
     * Create a new rule instance.
     *
     * @param $table
     * @param $column
     * @param null $ignore
     */
    public function __construct($table, $column, $ignore = null)
    {
        $this->table = $table;
        $this->column = $column;
        $this->ignore = $ignore;
        $this->locale = null;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->locale = $value['locale'];

        return $this->isUnique($value['value'], $this->locale);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.unique_translation', ['locale' => $this->locale, 'attribute' => trans('validation.attributes.name_translations')]);
    }

    /**
     * Check if a translation is unique.
     *
     * @param mixed $value
     * @param string $locale
     *
     * @return bool
     */
    protected function isUnique($value, $locale)
    {
        $connection = Config::get('database.default');

        $query = $this->findTranslation($connection, $this->table, $this->column, $locale, $value);
        $query = $this->ignore($query, 'id', $this->ignore);

        return $query->count() === 0;
    }

    /**
     * Find the given translated value in the database.
     *
     * @param string $connection
     * @param string $table
     * @param string $column
     * @param string $locale
     * @param mixed $value
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function findTranslation($connection, $table, $column, $locale, $value)
    {
        // Properly escape backslashes to work with LIKE queries...
        // See: https://stackoverflow.com/questions/14926386/how-to-search-for-slash-in-mysql-and-why-escaping-not-required-for-wher
        $value = str_replace('\\', '\\\\\\\\', $value);

        return DB::connection($connection)->table($table)
            ->where(function ($query) use ($column, $locale, $value) {
                $query->where($column, 'LIKE', "%\"{$locale}\": \"{$value}\"%")
                    ->orWhere($column, 'LIKE', "%\"{$locale}\":\"{$value}\"%");
            });
    }

    /**
     * Ignore the column with the given value.
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param string|null $column
     * @param mixed $value
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function ignore($query, $column = null, $value = null)
    {
        if ($value !== null && $column === null) {
            $column = 'id';
        }

        if ($column !== null) {
            $query = $query->where($column, '!=', $value);
        }

        return $query;
    }
}
