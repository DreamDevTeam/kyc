<?php


namespace App\Helpers;


class RecursiveDirSearch
{
    private $res = [];

    /**
     * @param string $dir
     * @param string $searchValue
     *
     * @return array|null
     */
    public function index(string $dir, string $searchValue): array|string|null
    {
        if(!is_dir($dir)) {
            return [];
        }

        $files = scandir($dir);

        foreach ($files as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }

            $path = $dir . DIRECTORY_SEPARATOR . $file;

            if (is_dir($path)) {
                $this->index($path, $searchValue);
            }else{
                $content = file_get_contents($path);
                if(str_contains($content, $searchValue)) {
                    $this->res[] = $path;
                }
            }
        }

        return $this->res;
    }
}
