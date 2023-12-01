<?php

declare(strict_types = 1);

namespace App\Models;

use Closure;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Expression;

/**
 * Class BaseModel
 *
 * @method static Model|$this create(array $attributes = []) Save a new model and return the instance.
 * @method static Model|$this firstOrNew(array $attributes = [], array $values = []) Find a model by its primary key or throw an exception or save a new model and return the instance.
 * @method static Model|$this firstOrCreate(array $attributes = [], array $values = []) Find a model by its primary key or throw an exception or save a new model and return the instance.
 * @method static Collection|Builder[] get(array|string $columns = ['*']) Execute the query as a "select" statement.
 * @method static $this|Model|Collection|Builder[]|Builder|null find(mixed $id, array $columns = ['*']) Find a model by its primary key.
 * @method static $this|Model|Collection|Builder[]|Builder findOrFail(mixed $id, array $columns = ['*']) Find a model by its primary key or throw an exception.
 * @method static $this where(Closure|string|array|Expression $column, mixed $operator = null, mixed $value = null, string $boolean = 'and') Add a basic where clause to the query.
 * @method static $this whereIn(Closure|string|array|Expression $column, array $array)
 * @method static $this whereDate(Expression|string $column, string $operator, DateTimeInterface|string|null $value = null, string $boolean = 'and')
 * @method Model|object|null first(array|string $columns = ['*']) Execute the query and get the first result.
 * @method mixed|null value(array|string $columns = ['*']) Execute the query and get the first result.
 * @mixin Builder
 */
class BaseModel extends Model
{
    public $timestamps = true;
}
