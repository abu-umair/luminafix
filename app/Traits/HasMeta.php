<?php

namespace App\Traits;

trait HasMeta
{
    /**
     * Checks wether the model has the specified meta data associated with it
     * without fetching the data from database
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasMeta(string $key): bool
    {
        return (bool) $this->meta()->where('meta_key', $key)->count();
    }

    /**
     * Fetches the value for specified meta key
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getMeta(string $key)
    {
        return $this->meta()->where('meta_key', $key)->select(['meta_key', 'meta_value'])->first();
    }

    /**
     * Sets the provided value for specified meta key
     *
     * @param string $key
     * @param mixed $value
     *
     * @return void
     */
    public function setMeta(string $key, $value = null): void
    {
        $this->meta()->updateOrCreate(['meta_key' => $key], ['meta_value' => $value]);
    }
}
