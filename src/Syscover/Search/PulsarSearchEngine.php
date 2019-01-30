<?php namespace Syscover\Search;

use Laravel\Scout\Engines\Engine;
use Laravel\Scout\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Support\Facades\Storage;

class PulsarSearchEngine extends Engine
{
    private $tableName;
    private $fileRoute;

    private function setProperties($models)
    {
        $this->tableName = $models->first()->getTable();
        $this->fileRoute = 'public/search/' . $this->tableName . '.json';
    }

    /**
     * Update the given model in the index.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $models
     * @return void
     */
    public function update($models)
    {
        $this->setProperties($models);

        if (Storage::exists($this->fileRoute))
        {
            // transform json to collection
            $data = collect(json_decode(Storage::get($this->fileRoute), true));

            foreach ($models as $model)
            {
                $match = false;

                foreach ($data as $key => $item)
                {
                    if (
                        (isset($model['ix']) && $item['ix'] === $model->ix) ||
                        (isset($model['id']) && $item['id'] === $model->id)
                    )
                    {
                        $match = true;
                        $data[$key] = $model->toSearchableArray();
                    }
                }

                if(!$match) $data->push($model->toSearchableArray());
            }
        }
        else
        {
            $data = $models->map(function($model) {
                return $model->toSearchableArray();
            });
        }

        Storage::disk('local')->put($this->fileRoute, $data);
    }

    /**
     * Remove the given model from the index.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $models
     * @return void
     */
    public function delete($models)
    {
        $this->setProperties($models);

        if (Storage::exists($this->fileRoute))
        {
            // transform json to collection
            $data = collect(json_decode(Storage::get($this->fileRoute), true));

            $match = false;
            $dataFiltered = collect();
            foreach ($models as $model)
            {
                foreach ($data as $item)
                {
                    if (
                        (isset($model['ix']) && $item['ix'] === $model->ix) ||
                        (isset($model['id']) && $item['id'] === $model->id)
                    )
                    {
                        $match = true;
                    }
                    else
                    {
                        $dataFiltered->push($item);
                    }
                }
            }

            if ($match) Storage::disk('local')->put($this->fileRoute, $dataFiltered);
        }
    }

    /**
     * Perform the given search on the engine.
     *
     * @param  \Laravel\Scout\Builder  $builder
     * @return mixed
     */
    public function search(Builder $builder)
    {
        return [];
    }

    /**
     * Perform the given search on the engine.
     *
     * @param  \Laravel\Scout\Builder  $builder
     * @param  int  $perPage
     * @param  int  $page
     * @return mixed
     */
    public function paginate(Builder $builder, $perPage, $page)
    {
        return [];
    }

    /**
     * Pluck and return the primary keys of the given results.
     *
     * @param  mixed  $results
     * @return \Illuminate\Support\Collection
     */
    public function mapIds($results)
    {
        return BaseCollection::make();
    }

    /**
     * Map the given results to instances of the given model.
     *
     * @param  \Laravel\Scout\Builder  $builder
     * @param  mixed  $results
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function map(Builder $builder, $results, $model)
    {
        return Collection::make();
    }

    /**
     * Get the total count from a raw result returned by the engine.
     *
     * @param  mixed  $results
     * @return int
     */
    public function getTotalCount($results)
    {
        return count($results);
    }

    /**
     * Flush all of the model's records from the engine.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function flush($model)
    {
        //
    }
}
