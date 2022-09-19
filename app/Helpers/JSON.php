<?php

namespace App\Helpers;

use CStr;
use Illuminate\Http\JsonResponse;

class JSON
{
    /**
     * Loads the JSON file from provided path and returns it's parsed contents
     *
     * @param string $filename    Name of the JSON file to load
     * @param string $directory   The directory to look the JSON file in,
     *                            defaults to `resources/data`
     * @param bool   $assoc       Whether to convert the JSON into an
     *                            associative array or not (if is valid JSON).
     *                            Default is `true`
     *
     * @return mixed              Returns the parsed JSON content or a `null`
     *                            either if the file has invalid content or is
     *                            empty
     */
    public static function parseFile(string $filename, string $directory = null, $assoc = true)
    {
        // Validate the passed directory, else default to `resources/data`
        $directory = CStr::isValidString($directory) ? $directory : resource_path('data');

        // Merge the directory name and provided filename to make a complete
        // file path
        $path = sprintf('%s/%s', $directory, $filename);

        // The file does not exists!
        if (!file_exists($path)) return null;

        return @json_decode(file_get_contents($path), $assoc) ?? null;
    }

    public static function response(
        ?bool $success = true,
        ?string $message = null,
        mixed $data = null,
        ?int $status = null
    ): JsonResponse {
        $status = $status ?: ($success ? 200 : 500);

        if (CStr::isValidString($message))
            $response['message'] = $message;

        if (!is_null($data))
            if ($success)
                $response['data'] = $data;
            else
                $response['errors'] = $data;

        return response()->json(
            array_merge([
                'status' => $status
            ], $response),
            $status
        );
    }

    public static function success(
        ?string $message = null,
        mixed $data = null,
        ?int $status = null
    ): JsonResponse {
        return static::response(true, $message, $data, $status);
    }

    public static function error(
        ?string $message = null,
        mixed $data = null,
        ?int $status = null
    ): JsonResponse {
        return static::response(false, $message, $data, $status);
    }
}
